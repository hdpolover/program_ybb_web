<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController; 

class Submission extends BaseController
{

    public function index()
    {
        $currentParticipantId = session()->get('current_participant_id');

        // participants data from session
        $participants = session()->get('participants');

        $currentParticipant = null;

        foreach ($participants as $participant) {
            if ($participant['id'] == $currentParticipantId) {
                $currentParticipant = $participant;
                break;
            }
        }

        $data = [
            'title' => 'Submission',
            'currentParticipant' => $currentParticipant,
        ];

        return $this->render('participant/submission/index', $data);
    }

    public function edit()
    {
        $subthemes = $this->makeGetRequest('/program-subthemes/program/' . $this->currentUrl);
        $data = [
            'title' => 'Submission',
            'subthemes' => $subthemes,
        ];

        return $this->render('participant/submission/edit', $data);
    }
}
