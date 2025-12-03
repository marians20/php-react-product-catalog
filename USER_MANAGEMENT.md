# User Management Feature

## Overview
Complete user management system with CRUD operations, user enable/disable functionality, and role allocation.

## Backend API Endpoints

All user management endpoints require **ROLE_ADMIN** authentication.

### Get All Users
```
GET /api/users
Authorization: Bearer {token}
```

### Get User by ID
```
GET /api/users/{id}
Authorization: Bearer {token}
```

### Update User
```
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe",           // optional
  "email": "john@example.com",  // optional
  "password": "newpassword",    // optional
  "roles": ["ROLE_ADMIN"]       // optional
}
```

### Delete User
```
DELETE /api/users/{id}
Authorization: Bearer {token}
```

### Toggle User Status (Enable/Disable)
```
PATCH /api/users/{id}/toggle-status
Authorization: Bearer {token}
Content-Type: application/json

{
  "enabled": true  // or false to disable
}
```

## Frontend Routes

### User Management Page
- **Route**: `/users`
- **Access**: Requires authentication (ROLE_ADMIN only on backend)
- **Features**:
  - View all users in a table
  - Add new users
  - Edit existing users (name, email, password, roles)
  - Delete users with confirmation
  - Enable/disable users with toggle switch
  - Visual indicators for user status

## User Roles

- **ROLE_USER**: Basic access (default for all users)
- **ROLE_ADMIN**: Full access including user management

## Security

- User management endpoints are restricted to ROLE_ADMIN via `#[IsGranted('ROLE_ADMIN')]` attribute
- Access control configured in `security.yaml`: `/api/users` requires ROLE_ADMIN
- Regular users (ROLE_USER) get 403 Forbidden when attempting to access user management

## Testing

### Test as Admin
```bash
# Login as admin
ADMIN_TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}' | \
  grep -o '"token":"[^"]*' | cut -d'"' -f4)

# Get all users
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer $ADMIN_TOKEN"

# Disable user
curl -X PATCH http://localhost:8000/api/users/2/toggle-status \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"enabled":false}'
```

### Test as Regular User (should fail)
```bash
# Login as regular user
USER_TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"user123"}' | \
  grep -o '"token":"[^"]*' | cut -d'"' -f4)

# Try to access users (should get 403 Forbidden)
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer $USER_TOKEN"
```

## Database Schema

The User entity includes:
- `id`: Integer (primary key)
- `email`: String (unique, 180 chars)
- `name`: String (100 chars)
- `password`: String (hashed)
- `roles`: JSON array
- `enabled`: Boolean (default: true)
- `createdAt`: DateTime

## Frontend Components

### UserList (pages/UserList.js)
Main user management page with table display and all CRUD operations.

### UserDialog (components/UserDialog.js)
Modal dialog for creating and editing users with role selection.

### API Service (services/api.js)
Extended with user management methods:
- `getAllUsers()`
- `getUser(id)`
- `updateUser(id, userData)`
- `deleteUser(id)`
- `toggleUserStatus(id, enabled)`

## Navigation

User management is accessible from the navbar (Users button) when logged in.
