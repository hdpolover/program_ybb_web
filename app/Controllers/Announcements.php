<?php

namespace App\Controllers;

class Announcements extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Announcements',
            'announcements' => $this->makeGetRequest('/program_announcements/list?program_id=' . $this->getProgramInfoDetail('id')),
        ];

        return $this->render('ybb/announcements', $data);
    }

    public function root($path = '')
    {
        if ($path !== '') {
            if (@file_exists(APPPATH . 'Views/' . $path . '.php')) {
                return view($path);
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        } else {
            echo 'Page Not Found.';
        }
    }

    // announcement details page
    public function details($slug)
    {
        $data = [
            'title' => 'Announcement Details',
            'announcement' => $this->makeGetRequest('/program_announcements/announcement_by_slug/' . $slug),
        ];

        return $this->render('ybb/announcement-detail', $data);
    }
}
