<?php

namespace App\Controllers;

class AboutUs extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'About Us',
            'about_us' => $this->makeGetRequest('/web_setting_about?program_id=' . $this->getProgramInfoDetail('id')),
            'program_photos' => $this->makeGetRequest('/program_photos?program_category_id=' . $this->getProgramInfoDetail('program_category_id')),
        ];

         // if program has no photos, use photos from other programs
         if (empty($data['program_photos'])) {
            $data['program_photos'] = $this->makeGetRequest('/program_photos');
        }

        return $this->render('ybb/about-us', $data);
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
