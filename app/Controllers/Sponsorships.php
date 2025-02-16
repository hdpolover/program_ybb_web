<?php

namespace App\Controllers;

class Sponsorships extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Sponsorships & Partnerships',
       ];

        return $this->render('ybb/sponsorships', $data);
    }

    public function root($path = '')
    {
        if ($path !== '') {
            if(@file_exists(APPPATH.'Views/'.$path.'.php')) {
                return view($path);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            echo 'Page Not Found.';
        }
    }
}
