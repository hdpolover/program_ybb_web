# 🚀 QUICK HOSTING DEPLOYMENT STEPS

## ⚡ IMMEDIATE ACTIONS TO FIX 500 ERROR

### 🚨 URGENT: Fix Directory Structure First!

**Your Issue:** Files are uploaded to wrong location (`/public_html/istanbulyouthsummit.com/public/` instead of `/public_html/istanbulyouthsummit.com/`)

**Quick Fix Steps:**
1. **Move ALL files up one directory level** from `public/` to the domain root
2. **Upload missing directories:** app/, system/, vendor/, writable/
3. **Create missing subdirectories:** writable/logs/, writable/cache/, writable/session/

### Step 1: Upload Test File First
1. Upload `deployment-test.php` to your hosting root directory
2. Visit: `https://yourdomain.com/deployment-test.php`
3. This will show you exactly what's wrong with your hosting environment

### Step 2: Fix File Structure

⚠️ **CRITICAL: Your files are in the wrong location!**

**Current (WRONG) structure:**
```
/public_html/istanbulyouthsummit.com/public/
├── diagnostic.php (working)
└── [missing all other files]
```

**Required (CORRECT) structure:**
```
/public_html/istanbulyouthsummit.com/
├── app/
├── system/
├── vendor/
├── writable/
│   ├── logs/
│   ├── cache/
│   └── session/
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── .env
└── composer.json
```

**OR for shared hosting without public folder:**
```
/public_html/istanbulyouthsummit.com/
├── index.php (move from public/index.php)
├── .htaccess (move from public/.htaccess)
├── assets/ (move from public/assets/)
├── app/
├── system/
├── vendor/
├── writable/ (MUST be writable!)
└── .env
```

### Step 3: Set File Permissions
Via cPanel File Manager or SSH:
```bash
chmod 755 app/
chmod 755 system/  
chmod 755 vendor/
chmod -R 777 writable/
chmod 644 .env
chmod 644 .htaccess
```

### Step 4: Configure Environment
1. Copy `.env.production.template` to `.env`
2. Update these critical values:
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://yourdomain.com/'
database.default.hostname = your_db_host
database.default.database = your_db_name  
database.default.username = your_db_user
database.default.password = your_db_password
```

### Step 5: Upload Files
1. Use `hosting-index.php` as your main `index.php`
2. Use `hosting.htaccess` as your `.htaccess`
3. Upload all app/, system/, vendor/, writable/ directories

### Step 6: Test Database
Upload and run `deployment-test.php` to verify:
- PHP version (needs 8.1+)
- Required extensions
- Database connection
- File permissions

## 🔧 TROUBLESHOOTING SPECIFIC ERRORS

### "Class 'Config\Paths' not found"
- Missing `app/Config/Paths.php`
- Check file upload completed properly

### "Writable directory not found"
- Set permissions: `chmod 777 writable/`
- Ensure writable/ directory exists

### "Database connection failed"  
- Update database credentials in `.env`
- Contact hosting provider for correct database settings

### "500 Internal Server Error"
- Check PHP error logs in cPanel
- Temporarily enable error display (see hosting-index.php)
- Ensure PHP version is 8.1 or higher

## 🌍 MULTI-DOMAIN SETUP

For domains like istanbulyouthsummit.com:

1. **Same Codebase, Different .env:**
```
/public_html/istanbulyouthsummit.com/
├── .env (Istanbul-specific)
├── assets/logo/logo.png (Istanbul logo)
└── [all other files same]
```

2. **Domain-Specific .env Files:**
- `.env.istanbul` → Istanbul Youth Summit settings
- `.env.korea` → Korea Youth Summit settings  
- `.env.japan` → Japan Youth Summit settings

## ⚠️ COMMON HOSTING PROVIDER ISSUES

### Shared Hosting Limitations:
- Can't put files outside public_html
- Limited file permissions
- May need to request PHP extensions

### Solutions:
- Use the provided `hosting-index.php`
- Contact support for required PHP extensions
- Use `deployment-test.php` to identify issues

## 🆘 EMERGENCY CONTACTS

If still getting 500 errors:
1. Check hosting provider's error logs
2. Submit support ticket with error log excerpts  
3. Verify PHP 8.1+ is available
4. Confirm mod_rewrite is enabled

**Delete `deployment-test.php` after fixing issues for security!**