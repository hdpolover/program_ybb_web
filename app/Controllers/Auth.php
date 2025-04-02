<?php

namespace App\Controllers;

class Auth extends BaseController
{

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Sign In',
        ];

        return $this->render('auth/sign-in', $data);
    }

    // sign up
    public function signUp()
    {
        $data = [
            'title' => 'Sign Up',
        ];

        return $this->render('auth/sign-up', $data);
    }

    public function signOut()
    {
        $session = session();
        $session->remove('jwt_token');
        $session->remove('user');
        $session->remove('participants');
        $session->remove('isLoggedIn');

        $data = [
            'title' => 'Sign Out',
        ];

        return redirect()->to('sign-in')->with('success', 'You have been signed out successfully');
    }

    // forgot password
    public function forgotPassword()
    {
        $data = [
            'title' => 'Forgot Password',
        ];

        return $this->render('auth/pass-reset', $data);
    }

    // reset password
    public function resetPassword()
    {
        $data = [
            'title' => 'Reset Password',
        ];

        return $this->render('auth/pass-change', $data);
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
                'type' => '2', // 2 = participant (as defined in the API)
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
            'type' => '2', // participant
        ];

        // Add web_url if available
        if (isset($this->currentUrl)) {
            $registerData['web_url'] = $this->currentUrl;
        }

        try {
            // Make API call to register endpoint - use form data instead of JSON
            $response = $this->makePostRequest('/api/auth/register', $registerData, [], false, false);

            // Log response for debugging
            log_message('debug', 'API Registration Response: ' . json_encode($response));

            if (!$response) {
                return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again later.');
            }

            // Check for successful registration
            if (isset($response['success']) && $response['success']) {
                return redirect()->to('sign-in')->with('success', 'Registration successful! Please sign in with your credentials.');
            } else {
                $errorMessage = isset($response['message']) ? $response['message'] : 'Registration failed. Please try again.';
                return redirect()->back()->withInput()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again later.');
        }
    }
}
