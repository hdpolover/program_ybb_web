<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Insights extends BaseController
{
    public function index()
    {

        $insightsData = $this->makeGetRequest('/landing/insights?web_url=' . $this->currentUrl);

        // Check if the insights data is empty and handle accordingly
        if (empty($insightsData)) {
            // Handle the case when there are no insights available
            return redirect()->to(base_url('home')); // Redirect to home or show a message
        }

        $data = [
            'title' => 'Insights',
            'category' => $insightsData['category'] ?? [],
            'insights' => $insightsData['insights'] ?? [],
        ];

        return $this->render('landing/insights', $data);
    }
}