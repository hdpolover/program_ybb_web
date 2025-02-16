<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

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

        // get base domain
        $baseDomain = getBaseDomain();
        $currentUrl = "";

        if ($baseDomain === "://localhost:8080") {
            $currentUrl = "https://koreayouthsummit.com";
        } else {
            $currentUrl = $baseDomain;
        }

        $this->data['program_info'] = $this->makeGetRequest('/program_categories/web?url=' . $currentUrl); 
    }

    function getProgramInfoDetail($key) {
        return $this->data['program_info'][$key];
    }

    protected function render($view, $data = [])
    {
        // Merge global data with view-specific data
        $data = array_merge($this->data, $data);
        return view($view, $data);
    }

    // create a fucntion for get requests that accepts endpoint and headers and returns response as json
    function makeGetRequest($endpoint, $headers = [])
    {
        try {
            // combine endpoint with base url
            $url = BASE_API_URL . $endpoint;

            $response = $this->client->request('GET', $url, [
            'headers' => array_merge($this->defaultHeaders, $headers),
            ]);

            $bodyDecoded = json_decode($response->getBody(), true);

            return $bodyDecoded['data'];
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            log_message('error', $e->getMessage());
            return null;
        }
    }

}
