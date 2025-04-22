<?php

namespace App\Controllers\ambassador;
use App\Controllers\BaseController;

class Profile extends BaseController
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

        $ambassadorDetails = $this->makeGetRequest('/ambassadors/' . $ambassador['id'], [], false);

        if (isset($ambassadorDetails['id'])) {
            $ambassador['details'] = $ambassadorDetails;
        } else {
            return redirect()->to('/ambassadors/dashboard')->with('error', 'Ambassador details not found');
        }

        // generated link
        $generatedLink = $this->makeGetRequest('/ambassadors/' . $ambassador['id'] . '/generate-link', [], false);

        $data = [
            'title' => 'Dashboard',
            'ambassador' => $ambassador,
            'currentProgram' => $ambassador['program'],
            'generatedLink' => $generatedLink['referral_link'],
        ];

        return $this->render('ambassador/profile', $data);
    }

}
