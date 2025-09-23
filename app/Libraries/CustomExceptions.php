<?php

namespace App\Libraries;

use CodeIgniter\Debug\Exceptions;
use CodeIgniter\Exceptions\HTTPExceptionInterface;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use ErrorException;
use Throwable;

class CustomExceptions extends Exceptions
{
    /**
     * Initialize the exception handler with proper services
     */
    public function __construct(\Config\Exceptions $config)
    {
        parent::__construct($config);
        
        // Ensure request and response are available
        if (!$this->request) {
            $this->request = Services::request();
        }
        if (!$this->response) {
            $this->response = Services::response();
        }
    }

    /**
     * We're overriding the exceptionHandler method instead of render
     * This method is publicly accessible and is called by the error handler
     */
    public function exceptionHandler(Throwable $exception)
    {
        [$statusCode, $exitCode] = $this->determineCodes($exception);

        // Handle timeout errors
        if ($this->isTimeoutException($exception)) {
            $this->handleTimeoutException($exception, $exitCode);
            return;
        }

        // Handle 404 errors specifically
        if ($exception instanceof PageNotFoundException || $statusCode === 404) {
            // Use the error helper to get data
            if (function_exists('get_error_data')) {
                $errorData = get_error_data(404);
            } else {
                // Fallback if helper isn't loaded
                $errorData = [
                    'webSettings' => [
                        'logo_url' => '/assets/images/logo.png',
                        'site_name' => 'Your Website Name',
                        'contact_email' => 'support@example.com'
                    ],
                    'customMessage' => 'The page you were looking for could not be found.',
                    'popularLinks' => [
                        ['url' => '/', 'title' => 'Home'],
                        ['url' => '/contact', 'title' => 'Contact Us'],
                        ['url' => '/about', 'title' => 'About Us']
                    ]
                ];
            }
            
            // Log the error if configured to do so
            if ($this->config->log === true && ! in_array($statusCode, $this->config->ignoreCodes, true)) {
                log_message('critical', "{message}\nin {exFile} on line {exLine}.\n{trace}", [
                    'message' => $exception->getMessage(),
                    'exFile'  => clean_path($exception->getFile()),
                    'exLine'  => $exception->getLine(),
                    'trace'   => $exception->getTraceAsString(),
                ]);
            }
            
            // For CLI requests, use the parent handler
            if (is_cli()) {
                parent::exceptionHandler($exception);
                return;
            }
            
            // Ensure we have valid request and response objects
            if (!$this->response) {
                $this->response = Services::response();
            }
            if (!$this->request) {
                $this->request = Services::request();
            }
            
            // Set the status code
            $this->response->setStatusCode($statusCode);
            
            // Set HTTP headers
            if (! headers_sent()) {
                header(sprintf(
                    'HTTP/%s %s %s', 
                    $this->request->getProtocolVersion(), 
                    $this->response->getStatusCode(), 
                    $this->response->getReasonPhrase()
                ), true, $statusCode);
            }
            
            // For non-HTML requests (API, etc.), return JSON
            $acceptHeader = $this->request->getHeaderLine('accept');
            if (!$acceptHeader || strpos($acceptHeader, 'text/html') === false) {
                $this->respond(ENVIRONMENT === 'development' ? $this->collectVars($exception, $statusCode) : '', $statusCode)->send();
                exit($exitCode);
            }
            
            // For HTML requests, render the custom 404 view with additional data
            $viewFile = $this->viewPath . 'html/error_404.php';
            $altViewFile = APPPATH . 'Views/errors/html/error_404.php';
            
            // Check if the view exists
            if (is_file($viewFile)) {
                $finalViewFile = $viewFile;
            } elseif (is_file($altViewFile)) {
                $finalViewFile = $altViewFile;
            } else {
                // If no view file is found, use the parent handler
                parent::exceptionHandler($exception);
                return;
            }
            
            // Prepare variables for the view
            $vars = array_merge($this->collectVars($exception, $statusCode), $errorData);
            
            // Clean any output buffers
            if (ob_get_level() > $this->ob_level + 1) {
                ob_end_clean();
            }
            
            // Render the view
            echo(function () use ($vars, $finalViewFile): string {
                extract($vars, EXTR_SKIP);
                ob_start();
                include $finalViewFile;
                return ob_get_clean() ?: '';
            })();
            
            exit($exitCode);
        }        // For non-404 errors, use the parent handler
        parent::exceptionHandler($exception);
    }
    
    /**
     * Checks if an exception is a timeout error
     *
     * @param Throwable $exception The exception to check
     * @return bool
     */
    protected function isTimeoutException(Throwable $exception): bool
    {
        $message = $exception->getMessage();
        
        // Look for common timeout phrases
        $timeoutPhrases = [
            'Maximum execution time of',
            'exceeded',
            'timeout',
            'timed out',
            'Connection timed out',
            'CURL error (28)',
            'Operation timed out',
        ];
        
        // Check if file is CURLRequest
        $isTimeoutFile = stripos($exception->getFile(), 'CURLRequest.php') !== false;
        
        // Check if message contains timeout phrases
        $hasTimeoutPhrase = false;
        foreach ($timeoutPhrases as $phrase) {
            if (stripos($message, $phrase) !== false) {
                $hasTimeoutPhrase = true;
                break;
            }
        }
        
        return ($isTimeoutFile && $hasTimeoutPhrase) || 
               (stripos($message, 'Maximum execution time') !== false);
    }
    
    /**
     * Handles a timeout exception
     *
     * @param Throwable $exception The exception to handle
     * @param int $exitCode The exit code
     */
    protected function handleTimeoutException(Throwable $exception, int $exitCode): void
    {
        // Set the status code to 504 (Gateway Timeout)
        $statusCode = 504;
        
        // Ensure we have a valid response object
        if (!$this->response) {
            $this->response = Services::response();
        }
        
        // Ensure we have a valid request object
        if (!$this->request) {
            $this->request = Services::request();
        }
        
        $this->response->setStatusCode($statusCode);
        
        // Set HTTP headers
        if (!headers_sent()) {
            header(sprintf(
                'HTTP/%s %s %s',
                $this->request->getProtocolVersion(),
                $this->response->getStatusCode(),
                $this->response->getReasonPhrase()
            ), true, $statusCode);
        }
          // For non-HTML requests (API, etc.), return JSON
        $acceptHeader = $this->request->getHeaderLine('accept');
        if (!$acceptHeader || strpos($acceptHeader, 'text/html') === false) {
            // Use the AjaxHandler controller for consistent handling
            $ajaxHandler = new \App\Controllers\AjaxHandler();
            $response = $ajaxHandler->timeout();
            
            if (ENVIRONMENT === 'development') {
                $errorData = json_decode($response->getJSON(), true);
                $errorData['details'] = $exception->getMessage();
                $errorData['file'] = clean_path($exception->getFile());
                $errorData['line'] = $exception->getLine();
                $response->setJSON($errorData);
            }
            
            $response->send();
            exit($exitCode);
        }
        // For HTML requests, use SweetAlert for a better user experience
        
        // Clean any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $errorDetails = '';
        if (ENVIRONMENT === 'development') {
            $errorDetails = htmlspecialchars($exception->getMessage()) . 
                ' in ' . clean_path($exception->getFile()) . 
                ' on line ' . $exception->getLine();
        }        // Display a simple HTML page with SweetAlert directly
        // without causing redirect loops
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Timeout</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; }
        .error-container { max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #e3e3e3; border-radius: 5px; }
        h1 { color: #d9534f; }
        .actions { margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 15px; margin: 0 5px; text-decoration: none; background-color: #007bff; color: white; border-radius: 5px; }
        .btn-secondary { background-color: #6c757d; }
        .error-details { margin-top: 30px; color: #6c757d; font-size: 12px; }
    </style>
    <!-- Load SweetAlert directly -->
    <script src="' . base_url('assets/js/sweetalert2.all.min.js') . '"></script>
</head>
<body>
    <div class="error-container">
        <h1>Request Timeout</h1>
        <p>The server took too long to process your request. Please try again later.</p>
        
        <div class="actions">
            <a href="' . current_url() . '" class="btn">Try Again</a>
            <a href="' . site_url('/') . '" class="btn btn-secondary">Go to Home</a>
        </div>
        
        <div class="error-details">' . $errorDetails . '</div>
    </div>
    
    <script>
    // Show SweetAlert directly without redirecting
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Request Timeout",
                text: "The server took too long to process your request. Please try again later.",
                icon: "warning",
                confirmButtonText: "Try Again",
                showCancelButton: true,
                cancelButtonText: "Go to Home"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                } else {
                    window.location.href = "' . site_url('/') . '";
                }
            });
        }
    });
    </script>
</body>
</html>';
        
        exit($exitCode);
    }
}