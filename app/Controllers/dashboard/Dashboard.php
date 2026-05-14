<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;
use Exception;

class Dashboard extends BaseController
{
    public function index()
    {
        $startTime = microtime(true);
        
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');
        $currentParticipantId = session()->get('current_participant_id');
        $currentParticipant = session()->get('current_participant');

        $programs = session()->get('programs') ?? [];
        $currentProgram = null;

        foreach ($programs as $program) {
            if ((string)($program['id'] ?? '') === (string)$currentProgramId) {
                $currentProgram = $program;
                break;
            }
        }

        $cache = \Config\Services::cache();

        // Fetch detailed participant information including category
        $detailedParticipant = null;
        $switchEligibility = null;
        if ($currentParticipantId) {
            $participantCacheKey = "participant_detail_{$currentParticipantId}";
            $detailedParticipant = $cache->get($participantCacheKey);
            if ($detailedParticipant === null) {
                $detailedParticipant = $this->makeGetRequest('/participants/' . $currentParticipantId, [], true);
                if ($detailedParticipant) {
                    $cache->save($participantCacheKey, $detailedParticipant, 120);
                }
            }

            $eligibilityCacheKey = "switch_eligibility_{$currentParticipantId}";
            $switchEligibility = $cache->get($eligibilityCacheKey);
            if ($switchEligibility === null) {
                $eligibilityStartTime = microtime(true);
                $switchEligibility = $this->makeGetRequest('/participants/' . $currentParticipantId . '/switch-category/check', [], false, false);
                $eligibilityLoadTime = round((microtime(true) - $eligibilityStartTime) * 1000, 2);
                log_message('info', "Switch eligibility fetched for {$currentParticipantId} in {$eligibilityLoadTime}ms");
                if ($switchEligibility) {
                    $cache->save($eligibilityCacheKey, $switchEligibility, 300);
                }
            }
        }

        // Get payment status with short cache for real-time feel
        $paymentStatus = 'completed'; // Default
        $paymentDueDate = null;
        $hasSubmittedForm = false;

        if ($currentParticipantId) {
            $paymentsCacheKey = "participant_payments_{$currentParticipantId}";
            $participantPaymentsResponse = $cache->get($paymentsCacheKey);
            if ($participantPaymentsResponse === null) {
                $paymentsStartTime = microtime(true);
                $participantPaymentsResponse = $this->makeGetRequest('/payments/participants/' . $currentParticipantId, [], false, false);
                $paymentsLoadTime = round((microtime(true) - $paymentsStartTime) * 1000, 2);
                log_message('info', "Payments fetched for {$currentParticipantId} in {$paymentsLoadTime}ms");
                if ($participantPaymentsResponse) {
                    $cache->save($paymentsCacheKey, $participantPaymentsResponse, 30);
                }
            }

            // Extract payments array from the new nested response structure
            $participantPayments = null;
            if (isset($participantPaymentsResponse['data']['payments']) && is_array($participantPaymentsResponse['data']['payments'])) {
                $participantPayments = $participantPaymentsResponse['data']['payments'];
            }

            // Check if any payment is pending
            $hasSuccessfulPayment = false;
            if (isset($participantPayments) && is_array($participantPayments)) {
                foreach ($participantPayments as $payment) {
                    if (isset($payment['status']) && (string)$payment['status'] === '2') {
                        $hasSuccessfulPayment = true;
                        break;
                    }
                }

                if (!$hasSuccessfulPayment) {
                    $paymentStatus = 'pending';

                    if (isset($currentProgram['payment_due_date'])) {
                        $paymentDueDate = $currentProgram['payment_due_date'];
                    }
                }
            }

            $statusCacheKey = "participant_status_{$currentParticipantId}";
            $participantStatuses = $cache->get($statusCacheKey);
            if ($participantStatuses === null) {
                $statusStartTime = microtime(true);
                $participantStatuses = $this->makeGetRequest('/participants/' . $currentParticipantId . '/status', [], false, false);
                $statusLoadTime = round((microtime(true) - $statusStartTime) * 1000, 2);
                log_message('info', "Participant status fetched for {$currentParticipantId} in {$statusLoadTime}ms");
                if ($participantStatuses) {
                    $cache->save($statusCacheKey, $participantStatuses, 30);
                }
            }

            if (isset($participantStatuses['form_status']) && $participantStatuses['form_status'] === '2') {
                $hasSubmittedForm = true;
            }
        }

        // Check for available programs that user is not registered for
        $availablePrograms = [];
        
        // Extract program IDs from participants array
        $participants = session()->get('participants') ?? [];
        $participantProgramIds = [];
        foreach ($participants as $p) {
            if (isset($p['program_id'])) {
                $participantProgramIds[] = $p['program_id'];
            }
        }
        
        if (isset($programs) && is_array($programs)) {
            foreach ($programs as $program) {
                // Check if program is active and user is not registered
                $isActive = isset($program['is_active']) && $program['is_active'];
                $isRegistered = in_array($program['id'] ?? null, $participantProgramIds);
                
                if ($isActive && !$isRegistered) {
                    $availablePrograms[] = $program;
                }
            }
        }
        
        log_message('info', 'Available unregistered programs: ' . count($availablePrograms) . ' (User registered in: ' . implode(', ', $participantProgramIds) . ')');

        // Build view data
        $data = [
            'title' => 'Dashboard',
            'currentProgram' => $currentProgram,
            'currentParticipant' => $currentParticipant,
            'detailedParticipant' => $detailedParticipant,
            'paymentStatus' => $paymentStatus,
            'paymentDueDate' => $paymentDueDate,
            'hasSubmittedForm' => $hasSubmittedForm,
            'switchEligibility' => $switchEligibility,
            'availablePrograms' => $availablePrograms,
        ];

        $totalLoadTime = round((microtime(true) - $startTime) * 1000, 2);
        log_message('info', "Dashboard loaded in {$totalLoadTime}ms");

        return $this->render('participant/dashboard/index', $data);
    }

    /**
     * Switch participant category using the new API endpoint
     */
    public function switchCategory()
    {
        log_message('info', 'switchCategory method called');
        
        // Only allow POST requests (case-insensitive comparison)
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            log_message('error', 'switchCategory: Non-POST request method: ' . $this->request->getMethod());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ])->setStatusCode(405);
        }

        log_message('info', 'switchCategory: POST request confirmed');

        try {
            // Get JSON data from request body
            $json = $this->request->getJSON(true);
            log_message('info', 'switchCategory: JSON data received: ' . json_encode($json));
            
            $participantId = $json['participant_id'] ?? null;

            log_message('info', 'switchCategory: Participant ID: ' . $participantId);

            // Validate required parameters
            if (!$participantId) {
                log_message('error', 'switchCategory: Missing required parameter: participant_id');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required parameter: participant_id'
                ])->setStatusCode(400);
            }

            // Additional validation: check if participant_id belongs to current user
            $sessionParticipantId = session()->get('current_participant_id');
            if ($sessionParticipantId && $sessionParticipantId !== $participantId) {
                log_message('warning', 'switchCategory: Unauthorized attempt to switch category for participant ' . $participantId . ' by user with participant ' . $sessionParticipantId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized: You can only switch your own registration category'
                ])->setStatusCode(403);
            }

            // Check if user is logged in and get session data
            if (!session()->get('isLoggedIn')) {
                log_message('error', 'switchCategory: User not authenticated');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User not authenticated'
                ])->setStatusCode(401);
            }

            log_message('info', 'switchCategory: User authentication confirmed');

            // Use the API endpoint for category switching (API will determine target category automatically)
            $apiEndpoint = "/participants/{$participantId}/switch-category";

            log_message('info', 'switchCategory: Making API call to: ' . $apiEndpoint);

            // Make the API call using the new endpoint (no POST data needed)
            $response = $this->makePostRequest($apiEndpoint, [], [], true);

            log_message('info', 'switchCategory: API response received: ' . json_encode($response));

            // Handle both possible response formats:
            // 1. Full response with status/message/data structure
            // 2. Just the data portion (if makePostRequest extracts it)
            $isSuccess = false;
            $responseData = null;
            $successMessage = '';
            
            if ($response) {
                // Check if this is a full response with status field
                if (isset($response['status']) && $response['status'] === 'success') {
                    $isSuccess = true;
                    $responseData = $response['data'] ?? [];
                    $successMessage = $response['message'] ?? 'Category switched successfully';
                }
                // Check if this is just the data portion (participant and category_switch exist)
                elseif (isset($response['participant']) && isset($response['category_switch'])) {
                    $isSuccess = true;
                    $responseData = $response;
                    $successMessage = 'Category switched successfully';
                }
            }

            if ($isSuccess && $responseData) {
                // Success - category switched
                $newCategory = $responseData['participant']['category'] ?? 'Unknown';
                $categorySwitch = $responseData['category_switch'] ?? [];
                $targetCategoryText = $newCategory === 'fully_funded' ? 'Fully Funded' : 'Self Funded';
                
                log_message('info', 'switchCategory: Success response, updating session data');
                log_message('info', 'switchCategory: Category switched from ' . ($categorySwitch['from'] ?? 'Unknown') . ' to ' . ($categorySwitch['to'] ?? 'Unknown'));
                
                // Update session participant data with the new participant data from the switch response
                $updatedParticipantFromResponse = $responseData['participant'] ?? null;
                if ($updatedParticipantFromResponse) {
                    log_message('info', 'switchCategory: Updating session with participant data from switch response');
                    session()->set('current_participant', $updatedParticipantFromResponse);
                    
                    // Also update the detailed participant data if it exists in session
                    $currentDetailedParticipant = session()->get('detailed_participant');
                    if ($currentDetailedParticipant) {
                        // Merge the updated category into the detailed participant data
                        $currentDetailedParticipant['category'] = $updatedParticipantFromResponse['category'];
                        $currentDetailedParticipant['updated_at'] = $updatedParticipantFromResponse['updated_at'];
                        session()->set('detailed_participant', $currentDetailedParticipant);
                        log_message('info', 'switchCategory: Updated detailed participant data in session');
                    }
                    
                    log_message('info', 'switchCategory: Session updated with new participant category: ' . $updatedParticipantFromResponse['category']);
                } else {
                    // Fallback: fetch fresh participant data from API
                    log_message('info', 'switchCategory: No participant data in response, fetching fresh data from API');
                    $freshParticipantData = $this->makeGetRequest("/participants/{$participantId}", [], true);
                    if ($freshParticipantData) {
                        log_message('info', 'switchCategory: Fresh participant data fetched: ' . json_encode($freshParticipantData));
                        session()->set('current_participant', $freshParticipantData);
                    }
                }
                
                log_message('info', "Category switch successful for participant {$participantId} to {$newCategory}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => [
                        'new_category' => $newCategory,
                        'participant_id' => $participantId,
                        'category_display_name' => $targetCategoryText,
                        'category_switch' => $categorySwitch
                    ]
                ]);
            } else {
                // API returned error or unexpected response
                $errorMessage = 'Failed to switch participant category';
                
                // Try to extract error message from various response formats
                if (is_array($response)) {
                    if (isset($response['message'])) {
                        $errorMessage = $response['message'];
                    } elseif (isset($response['error'])) {
                        $errorMessage = $response['error'];
                    } elseif (isset($response['status']) && $response['status'] === 'error') {
                        $errorMessage = $response['message'] ?? $errorMessage;
                    }
                }
                
                log_message('warning', "switchCategory: API returned error for participant {$participantId}: " . $errorMessage);
                log_message('warning', "switchCategory: Full error response: " . json_encode($response));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMessage
                ])->setStatusCode(400);
            }

        } catch (Exception $e) {
            log_message('error', 'switchCategory: Exception occurred: ' . $e->getMessage());
            log_message('error', 'switchCategory: Exception trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ])->setStatusCode(500);
        }
    }
}
