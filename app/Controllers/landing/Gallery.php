<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Gallery extends BaseController
{
    public function index()
    { 
        // get home data from API
        $homeData = $this->makeGetRequest('/landing/home?web_url=' . $this->currentUrl);

        $data = [
            'title' => 'Gallery',
            'photos' => $homeData['photos'] ?? [],
        ];
        
        log_message('info', 'Home data retrieved: ' . print_r($homeData, true));

        // if program has no photos, use photos from other programs
        if (empty($data['photos'])) {
            $data['photos'] = $this->makeGetRequest('/program-photos');
        }

        log_message('info', 'Photos data retrieved: ' . print_r($data['photos'], true));

        return $this->render('landing/gallery', $data);
    }
}