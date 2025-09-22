# Production Debugging Guide

This guide provides comprehensive tools and methods for debugging issues in production safely.

## 🚀 Quick Start - Debug Your Production Issues

### 1. Immediate Debugging (Upload these files to your server)

```bash
# Files to upload to production server:
- public/debug.php                    # Basic server diagnostic
- app/Controllers/ProductionDebugController.php  # Secure debug interface
- app/Libraries/ProductionLogger.php  # Enhanced logging
- monitor_errors.php                  # Error monitoring script
```

### 2. Access Debug Information

**Basic Diagnostics (Public - Remove after use):**
```
https://istanbulyouthsummit.com/debug.php
```

**Secure Debug Interface (Requires secret key):**
```
https://istanbulyouthsummit.com/prod-debug?key=change-this-secret-key-12345
https://istanbulyouthsummit.com/prod-debug/logs?key=change-this-secret-key-12345
https://istanbulyouthsummit.com/prod-debug/test-db?key=change-this-secret-key-12345
https://istanbulyouthsummit.com/prod-debug/clear-cache?key=change-this-secret-key-12345
https://istanbulyouthsummit.com/prod-debug/php-info?key=change-this-secret-key-12345
```

## 🔧 Setup Instructions

### 1. Configure Secret Key and IPs

Edit `app/Controllers/ProductionDebugController.php`:

```php
private $allowedIPs = [
    '127.0.0.1', '::1',
    'YOUR.IP.ADDRESS.HERE',  // Add your IP addresses
];

private $secretKey = 'your-unique-secret-key-here';  // Change this!
```

### 2. Set Up Environment File (.env)

Create `.env` file on your server with:

```bash
CI_ENVIRONMENT = production
app.baseURL = 'https://istanbulyouthsummit.com/'
database.default.hostname = localhost
database.default.database = your_database_name
database.default.username = your_database_user
database.default.password = your_database_password
encryption.key = your-32-character-encryption-key
```

### 3. Fix File Permissions

```bash
chmod -R 755 writable/
chmod -R 775 writable/cache/
chmod -R 775 writable/logs/
chmod -R 775 writable/session/
chmod -R 775 writable/uploads/
```

### 4. Install Dependencies (if missing)

```bash
composer install --no-dev --optimize-autoloader
```

## 📊 Debug Tools Overview

### Basic Diagnostic (debug.php)
- ✅ PHP version and extensions
- ✅ File permissions check
- ✅ Database connection test
- ✅ CodeIgniter files verification
- ✅ Recent error logs display

### Secure Debug Interface
- ✅ Real-time system health monitoring
- ✅ Error log analysis with filtering
- ✅ Database connection testing
- ✅ Cache management
- ✅ PHP info access
- ✅ Memory usage tracking

### Enhanced Logging (ProductionLogger)
- ✅ Structured error logging with context
- ✅ Performance monitoring
- ✅ Security event logging
- ✅ Slow query detection
- ✅ Resource usage monitoring
- ✅ Automatic log rotation

### Error Monitor (monitor_errors.php)
- ✅ Automated error detection
- ✅ Email alerts for critical issues
- ✅ Slack integration (optional)
- ✅ Error threshold monitoring
- ✅ Cron job compatible

## 🔍 Common Production Issues & Solutions

### HTTP 500 Error
1. **Missing .env file** → Create .env with database credentials
2. **Wrong file permissions** → Run chmod commands above
3. **Missing dependencies** → Run composer install
4. **Database connection** → Check credentials in .env
5. **Memory limit** → Increase PHP memory_limit

### Performance Issues
1. **Check error logs** → `/prod-debug/logs`
2. **Monitor memory usage** → `/prod-debug`
3. **Clear cache** → `/prod-debug/clear-cache`
4. **Check slow queries** → Look for slow_queries logs

### Database Issues
1. **Test connection** → `/prod-debug/test-db`
2. **Check credentials** → Verify .env file
3. **Database permissions** → Ensure user has proper rights

## 📝 Log Files Locations

```
writable/logs/
├── log-YYYY-MM-DD.log          # Standard CodeIgniter logs
├── error-YYYY-MM-DD.log        # Custom error logs
├── performance-YYYY-MM-DD.log  # Performance monitoring
├── security-YYYY-MM-DD.log     # Security events
├── slow_queries-YYYY-MM-DD.log # Slow database queries
└── error_summary-YYYY-MM-DD.log # Error monitoring summaries
```

## 🚨 Security Best Practices

1. **Remove debug.php** after initial troubleshooting
2. **Change default secret key** in ProductionDebugController
3. **Restrict IP access** to your debugging tools
4. **Monitor access logs** for unauthorized debug attempts
5. **Disable debug tools** when not needed

## 📧 Setting Up Email Alerts

Edit `monitor_errors.php`:

```php
$config = [
    'email_alerts' => true,
    'alert_email' => 'admin@istanbulyouthsummit.com',
    'error_threshold' => 5,  // Alert after 5 errors
];
```

Set up cron job:
```bash
# Run every 5 minutes
*/5 * * * * /usr/bin/php /path/to/your/project/monitor_errors.php
```

## 🔧 Using the Enhanced Logger

```php
// In your controllers/models
$logger = new \App\Libraries\ProductionLogger();

// Log errors with context
$logger->logError('Database connection failed', [
    'database' => 'main_db',
    'user_id' => $userId,
    'query' => $query
]);

// Log performance issues
$startTime = microtime(true);
// ... your code ...
$executionTime = microtime(true) - $startTime;
$logger->logPerformance('user_login', $executionTime, ['user_id' => $userId]);

// Log security events
$logger->logSecurity('failed_login_attempt', [
    'username' => $username,
    'ip' => $this->request->getIPAddress()
]);
```

## 🎯 Troubleshooting Checklist

### Before Debugging:
- [ ] Upload debug files to server
- [ ] Configure secret key and allowed IPs
- [ ] Ensure .env file exists with correct credentials
- [ ] Check file permissions on writable directories
- [ ] Verify composer dependencies are installed

### During Debugging:
- [ ] Access `/debug.php` for initial diagnosis
- [ ] Use `/prod-debug` for detailed analysis
- [ ] Check error logs for specific error messages
- [ ] Test database connection
- [ ] Clear cache if needed
- [ ] Monitor system resources

### After Fixing:
- [ ] Remove debug.php file
- [ ] Test all functionality
- [ ] Monitor error logs for new issues
- [ ] Set up automated monitoring
- [ ] Document the fix for future reference

## 🆘 Emergency Quick Fixes

### Site Down (HTTP 500)
```bash
# 1. Check basic info
curl -I https://istanbulyouthsummit.com/debug.php

# 2. Fix permissions
chmod -R 755 writable/

# 3. Clear cache
rm -rf writable/cache/* (keep index.html)

# 4. Check logs
tail -f writable/logs/log-$(date +%Y-%m-%d).log
```

### High Memory Usage
```bash
# Check current usage
https://istanbulyouthsummit.com/prod-debug?key=your-key

# Clear cache
https://istanbulyouthsummit.com/prod-debug/clear-cache?key=your-key

# Restart PHP-FPM (if available)
sudo service php-fpm restart
```

Remember to always backup your database and files before making changes in production!