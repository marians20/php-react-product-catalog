# Product Catalog Application

A full-stack product catalog application built with PHP/Symfony backend and React frontend, following Onion Architecture principles.

## 📚 Documentation

This project uses **[DocFX](https://dotnet.github.io/docfx/)** for documentation. The documentation source files are in the `docs/` directory.

### View Documentation

**🌐 Online**: [https://marians20.github.io/php-react-product-catalog/](https://marians20.github.io/php-react-product-catalog/)

**Option 1: Build and View Locally**

1. Install DocFX:
   ```bash
   # On macOS
   brew install docfx
   
   # On Linux
   wget https://github.com/dotnet/docfx/releases/latest/download/docfx-linux-x64-latest.zip
   unzip docfx-linux-x64-latest.zip -d ~/.docfx
   export PATH="$PATH:~/.docfx"
   
   # On Windows
   choco install docfx
   ```

2. Build documentation:
   ```bash
   cd docs
   docfx build
   ```

3. Serve documentation locally:
   ```bash
   docfx serve _site
   ```

4. Open in browser: http://localhost:8080

**Option 2: Read Markdown Files Directly**

Browse the documentation as markdown files in the `docs/` directory:

- **[Getting Started](docs/index.md)** - Project overview and installation
- **[Quick Start Guide](docs/articles/quickstart.md)** - 5-minute setup
- **[API Reference](docs/articles/api-reference.md)** - Complete API documentation
- **[Architecture](docs/articles/architecture.md)** - Onion Architecture details
- **[Security & RBAC](docs/articles/security.md)** - Authentication and authorization
- **[Deployment Guide](docs/articles/deployment.md)** - Production deployment

## 🚀 Quick Start

### Prerequisites
- PHP 8.3+
- Composer
- Node.js 18+
- npm

### Backend Setup
```bash
cd backend
composer install
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair
php -S localhost:8000 -t public
```

### Frontend Setup
```bash
cd frontend
npm install
npm start
```

### Default Credentials
- **Admin**: admin@example.com / admin123
- **User**: user@example.com / user123

## 📁 Project Structure

```
.
├── backend/          # Symfony API (Onion Architecture)
│   ├── src/
│   │   ├── Domain/           # Core business logic
│   │   ├── Application/      # Use cases and DTOs
│   │   └── Infrastructure/   # Controllers and repositories
│   └── config/
├── frontend/         # React application
│   └── src/
│       ├── components/
│       ├── pages/
│       └── services/
└── docs/            # DocFX documentation site
    ├── index.md
    ├── articles/
    ├── api/
    └── docfx.json
```

## 🔧 Technology Stack

**Backend:**
- PHP 8.3 / Symfony 6.4
- Doctrine ORM / SQLite
- LexikJWTAuthenticationBundle (JWT auth)
- NelmioApiDocBundle (Swagger)

**Frontend:**
- React 18.2
- Material-UI 5.14
- React Router 6
- Axios

**Documentation:**
- DocFX (static site generator)
- Markdown

## 📖 Features

- ✅ Product CRUD operations
- ✅ User management (CRUD, enable/disable, roles)
- ✅ JWT authentication
- ✅ Role-based access control (RBAC)
- ✅ RESTful API with Swagger documentation
- ✅ Responsive Material-UI interface
- ✅ Onion Architecture (clean separation of concerns)

## 🔐 Security

- JWT tokens with RS256 algorithm
- Password hashing with bcrypt
- Role-based permissions (ROLE_USER, ROLE_ADMIN)
- Protected routes and API endpoints

## 📝 License

This project is for demonstration purposes.

## 🤝 Contributing

For development guidelines and architecture details, see the [Architecture Guide](docs/articles/architecture.md).

## 📞 Support

For issues and questions, please refer to the [documentation](docs/) or check the API documentation at http://localhost:8000/api/doc when the backend is running.
