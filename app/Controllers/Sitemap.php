<?php

namespace App\Controllers;

class Sitemap extends BaseController
{
    public function index()
    {
        $urls = [
            [
                'loc' => base_url(),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => base_url('about-us'),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'loc' => base_url('faqs'),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'loc' => base_url('sponsorships'),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'loc' => base_url('announcements'),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],

        ];

        $announcements = $this->makeGetRequest('/program_announcements/list?program_id=' . $this->getProgramInfoDetail('id'));
        foreach ($announcements as $announcement) {
            $urls[] = [
                'loc' => base_url('announcements/' . $announcement['slug']),
                'lastmod' => date('Y-m-d', strtotime($announcement['updated_at'])),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
        };

        $response = service('response');
        $response->setHeader('Content-Type', 'application/xml');

        echo $this->generateSitemap($urls);
    }

    private function generateSitemap(array $urls): string
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $sitemap .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $sitemap .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $sitemap .= '<priority>' . $url['priority'] . '</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';

        return $sitemap;
    }
}
