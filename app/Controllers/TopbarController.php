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
            ];
        }

        // Sort programs based on currentParticipant programs
        foreach ($programs as $program) {
            if (in_array($program['id'] ?? null, $participant_programs)) {
                $sorted_programs[] = $program;
            }
        }

        // Sort the programs by active status (active programs first)
        usort($sorted_programs, function ($a, $b) {
            // Reverse the comparison to sort in descending order (true first, false last)
            return ($b['is_active'] ?? 0) <=> ($a['is_active'] ?? 0);
        });

        // Get current program id from session or use the first program
        $currentProgramId = null;
        
        // First try to get from session
        if (session()->has('current_program_id')) {
            $currentProgramId = session()->get('current_program_id');
        } 
        // Otherwise use the first sorted program if available
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
            'profileImage' => $profileImage
        ];
    }
}