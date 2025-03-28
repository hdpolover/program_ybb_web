<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home',
            'home_details' => $this->makeGetRequest('/web_setting_home?program_id=' . $this->getProgramInfoDetail('id')),
            'program_photos' => $this->makeGetRequest('/program_photos?program_category_id=' . $this->getProgramInfoDetail('program_category_id')),
            'program_schedules' => $this->makeGetRequest('/program_schedules?program_id=' . $this->getProgramInfoDetail('id')),
            'program_testimonies' => $this->makeGetRequest('/program_testimonies/program?id=' . $this->getProgramInfoDetail('id')),
        ];

        // if program has no photos, use photos from other programs
        if (empty($data['program_photos'])) {
            $data['program_photos'] = $this->makeGetRequest('/program_photos');
        }

        return $this->render('landing/home', $data);
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
