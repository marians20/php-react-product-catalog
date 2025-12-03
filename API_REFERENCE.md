# API Reference

Complete API documentation for the Product Catalog application.

## Base URL

```
http://localhost:8000/api
```

## Authentication

All protected endpoints require a JWT token in the Authorization header:

```
Authorization: Bearer <your_jwt_token>
```

Get a token by logging in via `/api/login`.

---

## Authentication Endpoints

### Register User

**POST** `/api/register`

Create a new user account.

**Access:** Public

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "John Doe",
  "roles": ["ROLE_USER"]  // optional, defaults to ["ROLE_USER"]
}
```

**Response:** `201 Created`
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe",
    "roles": ["ROLE_USER"]
  }
}
```

**Errors:**
- `400` - Invalid input or email already exists

---

### Login

**POST** `/api/login`

Authenticate and receive a JWT token.

**Access:** Public

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:** `200 OK`
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

**Errors:**
- `401` - Invalid credentials

---

## Product Endpoints

### List All Products

**GET** `/api/products`

Get all products.

**Access:** Requires `ROLE_USER`

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "name": "Laptop",
    "description": "High-performance laptop",
    "price": 999.99,
    "stock": 50,
    "createdAt": "2025-12-03 10:00:00",
    "updatedAt": "2025-12-03 10:00:00"
  }
]
```

---

### Get Single Product

**GET** `/api/products/{id}`

Get a specific product by ID.

**Access:** Requires `ROLE_USER`

**Response:** `200 OK`
```json
{
  "id": 1,
  "name": "Laptop",
  "description": "High-performance laptop",
  "price": 999.99,
  "stock": 50,
  "createdAt": "2025-12-03 10:00:00",
  "updatedAt": "2025-12-03 10:00:00"
}
```

**Errors:**
- `404` - Product not found

---

### Create Product

**POST** `/api/products`

Create a new product.

**Access:** Requires `ROLE_ADMIN`

**Request Body:**
```json
{
  "name": "Laptop",
  "description": "High-performance laptop",
  "price": 999.99,
  "stock": 50
}
```

**Validation:**
- `name`: Required, string, 1-100 characters
- `description`: Optional, string, max 500 characters
- `price`: Required, number, > 0
- `stock`: Required, integer, >= 0

**Response:** `201 Created`
```json
{
  "id": 1,
  "name": "Laptop",
  "description": "High-performance laptop",
  "price": 999.99,
  "stock": 50,
  "createdAt": "2025-12-03 10:00:00",
  "updatedAt": "2025-12-03 10:00:00"
}
```

**Errors:**
- `400` - Validation error
- `403` - Insufficient permissions

---

### Update Product

**PUT** `/api/products/{id}`

Update an existing product.

**Access:** Requires `ROLE_ADMIN`

**Request Body:** (all fields optional)
```json
{
  "name": "Updated Laptop",
  "description": "Updated description",
  "price": 1099.99,
  "stock": 30
}
```

**Response:** `200 OK`
```json
{
  "id": 1,
  "name": "Updated Laptop",
  "description": "Updated description",
  "price": 1099.99,
  "stock": 30,
  "createdAt": "2025-12-03 10:00:00",
  "updatedAt": "2025-12-03 11:00:00"
}
```

**Errors:**
- `400` - Validation error
- `403` - Insufficient permissions
- `404` - Product not found

---

### Delete Product

**DELETE** `/api/products/{id}`

Delete a product.

**Access:** Requires `ROLE_ADMIN`

**Response:** `204 No Content`

**Errors:**
- `403` - Insufficient permissions
- `404` - Product not found

---

## User Management Endpoints

All user management endpoints require `ROLE_ADMIN`.

### List All Users

**GET** `/api/users`

Get all users.

**Access:** Requires `ROLE_ADMIN`

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "email": "admin@example.com",
    "name": "Admin User",
    "roles": ["ROLE_ADMIN", "ROLE_USER"],
    "enabled": true,
    "createdAt": "2025-12-03 09:00:00"
  }
]
```

---

### Get Single User

**GET** `/api/users/{id}`

Get a specific user by ID.

**Access:** Requires `ROLE_ADMIN`

**Response:** `200 OK`
```json
{
  "id": 1,
  "email": "admin@example.com",
  "name": "Admin User",
  "roles": ["ROLE_ADMIN", "ROLE_USER"],
  "enabled": true,
  "createdAt": "2025-12-03 09:00:00"
}
```

**Errors:**
- `403` - Insufficient permissions
- `404` - User not found

---

### Update User

**PUT** `/api/users/{id}`

Update user details.

**Access:** Requires `ROLE_ADMIN`

**Request Body:** (all fields optional)
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "password": "newpassword123",
  "roles": ["ROLE_ADMIN"]
}
```

**Notes:**
- Password is optional; if not provided, current password is kept
- Email must be unique
- Roles array replaces existing roles

**Response:** `200 OK`
```json
{
  "id": 1,
  "email": "newemail@example.com",
  "name": "Updated Name",
  "roles": ["ROLE_ADMIN", "ROLE_USER"],
  "enabled": true
}
```

**Errors:**
- `400` - Validation error or duplicate email
- `403` - Insufficient permissions
- `404` - User not found

---

### Delete User

**DELETE** `/api/users/{id}`

Delete a user.

**Access:** Requires `ROLE_ADMIN`

**Response:** `204 No Content`

**Errors:**
- `403` - Insufficient permissions
- `404` - User not found

---

### Toggle User Status

**PATCH** `/api/users/{id}/toggle-status`

Enable or disable a user account.

**Access:** Requires `ROLE_ADMIN`

**Request Body:**
```json
{
  "enabled": false  // true to enable, false to disable
}
```

**Response:** `200 OK`
```json
{
  "id": 2,
  "enabled": false,
  "message": "User disabled successfully"
}
```

**Notes:**
- Disabled users cannot log in
- Existing JWT tokens remain valid until expiration

**Errors:**
- `400` - Invalid input
- `403` - Insufficient permissions
- `404` - User not found

---

## Error Responses

All errors follow a consistent format:

```json
{
  "error": "Error message description"
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| `200` | OK - Request successful |
| `201` | Created - Resource created successfully |
| `204` | No Content - Request successful, no content to return |
| `400` | Bad Request - Invalid input or validation error |
| `401` | Unauthorized - Missing or invalid JWT token |
| `403` | Forbidden - Insufficient permissions for this action |
| `404` | Not Found - Resource not found |
| `500` | Internal Server Error - Server-side error |

---

## Rate Limiting

Currently no rate limiting is implemented. Consider adding rate limiting for production use.

---

## Interactive Documentation

For interactive API testing, visit:

```
http://localhost:8000/api/doc
```

The Swagger UI allows you to:
- Browse all endpoints
- View request/response schemas
- Test endpoints directly
- Authenticate with JWT tokens

---

## cURL Examples

### Complete Workflow Example

```bash
# 1. Register a new user
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "test123",
    "name": "Test User"
  }'

# 2. Login and get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123"}' | \
  jq -r '.token')

# 3. View products (as authenticated user)
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN"

# 4. Create product (requires admin - this will fail for regular user)
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Product",
    "description": "Product description",
    "price": 49.99,
    "stock": 100
  }'
```

### Admin Operations

```bash
# Login as admin
ADMIN_TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}' | \
  jq -r '.token')

# Create a product
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Premium Laptop",
    "description": "Top-tier laptop",
    "price": 1499.99,
    "stock": 25
  }'

# List all users
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer $ADMIN_TOKEN"

# Disable a user
curl -X PATCH http://localhost:8000/api/users/2/toggle-status \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"enabled": false}'

# Update user roles
curl -X PUT http://localhost:8000/api/users/2 \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"roles": ["ROLE_ADMIN"]}'
```

---

## WebSocket Support

Currently not implemented. Future enhancement could add real-time updates via WebSockets.

---

## Versioning

Current API version: `v1` (implicit)

Future versions should use URL versioning: `/api/v2/...`
