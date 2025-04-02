<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
        ];

        // 

        return $this->render('participant/dashboard/index', $data);
    }

}
