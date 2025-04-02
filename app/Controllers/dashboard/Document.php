<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController;

class Document extends BaseController
{
    public function index()
    {
        return view('participant/document/additional-docs');
    }

    public function upload()
    {

    }
}
