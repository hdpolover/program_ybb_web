# 🚨 URGENT: Production 500 Error Fix Guide

## Step 1: Upload Diagnostic Files (DO THIS FIRST)
1. Upload `diagnostic.php` to your hosting public directory
2. Upload `quick-debug.php` to your hosting public directory  
3. Visit: `https://yourdomain.com/quick-debug.php`
4. Take a screenshot of any errors shown

## Step 2: Fix File Permissions (Most Common Issue)
Run these commands in your hosting file manager or SSH:
```bash
chmod -R 755 public/
chmod -R 755 writable/
chmod -R 777 writable/logs/
chmod -R 777 writable/cache/
chmod -R 777 writable/session/
```

## Step 3: Check .env File
Create/update `.env` file in root directory with:
```env
CI_ENVIRONMENT = production

# API Configuration
BASE_API_URL_PRODUCTION = 'https://admin.ybbfoundation.com/api'

app.baseURL = 'https://yourdomain.com/'
```

## Step 4: Verify PHP Requirements
Your hosting needs:
- PHP 7.4 or higher (preferably 8.1+)
- GD extension enabled
- Sufficient memory (512MB minimum)
- mod_rewrite enabled

## Step 5: Check These Common Issues

### ❌ If you see "500 Internal Server Error":
- File permissions are wrong (most common)
- Missing .env file
- PHP version too old
- mod_rewrite not enabled
- ENVIRONMENT constant undefined (fixed in Constants.php)

### ❌ If you see "API connection failed":
- Production API server not accessible
- Network/firewall blocking API calls
- API endpoint URL incorrect in configuration

### ❌ If images don't work:
- GD extension not installed
- writable/uploads/ not writable
- Image processing timeouts

## Step 6: Emergency Debug Mode
If nothing else works, temporarily add this to `public/index.php` (REMOVE AFTER FIXING):
```php
// Add this RIGHT AFTER <?php opening tag
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Step 7: Contact Hosting Support If Needed
If diagnostic tools show hosting-specific issues:
- PHP version too old
- Missing extensions  
- Server configuration problems
- File permission restrictions

## ✅ Success Indicators
- `https://yourdomain.com/` loads without 500 error
- `https://yourdomain.com/quick-debug.php` shows "All checks passed!"
- No errors in error logs

---
**Remember**: Remove diagnostic files and emergency debug mode after fixing!