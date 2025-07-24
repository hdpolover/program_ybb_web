<?php

namespace App\Controllers;

class CacheController extends BaseController
{
    /**
     * Clear all cache
     */
    public function clearAll()
    {
        $cache = \Config\Services::cache();
        $cache->clean();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Cache cleared successfully']);
        }
        
        return redirect()->back()->with('success', 'Cache cleared successfully');
    }
    
    /**
     * Clear specific cache pattern
     */
    public function clearPattern($pattern = null)
    {
        if (!$pattern) {
            $pattern = $this->request->getGet('pattern');
        }
        
        if (!$pattern) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pattern required']);
        }
        
        $cache = \Config\Services::cache();
        
        // CodeIgniter doesn't have deleteMatching, so we'll implement pattern clearing
        // For file cache, we can try to clear specific files
        $cleared = $this->clearCacheByPattern($pattern);
        
        if ($cleared > 0) {
            $message = "Cache cleared for pattern '{$pattern}' - {$cleared} items removed";
        } else {
            // Fallback to clearing all cache
            $cache->clean();
            $message = "Full cache cleared (pattern '{$pattern}' - fallback to full clear)";
        }
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => $message]);
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Clear cache by pattern - custom implementation
     */
    private function clearCacheByPattern($pattern)
    {
        $cache = \Config\Services::cache();
        $cleared = 0;
        
        // For file cache, we can scan the cache directory
        $cacheConfig = config('Cache');
        
        // Check if using file cache handler
        $handlerClass = get_class($cache);
        
        if (strpos($handlerClass, 'FileHandler') !== false) {
            $cachePath = WRITEPATH . 'cache/';
            
            if (is_dir($cachePath)) {
                $files = glob($cachePath . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);
                        // Remove the file extension and decode
                        $cacheKey = pathinfo($filename, PATHINFO_FILENAME);
                        
                        // Check if the cache key matches the pattern
                        if (fnmatch("*{$pattern}*", $cacheKey)) {
                            if (unlink($file)) {
                                $cleared++;
                            }
                        }
                    }
                }
            }
        } else {
            // For other cache types, we can try to delete specific known keys
            $commonPatterns = [
                "web_settings_{$pattern}",
                "landing_home_{$pattern}",
                "landing_programs_{$pattern}",
                "participant_payments_{$pattern}",
                "participant_status_{$pattern}",
                "programs_category_{$pattern}",
                "program_photos_{$pattern}"
            ];
            
            foreach ($commonPatterns as $key) {
                if ($cache->delete($key)) {
                    $cleared++;
                }
            }
        }
        
        return $cleared;
    }
    
    /**
     * Get cache statistics
     */
    public function stats()
    {
        $cache = \Config\Services::cache();
        
        $stats = [
            'handler' => get_class($cache),
            'cache_info' => $this->getCacheInfo(),
        ];
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($stats);
        }
        
        // Simple HTML output for testing
        $html = "<h2>Cache Statistics</h2>";
        $html .= "<p><strong>Handler:</strong> " . $stats['handler'] . "</p>";
        $html .= "<p><strong>Cache Info:</strong></p>";
        $html .= "<pre>" . print_r($stats['cache_info'], true) . "</pre>";
        $html .= "<p><a href='/cache/clear'>Clear All Cache</a></p>";
        
        return $this->response->setBody($html);
    }
    
    /**
     * Get detailed cache information
     */
    private function getCacheInfo()
    {
        $cache = \Config\Services::cache();
        $handlerClass = get_class($cache);
        
        $info = [
            'handler_class' => $handlerClass,
            'cache_directory' => null,
            'file_count' => 0,
            'total_size' => 0,
        ];
        
        // If using file cache, get more details
        if (strpos($handlerClass, 'FileHandler') !== false) {
            $cachePath = WRITEPATH . 'cache/';
            $info['cache_directory'] = $cachePath;
            
            if (is_dir($cachePath)) {
                $files = glob($cachePath . '*');
                $info['file_count'] = count($files);
                
                $totalSize = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $totalSize += filesize($file);
                    }
                }
                $info['total_size'] = $this->formatBytes($totalSize);
            }
        }
        
        return $info;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
