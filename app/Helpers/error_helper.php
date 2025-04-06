<?php

/**
 * Get data for error pages
 * 
 * @param int $errorCode The HTTP error code
 * @return array Data for the error page
 */
function get_error_data($errorCode = 404)
{
    try {
        // Get the WebSettings service
        $webSettingsService = \Config\Services::webSettings();
        $webSettings = $webSettingsService->getSettings();
    } catch (\Exception $e) {
        // Fallback if WebSettings service fails
        $webSettings = [
            'logo_url' => '/assets/images/logo.png',
            'site_name' => 'Your Website Name',
            'contact_email' => 'support@example.com'
        ];
        
        // Log the error but continue
        log_message('error', 'Error loading web settings in error_helper: ' . $e->getMessage());
    }
    
    // Return the data needed for the error page
    return [
        'webSettings' => $webSettings,
        'errorCode' => $errorCode,
        'popularLinks' => [
            ['url' => '/', 'title' => 'Home'],
            ['url' => '/contact', 'title' => 'Contact Us'],
            ['url' => '/about', 'title' => 'About Us']
        ]
    ];
}