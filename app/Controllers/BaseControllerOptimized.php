<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Services\CacheService;

/**
 * Optimized BaseController with caching implementation
 */
abstract class BaseControllerOptimized extends Controller
{
    protected $request;
    protected $helpers = ["url"];
    protected $client;
    protected $defaultHeaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];
    protected $data = [];
    protected $currentUrl = '';
    protected $apiBaseUrl;
    protected $cacheService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Initialize cache service
        $this->cacheService = new CacheService();

        // Initialize the HTTP client
        $this->client = \Config\Services::curlrequest([
            'timeout' => 30,
            'verify'  => false,
        ]);

        $this->setApiBaseUrl();
        helper('image_helper');

        // Get base domain with caching
        $this->initializeDomainAndSettings();
    }

    /**
     * Initialize domain and web settings with caching
     */
    protected function initializeDomainAndSettings(): void
    {
        $baseDomain = getBaseDomain();
        $host = $_SERVER['HTTP_HOST'] ?? 'unknown';

        // Handle special cases for localhost and different environments
        if ($baseDomain === "localhost:8103" || $baseDomain === "localhost" || $host === "localhost:8103") {
            $this->currentUrl = "istanbulyouthsummit.com";
        } else if (strpos($baseDomain, 'worldyouthfest.com') !== false || strpos($host, 'worldyouthfest.com') !== false) {
            $this->currentUrl = "worldyouthfest.com";
        } else {
            $this->currentUrl = $baseDomain;
        }

        $this->currentUrl = preg_replace('~^https?://~', '', $this->currentUrl);

        // Get web settings with caching
        $this->loadWebSettingsWithCache();
    }

    /**
     * Load web settings with caching
     */
    protected function loadWebSettingsWithCache(): void
    {
        $cacheKey = $this->cacheService->getWebSettingsKey($this->currentUrl);
        
        $webSettingData = $this->cacheService->remember($cacheKey, function() {
            return $this->makeGetRequest('/web-settings?url=' . $this->currentUrl, [], false);
        }, 3600); // 1 hour cache for web settings

        if (empty($webSettingData)) {
            $this->data['webSettings'] = null;
            log_message('warning', 'BaseController - No web settings found');
            return;
        }

        $this->data['webSettings'] = $webSettingData;
        $this->data['siteLogoUrl'] = $webSettingData['logo_url'] ?? null;

        // Handle program category and latest program dates with caching
        $this->loadProgramDataWithCache($webSettingData);

        // Handle maintenance mode
        $this->handleMaintenanceMode($webSettingData);
    }

    /**
     * Load program data with caching
     */
    protected function loadProgramDataWithCache(array $webSettingData): void
    {
        $programCategoryId = $webSettingData['program_category_id'] ?? null;
        
        if (!$programCategoryId) {
            return;
        }

        $cacheKey = $this->cacheService->getProgramsCategoryKey($programCategoryId);
        
        $programs = $this->cacheService->remember($cacheKey, function() use ($programCategoryId) {
            return $this->makeGetRequest('/programs/category/' . $programCategoryId, [], false);
        }, 1800); // 30 minute cache for programs

        if (!empty($programs) && is_array($programs)) {
            // Sort programs by start_date (descending) to get the latest one
            usort($programs, function ($a, $b) {
                return strtotime($b['start_date'] ?? '0') - strtotime($a['start_date'] ?? '0');
            });

            $latestProgram = $programs[0] ?? null;
            if ($latestProgram && isset($latestProgram['start_date']) && isset($latestProgram['end_date'])) {
                $this->data['webSettings']['event_start_date'] = $latestProgram['start_date'];
                $this->data['webSettings']['event_end_date'] = $latestProgram['end_date'];
            }
        }
    }

    /**
     * Handle maintenance mode
     */
    protected function handleMaintenanceMode(array $webSettingData): void
    {
        if (isset($webSettingData['is_maintenance_mode'])) {
            session()->set('is_maintenance_mode', $webSettingData['is_maintenance_mode']);
        }

        if (
            isset($webSettingData['is_maintenance_mode']) &&
            ($webSettingData['is_maintenance_mode'] === 1 || $webSettingData['is_maintenance_mode'] === '1') &&
            uri_string() !== 'maintenance'
        ) {
            header('Location: ' . base_url('maintenance'));
            exit();
        }
    }

    /**
     * Optimized prepareTopbarData with caching
     */
    protected function prepareTopbarData()
    {
        $startTime = microtime(true);
        
        // Create an instance of TopbarController
        $topbarController = new \App\Controllers\TopbarController();

        // Get the topbar data (this should also be optimized)
        $topbarData = $topbarController->processTopbarData();

        // Cache program type ID lookup
        if (isset($this->data['webSettings']['program_category_id'])) {
            $programCategoryId = $this->data['webSettings']['program_category_id'];
            $programTypeId = $this->getProgramTypeIdCached($programCategoryId);

            if ($programTypeId !== null) {
                $this->data['webSettings']['program_type_id'] = $programTypeId;
            }
        }

        $this->data = array_merge($this->data, $topbarData);
        
        $loadTime = round((microtime(true) - $startTime) * 1000, 2);
        log_message('debug', "Topbar data prepared in {$loadTime}ms");
    }

    /**
     * Get program type ID with caching
     */
    protected function getProgramTypeIdCached($programCategoryId): ?int
    {
        if (!$programCategoryId) {
            return null;
        }

        $cacheKey = "program_category_type:{$programCategoryId}:v1";
        
        return $this->cacheService->remember($cacheKey, function() use ($programCategoryId) {
            try {
                $programCategory = $this->makeGetRequest('/program-categories/' . $programCategoryId, [], false);
                return $programCategory['program_type_id'] ?? null;
            } catch (\Exception $e) {
                log_message('error', 'Failed to get program type ID: ' . $e->getMessage());
                return null;
            }
        }, 7200); // 2 hour cache for program category types
    }

    /**
     * Set the API base URL based on the current environment
     */
    protected function setApiBaseUrl()
    {
        $environment = ENVIRONMENT;

        if ($environment === 'development') {
            $this->apiBaseUrl = defined('DEV_BASE_API_URL') ? DEV_BASE_API_URL : 'http://localhost:8100/api';
        } else {
            $this->apiBaseUrl = defined('BASE_API_URL') ? BASE_API_URL : 'https://admin.ybbfoundation.com/api';
        }
    }

    /**
     * Override the render method to include topbar data with timing
     */
    protected function render($view, $data = [])
    {
        $startTime = microtime(true);
        
        // Prepare topbar data before rendering
        $this->prepareTopbarData();

        // Merge global data with view-specific data
        $data = array_merge($this->data, $data);
        
        $renderTime = round((microtime(true) - $startTime) * 1000, 2);
        log_message('debug', "View rendered in {$renderTime}ms");
        
        return view($view, $data);
    }

    // Keep all the existing makeGetRequest, makePostRequest methods...
    // (Copy from original BaseController)
    
    function makeGetRequest($endpoint, $headers = [], $useJwt = false)
    {
        // Add request timing
        $startTime = microtime(true);
        
        try {
            $url = $this->apiBaseUrl . $endpoint;

            if ($useJwt) {
                $token = $this->getJwtToken();
                if ($token) {
                    $headers['Authorization'] = 'Bearer ' . $token;
                }
            }

            $response = $this->client->request('GET', $url, [
                'headers' => array_merge($this->defaultHeaders, $headers),
            ]);

            $statusCode = $response->getStatusCode();
            $bodyDecoded = json_decode($response->getBody(), true);

            $requestTime = round((microtime(true) - $startTime) * 1000, 2);
            log_message('debug', "API GET {$endpoint} completed in {$requestTime}ms");

            if (isset($bodyDecoded['data'])) {
                return $bodyDecoded['data'];
            } else {
                return $bodyDecoded;
            }
        } catch (\CodeIgniter\HTTP\Exceptions\HTTPException $e) {
            $statusCode = $e->getCode();
            log_message('error', 'HTTP Error: ' . $e->getMessage() . ' (Status Code: ' . $statusCode . ')');
            return null;
        } catch (\Exception $e) {
            log_message('error', 'Error in makeGetRequest: ' . $e->getMessage());
            return null;
        }
    }

    // ... (include other methods from original BaseController)
    
    protected function getJwtToken()
    {
        if (session()->has('jwt_token')) {
            return session()->get('jwt_token');
        }

        if (session()->get('isLoggedIn') && !session()->has('jwt_token')) {
            session()->remove('isLoggedIn');
            session()->remove('user');
            log_message('error', 'JWT token missing for logged in user');
        }

        return null;
    }
}
