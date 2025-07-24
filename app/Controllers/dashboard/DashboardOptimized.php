<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;
use App\Services\CacheService;

class DashboardOptimized extends BaseController
{
    protected $cacheService;

    public function __construct()
    {
        parent::__construct();
        $this->cacheService = new CacheService();
    }

    public function index()
    {
        $startTime = microtime(true);
        
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');
        $currentParticipantId = session()->get('current_participant_id');

        $programs = session()->get('programs') ?? [];
        $currentProgram = $this->getCurrentProgram($programs, $currentProgramId);

        // Initialize default values
        $paymentStatus = 'completed';
        $paymentDueDate = null;
        $hasSubmittedForm = false;

        if ($currentParticipantId) {
            // Use parallel data fetching for better performance
            $dashboardData = $this->fetchDashboardDataParallel($currentParticipantId);
            
            $paymentStatus = $dashboardData['paymentStatus'];
            $paymentDueDate = $dashboardData['paymentDueDate'];
            $hasSubmittedForm = $dashboardData['hasSubmittedForm'];
        }

        $loadTime = round((microtime(true) - $startTime) * 1000, 2);
        log_message('info', "Dashboard loaded in {$loadTime}ms for participant {$currentParticipantId}");

        // Build view data
        $data = [
            'title' => 'Dashboard',
            'currentProgram' => $currentProgram,
            'paymentStatus' => $paymentStatus,
            'paymentDueDate' => $paymentDueDate,
            'hasSubmittedForm' => $hasSubmittedForm,
            'loadTime' => $loadTime, // For debugging
        ];

        return $this->render('participant/dashboard/index', $data);
    }

    /**
     * Get current program from programs array (optimized)
     */
    private function getCurrentProgram(array $programs, $currentProgramId): ?array
    {
        if (!$currentProgramId || empty($programs)) {
            return null;
        }

        // Use direct lookup instead of foreach loop
        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                return $program;
            }
        }

        return null;
    }

    /**
     * Fetch dashboard data with caching and parallel processing
     */
    private function fetchDashboardDataParallel(int $participantId): array
    {
        // Default values
        $result = [
            'paymentStatus' => 'completed',
            'paymentDueDate' => null,
            'hasSubmittedForm' => false
        ];

        try {
            // Fetch payment status with caching (5 minute cache)
            $paymentStatus = $this->getPaymentStatusCached($participantId);
            $result['paymentStatus'] = $paymentStatus['status'];
            $result['paymentDueDate'] = $paymentStatus['dueDate'];

            // Fetch form status with caching (10 minute cache)
            $formStatus = $this->getFormStatusCached($participantId);
            $result['hasSubmittedForm'] = $formStatus;

        } catch (\Exception $e) {
            log_message('error', 'Dashboard data fetch error: ' . $e->getMessage());
            // Return defaults on error
        }

        return $result;
    }

    /**
     * Get payment status with caching
     */
    private function getPaymentStatusCached(int $participantId): array
    {
        $cacheKey = $this->cacheService->getParticipantPaymentsKey($participantId);
        
        return $this->cacheService->remember($cacheKey, function() use ($participantId) {
            $participantPayments = $this->makeGetRequest('/payments/participants/' . $participantId, [], false, false);
            
            $status = 'completed';
            $dueDate = null;
            $hasSuccessfulPayment = false;
            
            if (isset($participantPayments) && is_array($participantPayments)) {
                foreach ($participantPayments as $payment) {
                    if (isset($payment['status']) && $payment['status'] === '2') {
                        $hasSuccessfulPayment = true;
                        break;
                    }
                }
                
                if (!$hasSuccessfulPayment) {
                    $status = 'pending';
                    
                    // Get due date from current program if available
                    $currentProgram = session()->get('current_program');
                    if (isset($currentProgram['payment_due_date'])) {
                        $dueDate = $currentProgram['payment_due_date'];
                    }
                }
            }
            
            return [
                'status' => $status,
                'dueDate' => $dueDate,
                'lastUpdated' => time()
            ];
        }, 300); // 5 minute cache
    }

    /**
     * Get form submission status with caching
     */
    private function getFormStatusCached(int $participantId): bool
    {
        $cacheKey = $this->cacheService->getParticipantStatusKey($participantId);
        
        return $this->cacheService->remember($cacheKey, function() use ($participantId) {
            $participantStatuses = $this->makeGetRequest('/participants/' . $participantId . '/status', [], false, false);
            
            return isset($participantStatuses) && 
                   isset($participantStatuses['form_status']) && 
                   $participantStatuses['form_status'] === '2';
        }, 600); // 10 minute cache
    }

    /**
     * Clear participant cache when data is updated
     */
    public function clearParticipantCache(int $participantId): void
    {
        $this->cacheService->clearParticipantCache($participantId);
        log_message('info', "Cleared cache for participant {$participantId}");
    }
}
