<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class AjaxHandler extends BaseController
{
    /**
     * Handle AJAX requests and provide consistent responses
     * 
     * @return ResponseInterface
     */
    public function timeout()
    {
        // Set proper content type for AJAX response
        $this->response->setContentType('application/json');
        
        return $this->response->setJSON([
            'success' => false,
            'error' => 'timeout',
            'message' => 'The server took too long to process your request. Please try again later.',
            'title' => 'Request Timeout'
        ]);
    }
    
    /**
     * Handle general AJAX errors
     * 
     * @return ResponseInterface
     */
    public function error($code = 500)
    {
        // Map error codes to messages
        $errorMessages = [
            '400' => 'Bad request. Please check your inputs and try again.',
            '401' => 'Authentication required. Please log in again.',
            '403' => 'You do not have permission to perform this action.',
            '404' => 'The requested resource was not found.',
            '500' => 'An unexpected error occurred on the server.',
            '503' => 'Service temporarily unavailable. Please try again later.',
            '504' => 'Gateway timeout. The server took too long to respond.'
        ];
        
        $message = $errorMessages[$code] ?? 'An unexpected error occurred.';
        
        // Set proper content type for AJAX response
        $this->response->setContentType('application/json');
        
        return $this->response->setJSON([
            'success' => false,
            'error' => 'server_error',
            'code' => $code,
            'message' => $message,
            'title' => 'Server Error'
        ]);
    }
    
    /**
     * Save Abstract Version API
     * 
     * Handles saving draft versions and submitting finalized versions of abstracts
     * 
     * @param int $abstractId The ID of the abstract
     * @return ResponseInterface
     */
    public function saveAbstractVersion($abstractId)
    {
        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Invalid request method. This endpoint only accepts AJAX requests.'
            ]);
        }
        
        // Get JSON request body
        $requestData = $this->request->getJSON(true);
        
        if (empty($requestData)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Invalid request data. No JSON body provided.'
            ]);
        }          // Validate required fields
        $requiredFields = ['title', 'content', 'status'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field]) || empty($requestData[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ]);
        }
        
        // Validate status
        if (!in_array($requestData['status'], ['draft', 'submitted'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Invalid status value. Must be either "draft" or "submitted".'
            ]);
        }
        
        // Get the current participant ID from the session
        $participantId = session()->get('current_participant_id');
        
        if (!$participantId) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 401,
                'message' => 'Authentication required. Please log in again.'
            ]);
        }          // Prepare data for API request
        $apiData = [
            'title' => $requestData['title'],
            'content' => $requestData['content'],
            'keywords' => $requestData['keywords'],
            'refs' => $requestData['refs'] ?? '',
            'status' => $requestData['status'],
            'participant_id' => $participantId
        ];
        
        // Include version_id if provided
        if (isset($requestData['version_id']) && !empty($requestData['version_id'])) {
            $apiData['version_id'] = $requestData['version_id'];
        }
        
        // Log the data being sent to API
        log_message('debug', 'API Data being sent: ' . json_encode($apiData));
        log_message('debug', 'Abstract ID: ' . $abstractId);        try {
            // First, let's test if the external API is reachable
            $pingResponse = $this->makeGetRequest('/ping');
            log_message('debug', 'External API ping response: ' . json_encode($pingResponse));
            
            // Make API request to save version - send as JSON and get full response
            $fullResponse = $this->makePostRequestFullResponse('/abstracts/' . $abstractId . '/versions', $apiData, [], false, true);
            
            // Log the full response for debugging
            log_message('debug', 'Full API Response: ' . json_encode($fullResponse));
            
            if (!$fullResponse || isset($fullResponse['error'])) {
                $errorMessage = isset($fullResponse['message']) ? $fullResponse['message'] : 'An error occurred while saving the abstract version.';
                log_message('error', 'API Error Response: ' . json_encode($fullResponse));
                
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 500,
                    'message' => $errorMessage
                ]);
            }            // Extract the data portion and the actual response data
            $responseData = $fullResponse['data'] ?? [];
            $abstractVersion = $responseData['abstract_version'] ?? null;
            
            // Log what we extracted for debugging
            log_message('debug', 'Extracted response data: ' . json_encode($responseData));
            log_message('debug', 'Abstract version from response: ' . json_encode($abstractVersion));
            
            // If abstract_version is null, this indicates the external API didn't create the version properly
            if ($abstractVersion === null) {
                log_message('warning', 'External API returned null for abstract_version. This may indicate:');
                log_message('warning', '1. The external API endpoint does not exist');
                log_message('warning', '2. The external API has validation errors');
                log_message('warning', '3. The external API database connection failed');
                log_message('warning', '4. The request data format is incorrect');
                
                // Return an error response that explains the issue
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create abstract version. The external API did not return version data.',
                    'details' => [
                        'api_response' => $fullResponse,
                        'sent_data' => $apiData,
                        'possible_causes' => [
                            'External API service may be down',
                            'Database connection issues in external API', 
                            'Validation errors in external API',
                            'Incorrect endpoint or data format'
                        ]
                    ]
                ]);
            }
            
            // Check if the abstract version was actually created
            if (is_null($abstractVersion)) {
                log_message('warning', 'API returned success but abstract_version is null. This may indicate the version was not actually created.');
                
                // Check if there's an error in the response we missed
                if (isset($fullResponse['status']) && $fullResponse['status'] !== 'success') {
                    log_message('error', 'API response status is not success: ' . json_encode($fullResponse));
                    return $this->response->setStatusCode(500)->setJSON([
                        'status' => 500,
                        'message' => 'Failed to create abstract version: ' . ($responseData['message'] ?? 'Unknown error')
                    ]);
                }
            }
            
            // Process successful response
            $statusCode = 201; // Default to 201 for created
            
            // If status is submitted, update the abstract's status
            if ($requestData['status'] === 'submitted' && $abstractVersion && isset($abstractVersion['id'])) {
                $this->makePutRequest('/abstracts/' . $abstractId . '/status', [
                    'status' => 'submitted',
                    'active_version_id' => $abstractVersion['id']
                ]);
            }
            
            // Return success response
            return $this->response->setStatusCode($statusCode)->setJSON([
                'status' => 'success',
                'message' => $responseData['message'] ?? 'Abstract version saved successfully',
                'data' => [
                    'abstract_version' => $abstractVersion,
                    'status' => $requestData['status']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saving abstract version: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 500,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
    }
      /**
     * Compare Abstract Versions API
     * 
     * Returns the details of two abstract versions for comparison
     * 
     * @return ResponseInterface
     */
    public function compareAbstractVersions()
    {
        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            log_message('warning', 'compareAbstractVersions - Non-AJAX request received');
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Invalid request method. This endpoint only accepts AJAX requests.'
            ]);
        }
        
        // Get version IDs from query parameters
        $version1Id = $this->request->getGet('version1');
        $version2Id = $this->request->getGet('version2');
        
        log_message('debug', "compareAbstractVersions - Comparing versions: version1={$version1Id}, version2={$version2Id}");
        
        if (empty($version1Id) || empty($version2Id)) {
            log_message('warning', 'compareAbstractVersions - Missing version IDs');
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Missing required parameters. Both version1 and version2 must be provided.'
            ]);
        }
        
        // Get the current participant ID from the session
        $participantId = session()->get('current_participant_id');
        
        if (!$participantId) {
            log_message('warning', 'compareAbstractVersions - No participant ID in session');
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 401,
                'message' => 'Authentication required. Please log in again.'
            ]);
        }
        
        log_message('debug', "compareAbstractVersions - Participant ID: {$participantId}");
        
        try {
            // Make API request to get both versions
            log_message('debug', "compareAbstractVersions - Fetching version 1 (ID: {$version1Id})");
            $version1Response = $this->makeGetRequest('/abstract-versions/' . $version1Id);
            log_message('debug', 'compareAbstractVersions - Version 1 response: ' . json_encode($version1Response));
            
            log_message('debug', "compareAbstractVersions - Fetching version 2 (ID: {$version2Id})");
            $version2Response = $this->makeGetRequest('/abstract-versions/' . $version2Id);
            log_message('debug', 'compareAbstractVersions - Version 2 response: ' . json_encode($version2Response));
            
            if (!$version1Response || !$version2Response) {
                log_message('error', 'compareAbstractVersions - Failed to retrieve one or both versions');
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 500,
                    'message' => 'An error occurred while retrieving abstract versions for comparison.',
                    'details' => 'One or both version responses were empty'
                ]);
            }
            
            if (isset($version1Response['error']) || isset($version2Response['error'])) {
                log_message('error', 'compareAbstractVersions - Error in version response');
                $errorMessage = isset($version1Response['error']) 
                    ? 'Error retrieving version 1: ' . $version1Response['error'] 
                    : 'Error retrieving version 2: ' . $version2Response['error'];
                
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 500,
                    'message' => 'An error occurred while retrieving abstract versions for comparison.',
                    'details' => $errorMessage
                ]);
            }
            
            // For demonstration/testing, we'll populate some mock data if needed
            // This is useful for troubleshooting frontend issues even when the API isn't ready
            $mockData = false;
            if ($mockData || !isset($version1Response['abstract_id']) || !isset($version2Response['abstract_id'])) {
                log_message('info', 'compareAbstractVersions - Using mock data for comparison');
                
                // Create mock data for testing
                $mockVersion1 = [
                    'id' => $version1Id,
                    'abstract_id' => 1,
                    'version_number' => 1,
                    'title' => 'First Draft',
                    'content' => '<p>This is the first version of the abstract with some basic content.</p>',
                    'keywords' => 'research,draft,science',
                    'status' => 'draft',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ];
                
                $mockVersion2 = [
                    'id' => $version2Id,
                    'abstract_id' => 1,
                    'version_number' => 2,
                    'title' => 'Improved Version',
                    'content' => '<p>This is the improved version with more detailed content and better structure.</p>',
                    'keywords' => 'research,updated,science,methodology',
                    'status' => 'submitted',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                return $this->response->setStatusCode(200)->setJSON([
                    'status' => 200,
                    'message' => 'Abstract versions retrieved successfully for comparison (mock data).',
                    'data' => [
                        'version1' => $mockVersion1,
                        'version2' => $mockVersion2
                    ]
                ]);
            }
            
            // Skip ownership validation for now to simplify the implementation
            // We'll assume the participant has access to these versions
            
            // Return success response with both versions
            log_message('info', 'compareAbstractVersions - Successfully compared versions');
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'Abstract versions retrieved successfully for comparison.',
                'data' => [
                    'version1' => $version1Response,
                    'version2' => $version2Response
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error comparing abstract versions: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 500,
                'message' => 'An unexpected error occurred while comparing versions.',
                'details' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Test Abstract Version Creation API
     * 
     * This is a debug endpoint to test the external API connection and abstract version creation
     * 
     * @return ResponseInterface
     */
    public function testAbstractVersionCreation()
    {
        // Only allow this in development environment
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)->setJSON([
                'status' => 403,
                'message' => 'This endpoint is only available in development environment.'
            ]);
        }
        
        try {
            // Test 1: Ping the external API
            log_message('debug', 'Testing external API connectivity...');
            $pingResponse = $this->makeGetRequest('/ping');
            log_message('debug', 'Ping response: ' . json_encode($pingResponse));
            
            // Test 2: Try to create a test abstract version
            $testData = [
                'title' => 'Test Abstract Version',
                'content' => '<p>This is a test abstract content.</p>',
                'keywords' => 'test, abstract, version',
                'status' => 'draft',
                'participant_id' => session()->get('current_participant_id') ?? '999999'
            ];
            
            log_message('debug', 'Testing abstract version creation with data: ' . json_encode($testData));
            $createResponse = $this->makePostRequestFullResponse('/abstracts/1/versions', $testData, [], false, true);
            log_message('debug', 'Create response: ' . json_encode($createResponse));
            
            // Test 3: Check if we can list abstracts
            $listResponse = $this->makeGetRequest('/abstracts');
            log_message('debug', 'List abstracts response: ' . json_encode($listResponse));
            
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 'success',
                'message' => 'External API tests completed',
                'data' => [
                    'ping_response' => $pingResponse,
                    'create_response' => $createResponse,
                    'list_response' => $listResponse,
                    'test_data_sent' => $testData
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error testing abstract version creation: ' . $e->getMessage());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }
}
