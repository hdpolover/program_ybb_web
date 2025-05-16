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

        // check if any program is_registration_open = 1 
        $isRegistrationOpen = false;

        // loop through programs to check if any program is open for registration
        foreach ($programs as $program) {
            if (isset($program['is_registration_open']) && $program['is_registration_open'] == '1') {
                $isRegistrationOpen = true;
                break; // Exit loop if any program is open for registration
            }
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
                $endpoint = '/ambassadors/check-query/';
                log_message('debug', 'Making API request to ' . $endpoint . ' with query: ' . $q);
                log_message('debug', 'Full API URL: ' . $this->apiBaseUrl . $endpoint);
                
                $queryData = $this->makePostRequest($endpoint, ['encrypted_query' => $q], [], false, false);

                // Log the response data
                log_message('debug', 'Ambassador query check response: ' . json_encode($queryData));

                if ($queryData) {
                    log_message('debug', 'Query validation returned data: ' . json_encode($queryData));

                    // check if query is valid
                    if (!isset($queryData['is_valid']) || $queryData['is_valid'] !== true) {
                        log_message('error', 'Query validation failed - query is marked as invalid by API');
                        return redirect()->to('sign-in')->with('error', 'Invalid query. Please contact support.');
                    }

                    log_message('debug', 'Query validation successful');
                } else {
                    log_message('error', 'Query validation failed - API returned empty response');
                    log_message('error', 'URL: ' . $this->apiBaseUrl . $endpoint);
                    log_message('error', 'POST data: ' . json_encode(['encrypted_query' => $q]));

                    // Continue with signup without ambassador reference instead of showing error
                    log_message('debug', 'Continuing signup process without ambassador reference');
                    return redirect()->to('sign-in')->with('error', 'Failed to validate query. Please contact support.');
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

        // set ambassador ref_code data
        if (isset($queryData['ambassador']['id'])) {
            // check if ref_code is valid
            $ambassadorId = $queryData['ambassador']['id'];
            log_message('debug', 'Using ambassador ID: ' . $ambassadorId);
        } else {
            $ambassadorId = null;
            log_message('debug', 'No ambassador ID available');
        }

        // Prepare data for the view
        $data = [
            'title' => 'Sign Up',
            'program' => $programData,
            'programSlug' => $programSlug,
            'ambassadorId' => $ambassadorId,
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
            // Prepare the data for API using the format required by sign-in-jwt endpoint
            $resetData = [
                'email' => $email,
                'web_url' => $this->currentUrl, // Add web_url if available
            ];

            // Log request for debugging
            log_message('debug', 'Reset password request data: ' . json_encode($resetData));

            // Use the correct endpoint /api/auth/reset-password
            $response = $this->makePostRequest('/auth/forgot-password', $resetData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Reset Password Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->with('error', 'Failed to send reset link. Please try again later.');
            }

            // Check for successful response
            if (isset($response['message']) && $response['message']) {
                return redirect()->to('sign-in')->with('success', 'Reset link sent to your email. Please check your inbox.');
            } else {
                return redirect()->back()->with('error', 'Failed to send reset link. Please try again later.');
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

            $response = $this->makeGetRequest('/auth/verify-token?token=' . $token, [], false);

            // Log response for debugging
            log_message('debug', 'API Token Verification Response: ' . json_encode($response));

            if (!isset($response)) {
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
            // Make API call to reset password endpoint - use form data instead of JSON
            $response = $this->makePostRequest('/auth/reset-password', $resetData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Reset Password Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->withInput()->with('error', 'Failed to reset password. Please try again later.');
            }

            // Check for successful response
            if (isset($response['message']) && $response['message']) {
                return redirect()->to('sign-in')->with('success', 'Password reset successfully. You can now sign in.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to reset password. Please try again later.');
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
            // Prepare the data for API using the format required by sign-in-jwt endpoint
            $authData = [
                'email' => $email,
                'password' => $password,
                'type' => 2, // 2 = participant (as defined in the API)
            ];

            // Add web_url if available
            if (isset($this->currentUrl)) {
                $authData['web_url'] = $this->currentUrl;
            }

            // Log request for debugging
            log_message('debug', 'Auth request data: ' . json_encode($authData));

            // Use the correct endpoint /api/auth/sign-in-jwt
            // Pass false as the last parameter to send as form data instead of JSON
            $response = $this->makePostRequest('/auth/sign-in', $authData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Authentication Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->with('error', 'Authentication failed. Please check your credentials.');
            }

            // API returns success field to indicate success
            if (isset($response['token']) && $response['token']) {
                $session = session();

                // Store token from the data field
                if (isset($response['token'])) {
                    $session->set('jwt_token', $response['token']);
                }

                // Store user data
                if (isset($response['user'])) {
                    $session->set('user', $response['user']);
                }

                // get participants data from api
                $participants = $this->makeGetRequest('/participants/user/' . $response['user']['id'], [], true);

                if ($participants) {

                    $session->set('participants', $participants);
                    $session->set('isLoggedIn', true);
                    log_message('info', 'User logged in successfully: ' . $response['user']['id']);

                    // get programs by category id
                    $programs = $this->makeGetRequest('/programs/category/' . $response['user']['program_category_id'], [], true);

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
                        log_message('error', 'Failed to fetch programs for user ID: ' . $response['user']['id']);
                        return redirect()->back()->with('error', 'Failed to fetch programs. Please try again later.');
                    }

                    return redirect()->to('/dashboard');
                } else {
                    log_message('error', 'Failed to fetch participants data for user ID: ' . $response['user']['id']);
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
        $programId = $this->request->getPost('program_id'); // Get program ID from form data
        $programCategoryId = $this->request->getPost('program_category_id'); // Get program category ID from form data
        $ambassadorId = $this->request->getPost('ambassador_id');

        // Validate input
        if (!$fullname || !$email || !$password) {
            return redirect()->back()->withInput()->with('error', 'All fields are required');
        }

        // Check if passwords match
        if ($password !== $confirmPassword) {
            return redirect()->back()->withInput()->with('error', 'Passwords do not match');
        }

        // Create registration data
        $registerData = [
            'full_name' => $fullname,
            'email' => $email,
            'password' => $password,
            'program_id' => $programId, // Include program ID in registration data
            'program_category_id' => $programCategoryId, // Include program category ID in registration data
        ];

        // Include ambassador ID if provided
        if ($ambassadorId) {
            $registerData['ambassador_id'] = $ambassadorId;
        }

        // var_dump($registerData); // Debugging line to check the data being sent

        try {
            // Make API call to register endpoint - use form data instead of JSON
            $response = $this->makePostRequest('/auth/participant/sign-up', $registerData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Registration Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again later.');
            }

            // Check for successful registration
            if (isset($response['participant']) && $response['participant']) {

                // check if user is already registered
                $message = 'Registration successful! Please check your email to verify your account.';

                if (isset($response['is_new']) && $response['is_new'] == true) {
                    $message = 'Registration successful! Please check your email to verify your account.';
                } else {
                    $message = 'Registration successful! You can now sign in to continue.';
                }

                return redirect()->to('sign-in')->with('success', $message);
            } else {
                $errorMessage = isset($response['errors']) ? implode(' ', $response['errors']) : 'Registration failed. Please try again.';
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
            $response = $this->makeGetRequest('/auth/verify-email?token=' . $token . '&email=' . $email, [], false);

            // Log response for debugging
            log_message('debug', 'API Email Verification Response: ' . json_encode($response));

            if (!isset($response)) {
                return redirect()->to('sign-in')->with('error', 'Invalid or expired token. Please request a new verification link.');
            }

            // Token is valid, proceed with email verification
            return redirect()->to('sign-in')->with('success', 'Email verified successfully! You can now sign in.');
        } catch (\Exception $e) {
            log_message('error', 'Email verification error: ' . $e->getMessage());
            return redirect()->to('sign-in')->with('error', 'Failed to verify email. Please try again.');
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
     * Authorize ambassador login
     */
    public function authorizeAmbassador()
    {
        $email = trim($this->request->getPost('email'));
        $refCode = trim($this->request->getPost('referral_code'));


        if (!$email || !$refCode) {
            return redirect()->back()->with('error', 'Please provide both email, referral code');
        }

        try {
            // Prepare the data for ambassador authentication
            $authData = [
                'email' => $email,
                'ref_code' => $refCode,
                'type' => 3, // 3 = ambassador (as defined in the API)
            ];

            // Add web_url if available
            if (isset($this->currentUrl)) {
                $authData['web_url'] = $this->currentUrl;
            }

            // var_dump($authData); // Debugging line to check the data being sent

            // Log request for debugging
            log_message('debug', 'Ambassador auth request data: ' . json_encode($authData));

            // Use the same endpoint with different type
            $response = $this->makePostRequest('/auth/sign-in', $authData, [], false, false);

            log_message('debug', 'API Ambassador Authentication Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->with('error', 'Authentication failed. Please check your credentials.');
            }

            if (isset($response['token']) && $response['token']) {
                $session = session();

                // Store token
                $session->set('jwt_token', $response['token']);

                // Store user data
                if (isset($response['user'])) {
                    $session->set('user', $response['user']);
                    $session->set('isAmbassador', true);
                }

                $session->set('isLoggedIn', true);
                log_message('info', 'Ambassador logged in successfully: ' . $response['user']['id']);

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
}