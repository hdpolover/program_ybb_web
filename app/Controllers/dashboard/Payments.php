<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Payments extends BaseController
{
    public function getVisibleProgramPayments($allPayments, $participantPayments)
    {
        $today = date('Y-m-d H:i:s');

        // Group participant payments by program_payment_id
        $participantPaymentMap = [];
        foreach ($participantPayments as $pmt) {
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
            $byCategory[$payment['category']][] = $payment;
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
                }
            } else {
                // No successful registration yet
                // Show if participant has attempted payment OR if within date range
                if ($hasTried || $isWithinDateRange) {
                    $registrationPayments[] = $payment;
                }
            }
        }
        
        // Add all registration payments to visible payments
        $visiblePayments = array_merge($visiblePayments, $registrationPayments);

        $completed['registration'] = $registrationDone;

        // Handle program_fee_1 - show if registration is done OR if participant has attempted this payment
        if ($completed['registration']) {
            foreach ($byCategory['program_fee_1'] as $payment) {
                $paymentId = $payment['id'];
                $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
                $isPaid = $isCompleted($paymentId);
                $hasTried = $hasAnyAttempt($paymentId);

                if ($isPaid) {
                    $completed['program_fee_1'] = true;
                }

                // Show if participant has attempted payment OR if within date range
                if ($hasTried || $isWithinDateRange) {
                    $visiblePayments[] = $payment;
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
                }
            }
        }

        // Handle program_fee_2 - show if program_fee_1 is done OR if participant has attempted this payment
        if ($completed['program_fee_1']) {
            foreach ($byCategory['program_fee_2'] as $payment) {
                $paymentId = $payment['id'];
                $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
                $isPaid = $isCompleted($paymentId);
                $hasTried = $hasAnyAttempt($paymentId);

                if ($isPaid) {
                    $completed['program_fee_2'] = true;
                }

                // Show if participant has attempted payment OR if within date range
                if ($hasTried || $isWithinDateRange) {
                    $visiblePayments[] = $payment;
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
                }
            }
        }

        // Remove duplicates just in case
        $visiblePayments = array_unique($visiblePayments, SORT_REGULAR);

        // Sort all by start date for final display
        usort($visiblePayments, function ($a, $b) {
            return strtotime($a['start_date']) - strtotime($b['start_date']);
        });

        return array_values($visiblePayments);
    }

    /**
     * Display the list of program payments required from the participant
     */
    public function index()
    {
        $programPayments = $this->makeGetRequest('/program-payments/program/' . session()->get('current_program_id'), [], false);
        $participantPayments = $this->makeGetRequest('/payments/participants/' . session()->get('current_participant_id'), [], false);
        $paymentMethods = $this->makeGetRequest('/payment-methods/program/' . session()->get('current_program_id'), [], false);        // Safety check - if not participant payments, skip loop

        // Store original participant payments for visibility logic
        $originalParticipantPayments = $participantPayments ?? [];
        
        if (empty($participantPayments)) {
            $participantPayments = [];
        } else {
            // Convert participant payments to a more usable format, prioritizing successful payments
            $organizedPayments = [];

            // First, group all payments by program_payment_id
            foreach ($participantPayments as $payment) {
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

        $data = [
            'title' => 'Payments',
            'programPayments' => $programPayments,
            'participantPayments' => $participantPayments,
            'paymentMethods' => $paymentMethods,
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

        // Fetch programPayment details from API
        $paymentDetails = $this->makeGetRequest('/payments/program-payment/' . $id . '/participant/' . $participantId, [], false);

        if (empty($paymentDetails)) {
            session()->setFlashdata('error', 'Program payment not found.');
            return redirect()->to(base_url('payments'));
        }

        $programPayment = $paymentDetails['program_payment'] ?? null;
        if (empty($programPayment)) {
            session()->setFlashdata('error', 'Program payment details not found.');
            return redirect()->to(base_url('payments'));
        }

        // Fetch programPayment attempts for the participant
        $payments = $paymentDetails['payments'] ?? [];

        if (empty($payments)) {
            $payments = [];
        } else {
            // Convert payments to a more usable format
            $payments = array_column($payments, null, 'id');
        }

        // get payment methods
        $paymentMethods = $this->makeGetRequest('/payment-methods/program/' . $programId, [], false);

        $data = [
            'title' => 'Payment Details',
            'programPayment' => $programPayment,
            'payments' => $payments,
            'paymentMethods' => $paymentMethods,
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

        // Get the program payment ID to validate amount
        $programPaymentId = $inputs['program_payment_id'] ?? null;
        if (empty($programPaymentId)) {
            return redirect()->to(base_url('payments'))->with('error', 'Program payment ID is required.');
        }

        // Fetch the program payment to validate its amount
        $programPayment = $this->makeGetRequest('/program-payments/' . $programPaymentId, [], false);
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

                // Set cURL options
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json'
                    ]
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
                    false, // Use JWT if needed
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
                    ]);
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
                    ]);
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

            // Make API call to initiate gateway payment           
            $response = $this->makePostRequest(
                '/payments/create',
                $paymentData,
                [], // No additional headers
                false, // Use JWT if needed
                true, // Send as JSON
                false // Not multipart
            );

            if (!$response) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Failed to initiate payment. Please try again or contact support.'
                    ]);
                } else {
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('error', 'Failed to initiate payment. Please try again or contact support.');
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
                    ]);
                } else {
                    return redirect()->to(base_url('payments/detail/' . $programPaymentId))
                        ->with('error', 'Payment gateway error. Please try again later.');
                }
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
            $payment = $this->makeGetRequest('/payments/' . $id, [], false);
            if (empty($payment)) {
                log_message('error', 'Payment not found with ID: ' . $id);
                return redirect()->back()->with('error', 'Payment not found.');
            }

            log_message('info', 'Payment found: ' . json_encode($payment));

            // Get the program payment details
            $programPaymentId = $payment['program_payment_id'] ?? null;
            if (empty($programPaymentId)) {
                log_message('error', 'Program payment ID not found in payment: ' . json_encode($payment));
                return redirect()->back()->with('error', 'Unable to find associated program payment.');
            }

            $programPayment = $this->makeGetRequest('/program-payments/' . $programPaymentId, [], false);
            log_message('info', 'Program payment found: ' . json_encode($programPayment));

            // Get participant details
            $participantId = session()->get('current_participant_id');
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
