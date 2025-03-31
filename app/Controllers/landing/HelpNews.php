<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class HelpNews extends BaseController
{
    public function index()
    {
        // Get help and news data from API
        $helpNewsData = $this->makeGetRequest('/landing/help-news?web_url=' . $this->currentUrl);
        $data = [
            'title' => 'Help & News',
            'helpNews' => $helpNewsData,
       ];

        return $this->render('landing/help-news', $data);
    }
}