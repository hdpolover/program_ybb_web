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
            $this->currentUrl = "https://koreayouthsummit.com";
        } else {
            $this->currentUrl = $baseDomain;
        }

        // Remove protocol (http:// or https://) from the current URL
        $this->currentUrl = preg_replace('~^https?://~', '', $this->currentUrl);
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

    protected function render($view, $data = [])
    {
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
                return null;
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
     * @return mixed Response data or null on error
     */
    function makePostRequest($endpoint, $data, $headers = [], $useJwt = false)
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
            
            $response = $this->client->request('POST', $url, [
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
            // You might want to check if the token is expired here
            return session()->get('jwt_token');
        }
        
        // If implementation requires fetching a new token, add that logic here
        // Example:
        // $response = $this->client->request('POST', $this->apiBaseUrl . '/auth/token', [...]);
        // $token = json_decode($response->getBody(), true)['token'];
        // session()->set('jwt_token', $token);
        // return $token;
        
        return null;
    }
}
