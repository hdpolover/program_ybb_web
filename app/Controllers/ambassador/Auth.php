<?php

namespace App\Controllers\ambassador;
use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
        ];

        // 

        return $this->render('ambassador/dashboard', $data);
    }

}
