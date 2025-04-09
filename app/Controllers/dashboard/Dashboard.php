<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        $programs = session()->get('programs') ?? [];
        $currentProgram = null;

        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                $currentProgram = $program;
                break;
            }
        }

        // Build view data
        $data = [
            'title' => 'Dashboard',
            'currentProgram' => $currentProgram,
        ];

        return $this->render('participant/dashboard/index', $data);
    }
}
