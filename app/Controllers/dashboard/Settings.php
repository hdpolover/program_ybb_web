<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController;

class Settings extends BaseController
{
    public function index()
    {
        $data = array(
            'title' => 'Settings',
            'pagetitle' => 'Settings',
            'page' => 'settings',
            'subpage' => 'index',
        );
        return $this->render('participant/settings/index', $data);
    }
}
