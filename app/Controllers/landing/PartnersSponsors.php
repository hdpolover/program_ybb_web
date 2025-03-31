<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class PartnersSponsors extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Partners & Sponsors',
            'partners' => $this->makeGetRequest('/program_partners?program_id=' . $this->getProgramInfoDetail('id')),
            'sponsors' => $this->makeGetRequest('/program_sponsors?program_id=' . $this->getProgramInfoDetail('id')),
        ];

        return $this->render('landing/partners-sponsors', $data);
    }
}