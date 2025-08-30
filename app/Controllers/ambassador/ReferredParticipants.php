<?php

namespace App\Controllers\ambassador;
use App\Controllers\BaseController;

class ReferredParticipants extends BaseController
{
    public function index()
    {
        $ambassador = session()->get('user');

        if (!$ambassador) {
            return redirect()->to('/ambassadors/sign-in')->with('error', 'Please sign in to continue');
        }

        // Try to get program details, but don't fail if not available
        $programId = $ambassador['program_id'] ?? null;
        $program = null;

        if ($programId) {
            $program = $this->makeGetRequest('/programs/' . $programId, [], false);
            if (isset($program['id'])) {
                $ambassador['program'] = $program;
            }
        }
        
        // If no program found, use default program data
        if (!$program || !isset($program['id'])) {
            $ambassador['program'] = [
                'id' => 6,
                'name' => 'Japan Youth Summit',
                'description' => 'International innovation competition and youth summit'
            ];
        }
        
        // Get pagination parameters from request
        $page = (int)($this->request->getGet('page') ?? 1);
        $limit = min((int)($this->request->getGet('limit') ?? 20), 100);
        $search = $this->request->getGet('search') ?? '';
        $formStatus = $this->request->getGet('form_status') ?? $this->request->getGet('status') ?? '';
        $category = $this->request->getGet('category') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? 'registration_date';
        $sortOrder = strtoupper($this->request->getGet('sort_order') ?? 'DESC');

        // Build query parameters for API
        $params = [
            'page' => $page,
            'limit' => $limit
        ];
        
        if (!empty($search)) $params['search'] = $search;
        if (!empty($formStatus)) $params['form_status'] = $formStatus;
        if (!empty($category)) $params['category'] = $category;
        if (!empty($sortBy)) $params['sort_by'] = $sortBy;
        if (!empty($sortOrder)) $params['sort_order'] = $sortOrder;
        
        // Use the documented dashboard participants API endpoint
        $queryString = http_build_query($params);
        $endpoint = '/ambassador/dashboard/participants?' . $queryString;
        
        $participantsResponse = $this->makeGetRequest($endpoint, [], true);
        
        // Initialize with empty data structure matching API response
        $participantsData = [
            'participants' => [],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => 0,
                'last_page' => 1,
                'from' => 0,
                'to' => 0
            ]
        ];
        
        // makeGetRequest already extracts the 'data' portion from API response
        if ($participantsResponse && isset($participantsResponse['participants'])) {
            $participantsData = $participantsResponse;
            log_message('info', 'Successfully retrieved participants data from API. Count: ' . count($participantsData['participants']));
            log_message('debug', 'Participants data structure: ' . json_encode(array_keys($participantsData)));
        } else {
            log_message('warning', 'Failed to fetch participants data from API. Response: ' . json_encode($participantsResponse));
            // Also log what we expected vs what we got
            if ($participantsResponse) {
                log_message('debug', 'API response keys: ' . json_encode(array_keys($participantsResponse)));
            }
        }
        
        // Calculate statistics from participant data
        $statistics = [
            'total_referrals' => $participantsData['pagination']['total'] ?? 0,
            'countries_count' => 0,
            'this_month_count' => 0,
            'completed_count' => 0,
            'incomplete_count' => 0
        ];
        
        // Calculate statistics from current page participants (for display purposes)
        if (!empty($participantsData['participants'])) {
            $participants = $participantsData['participants'];
            $countries = array_unique(array_filter(array_map(function($p) {
                $nationality = $p['nationality'] ?? '';
                // Clean up nationality field (remove HTML tags)
                return strip_tags(trim($nationality));
            }, $participants)));
            
            $statistics['countries_count'] = count($countries);
            
            // Count this month registrations
            $thisMonth = date('Y-m');
            $thisMonthCount = 0;
            $submittedCount = 0;
            
            foreach ($participants as $participant) {
                $regDate = $participant['registration_date'] ?? '';
                if (strpos($regDate, $thisMonth) === 0) {
                    $thisMonthCount++;
                }
                
                if (($participant['form_status'] ?? '') === 'submitted') {
                    $submittedCount++;
                }
            }
            
            $statistics['this_month_count'] = $thisMonthCount;
            $statistics['completed_count'] = $submittedCount;
            $statistics['incomplete_count'] = count($participants) - $submittedCount;
        }
        
        $data = [
            'title' => 'Referred Participants',
            'ambassador' => $ambassador,
            'participantsData' => $participantsData,
            'statistics' => $statistics,
            'currentPage' => $page,
            'searchTerm' => $search,
            'statusFilter' => $formStatus,
            'categoryFilter' => $category,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ];

        return $this->render('ambassador/referred-participants', $data);
    }

    /**
     * Participants List API endpoint
     * GET /ambassadors/dashboard/participants
     */
    public function participants()
    {
        try {
            $ambassador = session()->get('user');
            if (!$ambassador) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ]);
            }

            // Get query parameters (support all filters in available_filters)
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = min((int)($this->request->getGet('per_page') ?? $this->request->getGet('limit') ?? 20), 100);
            $search = $this->request->getGet('search') ?? '';
            $formStatus = $this->request->getGet('form_status') ?? $this->request->getGet('status') ?? '';
            $category = $this->request->getGet('category') ?? '';
            $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
            $sortOrder = strtoupper($this->request->getGet('sort_order') ?? 'DESC');

            // Build query parameters for API
            $params = [
                'page' => $page,
                'per_page' => $perPage,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ];
            if (!empty($search)) $params['search'] = $search;
            if (!empty($formStatus)) $params['form_status'] = $formStatus;
            if (!empty($category)) $params['category'] = $category;

            $queryString = http_build_query($params);
            $endpoint = '/ambassador/dashboard/participants?' . $queryString;
            log_message('info', 'Fetching participants with endpoint: ' . $endpoint);

            $apiResponse = $this->makeGetRequest($endpoint, [], true);

            // Compose response in the new structure
            if ($apiResponse && isset($apiResponse['participants'])) {
                $response = [
                    'status' => 'success',
                    'message' => 'Participants retrieved successfully',
                    'data' => [
                        'participants' => $apiResponse['participants'],
                        'total_participants' => $apiResponse['pagination']['total'] ?? count($apiResponse['participants']),
                        'filtered_count' => $apiResponse['pagination']['total'] ?? count($apiResponse['participants']),
                        'pagination' => [
                            'current_page' => $apiResponse['pagination']['current_page'] ?? $page,
                            'per_page' => $apiResponse['pagination']['per_page'] ?? $perPage,
                            'total' => $apiResponse['pagination']['total'] ?? count($apiResponse['participants']),
                            'last_page' => $apiResponse['pagination']['last_page'] ?? 1,
                            'from' => $apiResponse['pagination']['from'] ?? 0,
                            'to' => $apiResponse['pagination']['to'] ?? 0
                        ],
                        'filters_applied' => [
                            'page' => $page,
                            'per_page' => $perPage,
                            'search' => $search,
                            'form_status' => $formStatus,
                            'category' => $category,
                            'sort_by' => $sortBy,
                            'sort_order' => $sortOrder
                        ],
                        'available_filters' => [
                            'form_status' => ['not_started', 'in_progress', 'submitted'],
                            'category' => ['fully_funded', 'self_funded'],
                            'sort_by' => ['created_at', 'full_name', 'email', 'nationality', 'institution', 'form_status'],
                            'sort_order' => ['ASC', 'DESC']
                        ],
                        'last_updated' => date('Y-m-d H:i:s')
                    ]
                ];
                return $this->response->setJSON($response);
            } else {
                log_message('warning', 'Participants API call failed - service unavailable');
                return $this->response->setStatusCode(503)->setJSON([
                    'status' => 'error',
                    'message' => 'Participants data is currently unavailable. Please try again later.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Participants list error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve participants'
            ]);
        }
    }

    /**
     * Participant Payment Details API endpoint
     * GET /ambassador/dashboard/participant-payment/{participant_id}
     */
    public function participantPayment($participantId = null)
    {
        try {
            $ambassador = session()->get('user');
            if (!$ambassador) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ]);
            }

            if (!$participantId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Participant ID is required'
                ]);
            }

            // Call the external API for participant payment details
            $endpoint = '/ambassador/dashboard/participant-payment/' . $participantId;
            log_message('info', 'Fetching payment details with endpoint: ' . $endpoint);

            $apiResponse = $this->makeGetRequest($endpoint, [], true);

            if ($apiResponse) {
                log_message('info', 'Successfully retrieved payment details from API');
                // makeGetRequest returns only the 'data' portion, so wrap it in a proper response format
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Payment details retrieved successfully',
                    'data' => $apiResponse
                ]);
            } else {
                log_message('warning', 'Payment details API call failed - service unavailable');
                return $this->response->setStatusCode(503)->setJSON([
                    'status' => 'error',
                    'message' => 'Payment details are currently unavailable. Please try again later.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Payment details error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve payment details'
            ]);
        }
    }

}
