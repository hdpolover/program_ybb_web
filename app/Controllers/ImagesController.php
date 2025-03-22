<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class ImagesController extends Controller
{
    /**
     * Serve a cached image from the writable directory
     *
     * @param string $filename The image filename to serve
     * @return mixed
     */
    public function serve($filename = null)
    {
        if (empty($filename)) {
            return $this->response->setStatusCode(404);
        }

        $cachePath = WRITEPATH . 'cache/images/' . $filename;
        
        if (!file_exists($cachePath)) {
            return $this->response->setStatusCode(404);
        }

        $this->response->setHeader('Content-Type', 'image/jpeg');
        $this->response->setHeader('Cache-Control', 'max-age=2592000'); // 30 days cache
        
        // Output the file contents
        return $this->response->setBody(file_get_contents($cachePath));
    }
}
