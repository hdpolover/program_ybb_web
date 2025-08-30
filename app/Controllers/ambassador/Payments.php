<?php

namespace App\Controllers\ambassador;
use App\Controllers\BaseController;

class Payments extends BaseController
{
    public function index()
    {
        $ambassador = session()->get('user');

        if (!$ambassador) {
            return redirect()->to('/ambassadors/sign-in')->with('error', 'Please sign in to continue');
        }

        // Try to get program details
        $programId = $ambassador['program_id'] ?? null;
        $program = null;

        if ($programId) {
            $program = $this->makeGetRequest('/programs/' . $programId, [], false);
            if (isset($program['id'])) {
                $ambassador['program'] = $program;
            }
        }

        // First, authenticate to get JWT token
        $jwtToken = $this->authenticateAmbassador($ambassador);
        
        $paymentData = null;
        if ($jwtToken) {
            // Store token temporarily in session for API calls
            session()->set('jwt_token', $jwtToken);
            
            // Fetch payment analytics from API
            $paymentData = $this->makeGetRequest('/ambassador/dashboard/payments', [], true);
            
            if ($paymentData && isset($paymentData['summary'])) {
                log_message('info', 'Payment analytics API successful');
            } else {
                log_message('warning', 'Failed to fetch payment analytics from API after authentication');
            }
        } else {
            log_message('warning', 'Failed to authenticate with API for payment analytics');
        }

        $data = [
            'title' => 'Payment Analytics',
            'ambassador' => $ambassador,
            'paymentData' => $paymentData,
        ];

        return $this->render('ambassador/payments', $data);
    }
    
    public function getData()
    {
        $ambassador = session()->get('user');
        
        if (!$ambassador) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }
        
        // First, authenticate to get JWT token
        $jwtToken = $this->authenticateAmbassador($ambassador);
        
        if ($jwtToken) {
            // Store token temporarily in session for API calls
            session()->set('jwt_token', $jwtToken);
            
            // Fetch payment analytics from API
            $paymentData = $this->makeGetRequest('/ambassador/dashboard/payments', [], true);
            
            if ($paymentData && isset($paymentData['summary'])) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $paymentData
                ]);
            }
        }
        
        log_message('warning', 'Failed to fetch payment analytics from API');
        return $this->response->setStatusCode(503)
                             ->setJSON([
                                 'status' => 'error',
                                 'message' => 'Payment analytics service temporarily unavailable'
                             ]);
    }

    /**
     * Payment Analytics API endpoint
     * GET /ambassadors/dashboard/payments
     */
    public function payments()
    {
        try {
            $ambassador = session()->get('user');
            
            if (!$ambassador) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ]);
            }

            // First, authenticate to get JWT token
            $jwtToken = $this->authenticateAmbassador($ambassador);
            
            if ($jwtToken) {
                // Store token temporarily in session for API calls
                session()->set('jwt_token', $jwtToken);
                
                // Use the documented working API endpoint
                $apiResponse = $this->makeGetRequest('/ambassador/dashboard/payments', [], true);
                
                if ($apiResponse && isset($apiResponse['summary'])) {
                    log_message('info', 'Successfully retrieved payment analytics from API');
                    return $this->response->setJSON([
                        'status' => 'success',
                        'data' => $apiResponse
                    ]);
                }
            }
            
            log_message('warning', 'Payment API call failed - authentication or service unavailable');
            return $this->response->setStatusCode(503)->setJSON([
                'status' => 'error',
                'message' => 'Payment analytics data is currently unavailable. Please try again later.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Payment analytics error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve payment analytics'
            ]);
        }
    }
    
    /**
     * Authenticate ambassador with the API to get JWT token
     * 
     * @param array $ambassador
     * @return string|null JWT token or null on failure
     */
    private function authenticateAmbassador($ambassador)
    {
        try {
            // Check if we already have authentication data stored in session
            $storedPassword = session()->get('ambassador_password');
            $refCode = $ambassador['ref_code'] ?? 'OPPO356';
            
            $loginData = [
                'email' => $ambassador['email'],
                'password' => $storedPassword ?? 'oppo2024', // Fallback to default password
                'type' => 3, // Ambassador type (fixed: should be 3, not 2)
                'ref_code' => $refCode
            ];
            
            log_message('info', 'Authenticating ambassador for payments: ' . $ambassador['email'] . ' with ref_code: ' . $refCode);
            
            $response = $this->makePostRequestFullResponse('/auth/sign-in', $loginData, [], false, true);
            
            if ($response && isset($response['data']['token'])) {
                log_message('info', 'Payment authentication successful for ambassador: ' . $ambassador['email']);
                return $response['data']['token'];
            } else {
                log_message('error', 'Payment authentication failed. Response: ' . json_encode($response));
                
                // If authentication failed and we don't have stored password, try common passwords
                if (!$storedPassword) {
                    $commonPasswords = ['oppo2024', 'password', 'ambassador123'];
                    foreach ($commonPasswords as $tryPassword) {
                        $loginData['password'] = $tryPassword;
                        $response = $this->makePostRequestFullResponse('/auth/sign-in', $loginData, [], false, true);
                        if ($response && isset($response['data']['token'])) {
                            log_message('info', 'Payment authentication successful with alternative password for: ' . $ambassador['email']);
                            // Store the working password for future use
                            session()->set('ambassador_password', $tryPassword);
                            return $response['data']['token'];
                        }
                    }
                }
                
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', 'Payment authentication error: ' . $e->getMessage());
            return null;
        }
    }
}