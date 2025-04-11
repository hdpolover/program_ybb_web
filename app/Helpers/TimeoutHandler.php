<?php

namespace App\Helpers;

/**
 * TimeoutHandler Helper
 *
 * Provides methods to handle PHP timeouts gracefully
 */
class TimeoutHandler
{
    /**
     * Increase the PHP execution time limit for the current script
     *
     * @param int $seconds Number of seconds to set as time limit
     * @return bool Whether the time limit was successfully set
     */
    public static function extendTimeLimit($seconds = 120)
    {
        // Check if we can modify the time limit
        if (ini_get('max_execution_time') !== '0') {
            // Try to set new time limit
            return @set_time_limit($seconds);
        }
        
        return false;
    }
    
    /**
     * Register a shutdown function to catch timeouts
     * Must be called at the beginning of scripts that might time out
     *
     * @param string $redirectUrl URL to redirect to after timeout
     * @return void
     */
    public static function registerTimeoutHandler($redirectUrl = null)
    {
        register_shutdown_function(function() use ($redirectUrl) {
            $error = error_get_last();
            
            // Check if the error was a timeout
            if ($error && 
                ($error['type'] === E_ERROR || $error['type'] === E_CORE_ERROR) && 
                strpos($error['message'], 'Maximum execution time') !== false) {
                
                // For AJAX requests, return JSON
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    
                    // Clear any output buffers
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    
                    // Send JSON error response
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => 'timeout',
                        'message' => 'The request took too long to process. Please try again later.'
                    ]);
                    exit;
                }
                
                // For regular requests, redirect with error parameter
                if ($redirectUrl) {
                    // Add error parameter
                    $separator = (strpos($redirectUrl, '?') !== false) ? '&' : '?';
                    $redirectUrl .= $separator . 'error=timeout';
                    
                    // Clear any output buffers
                    while (ob_get_level()) {
                        ob_end_clean();
                    }
                    
                    // Redirect
                    header('Location: ' . $redirectUrl);
                    exit;
                }
            }
        });
    }
}
