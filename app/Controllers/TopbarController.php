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

                // Get updated participants data to refresh the session
                $updatedParticipants = $this->makeGetRequest('/participants/user/' . $userId, [], true);

                if ($updatedParticipants) {
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
        });

        // Get current program id from session or use the first program
        $currentProgramId = null;

        // First try to get from session
        if (session()->has('current_program_id')) {
            $currentProgramId = session()->get('current_program_id');

            // Verify if the current program ID is one the participant is registered for
            if (!in_array($currentProgramId, $participant_programs) && !empty($participant_programs)) {
                // If not registered for current program but registered for others,
                // switch to a program they are registered for
                $currentProgramId = $participant_programs[0];
            }
        }
        // Otherwise use the first registered program if available
        else if (!empty($participant_programs)) {
            $currentProgramId = $participant_programs[0];
        }
        // As last resort, use the first program in the sorted list
        else if (!empty($sorted_programs)) {
            $currentProgramId = $sorted_programs[0]['id'] ?? null;
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
        $name = $participantName ?: 'Guest';

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
