<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = (string) $request->getUri();
        log_message('debug', 'Auth Filter - Checking authentication for URI: ' . $uri);
        
        $isLoggedIn = session()->get('isLoggedIn');
        $hasToken = session()->has('jwt_token');
        
        log_message('debug', 'Auth Filter - isLoggedIn: ' . ($isLoggedIn ? 'true' : 'false') . ', hasToken: ' . ($hasToken ? 'true' : 'false'));
        
        // Check if user is logged in with a valid token
        if (!$isLoggedIn || !$hasToken) {
            log_message('warning', 'Auth Filter - Authentication failed for URI: ' . $uri . ' - redirecting to sign-in');
            
            // If token missing but still marked as logged in, clean up the session
            if ($isLoggedIn && !$hasToken) {
                log_message('debug', 'Auth Filter - Cleaning up session (logged in but no token)');
                session()->remove('isLoggedIn');
                session()->remove('user');
            }
            
            return redirect()->to(base_url('sign-in'))->with('error', 'Please sign in to access this page');
        }
        
        log_message('debug', 'Auth Filter - Authentication successful for URI: ' . $uri);
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
