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
                // Show if participant has attempted payment OR if within date range OR if coming soon
                if ($hasTried || $isWithinDateRange || $isComingSoon) {
                    $registrationPayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding registration payment ID {$paymentId} - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", inRange: " . ($isWithinDateRange ? 'YES' : 'NO') . ", comingSoon: " . ($isComingSoon ? 'YES' : 'NO') . " (days to start: " . round($daysToStart) . ")");
                }
            }
        }
        
        // Add all registration payments to visible payments
        $visiblePayments = array_merge($visiblePayments, $registrationPayments);

        $completed['registration'] = $registrationDone;

        // Handle program_fee_1 - show if registration is done OR if participant has attempted this payment OR if close to start date
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

                if ($isPaid) {
                    $completed['program_fee_1'] = true;
                }

                // Show if participant has attempted payment OR if within date range OR if coming soon
                if ($hasTried || $isWithinDateRange || $isComingSoon) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_1 payment ID {$paymentId} - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", inRange: " . ($isWithinDateRange ? 'YES' : 'NO') . ", comingSoon: " . ($isComingSoon ? 'YES' : 'NO') . " (days to start: " . round($daysToStart) . ")");
                }
            }
        } else {
            // Even if registration is not complete, show program_fee_1 if participant has attempted it
            foreach ($byCategory['program_fee_1'] as $payment) {
                $paymentId = $payment['id'];
                $hasTried = $hasAnyAttempt($paymentId);
                $isPaid = $isCompleted($paymentId);

                if ($isPaid) {
                    $completed['program_fee_1'] = true;
                }

                if ($hasTried) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_1 payment ID {$paymentId} due to previous attempt");
                }
            }
        }

        // Handle program_fee_2 - show if program_fee_1 is done OR if participant has attempted this payment OR if close to start date
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

                if ($isPaid) {
                    $completed['program_fee_2'] = true;
                }

                // Show if participant has attempted payment OR if within date range OR if coming soon
                if ($hasTried || $isWithinDateRange || $isComingSoon) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_2 payment ID {$paymentId} - hasTried: " . ($hasTried ? 'YES' : 'NO') . ", inRange: " . ($isWithinDateRange ? 'YES' : 'NO') . ", comingSoon: " . ($isComingSoon ? 'YES' : 'NO') . " (days to start: " . round($daysToStart) . ")");
                }
            }
        } else {
            // Even if program_fee_1 is not complete, show program_fee_2 if participant has attempted it
            foreach ($byCategory['program_fee_2'] as $payment) {
                $paymentId = $payment['id'];
                $hasTried = $hasAnyAttempt($paymentId);
                $isPaid = $isCompleted($paymentId);

                if ($isPaid) {
                    $completed['program_fee_2'] = true;
                }

                if ($hasTried) {
                    $visiblePayments[] = $payment;
                    log_message('debug', "getVisibleProgramPayments: Adding program_fee_2 payment ID {$paymentId} due to previous attempt");
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
     * Download programPayment receipt for a completed programPayment attempt
     * 
     * @param int $id The programPayment attempt ID
     */
    public function downloadReceipt($id)
    {
        try {
            if (empty($id)) {
                return redirect()->back()->with('error', 'Invalid payment ID specified.');
            }

            // Log payment ID for debugging
            log_message('info', 'Generating receipt for payment ID: ' . $id);

            // Get the payment details
        $payment = $this->makeGetRequest('/payments/get/' . $id, [], true);
            if (empty($payment)) {
                log_message('error', 'Payment not found with ID: ' . $id);
                return redirect()->back()->with('error', 'Payment not found.');
            }

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
            } else {
                // Fallback to individual endpoint if combined endpoint fails
                $programPaymentResponse = $this->makeGetRequest('/program-payments/' . $programPaymentId, [], false);
                $programPayment = $programPaymentResponse['data'] ?? null;
            }
            
            if (!$programPayment) {
                log_message('error', 'Program payment not found: ' . $programPaymentId);
                return redirect()->back()->with('error', 'Unable to find associated program payment details.');
            }
            
            log_message('info', 'Program payment found: ' . json_encode($programPayment));

            // Get participant details
            $participantData = $this->makeGetRequest('/participants/' . $participantId, [], true);
            $participantCategory = $participantData['category'] ?? 'self_funded'; // Default to self_funded if not found
            
            // Check if participant has access to this payment based on their category
            $paymentType = $programPayment['type'] ?? 'all';
            $hasAccess = ($paymentType === 'all') || ($paymentType === $participantCategory);
            
            if (!$hasAccess) {
                log_message('warning', 'Unauthorized receipt download attempt: Participant ' . $participantId . ' (' . $participantCategory . ') tried to download receipt for ' . $paymentType . ' payment ' . $programPaymentId);
                return redirect()->back()->with('error', 'You do not have access to this payment receipt.');
            }
            $participant = $this->makeGetRequest('/participants/' . $participantId, [], false);
            log_message('info', 'Participant found: ' . ($participant ? 'yes' : 'no'));

            // Get program details
            $programId = session()->get('current_program_id');
            $program = $this->makeGetRequest('/programs/' . $programId, [], false);

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

            log_message('info', 'Payment method found: ' . json_encode($paymentMethod));            // Prepare data for the view
            $data = [
                'payment' => $payment,
                'programPayment' => $programPayment,
                'participant' => $participant,
                'program' => $program,
                'paymentMethod' => $paymentMethod,
                'webSettings' => $this->data['webSettings'] ?? null,
            ];

            // Format the data to match the template's expected structure
            if (isset($programPayment)) {
                $data['programPayment'] = [
                    'name' => $programPayment['name'] ?? 'Program Payment',
                    'type' => $programPayment['category'] ?? 'Payment',
                    'amount' => $programPayment['usd_amount'] ?? 0,
                ];
            }

            log_message('info', 'Data prepared for PDF generation: ' . json_encode($data));

            // Make sure DOMPDF is available
            if (!class_exists('\Dompdf\Dompdf')) {
                log_message('error', 'DOMPDF library not found. Please install it using composer.');
                return redirect()->back()->with('error', 'PDF generation library not found. Please contact support.');
            }

            // Make sure QrCodeHelper is loaded
            helper('QrCodeHelper');
            // Set higher execution time limit for PDF generation
            ini_set('max_execution_time', 180); // 3 minutes
            set_time_limit(180);            // Generate PDF with optimized settings
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true); // Enable loading external images
            $options->set('defaultFont', 'Arial');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('debugKeepTemp', false);
            $options->set('debugCss', false);
            $options->set('debugLayout', false);

            // Optimize memory usage
            $options->set('chroot', FCPATH);
            $dompdf->setOptions($options);

            // Replace any external image references with local ones or placeholders
            $data['use_local_resources'] = true; // Flag for the view to use local resources

            // Load the receipt view into the PDF
            log_message('info', 'Rendering receipt view');
            $html = view('participant/payment/new-receipt', $data);
            $dompdf->loadHtml($html);

            // Set paper size and orientation (A4 is too large, use something smaller)
            $dompdf->setPaper('letter', 'portrait');

            // Render the PDF - with memory limit management
            $currentMemoryLimit = ini_get('memory_limit');
            // Temporarily increase memory limit if needed
            if ((int) $currentMemoryLimit < 256) {
                ini_set('memory_limit', '256M');
            }

            log_message('info', 'Rendering PDF - starting');
            $dompdf->render();
            log_message('info', 'Rendering PDF - completed');

            // Generate a filename
            $fileName = 'Receipt_' . ($payment['transaction_code'] ?? 'YBB-' . $id) . '.pdf';
            log_message('info', 'Streaming PDF to browser: ' . $fileName);

            // Reset memory limit
            ini_set('memory_limit', $currentMemoryLimit);

            // Get the PDF content
            $pdfContent = $dompdf->output();

            // Set the appropriate headers
            $response = service('response');
            $response->setHeader('Content-Type', 'application/pdf');
            $response->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->setHeader('Cache-Control', 'no-store');
            $response->setHeader('Content-Length', strlen($pdfContent));

            // Output the PDF content directly
            return $response->setBody($pdfContent);
        } catch (\Exception $e) {
            // Log the error for debugging
            log_message('error', 'Error generating receipt: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()->with('error', 'Error generating receipt: ' . $e->getMessage());
        }
    }
}
