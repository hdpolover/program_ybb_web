<?php

namespace App\Controllers\ambassador;
use App\Controllers\BaseController;

class ReferredParticipants extends BaseController
{
    public function index()
    {
        $ambassador = session()->get('user');

        // get program details
        $programId = $ambassador['program_id'] ?? null;

        if ($programId) {
            $program = $this->makeGetRequest('/programs/' . $programId, [], false);
            if (isset($program['id'])) {
                $ambassador['program'] = $program;
            } else {
                return redirect()->to('/ambassadors/dashboard')->with('error', 'Program not found');
            }
        } else {
            return redirect()->to('/ambassadors/dashboard')->with('error', 'Program ID not found in session');
        }
        
        // Fetch referred participants data from API (this is a placeholder, implement the actual API call)
        $referredParticipants = $this->makeGetRequest('/ambassadors/' . $ambassador['id'] . '/referrals', [], false);
        
        $data = [
            'title' => 'Referred Participants',
            'ambassador' => $ambassador,
            'currentProgram' => $ambassador['program'],
            'referredParticipants' => $referredParticipants['referrals'], // Update with actual participants data
        ];

        return $this->render('ambassador/referred-participants', $data);
    }

}
