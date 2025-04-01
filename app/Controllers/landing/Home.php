<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        // get home data from API
        $homeData = $this->makeGetRequest('/landing/home?web_url=' . $this->currentUrl);

        $data = [
            'title' => 'Home',
            'category' => $homeData['category'] ?? [],
            'programs' => $homeData['programs'] ?? [],
            'testimonies' => $homeData['testimonies'] ?? [],
            'photos' => $homeData['photos'] ?? [],
        ];

        log_message('info', 'Home data retrieved: ' . print_r($homeData, true));

        // if program has no photos, use photos from other programs
        if (empty($data['photos'])) {
            $data['photos'] = $this->makeGetRequest('/program-photos');
        }


        log_message('info', 'Photos data retrieved: ' . print_r($data['photos'], true));

        return $this->render('landing/home/home', $data);
    }

    public function root($path = '')
    {
        if ($path !== '') {
            if (@file_exists(APPPATH . 'Views/' . $path . '.php')) {
                return view($path);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            echo 'Page Not Found.';
        }
    }
}
