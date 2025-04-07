<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Cors implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get CORS config
        $config = new \Config\Cors();
        
        // Get the origin from the request
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        // Check if we should allow all origins
        if ($config->allowAllOrigins === true) {
            // Allow all origins - less secure but more flexible
            // Note: When using wildcard origin, credentials won't work
            // So we must specify the actual origin if credentials are needed
            if ($config->allowCredentials === true && $origin) {
                header("Access-Control-Allow-Origin: $origin");
            } else {
                header("Access-Control-Allow-Origin: *");
            }
        } 
        // Otherwise, check if the origin is in our allowed list
        else if (!empty($origin) && is_array($config->allowedOrigins) && in_array($origin, $config->allowedOrigins)) {
            // Set the specific allowed origin
            header("Access-Control-Allow-Origin: $origin");
        } else {
            // Default fallback to allow local development
            header("Access-Control-Allow-Origin: http://localhost:8081");
        }
        
        // Set allowed methods
        $methods = is_array($config->allowedMethods) 
            ? implode(', ', $config->allowedMethods) 
            : 'GET, POST, OPTIONS, PUT, DELETE, PATCH';
        header('Access-Control-Allow-Methods: ' . $methods);
        
        // Set allowed headers
        $headers = is_array($config->allowedHeaders)
            ? implode(', ', $config->allowedHeaders)
            : 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization';
        header('Access-Control-Allow-Headers: ' . $headers);
        
        // Set credentials header
        if ($config->allowCredentials === true) {
            header('Access-Control-Allow-Credentials: true');
        } else {
            header('Access-Control-Allow-Credentials: true'); // Default to true for backward compatibility
        }
        
        // Add cache control for preflight requests
        $maxAge = $config->maxAge ?? 7200;
        header('Access-Control-Max-Age: ' . $maxAge);
        
        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
            // Return early for preflight requests
            exit();
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}