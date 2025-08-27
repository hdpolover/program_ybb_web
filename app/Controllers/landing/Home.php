<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $startTime = microtime(true);
        
        // Use caching for home data
        $cacheKey = "landing_home_" . str_replace(['.', ':', '/', '\\', '@'], '_', $this->currentUrl) . "_v1";
        $cache = \Config\Services::cache();
        
        // Try to get from cache first
        $homeData = $cache->get($cacheKey);
        
        if ($homeData === null) {
            // Cache miss - fetch from API
            $apiStartTime = microtime(true);
            $homeData = $this->makeGetRequest('/landing/home?web_url=' . $this->currentUrl);
            $apiLoadTime = round((microtime(true) - $apiStartTime) * 1000, 2);
            
            // Cache for 15 minutes (900 seconds)
            if (!empty($homeData)) {
                $cache->save($cacheKey, $homeData, 900);
                log_message('info', "Home data cached for {$this->currentUrl} (API load: {$apiLoadTime}ms)");
            }
        } else {
            log_message('debug', "Home data cache hit for {$this->currentUrl}");
        }

        $data = [
            'title' => 'Home',
            'category' => $homeData['category'] ?? [],
            'programs' => $homeData['programs'] ?? [],
            'testimonies' => $homeData['testimonies'] ?? [],
            'hasPhotos' => $homeData['hasPhotos'] ?? false,
            'photos' => $homeData['photos'] ?? [],
            'hasVideoTestimonies' => $homeData['hasVideoTestimonies'] ?? false,
            'videoTestimonies' => $homeData['videoTestimonies'] ?? [],
        ];

        log_message('info', 'Home data retrieved: ' . print_r($homeData, true));

        // if program has no photos, use photos from other programs with caching
        if (empty($data['photos'])) {
            $photosCacheKey = "program_photos_all_v1";
            $cachedPhotos = $cache->get($photosCacheKey);
            
            if ($cachedPhotos === null) {
                $photosStartTime = microtime(true);
                $cachedPhotos = $this->makeGetRequest('/program-photos');
                $photosLoadTime = round((microtime(true) - $photosStartTime) * 1000, 2);
                
                // Cache photos for 30 minutes
                if (!empty($cachedPhotos)) {
                    $cache->save($photosCacheKey, $cachedPhotos, 1800);
                    log_message('info', "Program photos cached (API load: {$photosLoadTime}ms)");
                }
            } else {
                log_message('debug', "Program photos cache hit");
            }
            
            $data['photos'] = $cachedPhotos;
        }

        $totalLoadTime = round((microtime(true) - $startTime) * 1000, 2);
        log_message('info', "Home page loaded in {$totalLoadTime}ms");
        log_message('info', 'Photos data retrieved: ' . print_r($data['photos'], true));

        return $this->render('landing/home/home', $data);
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
}
