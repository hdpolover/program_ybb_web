<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Sitemap extends BaseController
{
    /**
     * Display the HTML sitemap page for users
     */
    public function sitemap()
    {
        $data = [
            'title' => 'Sitemap',
        ];

        // Get announcements from the Sitemap controller to display in our HTML sitemap
        $sitemapController = new \App\Controllers\Sitemap();

        try {
            // Get program ID for announcements
            $reflectionClass = new \ReflectionClass($sitemapController);
            $reflectionMethod = $reflectionClass->getMethod('getProgramInfoDetail');
            $reflectionMethod->setAccessible(true);

            $programId = $reflectionMethod->invoke($sitemapController, 'id');

            // Get the makeGetRequest method
            $makeGetRequestMethod = $reflectionClass->getMethod('makeGetRequest');
            $makeGetRequestMethod->setAccessible(true);

            // Get announcements
            $data['announcements'] = $makeGetRequestMethod->invoke($sitemapController, '/program_announcements/list?program_id=' . $programId);
        } catch (\Exception $e) {
            $data['announcements'] = [];
            log_message('error', 'Failed to get announcements for sitemap: ' . $e->getMessage());
        }

        return view('pages-sitemap', $data);
    }
}
