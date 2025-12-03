# Product Catalog Application

A full-stack product catalog application built with **Symfony (PHP)** backend following **Onion Architecture** principles and **React** frontend with **Material-UI**.

## Architecture

### Backend - Onion Architecture

The backend follows the Onion Architecture pattern with clear separation of concerns:

```
backend/
├── src/
│   ├── Domain/              # Core business logic (innermost layer)
│   │   ├── Entity/          # Business entities
│   │   └── Repository/      # Repository interfaces
│   ├── Application/         # Use cases and DTOs
│   │   ├── UseCase/         # Application use cases
│   │   └── DTO/             # Data Transfer Objects
│   └── Infrastructure/      # External concerns (outermost layer)
│       ├── Controller/      # HTTP controllers
│       └── Persistence/     # Database implementation
│           └── Doctrine/
│               └── Repository/
├── config/                  # Symfony configuration
└── public/                  # Web server entry point
```

**Onion Architecture Principles:**
- **Domain Layer**: Pure business logic with no external dependencies
- **Application Layer**: Use cases orchestrating domain logic
- **Infrastructure Layer**: External concerns like HTTP, database, framework-specific code
- Dependencies point inward (Infrastructure → Application → Domain)

### Frontend - React + Material-UI

```
frontend/
├── src/
│   ├── components/          # Reusable UI components
│   ├── pages/              # Page components
│   ├── services/           # API communication
│   ├── App.js              # Main application component
│   └── index.js            # Entry point
└── public/
```

## Features

- ✅ **CRUD Operations**: Create, Read, Update, Delete products
- ✅ **Clean Architecture**: Onion architecture on backend
- ✅ **Modern UI**: Material-UI components with responsive design
- ✅ **Form Validation**: Client and server-side validation
- ✅ **Error Handling**: Comprehensive error handling
- ✅ **RESTful API**: Standard REST endpoints
- ✅ **CORS Support**: Configured for cross-origin requests

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16
- npm or yarn

## Installation

### Backend Setup

1. Navigate to backend directory:
```bash
cd backend
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment (edit `.env` if needed):
```bash
# Already configured to use SQLite
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

4. Start the development server:
```bash
php -S localhost:8000 -t public
```

The API will be available at `http://localhost:8000`

### Frontend Setup

1. Navigate to frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Configure API URL (edit `.env` if needed):
```bash
REACT_APP_API_URL=http://localhost:8000/api
```

4. Start the development server:
```bash
npm start
```

The application will open at `http://localhost:3000`

## API Endpoints

- `GET /api/products` - Get all products
- `GET /api/products/{id}` - Get a single product
- `POST /api/products` - Create a new product
- `PUT /api/products/{id}` - Update a product
- `DELETE /api/products/{id}` - Delete a product

### Request Example (Create Product)

```json
POST /api/products
{
  "name": "Laptop",
  "description": "High-performance laptop",
  "price": 999.99,
  "stock": 50
}
```

## Best Practices Implemented

### Backend
- ✅ Onion Architecture with clear layer separation
- ✅ Dependency Injection
- ✅ Interface-based programming
- ✅ Single Responsibility Principle
- ✅ Type declarations (strict_types=1)
- ✅ Immutable value objects where appropriate
- ✅ Domain-driven validation
- ✅ RESTful API design

### Frontend
- ✅ Component composition
- ✅ Custom hooks for state management
- ✅ Error boundary handling
- ✅ Form validation
- ✅ Responsive design
- ✅ Material Design principles
- ✅ Service layer for API calls
- ✅ Environment configuration

## Project Structure Benefits

**Testability**: Each layer can be tested independently
**Maintainability**: Clear separation makes code easy to modify
**Scalability**: Easy to add new features without affecting existing code
**Framework Independence**: Domain logic is independent of Symfony
**Database Independence**: Repository pattern allows easy database switching

## Development Notes

- The backend uses in-memory storage for simplicity. To use a real database, implement the repository interface with Doctrine ORM.
- CORS is configured to allow all origins in development. Restrict this in production.
- Add authentication/authorization for production use.
- Consider adding request rate limiting for production.

## License

MIT
