<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class TopbarController extends BaseController
{
    use ResponseTrait;

    /**
     * Constructor
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Get topbar data for the current user
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getTopbarData()
    {
        $data = $this->processTopbarData();
        return $this->response->setJSON($data);
    }

    /**
     * Set the current program
     * 
     * @param int $programId
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function setProgram($programId)
    {
        // Store the program ID in the session
        session()->set('current_program_id', $programId);

        // Clear cached participant data to force refresh
        session()->remove('current_participant');

        // If the request is AJAX, return JSON response
        if ($this->request->isAJAX()) {
            // Process topbar data to get updated information
            $updatedData = $this->processTopbarData();

            // Ensure current participant is updated in session
            if ($updatedData['currentParticipant'] !== null) {
                session()->set('current_participant', $updatedData['currentParticipant']);
                session()->set('current_participant_id', $updatedData['currentParticipant']['id'] ?? null);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Program changed successfully',
                'currentProgram' => $updatedData['currentProgram'],
                'currentProgramId' => $updatedData['currentProgramId'],
                'currentParticipant' => $updatedData['currentParticipant']
            ]);
        }

        // Redirect back to the previous page
        return redirect()->back();
    }

    /**
     * Register a user for a program
     * 
     * @param int $userId The ID of the user to register
     * @return \CodeIgniter\HTTP\Response
     */
    public function registerForProgram($userId)
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Direct access is not allowed'
            ]);
        }

        // Get JSON data from request
        $requestData = $this->request->getJSON(true);

        // Validate required data
        if (empty($requestData) || !isset($requestData['program_id'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Program ID is required'
            ]);
        }

        // Get the user's full name from participants data if not provided
        if (!isset($requestData['full_name']) || empty($requestData['full_name'])) {
            $participants = session()->get('participants') ?? [];
            foreach ($participants as $participant) {
                if (isset($participant['full_name']) && !empty($participant['full_name'])) {
                    $requestData['full_name'] = $participant['full_name'];
                    break;
                }
            }

            // If still no full name, try to get from user session data
            if (!isset($requestData['full_name']) || empty($requestData['full_name'])) {
                $user = session()->get('user');
                if (isset($user['full_name'])) {
                    $requestData['full_name'] = $user['full_name'];
                } elseif (isset($user['name'])) {
                    $requestData['full_name'] = $user['name'];
                } elseif (isset($user['email'])) {
                    // Use email as a last resort (without the domain)
                    $requestData['full_name'] = explode('@', $user['email'])[0];
                }
            }
        }

        // Log the registration attempt
        log_message('info', 'Program registration attempt: User ID: ' . $userId . ', Program ID: ' . $requestData['program_id']);

        try {
            // Make the API call to register the participant
            $response = $this->makePostRequest('/participants/users/' . $userId . '/create', $requestData);

            // Log the API response
            log_message('debug', 'Registration API Response: ' . json_encode($response));

            if (isset($response['id']) ) {
                // Registration successful

                // Get updated participants data to refresh the session using corrected API endpoint
                $updatedParticipantsResponse = $this->makeGetRequest('/participants/user/' . $userId, [], true);

                // Note: makeGetRequest automatically extracts the 'data' portion, so we access 'participant' directly
                if ($updatedParticipantsResponse && isset($updatedParticipantsResponse['participant']) && !empty($updatedParticipantsResponse['participant'])) {
                    // Extract participant array from the API response structure (data portion already extracted)
                    $updatedParticipants = $updatedParticipantsResponse['participant'];
                    
                    // Update participants in session
                    session()->set('participants', $updatedParticipants);

                    // Update current participant in session if this is for the current program
                    if (session()->get('current_program_id') == $requestData['program_id']) {
                        foreach ($updatedParticipants as $p) {
                            if (($p['program_id'] ?? null) === $requestData['program_id']) {
                                session()->set('current_participant', $p);
                                session()->set('current_participant_id', $p['id'] ?? null);
                                break;
                            }
                        }
                    }
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Registration successful',
                    'participant' => $response,
                ]);
            }

            // Return error message from API or default error
            return $this->response->setJSON([
                'success' => false,
                'message' => $response['message'] ?? 'Failed to register for the program'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Program registration error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'An error occurred during registration: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get current user information for the API
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getCurrentUser()
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Direct access is not allowed'
            ]);
        }

        // Get user data from session
        $user = session()->get('user');

        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }

        // Return only the necessary data (don't expose sensitive information)
        return $this->response->setJSON([
            'success' => true,
            'user' => [
                'id' => $user['id'] ?? null,
                'name' => $user['full_name'] ?? $user['name'] ?? null,
                'email' => $user['email'] ?? null,
            ]
        ]);
    }

    /**
     * Update participant data in the session
     * Called via AJAX after a user updates their personal information
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function updateParticipantSession()
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Direct access is not allowed'
            ]);
        }

        // Get JSON data from request
        $requestData = $this->request->getJSON(true);
        
        // Validate required data
        if (empty($requestData) || !isset($requestData['participant_id'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Participant ID is required'
            ]);
        }

        // Get the current participant from the session
        $currentParticipant = session()->get('current_participant');
        
        if (!$currentParticipant) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No participant found in session'
            ]);
        }

        // Check if this is the current active participant
        if ($currentParticipant['id'] != $requestData['participant_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Participant ID does not match current participant'
            ]);
        }

        // Update fields in the session
        $updated = false;

        if (isset($requestData['full_name'])) {
            $currentParticipant['full_name'] = $requestData['full_name'];
            $updated = true;
        }

        if (isset($requestData['picture_url'])) {
            $currentParticipant['picture_url'] = $requestData['picture_url'];
            $updated = true;
        }

        if ($updated) {
            // Update the session with modified participant data
            session()->set('current_participant', $currentParticipant);
            
            // Log successful update for debugging
            log_message('debug', 'Updated participant session data for ID: ' . $requestData['participant_id']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Participant session data updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'No fields to update'
        ]);
    }

    /**
     * Process the topbar data and pass it to the view
     * 
     * @return array
     */
    public function processTopbarData()
    {
        // Get programs from session
        $programs = session()->get('programs') ?? [];

        // Get participants data from session
        $participants = session()->get('participants') ?? [];

        // Initialize arrays
        $participant_programs = [];
        $sorted_programs = [];
        $currentProgram = null;
        $currentParticipant = null;

        // Process participants and program connections
        foreach ($participants as $p) {
            if (isset($p['program_id'])) {
                $participant_programs[] = $p['program_id'];
            }
        }

        // Safety check for empty programs
        if (empty($programs)) {
            log_message('debug', 'TopbarController: No programs found in session');

            return [
                'sorted_programs' => [],
                'currentProgramId' => null,
                'currentProgram' => null,
                'currentParticipant' => null,
                'name' => 'Guest',
                'profileImage' => null,
                'participant_programs' => [],
            ];
        }

        // Include all programs in sorted_programs, not just registered ones
        $sorted_programs = $programs;        // First sort programs by registration status (registered programs first)
        usort($sorted_programs, function ($a, $b) use ($participant_programs) {
            $a_registered = in_array($a['id'] ?? null, $participant_programs) ? 1 : 0;
            $b_registered = in_array($b['id'] ?? null, $participant_programs) ? 1 : 0;

            // First compare by registration status
            if ($a_registered !== $b_registered) {
                return $b_registered <=> $a_registered; // Registered programs first
            }

            // Then by active status
            return ($b['is_active'] ?? 0) <=> ($a['is_active'] ?? 0);
        });        // Get current program id from session or determine the most appropriate one
        $currentProgramId = null;

        // First try to get from session
        if (session()->has('current_program_id')) {
            $currentProgramId = session()->get('current_program_id');
            
            // Verify if the current program ID is one the participant is registered for
            if (!in_array($currentProgramId, $participant_programs) && !empty($participant_programs)) {
                // If not registered for current program but registered for others,
                // find an active program among those they're registered for
                $foundActiveProgram = false;
                foreach ($programs as $program) {
                    if (in_array($program['id'] ?? null, $participant_programs) && 
                        isset($program['is_active']) && $program['is_active'] == '1') {
                        $currentProgramId = $program['id'];
                        $foundActiveProgram = true;
                        log_message('debug', 'TopbarController: Switched to active registered program ID: ' . $currentProgramId);
                        break;
                    }
                }
                
                // If no active program found among registered ones, use the first registered program
                if (!$foundActiveProgram) {
                    $currentProgramId = $participant_programs[0];
                    log_message('debug', 'TopbarController: Switched to first registered program ID: ' . $currentProgramId);
                }
            }
        } 
        // If no program ID in session but user is registered for programs,
        // select the first active registered program if available
        else if (!empty($participant_programs)) {
            $foundActiveProgram = false;
            foreach ($programs as $program) {
                if (in_array($program['id'] ?? null, $participant_programs) && 
                    isset($program['is_active']) && $program['is_active'] == '1') {
                    $currentProgramId = $program['id'];
                    $foundActiveProgram = true;
                    log_message('debug', 'TopbarController: First sign-in, selected active registered program ID: ' . $currentProgramId);
                    break;
                }
            }
            
            // If no active program found among registered ones, use the first registered program
            if (!$foundActiveProgram) {
                $currentProgramId = $participant_programs[0];
                log_message('debug', 'TopbarController: First sign-in, selected first registered program ID: ' . $currentProgramId);
            }
        }
        // If user has no registered programs, select an active program if possible
        else if (!empty($sorted_programs)) {
            // Try to find an active program first
            foreach ($sorted_programs as $program) {
                if (isset($program['is_active']) && $program['is_active'] == '1') {
                    $currentProgramId = $program['id'];
                    log_message('debug', 'TopbarController: First sign-in, selected active program ID: ' . $currentProgramId);
                    break;
                }
            }
            
            // If no active program found, fall back to the first program
            if ($currentProgramId === null) {
                $currentProgramId = $sorted_programs[0]['id'] ?? null;
                log_message('debug', 'TopbarController: First sign-in, selected first available program ID: ' . $currentProgramId);
            }
        }

        // Set current program id to session if we have one
        if ($currentProgramId !== null) {
            session()->set('current_program_id', $currentProgramId);
        }

        // Get current program based on current program id
        if ($currentProgramId !== null) {
            foreach ($sorted_programs as $program) {
                if (($program['id'] ?? null) === $currentProgramId) {
                    $currentProgram = $program;
                    break;
                }
            }
        }

        // Get current currentParticipant based on current program id
        if ($currentProgramId !== null) {
            foreach ($participants as $p) {
                if (($p['program_id'] ?? null) === $currentProgramId) {
                    $currentParticipant = $p;
                    break;
                }
            }
        }

        // set current participant id to session if we have one
        if ($currentParticipant !== null) {
            session()->set('current_participant_id', $currentParticipant['id'] ?? null);
        }

        $participantName = $currentParticipant['full_name'] ?? null;
        $profileImage = $currentParticipant['picture_url'] ?? null;
        
        // Log debug information
        log_message('debug', 'TopbarController processTopbarData - currentParticipant: ' . ($currentParticipant ? json_encode($currentParticipant) : 'NULL'));
        log_message('debug', 'TopbarController processTopbarData - participantName from currentParticipant: ' . ($participantName ?? 'NULL'));
        
        // If no participant name found, try to get name from user session or any participant
        if (!$participantName) {
            // Try to get name from user session
            $user = session()->get('user');
            log_message('debug', 'TopbarController processTopbarData - user session: ' . ($user ? json_encode($user) : 'NULL'));
            
            if (isset($user['full_name']) && !empty($user['full_name'])) {
                $participantName = $user['full_name'];
                log_message('debug', 'TopbarController processTopbarData - using user full_name: ' . $participantName);
            } elseif (isset($user['name']) && !empty($user['name'])) {
                $participantName = $user['name'];
                log_message('debug', 'TopbarController processTopbarData - using user name: ' . $participantName);
            }
            // If still no name and we have participants, try the first one
            elseif (!empty($participants)) {
                log_message('debug', 'TopbarController processTopbarData - trying participants for name');
                foreach ($participants as $p) {
                    if (!empty($p['full_name'])) {
                        $participantName = $p['full_name'];
                        log_message('debug', 'TopbarController processTopbarData - using participant full_name: ' . $participantName);
                        break;
                    }
                }
            }
        }
        
        $name = $participantName ?: 'Guest';
        log_message('debug', 'TopbarController processTopbarData - final name: ' . $name);

        // set current program to session
        if ($currentProgram !== null) {
            session()->set('current_program', $currentProgram);
        }

        return [
            'sorted_programs' => $sorted_programs,
            'currentProgramId' => $currentProgramId,
            'currentProgram' => $currentProgram,
            'currentParticipant' => $currentParticipant,
            'name' => $name,
            'profileImage' => $profileImage,
            'participant_programs' => $participant_programs
        ];
    }
}
