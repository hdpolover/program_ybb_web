<?php

namespace App\Controllers\Api;
use App\Controllers\BaseController;

/**
 * Ambassador API Controller for Admin Features
 * 
 * This controller handles administrative operations for ambassadors
 * including CRUD operations, referral tracking, and link generation.
 * Uses session-based authentication for admin features.
 */
class AmbassadorsApiController extends BaseController
{
    /**
     * Get all ambassadors with pagination
     * GET /api/ambassadors
     */
    public function index()
    {
        try {
            // Check admin authentication
            if (!session()->get('isLoggedIn') || !session()->get('selectedProgram')) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Admin authentication required'
                ]);
            }

            $programId = session()->get('selectedProgram')['id'];

            // Get query parameters
            $limit = min((int)($this->request->getGet('limit') ?? 10), 100);
            $offset = (int)($this->request->getGet('offset') ?? 0);
            $status = $this->request->getGet('status');

            // Build query parameters
            $params = [
                'program_id' => $programId,
                'limit' => $limit,
                'offset' => $offset
            ];
            
            if ($status !== null) {
                $params['status'] = $status;
            }

            // Make API call to get ambassadors
            $queryString = http_build_query($params);
            $endpoint = '/ambassadors?' . $queryString;
            
            $response = $this->makeGetRequest($endpoint, [], false);

            if ($response && is_array($response)) {
                $ambassadors = $response;
                $total = count($ambassadors);

                // Calculate pagination
                $currentPage = floor($offset / $limit) + 1;
                $totalPages = ceil($total / $limit);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Ambassadors retrieved successfully',
                    'data' => [
                        'data' => $ambassadors,
                        'total' => $total,
                        'program_id' => $programId,
                        'pagination' => [
                            'limit' => $limit,
                            'offset' => $offset,
                            'current_page' => $currentPage,
                            'total_pages' => $totalPages
                        ]
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'No ambassadors found',
                    'data' => [
                        'data' => [],
                        'total' => 0,
                        'program_id' => $programId,
                        'pagination' => [
                            'limit' => $limit,
                            'offset' => $offset,
                            'current_page' => 1,
                            'total_pages' => 0
                        ]
                    ]
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Admin ambassadors list error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve ambassadors'
            ]);
        }
    }

    /**
     * Get ambassador details with shareable link
     * GET /api/ambassadors/{id}
     */
    public function show($id)
    {
        try {
            // Check admin authentication
            if (!session()->get('isLoggedIn') || !session()->get('selectedProgram')) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Admin authentication required'
                ]);
            }

            $programId = session()->get('selectedProgram')['id'];

            // Get ambassador details
            $ambassador = $this->makeGetRequest('/ambassadors/' . $id, [], false);

            if (!$ambassador || !isset($ambassador['id'])) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Ambassador not found'
                ]);
            }

            // Verify ambassador belongs to current program
            if ($ambassador['program_id'] != $programId) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Ambassador not found in selected program'
                ]);
            }

            // Get shareable link from backend API
            $linkResponse = $this->makeGetRequest('/ambassadors/' . $id . '/generate-link', [], false);
            $shareableLink = $linkResponse['data']['referral_link'] ?? $linkResponse['referral_link'] ?? '';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Ambassador retrieved successfully',
                'data' => [
                    'ambassador' => $ambassador,
                    'program_id' => $programId,
                    'shareable_link' => $shareableLink
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin ambassador details error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve ambassador details'
            ]);
        }
    }

    /**
     * Get ambassador referrals
     * GET /api/ambassadors/{id}/referrals
     */
    public function referrals($id)
    {
        try {
            // Check admin authentication
            if (!session()->get('isLoggedIn') || !session()->get('selectedProgram')) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Admin authentication required'
                ]);
            }

            $programId = session()->get('selectedProgram')['id'];

            // Get ambassador details first
            $ambassador = $this->makeGetRequest('/ambassadors/' . $id, [], false);

            if (!$ambassador || !isset($ambassador['id'])) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Ambassador not found'
                ]);
            }

            // Verify ambassador belongs to current program
            if ($ambassador['program_id'] != $programId) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'Ambassador not found in selected program'
                ]);
            }

            // Get referrals
            $referrals = $this->makeGetRequest('/participants?ambassador_id=' . $id, [], false);

            if (!$referrals) {
                $referrals = [];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Referrals retrieved successfully',
                'data' => [
                    'referrals' => $referrals,
                    'total' => count($referrals)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin ambassador referrals error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve referrals'
            ]);
        }
    }

    /**
     * Generate referral link for ambassador
     * GET /api/ambassadors/{id}/generate-link
     * 
     * Calls backend API to get encrypted referral link
     */
    public function generateLink($id)
    {
        try {
            // Check admin authentication
            if (!session()->get('isLoggedIn') || !session()->get('selectedProgram')) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Admin authentication required'
                ]);
            }

            $programId = session()->get('selectedProgram')['id'];

            // Call backend API to generate the encrypted link
            $response = $this->makeGetRequest('/ambassadors/' . $id . '/generate-link', [], false);

            if (!$response || isset($response['error'])) {
                log_message('error', 'Backend API generate-link failed: ' . json_encode($response));
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => $response['message'] ?? 'Failed to generate referral link from backend'
                ]);
            }

            // Backend returns the encrypted link - just pass it through
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Referral link generated successfully',
                'data' => $response['data'] ?? $response
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin generate link error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to generate referral link'
            ]);
        }
    }

    /**
     * Validate encrypted referral code
     * GET /api/ambassadors/check-query
     * 
     * Calls backend API to decrypt and validate the query
     */
    public function checkQuery()
    {
        try {
            $encryptedQuery = $this->request->getGet('encrypted_query');

            if (!$encryptedQuery) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Encrypted query parameter required'
                ]);
            }

            // Call backend API to decrypt and validate the query
            $response = $this->makeGetRequest('/ambassadors/check-query?encrypted_query=' . urlencode($encryptedQuery), [], false);

            if (!$response || isset($response['error'])) {
                log_message('error', 'Backend API check-query failed: ' . json_encode($response));
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => $response['message'] ?? 'Failed to validate referral code'
                ]);
            }

            // Backend returns ambassador info - pass it through
            return $this->response->setJSON($response);

        } catch (\Exception $e) {
            log_message('error', 'Check query error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to validate referral code'
            ]);
        }
    }
}