# Documentation Index

Welcome to the Product Catalog Application documentation!

## 📚 Documentation Files

### 1. [README.md](README.md) - Main Documentation
**Complete project documentation** including:
- Features overview
- Architecture details (Onion Architecture)
- Installation instructions
- Technology stack
- Security & RBAC implementation
- Best practices
- Deployment guide
- Testing examples

**Read this first** for a comprehensive understanding of the project.

---

### 2. [QUICKSTART.md](QUICKSTART.md) - Get Started in 5 Minutes
**Quick setup guide** for getting the application running immediately:
- Step-by-step startup instructions
- Pre-configured test accounts
- Common issues and solutions
- Next steps for exploration

**Use this** if you want to run the app right away without reading detailed docs.

---

### 3. [API_REFERENCE.md](API_REFERENCE.md) - Complete API Documentation
**Detailed API endpoint reference** including:
- All endpoint descriptions
- Request/response formats
- Authentication details
- Error codes
- cURL examples
- Validation rules

**Use this** as a reference when working with the API.

---

## 🎯 Where to Start

**I want to run the app NOW:**
→ Go to [QUICKSTART.md](QUICKSTART.md)

**I want to understand the architecture:**
→ Go to [README.md](README.md) (Architecture section)

**I'm developing and need API details:**
→ Go to [API_REFERENCE.md](API_REFERENCE.md)

**I want interactive API testing:**
→ Start the backend and visit http://localhost:8000/api/doc

---

## 📁 Project Structure

```
poc/
├── backend/                 # Symfony backend
│   ├── src/
│   │   ├── Domain/         # Business logic
│   │   ├── Application/    # Use cases
│   │   └── Infrastructure/ # Controllers & DB
│   ├── config/             # Configuration
│   └── public/             # Entry point
│
├── frontend/               # React frontend
│   ├── src/
│   │   ├── components/    # UI components
│   │   ├── pages/         # Page components
│   │   ├── context/       # React context
│   │   └── services/      # API client
│   └── public/
│
├── README.md              # Main documentation
├── QUICKSTART.md          # Quick start guide
├── API_REFERENCE.md       # API documentation
└── DOCUMENTATION.md       # This file
```

---

## 🔗 Quick Links

- **Swagger UI**: http://localhost:8000/api/doc
- **Backend API**: http://localhost:8000/api
- **Frontend App**: http://localhost:3000
- **Source Code**: All source files include inline documentation

---

## 📖 Additional Resources

### Inline Documentation
All code includes comprehensive comments:
- **PHPDoc** blocks for all classes and methods
- **JSDoc** style comments in React components
- **OpenAPI** attributes on all API endpoints

### External Documentation
- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [React Documentation](https://react.dev/)
- [Material-UI Documentation](https://mui.com/)
- [JWT Authentication](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.rst)

---

## 🎓 Learning Path

If you're new to the project, follow this learning path:

1. **Quick Start** (5 min)
   - Read [QUICKSTART.md](QUICKSTART.md)
   - Start both servers
   - Login and explore the UI

2. **Architecture Understanding** (15 min)
   - Read Architecture section in [README.md](README.md)
   - Examine the folder structure
   - Review the Onion Architecture diagram

3. **API Exploration** (10 min)
   - Open Swagger UI at `/api/doc`
   - Read [API_REFERENCE.md](API_REFERENCE.md)
   - Try some API calls with cURL

4. **Code Exploration** (30 min)
   - Examine a complete feature flow:
     - `ProductController` → `CreateProductUseCase` → `Product` entity
   - Review security configuration in `security.yaml`
   - Check frontend authentication flow in `AuthContext.js`

5. **Advanced Topics** (1+ hour)
   - Study RBAC implementation
   - Examine repository pattern
   - Review error handling strategy
   - Explore form validation

---

## 🆘 Getting Help

### Check Documentation First
1. Specific API question? → [API_REFERENCE.md](API_REFERENCE.md)
2. Setup issue? → [QUICKSTART.md](QUICKSTART.md)
3. General question? → [README.md](README.md)

### Use Interactive Tools
- Swagger UI for API testing
- Browser DevTools for frontend debugging
- Symfony Profiler for backend debugging

### Code Comments
- All complex logic is documented inline
- OpenAPI attributes describe API behavior
- Type hints clarify expected data

---

## ✅ Documentation Completeness

| Topic | Coverage | Location |
|-------|----------|----------|
| Installation | ✅ Complete | README.md, QUICKSTART.md |
| Architecture | ✅ Complete | README.md |
| API Endpoints | ✅ Complete | API_REFERENCE.md, Swagger UI |
| Authentication | ✅ Complete | README.md, API_REFERENCE.md |
| RBAC | ✅ Complete | README.md |
| Security | ✅ Complete | README.md |
| Testing | ✅ Complete | README.md, API_REFERENCE.md |
| Deployment | ✅ Complete | README.md |
| Frontend | ✅ Complete | README.md, inline comments |
| Backend | ✅ Complete | README.md, inline comments |

---

**Last Updated**: December 3, 2025

**Documentation maintained by**: Project Team
