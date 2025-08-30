<?php

namespace App\Controllers\ambassador;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    /**
     * Get sample dashboard data for testing when API fails
     */
    private function getSampleDashboardData()
    {
        $sampleDataFile = FCPATH . '../sample_data_overview.json';
        
        if (file_exists($sampleDataFile)) {
            $sampleData = json_decode(file_get_contents($sampleDataFile), true);
            
            if ($sampleData) {
                log_message('info', 'Using sample dashboard data for testing');
                return $sampleData;
            }
        }
        
        log_message('warning', 'Sample data file not found, returning empty structure');
        return $this->getEmptyDashboardData();
    }
    
    /**
     * Get empty dashboard data structure
     */
    private function getEmptyDashboardData()
    {
        return [
            'ambassador' => [],
            'metrics' => [
                'total_referrals' => 0,
                'completed_registrations' => 0,
                'this_month_referrals' => 0,
                'conversion_rate' => 0
            ],
            'participant_breakdown' => [
                'by_category' => [
                    'fully_funded' => 0,
                    'self_funded' => 0
                ],
                'by_nationality' => [],
                'by_institution_type' => []
            ],
            'payment_summary' => [
                'total_revenue_generated' => 0,
                'paid_participants' => 0,
                'pending_payments' => 0,
                'payment_completion_rate' => 0,
                'average_payment_amount' => 0
            ],
            'performance_insights' => [
                'best_performing_month' => 'N/A',
                'registration_trend' => 'stable',
                'engagement_score' => 0,
                'quality_score' => 0
            ],
            'achievements' => [],
            'program' => [],
            'quick_stats' => [],
            'recent_activity_sample' => []
        ];
    }

    public function index()
    {
        $ambassador = session()->get('user');

        if (!$ambassador) {
            return redirect()->to('/ambassadors/login')->with('error', 'Please log in to access dashboard');
        }

        // get program details
        $programId = $ambassador['program_id'] ?? null;
        
        if ($programId) {
            $program = $this->makeGetRequest('/programs/' . $programId, [], false);
            if (isset($program['id'])) {
                $ambassador['program'] = $program;
            } else {
                log_message('warning', 'Program API call failed for program ID: ' . $programId);
            }
        } else {
            log_message('warning', 'No program ID found for ambassador: ' . ($ambassador['id'] ?? 'unknown'));
        }

        // Get dashboard overview data from API
        $overviewData = null;
        try {
            log_message('info', 'Ambassador ' . ($ambassador['id'] ?? 'unknown') . ' requesting dashboard overview data');
            
            $apiResponse = $this->makeGetRequest('/ambassador/dashboard/overview', [], true);
            
            if ($apiResponse && is_array($apiResponse) && !isset($apiResponse['error'])) {
                log_message('info', 'Successfully retrieved dashboard overview from API');
                $overviewData = $this->mapApiResponseToViewData($apiResponse);
            } else {
                $errorMessage = $apiResponse['message'] ?? 'Unknown API error';
                log_message('warning', 'Failed to retrieve dashboard overview: ' . $errorMessage . ' - Using sample data');
                
                // Use sample data for testing
                $overviewData = $this->getSampleDashboardData();
            }
        } catch (\Exception $e) {
            log_message('error', 'Dashboard overview exception: ' . $e->getMessage() . ' - Using sample data');
            
            // Use sample data for testing
            $overviewData = $this->getSampleDashboardData();
        }

        $data = [
            'title' => 'Dashboard',
            'ambassador' => $ambassador,
            'overviewData' => $overviewData,
        ];

        return $this->render('ambassador/dashboard', $data);
    }
    
    /**
     * Map API response data to view data structure
     */
    private function mapApiResponseToViewData($apiResponse)
    {
        $overviewData = [
            // Ambassador data
            'ambassador' => $apiResponse['ambassador'] ?? [],
            
            // Map metrics - try multiple possible structures
            'metrics' => [
                'total_referrals' => $apiResponse['metrics']['total_referrals'] ?? $apiResponse['quick_stats']['total_referrals'] ?? 0,
                'completed_registrations' => $apiResponse['metrics']['completed_registrations'] ?? 0,
                'this_month_referrals' => $apiResponse['metrics']['this_month_referrals'] ?? $apiResponse['quick_stats']['this_week'] ?? 0,
                'conversion_rate' => $apiResponse['metrics']['conversion_rate'] ?? floatval(str_replace('%', '', $apiResponse['quick_stats']['completion_rate'] ?? 0))
            ],
            
            // Map participant breakdown - check multiple sources
            'participant_breakdown' => [
                'by_category' => $apiResponse['participant_breakdown']['by_category'] ?? [
                    'fully_funded' => 0,
                    'self_funded' => 0
                ],
                'by_nationality' => $apiResponse['participant_breakdown']['by_nationality'] ?? [],
                'by_institution_type' => $apiResponse['participant_breakdown']['by_institution_type'] ?? []
            ],
            
            // Map other sections
            'payment_summary' => $apiResponse['payment_summary'] ?? [
                'total_revenue_generated' => 0,
                'paid_participants' => 0,
                'pending_payments' => 0,
                'payment_completion_rate' => 0,
                'average_payment_amount' => 0
            ],
            'performance_insights' => $apiResponse['performance_insights'] ?? [
                'best_performing_month' => 'N/A',
                'registration_trend' => 'stable',
                'engagement_score' => 0,
                'quality_score' => 0
            ],
            'achievements' => $apiResponse['achievements'] ?? [],
            'program' => $apiResponse['program'] ?? [],
            'quick_stats' => $apiResponse['quick_stats'] ?? [],
            
            // Recent activity sample - check multiple possible sources
            'recent_activity_sample' => array_slice($apiResponse['recent_activity'] ?? $apiResponse['participants'] ?? [], 0, 5)
        ];
        
        // If we don't have breakdown data, try to calculate from recent_activity
        $activityData = $apiResponse['recent_activity'] ?? $apiResponse['participants'] ?? [];
        
        if ((empty($overviewData['participant_breakdown']['by_nationality']) || 
             $overviewData['participant_breakdown']['by_category']['fully_funded'] == 0) && 
            !empty($activityData)) {
            
            log_message('info', 'Calculating participant breakdown from recent activity data (' . count($activityData) . ' entries)');
            
            $completedRegistrations = 0;
            $nationalityCount = [];
            $institutionCount = [];
            $fullyFunded = 0;
            $selfFunded = 0;
            
            foreach ($activityData as $participant) {
                // Count completed registrations
                if (isset($participant['form_completed']) && $participant['form_completed']) {
                    $completedRegistrations++;
                }
                
                // Count by nationality - clean up HTML tags
                $nationality = strip_tags($participant['nationality'] ?? '');
                $nationality = trim($nationality);
                
                // Additional cleanup for common HTML artifacts
                $nationality = preg_replace('/^<font[^>]*>/', '', $nationality);
                $nationality = preg_replace('/<\/font>$/', '', $nationality);
                $nationality = preg_replace('/\s+/', ' ', $nationality);
                $nationality = trim($nationality);
                
                if ($nationality && $nationality !== 'N/A' && $nationality !== '' && strlen($nationality) > 1) {
                    $nationalityCount[$nationality] = ($nationalityCount[$nationality] ?? 0) + 1;
                }
                
                // Count by institution type (if available)
                $institution = $participant['institution_type'] ?? 
                             $participant['institution'] ?? 
                             'Other Institution';
                $institutionCount[$institution] = ($institutionCount[$institution] ?? 0) + 1;
                
                // For now, assume most are fully funded (can be adjusted based on real data)
                $fullyFunded++;
            }
            
            // Update calculated values
            if ($completedRegistrations > 0 && $overviewData['metrics']['completed_registrations'] == 0) {
                $overviewData['metrics']['completed_registrations'] = $completedRegistrations;
            }
            
            if (!empty($nationalityCount)) {
                arsort($nationalityCount);
                $overviewData['participant_breakdown']['by_nationality'] = array_slice($nationalityCount, 0, 10, true);
            }
            
            if (!empty($institutionCount)) {
                arsort($institutionCount);
                $overviewData['participant_breakdown']['by_institution_type'] = $institutionCount;
            }
            
            // Update category counts if they're empty
            if ($overviewData['participant_breakdown']['by_category']['fully_funded'] == 0) {
                $overviewData['participant_breakdown']['by_category'] = [
                    'fully_funded' => $fullyFunded,
                    'self_funded' => $selfFunded
                ];
            }
            
            log_message('info', 'Calculated breakdown: ' . $completedRegistrations . ' completed, ' . 
                       count($nationalityCount) . ' countries, ' . $fullyFunded . ' fully funded');
        }
        
        return $overviewData;
    }
}
