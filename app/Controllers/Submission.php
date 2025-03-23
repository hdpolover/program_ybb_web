<?php

namespace App\Controllers;

class Submission extends BaseController
{

    public function index()
    {

        $data = [
            'title' => 'Submission',
        ];

        return $this->render('participant/submission/index', $data);
    }

    public function edit()
    {
        $data = [
            'title' => 'Submission',
        ];

        return $this->render('participant/submission/edit', $data);
    }
}
