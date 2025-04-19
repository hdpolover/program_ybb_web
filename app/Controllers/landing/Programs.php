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
            'otherPrograms' => $programs['otherPrograms'] ?? [],
        ];

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

        $data = [
            'title' => $program['name'] ?? 'Program Detail',
            'program' => $program,
            'category' => $category['category'] ?? [],
            'photos' => $photos,
            'participant_photos' => $participant_photos,
            'schedules' => $program_schedules,
            'faqs' => $program_faqs,
        ];
        
        return $this->render('landing/program-detail', $data);
    }
}
