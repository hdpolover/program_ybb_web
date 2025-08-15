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

        // get referral by
        $referral = $this->makeGetRequest('/participants/' . $currentParticipantId . '/referrals', [], false, false);

        log_message('debug', 'Submission::getSubmissionData - After referral API call');

        // check for any successful payments
        log_message('debug', 'Submission::getSubmissionData - About to check payments for participant: ' . $currentParticipantId);
        $participantPaymentsResponse = $this->makeGetRequest('/payments/participants/' . $currentParticipantId, [], false, false);
        log_message('debug', 'Submission::getSubmissionData - Payment API Response: ' . json_encode($participantPaymentsResponse));

        // Extract payments array from the response structure
        $participantPayments = [];
        
        // Try both possible response structures
        if (isset($participantPaymentsResponse['data']['payments']) && is_array($participantPaymentsResponse['data']['payments'])) {
            // Nested structure: {"data": {"payments": [...]}}
            $participantPayments = $participantPaymentsResponse['data']['payments'];
            log_message('debug', 'Submission::getSubmissionData - Extracted payments from nested structure: ' . json_encode($participantPayments));
        } elseif (isset($participantPaymentsResponse['payments']) && is_array($participantPaymentsResponse['payments'])) {
            // Direct structure: {"payments": [...]}
            $participantPayments = $participantPaymentsResponse['payments'];
            log_message('debug', 'Submission::getSubmissionData - Extracted payments from direct structure: ' . json_encode($participantPayments));
        } else {
            log_message('debug', 'Submission::getSubmissionData - No payments data found in API response structure');
            log_message('debug', 'Submission::getSubmissionData - Response keys: ' . json_encode(array_keys($participantPaymentsResponse ?? [])));
        }

        // loop through payments and check if any are successful
        $hasSuccessfulPayment = false;

        log_message('debug', 'Submission::getSubmissionData - About to loop through ' . count($participantPayments) . ' payments');

        foreach ($participantPayments as $index => $payment) {
            log_message('debug', 'Submission::getSubmissionData - Checking payment ' . $index . ': ' . json_encode($payment));
            if (isset($payment['status']) && $payment['status'] === '2') {
                log_message('debug', 'Submission::getSubmissionData - Found successful payment with status=2');
                $hasSuccessfulPayment = true;
                break;
            } else {
                log_message('debug', 'Submission::getSubmissionData - Payment status is: ' . ($payment['status'] ?? 'NULL'));
            }
        }

        // get participant statuses
        $participantStatuses = $this->makeGetRequest('/participants/' . $currentParticipantId . '/status', [], false, false);

        $hasSubmittedForm = false;

        // check if participant form_status from $participantStatuses equals to 2. $participantStatuse is one object. not an array
        if (isset($participantStatuses)) {
            if (isset($participantStatuses['form_status']) && $participantStatuses['form_status'] === '2') {
                $hasSubmittedForm = true;
            }
        }

        // Extract relevant data
        $data = [
            'participant' => $submissionData['participant'] ?? null,
            'participantEssays' => $submissionData['participant_essays'] ?? null,
            'participantSubtheme' => $submissionData['participant_subtheme'] ?? null,
            'participantCompetitionCategory' => $submissionData['participant_competition_category'] ?? null,
            'programEssays' => $submissionData['program_essays'] ?? null,
            'programSubthemes' => $submissionData['program_subthemes'] ?? null,
            'competitionCategories' => $submissionData['competition_categories'] ?? null,
            'hasSuccessfulPayment' => $hasSuccessfulPayment,
            'hasSubmittedForm' => $hasSubmittedForm,
            'referral' => $referral,
        ];

        // Debug logging to check payment status
        log_message('debug', 'Submission::getSubmissionData - hasSuccessfulPayment: ' . ($hasSuccessfulPayment ? 'true' : 'false'));
        log_message('debug', 'Submission::getSubmissionData - Number of payments found: ' . count($participantPayments ?? []));
        log_message('debug', 'Submission::getSubmissionData - About to return data array');
        log_message('debug', 'Submission::getSubmissionData - Data array keys: ' . json_encode(array_keys($data)));

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

        // Debug logging for view data
        log_message('debug', 'Submission::index - Final data hasSuccessfulPayment: ' . (isset($data['hasSuccessfulPayment']) ? ($data['hasSuccessfulPayment'] ? 'true' : 'false') : 'NOT SET'));

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

        // Debug logging for view data
        log_message('debug', 'Submission::edit - Final data hasSuccessfulPayment: ' . (isset($data['hasSuccessfulPayment']) ? ($data['hasSuccessfulPayment'] ? 'true' : 'false') : 'NOT SET'));

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

        // Check for profile image data
        if (!empty($requestData['participant']['profile_image'])) {
            // Log that we found image data
            log_message('debug', 'updatePersonal - Profile image data found');

            // The image data is already Base64 encoded, so we can pass it directly to the API
            // Make sure the API expects the data in this format
        }

        // Log the extracted participant data for debugging
        log_message('debug', 'updatePersonal - Extracted Participant Data: ' . json_encode($requestData));        // Send data to API endpoint
        $response = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $requestData);        // Log the API response
        log_message('debug', 'updatePersonal - API Response: ' . json_encode($response));

        // Extract picture_url from response if it exists
        $picture_url = null;
        if (isset($response['participant']) && isset($response['participant']['picture_url'])) {
            $picture_url = $response['participant']['picture_url'];
            log_message('debug', 'Picture URL from API response: ' . $picture_url);
        }

        if (isset($response['participant']) && $response['participant']) {
            // Update session data
            $updatedParticipant = $this->makeGetRequest('/participants/' . $participantId, [], false);

            if ($updatedParticipant) {
                // Force the picture_url to be updated using the value from the API response
                if ($picture_url) {
                    log_message('debug', 'Forcing picture_url update in session to: ' . $picture_url);
                    $updatedParticipant['picture_url'] = $picture_url;
                }

                // Log the participant data we're about to save
                log_message('debug', 'Updating session with participant data: ' . json_encode($updatedParticipant));

                // Store in session so it's available on other pages
                session()->set('current_participant', $updatedParticipant);

                // IMPORTANT: Also update the participants array in session
                $participants = session()->get('participants') ?? [];

                // Flag to track if we updated any participant
                $participantUpdated = false;

                // Update participant in the participants array
                foreach ($participants as $key => &$p) {
                    if (($p['id'] ?? null) == $participantId) {
                        // Create an updated copy
                        $p = $updatedParticipant;

                        // Make doubly sure picture_url is updated
                        if ($picture_url) {
                            $p['picture_url'] = $picture_url;
                        }

                        $participantUpdated = true;
                        break;
                    }
                }

                // If updated, save back to session
                if ($participantUpdated) {
                    log_message('debug', 'Updated participant in participants array');
                    session()->set('participants', $participants);
                } else {
                    log_message('debug', 'Participant not found in participants array');
                }

                // Force session save
                session()->set('session_refreshed_at', time());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Personal information updated successfully',
                'participant' => $updatedParticipant // Include updated data in response
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

        // If request data is null or empty, assume the user didn't edit anything
        // and return success without making any API calls
        if (empty($requestData)) {
            log_message('info', 'updateEntry - No request data provided, assuming no changes needed');
            return $this->response->setJSON([
                'success' => true,
                'message' => 'No changes were made to entry information'
            ]);
        }

        // Prepare data for updating participant
        $participantData = [
            'competition_category_id' => $requestData['competition_category_id'] ?? null,
            'program_subtheme_id' => $requestData['program_subtheme_id'] ?? null,
            'essays' => $requestData['essays'] ?? null,
        ];

        // Check if the program is a journal type
        $isJournalType = isset($this->data['webSettings']['is_journal_type']) &&
            $this->data['webSettings']['is_journal_type'] === true;

        // For non-journal type programs, validate essays
        if (!$isJournalType) {
            // Check if essays array is valid - if essays is provided but empty or invalid, that's an error
            if (isset($requestData['essays'])) {
                if (empty($participantData['essays']) || !is_array($participantData['essays'])) {
                    log_message('error', 'updateEntry - Invalid essays data: ' . json_encode($participantData['essays']));
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No essay data was provided or data format is invalid.'
                    ]);
                }

                // Validate the essays structure
                foreach ($participantData['essays'] as $essay) {
                    if (!isset($essay['program_essay_id']) || !isset($essay['answer'])) {
                        log_message('error', 'updateEntry - Invalid essay structure: ' . json_encode($essay));
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'One or more essays have an invalid format.'
                        ]);
                    }

                    // Log each essay to be saved
                    log_message('debug', 'updateEntry - Essay to be saved - ID: ' . $essay['program_essay_id'] . ', Content length: ' . strlen($essay['answer']) . ' characters');
                }
            }
        }

        // Log the participant data
        log_message('debug', 'updateEntry - Participant Data: ' . json_encode($participantData));

        // Update participant first
        $participantResponse = $this->makePostRequest('/submissions/participants/' . $participantId . '/update', $participantData);

        // Log the API response
        log_message('debug', 'updateEntry - Participant Update Response: ' . json_encode($participantResponse));

        // Check if we received any error response from the API
        if (isset($participantResponse['error']) || (isset($participantResponse['message']) && !isset($participantResponse['essays']))) {
            log_message('error', 'Error saving entry data: ' . ($participantResponse['message'] ?? 'Unknown error'));
            return $this->response->setJSON([
                'success' => false,
                'message' => $participantResponse['message'] ?? 'Failed to save entry information'
            ]);
        }

        // Verify that essays were properly saved - only if essays were included in the request and it's not a journal type
        $essaysNeeded = !$isJournalType && isset($requestData['essays']) && is_array($requestData['essays']) && !empty($requestData['essays']);
        $essaysSaved = isset($participantResponse['essays']) && is_array($participantResponse['essays']) && !empty($participantResponse['essays']);

        // Log essay validation results for debugging
        log_message('debug', 'updateEntry - Essays validation: needed=' . ($essaysNeeded ? 'true' : 'false') .
            ', saved=' . ($essaysSaved ? 'true' : 'false'));

        if ($essaysNeeded && !$essaysSaved) {
            log_message('error', 'Essay data was not saved properly');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Essay data was not saved properly. Please try again.'
            ]);
        }

        // Verify that competition_category and program_subtheme were properly saved
        // Check if category was saved successfully (structure is nested in participant_competition_category)
        $categorySaved = isset($participantResponse['participant_competition_category']) &&
            isset($participantResponse['participant_competition_category']['competition_category_id']) &&
            $participantResponse['participant_competition_category']['competition_category_id'] == $requestData['competition_category_id'];

        // Check if subtheme was saved successfully (structure is nested in participant_subtheme)
        $subthemeSaved = isset($participantResponse['participant_subtheme']) &&
            isset($participantResponse['participant_subtheme']['program_subtheme_id']) &&
            $participantResponse['participant_subtheme']['program_subtheme_id'] == $requestData['program_subtheme_id'];

        // Only validate if they were provided in the request
        $categoryNeeded = isset($requestData['competition_category_id']) && !empty($requestData['competition_category_id']);
        $subthemeNeeded = isset($requestData['program_subtheme_id']) && !empty($requestData['program_subtheme_id']);

        // Log response structure for debugging
        log_message('debug', 'updateEntry - Category validation: needed=' . ($categoryNeeded ? 'true' : 'false') .
            ', saved=' . ($categorySaved ? 'true' : 'false'));
        log_message('debug', 'updateEntry - Subtheme validation: needed=' . ($subthemeNeeded ? 'true' : 'false') .
            ', saved=' . ($subthemeSaved ? 'true' : 'false'));

        // Check if validation fails
        if (($categoryNeeded && !$categorySaved) || ($subthemeNeeded && !$subthemeSaved)) {
            log_message('error', 'Category or subtheme data was not saved properly');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category or subtheme data was not saved properly. Please try again.'
            ]);
        }

        // Update session data
        $updatedParticipant = $this->makeGetRequest('/participants/' . $participantId, [], false);

        // log updated participant data
        log_message('debug', 'updateEntry - Updated Participant Data: ' . json_encode($updatedParticipant));

        if ($updatedParticipant) {
            // Make sure we update the participant data in the session
            session()->set('current_participant', $updatedParticipant);

            // Log success message with details
            log_message('info', 'updateEntry - Successfully saved entry data for participant ' . $participantId .
                ' with ' . (isset($participantResponse['essays']) ? count($participantResponse['essays']) : 0) . ' essays');
        }

        // Prepare success message based on program type
        $message = $isJournalType ?
            'Entry information updated successfully. The selected program subtheme will be used to determine abstract contents.' :
            'Entry information updated successfully';

        // Everything was successful
        return $this->response->setJSON([
            'success' => true,
            'message' => $message
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

        if (isset($response['participant'])) {
            // Update session data
            $updatedParticipant = $this->makeGetRequest('/participants/' . $participantId, [], false);

            if ($updatedParticipant) {
                session()->set('current_participant', $updatedParticipant);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Miscellaneous information updated successfully',
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

    // submit form
    public function submitForm()
    {
        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        // Get participant ID from session
        $participantId = session()->get('current_participant_id');

        if (empty($participantId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Participant ID not found']);
        }

        // Send data to API endpoint
        $response = $this->makePostRequest('/submissions/participants/' . $participantId . '/submit', []);

        // Log the API response
        log_message('debug', 'submitForm - API Response: ' . json_encode($response));

        if (isset($response['participant_id'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Form submitted successfully'
            ]);
        }

        // Return error message from API or default error
        return $this->response->setJSON([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to submit form'
        ]);
    }
}
