<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ProductionDebugController extends Controller
{
    private $allowedIPs = [
        '127.0.0.1', '::1',
        // Add your IP addresses here
    ];
    
    private $secretKey = 'change-this-secret-key-12345';
    
    /**
     * Main debug dashboard
     * URL: /prod-debug?key=your-secret-key
     */
    public function index()
    {
        if (!$this->isAuthorized()) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }
        
        return $this->response->setJSON([
            'timestamp' => date('Y-m-d H:i:s'),
            'server_info' => $this->getServerInfo(),
            'error_logs' => $this->getRecentErrorLogs(),
            'system_health' => $this->getSystemHealth(),
            'database_status' => $this->getDatabaseStatus(),
            'file_permissions' => $this->checkFilePermissions(),
        ]);
    }
    
    /**
     * View recent error logs
     */
    public function logs()
    {
        if (!$this->isAuthorized()) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }
        
        $lines = (int)($this->request->getGet('lines') ?? 50);
        
        return $this->response->setJSON([
            'logs' => $this->getDetailedLogs($lines)
        ]);
    }
    
    /**
     * Clear cache
     */
    public function clearCache()
    {
        if (!$this->isAuthorized()) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }
        
        return $this->response->setJSON($this->clearCacheFiles());
    }
    
    /**
     * Test database connection
     */
    public function testDb()
    {
        if (!$this->isAuthorized()) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }
        
        return $this->response->setJSON($this->testDatabaseConnection());
    }
    
    /**
     * Get PHP info
     */
    public function phpInfo()
    {
        if (!$this->isAuthorized()) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }
        
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();
        
        return $this->response->setContentType('text/html')->setBody($phpinfo);
    }
    
    // Private helper methods
    
    private function isAuthorized(): bool
    {
        $providedKey = $this->request->getGet('key');
        if ($providedKey !== $this->secretKey) {
            return false;
        }
        
        $clientIP = $this->request->getIPAddress();
        if (!empty($this->allowedIPs) && !in_array($clientIP, $this->allowedIPs)) {
            return false;
        }
        
        return true;
    }
    
    private function getServerInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'environment' => ENVIRONMENT,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'current_memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'peak_memory_usage' => $this->formatBytes(memory_get_peak_usage(true)),
            'loaded_extensions' => array_slice(get_loaded_extensions(), 0, 20),
        ];
    }
    
    private function getRecentErrorLogs(): array
    {
        $logPath = WRITEPATH . 'logs/';
        $logs = [];
        
        if (is_dir($logPath)) {
            $files = glob($logPath . '*.log');
            foreach (array_slice($files, -3) as $file) {
                $content = file_get_contents($file);
                $lines = array_filter(explode("\n", $content));
                
                $logs[basename($file)] = [
                    'size' => $this->formatBytes(filesize($file)),
                    'modified' => date('Y-m-d H:i:s', filemtime($file)),
                    'line_count' => count($lines),
                    'recent_lines' => array_slice($lines, -5),
                ];
            }
        }
        
        return $logs;
    }
    
    private function getDetailedLogs(int $lines): array
    {
        $logPath = WRITEPATH . 'logs/';
        $detailedLogs = [];
        
        if (is_dir($logPath)) {
            $files = glob($logPath . '*.log');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $allLines = array_filter(explode("\n", $content));
                
                $detailedLogs[basename($file)] = [
                    'lines' => array_slice($allLines, -$lines),
                    'total_lines' => count($allLines),
                    'file_size' => $this->formatBytes(filesize($file)),
                ];
            }
        }
        
        return $detailedLogs;
    }
    
    private function getSystemHealth(): array
    {
        $health = ['status' => 'healthy', 'issues' => []];
        
        // Check writable directories
        $writableDirs = ['cache', 'logs', 'session', 'uploads'];
        foreach ($writableDirs as $dir) {
            $path = WRITEPATH . $dir;
            if (!is_writable($path)) {
                $health['issues'][] = "Directory not writable: {$dir}";
                $health['status'] = 'warning';
            }
        }
        
        // Check memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        if ($memoryLimit > 0) {
            $memoryPercent = round(($memoryUsage / $memoryLimit) * 100, 2);
            $health['memory_usage'] = "{$memoryPercent}%";
            
            if ($memoryPercent > 80) {
                $health['issues'][] = "High memory usage: {$memoryPercent}%";
                $health['status'] = 'warning';
            }
        }
        
        return $health;
    }
    
    private function getDatabaseStatus(): array
    {
        try {
            $db = \Config\Database::connect();
            $result = $db->query("SELECT 1 as test");
            
            return [
                'connected' => true,
                'version' => $db->getVersion(),
                'database' => $db->getDatabase(),
                'platform' => $db->getPlatform(),
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    private function checkFilePermissions(): array
    {
        $paths = [
            'writable' => WRITEPATH,
            'cache' => WRITEPATH . 'cache',
            'logs' => WRITEPATH . 'logs',
            'session' => WRITEPATH . 'session',
            'uploads' => WRITEPATH . 'uploads',
        ];
        
        $permissions = [];
        foreach ($paths as $name => $path) {
            if (file_exists($path)) {
                $permissions[$name] = [
                    'exists' => true,
                    'writable' => is_writable($path),
                    'permissions' => substr(sprintf('%o', fileperms($path)), -4),
                ];
            } else {
                $permissions[$name] = ['exists' => false];
            }
        }
        
        return $permissions;
    }
    
    private function testDatabaseConnection(): array
    {
        try {
            $db = \Config\Database::connect();
            $query = $db->query("SHOW TABLES");
            $tables = $query->getResultArray();
            
            return [
                'success' => true,
                'message' => 'Database connection successful',
                'table_count' => count($tables),
                'sample_tables' => array_slice(array_column($tables, array_keys($tables[0])[0] ?? 'Tables_in_database'), 0, 5),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    private function clearCacheFiles(): array
    {
        $cachePath = WRITEPATH . 'cache/';
        $filesDeleted = 0;
        
        try {
            if (is_dir($cachePath)) {
                $files = glob($cachePath . '*');
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== 'index.html') {
                        unlink($file);
                        $filesDeleted++;
                    }
                }
            }
            
            return [
                'success' => true,
                'message' => "Cache cleared. {$filesDeleted} files deleted.",
                'files_deleted' => $filesDeleted,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
    
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
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