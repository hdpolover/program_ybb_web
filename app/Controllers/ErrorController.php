<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ErrorController extends BaseController
{
    

    /**
     * Custom 404 error page
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function error404()
    {
        // Set status code
        return $this->response->setStatusCode(404)
            ->setBody(view('errors/html/error_404', [
                'webSettings' => $this->data['webSettings'],
                'customMessage' => 'Sorry, the page you requested was not found.',
                'popularLinks' => [
                    ['url' => '/', 'title' => 'Home']
                ]
            ]));
    }
}