<?php

namespace App\Controllers;

class Faqs extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'FAQs',
            'faqs' => $this->makeGetRequest('/program_faqs/list_program?program_id=' . $this->getProgramInfoDetail('id')),
        ];


        return $this->render('ybb/faqs', $data);
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
