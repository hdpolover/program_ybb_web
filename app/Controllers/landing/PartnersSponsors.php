<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class PartnersSponsors extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Partners & Sponsors',
        ];

        return $this->render('landing/partners-sponsors', $data);
    }
}
