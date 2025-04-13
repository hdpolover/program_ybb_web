<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');
        $currentParticipantId = session()->get('current_participant_id');

        $programs = session()->get('programs') ?? [];
        $currentProgram = null;

        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                $currentProgram = $program;
                break;
            }
        }

        // Get payment status
        $paymentStatus = 'completed'; // Default
        $paymentDueDate = null;
        
        if ($currentParticipantId) {
            // Get participant payments
            $participantPayments = $this->makeGetRequest('/payments/participants/' . $currentParticipantId, [], false, false);
            
            // Check if any payment is pending
            $hasSuccessfulPayment = false;
            if (isset($participantPayments) && is_array($participantPayments)) {
                foreach ($participantPayments as $payment) {
                    if (isset($payment['status']) && $payment['status'] === '2') {
                        $hasSuccessfulPayment = true;
                        break;
                    }
                }
                
                if (!$hasSuccessfulPayment) {
                    $paymentStatus = 'pending';
                    
                    // If program has payment due date, use it
                    if (isset($currentProgram['payment_due_date'])) {
                        $paymentDueDate = $currentProgram['payment_due_date'];
                    }
                }
            }
            
            // Get form submission status
            $participantStatuses = $this->makeGetRequest('/participants/' . $currentParticipantId . '/status', [], false, false);
            $hasSubmittedForm = false;
            
            if (isset($participantStatuses) && isset($participantStatuses['form_status']) && $participantStatuses['form_status'] === '2') {
                $hasSubmittedForm = true;
            }
        }

        // Build view data
        $data = [
            'title' => 'Dashboard',
            'currentProgram' => $currentProgram,
            'paymentStatus' => $paymentStatus,
            'paymentDueDate' => $paymentDueDate,
            'hasSubmittedForm' => $hasSubmittedForm ?? false,
        ];

        return $this->render('participant/dashboard/index', $data);
    }
}
