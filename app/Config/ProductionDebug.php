<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ProductionDebug extends BaseConfig
{
    /**
     * Production Debug Configuration
     * 
     * Safe debugging tools for production environment
     */
    
    // IP addresses allowed to access debug tools
    public array $allowedIPs = [
        '127.0.0.1',           // Localhost
        '::1',                 // IPv6 localhost
        // Add your IP addresses here:
        // '192.168.1.100',    // Your office IP
        // '203.0.113.1',      // Your home IP
    ];
    
    // Secret key for accessing debug tools
    public string $debugKey = 'your-secret-debug-key-change-this';
    
    // Enable/disable debug tools
    public bool $enableDebugTools = true;
    
    // Log retention days
    public int $logRetentionDays = 30;
    
    // Maximum log file size (in MB)
    public int $maxLogSize = 50;
    
    // Email alerts for critical errors
    public bool $emailAlerts = false;
    public string $alertEmail = 'admin@istanbulyouthsummit.com';
    
    // Slack webhook for error notifications (optional)
    public string $slackWebhook = '';
}