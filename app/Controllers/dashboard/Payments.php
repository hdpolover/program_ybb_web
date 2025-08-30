<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Payments extends BaseController
{
    /**
     * Filter program payments based on participant category and payment type
     * 
     * @param array $programPayments All program payments
     * @param string $participantCategory The participant's category (self_funded, fully_funded)
     * @param array $participantPayments Array of participant's payment attempts (used for payment history)
     * @return array Filtered program payments
     */
    public function filterPaymentsByCategory($programPayments, $participantCategory, $participantPayments = [])
    {
        if (empty($programPayments) || !is_array($programPayments)) {
            log_message('debug', 'filterPaymentsByCategory: Empty or invalid program payments provided');
            return [];
        }
        
        // Create a map of program payment IDs that the participant has attempted
        $attemptedPaymentIds = [];
        foreach ($participantPayments as $payment) {
            if (isset($payment['program_payment_id'])) {
                $attemptedPaymentIds[$payment['program_payment_id']] = true;
            }
        }
        
        log_message('debug', 'filterPaymentsByCategory: Processing ' . count($programPayments) . ' payments for category: ' . $participantCategory);
        log_message('debug', 'filterPaymentsByCategory: Participant has attempted payments for IDs: ' . implode(', ', array_keys($attemptedPaymentIds)));
        
        $filteredPayments = [];
        
        foreach ($programPayments as $payment) {
            $paymentType = $payment['type'] ?? 'all'; // Default to 'all' if type not specified
            $paymentId = $payment['id'] ?? 'unknown';
            $paymentName = $payment['name'] ?? 'unnamed';
            
            log_message('debug', "filterPaymentsByCategory: Checking payment ID {$paymentId} ('{$paymentName}') with type '{$paymentType}' for participant category '{$participantCategory}'");
            
            // Logic for filtering:
            // - 'all' type payments are visible to everyone
            // - 'self_funded' type payments are only visible to self_funded participants
            // - 'fully_funded' type payments are only visible to fully_funded participants
            // - EXCEPTION: Always show payments that participant has attempted, regardless of category (for payment history)
            
            $shouldInclude = false;
            
            if ($paymentType === 'all') {
                // All participants can see 'all' type payments
                $shouldInclude = true;
                log_message('debug', "filterPaymentsByCategory: Including payment {$paymentId} - type is 'all'");
            } elseif ($paymentType === $participantCategory) {
                // Participant can see payments that match their category
                $shouldInclude = true;
                log_message('debug', "filterPaymentsByCategory: Including payment {$paymentId} - type matches participant category");
            } elseif (isset($attemptedPaymentIds[$paymentId])) {
                // IMPORTANT: Include payments that the participant has previously attempted, regardless of category
                $shouldInclude = true;
                log_message('debug', "filterPaymentsByCategory: Including payment {$paymentId} - participant has payment history for this payment (type: {$paymentType}, participant category: {$participantCategory})");
            } else {
                log_message('debug', "filterPaymentsByCategory: Excluding payment {$paymentId} - type '{$paymentType}' does not match participant category '{$participantCategory}' and no payment history");
            }
            
            if ($shouldInclude) {
                $filteredPayments[] = $payment;
            }
        }
        
        log_message('debug', 'filterPaymentsByCategory: Filtered payments for category ' . $participantCategory . ': ' . count($filteredPayments) . ' out of ' . count($programPayments) . ' total payments');
        
        return $filteredPayments;
    }

    public function getVisibleProgramPayments($allPayments, $participantPayments)
    {
        $today = date('Y-m-d H:i:s');
        
        log_message('debug', 'getVisibleProgramPayments: Processing ' . count($allPayments) . ' filtered payments and ' . count($participantPayments) . ' participant payments');

        // Group participant payments by program_payment_id (with safety check)
        $participantPaymentMap = [];
        foreach ($participantPayments as $pmt) {
            // Safety check for program_payment_id existence
            if (!isset($pmt['program_payment_id'])) {
                log_message('warning', 'Participant payment missing program_payment_id: ' . json_encode($pmt));
                continue; // Skip this payment if program_payment_id is missing
            }
            $participantPaymentMap[$pmt['program_payment_id']][] = $pmt;
        }

        $visiblePayments = [];

        // Track completed statuses
        $completed = [
            'registration' => false,
            'program_fee_1' => false,
            'program_fee_2' => false,
        ];

        // Helper to check if a payment is completed
        $isCompleted = function ($paymentId) use ($participantPaymentMap) {
            if (empty($participantPaymentMap[$paymentId])) return false;
            foreach ($participantPaymentMap[$paymentId] as $pmt) {
                if ($pmt['status'] == '2') return true; // 2 = success
            }
            return false;
        };

        // Helper to check if user has paid anything for this payment
        $hasAnyAttempt = function ($paymentId) use ($participantPaymentMap) {
            return !empty($participantPaymentMap[$paymentId]);
        };

        // Separate payments by category
        $byCategory = [
            'registration' => [],
            'program_fee_1' => [],
            'program_fee_2' => [],
        ];

        foreach ($allPayments as $payment) {
            $paymentCategory = $payment['category'] ?? 'unknown';
            $paymentType = $payment['type'] ?? 'all';
            $paymentId = $payment['id'] ?? 'unknown';
            
            log_message('debug', "getVisibleProgramPayments: Payment ID {$paymentId} has category '{$paymentCategory}' and type '{$paymentType}'");
            
            if (isset($byCategory[$paymentCategory])) {
                $byCategory[$paymentCategory][] = $payment;
            } else {
                log_message('warning', "getVisibleProgramPayments: Unknown payment category '{$paymentCategory}' for payment ID {$paymentId}. Available categories: " . implode(', ', array_keys($byCategory)));
                // Still add to visible payments if it doesn't fit standard categories
                $visiblePayments[] = $payment;
            }
        }
        
        // Debug the categorized payments
        foreach ($byCategory as $categoryName => $payments) {
            log_message('debug', "getVisibleProgramPayments: Category '{$categoryName}' has " . count($payments) . " payments");
        }

        // Sort each category by start_date (especially important for registration: early/late bid)
        foreach ($byCategory as &$group) {
            usort($group, function ($a, $b) {
                return strtotime($a['start_date']) - strtotime($b['start_date']);
            });
        }

        // Handle registration with funding type filtering
        $registrationDone = false;
        $registrationPayments = [];
        $hasSuccessfulRegistration = false;
        $successfulRegistrationType = null;
        
        log_message('debug', 'getVisibleProgramPayments: Today is ' . $today);
        log_message('debug', 'getVisibleProgramPayments: Processing registration payments: ' . count($byCategory['registration']));
        
        // Debug each payment's date range
        foreach ($allPayments as $payment) {
            $paymentId = $payment['id'];
            $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
            $hasAttempts = !empty($participantPaymentMap[$paymentId]);
            log_message('debug', "getVisibleProgramPayments: Payment ID {$paymentId} ({$payment['name']}) - Start: {$payment['start_date']}, End: {$payment['end_date']}, Within range: " . ($isWithinDateRange ? 'YES' : 'NO') . ", Has attempts: " . ($hasAttempts ? 'YES' : 'NO'));
        }
        
        // First pass: check for successful registrations and their types
        foreach ($byCategory['registration'] as $payment) {
            $paymentId = $payment['id'];
            $isPaid = $isCompleted($paymentId);
            
            if ($isPaid) {
                $hasSuccessfulRegistration = true;
                $successfulRegistrationType = $payment['type'];
                $registrationPayments[] = $payment;
                $registrationDone = true;
            }
        }
        
        // Second pass: handle unsuccessful attempts and active registrations
        foreach ($byCategory['registration'] as $payment) {
            $paymentId = $payment['id'];
            $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
            $isPaid = $isCompleted($paymentId);
            $hasTried = $hasAnyAttempt($paymentId);
            
            // Check if payment is close to start date (within 30 days)
            $startDate = strtotime($payment['start_date']);
            $todayTimestamp = strtotime($today);
            $daysToStart = ($startDate - $todayTimestamp) / (60 * 60 * 24);
            $isComingSoon = $daysToStart <= 30 && $daysToStart >= 0;
            
            // Check if payment has expired
            $endDate = strtotime($payment['end_date']);
            $isExpired = $todayTimestamp > $endDate;
            
            // Skip if already added as successful
            if ($isPaid) {
                continue;
            }
            
            // If there's a successful registration, only show unsuccessful attempts of different funding types
            // or unsuccessful attempts of the same type (for history)
            if ($hasSuccessfulRegistration) {
                // Show unsuccessful attempts regardless of type (for payment history)
                if ($hasTried) {
                    $registrationPayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding registration payment ID {$paymentId} due to previous attempt (with successful registration)");
                }
            } else {
                // No successful registration yet
                // Show if participant has attempted payment OR if within date range OR if coming soon OR if expired (to show what they missed)
                if ($hasTried || $isWithinDateRange || $isComingSoon || $isExpired) {
                    $registrationPayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding registration payment ID {$paymentId} - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", inRange: " . ($isWithinDateRange ? 'YES' : 'NO') . ", comingSoon: " . ($isComingSoon ? 'YES' : 'NO') . ", isExpired: " . ($isExpired ? 'YES' : 'NO') . " (days to start: " . round($daysToStart) . ")");
                }
            }
        }
        
        // Add all registration payments to visible payments
        $visiblePayments = array_merge($visiblePayments, $registrationPayments);

        $completed['registration'] = $registrationDone;

        // Handle program_fee_1 - show if registration is done OR if participant has attempted this payment OR if close to start date OR if expired
        if ($completed['registration']) {
            foreach ($byCategory['program_fee_1'] as $payment) {
                $paymentId = $payment['id'];
                $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
                $isPaid = $isCompleted($paymentId);
                $hasTried = $hasAnyAttempt($paymentId);
                
                // Check if payment is close to start date (within 30 days)
                $startDate = strtotime($payment['start_date']);
                $todayTimestamp = strtotime($today);
                $daysToStart = ($startDate - $todayTimestamp) / (60 * 60 * 24);
                $isComingSoon = $daysToStart <= 30 && $daysToStart >= 0;
                
                // Check if payment has expired
                $endDate = strtotime($payment['end_date']);
                $isExpired = $todayTimestamp > $endDate;

                if ($isPaid) {
                    $completed['program_fee_1'] = true;
                }

                // Show if participant has attempted payment OR if within date range OR if coming soon OR if expired
                if ($hasTried || $isWithinDateRange || $isComingSoon || $isExpired) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_1 payment ID {$paymentId} - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", inRange: " . ($isWithinDateRange ? 'YES' : 'NO') . ", comingSoon: " . ($isComingSoon ? 'YES' : 'NO') . ", isExpired: " . ($isExpired ? 'YES' : 'NO') . " (days to start: " . round($daysToStart) . ")");
                }
            }
        } else {
            // Even if registration is not complete, show program_fee_1 if participant has attempted it OR if it's expired (to show what they missed)
            foreach ($byCategory['program_fee_1'] as $payment) {
                $paymentId = $payment['id'];
                $hasTried = $hasAnyAttempt($paymentId);
                $isPaid = $isCompleted($paymentId);
                
                // Check if payment has expired
                $endDate = strtotime($payment['end_date']);
                $todayTimestamp = strtotime($today);
                $isExpired = $todayTimestamp > $endDate;

                if ($isPaid) {
                    $completed['program_fee_1'] = true;
                }

                if ($hasTried || $isExpired) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_1 payment ID {$paymentId} due to previous attempt or expiration - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", isExpired: " . ($isExpired ? 'YES' : 'NO'));
                }
            }
        }

        // Handle program_fee_2 - show if program_fee_1 is done OR if participant has attempted this payment OR if close to start date OR if expired
        if ($completed['program_fee_1']) {
            foreach ($byCategory['program_fee_2'] as $payment) {
                $paymentId = $payment['id'];
                $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
                $isPaid = $isCompleted($paymentId);
                $hasTried = $hasAnyAttempt($paymentId);
                
                // Check if payment is close to start date (within 30 days)
                $startDate = strtotime($payment['start_date']);
                $todayTimestamp = strtotime($today);
                $daysToStart = ($startDate - $todayTimestamp) / (60 * 60 * 24);
                $isComingSoon = $daysToStart <= 30 && $daysToStart >= 0;
                
                // Check if payment has expired
                $endDate = strtotime($payment['end_date']);
                $isExpired = $todayTimestamp > $endDate;

                if ($isPaid) {
                    $completed['program_fee_2'] = true;
                }

                // Show if participant has attempted payment OR if within date range OR if coming soon OR if expired
                if ($hasTried || $isWithinDateRange || $isComingSoon || $isExpired) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_2 payment ID {$paymentId} - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", inRange: " . ($isWithinDateRange ? 'YES' : 'NO') . ", comingSoon: " . ($isComingSoon ? 'YES' : 'NO') . ", isExpired: " . ($isExpired ? 'YES' : 'NO') . " (days to start: " . round($daysToStart) . ")");
                }
            }
        } else {
            // Even if program_fee_1 is not complete, show program_fee_2 if participant has attempted it OR if it's expired
            foreach ($byCategory['program_fee_2'] as $payment) {
                $paymentId = $payment['id'];
                $hasTried = $hasAnyAttempt($paymentId);
                $isPaid = $isCompleted($paymentId);
                
                // Check if payment has expired
                $endDate = strtotime($payment['end_date']);
                $todayTimestamp = strtotime($today);
                $isExpired = $todayTimestamp > $endDate;

                if ($isPaid) {
                    $completed['program_fee_2'] = true;
                }

                if ($hasTried || $isExpired) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_2 payment ID {$paymentId} due to previous attempt or expiration - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", isExpired: " . ($isExpired ? 'YES' : 'NO'));
                }
            }
        }

        // Remove duplicates just in case
        $visiblePayments = array_unique($visiblePayments, SORT_REGULAR);

        // Sort all by start date for final display
        usort($visiblePayments, function ($a, $b) {
            return strtotime($a['start_date']) - strtotime($b['start_date']);
        });

        log_message('debug', 'getVisibleProgramPayments: Returning ' . count($visiblePayments) . ' visible payments after processing all categories and visibility rules');

        return array_values($visiblePayments);
    }

    /**
     * Display the list of program payments required from the participant
     */
    public function index()
    {
        $programPayments = $this->makeGetRequest('/program-payments/program/' . session()->get('current_program_id'), [], false);
        $participantPaymentsResponse = $this->makeGetRequest('/payments/participants/' . session()->get('current_participant_id'), [], true);
        $paymentMethods = $this->makeGetRequest('/payment-methods/program/' . session()->get('current_program_id'), [], false);
        
        log_message('debug', 'Participant payments API response structure: ' . json_encode(array_keys($participantPaymentsResponse ?? []), JSON_PRETTY_PRINT));
        
        // Get participant data to determine their category
        $participantId = session()->get('current_participant_id');
        $participantResponse = $this->makeGetRequest('/participants/' . $participantId, [], true);
        $participantData = $participantResponse['participant'] ?? $participantResponse;
        $participantCategoryFromAPI = $participantData['category'] ?? 'self_funded'; // Default to self_funded if not found

        // Check if there's a session category that differs from API (due to recent category switch)
        $participantCategoryFromSession = session()->get('current_participant_category');
        
        // Use session category if it exists and is different from API (indicates recent switch)
        if (!empty($participantCategoryFromSession) && $participantCategoryFromSession !== $participantCategoryFromAPI) {
            log_message('info', "Using session category '{$participantCategoryFromSession}' instead of API category '{$participantCategoryFromAPI}' - likely due to recent category switch");
            $participantCategory = $participantCategoryFromSession;
        } else {
            $participantCategory = $participantCategoryFromAPI;
        }

        // Check category switch eligibility for payments page guidance
        $switchEligibility = null;
        if ($participantId) {
            $eligibilityStartTime = microtime(true);
            $switchEligibility = $this->makeGetRequest('/participants/' . $participantId . '/switch-category/check', [], false, false);
            $eligibilityLoadTime = round((microtime(true) - $eligibilityStartTime) * 1000, 2);
            
            if ($switchEligibility === null) {
                log_message('warning', "Category switch eligibility check failed for participant {$participantId} - API returned error (loaded in {$eligibilityLoadTime}ms)");
            } else {
                log_message('info', "Category switch eligibility checked for payments page - {$participantId} (loaded in {$eligibilityLoadTime}ms)");
                log_message('debug', 'Payments page switch eligibility data: ' . json_encode($switchEligibility));
            }
        }

        // Debug logging for program payments BEFORE filtering
        log_message('debug', 'Program payments BEFORE filtering: ' . count($programPayments ?? []) . ' payments');
        log_message('debug', 'Participant category: ' . $participantCategory . ' (API: ' . $participantCategoryFromAPI . ', Session: ' . ($participantCategoryFromSession ?? 'none') . ')');
        if (!empty($programPayments)) {
            foreach ($programPayments as $idx => $payment) {
                $paymentType = $payment['type'] ?? 'all';
                log_message('debug', "Payment {$idx}: ID={$payment['id']}, Name={$payment['name']}, Type={$paymentType}");
            }
        }
        
        // Extract the actual payments array from the API response first
        $participantPayments = [];
        if (isset($participantPaymentsResponse['data']['payments']) && is_array($participantPaymentsResponse['data']['payments'])) {
            $participantPayments = $participantPaymentsResponse['data']['payments'];
        } elseif (isset($participantPaymentsResponse['payments']) && is_array($participantPaymentsResponse['payments'])) {
            // Fallback for direct payments array (backward compatibility)
            $participantPayments = $participantPaymentsResponse['payments'];
        }
        
        log_message('debug', 'Extracted participant payments count: ' . count($participantPayments));
        
        // Filter program payments by participant category and type (including payment history)
        $programPayments = $this->filterPaymentsByCategory($programPayments, $participantCategory, $participantPayments);
        
        // Debug logging AFTER filtering
        log_message('debug', 'Program payments AFTER filtering: ' . count($programPayments ?? []) . ' payments');
        if (!empty($programPayments)) {
            foreach ($programPayments as $idx => $payment) {
                $paymentType = $payment['type'] ?? 'all';
                log_message('debug', "Filtered Payment {$idx}: ID={$payment['id']}, Name={$payment['name']}, Type={$paymentType}");
            }
        }
        // Store original participant payments for visibility logic
        $originalParticipantPayments = $participantPayments ?? [];
        
        if (empty($participantPayments)) {
            $participantPayments = [];
        } else {
            // Convert participant payments to a more usable format, prioritizing successful payments
            $organizedPayments = [];

            // First, group all payments by program_payment_id (with safety check)
            foreach ($participantPayments as $payment) {
                // Safety check for program_payment_id existence
                if (!isset($payment['program_payment_id'])) {
                    log_message('warning', 'Payment missing program_payment_id: ' . json_encode($payment));
                    continue; // Skip this payment if program_payment_id is missing
                }
                $organizedPayments[$payment['program_payment_id']][] = $payment;
            }

            // Then, for each program payment, prioritize successful payments
            foreach ($organizedPayments as $programPaymentId => $payments) {
                // Sort by status, with successful payments (status=2) first
                usort($payments, function ($a, $b) {
                    // If a is success (2), prioritize it
                    if ($a['status'] == '2') return -1;
                    // If b is success (2), prioritize it
                    if ($b['status'] == '2') return 1;
                    // Otherwise sort by timestamp (newest first) or another property
                    return 0;
                });

                // Use the highest priority payment (success if any exists)
                $participantPayments[$programPaymentId] = $payments[0];
            }
        }

        // Get visible program payments using original structure
        $programPayments = $this->getVisibleProgramPayments($programPayments, $originalParticipantPayments);
        
        // Debug logging for final visible payments
        log_message('debug', 'Final visible program payments: ' . count($programPayments ?? []) . ' payments');
        if (!empty($programPayments)) {
            foreach ($programPayments as $idx => $payment) {
                $paymentType = $payment['type'] ?? 'all';
                log_message('debug', "Final Payment {$idx}: ID={$payment['id']}, Name={$payment['name']}, Type={$paymentType}");
            }
        } else {
            log_message('warning', 'No visible program payments found after getVisibleProgramPayments()');
        }

        $data = [
            'title' => 'Payments',
            'programPayments' => $programPayments,
            'participantPayments' => $participantPayments,
            'paymentMethods' => $paymentMethods,
            'participantCategory' => $participantCategory, // Add participant category to view data
            'switchEligibility' => $switchEligibility, // Add switch eligibility data to view
        ];

        return $this->render('participant/payment/index', $data);
    }

    /**
     * Display the programPayment details and attempt history for a specific program programPayment
     * 
     * @param int $id The program programPayment ID
     */
    public function detail($id = null)
    {
        if (empty($id)) {
            return redirect()->to(base_url('payments'));
        }

        $participantId = session()->get('current_participant_id');
        $programId = session()->get('current_program_id');

        // Get participant data to determine their category
        $participantResponse = $this->makeGetRequest('/participants/' . $participantId, [], true);
        $participantData = $participantResponse['participant'] ?? $participantResponse;
        $participantCategoryFromAPI = $participantData['category'] ?? 'self_funded'; // Default to self_funded if not found

        // Check if there's a session category that differs from API (due to recent category switch)
        $participantCategoryFromSession = session()->get('current_participant_category');
        
        // Use session category if it exists and is different from API (indicates recent switch)
        if (!empty($participantCategoryFromSession) && $participantCategoryFromSession !== $participantCategoryFromAPI) {
            log_message('info', "Detail: Using session category '{$participantCategoryFromSession}' instead of API category '{$participantCategoryFromAPI}' - likely due to recent category switch");
            $participantCategory = $participantCategoryFromSession;
        } else {
            $participantCategory = $participantCategoryFromAPI;
        }

        // Use the combined endpoint that returns both program payment details and participant payment attempts
        log_message('debug', "Detail: Attempting to fetch combined endpoint: /payments/program-payments/{$id}/participants/{$participantId}");
        $responseData = $this->makeGetRequest('/payments/program-payments/' . $id . '/participants/' . $participantId, [], true);
        
        log_message('debug', 'Detail: Combined endpoint response: ' . json_encode($responseData));
        
        if (empty($responseData) || !isset($responseData['program_payment'])) {
            log_message('error', 'Detail: Combined endpoint failed or returned no program_payment. Response: ' . json_encode($responseData));
            session()->setFlashdata('error', 'Program payment not found.');
            return redirect()->to(base_url('payments'));
        }

        $programPayment = $responseData['program_payment'] ?? null;
        $payments = [];
        
        if (!$programPayment) {
            session()->setFlashdata('error', 'Program payment details not found.');
            return redirect()->to(base_url('payments'));
        }
        
        // Check if participant has access to this payment based on their category
        // Use the same logic as filterPaymentsByCategory - allow access if:
        // 1. Payment type is 'all'
        // 2. Payment type matches participant category  
        // 3. Participant has payment history for this payment (even if category doesn't match)
        $paymentType = $programPayment['type'] ?? 'all';
        $hasDirectAccess = ($paymentType === 'all') || ($paymentType === $participantCategory);
        
        // Check if participant has payment history for this payment
        $hasPaymentHistory = false;
        if (isset($responseData['payments']) && is_array($responseData['payments'])) {
            foreach ($responseData['payments'] as $payment) {
                if (isset($payment['program_payment_id']) && $payment['program_payment_id'] == $id) {
                    $hasPaymentHistory = true;
                    break;
                }
            }
        }
        
        $hasAccess = $hasDirectAccess || $hasPaymentHistory;
        
        if (!$hasAccess) {
            log_message('warning', 'Unauthorized access attempt: Participant ' . $participantId . ' (' . $participantCategory . ') tried to access ' . $paymentType . ' payment ' . $id . ' without history or direct access');
            session()->setFlashdata('error', 'You do not have access to this payment.');
            return redirect()->to(base_url('payments'));
        }

        // Log successful access
        $accessReason = $hasDirectAccess ? 'direct access' : 'payment history';
        log_message('debug', 'Payment detail access granted for participant ' . $participantId . ' to payment ' . $id . ' via ' . $accessReason);

        // Extract participant payment attempts from the combined response
        if (isset($responseData['payments']) && is_array($responseData['payments'])) {
            foreach ($responseData['payments'] as $payment) {
                $payments[$payment['id']] = $payment;
            }
        }
        
        log_message('debug', 'Payment detail: Found ' . count($payments) . ' payment attempts for program payment ID: ' . $id);
        log_message('debug', 'Program payment details: ' . json_encode([
            'id' => $programPayment['id'],
            'name' => $programPayment['name'],
            'type' => $programPayment['type'],
            'category' => $programPayment['category'],
            'start_date' => $programPayment['start_date'],
            'end_date' => $programPayment['end_date']
        ]));

        // get payment methods
        $paymentMethods = $this->makeGetRequest('/payment-methods/program/' . $programId, [], false);

        // Get participant data for payment access validation
        $participant = $this->makeGetRequest('/participants/' . $participantId, [], true);
        $participantCategory = $participant['category'] ?? 'self_funded';

        $data = [
            'title' => 'Payment Details',
            'programPayment' => $programPayment,
            'payments' => $payments,
            'paymentMethods' => $paymentMethods,
            'participant' => $participant,
            'participantCategory' => $participantCategory,
        ];

        return $this->render('participant/payment/detail', $data);
    }

    /**
     * Process a new programPayment attempt for a specific program programPayment
     */
    public function makePayment()
    {
        // get post data
        $inputs = $this->request->getPost();

        // get participant id from session
        $participantId = session()->get('current_participant_id');

        if (empty($participantId)) {
            return redirect()->to(base_url('payments'))->with('error', 'Participant ID not found in session.');
        }

        // Get participant data to determine their category
        $participantResponse = $this->makeGetRequest('/participants/' . $participantId, [], true);
        $participantData = $participantResponse['participant'] ?? $participantResponse;
        $participantCategoryFromAPI = $participantData['category'] ?? 'self_funded'; // Default to self_funded if not found

        // Check if there's a session category that differs from API (due to recent category switch)
        $participantCategoryFromSession = session()->get('current_participant_category');
        
        // Use session category if it exists and is different from API (indicates recent switch)
        if (!empty($participantCategoryFromSession) && $participantCategoryFromSession !== $participantCategoryFromAPI) {
            log_message('info', "MakePayment: Using session category '{$participantCategoryFromSession}' instead of API category '{$participantCategoryFromAPI}' - likely due to recent category switch");
            $participantCategory = $participantCategoryFromSession;
        } else {
            $participantCategory = $participantCategoryFromAPI;
        }

        // Get the program payment ID to validate amount
        $programPaymentId = $inputs['program_payment_id'] ?? null;
        if (empty($programPaymentId)) {
            return redirect()->to(base_url('payments'))->with('error', 'Program payment ID is required.');
        }

        // Fetch the program payment to validate its amount and access rights
        $programPayment = $this->makeGetRequest('/program-payments/' . $programPaymentId, [], false);
        if (!$programPayment) {
            return redirect()->to(base_url('payments'))->with('error', 'Unable to retrieve payment information.');
        }
        
        // Check if participant has access to this payment based on their category
        $paymentType = $programPayment['type'] ?? 'all';
        $hasAccess = ($paymentType === 'all') || ($paymentType === $participantCategory);
        
        if (!$hasAccess) {
            log_message('warning', 'Unauthorized payment attempt: Participant ' . $participantId . ' (' . $participantCategory . ') tried to pay for ' . $paymentType . ' payment ' . $programPaymentId);
            
            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You do not have access to this payment option.'
                ])->setStatusCode(403);
            } else {
                return redirect()->to(base_url('payments'))->with('error', 'You do not have access to this payment option.');
            }
        }
        
        // Check if the payment period has started
        $currentDateTime = new \DateTime();
        $paymentStartDate = new \DateTime($programPayment['start_date']);
        $paymentEndDate = new \DateTime($programPayment['end_date']);
        
        if ($currentDateTime < $paymentStartDate) {
            $daysUntilStart = $currentDateTime->diff($paymentStartDate)->days;
            $startDateFormatted = $paymentStartDate->format('M d, Y H:i');
            
            log_message('warning', 'Early payment attempt: Participant ' . $participantId . ' tried to pay for payment ' . $programPaymentId . ' before start date (' . $startDateFormatted . ')');
            
            $message = 'This payment is not yet available. ';
            if ($daysUntilStart == 0) {
                $message .= 'Payment opens today at ' . $paymentStartDate->format('H:i');
            } elseif ($daysUntilStart == 1) {
                $message .= 'Payment opens tomorrow (' . $startDateFormatted . ')';
            } else {
                $message .= 'Payment opens in ' . $daysUntilStart . ' days (' . $startDateFormatted . ')';
            }
            
            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $message
                ])->setStatusCode(400);
            } else {
                return redirect()->to(base_url('payments'))->with('error', $message);
            }
        }
        
        // Check if the payment period has ended
        if ($currentDateTime > $paymentEndDate) {
            $endDateFormatted = $paymentEndDate->format('M d, Y H:i');
            
            log_message('warning', 'Late payment attempt: Participant ' . $participantId . ' tried to pay for payment ' . $programPaymentId . ' after end date (' . $endDateFormatted . ')');
            
            $message = 'This payment period has ended on ' . $endDateFormatted;
            
            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $message
                ])->setStatusCode(400);
            } else {
                return redirect()->to(base_url('payments'))->with('error', $message);
            }
        }
        
        if (!$programPayment) {
            return redirect()->to(base_url('payments'))->with('error', 'Unable to retrieve payment information.');
        }

        // Check if the payment amount is 0 or less
        $paymentAmount = $programPayment['usd_amount'] ?? 0;
        if ($paymentAmount <= 0) {
            log_message('warning', 'Payment attempt with zero or negative amount: ' . $paymentAmount . ' for program payment ID: ' . $programPaymentId);

            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Payment amount  was not retrieved. Please refresh the page and try again.'
                ])->setStatusCode(400);
            } else {
                return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                    ->with('error', 'Payment amount was not retrieved. Please refresh the page and try again.');
            }
        }

        $paymentType = $inputs['paymentType'];

        if ($paymentType == 'manual') {
            $programPaymentId = $inputs['program_payment_id'];
            $paymentMethodId = $inputs['payment_method_id'];
            $sourceName = $inputs['source_name'] ?? '';
            $accountName = $inputs['account_name'] ?? '';
            $notes = $inputs['notes'] ?? '';
            $paymentDate = $inputs['payment_date'] ?? '';

            // Calculate IDR amount using the conversion rate from webSettings for manual payments
            $usdToIdrRate = $this->data['webSettings']['usd_in_idr'] ?? null;

            if (empty($usdToIdrRate) || $usdToIdrRate <= 0) {
                log_message('error', 'Invalid USD to IDR conversion rate for manual payment: ' . ($usdToIdrRate ?? 'null'));
                return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                    ->with('error', 'Currency conversion rate not available. Please contact support.');
            }

            $idrAmount = $paymentAmount * $usdToIdrRate;
            log_message('debug', 'Manual payment USD to IDR conversion: ' . $paymentAmount . ' USD * ' . $usdToIdrRate . ' = ' . $idrAmount . ' IDR');

            // Get the file with the correct field name matching the API
            $proof = $this->request->getFile('proof_url');

            // Check if we have a valid file
            $hasValidFile = $proof && $proof->isValid() && !$proof->hasMoved();

            if ($hasValidFile) {
                // This is a file upload, so we need to use a different approach
                // Manually build the POST payload and directly send it with cURL

                // Prepare the endpoint URL
                $url = $this->apiBaseUrl . '/payments/create';

                // Set up cURL
                $curl = curl_init();
                // Build the multipart/form-data payload
                $payload = [
                    'participant_id' => $participantId,
                    'program_payment_id' => $programPaymentId,
                    'payment_method_id' => $paymentMethodId,
                    'source_name' => $sourceName,
                    'account_name' => $accountName,
                    'notes' => $notes,
                    'payment_date' => $paymentDate,
                    'idr_amount' => $idrAmount,
                    'usd_amount' => $paymentAmount,
                    'proof' => curl_file_create(
                        $proof->getTempName(),
                        $proof->getMimeType(),
                        $proof->getName()
                    )
                ];

                log_message('debug', 'Making direct cURL payment request with file upload');

                // Get JWT token for authentication
                $jwtToken = $this->getJwtToken();
                $headers = [
                    'Accept: application/json'
                ];
                if ($jwtToken) {
                    $headers[] = 'Authorization: Bearer ' . $jwtToken;
                }

                // Set cURL options
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_HTTPHEADER => $headers
                ]);

                // Execute the cURL request
                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $error = curl_error($curl);

                // Close the cURL session
                curl_close($curl);

                // Log the response
                log_message('debug', 'Payment cURL Response: ' . $response);
                if ($error) {
                    log_message('error', 'cURL Error: ' . $error);
                }

                // Process the response
                $responseData = json_decode($response, true);

                if ($httpCode >= 200 && $httpCode < 300 && $responseData) {
                    log_message('debug', 'Payment request successful');
                    session()->setFlashdata('swal', json_encode([
                        'title' => 'Success!',
                        'text' => 'Your payment has been submitted successfully and is now awaiting approval.',
                        'icon' => 'success'
                    ]));
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId));
                } else {
                    log_message('error', 'Payment request failed: ' . ($error ?: 'API Error'));
                    session()->setFlashdata('swal', json_encode([
                        'title' => 'Error!',
                        'text' => 'There was a problem submitting your payment. Please try again.',
                        'icon' => 'error'
                    ]));
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId));
                }
            } else {
                // No file upload, use the regular makePostRequest method
                $formData = [
                    'participant_id' => $participantId,
                    'program_payment_id' => $programPaymentId,
                    'payment_method_id' => $paymentMethodId,
                    'source_name' => $sourceName,
                    'account_name' => $accountName,
                    'notes' => $notes,
                    'payment_date' => $paymentDate,
                    'idr_amount' => $idrAmount,
                    'usd_amount' => $paymentAmount,
                ];

                log_message('debug', 'Making payment request with data (no file): ' . json_encode($formData));

                $response = $this->makePostRequest(
                    '/payments/create',
                    $formData,
                    [], // No additional headers
                    true, // Use JWT authentication (required for this endpoint)
                    true, // Send as JSON
                    false, // Not multipart
                    [] // No file data
                );

                if ($response) {
                    log_message('debug', 'Payment request successful: ' . json_encode($response));
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('success', 'Payment request submitted successfully.');
                } else {
                    log_message('error', 'Payment request failed');
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('error', 'Error submitting payment request.');
                }
            }
        } else {
            // Handle gateway payment types
            try {
                $programPaymentId = $inputs['program_payment_id'];
                $paymentMethodId = $inputs['payment_method_id'];

                // Check if this is an AJAX request
                $isAjax = $this->request->isAJAX();
                log_message('debug', 'Gateway payment is AJAX request: ' . ($isAjax ? 'yes' : 'no'));

                // Verify we have a valid payment method ID
                if (empty($paymentMethodId)) {
                    if ($isAjax) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => 'Please select a valid payment method'
                        ])->setStatusCode(400);
                    } else {
                        return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                            ->with('error', 'Please select a valid payment method.');
                    }
                }

                // Calculate IDR amount using the conversion rate from webSettings
                $usdToIdrRate = $this->data['webSettings']['usd_in_idr'] ?? null;

                if (empty($usdToIdrRate) || $usdToIdrRate <= 0) {
                    log_message('error', 'Invalid USD to IDR conversion rate: ' . ($usdToIdrRate ?? 'null'));

                    if ($isAjax) {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => 'Currency conversion rate not available. Please contact support.'
                        ])->setStatusCode(400);
                    } else {
                        return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                            ->with('error', 'Currency conversion rate not available. Please contact support.');
                    }
                }

                $idrAmount = $paymentAmount * $usdToIdrRate;

                log_message('debug', 'USD to IDR conversion: ' . $paymentAmount . ' USD * ' . $usdToIdrRate . ' = ' . $idrAmount . ' IDR');

                $paymentData = [
                    'participant_id' => $participantId,
                    'program_payment_id' => $programPaymentId,
                    'payment_method_id' => $paymentMethodId,
                    'idr_amount' => $idrAmount,
                    'usd_amount' => $paymentAmount,
                ];

            log_message('debug', 'Gateway payment data: ' . json_encode($paymentData));

            // Use the correct API endpoint for payment creation
            $apiEndpoint = '/payments/create';
            log_message('debug', 'Using API endpoint: ' . $this->apiBaseUrl . $apiEndpoint);

            // Make API call to initiate gateway payment           
            $response = $this->makePostRequest(
                $apiEndpoint,
                $paymentData,
                [], // No additional headers
                true, // Use JWT authentication (required for this endpoint)
                true, // Send as JSON
                false // Not multipart
            );

            // Log the complete response for debugging
            log_message('debug', 'Payment response: ' . json_encode($response));

            if (!$response || (isset($response['error']))) {
                $errorMessage = 'Failed to initiate payment. Please try again or contact support.';
                if (isset($response['error'])) {
                    log_message('error', 'API Error: ' . json_encode($response));
                    if (isset($response['message'])) {
                        $errorMessage = $response['message'];
                    }
                }

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $errorMessage
                    ])->setStatusCode(400);
                } else {
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('error', $errorMessage);
                }
            }

            // Log the complete response for debugging
            log_message('debug', 'Payment response structure: ' . json_encode($response));

            // Check if we have a redirect URL in the response data (handling nested data object)
            if (isset($response['data']['redirect_url']) && !empty($response['data']['redirect_url'])) {
                $redirectUrl = $response['data']['redirect_url'];
                log_message('debug', 'Found gateway URL in data.redirect_url: ' . $redirectUrl);

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'redirect_url' => $redirectUrl,
                        'program_payment_id' => $programPaymentId // Add program_payment_id to the response
                    ]);
                } else {
                    return redirect()->to($redirectUrl);
                }
            }
            // Direct redirect_url (non-nested)
            else if (isset($response['redirect_url']) && !empty($response['redirect_url'])) {
                $redirectUrl = $response['redirect_url'];
                log_message('debug', 'Found direct gateway URL: ' . $redirectUrl);

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'redirect_url' => $redirectUrl
                    ]);
                } else {
                    return redirect()->to($redirectUrl);
                }
            }
            // Payment ID in data object
            else if (isset($response['data']['payment_id'])) {
                $paymentId = $response['data']['payment_id'];
                log_message('debug', 'Found payment_id in data object: ' . $paymentId);

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'payment_id' => $paymentId,
                        'message' => 'Payment initiated. Please check payment status.'
                    ]);
                } else {
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('success', 'Payment initiated. Please check payment status.');
                }
            }
            // Direct payment_id (non-nested)
            else if (isset($response['payment_id'])) {
                $paymentId = $response['payment_id'];
                log_message('debug', 'Found direct payment_id: ' . $paymentId);

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'payment_id' => $paymentId,
                        'message' => 'Payment initiated. Please check payment status.'
                    ]);
                } else {
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('success', 'Payment initiated. Please check payment status.');
                }
            } else {
                log_message('error', 'No redirect URL or payment ID found in response: ' . json_encode($response));

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Payment gateway error. Please try again later.'
                    ])->setStatusCode(400);
                } else {
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('error', 'Payment gateway error. Please try again later.');
                }
            }
            
            } catch (\Exception $e) {
                log_message('error', 'Exception in gateway payment processing: ' . $e->getMessage());
                log_message('error', 'Exception trace: ' . $e->getTraceAsString());

                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Payment processing error: ' . $e->getMessage()
                    ])->setStatusCode(500);
                } else {
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('error', 'Payment processing error. Please try again.');
                }
            }
        }
    }

    /**
     * Test endpoint to verify API connectivity and payment processing
     * This is a temporary debugging method that can be removed in production
     */
    public function testPaymentAPI()
    {
        // Check if this is an AJAX request
        $isAjax = $this->request->isAJAX();
        
        try {
            // Test basic connectivity
            $participantId = session()->get('current_participant_id');
            $programId = session()->get('current_program_id');
            
            if (empty($participantId) || empty($programId)) {
                $response = [
                    'status' => 'error',
                    'message' => 'Missing session data',
                    'participant_id' => $participantId,
                    'program_id' => $programId
                ];
            } else {
                // Test API connectivity
                $testResponse = $this->makeGetRequest('/program-payments/program/' . $programId, [], false);
                
                $response = [
                    'status' => 'success',
                    'message' => 'API connectivity test successful',
                    'participant_id' => $participantId,
                    'program_id' => $programId,
                    'api_base_url' => $this->apiBaseUrl,
                    'api_test_response' => $testResponse ? 'success' : 'failed',
                    'usd_in_idr' => $this->data['webSettings']['usd_in_idr'] ?? 'not found'
                ];
            }
            
            if ($isAjax) {
                return $this->response->setJSON($response);
            } else {
                echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT) . '</pre>';
                return;
            }
            
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Test failed with exception: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
            
            if ($isAjax) {
                return $this->response->setJSON($response);
            } else {
                echo '<pre>' . json_encode($response, JSON_PRETTY_PRINT) . '</pre>';
                return;
            }
        }
    }

    /**
     * Test receipt HTML generation without PDF creation
     * This is a debugging method to check if view rendering works correctly
     */
    public function testReceiptHTML($id = null)
    {
        if (empty($id)) {
            return $this->response->setJSON(['error' => 'Payment ID required']);
        }

        try {
            // Get the payment details (same as downloadReceipt but without PDF generation)
            $paymentResponse = $this->makeGetRequest('/payments/' . $id, [], true);
            
            if (empty($paymentResponse)) {
                return $this->response->setJSON(['error' => 'Payment not found']);
            }

            $payment = $paymentResponse['payment'] ?? $paymentResponse;
            $participantId = session()->get('current_participant_id');
            $programId = session()->get('current_program_id');

            // Get program payment details
            $programPaymentId = $payment['program_payment_id'] ?? null;
            if (empty($programPaymentId)) {
                return $this->response->setJSON(['error' => 'Program payment ID not found']);
            }

            $combinedResponse = $this->makeGetRequest('/payments/program-payments/' . $programPaymentId . '/participants/' . $participantId, [], true);
            $programPayment = null;
            
            if ($combinedResponse && isset($combinedResponse['data']['program_payment'])) {
                $programPayment = $combinedResponse['data']['program_payment'];
            } else {
                $programPaymentResponse = $this->makeGetRequest('/program-payments/' . $programPaymentId, [], false);
                $programPayment = $programPaymentResponse;
            }
            
            if (!$programPayment) {
                return $this->response->setJSON(['error' => 'Program payment not found']);
            }

            // Get other required data
            $participantData = $this->makeGetRequest('/participants/' . $participantId, [], true);
            $participant = $participantData['participant'] ?? $participantData ?? [];
            
            $programApiResponse = $this->makeGetRequest('/programs/' . $programId, [], false);
            $paymentMethods = $this->makeGetRequest('/payment-methods/program/' . $programId, [], false);

            // Handle program data structure (same logic as downloadReceipt)
            $programData = null;
            
            if (!empty($programApiResponse)) {
                if (isset($programApiResponse['name'])) {
                    $programData = $programApiResponse;
                } elseif (isset($programApiResponse['data']) && isset($programApiResponse['data']['name'])) {
                    $programData = $programApiResponse['data'];
                } elseif (is_array($programApiResponse) && count($programApiResponse) > 0) {
                    $firstKey = array_keys($programApiResponse)[0];
                    if (is_numeric($firstKey)) {
                        $programData = $programApiResponse[0] ?? null;
                    } else {
                        $programData = $programApiResponse;
                    }
                }
            }
            
            if (empty($programData) || !isset($programData['name'])) {
                $programData = [
                    'name' => 'Youth Break the Boundaries',
                    'email' => null,
                    'logo_url' => null
                ];
            }

            // Find payment method
            $paymentMethod = null;
            if (isset($payment['payment_method_id'])) {
                foreach ($paymentMethods as $method) {
                    if ($method['id'] == $payment['payment_method_id']) {
                        $paymentMethod = $method;
                        break;
                    }
                }
            }

            // Prepare data for the view (same structure as downloadReceipt)
            $data = [
                'payment' => $payment,
                'programPayment' => [
                    'name' => $programPayment['name'] ?? 'Program Payment',
                    'type' => $programPayment['category'] ?? 'Payment',
                    'amount' => $programPayment['usd_amount'] ?? 0,
                    'id' => $programPayment['id'] ?? null,
                ],
                'participant' => $participant,
                'program' => $programData,
                'paymentMethod' => $paymentMethod ?: ['name' => 'Payment Method'],
                'webSettings' => $this->data['webSettings'] ?? [],
                'use_local_resources' => true,
                'disable_qr' => true,
            ];

            // Test view rendering
            $html = view('participant/payment/new-receipt', $data);
            
            // Return HTML for inspection
            return $this->response->setHeader('Content-Type', 'text/html')->setBody($html);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Error testing receipt HTML',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Debug method to test program API response structure
     */
    public function debugProgramAPI($programId = null)
    {
        if (empty($programId)) {
            $programId = session()->get('current_program_id');
        }
        
        if (empty($programId)) {
            return $this->response->setJSON(['error' => 'No program ID provided or found in session']);
        }
        
        // Make the same API call as in downloadReceipt
        $program = $this->makeGetRequest('/programs/' . $programId, [], false);
        
        $debug_info = [
            'program_id' => $programId,
            'api_url' => $this->apiBaseUrl . '/programs/' . $programId,
            'raw_response' => $program,
            'is_array' => is_array($program),
            'has_data_key' => isset($program['data']),
            'has_name_key' => isset($program['name']),
            'program_name' => $program['name'] ?? 'NOT FOUND',
            'data_program_name' => $program['data']['name'] ?? 'NOT FOUND IN DATA',
            'response_keys' => is_array($program) ? array_keys($program) : 'NOT ARRAY',
            'empty_check' => empty($program),
            'empty_name_check' => empty($program['name'] ?? null),
        ];
        
        if (isset($program['data']) && is_array($program['data'])) {
            $debug_info['data_keys'] = array_keys($program['data']);
        }
        
        return $this->response->setJSON($debug_info);
    }

    /**
     * Download programPayment receipt for a completed programPayment attempt
     * 
     * @param int $id The programPayment attempt ID
     */
    public function downloadReceipt($id)
    {
        try {
            log_message('error', '=== RECEIPT GENERATION START ===');
            log_message('error', 'Request method: ' . $this->request->getMethod());
            log_message('error', 'Request URI: ' . $this->request->getUri());
            log_message('error', 'Payment ID received: ' . $id);
            
            if (empty($id) || !is_numeric($id)) {
                log_message('error', 'Invalid payment ID provided: ' . $id);
                return redirect()->to(base_url('payments'))->with('error', 'Invalid payment ID specified.');
            }

            // Log payment ID for debugging
            log_message('info', 'Generating receipt for payment ID: ' . $id);
            log_message('error', 'Payment ID type: ' . gettype($id) . ', value: ' . $id);

            // Ensure session data is available
            $participantId = session()->get('current_participant_id');
            $programId = session()->get('current_program_id');
            
            if (empty($participantId) || empty($programId)) {
                log_message('error', 'Missing session data - Participant ID: ' . ($participantId ?? 'null') . ', Program ID: ' . ($programId ?? 'null'));
                return redirect()->to(base_url('login'))->with('error', 'Session expired. Please login again.');
            }

            // Get the payment details
            $paymentResponse = $this->makeGetRequest('/payments/' . $id, [], true);
            log_message('error', 'Payment API response status: ' . (empty($paymentResponse) ? 'EMPTY' : 'SUCCESS'));
            
            if (empty($paymentResponse)) {
                log_message('error', 'Payment not found with ID: ' . $id);
                return redirect()->back()->with('error', 'Payment not found.');
            }

            // Extract the payment data from the response
            $payment = $paymentResponse['payment'] ?? $paymentResponse;
            log_message('info', 'Payment found: ' . json_encode($payment));

            // Get participant ID from session (needed for combined endpoint)
            $participantId = session()->get('current_participant_id');

            // Get the program payment details using the combined endpoint for better efficiency and access control
            $programPaymentId = $payment['program_payment_id'] ?? null;
            if (empty($programPaymentId)) {
                log_message('error', 'Program payment ID not found in payment: ' . json_encode($payment));
                return redirect()->back()->with('error', 'Unable to find associated program payment.');
            }

            // Use combined endpoint to get program payment details and verify access
            $combinedResponse = $this->makeGetRequest('/payments/program-payments/' . $programPaymentId . '/participants/' . $participantId, [], true);
            $programPayment = null;
            
            if ($combinedResponse && isset($combinedResponse['data']['program_payment'])) {
                $programPayment = $combinedResponse['data']['program_payment'];
                log_message('info', 'Program payment found via combined endpoint: ' . json_encode($programPayment));
            } else {
                // Fallback to individual endpoint if combined endpoint fails
                log_message('info', 'Combined endpoint failed, trying individual endpoint for program payment: ' . $programPaymentId);
                $programPaymentResponse = $this->makeGetRequest('/program-payments/' . $programPaymentId, [], false);
                $programPayment = $programPaymentResponse; // Fix: Don't look for nested 'data' field
                log_message('info', 'Individual endpoint response: ' . json_encode($programPaymentResponse));
            }
            
            if (!$programPayment) {
                log_message('error', 'Program payment not found: ' . $programPaymentId);
                return redirect()->back()->with('error', 'Unable to find associated program payment details.');
            }
            
            log_message('info', 'Program payment found: ' . json_encode($programPayment));

            // Get participant details
            $participantData = $this->makeGetRequest('/participants/' . $participantId, [], true);
            // Extract participant data from the response structure
            $participant = $participantData['participant'] ?? $participantData ?? [];
            $participantCategory = $participant['category'] ?? 'self_funded'; // Default to self_funded if not found
            
            log_message('error', 'AUTHORIZATION CHECK: Participant ' . $participantId . ' has category: ' . $participantCategory);
            log_message('error', 'AUTHORIZATION CHECK: Payment ' . $programPaymentId . ' has type: ' . ($programPayment['type'] ?? 'all'));
            
            // Check if participant has access to this payment based on their category
            $paymentType = $programPayment['type'] ?? 'all';
            $hasAccess = ($paymentType === 'all') || ($paymentType === $participantCategory);
            
            log_message('error', 'AUTHORIZATION CHECK: Access granted: ' . ($hasAccess ? 'YES' : 'NO'));
            
            if (!$hasAccess) {
                log_message('warning', 'Unauthorized receipt download attempt: Participant ' . $participantId . ' (' . $participantCategory . ') tried to download receipt for ' . $paymentType . ' payment ' . $programPaymentId);
                return redirect()->back()->with('error', 'You do not have access to this payment receipt.');
            }
            
            // Use the participant data we already extracted for authorization
            log_message('info', 'Participant found: ' . ($participant ? 'yes' : 'no'));
            log_message('info', 'Participant data structure: ' . json_encode($participant));

            // Get program details
            $programId = session()->get('current_program_id');
            log_message('error', 'Fetching program details for program ID: ' . $programId);
            $programApiResponse = $this->makeGetRequest('/programs/' . $programId, [], false);
            log_message('error', 'Program API raw response: ' . json_encode($programApiResponse));
            
            // Handle program data structure - makeGetRequest can return different structures
            $programData = null;
            
            if (!empty($programApiResponse)) {
                // Case 1: Direct program data with name field
                if (isset($programApiResponse['name'])) {
                    $programData = $programApiResponse;
                    log_message('info', 'Program data found in direct response structure');
                }
                // Case 2: Nested structure with data.name
                elseif (isset($programApiResponse['data']) && isset($programApiResponse['data']['name'])) {
                    $programData = $programApiResponse['data'];
                    log_message('info', 'Program data found in nested response structure');
                }
                // Case 3: Structure with 'program' field (like the actual API response)
                elseif (isset($programApiResponse['program']) && isset($programApiResponse['program']['name'])) {
                    $programData = $programApiResponse['program'];
                    log_message('info', 'Program data found in program field structure');
                }
                // Case 4: Check if the response itself is the program data
                elseif (is_array($programApiResponse) && count($programApiResponse) > 0) {
                    // Sometimes the API returns the program data directly without 'data' wrapper
                    $firstKey = array_keys($programApiResponse)[0];
                    if (is_numeric($firstKey)) {
                        // It's an array of programs, take the first one
                        $programData = $programApiResponse[0] ?? null;
                        log_message('info', 'Program data found in array format (taking first)');
                    } else {
                        // It's a single program object
                        $programData = $programApiResponse;
                        log_message('info', 'Program data found in direct object format');
                    }
                }
            }
            
            // Fallback if no valid program data found
            if (!is_array($programData) || !isset($programData['name']) || trim($programData['name']) === '') {
                log_message('error', 'No valid program data found, using fallback. Conditions: is_array=' . (is_array($programData) ? 'YES' : 'NO') . ', name isset=' . (isset($programData['name']) ? 'YES' : 'NO') . ', name value="' . ($programData['name'] ?? 'NULL') . '". Raw response was: ' . json_encode($programApiResponse));
                $programData = [
                    'name' => 'Youth Break the Boundaries',
                    'email' => null,
                    'logo_url' => null
                ];
            }
            
            // Enhance program data with logo from webSettings if not present
            if (empty($programData['logo_url']) && isset($this->data['webSettings']['logo_url'])) {
                $programData['logo_url'] = $this->data['webSettings']['logo_url'];
                log_message('info', 'Added logo URL from webSettings: ' . $this->data['webSettings']['logo_url']);
            }
            
            // Enhance program data with email if not present
            if (empty($programData['email']) && isset($this->data['webSettings']['email'])) {
                $programData['email'] = $this->data['webSettings']['email'];
                log_message('info', 'Added email from webSettings: ' . $this->data['webSettings']['email']);
            }
            
            log_message('info', 'Final program data for template: ' . json_encode($programData));
            log_message('info', 'Program name that will be used: ' . ($programData['name'] ?? 'MISSING'));
            
            // CRITICAL DEBUG: Force log exactly what we're passing to template
            log_message('error', '=== TEMPLATE DATA DEBUG ===');
            log_message('error', 'Program data being passed to template: ' . json_encode($programData));
            log_message('error', 'Program name: ' . ($programData['name'] ?? 'NULL'));
            log_message('error', 'WebSettings in data array: ' . json_encode($this->data['webSettings'] ?? 'NULL'));
            log_message('error', '=== END TEMPLATE DATA DEBUG ===');
            
            // Debug webSettings for logo
            log_message('error', 'WebSettings data: ' . json_encode($this->data['webSettings'] ?? 'NULL'));
            if (isset($this->data['webSettings']['logo_url'])) {
                log_message('error', 'Logo URL from webSettings: ' . $this->data['webSettings']['logo_url']);
            }

            // get payment methods
            $paymentMethods = $this->makeGetRequest('/payment-methods/program/' . $programId, [], false);

            log_message('info', 'Payment methods found: ' . json_encode($paymentMethods));

            // get payment method by payment method id
            $paymentMethod = null;

            if (isset($payment['payment_method_id'])) {
                foreach ($paymentMethods as $method) {
                    if ($method['id'] == $payment['payment_method_id']) {
                        $paymentMethod = $method;
                        break;
                    }
                }
            }

            log_message('info', 'Payment method found: ' . json_encode($paymentMethod));
            
            // Ensure participant data is properly structured for the template
            // The API can return either direct participant data OR nested data.participant structure
            if (isset($participant['data']['participant'])) {
                // Nested structure from some API responses
                $participantData = $participant['data']['participant'];
            } elseif (isset($participant['data']) && is_array($participant['data']) && isset($participant['data']['full_name'])) {
                // Data is in 'data' key but not nested under 'participant'
                $participantData = $participant['data'];
            } elseif (isset($participant['full_name'])) {
                // Direct structure
                $participantData = $participant;
            } else {
                // Fallback - use the participant as-is and let template handle missing keys
                $participantData = $participant;
            }
            
            log_message('info', 'Template data structure - participant keys: ' . (is_array($participantData) ? implode(', ', array_keys($participantData)) : 'not an array'));
            log_message('info', 'Template data structure - participant full_name: ' . ($participantData['full_name'] ?? 'NOT FOUND'));
            
            // Prepare data for the view
            $data = [
                'payment' => $payment,
                'programPayment' => [
                    'name' => $programPayment['name'] ?? 'Program Payment',
                    'type' => $programPayment['category'] ?? 'Payment',
                    'amount' => $programPayment['usd_amount'] ?? 0,
                    'id' => $programPayment['id'] ?? null,
                ],
                'participant' => $participantData,
                'program' => $programData, // Use the processed program data
                'paymentMethod' => $paymentMethod,
                'webSettings' => $this->data['webSettings'] ?? [],
            ];

            // CRITICAL DEBUG: Log the exact data being passed to template
            log_message('error', '=== FINAL TEMPLATE DATA ===');
            log_message('error', 'data[program]: ' . json_encode($data['program']));
            log_message('error', 'data[webSettings]: ' . json_encode($data['webSettings']));
            log_message('error', '=== END FINAL TEMPLATE DATA ===');

            // Ensure all required data is available for template
            if (!isset($data['program']) || !is_array($data['program']) || !isset($data['program']['name']) || trim($data['program']['name']) === '') {
                log_message('error', 'FALLBACK TRIGGERED - Conditions: program isset=' . (isset($data['program']) ? 'YES' : 'NO') . ', is_array=' . (is_array($data['program']) ? 'YES' : 'NO') . ', name isset=' . (isset($data['program']['name']) ? 'YES' : 'NO') . ', name value="' . ($data['program']['name'] ?? 'NULL') . '"');
                $data['program'] = ['name' => 'Youth Break the Boundaries'];
                log_message('warning', 'Program data was empty, using fallback name');
                log_message('error', 'FALLBACK TRIGGERED - data[program] after fallback: ' . json_encode($data['program']));
            } else {
                log_message('error', 'NO FALLBACK NEEDED - Program data is valid: ' . json_encode($data['program']));
            }
            if (empty($data['paymentMethod'])) {
                $data['paymentMethod'] = ['name' => 'Payment Method'];
            }

            log_message('info', 'Final template data - Program name: ' . ($data['program']['name'] ?? 'MISSING'));
            log_message('debug', 'Complete data prepared for PDF generation: ' . json_encode($data));

            // Make sure DOMPDF is available
            if (!class_exists('\Dompdf\Dompdf')) {
                log_message('error', 'DOMPDF library not found. Please install it using composer.');
                return redirect()->back()->with('error', 'PDF generation library not found. Please contact support.');
            }

            log_message('error', 'DOMPDF class found - proceeding with PDF generation');

            // Set execution time limit for PDF generation
            ini_set('max_execution_time', 120); // 2 minutes
            set_time_limit(120);
            log_message('error', 'Execution time limit set to 120 seconds');

            // Generate PDF with optimized settings for reliability
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();
            // Enable remote resources for logos, but with timeout
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);
            $options->set('debugLayout', false);
            $options->set('logOutputFile', WRITEPATH . 'logs/dompdf.log');

            // Optimize memory usage
            $options->set('chroot', FCPATH);
            $dompdf->setOptions($options);
            log_message('error', 'DOMPDF options configured with remote resources enabled for logos');

            // Flag for the view to allow external resources
            $data['use_local_resources'] = false;
            $data['disable_qr'] = true; // Still disable QR code generation

            // Load the receipt view into the PDF
            log_message('info', 'Rendering receipt view');
            log_message('error', 'About to render receipt view with data: ' . json_encode(array_keys($data)));
            
            try {
                $html = view('participant/payment/new-receipt', $data);
            } catch (\Exception $viewException) {
                log_message('error', 'View rendering failed: ' . $viewException->getMessage());
                throw new \Exception('Failed to render receipt view: ' . $viewException->getMessage());
            }
            
            if (empty($html) || strlen($html) < 100) {
                log_message('error', 'Receipt view produced minimal output: ' . strlen($html) . ' chars');
                throw new \Exception('Receipt view generated insufficient content');
            }
            
            log_message('error', 'Receipt view rendered successfully. HTML length: ' . strlen($html));
            log_message('debug', 'HTML preview (first 500 chars): ' . substr($html, 0, 500));
            
            $dompdf->loadHtml($html);
            log_message('error', 'HTML loaded into DOMPDF');

            // Set paper size and orientation (A4 is too large, use something smaller)
            $dompdf->setPaper('letter', 'portrait');
            log_message('error', 'Paper size set to letter portrait');

            // Render the PDF - with memory limit management
            $currentMemoryLimit = ini_get('memory_limit');
            log_message('error', 'Current memory limit: ' . $currentMemoryLimit);
            
            // Temporarily increase memory limit if needed
            if ((int) $currentMemoryLimit < 256) {
                ini_set('memory_limit', '256M');
                log_message('error', 'Memory limit increased to 256M');
            }

            log_message('info', 'Rendering PDF - starting');
            log_message('error', '=== STARTING PDF RENDER ===');
            
            try {
                $dompdf->render();
            } catch (\Exception $renderException) {
                log_message('error', 'PDF rendering failed: ' . $renderException->getMessage());
                throw new \Exception('PDF rendering failed: ' . $renderException->getMessage());
            }
            
            log_message('error', '=== PDF RENDER COMPLETED ===');
            log_message('info', 'Rendering PDF - completed');

            // Generate a filename
            $transactionCode = $payment['transaction_code'] ?? ('YBB-' . $id);
            $fileName = 'Receipt_' . preg_replace('/[^a-zA-Z0-9\-_]/', '', $transactionCode) . '.pdf';
            log_message('info', 'Streaming PDF to browser: ' . $fileName);
            log_message('error', 'Generated filename: ' . $fileName);

            // Reset memory limit
            ini_set('memory_limit', $currentMemoryLimit);
            log_message('error', 'Memory limit reset to: ' . $currentMemoryLimit);

            // Get the PDF content
            $pdfContent = $dompdf->output();
            
            if (empty($pdfContent)) {
                throw new \Exception('PDF generation produced no content');
            }
            
            log_message('error', 'PDF content generated. Size: ' . strlen($pdfContent) . ' bytes');
            
            // Check if content is actually PDF
            $isPdf = (substr($pdfContent, 0, 4) === '%PDF');
            log_message('error', 'Content starts with PDF header: ' . ($isPdf ? 'YES' : 'NO'));
            
            if (!$isPdf) {
                log_message('error', 'Invalid PDF content. Preview (first 200 chars): ' . substr($pdfContent, 0, 200));
                throw new \Exception('Generated content is not a valid PDF');
            }

            // Clear any output buffers to prevent corruption
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Set the appropriate headers
            $response = service('response');
            log_message('error', 'Setting response headers for PDF download');
            
            $response->setHeader('Content-Type', 'application/pdf');
            $response->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->setHeader('Pragma', 'no-cache');
            $response->setHeader('Expires', '0');
            $response->setHeader('Content-Length', strlen($pdfContent));
            $response->setHeader('Accept-Ranges', 'none');
            
            log_message('error', 'Response headers set for PDF download');

            // Output the PDF content directly
            log_message('error', '=== SENDING PDF RESPONSE ===');
            return $response->setBody($pdfContent);
        } catch (\Exception $e) {
            // Log the error for debugging
            log_message('error', '=== RECEIPT GENERATION EXCEPTION ===');
            log_message('error', 'Exception type: ' . get_class($e));
            log_message('error', 'Error generating receipt: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            log_message('error', 'File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error generating receipt: ' . $e->getMessage()
                ])->setStatusCode(500);
            }

            return redirect()->back()->with('error', 'Error generating receipt: ' . $e->getMessage());
        }
    }

    /**
     * Get all payments for a specific participant (API endpoint)
     * GET /payments/participant/{participant_id}
     */
    public function getParticipantPayments($participantId)
    {
        try {
            // Check if user is logged in
            $user = session()->get('user');
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ]);
            }

            // Get payments for the specified participant
            $paymentsResponse = $this->makeGetRequest('/payments/participant/' . $participantId, [], false);

            if (!$paymentsResponse) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Payments not found for participant'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payments retrieved successfully',
                'data' => [
                    'payments' => $paymentsResponse['payments'] ?? [],
                    'participant_id' => $participantId,
                    'timestamp' => time(),
                    'cache_disabled' => true
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get participant payments error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve participant payments'
            ]);
        }
    }

    /**
     * Get payments by program payment type for a specific participant (API endpoint)
     * GET /payments/program-payment/{program_payment_id}/participant/{participant_id}
     */
    public function getPaymentsByProgramPayment($programPaymentId, $participantId)
    {
        try {
            // Check if user is logged in
            $user = session()->get('user');
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ]);
            }

            // Get payments for the specified program payment and participant
            $paymentsResponse = $this->makeGetRequest('/payments/program-payment/' . $programPaymentId . '/participant/' . $participantId, [], false);

            if (!$paymentsResponse) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Payments not found for specified criteria'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payments retrieved successfully',
                'data' => $paymentsResponse
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get payments by program payment error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve payments'
            ]);
        }
    }

    /**
     * Get single payment details (API endpoint)
     * GET /payments/get/{payment_id}
     */
    public function getPaymentDetails($paymentId)
    {
        try {
            // Check if user is logged in
            $user = session()->get('user');
            if (!$user) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ]);
            }

            // Get payment details
            $paymentResponse = $this->makeGetRequest('/payments/get/' . $paymentId, [], false);

            if (!$paymentResponse) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Payment not found'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payment details retrieved successfully',
                'data' => $paymentResponse
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get payment details error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve payment details'
            ]);
        }
    }
}
