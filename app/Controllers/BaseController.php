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

        if ($baseDomain === "://localhost:8081") {
            $this->currentUrl = "https://worldyouthfest.com";
        } else {
            $this->currentUrl = $baseDomain;
        }

        // Remove protocol (http:// or https://) from the current URL
        $this->currentUrl = preg_replace('~^https?://~', '', $this->currentUrl);

        $webSettingData = $this->makeGetRequest('/web-settings?url=' . $this->currentUrl, [], false);

        // Debug log for web settings data
        log_message('debug', 'BaseController - Web settings retrieved: ' . json_encode($webSettingData));

        // Check if the web settings data is empty and handle accordingly
        if (empty($webSettingData)) {
            // Just store null settings instead of redirecting here
            $this->data['webSettings'] = null;
            log_message('warning', 'BaseController - No web settings found');
        } else {
            $this->data['webSettings'] = $webSettingData;
            
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
        // Check if we're in development environment
        if (ENVIRONMENT === 'development' || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false) {
            $this->apiBaseUrl = DEV_BASE_API_URL;
        } else {
            // Use production URL for all other environments
            $this->apiBaseUrl = BASE_API_URL;
        }
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

    // create a fucntion for get requests that accepts endpoint and headers and returns response as json
    function makeGetRequest($endpoint, $headers = [], $useJwt = false)
    {
        try {
            // combine endpoint with base url
            $url = $this->apiBaseUrl . $endpoint;

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

            $bodyDecoded = json_decode($response->getBody(), true);

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
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            log_message('error', $e->getMessage());
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
     * @return mixed Response data or null on error
     */
    function makePostRequest($endpoint, $data, $headers = [], $useJwt = false, $asJson = false)
    {
        try {
            $url = $this->apiBaseUrl . $endpoint;
            $options = [];

            // Handle data format based on $asJson parameter
            if ($asJson) {
                // Use JSON format
                $options['body'] = is_array($data) ? json_encode($data) : $data;
                $options['headers'] = array_merge($this->defaultHeaders, $headers);
            } else {
                // Use form data format
                $options['form_params'] = $data;
                
                // Only include Content-Type: application/json if explicitly requested
                $formHeaders = [
                    'Accept' => 'application/json'
                ];
                $options['headers'] = array_merge($formHeaders, $headers);
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
            
            // Make the request
            $response = $this->client->request('POST', $url, $options);
            
            // Get response body
            $responseBody = $response->getBody();
            $bodyDecoded = json_decode($responseBody, true);
            
            // Log response body for debugging
            log_message('debug', "POST Response Body: " . json_encode($bodyDecoded));

            // Return the data or full response based on response structure
            if (isset($bodyDecoded['data'])) {
                return $bodyDecoded['data'];
            } else {
                return $bodyDecoded; // Return the whole response if 'data' key is not present
            }
        } catch (\Exception $e) {
            log_message('error', 'POST Request Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Make a PUT request to an API endpoint with optional JWT authentication
     * 
     * @param string $endpoint The API endpoint
     * @param array|string $data The data to send
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @return mixed Response data or null on error
     */
    function makePutRequest($endpoint, $data, $headers = [], $useJwt = false)
    {
        try {
            $url = $this->apiBaseUrl . $endpoint;

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
