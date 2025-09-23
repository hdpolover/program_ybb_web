# 🚀 Production Deployment Checklist

## 🚨 500 Error Quick Fix Guide

### Step 1: Upload Debug Tools
1. Upload `public/quick-debug.php` to your hosting
2. Access: `https://yourdomain.com/quick-debug.php`
3. Upload `public/diagnostic.php` for detailed analysis
4. Access: `https://yourdomain.com/diagnostic.php`

### Step 2: Check File Permissions
**Required permissions for these directories:**
```
writable/               → 755 or 775
writable/logs/          → 755 or 775  
writable/cache/         → 755 or 775
writable/session/       → 755 or 775
```

**Commands to fix permissions:**
```bash
chmod -R 755 writable/
# OR if that doesn't work:
chmod -R 775 writable/
```

### Step 3: Environment Configuration
1. Copy `.env.production` to `.env`
2. Update these values in `.env`:
   ```
   CI_ENVIRONMENT = production
   app.baseURL = 'https://yourdomain.com/'
   database.default.hostname = your_db_host
   database.default.database = your_db_name
   database.default.username = your_db_user
   database.default.password = your_db_password
   ```

### Step 4: Common 500 Error Causes & Solutions

#### A. Missing Composer Dependencies
**Problem**: vendor/ folder missing or incomplete
**Solution**: 
```bash
composer install --no-dev --optimize-autoloader
```

#### B. Writable Directory Permissions
**Problem**: CodeIgniter can't write to logs/cache
**Solution**: Set correct permissions (see Step 2)

#### C. PHP Version Compatibility
**Problem**: Hosting PHP version incompatible
**Solution**: Ensure hosting uses PHP 8.1 or higher

#### D. Missing PHP Extensions
**Problem**: Required extensions not installed
**Solution**: Ask hosting to enable:
- mbstring
- intl  
- json
- curl
- gd

#### E. .htaccess Issues
**Problem**: URL rewriting not working
**Solution**: Ensure `.htaccess` exists in public/ with:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

#### F. Memory/Execution Limits
**Problem**: Scripts timing out or running out of memory
**Solution**: Add to `.htaccess`:
```apache
php_value memory_limit 256M
php_value max_execution_time 300
```

### Step 5: Database Issues
1. **Check database connection** in `.env`
2. **Import database** if needed
3. **Run migrations** if required:
   ```bash
   php spark migrate
   ```

### Step 6: API Dependencies
**Problem**: Application tries to connect to localhost:8080/api
**Solution**: Already handled - production uses https://admin.ybbfoundation.com/api

### Step 7: Debug in Production (Temporarily)
If you still get 500 errors, temporarily enable errors:

**Add to public/index.php** (at the very top):
```php
<?php
// TEMPORARY - Remove after debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

**⚠️ IMPORTANT: Remove this code after finding the issue!**

### Step 8: Check Server Error Logs
**cPanel**: Error Logs section
**WHM/DirectAdmin**: Check error_log in file manager
**SSH**: `tail -f /path/to/error_log`

Common log locations:
- `/var/log/apache2/error.log`
- `/var/log/nginx/error.log`
- `~/logs/error_log` (in your account)
- `public_html/error_log`

### Step 9: File Upload Checklist
Ensure all these files/folders are uploaded:
```
✅ app/ (entire folder)
✅ system/ (entire folder) 
✅ public/ (entire folder)
✅ vendor/ (entire folder - from composer install)
✅ writable/ (entire folder)
✅ .env (your production environment file)
✅ .htaccess (in public/ folder)
```

### Step 10: Quick Tests
1. Access `https://yourdomain.com/quick-debug.php`
2. Check for ❌ errors and fix them
3. Test main site: `https://yourdomain.com/`
4. Remove debug files when done

## 🆘 Emergency Fixes

### If Nothing Works:
1. **Enable maintenance mode**: Create `public/maintenance.html`
2. **Revert to last working version**
3. **Contact hosting support** with error logs
4. **Check hosting PHP error logs**

### Most Common Quick Fixes:
1. **Fix permissions**: `chmod -R 755 writable/`
2. **Install dependencies**: `composer install --no-dev`
3. **Check .env file**: Correct database credentials
4. **Upload missing files**: Especially vendor/ folder

### Getting Help:
Include this info when asking for help:
- PHP version (from quick-debug.php)
- Error message from diagnostic.php
- Hosting provider name
- Any error logs from hosting control panel