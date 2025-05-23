<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Gallery extends BaseController
{
    public function index()
    { 
        // get gallery data from API
        $galleryData = $this->makeGetRequest('/landing/gallery?web_url=' . $this->currentUrl);

        $data = [
            'title' => 'Gallery',
            'category' => $galleryData['category'] ?? [],
            'photos' => $galleryData['photos'] ?? [],
            'otherProgramPhotos' => $galleryData['otherProgramPhotos'] ?? [],
        ];

        log_message('info', 'Gallery data retrieved: ' . print_r($galleryData, true));

        // if program has no photos, use photos from other programs
        if (empty($data['photos'])) {
            $data['photos'] = $this->makeGetRequest('/program-photos');
        }

        log_message('info', 'Photos data retrieved: ' . print_r($data['photos'], true));

        return $this->render('landing/gallery', $data);
    }
}