<?php

namespace App\Controllers;

class Auth extends BaseController
{

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        // get program category id from web settings (access from controller data)
        $categoryId = $this->data['webSettings']['program_category_id'] ?? null;
        log_message('debug', 'No program slug provided, using category ID from settings: ' . ($categoryId ?? 'not found'));

        // get programs by category id
        $programs = $this->makeGetRequest('/programs/category/' . $categoryId, [], true);

        // DEBUG: Log the actual API response
        log_message('debug', 'API Response for programs/category/' . $categoryId . ': ' . json_encode($programs));
        log_message('debug', 'Programs array type: ' . gettype($programs));
        log_message('debug', 'Programs array count: ' . (is_array($programs) ? count($programs) : 'not array'));

        // check if any program is_registration_open = 1 OR has active registration payment options
        $isRegistrationOpen = false;        // loop through programs to check if any program is open for registration
        if (is_array($programs) && !empty($programs)) {
            log_message('debug', 'Processing ' . count($programs) . ' programs for registration status');
            foreach ($programs as $program) {
                log_message('debug', 'Checking program: ' . json_encode($program));
                
                // Check traditional registration open flag
                if (isset($program['is_registration_open']) && $program['is_registration_open'] == '1') {
                    $isRegistrationOpen = true;
                    log_message('debug', 'Registration open via is_registration_open flag for program: ' . ($program['name'] ?? 'unknown'));
                    break; // Exit loop if any program is open for registration
                }
                
                // Check if any registration payment options are currently available
                if (isset($program['registration_payments']) && !empty($program['registration_payments'])) {
                    $currentDate = new \DateTime();
                    $registrationPayments = $program['registration_payments'];
                    
                    // Check self_funded option
                    if (isset($registrationPayments['self_funded'])) {
                        $selfFunded = $registrationPayments['self_funded'];
                        $startDate = new \DateTime($selfFunded['start_date']);
                        $endDate = new \DateTime($selfFunded['end_date']);
                        
                        if ($selfFunded['is_available'] && 
                            $selfFunded['is_active'] && 
                            $currentDate >= $startDate && 
                            $currentDate <= $endDate) {
                            $isRegistrationOpen = true;
                            log_message('debug', 'Registration open via self_funded option for program: ' . ($program['name'] ?? 'unknown'));
                            break;
                        }
                    }
                    
                    // Check fully_funded option
                    if (isset($registrationPayments['fully_funded'])) {
                        $fullyFunded = $registrationPayments['fully_funded'];
                        $startDate = new \DateTime($fullyFunded['start_date']);
                        $endDate = new \DateTime($fullyFunded['end_date']);
                        
                        if ($fullyFunded['is_available'] && 
                            $fullyFunded['is_active'] && 
                            $currentDate >= $startDate && 
                            $currentDate <= $endDate) {
                            $isRegistrationOpen = true;
                            log_message('debug', 'Registration open via fully_funded option for program: ' . ($program['name'] ?? 'unknown'));
                            break;
                        }
                    }
                }
            }
        } else {
            log_message('warning', 'No programs found or invalid response from API for category ID: ' . ($categoryId ?? 'null'));
        }

        // log the result
        log_message('debug', 'Is registration open: ' . ($isRegistrationOpen ? 'Yes' : 'No'));

        $data = [
            'title' => 'Sign In',
            'isRegistrationOpen' => $isRegistrationOpen,
        ];

        return $this->render('auth/sign-in', $data);
    }

    // sign up
    public function signUp()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        // get q from query parameter
        $q = $this->request->getGet('q');
        log_message('debug', '===== SIGNUP PROCESS STARTED =====');
        log_message('debug', 'Query parameter q: ' . ($q ?? 'not provided'));
        log_message('debug', 'Current API URL: ' . $this->apiBaseUrl);
        log_message('debug', 'Environment: ' . ENVIRONMENT);
        log_message('debug', 'HTTP Host: ' . ($_SERVER['HTTP_HOST'] ?? 'not set'));
        if ($q) {
            // Log the ambassador query parameter that we're about to validate
            log_message('debug', 'Attempting to validate ambassador query: ' . $q);

            try {
                // check query value
                $endpoint = '/ambassadors/check-query';
                log_message('debug', 'Making API request to ' . $endpoint . ' with query: ' . $q);
                log_message('debug', 'Full API URL: ' . $this->apiBaseUrl . $endpoint);
                log_message('debug', 'Current URL being used: ' . $this->currentUrl);

                // Add a detailed trace of the request for troubleshooting
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                log_message('debug', 'Called from: ' . ($trace[1]['file'] ?? 'unknown') . ' line ' . ($trace[1]['line'] ?? 'unknown'));
                
                // Change from POST to GET with query parameter
                $queryData = $this->makeGetRequest($endpoint . '?encrypted_query=' . urlencode($q), [], false, true);

                // Log the response data
                log_message('debug', 'Ambassador query check response: ' . json_encode($queryData));

                // Check for API errors
                if (isset($queryData['error'])) {
                    log_message('error', 'API error during ambassador validation: ' . ($queryData['message'] ?? 'Unknown error'));

                    // Log more details if available
                    if (isset($queryData['raw_response'])) {
                        log_message('error', 'Raw API response: ' . $queryData['raw_response']);
                    }

                    // Instead of just redirecting with a generic error, let's try a fallback approach
                    try {
                        // Try to validate the ambassador directly using a different endpoint
                        $directEndpoint = '/ambassadors/ref-code-from-query/' . urlencode($q);
                        log_message('debug', 'Attempting fallback with direct query: ' . $directEndpoint);

                        $fallbackData = $this->makeGetRequest($directEndpoint, [], false);

                        if ($fallbackData && isset($fallbackData['ref_code'])) {
                            log_message('debug', 'Fallback succeeded! Found ref_code: ' . $fallbackData['ref_code']);
                            // We found the referral code, we can proceed
                            $queryData = [
                                'is_valid' => true,
                                'ref_code' => $fallbackData['ref_code'],
                                'ambassador' => $fallbackData
                            ];
                        } else {
                            log_message('error', 'Fallback approach failed: ' . json_encode($fallbackData));
                            return redirect()->to('sign-in')->with('error', 'Unable to validate ambassador reference. Please try again or contact support.');
                        }
                    } catch (\Exception $fallbackEx) {
                        log_message('error', 'Fallback exception: ' . $fallbackEx->getMessage());
                        return redirect()->to('sign-in')->with('error', 'Failed to validate ambassador reference. Please try a direct sign-up.');
                    }
                }

                if ($queryData) {
                    log_message('debug', 'Query validation returned data: ' . json_encode($queryData));

                    // check if query is valid
                    if (!isset($queryData['is_valid']) || $queryData['is_valid'] !== true) {
                        log_message('error', 'Query validation failed - query is marked as invalid by API');
                        return redirect()->to('sign-in')->with('error', 'Invalid query. Please contact support.');
                    }
                    log_message('debug', 'Query validation successful');
                } else {
                    // queryData is null or empty
                    log_message('error', 'Query validation failed - API returned empty response');
                    log_message('error', 'URL: ' . $this->apiBaseUrl . $endpoint . '?encrypted_query=' . urlencode($q));
                    log_message('error', 'GET query parameter: ' . $q);

                    // Try direct approach as a fallback
                    try {
                        // Attempt to decrypt the query locally or use a different API endpoint
                        $directEndpoint = '/ambassadors/decode-query?q=' . urlencode($q);
                        log_message('debug', 'Attempting direct decoding: ' . $directEndpoint);

                        $decodedData = $this->makeGetRequest($directEndpoint, [], false);

                        if ($decodedData && isset($decodedData['ref_code'])) {
                            log_message('debug', 'Direct decoding succeeded! Found ref_code: ' . $decodedData['ref_code']);
                            // We got the decoded data
                            $ambassadorId = $decodedData['ambassador_id'] ?? null;

                            if ($ambassadorId) {
                                // Try to get ambassador details
                                $ambassadorData = $this->makeGetRequest('/ambassadors/' . $ambassadorId, [], false);

                                if ($ambassadorData && isset($ambassadorData['id'])) {
                                    log_message('debug', 'Retrieved ambassador data for ID ' . $ambassadorId);
                                    session()->set('ambassador_referral', [
                                        'ref_code' => $decodedData['ref_code'],
                                        'ambassador_id' => $ambassadorId
                                    ]);

                                    // Redirect to sign-up page and continue
                                    log_message('debug', 'Setting session with ambassador referral data and continuing');
                                    // No redirect, just continue with the sign-up process
                                } else {
                                    log_message('error', 'Could not get ambassador data for ID ' . $ambassadorId);
                                    return redirect()->to('sign-in')->with('error', 'Ambassador not found. Please try again.');
                                }
                            } else {
                                log_message('error', 'No ambassador ID in decoded data');
                                return redirect()->to('sign-in')->with('error', 'Invalid referral link. Please try direct sign-up.');
                            }
                        } else {
                            log_message('error', 'Direct decoding failed: ' . json_encode($decodedData));
                            return redirect()->to('sign-in')->with('error', 'Failed to validate query. Please contact support.');
                        }
                    } catch (\Exception $directEx) {
                        log_message('error', 'Direct decoding exception: ' . $directEx->getMessage());
                        return redirect()->to('sign-in')->with('error', 'Failed to validate query. Please contact support.');
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Exception during query validation: ' . $e->getMessage());
                log_message('error', 'Exception trace: ' . $e->getTraceAsString());
                // Continue with signup without ambassador reference
                return redirect()->to('sign-in')->with('error', 'Failed to validate ambassador reference: ' . $e->getMessage());
            }
        }

        // Get program slug from query parameter
        $programSlug = $this->request->getGet('program');
        log_message('debug', 'Program slug from query: ' . ($programSlug ?? 'not provided'));
        
        // Get registration type from query parameter (self_funded or fully_funded)
        $registrationType = $this->request->getGet('type');
        log_message('debug', 'Registration type from query: ' . ($registrationType ?? 'not provided'));
        
        $programData = null;

        // If program slug is provided, fetch program data
        if ($programSlug) {
            log_message('debug', 'Attempting to fetch program data for slug: ' . $programSlug);
            try {
                $programData = $this->makeGetRequest('/programs/slug/' . $programSlug, [], true);
                if (!$programData) {
                    log_message('error', 'Failed to fetch program data for slug: ' . $programSlug);
                } else {
                    log_message('debug', 'Successfully fetched program data with ID: ' . ($programData['id'] ?? 'unknown'));
                }
            } catch (\Exception $e) {
                log_message('error', 'Error fetching program data: ' . $e->getMessage());
                log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            }
        }

        // if program slug is not provided, get programs by category id
        if (!$programSlug) {
            // get category id from web settings (access from controller data)
            $categoryId = $this->data['webSettings']['program_category_id'] ?? null;
            log_message('debug', 'No program slug provided, using category ID from settings: ' . ($categoryId ?? 'not found'));

            if (!$categoryId) {
                log_message('error', 'No category ID found in web settings');
                return redirect()->to('sign-in')->with('error', 'No category ID found. Please contact support.');
            }

            if ($categoryId) {
                log_message('debug', 'Fetching programs for category ID: ' . $categoryId);
                try {
                    $programs = $this->makeGetRequest('/programs/category/' . $categoryId, [], true);

                    if (!$programs) {
                        log_message('error', 'Failed to fetch programs for category ID: ' . $categoryId);
                    } else {
                        log_message('debug', 'Found ' . count($programs) . ' programs for category ID: ' . $categoryId);
                    }

                    // get the first program from the list which is active
                    $programData = null;

                    foreach ($programs as $program) {
                        if (isset($program['is_active']) && $program['is_active'] == '1') {
                            $programData = $program;
                            log_message('debug', 'Selected active program ID: ' . ($program['id'] ?? 'unknown'));
                            break; // Exit loop after finding the first active program
                        }
                    }

                    if (!$programData) {
                        log_message('warning', 'No active programs found for category ID: ' . $categoryId);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error fetching programs by category: ' . $e->getMessage());
                    log_message('error', 'Exception trace: ' . $e->getTraceAsString());
                }
            }
        }

        // If program data is not found, redirect to sign-in page with error message
        if ($programSlug && !$programData) {
            log_message('error', 'Program not found for slug: ' . $programSlug);
            return redirect()->to('sign-in')->with('error', 'Program not found. Please check the link or contact support.');
        }

        // check if program is registration open
        if (isset($programData['is_registration_open']) && $programData['is_registration_open'] == '0') {
            log_message('error', 'Program registration is closed for slug: ' . $programSlug);
            return redirect()->to('sign-in')->with('error', 'Registration is currently closed for this program. Please check back later.');
        }

        // Log the program slug for debugging
        log_message('info', 'Program slug used for sign up: ' . $programSlug);

        // log program data for debugging
        log_message('info', 'Program data: ' . json_encode($programData));

        // set ambassador data for the view
        $ambassadorId = null;
        $ambassadorQuery = null;
        
        if (isset($queryData['ambassador']['id'])) {
            $ambassadorId = $queryData['ambassador']['id'];
            log_message('debug', 'Using ambassador ID: ' . $ambassadorId);
        } elseif ($q) {
            // Pass the encrypted query to the view for API processing
            $ambassadorQuery = $q;
            log_message('debug', 'Using encrypted ambassador query for signup');
        } else {
            log_message('debug', 'No ambassador reference available');
        }

        // Prepare data for the view
        $data = [
            'title' => 'Sign Up',
            'program' => $programData,
            'programSlug' => $programSlug,
            'registrationType' => $registrationType,
            'ambassadorId' => $ambassadorId,
            'ambassadorQuery' => $ambassadorQuery,
        ];

        log_message('debug', '===== SIGNUP PREPARATION COMPLETED =====');
        return $this->render('auth/sign-up-participant', $data);
    }

    public function signOut()
    {
        $user = session()->get('user');

        // get user type
        $userType = $user['type'] ?? null;


        $session = session();
        $session->remove('jwt_token');
        $session->remove('user');
        $session->remove('participants');
        $session->remove('isLoggedIn');

        $data = [
            'title' => 'Sign Out',
        ];

        if ($userType == 3) {
            $session->remove('isAmbassador');
            return redirect()->to('ambassadors/sign-in')->with('success', 'You have been signed out successfully');
        }

        return redirect()->to('sign-in')->with('success', 'You have been signed out successfully');
    }

    // forgot password
    public function forgotPassword()
    {
        $data = [
            'title' => 'Forgot Password',
        ];

        return $this->render('auth/pass-forgot', $data);
    }

    // send reset link
    public function sendResetLink()
    {
        $email = $this->request->getPost('email');

        if (!$email) {
            return redirect()->back()->with('error', 'Please provide an email address');
        }

        try {
            // Prepare the data for API using the new format
            $resetData = [
                'email' => $email,
                'web_url' => $this->currentUrl ?? $_SERVER['HTTP_HOST'] ?? 'default.com',
            ];

            // Log request for debugging
            log_message('debug', 'Password reset request data: ' . json_encode($resetData));

            // Use the correct endpoint for forgot password
            $response = $this->makePostRequest('/auth/forgot-password', $resetData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Password Reset Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->with('error', 'Failed to send reset link. Please try again later.');
            }

            // Check for successful response using new API format
            if (isset($response['status']) && $response['status'] === 'success') {
                return redirect()->to('sign-in')->with('success', 'Reset link sent to your email. Please check your inbox.');
            } else {
                $errorMessage = isset($response['message']) ? $response['message'] : 'Failed to send reset link. Please try again later.';
                return redirect()->back()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Reset password error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send reset link. Please try again later.');
        }
    }

    // reset password
    public function resetPassword()
    {
        // Check if token exists in the query parameters
        $token = $this->request->getGet('token');

        if (!$token) {
            return redirect()->to('forgot-password')->with('error', 'Reset token is missing. Please request a new password reset link.');
        }

        try {
            $response = $this->makeGetRequest('/auth/verify-token?token=' . urlencode($token), [], false);

            // Log response for debugging
            log_message('debug', 'API Token Verification Response: ' . json_encode($response));

            if (!$response || (isset($response['status']) && $response['status'] !== 'success')) {
                return redirect()->to('forgot-password')->with('error', 'Invalid or expired token. Please request a new password reset link.');
            }

            // Token is valid, proceed with password reset
            $data = [
                'title' => 'Reset Password',
                'token' => $token,
            ];

            return $this->render('auth/pass-reset', $data);
        } catch (\Exception $e) {
            log_message('error', 'Token verification error: ' . $e->getMessage());
            return redirect()->to('forgot-password')->with('error', 'Failed to verify reset token. Please try again.');
        }
    }

    // set new password
    public function setNewPassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validate input
        if (!$token || !$password) {
            return redirect()->back()->withInput()->with('error', 'All fields are required');
        }

        // Check if passwords match
        if ($password !== $confirmPassword) {
            return redirect()->back()->withInput()->with('error', 'Passwords do not match');
        }

        // Create reset data
        $resetData = [
            'token' => $token,
            'password' => $password,
        ];

        try {
            // Make API call to reset password endpoint
            $response = $this->makePostRequest('/auth/reset-password', $resetData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Reset Password Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->withInput()->with('error', 'Failed to reset password. Please try again later.');
            }

            // Check for successful response using new API format
            if (isset($response['status']) && $response['status'] === 'success') {
                return redirect()->to('sign-in')->with('success', 'Password reset successfully. You can now sign in.');
            } else {
                $errorMessage = isset($response['message']) ? $response['message'] : 'Failed to reset password. Please try again later.';
                return redirect()->back()->withInput()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Reset password error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to reset password. Please try again later.');
        }
    }

    // two step verification
    public function twoStepVerification()
    {
        $data = [
            'title' => 'Two Step Verification',
        ];

        return $this->render('auth/two-step', $data);
    }

    public function authorize()
    {
        $email = trim($this->request->getPost('email'));
        $password = trim($this->request->getPost('password'));

        if (!$email || !$password) {
            return redirect()->back()->with('error', 'Please provide both email and password');
        }

        try {
            // Prepare the data for API using the new authentication API format
            $authData = [
                'email' => $email,
                'password' => $password,
                'web_url' => $this->currentUrl ?? $_SERVER['HTTP_HOST'] ?? 'default.com',
            ];

            // Log request for debugging
            log_message('debug', 'Participant sign-in request data: ' . json_encode($authData));
            
            // Use the new participant sign-in endpoint
            $response = $this->makePostRequest('/auth/participant/sign-in', $authData, [], false, false);
            
            // Untuk debugging manual (jangan di production)
                // echo '<pre>';
                // var_dump($authData);
                // echo '</pre>';
                // exit;

            // Log response for debugging
            log_message('debug', 'API Authentication Response: ' . json_encode($response));
            // Untuk debugging manual (jangan di production)
            // echo '<pre>';
            // var_dump($response);
            // echo '</pre>';
            // exit;
            if (!$response) {
                return redirect()->back()->with('error', 'Authentication failed. Please check your credentials.');
            }

            // Check for successful authentication - handle both response formats
            $isSuccess = false;
            $token = null;
            $user = null;
            
            // Check for new API format with status/data wrapper
            if (isset($response['status']) && $response['status'] === 'success' && isset($response['data']['token'])) {
                $isSuccess = true;
                $token = $response['data']['token'];
                $user = $response['data']['user'] ?? null;
            }
            // Check for direct token/user format (current API response)
            elseif (isset($response['token']) && isset($response['user'])) {
                $isSuccess = true;
                $token = $response['token'];
                $user = $response['user'];
            }
            
            if ($isSuccess && $token && $user) {
                $session = session();
                
                // Store token
                $session->set('jwt_token', $token);
                
                // Store user data
                $session->set('user', $user);

                // get participants data from api using the updated endpoint
                $participantsResponse = $this->makeGetRequest('/participants/user/' . $user['id'], [], true);

                // Note: makeGetRequest automatically extracts the 'data' portion, so we access 'participant' directly
                if ($participantsResponse && isset($participantsResponse['participant']) && !empty($participantsResponse['participant'])) {
                    // Extract participant array from the API response structure (data portion already extracted)
                    $participants = $participantsResponse['participant'];
                    
                    $session->set('participants', $participants);
                    $session->set('isLoggedIn', true);
                    log_message('info', 'User logged in successfully: ' . $user['id']);

                    // get programs by category id
                    $programs = $this->makeGetRequest('/programs/category/' . $user['program_category_id'], [], true);

                    if ($programs) {
                        $session->set('programs', $programs);

                        // Set initial program ID during sign-in
                        // First check for programs the participant is registered in
                        $participant_programs = [];
                        foreach ($participants as $p) {
                            if (isset($p['program_id'])) {
                                $participant_programs[] = $p['program_id'];
                            }
                        }

                        $initialProgramId = null;

                        // First try to use a program the user is registered for
                        if (!empty($participant_programs)) {
                            // find active program from the list of registered programs
                            foreach ($programs as $program) {
                                if (in_array($program['id'], $participant_programs) && isset($program['is_active']) && $program['is_active'] == '1') {
                                    $initialProgramId = $program['id'];
                                    log_message('debug', 'Auth: Setting initial program to registered program: ' . $initialProgramId);
                                    break;
                                }
                            }

                            // If no active program is found, use the first registered program
                            if ($initialProgramId === null) {
                                $initialProgramId = $participant_programs[0];
                                log_message('debug', 'Auth: Setting initial program to first registered program: ' . $initialProgramId);
                            }
                        }

                        // If not registered for any program, try to find an active one
                        else {
                            foreach ($programs as $program) {
                                if (isset($program['is_active']) && $program['is_active'] == '1') {
                                    $initialProgramId = $program['id'];
                                    log_message('debug', 'Auth: Setting initial program to active program: ' . $initialProgramId);
                                    break;
                                }
                            }

                            // Last resort - use the first available program
                            if ($initialProgramId === null && !empty($programs)) {
                                $initialProgramId = $programs[0]['id'];
                                log_message('debug', 'Auth: Setting initial program to first available: ' . $initialProgramId);
                            }
                        }

                        // Set the selected program ID in session
                        if ($initialProgramId !== null) {
                            $session->set('current_program_id', $initialProgramId);

                            // Also set the current participant if applicable
                            foreach ($participants as $p) {
                                if (($p['program_id'] ?? null) === $initialProgramId) {
                                    $session->set('current_participant', $p);
                                    $session->set('current_participant_id', $p['id'] ?? null);
                                    break;
                                }
                            }
                        }
                } else {
                    log_message('error', 'Failed to fetch programs for user ID: ' . $user['id']);
                    return redirect()->back()->with('error', 'Failed to fetch programs. Please try again later.');
                }                    return redirect()->to('/dashboard');
                } else {
                    log_message('error', 'Failed to fetch participants data for user ID: ' . $user['id']);
                    return redirect()->back()->with('error', 'Failed to fetch participants data. Please try again later.');
                }
            } else {
                // Handle specific error messages from the API
                $errorMessage = isset($response['message'])
                    ? $response['message']
                    : 'Invalid credentials or server error';

                return redirect()->back()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Authentication error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Authentication failed. Please try again later.');
        }
    }

    /**
     * Register a new user account
     */
    public function register()
    {
        $fullname = $this->request->getPost('fullname');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');
        $ambassadorId = $this->request->getPost('ambassador_id');
        $ambassadorQuery = $this->request->getPost('q'); // Get encrypted ambassador query if available

        // Validate input
        if (!$fullname || !$email || !$password) {
            return redirect()->back()->withInput()->with('error', 'All fields are required');
        }

        // Check if passwords match
        if ($password !== $confirmPassword) {
            return redirect()->back()->withInput()->with('error', 'Passwords do not match');
        }

        // Create registration data using new API format
        $registerData = [
            'full_name' => $fullname,
            'email' => $email,
            'password' => $password,
            'web_url' => $this->currentUrl ?? $_SERVER['HTTP_HOST'] ?? 'default.com',
        ];

        // Include ambassador referral information
        if ($ambassadorId) {
            $registerData['ambassador_id'] = $ambassadorId;
        } elseif ($ambassadorQuery) {
            $registerData['q'] = $ambassadorQuery;
        }

        log_message('debug', 'Registration data: ' . json_encode($registerData));

        try {
            // Make API call to new participant sign-up endpoint
            $response = $this->makePostRequest('/auth/participant/sign-up', $registerData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Registration Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again later.');
            }

            // Check for successful registration - handle both response formats
            $isSuccess = false;
            $participant = null;
            $isNewUser = true;
            
            // Check for new API format with status/data wrapper
            if (isset($response['status']) && $response['status'] === 'success' && isset($response['data']['participant'])) {
                $isSuccess = true;
                $participant = $response['data']['participant'];
                $isNewUser = isset($response['data']['is_new']) ? $response['data']['is_new'] : true;
            }
            // Check for direct participant format (current API response)
            elseif (isset($response['participant'])) {
                $isSuccess = true;
                $participant = $response['participant'];
                $isNewUser = isset($response['is_new']) ? $response['is_new'] : true;
            }
            
            if ($isSuccess && $participant) {
                
                // get web settings to check if email verification is required
                $webSettings = $this->data['webSettings'] ?? [];
                $isVerificationRequired = isset($webSettings['is_verification_required']) && $webSettings['is_verification_required'] == '1';

                // Log the verification requirement
                log_message('debug', 'Is email verification required: ' . ($isVerificationRequired ? 'Yes' : 'No'));

                // Check if user is new from API response
                
                if (!$isNewUser) {
                    $message = 'You are already registered. Please sign in to continue.';
                } elseif ($isVerificationRequired) {
                    $message = 'Registration successful! Please check your email to verify your account.';
                } else {
                    $message = 'Registration successful! You can now sign in to continue.';
                }

                return redirect()->to('sign-in')->with('success', $message);
            } else {
                // Handle error response from new API format
                $errorMessage = 'Registration failed. Please try again.';
                if (isset($response['message'])) {
                    $errorMessage = $response['message'];
                } elseif (isset($response['errors'])) {
                    $errors = is_array($response['errors']) ? $response['errors'] : [$response['errors']];
                    $errorMessage = implode(' ', array_values($errors));
                }
                return redirect()->back()->withInput()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again later.');
        }
    }

    /**
     * Verify email address
     */
    public function verifyEmail()
    {
        $token = $this->request->getGet('token');
        $email = $this->request->getGet('email');

        if (!$email) {
            return redirect()->to('sign-in')->with('error', 'Email address is missing. Please request a new verification link.');
        }

        if (!$token) {
            return redirect()->to('sign-in')->with('error', 'Verification token is missing. Please request a new verification link.');
        }

        try {
            $response = $this->makeGetRequest('/auth/verify-email?token=' . urlencode($token) . '&email=' . urlencode($email), [], false);

            // Log response for debugging
            log_message('debug', 'API Email Verification Response: ' . json_encode($response));

            if (!$response || (isset($response['status']) && $response['status'] !== 'success')) {
                return redirect()->to('sign-in')->with('error', 'Invalid or expired token. Please request a new verification link.');
            }

            // Email verified successfully
            return redirect()->to('sign-in')->with('success', 'Email verified successfully! You can now sign in.');
        } catch (\Exception $e) {
            log_message('error', 'Email verification error: ' . $e->getMessage());
            return redirect()->to('sign-in')->with('error', 'Failed to verify email. Please try again.');
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification()
    {
        $email = $this->request->getPost('email');

        if (!$email) {
            return redirect()->back()->with('error', 'Please provide an email address');
        }

        try {
            $resendData = [
                'email' => $email,
                'web_url' => $this->currentUrl ?? $_SERVER['HTTP_HOST'] ?? 'default.com',
            ];

            // Log request for debugging
            log_message('debug', 'Resend verification request data: ' . json_encode($resendData));

            $response = $this->makePostRequest('/auth/resend-verification', $resendData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Resend Verification Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->with('error', 'Failed to send verification email. Please try again later.');
            }

            // Check for successful response
            if (isset($response['status']) && $response['status'] === 'success') {
                return redirect()->to('sign-in')->with('success', 'Verification email sent successfully. Please check your inbox.');
            } else {
                $errorMessage = isset($response['message']) ? $response['message'] : 'Failed to send verification email. Please try again later.';
                return redirect()->back()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Resend verification error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send verification email. Please try again later.');
        }
    }

    /**
     * Ambassador sign in page
     */
    public function ambassadorSignIn()
    {

        $data = [
            'title' => 'Ambassador Sign In',
        ];

        return $this->render('auth/sign-in-amb', $data);
    }

    /**
     * API Authentication endpoint (no /api prefix)
     * POST /auth/sign-in
     */
    public function authApiSignIn()
    {
        try {
            // Get JSON input or form data
            $input = $this->request->getJSON(true) ?? $this->request->getPost();
            
            $email = trim($input['email'] ?? '');
            $password = trim($input['password'] ?? '');
            $refCode = trim($input['ref_code'] ?? '');
            $type = (int)($input['type'] ?? 2);

            // Validate required fields based on user type
            if ($type == 3) { // Ambassador
                if (!$email || !$refCode) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status' => 'error',
                        'message' => 'Email and referral code are required for ambassador login'
                    ]);
                }
            } else { // Participant
                if (!$email || !$password) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status' => 'error',
                        'message' => 'Email and password are required'
                    ]);
                }
            }

            // Prepare authentication data
            $authData = [
                'email' => $email,
                'type' => $type,
            ];

            if ($type == 3) {
                $authData['ref_code'] = $refCode;
            } else {
                $authData['password'] = $password;
            }

            // Add web_url if available
            if (isset($this->currentUrl)) {
                $authData['web_url'] = $this->currentUrl;
            }

            // Log request for debugging
            log_message('debug', 'API Auth request data: ' . json_encode($authData));

            // Make API call for authentication
            $response = $this->makePostRequest('/auth/sign-in', $authData, [], false, false);

            log_message('debug', 'API Authentication Response: ' . json_encode($response));

            if (!$response) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Authentication failed. Please try again later.'
                ]);
            }

            if (isset($response['token']) && $response['token']) {
                // For API response, return the token and user data
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'data' => [
                        'token' => $response['token'],
                        'user' => $response['user'],
                        'expires_in' => 86400 // 24 hours
                    ]
                ]);
            } else {
                $errorMessage = isset($response['message'])
                    ? $response['message']
                    : 'Invalid credentials or server error';

                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => $errorMessage
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'API Authentication error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Authentication failed. Please try again later.'
            ]);
        }
    }

    /**
     * Authorize ambassador login
     */
    public function authorizeAmbassador()
    {
        $email = trim($this->request->getPost('email'));
        $refCode = trim($this->request->getPost('referral_code'));

        if (!$email || !$refCode) {
            return redirect()->back()->with('error', 'Please provide both email and referral code');
        }

        try {
            // Prepare the data for ambassador authentication using new API format
            $authData = [
                'email' => $email,
                'ref_code' => $refCode,
            ];

            // Log request for debugging
            log_message('debug', 'Ambassador sign-in request data: ' . json_encode($authData));

            // Use the new ambassador sign-in endpoint
            $response = $this->makePostRequest('/auth/ambassador/sign-in', $authData, [], false, false);

            log_message('debug', 'API Ambassador Authentication Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->with('error', 'Authentication failed. Please check your credentials.');
            }

            // Check for successful authentication - handle both response formats
            $isSuccess = false;
            $token = null;
            $ambassador = null;
            
            // Check for new API format with status/data wrapper
            if (isset($response['status']) && $response['status'] === 'success' && isset($response['data']['token'])) {
                $isSuccess = true;
                $token = $response['data']['token'];
                $ambassador = $response['data']['ambassador'] ?? null;
            }
            // Check for direct token/ambassador format (current API response)
            elseif (isset($response['token']) && isset($response['ambassador'])) {
                $isSuccess = true;
                $token = $response['token'];
                $ambassador = $response['ambassador'];
            }
            
            if ($isSuccess && $token && $ambassador) {
                $session = session();

                // Store token
                $session->set('jwt_token', $token);

                // Store ambassador data - prefer ambassador_info if available, fallback to ambassador
                $ambassadorInfo = $response['data']['ambassador_info'] ?? null;
                $userData = [
                    'id' => $ambassador['id'],
                    'email' => $ambassador['email'],
                    'full_name' => $ambassadorInfo['full_name'] ?? $ambassador['name'] ?? 'Ambassador',
                    'name' => $ambassador['name'] ?? $ambassadorInfo['full_name'] ?? 'Ambassador', // Keep backward compatibility
                    'type' => 3, // Ambassador type
                    'program_id' => $ambassador['program_id'] ?? null,
                    'ref_code' => $ambassador['ref_code'] ?? $ambassadorInfo['ref_code'] ?? null,
                    'institution' => $ambassador['institution'] ?? $ambassadorInfo['institution'] ?? null,
                ];
                $session->set('user', $userData);
                $session->set('isAmbassador', true);
                $session->set('isLoggedIn', true);
                
                log_message('info', 'Ambassador logged in successfully: ' . $ambassador['id']);

                return redirect()->to('ambassadors/dashboard');
            } else {
                // Handle specific error messages from the API
                $errorMessage = isset($response['message'])
                    ? $response['message']
                    : 'Invalid credentials or server error';

                return redirect()->back()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Ambassador authentication error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Authentication failed. Please try again later.');
        }
    }

    /**
     * Get profile from JWT token
     * GET /auth/profile
     */
    public function authProfile()
    {
        try {
            // Get JWT token from Authorization header
            $token = $this->getJwtTokenFromHeader();
            
            if (!$token) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'JWT token required'
                ]);
            }

            // Validate token with API
            $response = $this->makeGetRequest('/auth/profile', [], true);

            if ($response && isset($response['user'])) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Profile retrieved successfully',
                    'data' => [
                        'user' => $response['user']
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid or expired token'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'JWT profile validation error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Token validation failed'
            ]);
        }
    }

    /**
     * Refresh JWT token
     * POST /auth/refresh
     */
    public function authRefresh()
    {
        try {
            // Get JWT token from Authorization header
            $token = $this->getJwtTokenFromHeader();
            
            if (!$token) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'JWT token required'
                ]);
            }

            // Refresh token with API
            $response = $this->makePostRequest('/auth/refresh', [], [], true, false);

            if ($response && isset($response['token'])) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Token refreshed successfully',
                    'data' => [
                        'token' => $response['token'],
                        'expires_in' => 86400 // 24 hours
                    ]
                ]);
            } else {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Token refresh failed'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'JWT token refresh error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Token refresh failed'
            ]);
        }
    }

    /**
     * Get JWT token from Authorization header
     * 
     * @return string|null
     */
    private function getJwtTokenFromHeader()
    {
        $authHeader = $this->request->getHeader('Authorization');
        
        if ($authHeader && $authHeader->getValue()) {
            $headerValue = $authHeader->getValue();
            if (preg_match('/Bearer\s+(.*)$/i', $headerValue, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
}