<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    /**
     * List of allowed origins that can access your API.
     * Specify domains with protocol (http/https)
     */
    public array $allowedOrigins = [
        'http://localhost:8081',    // Local development
        'http://localhost',         // Other local variants
        'http://127.0.0.1:8081',    // Local development IPv4
        'http://127.0.0.1',         // Local IPv4
        'https://worldyouthfest.com', // Production domain
        'https://www.worldyouthfest.com', // Production domain with www
        'https://koreayouthsummit.com',
        'https://www.koreayouthsummit.com',
        'https://japanyouthsummit.com',
        'https://www.japanyouthsummit.com',
        'https://istanbulyouthsummit.com',
        'https://www.istanbulyouthsummit.com',
        'https://youthacademicforum.com',
        'https://www.youthacademicforum.com',
        // Add more domains as needed
    ];

    /**
     * Allow requests from any origin
     * WARNING: Only set this to true if you understand the security implications
     */
    public bool $allowAllOrigins = false;

    /**
     * Allowed HTTP Methods for CORS requests
     */
    public array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'];

    /**
     * Allowed HTTP Headers for CORS requests
     */
    public array $allowedHeaders = [
        'Origin',
        'X-Requested-With',
        'Content-Type',
        'Accept',
        'Access-Control-Request-Method',
        'Authorization',
        'X-API-KEY'
    ];

    /**
     * Whether to allow credentials (cookies, authorization headers)
     */
    public bool $allowCredentials = true;

    /**
     * How long the preflight request can be cached (in seconds)
     */
    public int $maxAge = 7200; // 2 hours
}