<?php

if (!function_exists('getBaseDomain')) {
    function getBaseDomain()
    {
        $request = \Config\Services::request();
        $scheme = $request->getServer('REQUEST_SCHEME') ?? 'https'; // Default to https if REQUEST_SCHEME is not available
        $host = $request->getServer('HTTP_HOST') ?? $_SERVER['HTTP_HOST'] ?? 'localhost'; // Fallback to $_SERVER if needed
        
        log_message('debug', 'getBaseDomain - scheme: ' . $scheme . ', host: ' . $host);
        
        return $scheme . '://' . $host;
    }
}

/**
 * Creates a URL-friendly slug from a string
 *
 * @param string $string The string to convert to a slug
 * @param string $separator The separator to use between words (default: hyphen)
 * @return string The formatted slug
 */
if (!function_exists('create_slug')) {
    function create_slug($string, $separator = '-')
    {
        // Convert all characters to lowercase
        $string = strtolower($string);
        
        // Replace non-alphanumeric characters with the separator
        $string = preg_replace('/[^a-z0-9]/', $separator, $string);
        
        // Remove duplicate separators
        $string = preg_replace('/' . preg_quote($separator) . '+/', $separator, $string);
        
        // Remove separators from the beginning and end of the string
        $string = trim($string, $separator);
        
        return $string;
    }
}
