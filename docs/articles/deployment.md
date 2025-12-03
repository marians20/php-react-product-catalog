# Deployment Guide

## Overview

This guide covers deploying the Product Catalog application to production environments.

## Production Checklist

### 1. Environment Preparation

#### Backend Requirements
- [x] PHP 8.3 or higher
- [x] Composer 2.x
- [x] SQLite support (or PostgreSQL/MySQL)
- [x] Web server (Apache/Nginx)
- [x] HTTPS/SSL certificate
- [x] Git for deployment

#### Frontend Requirements
- [x] Node.js 18.x or higher
- [x] npm or yarn
- [x] Web server for static files
- [x] CDN (optional)

### 2. Security Configuration

#### Generate Production JWT Keys

```bash
cd backend
mkdir -p config/jwt

# Generate private key
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keybits:4096

# Generate public key
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

# Set permissions
chmod 600 config/jwt/private.pem
chmod 644 config/jwt/public.pem
```

⚠️ **Important**: Store the passphrase securely (use environment variables, not .env file).

#### Update JWT Configuration

**File**: `backend/.env`

```env
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=YOUR_SECURE_PASSPHRASE_HERE
JWT_TOKEN_TTL=3600  # 1 hour
###< lexik/jwt-authentication-bundle ###
```

**Best Practice**: Use environment variables:
```bash
export JWT_PASSPHRASE='your-secure-passphrase'
```

### 3. Database Configuration

#### Option A: SQLite (Simple)

**File**: `backend/.env`

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

**Production Setup**:
```bash
cd backend

# Create database directory
mkdir -p var

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Set permissions
chmod 644 var/data.db
chmod 755 var
```

#### Option B: PostgreSQL (Recommended for Production)

**File**: `backend/.env`

```env
DATABASE_URL="postgresql://username:password@localhost:5432/product_catalog?serverVersion=15&charset=utf8"
```

**Production Setup**:
```bash
# Create database
createdb product_catalog

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Create admin user
php bin/console app:create-user admin@example.com "Admin User" password ROLE_ADMIN
```

#### Option C: MySQL

**File**: `backend/.env`

```env
DATABASE_URL="mysql://username:password@localhost:3306/product_catalog?serverVersion=8.0"
```

### 4. CORS Configuration

**File**: `backend/config/packages/nelmio_cors.yaml`

```yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['https://yourdomain.com']  # Your production domain
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        expose_headers: ['Link']
        max_age: 3600
    paths:
        '^/api/': ~
```

### 5. Symfony Configuration

#### Enable Production Mode

**File**: `backend/.env`

```env
APP_ENV=prod
APP_DEBUG=0
```

#### Clear and Warm Up Cache

```bash
cd backend

# Clear cache
php bin/console cache:clear --env=prod --no-debug

# Warm up cache
php bin/console cache:warmup --env=prod --no-debug

# Install assets
php bin/console assets:install --env=prod --no-debug
```

## Web Server Configuration

### Nginx Configuration

**File**: `/etc/nginx/sites-available/product-catalog`

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.yourdomain.com;
    
    root /var/www/product-catalog/backend/public;
    
    # SSL Configuration
    ssl_certificate /path/to/fullchain.pem;
    ssl_certificate_key /path/to/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Logs
    access_log /var/log/nginx/product-catalog-access.log;
    error_log /var/log/nginx/product-catalog-error.log;
    
    location / {
        try_files $uri /index.php$is_args$args;
    }
    
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        
        internal;
    }
    
    location ~ \.php$ {
        return 404;
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
}
```

**Enable Site**:
```bash
sudo ln -s /etc/nginx/sites-available/product-catalog /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration

**File**: `/etc/apache2/sites-available/product-catalog.conf`

```apache
<VirtualHost *:80>
    ServerName api.yourdomain.com
    Redirect permanent / https://api.yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName api.yourdomain.com
    DocumentRoot /var/www/product-catalog/backend/public
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/fullchain.pem
    SSLCertificateKeyFile /path/to/privkey.pem
    
    <Directory /var/www/product-catalog/backend/public>
        AllowOverride All
        Require all granted
        
        FallbackResource /index.php
    </Directory>
    
    <Directory /var/www/product-catalog/backend/public/bundles>
        FallbackResource disabled
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/product-catalog-error.log
    CustomLog ${APACHE_LOG_DIR}/product-catalog-access.log combined
    
    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "no-referrer-when-downgrade"
</VirtualHost>
```

**Enable Site**:
```bash
sudo a2ensite product-catalog
sudo a2enmod rewrite ssl headers
sudo systemctl reload apache2
```

## Frontend Deployment

### Build for Production

```bash
cd frontend

# Install dependencies
npm install

# Update API URL
# Edit src/services/api.js
# Change baseURL to production API URL

# Build
npm run build
```

**File**: `frontend/src/services/api.js`

```javascript
const apiClient = axios.create({
  baseURL: 'https://api.yourdomain.com/api',  // Production API
  headers: {
    'Content-Type': 'application/json',
  },
});
```

### Deploy Static Files

#### Option A: Nginx

**File**: `/etc/nginx/sites-available/product-catalog-frontend`

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/product-catalog/frontend/build;
    index index.html;
    
    # SSL Configuration
    ssl_certificate /path/to/fullchain.pem;
    ssl_certificate_key /path/to/privkey.pem;
    
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

#### Option B: Apache

**File**: `/etc/apache2/sites-available/product-catalog-frontend.conf`

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/product-catalog/frontend/build
    
    SSLEngine on
    SSLCertificateFile /path/to/fullchain.pem
    SSLCertificateKeyFile /path/to/privkey.pem
    
    <Directory /var/www/product-catalog/frontend/build>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>
    
    # Cache static assets
    <FilesMatch "\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$">
        Header set Cache-Control "max-age=31536000, public, immutable"
    </FilesMatch>
</VirtualHost>
```

#### Option C: Vercel/Netlify (Serverless)

**Vercel**:
```bash
npm install -g vercel
cd frontend
vercel --prod
```

**Netlify**:
```bash
npm install -g netlify-cli
cd frontend
netlify deploy --prod --dir=build
```

**vercel.json**:
```json
{
  "rewrites": [
    { "source": "/(.*)", "destination": "/index.html" }
  ]
}
```

**netlify.toml**:
```toml
[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

## Deployment Workflow

### Automated Deployment Script

**File**: `deploy.sh`

```bash
#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Backend deployment
echo "📦 Deploying backend..."
cd backend

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Clear cache
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

# Set permissions
chmod -R 755 var
chmod -R 755 vendor

cd ..

# Frontend deployment
echo "📦 Deploying frontend..."
cd frontend

# Install dependencies
npm ci

# Build
npm run build

# Deploy static files (example for Nginx)
sudo rm -rf /var/www/product-catalog/frontend/build
sudo cp -r build /var/www/product-catalog/frontend/

cd ..

echo "✅ Deployment complete!"
```

**Make executable**:
```bash
chmod +x deploy.sh
```

### CI/CD with GitHub Actions

**File**: `.github/workflows/deploy.yml`

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      
      - name: Install backend dependencies
        run: |
          cd backend
          composer install --no-dev --optimize-autoloader
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
      
      - name: Install frontend dependencies
        run: |
          cd frontend
          npm ci
      
      - name: Build frontend
        run: |
          cd frontend
          npm run build
        env:
          REACT_APP_API_URL: ${{ secrets.PRODUCTION_API_URL }}
      
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/product-catalog
            git pull origin main
            ./deploy.sh
```

## Database Migration

### Backup Database

```bash
# SQLite
cp backend/var/data.db backend/var/data.db.backup

# PostgreSQL
pg_dump product_catalog > backup.sql

# MySQL
mysqldump product_catalog > backup.sql
```

### Run Migrations

```bash
cd backend
php bin/console doctrine:migrations:migrate --no-interaction
```

### Rollback Migration

```bash
php bin/console doctrine:migrations:migrate prev
```

## Performance Optimization

### Backend Optimization

#### OPcache Configuration

**File**: `/etc/php/8.3/fpm/conf.d/10-opcache.ini`

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

#### PHP-FPM Configuration

**File**: `/etc/php/8.3/fpm/pool.d/www.conf`

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

#### Database Query Optimization

```bash
# Enable query logging
php bin/console doctrine:query:sql "EXPLAIN SELECT * FROM products"

# Check for missing indexes
php bin/console doctrine:schema:validate
```

### Frontend Optimization

#### Build Optimization

**File**: `frontend/package.json`

```json
{
  "scripts": {
    "build": "react-scripts build",
    "build:analyze": "source-map-explorer 'build/static/js/*.js'"
  }
}
```

#### Code Splitting

```javascript
// Lazy load routes
const ProductList = lazy(() => import('./pages/ProductList'));
const UserList = lazy(() => import('./pages/UserList'));

<Suspense fallback={<CircularProgress />}>
  <Routes>
    <Route path="/products" element={<ProductList />} />
    <Route path="/users" element={<UserList />} />
  </Routes>
</Suspense>
```

#### Enable Compression

**Nginx**:
```nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript 
           application/x-javascript application/xml+rss 
           application/json application/javascript;
```

## Monitoring

### Application Monitoring

**Install Monolog**:
```bash
cd backend
composer require symfony/monolog-bundle
```

**File**: `backend/config/packages/prod/monolog.yaml`

```yaml
monolog:
    handlers:
        main:
            type: fingers_crossed
            action_level: error
            handler: nested
            excluded_http_codes: [404, 405]
        nested:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
        console:
            type: console
            process_psr_3_messages: false
            channels: ["!event", "!doctrine"]
```

### Server Monitoring

**Install monitoring tools**:
```bash
# Install New Relic, Datadog, or similar
sudo apt-get install newrelic-php5

# Configure monitoring
newrelic-install install
```

### Health Check Endpoint

**File**: `backend/src/Infrastructure/Controller/HealthController.php`

```php
#[Route('/health')]
class HealthController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function check(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'timestamp' => time(),
        ]);
    }
}
```

**Test**:
```bash
curl https://api.yourdomain.com/health
```

## Troubleshooting

### Common Issues

#### 500 Internal Server Error

**Check logs**:
```bash
tail -f backend/var/log/prod.log
tail -f /var/log/nginx/error.log
```

**Common causes**:
- Wrong file permissions
- Missing .env variables
- Database connection issues

#### JWT Token Issues

**Verify keys**:
```bash
ls -la backend/config/jwt/
# Ensure private.pem (600) and public.pem (644)
```

**Test token generation**:
```bash
curl -X POST https://api.yourdomain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

#### CORS Errors

**Check configuration**:
```bash
curl -I -X OPTIONS https://api.yourdomain.com/api/products \
  -H "Origin: https://yourdomain.com" \
  -H "Access-Control-Request-Method: GET"
```

**Should return**:
```
Access-Control-Allow-Origin: https://yourdomain.com
Access-Control-Allow-Methods: GET, POST, PUT, DELETE
```

## Rollback Plan

### Quick Rollback

```bash
# Tag current version
git tag v1.0.0

# Push tag
git push origin v1.0.0

# Rollback to previous version
git checkout v0.9.0
./deploy.sh

# Restore database
php bin/console doctrine:migrations:migrate prev
```

### Database Rollback

```bash
# Restore from backup
# SQLite
cp backend/var/data.db.backup backend/var/data.db

# PostgreSQL
psql product_catalog < backup.sql

# MySQL
mysql product_catalog < backup.sql
```

## Production Checklist

### Pre-Deployment
- [ ] Generate production JWT keys
- [ ] Configure production database
- [ ] Update CORS configuration
- [ ] Set APP_ENV=prod and APP_DEBUG=0
- [ ] Review security.yaml configuration
- [ ] Test all endpoints with production-like data
- [ ] Run security audit (composer audit)
- [ ] Check for PHP/composer dependency updates

### Deployment
- [ ] Backup current database
- [ ] Tag current version in git
- [ ] Run deployment script
- [ ] Verify backend health check
- [ ] Verify frontend loads correctly
- [ ] Test authentication flow
- [ ] Test critical user paths

### Post-Deployment
- [ ] Monitor logs for errors
- [ ] Check application performance
- [ ] Verify SSL/HTTPS working
- [ ] Test API from production frontend
- [ ] Create admin user if needed
- [ ] Document any issues encountered
- [ ] Update documentation with production URLs

## Resources

- [Symfony Deployment Best Practices](https://symfony.com/doc/current/deployment.html)
- [React Production Build](https://create-react-app.dev/docs/production-build/)
- [Let's Encrypt SSL](https://letsencrypt.org/)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [PHP-FPM Tuning](https://www.php.net/manual/en/install.fpm.configuration.php)
