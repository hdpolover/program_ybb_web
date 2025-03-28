<?php

if (!function_exists('get_jwt_token')) {
    /**
     * Get JWT token from session or generate a new one
     * 
     * @return string|null JWT token or null if not available
     */
    function get_jwt_token() {
        // Check if token exists in session
        if (session()->has('jwt_token')) {
            return session()->get('jwt_token');
        }
        
        // If token doesn't exist or is expired, you might want to request a new one
        // This depends on your JWT implementation
        return null;
    }
}

if (!function_exists('make_get_request')) {
    /**
     * Make a GET request with JWT authorization
     * 
     * @param string $url The URL to make the request to
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @return array Response array with status code and response body
     */
    function make_get_request($url, $headers = [], $useJwt = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);

        // Add JWT token to headers if needed
        if ($useJwt) {
            $token = get_jwt_token();
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }

        curl_close($ch);

        return ['status_code' => $httpCode, 'response' => $response];
    }
}

if (!function_exists('make_post_request')) {
    /**
     * Make a POST request with JWT authorization
     * 
     * @param string $url The URL to make the request to
     * @param array|string $data The data to send
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @return array Response array with status code and response body
     */
    function make_post_request($url, $data, $headers = [], $useJwt = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        // Set the data to be sent
        if (is_array($data)) {
            $data = json_encode($data);
            $headers[] = 'Content-Type: application/json';
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Add JWT token to headers if needed
        if ($useJwt) {
            $token = get_jwt_token();
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }

        curl_close($ch);

        return ['status_code' => $httpCode, 'response' => $response];
    }
}

if (!function_exists('make_put_request')) {
    /**
     * Make a PUT request with JWT authorization
     * 
     * @param string $url The URL to make the request to
     * @param array|string $data The data to send
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @return array Response array with status code and response body
     */
    function make_put_request($url, $data, $headers = [], $useJwt = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        
        // Set the data to be sent
        if (is_array($data)) {
            $data = json_encode($data);
            $headers[] = 'Content-Type: application/json';
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Add JWT token to headers if needed
        if ($useJwt) {
            $token = get_jwt_token();
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }

        curl_close($ch);

        return ['status_code' => $httpCode, 'response' => $response];
    }
}

if (!function_exists('make_delete_request')) {
    /**
     * Make a DELETE request with JWT authorization
     * 
     * @param string $url The URL to make the request to
     * @param array $headers Additional headers
     * @param bool $useJwt Whether to use JWT authorization
     * @return array Response array with status code and response body
     */
    function make_delete_request($url, $headers = [], $useJwt = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        // Add JWT token to headers if needed
        if ($useJwt) {
            $token = get_jwt_token();
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }

        curl_close($ch);

        return ['status_code' => $httpCode, 'response' => $response];
    }
}