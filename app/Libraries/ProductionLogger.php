<?php

namespace App\Libraries;

class ProductionLogger
{
    private $logPath;
    private $maxLogSize;
    
    public function __construct()
    {
        $this->logPath = WRITEPATH . 'logs/';
        $this->maxLogSize = 50 * 1024 * 1024; // 50MB
    }
    
    /**
     * Log critical errors with context
     */
    public function logError(string $message, array $context = []): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => 'ERROR',
            'message' => $message,
            'context' => $context,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];
        
        $this->writeToLog('error', $logData);
    }
    
    /**
     * Log performance issues
     */
    public function logPerformance(string $action, float $executionTime, array $context = []): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => 'PERFORMANCE',
            'action' => $action,
            'execution_time' => $executionTime,
            'context' => $context,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];
        
        $this->writeToLog('performance', $logData);
    }
    
    /**
     * Log security events
     */
    public function logSecurity(string $event, array $context = []): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => 'SECURITY',
            'event' => $event,
            'context' => $context,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'session_id' => session_id(),
        ];
        
        $this->writeToLog('security', $logData);
    }
    
    /**
     * Log database queries that are slow
     */
    public function logSlowQuery(string $query, float $executionTime, array $params = []): void
    {
        if ($executionTime > 1.0) { // Log queries taking more than 1 second
            $logData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'SLOW_QUERY',
                'query' => $query,
                'execution_time' => $executionTime,
                'parameters' => $params,
                'request_uri' => $_SERVER['REQUEST_URI'] ?? 'CLI',
            ];
            
            $this->writeToLog('slow_queries', $logData);
        }
    }
    
    /**
     * Monitor system resources
     */
    public function monitorResources(): array
    {
        $resources = [
            'timestamp' => date('Y-m-d H:i:s'),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'disk_free_space' => disk_free_space($this->logPath),
            'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
        ];
        
        // Log if memory usage is high
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        if ($memoryLimit > 0) {
            $memoryPercent = ($resources['memory_usage'] / $memoryLimit) * 100;
            if ($memoryPercent > 80) {
                $this->writeToLog('resources', [
                    'level' => 'WARNING',
                    'message' => 'High memory usage detected',
                    'memory_percent' => $memoryPercent,
                    'details' => $resources,
                ]);
            }
        }
        
        return $resources;
    }
    
    private function writeToLog(string $type, array $data): void
    {
        $filename = $this->logPath . "{$type}-" . date('Y-m-d') . '.log';
        
        // Rotate log if it's too large
        if (file_exists($filename) && filesize($filename) > $this->maxLogSize) {
            $this->rotateLog($filename);
        }
        
        $logLine = json_encode($data) . "\n";
        file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    private function rotateLog(string $filename): void
    {
        $rotatedName = $filename . '.' . time();
        rename($filename, $rotatedName);
        
        // Keep only last 5 rotated logs
        $pattern = dirname($filename) . '/' . basename($filename) . '.*';
        $rotatedFiles = glob($pattern);
        if (count($rotatedFiles) > 5) {
            sort($rotatedFiles);
            $filesToDelete = array_slice($rotatedFiles, 0, -5);
            foreach ($filesToDelete as $file) {
                unlink($file);
            }
        }
    }
    
    private function parseMemoryLimit(string $limit): int
    {
        if ($limit === '-1') return -1;
        
        $limit = trim($limit);
        $last = strtolower($limit[strlen($limit)-1]);
        $limit = (int) $limit;
        
        switch($last) {
            case 'g': $limit *= 1024;
            case 'm': $limit *= 1024;
            case 'k': $limit *= 1024;
        }
        
        return $limit;
    }
}