# Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Start the Backend

```bash
cd backend
php -S localhost:8000 -t public
```

Keep this terminal running.

### Step 2: Start the Frontend (New Terminal)

```bash
cd frontend
npm start
```

Your browser will automatically open to `http://localhost:3000`

### Step 3: Login

Use one of the pre-created test accounts:

**Admin Account:**
- Email: `admin@example.com`
- Password: `admin123`
- Can do everything: view/edit products, manage users

**Regular User Account:**
- Email: `user@example.com`  
- Password: `user123`
- Can only view products

### Step 4: Explore Features

**As Admin:**
1. Click "Products" to manage products (add, edit, delete)
2. Click "Users" to manage users (add, edit, delete, enable/disable, assign roles)
3. Try creating a new product
4. Try disabling/enabling a user

**As Regular User:**
1. Login with user credentials
2. View products (read-only)
3. Try to access Users page (will get 403 error - this is correct!)

## 📋 What's Already Set Up

✅ Backend server configured  
✅ Database created with schema  
✅ JWT authentication configured  
✅ Two test users created  
✅ Sample product data  
✅ Full RBAC implementation  
✅ Swagger documentation at `/api/doc`

## 🔍 Test the API Directly

```bash
# Login and get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}'

# View API documentation
open http://localhost:8000/api/doc
```

## ❓ Common Issues

**"Port 8000 already in use"**
```bash
# Find and kill the process
lsof -ti:8000 | xargs kill -9
```

**"Port 3000 already in use"**
```bash
# Use a different port
PORT=3001 npm start
```

**"Cannot connect to backend"**
- Make sure backend is running on port 8000
- Check `.env` file in frontend has correct API URL

**"JWT token not found"**
- You need to login first
- Check that JWT keys were generated: `backend/config/jwt/private.pem` should exist

## 🎯 Next Steps

1. Explore the codebase structure
2. Check the Swagger documentation
3. Try the RBAC by logging in as different users
4. Modify and extend the application

## 📚 Full Documentation

See [README.md](README.md) for complete documentation including:
- Full architecture details
- API endpoints
- Security implementation
- Deployment guide
- Best practices
