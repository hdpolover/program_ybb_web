<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Submission extends BaseController
{

    function getSubmissionData()
    {
        $data = [];

        // get current participant id from session
        $currentParticipantId = session()->get('current_participant_id');

        // Safety check - if no program ID, redirect to home
        if (empty($currentParticipantId)) {
            return [];
        }

        // Get submission data for this participant
        $submissionData = $this->makeGetRequest('/submissions/participants/' . $currentParticipantId);

        // Log submission data for debugging
        log_message('debug', 'Submission::getSubmissionData - Submission Data: ' . json_encode($submissionData));

        // Extract relevant data
        $data = [
            'participant' => $submissionData['participant'] ?? null,
            'participantEssays' => $submissionData['participant_essays'] ?? null,
            'participantSubtheme' => $submissionData['participant_subtheme'] ?? null,
            'participantCompetitionCategory' => $submissionData['participant_competition_category'] ?? null,
            'programEssays' => $submissionData['program_essays'] ?? null,
            'programSubthemes' => $submissionData['program_subthemes'] ?? null,
            'competitionCategories' => $submissionData['competition_categories'] ?? null,
        ];

        return $data;
    }

    public function index()
    {
        // Get submission data for this participant
        $submissionData = $this->getSubmissionData();

        // 5. Get current program data
        $programs = session()->get('programs') ?? [];
        $currentProgram = null;

        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                $currentProgram = $program;
                break;
            }
        }

        // Build view data
        $data = [
            'title' => 'Submission',
            'currentProgram' => $currentProgram,
        ];

        // Merge submission data into view data
        $data = array_merge($data, $submissionData);

        // Output data structure as JSON for easier debugging
        // echo '<pre>';
        // echo json_encode($data, JSON_PRETTY_PRINT);
        // echo '</pre>';

        // Uncomment the line below when done debugging
        return $this->render('participant/submission/index', $data);
    }

    public function edit()
    {
        // Get submission data for this participant
        $submissionData = $this->getSubmissionData();

        // 5. Get current program data
        $programs = session()->get('programs') ?? [];
        $currentProgram = null;

        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                $currentProgram = $program;
                break;
            }
        }

        // Build view data
        $data = [
            'title' => 'Submission',
            'currentProgram' => $currentProgram,
        ];

        // Merge submission data into view data
        $data = array_merge($data, $submissionData);

        // Output data structure as JSON for easier debugging
        // echo '<pre>';
        // echo json_encode($data, JSON_PRETTY_PRINT);
        // echo '</pre>';

        // Uncomment the line below when done debugging
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

        // Extract required fields from request data
        if (empty($requestData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No participant data provided']);
        }

        // Log the extracted participant data for debugging
        log_message('debug', 'updatePersonal - Extracted Participant Data: ' . json_encode($requestData));

        // Send data to API endpoint
        $response = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $requestData);

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


        // Log the prepared data
        log_message('debug', 'updateProfessional - Prepared Data: ' . json_encode($requestData));

        // Send data to API endpoint
        $response = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $requestData);

        // Log the API response
        log_message('debug', 'updateProfessional - API Response: ' . json_encode($response));

        if (isset($response['participant']) && $response['participant']) {
            // Update session data
            $updatedParticipant = $this->makeGetRequest('/participants/' . $participantId, [], false);

            if ($updatedParticipant) {
                session()->set('current_participant', $updatedParticipant);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Professional Profile information updated successfully'
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
            'competition_category_id' => $requestData['competition_category_id'] ?? null,
            'program_subtheme_id' => $requestData['program_subtheme_id'] ?? null,
            'essays' => $requestData['essays'] ?? null,
        ];

        // Log the participant data
        log_message('debug', 'updateEntry - Participant Data: ' . json_encode($participantData));

        // Update participant first
        $participantResponse = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $participantData);

        // Log the API response
        log_message('debug', 'updateEntry - Participant Update Response: ' . json_encode($participantResponse));

        if (isset($participantResponse['essays']) && isset($participantResponse['competition_category_id']) && isset($participantResponse['program_subtheme_id'])) {
            // Update session data
            $updatedParticipant = $this->makeGetRequest('/participants/' . $participantId, [], false);

            // log updated participant data
            log_message('debug', 'updateEntry - Updated Participant Data: ' . json_encode($updatedParticipant));

            if ($updatedParticipant) {
                session()->set('current_participant', $updatedParticipant);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Entry information updated successfully'
            ]);
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

        if (isset($response['participant'])) {
            // Update session data
            $updatedParticipant = $this->makeGetRequest('/participants/' . $participantId, [], false);

            // log updated participant data
            log_message('debug', 'updateEntry - Updated Participant Data: ' . json_encode($updatedParticipant));

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

        if (empty($requestData['code']) && empty($requestData['program_id'])) {
            return $this->response->setJSON(['valid' => false]);
        }

        // Check ambassador code with API
        $response = $this->makeGetRequest('/ambassadors/programs/' . $requestData['program_id'] . '/ref-code/' . $requestData['code'], [], false, false);

        // Log the API response
        log_message('debug', 'validateAmbassadorCode - API Response: ' . json_encode($response));

        // Check if the response is valid
        if (isset($response)) {
            if (isset($response['is_valid']) && $response['is_valid'] === true) {
                return $this->response->setJSON([
                    'is_valid' => true,
                    'ambassador' => $response['ambassador'] ?? null,
                    'message' => 'Ambassador code is valid'
                ]);
            } elseif (isset($response['is_valid']) && $response['is_valid'] === false) {
                return $this->response->setJSON([
                    'is_valid' => false,
                    'message' => $response['message'] ?? 'Ambassador code is invalid'
                ]);
            }
        } 

        // Return API response or default
        return $this->response->setJSON([
            'is_valid' =>  false,
            'message' => $response['message'] ?? 'Failed to validate ambassador code'
        ]);
    }
}
