<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $startTime = microtime(true);
        
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

        // Initialize cache service
        $cache = \Config\Services::cache();

        // Get payment status with caching
        $paymentStatus = 'completed'; // Default
        $paymentDueDate = null;
        $hasSubmittedForm = false;
        
        if ($currentParticipantId) {
            // Cache participant payments (5 minute cache)
            $paymentsCacheKey = "participant_payments_" . $currentParticipantId . "_v1";
            $participantPayments = $cache->get($paymentsCacheKey);
            
            if ($participantPayments === null) {
                $paymentsStartTime = microtime(true);
                $participantPayments = $this->makeGetRequest('/payments/participants/' . $currentParticipantId, [], false, false);
                $paymentsLoadTime = round((microtime(true) - $paymentsStartTime) * 1000, 2);
                
                // Cache for 5 minutes (300 seconds)
                if ($participantPayments !== null) {
                    $cache->save($paymentsCacheKey, $participantPayments, 300);
                    log_message('info', "Participant payments cached for {$currentParticipantId} (loaded in {$paymentsLoadTime}ms)");
                }
            } else {
                log_message('debug', "Participant payments cache hit for {$currentParticipantId}");
            }
            
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
            
            // Cache form submission status (10 minute cache)
            $statusCacheKey = "participant_status_" . $currentParticipantId . "_v1";
            $participantStatuses = $cache->get($statusCacheKey);
            
            if ($participantStatuses === null) {
                $statusStartTime = microtime(true);
                $participantStatuses = $this->makeGetRequest('/participants/' . $currentParticipantId . '/status', [], false, false);
                $statusLoadTime = round((microtime(true) - $statusStartTime) * 1000, 2);
                
                // Cache for 10 minutes (600 seconds)
                if ($participantStatuses !== null) {
                    $cache->save($statusCacheKey, $participantStatuses, 600);
                    log_message('info', "Participant status cached for {$currentParticipantId} (loaded in {$statusLoadTime}ms)");
                }
            } else {
                log_message('debug', "Participant status cache hit for {$currentParticipantId}");
            }
            
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
            'hasSubmittedForm' => $hasSubmittedForm,
        ];

        $totalLoadTime = round((microtime(true) - $startTime) * 1000, 2);
        log_message('info', "Dashboard loaded in {$totalLoadTime}ms");

        return $this->render('participant/dashboard/index', $data);
    }
}
