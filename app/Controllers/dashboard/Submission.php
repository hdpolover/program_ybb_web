<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Submission extends BaseController
{

    public function index()
    {
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        // Safety check - if no program ID, redirect to home
        if (empty($currentProgramId)) {
            return redirect()->to(base_url('dashboard'));
        }

        // CRITICAL FIX: Force refresh of API data for the current program
        // 1. Get all participants for current user
        $apiParticipants = $this->makeGetRequest('/participants/user/' . session()->get('user_id'), [], false);

        // Log the raw API response for debugging
        log_message('debug', 'Submission::index - API Participants Response: ' . json_encode($apiParticipants));

        // Use API data if available, otherwise fallback to session data
        $participants = !empty($apiParticipants['data']) ? $apiParticipants['data'] : (!empty($apiParticipants) ? $apiParticipants : (session()->get('participants') ?? []));

        // Update session with refreshed participants
        session()->set('participants', $participants);

        // 2. Find the participant matching the current program ID
        $currentParticipant = null;
        foreach ($participants as $participant) {
            // Ensure we're comparing consistently (string vs int)
            if ((string)($participant['program_id'] ?? '') === (string)$currentProgramId) {
                $currentParticipant = $participant;
                break;
            }
        }

        // Log the matched participant
        log_message('debug', 'Submission::index - Matched Participant: ' . json_encode($currentParticipant));

        // Safety check - if no participant found for this program
        if (empty($currentParticipant)) {
            // Display an error message
            session()->setFlashdata('error', 'No participant data found for the selected program.');
            return redirect()->to(base_url('dashboard'));
        }

        // 3. Update session with the current participant data
        $currentParticipantId = $currentParticipant['id'] ?? null;
        session()->set('current_participant', $currentParticipant);
        session()->set('current_participant_id', $currentParticipantId);

        // 4. Get submission data for this participant
        $submissionData = [];
        if ($currentParticipantId) {
            $submissionData = $this->makeGetRequest('/submissions/participants/' . $currentParticipantId);

            // Log submission data for debugging
            log_message('debug', 'Submission::index - Submission Data: ' . json_encode($submissionData));
        }

        // 5. Get current program data
        $programs = session()->get('programs') ?? [];
        $currentProgram = null;

        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                $currentProgram = $program;
                break;
            }
        }

        // Update session with current program
        if ($currentProgram) {
            session()->set('current_program', $currentProgram);
        }

        // Extract submission data components
        $participant = $submissionData['participant'] ?? $currentParticipant;
        $participantEssays = $submissionData['participant_essays'] ?? null;
        $participantSubtheme = $submissionData['participant_subtheme'] ?? null;
        $programEssays = $submissionData['program_essays'] ?? null;
        $programSubthemes = $submissionData['program_subthemes'] ?? null;

        // Build view data
        $data = [
            'title' => 'Submission',
            'currentParticipant' => $participant,
            'currentProgram' => $currentProgram,
            'currentParticipantId' => $currentParticipantId,
            'submittedEssays' => $participantEssays,
            'submittedSubtheme' => $participantSubtheme,
            'programEssays' => $programEssays,
            'programSubthemes' => $programSubthemes,
        ];

        return $this->render('participant/submission/index', $data);
    }

    public function edit()
    {
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        // Safety check - if no program ID, redirect to home
        if (empty($currentProgramId)) {
            return redirect()->to(base_url('dashboard'));
        }

        // Make sure we have the current program details
        $programs = session()->get('programs') ?? [];
        $currentProgram = null;

        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                $currentProgram = $program;
                break;
            }
        }

        // Safety check - if no program found
        if (empty($currentProgram)) {
            session()->setFlashdata('error', 'Program not found.');
            return redirect()->to(base_url('dashboard'));
        }

        // Update session with current program
        session()->set('current_program', $currentProgram);

        // Get form data for the current program
        $formData = [];
        if ($currentProgramId) {
            $formData = $this->makeGetRequest('/submissions/program/' . $currentProgramId . '/form');

            // Log the form data for debugging
            log_message('debug', 'Submission::edit - Form Data: ' . json_encode($formData));
        }

        // Extract form components
        $programSubthemes = $formData['subthemes'] ?? [];
        $programEssays = $formData['essays'] ?? [];
        $competitionCategories = $formData['competition_categories'] ?? [];

        // Get current participant data from session (this should be set in the index method)
        $currentParticipant = session()->get('current_participant');
        $currentParticipantId = session()->get('current_participant_id');

        // Build view data
        $data = [
            'title' => 'Edit Submission',
            'currentProgram' => $currentProgram,
            'currentParticipant' => $currentParticipant,
            'currentParticipantId' => $currentParticipantId,
            'subthemes' => $programSubthemes,
            'essays' => $programEssays,
            'competitionCategories' => $competitionCategories,
        ];

        return $this->render('participant/submission/edit', $data);
    }

    /**
     * Handle personal information form submission
     */
    public function updatePersonal($participantId = null)
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        // Get JSON data from request
        $requestData = $this->request->getJSON(true);

        // Get participant ID from request
        if (empty($participantId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Participant ID not found']);
        }

        // Log the received data
        log_message('debug', 'updatePersonal - Request Data: ' . json_encode($requestData));

        // Extract participant data from the nested structure
        $participantData = $requestData ?? [];
        if (empty($participantData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No participant data provided']);
        }
        
        // Special debug log for country codes
        log_message('debug', 'PHONE DEBUG - Country codes: ' . 
            'Personal: ' . ($participantData['country_code'] ?? 'NULL') . ', ' .
            'Emergency: ' . ($participantData['emergency_country_code'] ?? 'NULL')
        );
        
        // Log the extracted participant data for debugging
        log_message('debug', 'updatePersonal - Extracted Participant Data: ' . json_encode($participantData));

        // Send data to API endpoint
        $response = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $participantData);

        // Log the API response
        log_message('debug', 'updatePersonal - API Response: ' . json_encode($response));

        if (isset($response['participant']) && $response['participant']) {
            // Update session data
            $updatedParticipant = $this->makeGetRequest('/participants/' . $participantId, [], false);
            
            if ($updatedParticipant) {
                session()->set('current_participant', $updatedParticipant);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Personal information updated successfully'
            ]);
        }

        // Return error message from API or default error
        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to update personal information'
        ]);
    }

    /**
     * Handle professional information form submission
     */
    public function updateProfessional()
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        // Get JSON data from request
        $requestData = $this->request->getJSON(true);

        // Get participant ID from session
        $participantId = session()->get('current_participant_id');

        if (empty($participantId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Participant ID not found']);
        }

        // Prepare data for API
        $apiData = [
            'education_level' => $requestData['education'] ?? null,
            'institution' => $requestData['institution'] ?? null,
            'major' => $requestData['major'] ?? null,
            'occupation' => $requestData['occupation'] ?? null,
            'organizations' => $requestData['organization'] ?? null,
            'experiences' => $requestData['experiences'] ?? null,
            'achievements' => $requestData['achievements'] ?? null,
            'resume_url' => $requestData['resume_link'] ?? null
        ];

        // Log the prepared data
        log_message('debug', 'updateProfessional - Prepared Data: ' . json_encode($apiData));

        // Send data to API endpoint
        $response = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $apiData);

        // Log the API response
        log_message('debug', 'updateProfessional - API Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            // Update session data
            $updatedParticipant = $response['participant'] ?? null;
            if ($updatedParticipant) {
                session()->set('current_participant', $updatedParticipant);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Professional information updated successfully'
            ]);
        }

        // Return error message from API or default error
        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to update professional information'
        ]);
    }

    /**
     * Handle entry information form submission
     */
    public function updateEntry()
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        // Get JSON data from request
        $requestData = $this->request->getJSON(true);

        // Get participant ID and program ID from session
        $participantId = session()->get('current_participant_id');
        $programId = session()->get('current_program_id');

        if (empty($participantId) || empty($programId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Participant ID or Program ID not found'
            ]);
        }

        // Prepare data for updating participant
        $participantData = [
            'competition_category_id' => $requestData['competition_category'] ?? null,
            'subtheme_id' => $requestData['subtheme'] ?? null,
        ];

        // Log the participant data
        log_message('debug', 'updateEntry - Participant Data: ' . json_encode($participantData));

        // Update participant first
        $participantResponse = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $participantData);

        // Check if participant update was successful
        if (!isset($participantResponse['success']) || !$participantResponse['success']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $participantResponse['message'] ?? 'Failed to update entry information'
            ]);
        }

        // Update session data
        $updatedParticipant = $participantResponse['participant'] ?? null;
        if ($updatedParticipant) {
            session()->set('current_participant', $updatedParticipant);
        }

        // Now handle essays if they exist
        if (isset($requestData['essays']) && is_array($requestData['essays'])) {
            // Prepare essay submission data
            $essaysData = [
                'participant_id' => $participantId,
                'program_id' => $programId,
                'essays' => $requestData['essays']
            ];

            // Log the essay data
            log_message('debug', 'updateEntry - Essays Data: ' . json_encode($essaysData));

            // Submit essays
            $essaysResponse = $this->makePostRequest('/submissions/essays', $essaysData);

            // Log the essay submission response
            log_message('debug', 'updateEntry - Essays Response: ' . json_encode($essaysResponse));

            if (!isset($essaysResponse['success']) || !$essaysResponse['success']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $essaysResponse['message'] ?? 'Failed to submit essays'
                ]);
            }
        }

        // Everything was successful
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Entry information updated successfully'
        ]);
    }

    /**
     * Handle miscellaneous information form submission
     */
    public function updateMisc()
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        // Get JSON data from request
        $requestData = $this->request->getJSON(true);

        // Get participant ID from session
        $participantId = session()->get('current_participant_id');

        if (empty($participantId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Participant ID not found']);
        }

        // Prepare data for API
        $apiData = [
            'instagram_account' => $requestData['instagram_account'] ?? null,
            'knowledge_source' => $requestData['knowledge_source'] ?? null,
            'source_account_name' => $requestData['source_account_name'] ?? null,
            'twibbon_link' => $requestData['twibbon_link'] ?? null,
            'requirement_link' => $requestData['requirement_link'] ?? null,
            'ambassador_code' => $requestData['ambassador_code'] ?? null,
        ];

        // Log the prepared data
        log_message('debug', 'updateMisc - Prepared Data: ' . json_encode($apiData));

        // Send data to API endpoint
        $response = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $apiData);

        // Log the API response
        log_message('debug', 'updateMisc - API Response: ' . json_encode($response));

        if (isset($response['success']) && $response['success']) {
            // Update session data
            $updatedParticipant = $response['participant'] ?? null;
            if ($updatedParticipant) {
                session()->set('current_participant', $updatedParticipant);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Miscellaneous information updated successfully'
            ]);
        }

        // Return error message from API or default error
        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to update miscellaneous information'
        ]);
    }

    /**
     * Validate ambassador code
     */
    public function validateAmbassadorCode()
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        // Get JSON data from request
        $requestData = $this->request->getJSON(true);

        if (empty($requestData['code'])) {
            return $this->response->setJSON(['valid' => false]);
        }

        // Check ambassador code with API
        $response = $this->makePostRequest('/ambassadors/validate-code', ['code' => $requestData['code']]);

        // Log the API response
        log_message('debug', 'validateAmbassadorCode - API Response: ' . json_encode($response));

        // Return API response or default
        return $this->response->setJSON([
            'valid' => $response['valid'] ?? false,
            'ambassador' => $response['ambassador'] ?? null
        ]);
    }
}
