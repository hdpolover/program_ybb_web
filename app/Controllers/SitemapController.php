<?php

namespace App\Controllers;

class SitemapController extends BaseController
{
    public function index()
    {
        // Set the content type
        $this->response->setContentType('application/xml');

        // Try to get from cache first (cache for 6 hours)
        $cache = \Config\Services::cache();
        $sitemap = $cache->get('sitemap');

        if ($sitemap === null) {
            // Create the sitemap
            $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // Add home page
            $sitemap .= $this->addUrl(site_url(), '1.0', 'daily');

            // Get common URLs from your web settings if available
            if (!empty($this->data['webSettings'])) {
                $webSettings = $this->data['webSettings'];

                // Add pages from navigation menu if available
                if (!empty($webSettings['navigation'])) {
                    foreach ($webSettings['navigation'] as $navItem) {
                        if (!empty($navItem['url'])) {
                            $url = $navItem['url'];

                            // Convert relative URLs to absolute
                            if (strpos($url, 'http') !== 0) {
                                $url = site_url(ltrim($url, '/'));
                            }

                            $sitemap .= $this->addUrl($url, '0.8', 'weekly');
                        }
                    }
                }
            }

            // Try to add dynamic program detail pages
            try {
                $programData = $this->makeGetRequest('/programs', [], false);
                if (!empty($programData) && is_array($programData)) {
                    foreach ($programData as $program) {
                        if (isset($program['slug'])) {
                            $sitemap .= $this->addUrl(
                                site_url('programs/' . $program['slug'] . '/details'),
                                '0.9',
                                'weekly'
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to fetch program data for sitemap: ' . $e->getMessage());
            }

            // Try to add dynamic announcement pages
            try {
                $announcementData = $this->makeGetRequest('/announcements', [], false);
                if (!empty($announcementData) && is_array($announcementData)) {
                    foreach ($announcementData as $announcement) {
                        if (isset($announcement['slug'])) {
                            $sitemap .= $this->addUrl(
                                site_url('announcements/' . $announcement['slug']),
                                '0.8',
                                'daily'
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to fetch announcement data for sitemap: ' . $e->getMessage());
            }

            
            // Add standard pages
            $standardPages = [
                // Main landing pages
                '' => ['priority' => '1.0', 'changefreq' => 'daily'],
                'programs' => ['priority' => '0.9', 'changefreq' => 'weekly'],
                'insights' => ['priority' => '0.8', 'changefreq' => 'weekly'],
                'partners-sponsors' => ['priority' => '0.8', 'changefreq' => 'monthly'],
                'announcements' => ['priority' => '0.9', 'changefreq' => 'daily'],
                'contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],

                // Authentication pages
                'sign-in' => ['priority' => '0.6', 'changefreq' => 'monthly'],
                'sign-up' => ['priority' => '0.6', 'changefreq' => 'monthly'],
                'forgot-password' => ['priority' => '0.5', 'changefreq' => 'yearly'],
                'reset-password' => ['priority' => '0.5', 'changefreq' => 'yearly'],
                'verify-email' => ['priority' => '0.5', 'changefreq' => 'yearly'],
                'two-step-verification' => ['priority' => '0.5', 'changefreq' => 'yearly'],

                // Ambassador pages that don't require login
                'ambassadors/sign-in' => ['priority' => '0.6', 'changefreq' => 'monthly'],

                // Miscellaneous pages
                'sitemap' => ['priority' => '0.4', 'changefreq' => 'monthly'], // HTML sitemap
                'maintenance' => ['priority' => '0.3', 'changefreq' => 'yearly'],
            ];

            foreach ($standardPages as $page => $settings) {
                $sitemap .= $this->addUrl(site_url($page), $settings['priority'], $settings['changefreq']);
            }

            // Close sitemap
            $sitemap .= '</urlset>';

            return $sitemap;
        }
    }

    function addUrl($url, $priority = '0.5', $changefreq = 'monthly')
    {
        return "\t<url>\n" .
            "\t\t<loc>" . htmlspecialchars($url) . "</loc>\n" .
            "\t\t<lastmod>" . date('Y-m-d') . "</lastmod>\n" .
            "\t\t<changefreq>" . $changefreq . "</changefreq>\n" .
            "\t\t<priority>" . $priority . "</priority>\n" .
            "\t</url>\n";
    }
}
