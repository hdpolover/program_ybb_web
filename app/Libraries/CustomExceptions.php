<?php

namespace App\Libraries;

use CodeIgniter\Debug\Exceptions;
use CodeIgniter\Exceptions\HTTPExceptionInterface;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Throwable;

class CustomExceptions extends Exceptions
{
    /**
     * We're overriding the exceptionHandler method instead of render
     * This method is publicly accessible and is called by the error handler
     */
    public function exceptionHandler(Throwable $exception)
    {
        [$statusCode, $exitCode] = $this->determineCodes($exception);

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
            if (strpos($this->request->getHeaderLine('accept'), 'text/html') === false) {
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
        }

        // For non-404 errors, use the parent handler
        parent::exceptionHandler($exception);
    }
}