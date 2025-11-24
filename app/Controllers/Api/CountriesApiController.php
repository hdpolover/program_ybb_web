<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class CountriesApiController extends ResourceController
{
    protected $format = 'json';
    
    private function getCountriesData()
    {
        $jsonPath = FCPATH . 'assets/json/country-list.json';
        
        if (!file_exists($jsonPath)) {
            return null;
        }
        
        $jsonContent = file_get_contents($jsonPath);
        $countries = json_decode($jsonContent, true);
        
        // Add full URL to flag images
        $baseUrl = base_url();
        foreach ($countries as &$country) {
            if (isset($country['flagImg'])) {
                $country['flagImg'] = $baseUrl . '/' . ltrim($country['flagImg'], '/');
            }
        }
        
        return $countries;
    }
    
    /**
     * GET /api/countries
     * Get all countries
     */
    public function index()
    {
        $countries = $this->getCountriesData();
        
        if ($countries === null) {
            return $this->fail('Countries data file not found', 404);
        }
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Countries retrieved successfully',
            'data' => [
                'countries' => $countries,
                'total' => count($countries)
            ]
        ]);
    }
    
    /**
     * GET /api/countries/search?q=query
     * Search countries by name or code
     */
    public function search()
    {
        $request = service('request');
        $query = $request->getVar('q');
        
        if (empty($query)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Search query is required',
                'data' => null
            ], 400);
        }
        
        $countries = $this->getCountriesData();
        
        if ($countries === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Countries data file not found',
                'data' => null
            ], 404);
        }
        
        // Case-insensitive search in country name and code
        $results = array_filter($countries, function($country) use ($query) {
            $nameMatch = stripos($country['countryName'], $query) !== false;
            $codeMatch = stripos($country['countryCode'], $query) !== false;
            return $nameMatch || $codeMatch;
        });
        
        // Reset array keys
        $results = array_values($results);
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Search completed successfully',
            'data' => [
                'query' => $query,
                'count' => count($results),
                'countries' => $results
            ]
        ]);
    }
    
    /**
     * GET /api/countries/{id}
     * Get country by ID
     */
    public function show($id = null)
    {
        if ($id === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Country ID is required',
                'data' => null
            ], 400);
        }
        
        $countries = $this->getCountriesData();
        
        if ($countries === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Countries data file not found',
                'data' => null
            ], 404);
        }
        
        // Find country by ID
        $country = null;
        foreach ($countries as $c) {
            if ($c['id'] == $id) {
                $country = $c;
                break;
            }
        }
        
        if ($country === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Country not found',
                'data' => null
            ], 404);
        }
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Country retrieved successfully',
            'data' => $country
        ]);
    }
    
    /**
     * GET /api/countries/by-name/{name}
     * Get country by exact name
     */
    public function byName($name = null)
    {
        if ($name === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Country name is required',
                'data' => null
            ], 400);
        }
        
        $countries = $this->getCountriesData();
        
        if ($countries === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Countries data file not found',
                'data' => null
            ], 404);
        }
        
        // Find country by exact name (case-insensitive)
        $country = null;
        foreach ($countries as $c) {
            if (strcasecmp($c['countryName'], $name) === 0) {
                $country = $c;
                break;
            }
        }
        
        if ($country === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Country not found',
                'data' => null
            ], 404);
        }
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Country retrieved successfully',
            'data' => $country
        ]);
    }
    
    /**
     * GET /api/countries/codes
     * Get countries with codes only (without flag images for lightweight response)
     */
    public function codes()
    {
        $countries = $this->getCountriesData();
        
        if ($countries === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Countries data file not found',
                'data' => null
            ], 404);
        }
        
        // Remove flag images for lightweight response
        $simplifiedCountries = array_map(function($country) {
            return [
                'id' => $country['id'],
                'countryName' => $country['countryName'],
                'countryCode' => $country['countryCode']
            ];
        }, $countries);
        
        return $this->respond([
            'status' => 'success',
            'message' => 'Country codes retrieved successfully',
            'data' => [
                'countries' => $simplifiedCountries,
                'total' => count($simplifiedCountries)
            ]
        ]);
    }
}
