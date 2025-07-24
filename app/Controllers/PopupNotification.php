<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class PopupNotification extends BaseController
{


    public function index()
    {
        // Load the view for the popup notification
        return view('popup_notification', [
            'webSettings' => $this->data['webSettings'],
            'customMessage' => 'This is a custom popup notification message.',
            'popularLinks' => [
                ['url' => '/', 'title' => 'Home'],
                ['url' => '/about', 'title' => 'About Us']
            ]
        ]);
    }

    /**
     * Get recent participant registrations for toast notifications
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getRecentRegistrations()
    {
        $startTime = microtime(true);
        
        // Set content type as JSON
        $this->response->setContentType('application/json');

        try {
            // Use caching for notification data (2 minute cache)
            $cacheKey = "popup_notifications_" . str_replace(['.', ':', '/', '\\', '@'], '_', $this->currentUrl) . "_v1";
            $cache = \Config\Services::cache();
            
            // Try to get from cache first
            $notif = $cache->get($cacheKey);
            
            if ($notif === null) {
                // Cache miss - fetch from API
                $apiStartTime = microtime(true);
                $notif = $this->makeGetRequest('/notifications/random-registration?web_url=' . $this->currentUrl);
                $apiLoadTime = round((microtime(true) - $apiStartTime) * 1000, 2);
                
                // Cache for 2 minutes (120 seconds) - frequent enough for "recent" notifications
                if (!empty($notif)) {
                    $cache->save($cacheKey, $notif, 120);
                    log_message('info', "Popup notifications cached for {$this->currentUrl} (API load: {$apiLoadTime}ms)");
                }
            } else {
                log_message('debug', "Popup notifications cache hit for {$this->currentUrl}");
            }

            $totalLoadTime = round((microtime(true) - $startTime) * 1000, 2);
            log_message('debug', "Popup notification loaded in {$totalLoadTime}ms");

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'notif' => $notif,
                ]
            ]);
        } catch (\Exception $e) {
            // do nothing and log error
            log_message('error', 'Error fetching recent registrations: ' . $e->getMessage());
            
            // Return a fallback response
            return $this->response->setJSON([
                'success' => false,
                'data' => [
                    'notif' => 'Welcome to our community!',
                ]
            ]);
        }
    }
}
