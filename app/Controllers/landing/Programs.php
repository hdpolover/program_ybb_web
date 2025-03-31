<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Programs extends BaseController
{
    public function index()
    {
        // Get program data from API
        $programs = $this->makeGetRequest('/landing/programs?web_url=' . $this->currentUrl);

        $data = [
            'title' => 'Programs',
            'category' => $programs['category'] ?? [],
            'programs' => $programs['programs'] ?? [],
        ];

        return $this->render('landing/programs', $data);
    }
    
    public function detail($slug)
    {
        // Get program details from API using the slug
        $programDetails = $this->makeGetRequest('/programs/' . $slug); // Updated to use $slug instead of $this->currentUrl
        
        // Find the specific program by slug
        $program = null;
        foreach ($programDetails as $p) {
            if (isset($p['slug']) && $p['slug'] == $slug) {
                $program = $p;
                break;
            } else if (isset($p['id']) && $p['id'] == $slug) {
                // Fallback to ID if slug doesn't match
                $program = $p;
                break;
            }
        }
        
        if (!$program) {
            // Program not found, redirect to programs list
            return redirect()->to(base_url('programs'));
        }
        
        // Get additional program details if needed
        $programSchedules = $this->makeGetRequest('/program_schedules?program_id=' . ($program['id'] ?? ''));
        $programTestimonials = $this->makeGetRequest('/program_testimonies?program_id=' . ($program['id'] ?? ''));
        
        $data = [
            'title' => $program['name'] ?? 'Program Detail',
            'program' => $program,
            'schedules' => $programSchedules,
            'testimonials' => $programTestimonials,
            // Add more program-related data as needed
        ];
        
        return $this->render('landing/program-detail', $data);
    }
}