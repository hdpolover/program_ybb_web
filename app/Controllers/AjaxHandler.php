<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class AjaxHandler extends BaseController
{
    /**
     * Handle AJAX requests and provide consistent responses
     * 
     * @return ResponseInterface
     */
    public function timeout()
    {
        // Set proper content type for AJAX response
        $this->response->setContentType('application/json');
        
        return $this->response->setJSON([
            'success' => false,
            'error' => 'timeout',
            'message' => 'The server took too long to process your request. Please try again later.',
            'title' => 'Request Timeout'
        ]);
    }
    
    /**
     * Handle general AJAX errors
     * 
     * @return ResponseInterface
     */
    public function error($code = 500)
    {
        // Map error codes to messages
        $errorMessages = [
            '400' => 'Bad request. Please check your inputs and try again.',
            '401' => 'Authentication required. Please log in again.',
            '403' => 'You do not have permission to perform this action.',
            '404' => 'The requested resource was not found.',
            '500' => 'An unexpected error occurred on the server.',
            '503' => 'Service temporarily unavailable. Please try again later.',
            '504' => 'Gateway timeout. The server took too long to respond.'
        ];
        
        $message = $errorMessages[$code] ?? 'An unexpected error occurred.';
        
        // Set proper content type for AJAX response
        $this->response->setContentType('application/json');
        
        return $this->response->setJSON([
            'success' => false,
            'error' => 'server_error',
            'code' => $code,
            'message' => $message,
            'title' => 'Server Error'
        ]);
    }
}
