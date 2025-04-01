<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Maintenance implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get current URI string
        $uri = uri_string();
        
        // Debug log
        log_message('debug', 'Maintenance Filter - Current URI: ' . $uri);
        
        // Skip maintenance check for the maintenance page itself to avoid redirect loops
        if ($uri === 'maintenance') {
            log_message('debug', 'Maintenance Filter - Skipping check for maintenance page');
            return;
        }
        
        // Get web settings from the service
        $webSettingsService = service('webSettings');
        
        // Debug log - Check if service exists
        if (!$webSettingsService) {
            log_message('error', 'Maintenance Filter - WebSettings service not found');
            return;
        }
        
        $webSettings = $webSettingsService->getSettings();
        
        // Debug log - Check settings
        log_message('debug', 'Maintenance Filter - WebSettings: ' . json_encode($webSettings));
        
        // Check if site is in maintenance mode
        if (!empty($webSettings) && isset($webSettings['is_maintenance_mode'])) {
            log_message('debug', 'Maintenance Filter - is_maintenance_mode value: ' . $webSettings['is_maintenance_mode']);
            
            if ($webSettings['is_maintenance_mode'] === 1 || $webSettings['is_maintenance_mode'] === '1') {
                log_message('info', 'Maintenance Filter - Redirecting to maintenance page');
                return redirect()->to(base_url('maintenance'))->send();
            }
        } else {
            log_message('debug', 'Maintenance Filter - is_maintenance_mode not found in settings');
        }
        
        log_message('debug', 'Maintenance Filter - Maintenance mode not active, continuing');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the controller
    }
}