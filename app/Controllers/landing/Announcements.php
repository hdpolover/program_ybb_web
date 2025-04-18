<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Announcements extends BaseController
{
    public function index()
    {
        // Get announcements data from API
        $announcementsData = $this->makeGetRequest('/landing/announcements?web_url=' . $this->currentUrl);
        
        $data = [
            'title' => 'Help & News',
            'category' => $announcementsData['category'] ?? [],
            'announcements' => $announcementsData['announcements'] ?? [],
       ];

        return $this->render('landing/announcements', $data);
    }
}