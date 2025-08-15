<?php

namespace App\Controllers;

class DebugController extends BaseController
{
    public function sessionData()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [];
        $data['session_id'] = session_id();
        $data['all_session_data'] = session()->get();
        
        $data['user'] = session()->get('user');
        $data['current_participant'] = session()->get('current_participant');
        $data['participants'] = session()->get('participants');
        $data['current_program_id'] = session()->get('current_program_id');
        $data['current_program'] = session()->get('current_program');
        $data['programs'] = session()->get('programs');

        // Test processTopbarData
        try {
            $topbarController = new \App\Controllers\TopbarController();
            $topbarData = $topbarController->processTopbarData();
            $data['topbar_data'] = $topbarData;
        } catch (\Exception $e) {
            $data['topbar_error'] = $e->getMessage();
        }

        echo "<pre>";
        print_r($data);
        echo "</pre>";
        
        return null;
    }
}
?>
