<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Insights extends BaseController
{
    public function index()
    {

        $insightsResponse = $this->makeGetRequest('/landing/insights?web_url=' . $this->currentUrl);

        $category = $insightsResponse['category'] ?? []; // Extract the category data from the response
        $insightsData = $insightsResponse['insightsData'] ?? []; // Extract the data from the response

        $activeProgramInsights = $insightsData['active_program_insights'] ?? []; // Extract the active program insights from the response

        $program = $activeProgramInsights['program'] ?? []; // Extract the program data from the response

        $totalParticipants = $activeProgramInsights['total_registered_participants'] ?? 0; // Extract the total participants from the response
        $totalCountries = $activeProgramInsights['total_countries'] ?? 0; // Extract the total countries from the response
        $countriesData = $activeProgramInsights['countries_data'] ?? []; // Extract the countries data from the response
        
        // Normalize countries data to ensure consistent field names
        $normalizedCountriesData = [];
        foreach ($countriesData as $country) {
            $normalizedCountriesData[] = [
                'country' => $country['country'] ?? $country['nationality'] ?? null,
                'participant_count' => $country['participant_count'] ?? $country['participants_count'] ?? 0,
                'country_code' => $country['country_code'] ?? null
            ];
        }
        $countriesData = $normalizedCountriesData;
        
        // Check if the insights data is empty and handle accordingly
        if (empty($insightsData)) {
            // Handle the case when there are no insights available
            return redirect()->to(base_url('home')); // Redirect to home or show a message
        }

        $data = [
            'title' => 'Insights',
            'category' => $category,
            'program' => $program,
            'totalParticipants' => $totalParticipants,
            'totalCountries' => $totalCountries,
            'countriesData' => $countriesData,
        ];

        return $this->render('landing/insights', $data);
    }
}