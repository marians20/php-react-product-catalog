# Product Catalog Application

A full-stack product catalog and user management system built with **Symfony 6.4 (PHP 8.3)** backend following **Onion Architecture** principles and **React 18** frontend with **Material-UI**.

> [!TIP]
> New to the project? Start with the [Quick Start Guide](articles/quickstart.md) to get up and running in 5 minutes!

## 🚀 Features

### Authentication & Authorization
- ✅ **JWT Authentication** - Secure token-based authentication with LexikJWTAuthenticationBundle
- ✅ **Role-Based Access Control (RBAC)** - Two roles: ROLE_USER and ROLE_ADMIN
- ✅ **Protected Routes** - Frontend route guards with automatic redirect
- ✅ **User Registration** - Self-service user registration with validation

### Product Management
- ✅ **CRUD Operations** - Create, Read, Update, Delete products
- ✅ **Role-Based Access** - Users can view products, Admins can create/update/delete
- ✅ **Form Validation** - Client and server-side validation
- ✅ **Real-time Updates** - Instant UI refresh after operations

### User Management (Admin Only)
- ✅ **User CRUD** - Full user lifecycle management
- ✅ **Enable/Disable Users** - Toggle user account status with visual indicators
- ✅ **Role Allocation** - Assign ROLE_USER or ROLE_ADMIN with multi-select
- ✅ **User Overview** - Sortable table with status, roles, and actions

### Technical Features
- ✅ **Onion Architecture** - Clean, testable, maintainable backend structure
- ✅ **OpenAPI/Swagger Documentation** - Interactive API docs at `/api/doc`
- ✅ **CORS Support** - Configured for cross-origin requests
- ✅ **SQLite Database** - Lightweight, zero-configuration database
- ✅ **Modern UI** - Material-UI with responsive design
- ✅ **Comprehensive Error Handling** - User-friendly error messages

## 🏗️ Architecture

### Backend - Onion Architecture

The backend follows the Onion Architecture pattern with clear separation of concerns:

```
backend/
├── src/
│   ├── Domain/              # Core business logic (innermost layer)
│   │   ├── Entity/          # Business entities (Product, User)
│   │   └── Repository/      # Repository interfaces
│   ├── Application/         # Use cases and DTOs
│   │   ├── UseCase/         # Application use cases (business operations)
│   │   └── DTO/             # Data Transfer Objects
│   └── Infrastructure/      # External concerns (outermost layer)
│       ├── Controller/      # HTTP REST controllers
│       └── Persistence/     # Database implementation
│           └── Doctrine/
│               └── Repository/
├── config/                  # Symfony configuration
│   ├── packages/            # Bundle configurations
│   ├── routes.yaml          # Route definitions
│   └── services.yaml        # Dependency injection
└── public/                  # Web server entry point
```

**Onion Architecture Principles:**
- **Domain Layer**: Pure business logic with no external dependencies
- **Application Layer**: Use cases orchestrating domain logic
- **Infrastructure Layer**: External concerns (HTTP, database, framework)
- **Dependency Rule**: Dependencies point inward only (Infrastructure → Application → Domain)

### Frontend - React + Material-UI

```
frontend/
├── src/
│   ├── components/          # Reusable UI components
│   │   ├── Navbar.js        # Navigation bar
│   │   ├── PrivateRoute.js  # Route protection
│   │   ├── ProductDialog.js # Product create/edit modal
│   │   ├── UserDialog.js    # User create/edit modal
│   │   └── DeleteConfirmDialog.js
│   ├── pages/              # Page components
│   │   ├── Login.js        # Login page
│   │   ├── Register.js     # Registration page
│   │   ├── ProductList.js  # Product management
│   │   └── UserList.js     # User management (admin)
│   ├── context/            # React Context
│   │   └── AuthContext.js  # Global auth state
│   ├── services/           # API communication
│   │   └── api.js          # API client with interceptors
│   ├── App.js              # Main application with routing
│   └── index.js            # Entry point
└── public/
```

## 📋 Prerequisites

- **PHP** >= 8.3
- **Composer** (PHP dependency manager)
- **Node.js** >= 18
- **npm** (Node package manager)
- **SQLite3** PHP extension

## 🛠️ Installation

### Backend Setup

1. **Navigate to backend directory:**
```bash
cd backend
```

2. **Install dependencies:**
```bash
composer install
```

3. **Configure environment:**
The `.env` file is pre-configured for development. Key settings:
```bash
APP_ENV=dev
APP_SECRET=your-secret-key
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase
```

4. **Generate JWT keys (if not present):**
```bash
php bin/console lexik:jwt:generate-keypair
```

5. **Create database schema:**
```bash
php bin/console doctrine:schema:update --force
```

6. **Start the development server:**
```bash
php -S localhost:8000 -t public
```

The API will be available at `http://localhost:8000`

### Frontend Setup

1. **Navigate to frontend directory:**
```bash
cd frontend
```

2. **Install dependencies:**
```bash
npm install
```

3. **Configure environment:**
Create `.env` file if needed (default configuration works):
```bash
REACT_APP_API_URL=http://localhost:8000/api
```

4. **Start the development server:**
```bash
npm start
```

The application will open at `http://localhost:3000`

## 🔑 Default Users

After setting up the backend, create test users:

**Admin User:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "admin123",
    "name": "Admin User",
    "roles": ["ROLE_ADMIN"]
  }'
```

**Regular User:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "user123",
    "name": "Regular User",
    "roles": ["ROLE_USER"]
  }'
```

## 📡 API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login and receive JWT token

### Products
- `GET /api/products` - Get all products (requires ROLE_USER)
- `GET /api/products/{id}` - Get single product (requires ROLE_USER)
- `POST /api/products` - Create product (requires ROLE_ADMIN)
- `PUT /api/products/{id}` - Update product (requires ROLE_ADMIN)
- `DELETE /api/products/{id}` - Delete product (requires ROLE_ADMIN)

### Users (Admin Only)
- `GET /api/users` - Get all users (requires ROLE_ADMIN)
- `GET /api/users/{id}` - Get single user (requires ROLE_ADMIN)
- `PUT /api/users/{id}` - Update user (requires ROLE_ADMIN)
- `DELETE /api/users/{id}` - Delete user (requires ROLE_ADMIN)
- `PATCH /api/users/{id}/toggle-status` - Enable/disable user (requires ROLE_ADMIN)

### Documentation
- `GET /api/doc` - Swagger/OpenAPI documentation (public access)

### API Request Examples

**Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}'
```

**Create Product (with JWT):**
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "name": "Laptop",
    "description": "High-performance laptop",
    "price": 999.99,
    "stock": 50
  }'
```

**Disable User:**
```bash
curl -X PATCH http://localhost:8000/api/users/2/toggle-status \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_JWT_TOKEN" \
  -d '{"enabled":false}'
```

## 🔐 Security & RBAC

### Role Hierarchy
```yaml
ROLE_ADMIN:
  - Inherits all ROLE_USER permissions
  - Can manage users (CRUD, enable/disable, role assignment)
  - Can create/update/delete products

ROLE_USER:
  - Can view products
  - Cannot modify products or access user management
```

### Access Control Matrix

| Endpoint | Public | ROLE_USER | ROLE_ADMIN |
|----------|--------|-----------|------------|
| POST /api/register | ✅ | ✅ | ✅ |
| POST /api/login | ✅ | ✅ | ✅ |
| GET /api/doc | ✅ | ✅ | ✅ |
| GET /api/products | ❌ | ✅ | ✅ |
| POST/PUT/DELETE /api/products | ❌ | ❌ | ✅ |
| /api/users/* | ❌ | ❌ | ✅ |

### Security Features
- JWT tokens with RS256 signing
- Password hashing with bcrypt
- CORS protection
- SQL injection protection via Doctrine ORM
- XSS protection via React
- CSRF protection disabled for stateless API

## 🎨 Frontend Routes

| Route | Access | Description |
|-------|--------|-------------|
| `/login` | Public | Login page |
| `/register` | Public | Registration page |
| `/products` | Protected | Product management (all users) |
| `/users` | Protected | User management (admins only) |
| `/` | - | Redirects to `/products` |

## 📚 Best Practices Implemented

### Backend
- ✅ **Onion Architecture** with strict layer separation
- ✅ **Dependency Injection** via Symfony container
- ✅ **Interface-Based Programming** for repositories
- ✅ **SOLID Principles** throughout codebase
- ✅ **Strict Type Declarations** (`declare(strict_types=1)`)
- ✅ **DTOs** for data transfer and validation
- ✅ **Use Cases** for business logic encapsulation
- ✅ **RESTful API Design** with proper HTTP methods
- ✅ **OpenAPI Documentation** for all endpoints

### Frontend
- ✅ **Component Composition** for reusability
- ✅ **React Context** for global state management
- ✅ **Custom Hooks** for logic reuse
- ✅ **Error Boundaries** for graceful degradation
- ✅ **Form Validation** with user feedback
- ✅ **Responsive Design** with Material-UI grid
- ✅ **Service Layer** for API abstraction
- ✅ **Axios Interceptors** for auth token injection
- ✅ **Protected Routes** with automatic redirects

## 🧪 Testing the Application

### Test Authentication
```bash
# Login as admin
ADMIN_TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}' | \
  jq -r '.token')

# Login as regular user
USER_TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"user123"}' | \
  jq -r '.token')
```

### Test RBAC
```bash
# User can view products
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer $USER_TOKEN"

# User cannot create products (should get 403)
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $USER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","description":"Test","price":10,"stock":5}'

# Admin can create products
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Product","description":"Test","price":99.99,"stock":10}'

# User cannot access user management (should get 403)
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer $USER_TOKEN"

# Admin can access user management
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

## 📖 Documentation

### API Documentation
Access the interactive Swagger UI at:
```
http://localhost:8000/api/doc
```

Features:
- Complete API endpoint documentation
- Request/response schemas
- Try-it-out functionality
- Authentication support

### Code Documentation
- All classes and methods include PHPDoc comments
- DTOs clearly define data structures
- Use cases document business operations
- Controllers include OpenAPI attributes

## 🚀 Deployment Considerations

### Production Checklist
- [ ] Set `APP_ENV=prod` in `.env`
- [ ] Generate new `APP_SECRET`
- [ ] Configure production database (PostgreSQL/MySQL)
- [ ] Restrict CORS to specific origins
- [ ] Enable HTTPS/SSL
- [ ] Configure JWT token expiration
- [ ] Set up proper logging
- [ ] Add rate limiting
- [ ] Configure caching (Redis/Memcached)
- [ ] Set up monitoring and alerts
- [ ] Configure backups
- [ ] Use environment variables for secrets

### Environment Variables
```bash
# Production .env
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL=postgresql://user:pass@host:5432/dbname
CORS_ALLOW_ORIGIN=https://yourdomain.com
JWT_PASSPHRASE=strong-passphrase
```

## 🏗️ Project Structure Benefits

| Benefit | Description |
|---------|-------------|
| **Testability** | Each layer can be tested independently with mocks |
| **Maintainability** | Clear separation makes code easy to understand and modify |
| **Scalability** | Easy to add features without affecting existing code |
| **Framework Independence** | Domain logic doesn't depend on Symfony |
| **Database Independence** | Repository pattern allows easy database switching |
| **Team Collaboration** | Clear boundaries enable parallel development |

## 🔧 Technology Stack

### Backend
- **Symfony 6.4** - PHP framework
- **Doctrine ORM** - Database abstraction
- **LexikJWTAuthenticationBundle** - JWT authentication
- **NelmioApiDocBundle** - OpenAPI/Swagger documentation
- **NelmioCorsBundle** - CORS support
- **SQLite** - Development database

### Frontend
- **React 18** - UI library
- **Material-UI 5** - Component library
- **React Router 6** - Client-side routing
- **Axios** - HTTP client
- **React Context API** - State management

## 📝 License

MIT License - feel free to use this project for learning or as a starter template.

## 🤝 Contributing

This is a demonstration project showcasing best practices for full-stack development with Symfony and React. Feel free to fork and adapt to your needs.

## 📞 Support

For issues or questions:
- Check the Swagger documentation at `/api/doc`
- Review the code comments and PHPDoc blocks
- Examine the Onion Architecture layer separation

---

**Built with ❤️ following software engineering best practices**
