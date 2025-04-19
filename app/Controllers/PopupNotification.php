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
        // Set content type as JSON
        $this->response->setContentType('application/json');

        try {
            // Get a list of names and programs for recent signups
            $notif = $this->makeGetRequest('/notifications/random-registration?web_url=' . $this->currentUrl);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'notif' => $notif,
                ]
            ]);
        } catch (\Exception $e) {
            // do nothing and log error
            log_message('error', 'Error fetching recent registrations: ' . $e->getMessage());
        }
    }
}
