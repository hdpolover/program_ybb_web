<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController;

class Announcements extends BaseController
{
    public function index()
    {
        $data = array(
            'title' => 'Announcements',
            'pagetitle' => 'Announcements',
            'page' => 'announcements',
            'subpage' => 'index',
        );

        return $this->render('participant/announcements/index', $data);
    }
}
