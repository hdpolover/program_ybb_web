<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Programs extends BaseController
{
    public function index()
    {
        $startTime = microtime(true);
        
        // Use caching for programs data
        $cacheKey = "landing_programs_" . str_replace(['.', ':', '/', '\\', '@'], '_', $this->currentUrl) . "_v1";
        $cache = \Config\Services::cache();
        
        // Try to get from cache first
        $programs = $cache->get($cacheKey);
        
        if ($programs === null) {
            // Cache miss - fetch from API
            $apiStartTime = microtime(true);
            $programs = $this->makeGetRequest('/landing/programs?web_url=' . $this->currentUrl);
            $apiLoadTime = round((microtime(true) - $apiStartTime) * 1000, 2);
            
            // Cache for 20 minutes (1200 seconds)
            if (!empty($programs)) {
                $cache->save($cacheKey, $programs, 1200);
                log_message('info', "Programs data cached for {$this->currentUrl} (API load: {$apiLoadTime}ms)");
            }
        } else {
            log_message('debug', "Programs data cache hit for {$this->currentUrl}");
        }

        $otherPrograms = $programs['otherPrograms'] ?? [];

        // remove programs if is_registration_open is 0
        foreach ($otherPrograms as $key => $program) {
            if ($program['is_registration_open'] == 0) {
                unset($otherPrograms[$key]);
            }
        }

        $data = [
            'title' => 'Programs',
            'category' => $programs['category'] ?? [],
            'programs' => $programs['programs'] ?? [],
            'otherPrograms' => $otherPrograms,
        ];

        $totalLoadTime = round((microtime(true) - $startTime) * 1000, 2);
        log_message('info', "Programs page loaded in {$totalLoadTime}ms");

        return $this->render('landing/programs', $data);
    }

    public function detail($slug)
    {
        // Get program details from API using the slug
        $programDetails = $this->makeGetRequest('/programs/slug/' . $slug); // Updated to use $slug instead of $this->currentUrl

        if (empty($programDetails)) {
            // Handle the case where no program details are found
            return redirect()->to(base_url('programs'))->with('error', 'Program not found.');
        }

        // Get additional program details if needed
        $program = $programDetails;

        // You can also fetch related programs or other data here if needed
        $category = $this->makeGetRequest('/landing/programs?web_url=' . $this->currentUrl); // Fetch category data again if needed

        $photos = $this->makeGetRequest('/program_photos/category/' . $program['program_category_id']); // Fetch photos related to the program

        // if no photos are found, use photos from other programs
        if (empty($photos)) {
            $photos = $this->makeGetRequest('/program-photos'); // Fetch all program photos
        }

        // get participant photos
        $participant_photos = $this->makeGetRequest('/participants/program/' . $program['id'] . '/photos'); // Fetch participant photos related to the program

        // get program schedules by program id
        $program_schedules = $this->makeGetRequest('/program-schedules/program/' . $program['id']); // Fetch program schedules related to the program
       
        // get program faqs by program id
        $program_faqs = $this->makeGetRequest('/program-faqs/program/' . $program['id']); // Fetch program faqs related to the program

        // get program rundowns by program id
        $program_rundowns = $this->makeGetRequest('/program-rundowns/program/' . $program['id']); // Fetch program rundowns related to the program

        $data = [
            'title' => $program['name'] ?? 'Program Detail',
            'program' => $program,
            'category' => $category['category'] ?? [],
            'photos' => $photos,
            'participant_photos' => $participant_photos,
            'schedules' => $program_schedules,
            'faqs' => $program_faqs,
            'rundowns' => $program_rundowns,
        ];

        // var_dump($program_rundowns); // Debugging line to check the data being passed to the view
        
        return $this->render('landing/program-detail', $data);
    }
}
