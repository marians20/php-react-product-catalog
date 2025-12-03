# Architecture

## Overview

The Product Catalog Application follows the **Onion Architecture** pattern, ensuring a clean separation of concerns and high maintainability.

## Onion Architecture

### Layer Structure

```
┌─────────────────────────────────────────┐
│        Infrastructure Layer             │
│  (Controllers, Database, Framework)     │
│  ┌───────────────────────────────────┐  │
│  │      Application Layer            │  │
│  │   (Use Cases, DTOs)               │  │
│  │  ┌─────────────────────────────┐  │  │
│  │  │      Domain Layer           │  │  │
│  │  │  (Entities, Interfaces)     │  │  │
│  │  │                             │  │  │
│  │  └─────────────────────────────┘  │  │
│  │                                   │  │
│  └───────────────────────────────────┘  │
│                                         │
└─────────────────────────────────────────┘
```

### Dependency Rule

Dependencies point **inward** only:
- Infrastructure → Application → Domain
- Domain layer has **zero external dependencies**
- Application layer depends only on Domain
- Infrastructure layer depends on both Application and Domain

## Backend Architecture

### Directory Structure

```
backend/
├── src/
│   ├── Domain/              # Innermost layer
│   │   ├── Entity/          
│   │   │   ├── Product.php
│   │   │   └── User.php
│   │   └── Repository/      
│   │       ├── ProductRepositoryInterface.php
│   │       └── UserRepositoryInterface.php
│   │
│   ├── Application/         # Middle layer
│   │   ├── DTO/            
│   │   │   ├── CreateProductDTO.php
│   │   │   ├── UpdateProductDTO.php
│   │   │   ├── RegisterUserDTO.php
│   │   │   └── UpdateUserDTO.php
│   │   └── UseCase/        
│   │       ├── Product/
│   │       │   ├── CreateProductUseCase.php
│   │       │   ├── UpdateProductUseCase.php
│   │       │   ├── DeleteProductUseCase.php
│   │       │   ├── GetProductUseCase.php
│   │       │   └── GetAllProductsUseCase.php
│   │       └── User/
│   │           ├── RegisterUserUseCase.php
│   │           ├── UpdateUserUseCase.php
│   │           ├── DeleteUserUseCase.php
│   │           ├── GetUserUseCase.php
│   │           ├── GetAllUsersUseCase.php
│   │           └── ToggleUserStatusUseCase.php
│   │
│   └── Infrastructure/      # Outermost layer
│       ├── Controller/      
│       │   ├── ProductController.php
│       │   ├── UserController.php
│       │   └── AuthController.php
│       └── Persistence/    
│           └── Doctrine/
│               └── Repository/
│                   ├── DoctrineProductRepository.php
│                   └── DoctrineUserRepository.php
│
├── config/                  
│   ├── packages/           
│   │   ├── doctrine.yaml
│   │   ├── security.yaml
│   │   ├── nelmio_api_doc.yaml
│   │   ├── nelmio_cors.yaml
│   │   └── lexik_jwt_authentication.yaml
│   ├── routes.yaml
│   └── services.yaml
│
└── public/
    └── index.php
```

## Frontend Architecture

### Directory Structure

```
frontend/
├── src/
│   ├── components/          # Reusable components
│   │   ├── Navbar.js
│   │   ├── PrivateRoute.js
│   │   ├── ProductDialog.js
│   │   ├── UserDialog.js
│   │   └── DeleteConfirmDialog.js
│   │
│   ├── pages/              # Page components
│   │   ├── Login.js
│   │   ├── Register.js
│   │   ├── ProductList.js
│   │   └── UserList.js
│   │
│   ├── context/            # Global state
│   │   └── AuthContext.js
│   │
│   ├── services/           # API layer
│   │   └── api.js
│   │
│   ├── App.js              # Root component
│   └── index.js            # Entry point
│
└── public/
    └── index.html
```

## Layer Responsibilities

### Domain Layer

**Purpose**: Contains core business logic and rules

**Responsibilities**:
- Define business entities (Product, User)
- Define repository interfaces
- Enforce business rules and validation
- Remain framework-agnostic

**Example**:
```php
// Product entity with business logic
class Product
{
    public function setPrice(float $price): void
    {
        if ($price <= 0) {
            throw new \InvalidArgumentException('Price must be greater than 0');
        }
        $this->price = $price;
    }
}
```

### Application Layer

**Purpose**: Orchestrates business operations

**Responsibilities**:
- Define use cases (business operations)
- Define DTOs for data transfer
- Coordinate domain objects
- Implement application-specific logic

**Example**:
```php
// Use case orchestrating domain logic
class CreateProductUseCase
{
    public function execute(CreateProductDTO $dto): Product
    {
        $product = new Product(
            $dto->name,
            $dto->description,
            $dto->price,
            $dto->stock
        );
        
        $this->repository->save($product);
        return $product;
    }
}
```

### Infrastructure Layer

**Purpose**: Handles external concerns

**Responsibilities**:
- HTTP controllers and routing
- Database persistence (Doctrine)
- Framework integration (Symfony)
- External service integration
- Configuration

**Example**:
```php
// Controller handling HTTP concerns
class ProductController
{
    #[Route('/api/products', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = CreateProductDTO::fromArray($data);
        $product = $this->createProductUseCase->execute($dto);
        return new JsonResponse($product->toArray(), 201);
    }
}
```

## Data Flow

### Request Flow (Example: Create Product)

```
1. HTTP Request
   POST /api/products
   │
   ├─→ Infrastructure Layer
   │   └─→ ProductController::create()
   │       └─→ Parse JSON → CreateProductDTO
   │
   ├─→ Application Layer
   │   └─→ CreateProductUseCase::execute()
   │       └─→ Create Product entity
   │       └─→ Call repository->save()
   │
   ├─→ Domain Layer
   │   └─→ Product entity (validates business rules)
   │   └─→ ProductRepositoryInterface
   │
   └─→ Infrastructure Layer
       └─→ DoctrineProductRepository::save()
           └─→ Persist to database
```

### Response Flow

```
1. Domain entity returned from repository
   │
   ├─→ Application Layer
   │   └─→ Use case returns Product
   │
   ├─→ Infrastructure Layer
   │   └─→ Controller calls $product->toArray()
   │   └─→ Creates JsonResponse
   │
   └─→ HTTP Response (JSON)
```

## Design Patterns

### Repository Pattern

**Purpose**: Abstract data persistence

```php
// Interface in Domain
interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function find(int $id): ?Product;
    public function findAll(): array;
    public function delete(Product $product): void;
}

// Implementation in Infrastructure
class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
```

### DTO Pattern

**Purpose**: Transfer data between layers

```php
class CreateProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly int $stock
    ) {
    }
    
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'],
            $data['price'],
            $data['stock']
        );
    }
}
```

### Use Case Pattern

**Purpose**: Encapsulate business operations

```php
class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }
    
    public function execute(CreateProductDTO $dto): Product
    {
        $product = new Product(
            $dto->name,
            $dto->description,
            $dto->price,
            $dto->stock
        );
        
        $this->repository->save($product);
        return $product;
    }
}
```

## Benefits of This Architecture

### Testability
- Each layer can be tested independently
- Mock interfaces for unit testing
- Integration tests at controller level

### Maintainability
- Clear separation of concerns
- Easy to locate and modify code
- Changes in one layer don't affect others

### Scalability
- Easy to add new features
- New use cases don't affect existing code
- Can replace infrastructure without touching business logic

### Framework Independence
- Domain logic doesn't depend on Symfony
- Can switch frameworks with minimal impact
- Business rules remain constant

### Team Collaboration
- Clear boundaries for parallel development
- Different teams can work on different layers
- Reduces merge conflicts

## Technology Stack

### Backend
- **PHP 8.3** - Modern PHP with type declarations
- **Symfony 6.4** - Framework (Infrastructure layer only)
- **Doctrine ORM** - Database abstraction
- **LexikJWTAuthenticationBundle** - JWT authentication
- **NelmioApiDocBundle** - API documentation
- **SQLite** - Database

### Frontend
- **React 18** - UI library
- **Material-UI 5** - Component library
- **React Router 6** - Client-side routing
- **Axios** - HTTP client
- **React Context** - State management

## Best Practices Applied

1. **SOLID Principles**
   - Single Responsibility: Each class has one reason to change
   - Open/Closed: Open for extension, closed for modification
   - Liskov Substitution: Interfaces are substitutable
   - Interface Segregation: Small, focused interfaces
   - Dependency Inversion: Depend on abstractions

2. **Clean Code**
   - Meaningful names
   - Small functions
   - Type declarations
   - Comprehensive documentation

3. **Security**
   - JWT authentication
   - Role-based access control
   - Password hashing
   - Input validation

4. **Performance**
   - Eager loading where needed
   - Caching strategies
   - Optimized queries
   - Minimal dependencies
