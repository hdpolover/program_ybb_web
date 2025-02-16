<?php

if (!function_exists('getBaseDomain')) {
    function getBaseDomain()
    {
        $request = \Config\Services::request();
        $scheme = $request->getServer('REQUEST_SCHEME'); // "http" or "https"
        $host = $request->getServer('HTTP_HOST');        // "example.com"

        return $scheme . '://' . $host;
    }
}
