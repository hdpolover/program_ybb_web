<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Intervention\Image\ImageManager;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ["url"];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * HTTP Client for making external API calls.
     *
     * @var \CodeIgniter\HTTP\CURLRequest
     */
    protected $client;

    /**
     * Default headers for API requests.
     *
     * @var array<string, string>
     */
    protected $defaultHeaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    // make data variable available to all controllers
    protected $data = [];

    // current url
    protected $currentUrl = '';

    /**
     * API base URL, set dynamically based on environment
     * 
     * @var string
     */
    protected $apiBaseUrl;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Initialize the HTTP client
        $this->client = \Config\Services::curlrequest([
            'timeout' => 30,
            'verify'  => false, // Set to true in production for SSL verification
        ]);

        // Set the API base URL based on environment
        $this->setApiBaseUrl();

        // Make sure image helper is loaded
        helper('image_helper');

        // get base domain
        $baseDomain = getBaseDomain();
        $host = $_SERVER['HTTP_HOST'] ?? 'unknown';
        log_message('debug', 'Original base domain: ' . $baseDomain);
        log_message('debug', 'HTTP_HOST: ' . $host);

        // Handle special cases for localhost and different environments
        if ($baseDomain === "localhost:8081" || $baseDomain === "localhost" || $host === "localhost:8081") {
            // You can change this to test different domains
            $this->currentUrl = "worldyouthfest.com"; // Changed from koreayouthsummit.com for testing
            log_message('debug', 'Detected localhost, setting currentUrl to middleeastyouthsummit.com');
        } else if (strpos($baseDomain, 'worldyouthfest.com') !== false || strpos($host, 'worldyouthfest.com') !== false) {
            // Ensure we're using the correct domain for WorldYouthFest
            // Check both baseDomain and HTTP_HOST to be safe
            $this->currentUrl = "worldyouthfest.com";
            log_message('debug', 'Detected WorldYouthFest domain, setting currentUrl to worldyouthfest.com');
        } else {
            $this->currentUrl = $baseDomain;
            log_message('debug', 'Using default domain: ' . $baseDomain);
        }

        // Remove protocol (http:// or https://) from the current URL
        $this->currentUrl = preg_replace('~^https?://~', '', $this->currentUrl);
        log_message('debug', 'Final currentUrl value: ' . $this->currentUrl);

        // Get web settings with caching
        $this->loadWebSettingsWithCache();
    }

    /**
     * Load web settings with caching
     */
    protected function loadWebSettingsWithCache(): void
    {
        $cacheKey = "web_settings_" . str_replace(['.', ':', '/', '\\', '@'], '_', $this->currentUrl) . "_v1";
        $cache = \Config\Services::cache();

        // Try to get from cache first
        $webSettingData = $cache->get($cacheKey);

        if ($webSettingData === null) {
            // Cache miss - fetch from API
            $startTime = microtime(true);
            $webSettingData = $this->makeGetRequest('/web-settings?url=' . $this->currentUrl, [], false);
            $loadTime = round((microtime(true) - $startTime) * 1000, 2);

            // Cache for 1 hour (3600 seconds)
            if (!empty($webSettingData)) {
                $cache->save($cacheKey, $webSettingData, 3600);
                log_message('info', "Web settings cached for {$this->currentUrl} (loaded in {$loadTime}ms)");
            }
        } else {
            log_message('debug', "Web settings cache hit for {$this->currentUrl}");
        }

        $siteLogoUrl = $webSettingData['logo_url'] ?? null;
        // Debug log for web settings data
        log_message('debug', 'BaseController - Web settings retrieved: ' . json_encode($webSettingData));

        // Get program_category_id from web settings
        $programCategoryId = $webSettingData['program_category_id'] ?? null;

        // Log the program category ID for debugging
        log_message('debug', 'BaseController - Program Category ID: ' . $programCategoryId);

        // Get the latest program dates if program_category_id exists
        if ($programCategoryId) {
            $this->loadProgramDataWithCache($programCategoryId, $webSettingData);
        }

        // Check if the web settings data is empty and handle accordingly
        if (empty($webSettingData)) {
            // Just store null settings instead of redirecting here
            $this->data['webSettings'] = null;
            log_message('warning', 'BaseController - No web settings found');
        } else {
            $this->data['webSettings'] = $webSettingData;
            $this->data['siteLogoUrl'] = $siteLogoUrl;

            // Store maintenance mode status in session for filter to access
            if (isset($webSettingData['is_maintenance_mode'])) {
                // Store in session
                session()->set('is_maintenance_mode', $webSettingData['is_maintenance_mode']);
                log_message('debug', 'BaseController - Saved is_maintenance_mode to session: ' . $webSettingData['is_maintenance_mode']);
            }

            // Initialize the WebSettings service as a backup approach
            try {
                $webSettingsService = \Config\Services::webSettings(true);
                $webSettingsService->setSettings($webSettingData);
            } catch (\Exception $e) {
                log_message('error', 'BaseController - Failed to initialize WebSettings service: ' . $e->getMessage());
            }

            // Direct maintenance mode check
            if (
                isset($webSettingData['is_maintenance_mode']) &&
                ($webSettingData['is_maintenance_mode'] === 1 || $webSettingData['is_maintenance_mode'] === '1') &&
                uri_string() !== 'maintenance'
            ) {
                log_message('info', 'BaseController - Redirecting to maintenance page');
                header('Location: ' . base_url('maintenance'));
                exit();
            }
        }
    }

    /**
     * Load program data with caching
     */
    protected function loadProgramDataWithCache($programCategoryId, &$webSettingData): void
    {
        $cacheKey = "programs_category_" . $programCategoryId . "_v1";
        $cache = \Config\Services::cache();

        // Try to get from cache first
        $programs = $cache->get($cacheKey);

        if ($programs === null) {
            // Cache miss - fetch from API
            $startTime = microtime(true);
            $programs = $this->makeGetRequest('/programs/category/' . $programCategoryId, [], false);
            $loadTime = round((microtime(true) - $startTime) * 1000, 2);

            // Cache for 30 minutes (1800 seconds)
            if (!empty($programs)) {
                $cache->save($cacheKey, $programs, 1800);
                log_message('info', "Programs data cached for category {$programCategoryId} (loaded in {$loadTime}ms)");
            }
        } else {
            log_message('debug', "Programs data cache hit for category {$programCategoryId}");
        }

        // Log the programs data for debugging
        log_message('debug', 'BaseController - Programs retrieved: ' . json_encode($programs));

        // Sort programs by start_date (descending) to get the latest one
        if (!empty($programs) && is_array($programs)) {
            usort($programs, function ($a, $b) {
                return strtotime($b['start_date'] ?? '0') - strtotime($a['start_date'] ?? '0');
            });

            // Get start_date and end_date from the latest program
            $latestProgram = $programs[0] ?? null;
            if ($latestProgram && isset($latestProgram['start_date']) && isset($latestProgram['end_date'])) {
                // Add these dates to the webSettings array
                $webSettingData['event_start_date'] = $latestProgram['start_date'];
                $webSettingData['event_end_date'] = $latestProgram['end_date'];

                log_message('debug', 'BaseController - Latest program dates set: ' .
                    $latestProgram['start_date'] . ' to ' . $latestProgram['end_date']);
            }
        } else {
            log_message('warning', 'BaseController - No programs found for category ID: ' . $programCategoryId);
        }
    }

    /**
     * Override the before method to check for maintenance mode
     * This runs before each controller method execution
     */
    public function before()
    {
        // Check if site is in maintenance mode and redirect if necessary
        if (!empty($this->data['maintenance_mode']) && uri_string() !== 'maintenance') {
            // This will properly redirect and exit
            return redirect()->to(base_url('maintenance'))->send();
        }
    }
    /**
     * Set the API base URL based on the current environment
     */
    protected function setApiBaseUrl()
    {
        // Check environment from .env file
        $environment = ENVIRONMENT;

        if ($environment === 'development') {
            // Use development API URL
            $this->apiBaseUrl = defined('DEV_BASE_API_URL') ? DEV_BASE_API_URL : 'http://localhost:8080/api';
            log_message('info', "[BaseController::setApiBaseUrl] Development environment detected, using: {$this->apiBaseUrl}");
        } else {
            // TODO: Change back to real API when admin.ybbfoundation.com/api is fixed
            $this->apiBaseUrl = defined('BASE_API_URL') ? BASE_API_URL : 'https://admin.ybbfoundation.com/api';
            log_message('info', "[BaseController::setApiBaseUrl] Production environment detected (using MOCK), using: {$this->apiBaseUrl}");
        }

        log_message('debug', "[BaseController::setApiBaseUrl] Environment: {$environment}");
        log_message('debug', "[BaseController::setApiBaseUrl] Final API base URL: {$this->apiBaseUrl}");
    }

    /**
     * Process and prepare the topbar data for views
     */
    protected function prepareTopbarData()
    {
        // Create an instance of TopbarController
        $topbarController = new \App\Controllers\TopbarController();

        // Get the topbar data
        $topbarData = $topbarController->processTopbarData();

        // Get program_category_id and program_type_id from webSettings
        if (isset($this->data['webSettings']['program_category_id'])) {
            $programCategoryId = $this->data['webSettings']['program_category_id'];
            log_message('debug', 'BaseController - Getting program_type_id for program_category_id: ' . $programCategoryId);

            // Get program type ID based on program category ID
            $programTypeId = $this->getProgramTypeId($programCategoryId);

            // Add program type ID to webSettings
            if ($programTypeId !== null) {
                $this->data['webSettings']['program_type_id'] = $programTypeId;
                log_message('debug', 'BaseController - Added program_type_id to webSettings: ' . $programTypeId);
            } else {
                log_message('warning', 'BaseController - Could not determine program_type_id for program_category_id: ' . $programCategoryId);
            }
        } else {
            log_message('debug', 'BaseController - No program_category_id found in webSettings');
        }

        // Merge topbar data with the existing data array
        $this->data = array_merge($this->data, $topbarData);
    }

    /**
     * Override the render method to include topbar data
     */
    protected function render($view, $data = [])
    {
        // Prepare topbar data before rendering
        $this->prepareTopbarData();

        // Merge global data with view-specific data
        $data = array_merge($this->data, $data);
        return view($view, $data);
    }
    /**
     * Get program type ID for a given program category ID
     * 
     * @param int $programCategoryId The program category ID
     * @return int|null The program type ID, or null if not found
     */
    protected function getProgramTypeId($programCategoryId)
    {
        if (!$programCategoryId) {
            log_message('debug', 'BaseController - Cannot get program type ID: program_category_id is empty');
            return null;
        }

        try {
            // Make API request to get program category details
            $programCategory = $this->makeGetRequest('/program-categories/' . $programCategoryId, [], false);

            // Log for debugging
            log_message('debug', 'BaseController - Program Category details: ' . json_encode($programCategory));

            // Return the program type ID if available
            if (isset($programCategory['program_type_id'])) {
                log_message('debug', 'BaseController - Found program_type_id: ' . $programCategory['program_type_id'] . ' for program_category_id: ' . $programCategoryId);
                return $programCategory['program_type_id'];
            } else {
                log_message('warning', 'BaseController - program_type_id not found in program category response');
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', 'BaseController - Failed to get program type ID: ' . $e->getMessage());
            return null;
        }
    }

    // create a fucntion for get requests that accepts endpoint and headers and returns response as json
    function makeGetRequest($endpoint, $headers = [], $useJwt = false)
    {
        try {
            // combine endpoint with base url
            $url = $this->apiBaseUrl . $endpoint;

            // Log the request URL for debugging
            log_message('debug', 'Making GET request to: ' . $url);

            // Add JWT token to headers if needed
            if ($useJwt) {
                $token = $this->getJwtToken();
                if ($token) {
                    $headers['Authorization'] = 'Bearer ' . $token;
                }
            }

            $response = $this->client->request('GET', $url, [
                'headers' => array_merge($this->defaultHeaders, $headers),
            ]);

            // Log the response status code
            $statusCode = $response->getStatusCode();
            log_message('debug', 'API response status code: ' . $statusCode);

            $bodyDecoded = json_decode($response->getBody(), true);

            // Log the full response for debugging
            log_message('debug', 'API response body: ' . json_encode($bodyDecoded));

            if (isset($bodyDecoded['data'])) {
                return $bodyDecoded['data'];
            } else {
                log_message('error', 'Data key not found in response');

                // get error data if available
                if (isset($bodyDecoded['errors'])) {
                    log_message('error', 'Error data: ' . json_encode($bodyDecoded['errors']));
                } else {
                    log_message('error', 'No error data available in response');
                }
                // Log the entire response for debugging
                log_message('debug', 'Response: ' . json_encode($bodyDecoded));

                return $bodyDecoded; // Return the whole response if 'data' key is not present
            }
        } catch (\CodeIgniter\HTTP\Exceptions\HTTPException $e) {
            // Handle HTTP-specific exceptions
            $statusCode = $e->getCode();
            log_message('error', 'HTTP Error: ' . $e->getMessage() . ' (Status Code: ' . $statusCode . ')');

            if ($statusCode == 404) {
                log_message('error', 'API endpoint not found: ' . $endpoint);
            }

            return null;
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            log_message('error', 'Error in makeGetRequest: ' . $e->getMessage() . ' (' . get_class($e) . ')');
            log_message('error', 'Error trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    /**
     * Make a POST request to an API endpoint with optional JWT authentication
     * 
     * @param string $endpoint The API endpoint
     * @param array|string $data The data to send
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @param bool $asJson Whether to send data as JSON or form data
     * @param bool $isMultipart Whether to send as multipart/form-data (for file uploads)
     * @param array $multipartData Array of multipart data for file uploads in format [['name' => 'field_name', 'contents' => file_contents, 'filename' => 'filename.ext']]
     * @return mixed Response data or null on error
     */
    function makePostRequest($endpoint, $data, $headers = [], $useJwt = false, $asJson = false, $isMultipart = false, $multipartData = [])
    {
        try {
            $url = $this->apiBaseUrl . $endpoint;
            $options = [];

            // Handle data format based on parameters
            if ($isMultipart) {
                // Use multipart/form-data format (for file uploads)
                $options['multipart'] = [];

                // Add regular form fields to multipart data
                if (is_array($data)) {
                    foreach ($data as $name => $contents) {
                        // Convert any arrays to JSON strings to prevent "Array to string conversion" errors
                        if (is_array($contents)) {
                            $contents = json_encode($contents);
                        }
                        $options['multipart'][] = [
                            'name' => $name,
                            'contents' => $contents
                        ];
                    }
                }

                // Add provided multipart data (files)
                if (!empty($multipartData)) {
                    foreach ($multipartData as $part) {
                        // Ensure each part has the required fields and is properly formatted
                        if (isset($part['name']) && isset($part['contents']) && isset($part['filename'])) {
                            $options['multipart'][] = $part;
                        } else {
                            log_message('warning', 'Skipping invalid multipart data: ' . json_encode($part));
                        }
                    }
                }

                // For multipart requests, we need to let the client set the Content-Type
                // Create headers without Content-Type to let Guzzle set it properly for multipart data
                $multipartHeaders = [
                    'Accept' => 'application/json'
                ];

                // Do NOT include the default headers which contain Content-Type: application/json
                // This is critical for multipart requests
                $options['headers'] = array_merge($multipartHeaders, $headers);

                // Explicitly remove Content-Type if it exists to let Guzzle handle it
                if (isset($options['headers']['Content-Type'])) {
                    unset($options['headers']['Content-Type']);
                }
            } else if ($asJson) {
                // Use JSON format
                $options['body'] = is_array($data) ? json_encode($data) : $data;
                $options['headers'] = array_merge($this->defaultHeaders, $headers);

                // Explicitly ensure Content-Type is set for JSON
                $options['headers']['Content-Type'] = 'application/json';
                log_message('debug', 'POST as JSON with Content-Type: application/json');
            } else {
                // Use regular form data format
                $options['form_params'] = $data;

                // Only include Content-Type: application/json if explicitly requested
                $formHeaders = [
                    'Accept' => 'application/json'
                ];
                $options['headers'] = array_merge($formHeaders, $headers);
                log_message('debug', 'POST as form data without JSON Content-Type');
            }

            // Add JWT token to headers if needed
            if ($useJwt) {
                $token = $this->getJwtToken();
                if ($token) {
                    $options['headers']['Authorization'] = 'Bearer ' . $token;
                }
            }            // Log the URL and request data for debugging
            log_message('debug', "POST Request URL: " . $url);
            log_message('debug', "POST Request Data: " . json_encode($data));
            log_message('debug', "POST Request Headers: " . json_encode($options['headers']));
            log_message('debug', "POST Request as JSON: " . ($asJson ? 'Yes' : 'No'));

            // Set up additional options
            $options['http_errors'] = false; // Don't throw exceptions for error responses
            $options['timeout'] = 30; // Set a longer timeout for API calls
            $options['connect_timeout'] = 10; // Add connection timeout

            // Make the request
            try {
                // Make the request
                $response = $this->client->request('POST', $url, $options);
                // Get response body
                $responseBody = $response->getBody();
                $bodyText = '';

                // Safely read the response body
                if ($responseBody) {
                    $bodyText = (string)$responseBody;
                }

                $bodyDecoded = null;
                if (!empty($bodyText)) {
                    $bodyDecoded = json_decode($bodyText, true);
                }

                // Get HTTP status code
                $statusCode = $response->getStatusCode();
                log_message('debug', "POST Response Status Code: " . $statusCode);

                // Log response body for debugging
                log_message('debug', "POST Response Body: " . ($bodyText ?: 'empty'));

                // Check if response is valid JSON
                if ($bodyText && json_last_error() !== JSON_ERROR_NONE) {
                    log_message('error', "POST Response is not valid JSON: " . json_last_error_msg());
                    // Try to return something useful even if it's not JSON
                    return ['error' => 'Invalid JSON response', 'raw_response' => $bodyText];
                }

                // Return the data or full response based on response structure                
                if ($bodyDecoded && isset($bodyDecoded['data'])) {
                    return $bodyDecoded['data'];
                } else {
                    return $bodyDecoded ?: ['error' => 'empty_response', 'status_code' => $statusCode]; // Return the whole response if 'data' key is not present
                }
            } catch (\Exception $e) {
                // Handle all connection errors (timeouts, DNS failures, etc.)
                $isConnectionError = (strpos($e->getMessage(), 'Connection') !== false ||
                    strpos($e->getMessage(), 'timeout') !== false ||
                    strpos($e->getMessage(), 'DNS') !== false);

                if ($isConnectionError) {
                    log_message('error', 'POST Request Connection Error: ' . $e->getMessage());
                    log_message('error', 'POST Request to URL: ' . ($url ?? 'unknown'));
                    return ['error' => 'connection_failed', 'message' => 'Could not connect to API server'];
                } else {
                    // Handle all other errors
                    $errorMessage = $e->getMessage();
                    log_message('error', 'POST Request Error: ' . $errorMessage);
                    log_message('error', 'POST Request to URL: ' . ($url ?? 'unknown'));
                    log_message('error', 'Exception trace: ' . $e->getTraceAsString());

                    return ['error' => 'request_failed', 'message' => $errorMessage];
                }
            }
        } catch (\Exception $e) {
            // Outer catch block for any unexpected errors
            $errorMessage = $e->getMessage();
            log_message('error', 'Unexpected POST Request Error: ' . $errorMessage);
            log_message('error', 'POST Request to URL: ' . ($url ?? 'unknown'));
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return ['error' => 'unexpected_error', 'message' => $errorMessage];
        }
    }
    /**
     * Make a POST request to an API endpoint and return the full response (not just data)
     * 
     * @param string $endpoint The API endpoint
     * @param array|string $data The data to send
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @param bool $asJson Whether to send data as JSON
     * @param bool $isMultipart Whether to send as multipart
     * @param array $multipartData Additional multipart data
     * @return mixed Full response data
     */
    function makePostRequestFullResponse($endpoint, $data, $headers = [], $useJwt = false, $asJson = false, $isMultipart = false, $multipartData = [])
    {
        try {
            $url = $this->apiBaseUrl . $endpoint;
            $options = [];

            // Handle data format based on parameters
            if ($isMultipart) {
                // Use multipart/form-data format (for file uploads)
                $options['multipart'] = [];

                // Add regular form fields to multipart data
                if (is_array($data)) {
                    foreach ($data as $name => $contents) {
                        // Convert any arrays to JSON strings to prevent "Array to string conversion" errors
                        if (is_array($contents)) {
                            $contents = json_encode($contents);
                        }
                        $options['multipart'][] = [
                            'name' => $name,
                            'contents' => $contents
                        ];
                    }
                }

                // Add provided multipart data (files)
                if (!empty($multipartData)) {
                    foreach ($multipartData as $part) {
                        // Ensure each part has the required fields and is properly formatted
                        if (isset($part['name']) && isset($part['contents']) && isset($part['filename'])) {
                            $options['multipart'][] = $part;
                        } else {
                            log_message('warning', 'Skipping invalid multipart data: ' . json_encode($part));
                        }
                    }
                }

                // For multipart requests, we need to let the client set the Content-Type
                // Create headers without Content-Type to let Guzzle set it properly for multipart data
                $multipartHeaders = [
                    'Accept' => 'application/json'
                ];

                // Do NOT include the default headers which contain Content-Type: application/json
                // This is critical for multipart requests
                $options['headers'] = array_merge($multipartHeaders, $headers);

                // Explicitly remove Content-Type if it exists to let Guzzle handle it
                if (isset($options['headers']['Content-Type'])) {
                    unset($options['headers']['Content-Type']);
                }
            } else if ($asJson) {
                // Use JSON format
                $options['body'] = is_array($data) ? json_encode($data) : $data;
                $options['headers'] = array_merge($this->defaultHeaders, $headers);

                // Explicitly ensure Content-Type is set for JSON
                $options['headers']['Content-Type'] = 'application/json';
                log_message('debug', 'POST as JSON with Content-Type: application/json');
            } else {
                // Use regular form data format
                $options['form_params'] = $data;

                // Only include Content-Type: application/json if explicitly requested
                $formHeaders = [
                    'Accept' => 'application/json'
                ];
                $options['headers'] = array_merge($formHeaders, $headers);
                log_message('debug', 'POST as form data without JSON Content-Type');
            }

            // Add JWT token to headers if needed
            if ($useJwt) {
                $token = $this->getJwtToken();
                if ($token) {
                    $options['headers']['Authorization'] = 'Bearer ' . $token;
                }
            }

            // Log the URL and request data for debugging
            log_message('debug', "POST Request URL: " . $url);
            log_message('debug', "POST Request Data: " . json_encode($data));
            log_message('debug', "POST Request Headers: " . json_encode($options['headers']));
            log_message('debug', "POST Request as JSON: " . ($asJson ? 'Yes' : 'No'));

            // Set up additional options
            $options['http_errors'] = false; // Don't throw exceptions for error responses
            $options['timeout'] = 30; // Set a longer timeout for API calls
            $options['connect_timeout'] = 10; // Add connection timeout

            // Make the request
            try {
                $response = $this->client->request('POST', $url, $options);

                // Get response body
                $responseBody = $response->getBody();
                $bodyText = '';

                // Safely read the response body
                if ($responseBody) {
                    $bodyText = (string)$responseBody;
                }

                $bodyDecoded = null;
                if (!empty($bodyText)) {
                    $bodyDecoded = json_decode($bodyText, true);
                }

                // Get HTTP status code
                $statusCode = $response->getStatusCode();
                log_message('debug', "POST Response Status Code: " . $statusCode);

                // Log response body for debugging
                log_message('debug', "POST Response Body: " . ($bodyText ?: 'empty'));

                // Check if response is valid JSON
                if ($bodyText && json_last_error() !== JSON_ERROR_NONE) {
                    log_message('error', "POST Response is not valid JSON: " . json_last_error_msg());
                    // Try to return something useful even if it's not JSON
                    return ['error' => 'Invalid JSON response', 'raw_response' => $bodyText];
                }

                // Return the FULL response, not just the data portion
                return $bodyDecoded ?: ['error' => 'empty_response', 'status_code' => $statusCode];
            } catch (\Exception $e) {
                // Handle all connection errors (timeouts, DNS failures, etc.)
                $isConnectionError = (strpos($e->getMessage(), 'Connection') !== false ||
                    strpos($e->getMessage(), 'timeout') !== false ||
                    strpos($e->getMessage(), 'DNS') !== false);

                if ($isConnectionError) {
                    log_message('error', 'POST Request Connection Error: ' . $e->getMessage());
                    log_message('error', 'POST Request to URL: ' . ($url ?? 'unknown'));
                    return ['error' => 'connection_failed', 'message' => 'Could not connect to API server'];
                } else {
                    // Handle all other errors
                    $errorMessage = $e->getMessage();
                    log_message('error', 'POST Request Error: ' . $errorMessage);
                    log_message('error', 'POST Request to URL: ' . ($url ?? 'unknown'));
                    log_message('error', 'Exception trace: ' . $e->getTraceAsString());

                    return ['error' => 'request_failed', 'message' => $errorMessage];
                }
            }
        } catch (\Exception $e) {
            // Outer catch block for any unexpected errors
            $errorMessage = $e->getMessage();
            log_message('error', 'Unexpected POST Request Error: ' . $errorMessage);
            log_message('error', 'POST Request to URL: ' . ($url ?? 'unknown'));
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return ['error' => 'unexpected_error', 'message' => $errorMessage];
        }
    }
    /**
     * Make a PUT request to an API endpoint with optional JWT authentication
     * 
     * @param string $endpoint The API endpoint
     * @param array|string $data The data to send
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @param bool $returnException Whether to return exception (true) or throw it (false)
     * @return mixed Response data or null on error
     */
    function makePutRequest($endpoint, $data, $headers = [], $useJwt = false, $returnException = false)
    {
        try {
            $url = $this->apiBaseUrl . $endpoint;

            // Log the request details (without sensitive information)
            $logData = $data;
            if (isset($logData['password'])) $logData['password'] = '***';
            log_message('info', 'Making PUT request to: ' . $url . ' with data: ' . json_encode($logData));

            // Prepare the request body
            $requestBody = is_array($data) ? json_encode($data) : $data;

            // Add JWT token to headers if needed
            if ($useJwt) {
                $token = $this->getJwtToken();
                if ($token) {
                    $headers['Authorization'] = 'Bearer ' . $token;
                }
            }

            $response = $this->client->request('PUT', $url, [
                'headers' => array_merge($this->defaultHeaders, $headers),
                'body' => $requestBody,
            ]);

            $bodyDecoded = json_decode($response->getBody(), true);

            if (isset($bodyDecoded['data'])) {
                return $bodyDecoded['data'];
            } else {
                return $bodyDecoded; // Return the whole response if 'data' key is not present
            }
        } catch (\Exception $e) {
            log_message('error', 'PUT Request Error: ' . $e->getMessage());
            if ($returnException) {
                return $e;
            } else {
                throw $e;
            }
            return null;
        }
    }

    /**
     * Make a DELETE request to an API endpoint with optional JWT authentication
     * 
     * @param string $endpoint The API endpoint
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @return mixed Response data or null on error
     */
    function makeDeleteRequest($endpoint, $headers = [], $useJwt = false)
    {
        try {
            $url = $this->apiBaseUrl . $endpoint;

            // Add JWT token to headers if needed
            if ($useJwt) {
                $token = $this->getJwtToken();
                if ($token) {
                    $headers['Authorization'] = 'Bearer ' . $token;
                }
            }

            $response = $this->client->request('DELETE', $url, [
                'headers' => array_merge($this->defaultHeaders, $headers),
            ]);

            $bodyDecoded = json_decode($response->getBody(), true);

            if (isset($bodyDecoded['data'])) {
                return $bodyDecoded['data'];
            } else {
                return $bodyDecoded; // Return the whole response if 'data' key is not present
            }
        } catch (\Exception $e) {
            log_message('error', 'DELETE Request Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get JWT token from session or request a new one from the API
     * 
     * @return string|null JWT token or null if not available
     */
    protected function getJwtToken()
    {
        // Check if token exists in session
        if (session()->has('jwt_token')) {
            // In a real implementation, you would check token expiration here
            // For now, we'll just return the token
            return session()->get('jwt_token');
        }

        // If user is logged in but token is missing, redirect to login
        if (session()->get('isLoggedIn') && !session()->has('jwt_token')) {
            session()->remove('isLoggedIn');
            session()->remove('user');

            // In a production app, you might want to redirect to login here
            // but for a library function, we'll just return null
            log_message('error', 'JWT token missing for logged in user');
        }

        return null;
    }
}
