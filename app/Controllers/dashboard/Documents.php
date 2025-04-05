<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController;

class Documents extends BaseController
{
    public function index()
    {
        return $this->render('participant/documents/program-docs');
    }

    public function certificates()
    {
        return $this->render('participant/documents/certificates');
    }
}
