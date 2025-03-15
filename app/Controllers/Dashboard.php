<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    
    public function index()
    {
        return $this->render('participant/dashboard/index');
    }

}
