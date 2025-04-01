<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Maintenance extends BaseController
{
    public function index()
    {
        // Get web settings data from the service instead of controller data
        $webSettings = \Config\Services::webSettings(true)->getSettings();
        $data['webSettings'] = $webSettings;
        
        // Set the response status code to 503 Service Unavailable
        $this->response->setStatusCode(503);
        
        // Add Retry-After header (in seconds) - 1 hour
        $this->response->setHeader('Retry-After', '3600');
        
        // Render the maintenance page view
        return $this->render('landing/pages/maintenance', $data);
    }
}