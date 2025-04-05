<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class PartnersSponsors extends BaseController
{
    public function index()
    {
        
        $response = $this->makeGetRequest('/landing/partners-sponsors?web_url=' . $this->currentUrl);
        $category = $response['category'] ?? []; // Extract the category data from the response
       
        $data = [
            'title' => 'Partners & Sponsors',
            'category' => $category,
        ];

        return $this->render('landing/partners-sponsors', $data);
    }
}
