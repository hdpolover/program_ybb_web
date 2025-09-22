#!/usr/bin/env php
<?php

/**
 * Production Error Monitor
 * 
 * This script monitors error logs and sends alerts for critical issues.
 * Set up as a cron job to run every 5-10 minutes:
 * 
 * Example cron entry (runs every 5 minutes):
 * 5,10,15,20,25,30,35,40,45,50,55,0 * * * * /path/to/php /path/to/your/project/monitor_errors.php
 */

// Configuration
$config = [
    'log_path' => __DIR__ . '/writable/logs/',
    'email_alerts' => false,
    'alert_email' => 'admin@istanbulyouthsummit.com',
    'slack_webhook' => '', // Optional Slack webhook URL
    'check_interval' => 300, // 5 minutes in seconds
    'error_threshold' => 5, // Alert if more than 5 errors in interval
];

class ErrorMonitor
{
    private $config;
    private $lastCheckFile;
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->lastCheckFile = __DIR__ . '/writable/cache/last_error_check.txt';
    }
    
    public function run(): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] Starting error monitor check\n";
        
        $lastCheck = $this->getLastCheckTime();
        $currentTime = time();
        
        $errors = $this->scanForErrors($lastCheck, $currentTime);
        
        if (!empty($errors)) {
            echo "Found " . count($errors) . " errors since last check\n";
            
            if (count($errors) >= $this->config['error_threshold']) {
                $this->sendAlert($errors);
            }
            
            $this->logErrorSummary($errors);
        } else {
            echo "No new errors found\n";
        }
        
        $this->updateLastCheckTime($currentTime);
        echo "Error monitor check completed\n\n";
    }
    
    private function getLastCheckTime(): int
    {
        if (file_exists($this->lastCheckFile)) {
            return (int) file_get_contents($this->lastCheckFile);
        }
        
        return time() - $this->config['check_interval'];
    }
    
    private function updateLastCheckTime(int $time): void
    {
        file_put_contents($this->lastCheckFile, $time);
    }
    
    private function scanForErrors(int $since, int $until): array
    {
        $errors = [];
        $logFiles = glob($this->config['log_path'] . '*.log');
        
        foreach ($logFiles as $logFile) {
            if (filemtime($logFile) >= $since) {
                $fileErrors = $this->parseLogFile($logFile, $since, $until);
                $errors = array_merge($errors, $fileErrors);
            }
        }
        
        return $errors;
    }
    
    private function parseLogFile(string $filepath, int $since, int $until): array
    {
        $errors = [];
        
        if (!is_readable($filepath)) {
            return $errors;
        }
        
        $content = file_get_contents($filepath);
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            // Check if it's a JSON log entry
            $logEntry = json_decode($line, true);
            if ($logEntry && isset($logEntry['timestamp'], $logEntry['level'])) {
                $logTime = strtotime($logEntry['timestamp']);
                if ($logTime >= $since && $logTime <= $until) {
                    if (in_array($logEntry['level'], ['ERROR', 'CRITICAL', 'EMERGENCY'])) {
                        $errors[] = [
                            'file' => basename($filepath),
                            'timestamp' => $logEntry['timestamp'],
                            'level' => $logEntry['level'],
                            'message' => $logEntry['message'] ?? 'No message',
                            'context' => $logEntry['context'] ?? [],
                        ];
                    }
                }
            } else {
                // Parse standard CodeIgniter log format
                if (preg_match('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\s+(\w+)\s+-->\s+(.+)/', $line, $matches)) {
                    $logTime = strtotime($matches[1]);
                    if ($logTime >= $since && $logTime <= $until) {
                        if (in_array(strtoupper($matches[2]), ['ERROR', 'CRITICAL', 'EMERGENCY'])) {
                            $errors[] = [
                                'file' => basename($filepath),
                                'timestamp' => $matches[1],
                                'level' => strtoupper($matches[2]),
                                'message' => $matches[3],
                                'context' => [],
                            ];
                        }
                    }
                }
            }
        }
        
        return $errors;
    }
    
    private function sendAlert(array $errors): void
    {
        $subject = 'Critical Errors Detected - Istanbul Youth Summit';
        $message = $this->formatAlertMessage($errors);
        
        // Email alert
        if ($this->config['email_alerts'] && !empty($this->config['alert_email'])) {
            $headers = [
                'From: noreply@istanbulyouthsummit.com',
                'Content-Type: text/html; charset=UTF-8',
            ];
            
            mail($this->config['alert_email'], $subject, $message, implode("\r\n", $headers));
            echo "Email alert sent to {$this->config['alert_email']}\n";
        }
        
        // Slack alert
        if (!empty($this->config['slack_webhook'])) {
            $this->sendSlackAlert($errors);
        }
    }
    
    private function formatAlertMessage(array $errors): string
    {
        $html = "<h2>Critical Error Alert</h2>";
        $html .= "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
        $html .= "<p><strong>Server:</strong> " . gethostname() . "</p>";
        $html .= "<p><strong>Errors Found:</strong> " . count($errors) . "</p>";
        
        $html .= "<h3>Recent Errors:</h3>";
        $html .= "<table border='1' cellpadding='5' cellspacing='0'>";
        $html .= "<tr><th>Time</th><th>Level</th><th>File</th><th>Message</th></tr>";
        
        foreach (array_slice($errors, 0, 10) as $error) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($error['timestamp']) . "</td>";
            $html .= "<td>" . htmlspecialchars($error['level']) . "</td>";
            $html .= "<td>" . htmlspecialchars($error['file']) . "</td>";
            $html .= "<td>" . htmlspecialchars(substr($error['message'], 0, 100)) . "</td>";
            $html .= "</tr>";
        }
        
        $html .= "</table>";
        
        if (count($errors) > 10) {
            $html .= "<p><em>... and " . (count($errors) - 10) . " more errors</em></p>";
        }
        
        return $html;
    }
    
    private function sendSlackAlert(array $errors): void
    {
        $payload = [
            'text' => '🚨 Critical Error Alert',
            'attachments' => [
                [
                    'color' => 'danger',
                    'title' => 'Error Summary',
                    'fields' => [
                        [
                            'title' => 'Error Count',
                            'value' => count($errors),
                            'short' => true
                        ],
                        [
                            'title' => 'Server',
                            'value' => gethostname(),
                            'short' => true
                        ]
                    ],
                    'footer' => 'Istanbul Youth Summit Monitor',
                    'ts' => time()
                ]
            ]
        ];
        
        $ch = curl_init($this->config['slack_webhook']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        echo "Slack alert sent\n";
    }
    
    private function logErrorSummary(array $errors): void
    {
        $summary = [
            'timestamp' => date('Y-m-d H:i:s'),
            'error_count' => count($errors),
            'error_levels' => array_count_values(array_column($errors, 'level')),
            'error_files' => array_count_values(array_column($errors, 'file')),
        ];
        
        $summaryFile = $this->config['log_path'] . 'error_summary-' . date('Y-m-d') . '.log';
        file_put_contents($summaryFile, json_encode($summary) . "\n", FILE_APPEND | LOCK_EX);
    }
}

// Run the monitor
$monitor = new ErrorMonitor($config);
$monitor->run();