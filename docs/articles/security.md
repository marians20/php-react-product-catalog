# Security & RBAC

## Overview

The application implements comprehensive security using JWT authentication and role-based access control (RBAC).

## Authentication

### JWT (JSON Web Tokens)

The application uses JWT for stateless authentication:

**Key Features**:
- RS256 algorithm (RSA signature)
- Configurable token expiration
- Automatic token validation
- Secure key storage

**Token Structure**:
```json
{
  "iat": 1764768217,
  "exp": 1764771817,
  "roles": ["ROLE_ADMIN", "ROLE_USER"],
  "username": "admin@example.com"
}
```

### Authentication Flow

```
1. User submits credentials
   POST /api/login
   {
     "email": "user@example.com",
     "password": "password"
   }
   │
   ├─→ Symfony Security validates credentials
   │   └─→ Password verification (bcrypt)
   │
   ├─→ LexikJWT generates token
   │   └─→ Signs with private key
   │
   └─→ Returns JWT token
       {
         "token": "eyJ0eXAiOiJKV1Q..."
       }

2. Client stores token
   └─→ LocalStorage (frontend)

3. Subsequent requests include token
   Authorization: Bearer eyJ0eXAiOiJKV1Q...
   │
   ├─→ Symfony Security validates token
   │   └─→ Verifies signature with public key
   │   └─→ Checks expiration
   │
   └─→ Loads user from token data
       └─→ Request proceeds with authenticated user
```

## Authorization (RBAC)

### Role Hierarchy

```yaml
ROLE_ADMIN:
  - Inherits ROLE_USER permissions
  - Full system access
  - User management
  - Product management

ROLE_USER:
  - Basic authenticated access
  - Read products
  - Cannot manage users or products
```

### Access Control Matrix

| Endpoint | Public | ROLE_USER | ROLE_ADMIN |
|----------|--------|-----------|------------|
| **Authentication** |
| POST /api/register | ✅ | ✅ | ✅ |
| POST /api/login | ✅ | ✅ | ✅ |
| **Documentation** |
| GET /api/doc | ✅ | ✅ | ✅ |
| **Products** |
| GET /api/products | ❌ | ✅ | ✅ |
| GET /api/products/{id} | ❌ | ✅ | ✅ |
| POST /api/products | ❌ | ❌ | ✅ |
| PUT /api/products/{id} | ❌ | ❌ | ✅ |
| DELETE /api/products/{id} | ❌ | ❌ | ✅ |
| **User Management** |
| GET /api/users | ❌ | ❌ | ✅ |
| GET /api/users/{id} | ❌ | ❌ | ✅ |
| PUT /api/users/{id} | ❌ | ❌ | ✅ |
| DELETE /api/users/{id} | ❌ | ❌ | ✅ |
| PATCH /api/users/{id}/toggle-status | ❌ | ❌ | ✅ |

## Implementation Details

### Security Configuration

**File**: `backend/config/packages/security.yaml`

```yaml
security:
  password_hashers:
    App\Domain\Entity\User: 'auto'
  
  providers:
    app_user_provider:
      entity:
        class: App\Domain\Entity\User
        property: email
  
  role_hierarchy:
    ROLE_ADMIN: [ROLE_USER]
  
  firewalls:
    login:
      pattern: ^/api/login
      stateless: true
      json_login:
        check_path: /api/login
        username_path: email
        password_path: password
        success_handler: lexik_jwt_authentication.handler.authentication_success
        failure_handler: lexik_jwt_authentication.handler.authentication_failure
    
    api:
      pattern: ^/api
      stateless: true
      jwt: ~
  
  access_control:
    - { path: ^/api/login, roles: PUBLIC_ACCESS }
    - { path: ^/api/register, roles: PUBLIC_ACCESS }
    - { path: ^/api/doc, roles: PUBLIC_ACCESS }
    - { path: ^/api/users, roles: ROLE_ADMIN }
    - { path: ^/api/products$, roles: ROLE_USER, methods: [GET] }
    - { path: ^/api/products/[0-9]+$, roles: ROLE_USER, methods: [GET] }
    - { path: ^/api/products, roles: ROLE_ADMIN }
```

### Controller Security

**Using Attributes**:
```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController
{
    #[Route('', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        // Only ROLE_ADMIN can access
    }
}
```

### User Entity

**File**: `backend/src/Domain/Entity/User.php`

```php
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    private array $roles = [];
    private bool $enabled = true;

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Guarantee every user has ROLE_USER
        $roles[] = self::ROLE_USER;
        return array_unique($roles);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
```

## Password Security

### Hashing

**Algorithm**: Bcrypt (auto-selected by Symfony)

**Configuration**:
```yaml
security:
  password_hashers:
    Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
```

**Usage**:
```php
class RegisterUserUseCase
{
    public function execute(RegisterUserDTO $dto): User
    {
        $user = new User($dto->email, $dto->name, $dto->roles);
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $dto->password
        );
        
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user);
        
        return $user;
    }
}
```

### Password Requirements

Current implementation:
- Minimum length enforced by frontend validation
- No complexity requirements (can be added)
- Passwords never returned in API responses
- Passwords only updated when explicitly provided

## Frontend Security

### Token Storage

```javascript
// Store token after login
localStorage.setItem('token', token);

// Include in requests
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Clear on logout
localStorage.removeItem('token');
```

### Protected Routes

```javascript
const PrivateRoute = ({ children }) => {
  const { token, loading } = useAuth();

  if (loading) {
    return <CircularProgress />;
  }

  return token ? children : <Navigate to="/login" />;
};
```

### Automatic Logout on 401

```javascript
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

## Security Best Practices

### Implemented

✅ **JWT with RS256** - Asymmetric key signing
✅ **Password Hashing** - Bcrypt algorithm
✅ **Role-Based Access Control** - Granular permissions
✅ **Input Validation** - DTO validation
✅ **SQL Injection Protection** - Doctrine ORM
✅ **XSS Protection** - React escaping
✅ **CORS Configuration** - Controlled origins
✅ **HTTPS Ready** - Works with SSL/TLS
✅ **Stateless API** - No server-side sessions
✅ **Token Expiration** - Configurable lifetime

### Recommended for Production

- [ ] Rate limiting on authentication endpoints
- [ ] Account lockout after failed attempts
- [ ] Password complexity requirements
- [ ] Token refresh mechanism
- [ ] CSRF protection for cookie-based flows
- [ ] Security headers (CSP, HSTS, etc.)
- [ ] API versioning
- [ ] Request/response logging
- [ ] Intrusion detection
- [ ] Regular security audits

## User Disable Feature

### Purpose

Disable users without deleting their data.

**Behavior**:
- Disabled users cannot log in
- Existing tokens remain valid until expiration
- User data is preserved
- Can be re-enabled at any time

**Implementation**:
```php
class ToggleUserStatusUseCase
{
    public function execute(int $id, bool $enabled): ?User
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return null;
        }

        $user->setEnabled($enabled);
        $this->userRepository->save($user);

        return $user;
    }
}
```

**API Endpoint**:
```bash
PATCH /api/users/{id}/toggle-status
{
  "enabled": false
}
```

## Common Security Scenarios

### Scenario 1: User Tries Unauthorized Access

```
User with ROLE_USER attempts:
POST /api/products

Flow:
1. Request includes valid JWT token
2. Symfony validates token ✓
3. Symfony checks access_control rules
4. User has ROLE_USER, endpoint requires ROLE_ADMIN
5. Access denied → 403 Forbidden
```

### Scenario 2: Expired Token

```
User with expired JWT token attempts:
GET /api/products

Flow:
1. Request includes expired JWT token
2. Symfony validates token signature ✓
3. Symfony checks token expiration ✗
4. Token expired → 401 Unauthorized
5. Frontend intercepts 401
6. Redirects to /login
7. Clears stored token
```

### Scenario 3: Disabled User Login

```
Disabled user attempts:
POST /api/login

Flow:
1. Credentials validated ✓
2. User loaded from database
3. User checker validates user status
4. User is disabled → Login failed
5. Returns authentication error
```

## Security Testing

### Test Authentication

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Expected: 200 OK with token
# Expected (wrong password): 401 Unauthorized
```

### Test Authorization

```bash
# Get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"user123"}' | \
  jq -r '.token')

# Test allowed endpoint (ROLE_USER can GET products)
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN"
# Expected: 200 OK with products

# Test forbidden endpoint (ROLE_USER cannot POST products)
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","description":"Test","price":10,"stock":5}'
# Expected: 403 Forbidden
```

### Test User Disable

```bash
# Disable user (as admin)
curl -X PATCH http://localhost:8000/api/users/2/toggle-status \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"enabled":false}'
# Expected: 200 OK

# Try to login as disabled user
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"disabled@example.com","password":"password"}'
# Expected: 401 Unauthorized
```

## Security Checklist

### Development
- [x] JWT authentication configured
- [x] Password hashing enabled
- [x] RBAC implemented
- [x] Input validation in place
- [x] Error handling doesn't leak sensitive info
- [x] CORS configured for development

### Production
- [ ] Change JWT passphrase
- [ ] Generate new JWT keys
- [ ] Restrict CORS to specific origin
- [ ] Enable HTTPS only
- [ ] Configure token expiration appropriately
- [ ] Add rate limiting
- [ ] Set up monitoring and alerts
- [ ] Review and harden security.yaml
- [ ] Enable security headers
- [ ] Regular dependency updates
