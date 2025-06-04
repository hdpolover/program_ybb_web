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
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Invalid request method. This endpoint only accepts AJAX requests.'
            ]);
        }
        
        // Get version IDs from request
        $version1Id = $this->request->getGet('version1');
        $version2Id = $this->request->getGet('version2');
        
        // Validate input
        if (empty($version1Id) || empty($version2Id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Both version1 and version2 parameters are required.'
            ]);
        }
        
        if ($version1Id === $version2Id) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Cannot compare a version with itself.'
            ]);
        }
        
        try {
            // Get the current participant ID from session
            $participantId = session()->get('current_participant_id');
            
            if (!$participantId) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 401,
                    'message' => 'Authentication required. Please log in again.'
                ]);
            }
            
            log_message('info', "[AjaxHandler::compareAbstractVersions] Comparing versions {$version1Id} and {$version2Id} for participant {$participantId}");
            
            // Fetch version data from external API
            $version1Data = $this->makeGetRequest("/abstracts/versions/{$version1Id}");
            $version2Data = $this->makeGetRequest("/abstracts/versions/{$version2Id}");
            
            // Verify both versions belong to the same participant
            if (!$this->verifyVersionAccess($version1Data, $participantId) || 
                !$this->verifyVersionAccess($version2Data, $participantId)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 403,
                    'message' => 'You do not have permission to access these versions.'
                ]);
            }
            
            // Prepare comparison data
            $comparisonData = [
                'version1' => $this->formatVersionForComparison($version1Data),
                'version2' => $this->formatVersionForComparison($version2Data),
                'differences' => $this->calculateDifferences($version1Data, $version2Data)
            ];
            
            log_message('info', "[AjaxHandler::compareAbstractVersions] Comparison successful");
            
            return $this->response->setJSON([
                'status' => 200,
                'message' => 'Version comparison retrieved successfully.',
                'data' => $comparisonData
            ]);
            
        } catch (\Exception $e) {
            log_message('error', "[AjaxHandler::compareAbstractVersions] Exception: " . $e->getMessage());
            log_message('error', "[AjaxHandler::compareAbstractVersions] Stack trace: " . $e->getTraceAsString());
            
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 500,
                'message' => 'An error occurred while comparing versions: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Verify that a version belongs to the specified participant
     * 
     * @param array $versionData
     * @param string $participantId
     * @return bool
     */
    private function verifyVersionAccess($versionData, $participantId)
    {
        // Check if version data contains participant information
        if (isset($versionData['participant_id'])) {
            return $versionData['participant_id'] == $participantId;
        }
        
        // If no direct participant ID, check if we can access via abstract
        if (isset($versionData['abstract_id'])) {
            try {
                $abstractData = $this->makeGetRequest("/abstracts/{$versionData['abstract_id']}");
                return isset($abstractData['participant_id']) && $abstractData['participant_id'] == $participantId;
            } catch (\Exception $e) {
                log_message('warning', "[AjaxHandler::verifyVersionAccess] Could not verify access: " . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Format version data for comparison display
     * 
     * @param array $versionData
     * @return array
     */
    private function formatVersionForComparison($versionData)
    {
        return [
            'id' => $versionData['id'] ?? '',
            'version_number' => $versionData['version_number'] ?? '1',
            'title' => $versionData['title'] ?? 'Untitled',
            'content' => $versionData['content'] ?? '',
            'keywords' => $versionData['keywords'] ?? '',
            'refs' => $versionData['refs'] ?? '',
            'status' => $versionData['status'] ?? 'draft',
            'created_at' => $versionData['created_at'] ?? date('Y-m-d H:i:s'),
            'updated_at' => $versionData['updated_at'] ?? date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Calculate differences between two versions
     * 
     * @param array $version1
     * @param array $version2
     * @return array
     */
    private function calculateDifferences($version1, $version2)
    {
        $differences = [];
        
        // Fields to compare
        $fieldsToCompare = ['title', 'content', 'keywords', 'refs', 'status'];
        
        foreach ($fieldsToCompare as $field) {
            $value1 = $version1[$field] ?? '';
            $value2 = $version2[$field] ?? '';
            
            if ($value1 !== $value2) {
                $differences[$field] = [
                    'changed' => true,
                    'old_value' => $value1,
                    'new_value' => $value2,
                    'change_type' => $this->getChangeType($value1, $value2)
                ];
            } else {
                $differences[$field] = [
                    'changed' => false,
                    'value' => $value1
                ];
            }
        }
        
        return $differences;
    }
    
    /**
     * Determine the type of change between two values
     * 
     * @param string $oldValue
     * @param string $newValue
     * @return string
     */
    private function getChangeType($oldValue, $newValue)
    {
        if (empty($oldValue) && !empty($newValue)) {
            return 'added';
        } elseif (!empty($oldValue) && empty($newValue)) {
            return 'removed';
        } else {
            return 'modified';
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
