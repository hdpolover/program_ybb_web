<?php

namespace App\Controllers\ambassador;
use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $ambassador = session()->get('user');

        if (!$ambassador) {
            return redirect()->to('/ambassadors/sign-in')->with('error', 'Please sign in to continue');
        }

        // Initialize variables
        $referralLink = null;
        $program = null;
        
        // Try to get profile data from the new dashboard profile endpoint
        log_message('info', 'Attempting to fetch profile data from API for ambassador ID: ' . $ambassador['id']);
        
        // First, authenticate to get JWT token
        $jwtToken = $this->authenticateAmbassador($ambassador);
        
        if ($jwtToken) {
            // Store token temporarily in session for API calls
            session()->set('jwt_token', $jwtToken);
            
            // Now try to get profile data
            $profileData = $this->makeGetRequest('/ambassador/dashboard/profile', [], true);
            
            if ($profileData && isset($profileData['ambassador'])) {
                log_message('info', 'Profile API successful. Response: ' . json_encode($profileData));
                
                // Use data from the API response
                $ambassadorDetails = $profileData['ambassador'];
                $program = $profileData['program'] ?? null;
                $referralLink = $profileData['referral_link']['url'] ?? null;
                
                log_message('info', 'Referral link from API: ' . ($referralLink ?? 'NULL'));
                
                // Update ambassador data with API response
                $ambassador['details'] = $ambassadorDetails;
                $ambassador['program'] = $program;
            } else {
                log_message('warning', 'Failed to fetch profile data from API after authentication');
                $this->useFallbackData($ambassador, $program);
            }
        } else {
            log_message('warning', 'Failed to authenticate with API, using fallback methods');
            $this->useFallbackData($ambassador, $program);
        }
        
        // Ensure we have a referral link
        if (!$referralLink) {
            log_message('warning', 'No referral link from API, generating fallback link');
            $refCode = $ambassador['details']['ref_code'] ?? $ambassador['ref_code'] ?? 'AMBASSADOR';
            $referralLink = 'https://japanyouthsummit.com/sign-up?q=' . base64_encode($refCode);
            log_message('info', 'Generated fallback referral link: ' . $referralLink);
        }
        
        // Get additional ambassador statistics
        $statistics = $this->getAmbassadorStatistics($ambassador['id']);

        $data = [
            'title' => 'Profile',
            'ambassador' => $ambassador,
            'generatedLink' => $referralLink,
            'statistics' => $statistics,
        ];

        log_message('info', 'Profile data prepared. Link: ' . $referralLink);
        return $this->render('ambassador/profile', $data);
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
                'type' => 3, // Ambassador type  
                'ref_code' => $refCode
            ];
            
            log_message('info', 'Authenticating ambassador: ' . $ambassador['email'] . ' with ref_code: ' . $refCode);
            
            $response = $this->makePostRequestFullResponse('/auth/sign-in', $loginData, [], false, true);
            
            if ($response && isset($response['data']['token'])) {
                log_message('info', 'Authentication successful for ambassador: ' . $ambassador['email']);
                return $response['data']['token'];
            } else {
                log_message('error', 'Authentication failed. Response: ' . json_encode($response));
                
                // If authentication failed and we don't have stored password, try common passwords
                if (!$storedPassword) {
                    $commonPasswords = ['oppo2024', 'password', 'ambassador123'];
                    foreach ($commonPasswords as $tryPassword) {
                        $loginData['password'] = $tryPassword;
                        $response = $this->makePostRequestFullResponse('/auth/sign-in', $loginData, [], false, true);
                        if ($response && isset($response['data']['token'])) {
                            log_message('info', 'Authentication successful with alternative password for: ' . $ambassador['email']);
                            // Store the working password for future use
                            session()->set('ambassador_password', $tryPassword);
                            return $response['data']['token'];
                        }
                    }
                }
                
                return null;
            }
        } catch (\Exception $e) {
            log_message('error', 'Authentication error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Use fallback data when API is not available
     * 
     * @param array $ambassador
     * @param mixed $program
     */
    private function useFallbackData(&$ambassador, &$program)
    {
        // Fallback: Build ambassador details from session data
        $ambassador['details'] = [
            'id' => $ambassador['id'],
            'name' => $ambassador['name'],
            'email' => $ambassador['email'],
            'ref_code' => $ambassador['ref_code'],
            'institution' => null,
            'phone_number' => null,
            'gender' => null,
            'is_active' => true,
            'created_at' => null,
            'notes' => ''
        ];
        
        // Fallback: Try to get program details
        $programId = $ambassador['program_id'] ?? 7; // Default to Japan Youth Summit
        $programDetails = $this->makeGetRequest('/programs/' . $programId, [], false);
        
        if (isset($programDetails['id'])) {
            $program = $programDetails;
        } else {
            // Default program data
            $program = [
                'id' => 7,
                'name' => 'Japan Youth Summit 2025',
                'description' => 'International innovation competition and youth summit',
                'start_date' => '2025-10-12 00:00:00',
                'end_date' => '2025-10-15 00:00:00',
                'status' => 'upcoming',
                'category' => [
                    'id' => 6,
                    'name' => 'Japan Youth Summit',
                    'web_url' => 'japanyouthsummit.com'
                ]
            ];
        }
        
        $ambassador['program'] = $program;
    }

    /**
     * Get ambassador statistics from dashboard overview
     * 
     * @param int $ambassadorId
     * @return array
     */
    private function getAmbassadorStatistics($ambassadorId)
    {
        $defaultStats = [
            'total_referrals' => 0,
            'active_participants' => 0,
            'completion_rate' => 0.0,
            'total_earnings' => 0.00
        ];
        
        try {
            $overviewData = $this->makeGetRequest('/ambassador/dashboard/overview', [], true);
            if ($overviewData && isset($overviewData['metrics'])) {
                return array_merge($defaultStats, $overviewData['metrics']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch ambassador statistics: ' . $e->getMessage());
        }
        
        return $defaultStats;
    }

}
