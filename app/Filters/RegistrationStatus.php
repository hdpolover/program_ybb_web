<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


class RegistrationStatus implements FilterInterface
{
    /**
     * Check if registration is open
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Get the registration status from the configuration or database
        $isRegistrationOpen = config('App')->isRegistrationOpen ?? false;
        
        // If registration is closed, redirect to a specific page or return an error
        if (!$isRegistrationOpen) {
            return redirect()->to('/registration-closed')->with('error', 'Registration is currently closed.');
        }
        
        // If registration is open, continue with the request
        return $request;
    }

    /**
     * We don't have anything to do after the controller is executed
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing, this filter only needs to run before the controller
        return $response;
    }
}