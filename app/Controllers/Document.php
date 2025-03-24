<?php

namespace App\Controllers;

class Document extends BaseController
{
    public function index()
    {
        return view('participant/document/additional-docs');
    }
}
