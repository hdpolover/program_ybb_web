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

/**
 * Check if registration is actually available for a program
 * 
 * This function properly checks both the global registration flag and individual
 * registration payment options (self_funded and fully_funded) to determine if
 * any registration path is currently open and available.
 *
 * @param array $program The program data array
 * @return bool True if any registration option is available, false otherwise
 */
if (!function_exists('is_registration_actually_available')) {
    function is_registration_actually_available($program)
    {
        // First check the global registration flag
        if (!isset($program['is_registration_open']) || $program['is_registration_open'] != "1") {
            return false;
        }
        
        // If there are no specific registration payment options, use the global flag
        if (!isset($program['registration_payments']) || empty($program['registration_payments'])) {
            return true;
        }
        
        $currentDate = new DateTime();
        $registrationPayments = $program['registration_payments'];
        
        // Check self_funded option
        if (isset($registrationPayments['self_funded'])) {
            $selfFunded = $registrationPayments['self_funded'];
            if (isset($selfFunded['is_available']) && isset($selfFunded['is_active']) &&
                isset($selfFunded['start_date']) && isset($selfFunded['end_date'])) {
                
                $startDate = new DateTime($selfFunded['start_date']);
                $endDate = new DateTime($selfFunded['end_date']);
                
                if ($selfFunded['is_available'] && 
                    $selfFunded['is_active'] && 
                    $currentDate >= $startDate && 
                    $currentDate <= $endDate) {
                    return true; // Self-funded is available
                }
            }
        }
        
        // Check fully_funded option
        if (isset($registrationPayments['fully_funded'])) {
            $fullyFunded = $registrationPayments['fully_funded'];
            if (isset($fullyFunded['is_available']) && isset($fullyFunded['is_active']) &&
                isset($fullyFunded['start_date']) && isset($fullyFunded['end_date'])) {
                
                $startDate = new DateTime($fullyFunded['start_date']);
                $endDate = new DateTime($fullyFunded['end_date']);
                
                if ($fullyFunded['is_available'] && 
                    $fullyFunded['is_active'] && 
                    $currentDate >= $startDate && 
                    $currentDate <= $endDate) {
                    return true; // Fully-funded is available
                }
            }
        }
        
        // If we have registration payment options but none are available, registration is closed
        return false;
    }
}
