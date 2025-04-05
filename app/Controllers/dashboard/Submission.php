<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Submission extends BaseController
{

    public function index()
    {
        $currentParticipantId = session()->get('current_participant_id');

        $submissionData = $this->makeGetRequest('/submissions/participants/' . $currentParticipantId);

        // Get programs from session
        $programs = session()->get('programs') ?? [];

        $currentProgramId = session()->get('current_program_id') ?? null;
        $currentProgram = null;
        $currentParticipant = null;

        // get current program based on current program id
        if ($currentProgramId !== null) {
            foreach ($programs as $program) {
                if (($program['id'] ?? null) === $currentProgramId) {
                    $currentProgram = $program;
                    break;
                }
            }
        }

        $participant = $submissionData['participant'] ?? null;
        $participantEssays = $submissionData['participant_essays'] ?? null;
        $participantSubtheme = $submissionData['participant_subtheme'] ?? null;
        $programEssays = $submissionData['program_essays'] ?? null;
        $programSubthemes = $submissionData['program_subthemes'] ?? null;

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
        $currentProgram = session()->get('current_program') ?? null;

        $formData = $this->makeGetRequest('/submissions/program/' . $currentProgram['id'] . '/form');
        $programSubthemes = $formData['subthemes'] ?? [];
        $programEssays = $formData['essays'] ?? [];
        $competitionCategories = $formData['competition_categories'] ?? [];

        $data = [
            'title' => 'Submission',
            'currentProgram' => $currentProgram,
            'subthemes' => $programSubthemes,
            'essays' => $programEssays,
            'competitionCategories' => $competitionCategories,
        ];

        return $this->render('participant/submission/edit', $data);
    }
}
