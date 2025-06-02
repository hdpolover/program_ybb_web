<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class AbstractPaper extends BaseController
{
    public function __construct()
    {
        // Helper for form and url functions
        helper(['form', 'url']);
    }

    public function index()
    {
        // Get participant ID from session
        $participantId = session()->get('current_participant_id');
        $abstractData = $this->makeGetRequest('/abstracts/participant/' . $participantId . '/details', [], false);

        log_message('info', 'Participant Data: ' . print_r($abstractData, true));

        // Build view data
        $data = [
            'title' => 'Abstract and Paper',
            'participant_data' => $abstractData
        ];

        return $this->render('participant/abstract-paper/index', $data);
    }

    public function create()
    {
        // Get available topics for abstract submission
        $topics = $this->getAvailableTopics();

        $data = [
            'title' => 'Create New Abstract',
            'topics' => $topics
        ];

        return $this->render('participant/abstract-paper/manage-abstract', $data);
    }

    public function edit($id, $versionId = 1)
    {
        // Add debug logging
        log_message('debug', "Edit method called with ID: {$id}, Version ID: {$versionId}");

        // Get abstract data by ID
        try {
            // Call API to get abstract data
            $abstract = $this->makeGetRequest('/abstracts/' . $id, [], false);

            if (!$abstract) {
                log_message('error', "Abstract not found with ID: {$id}");
                return redirect()->to('/abstract-paper')->with('error', 'Abstract not found.');
            }            
            
            // Get abstract versions
            $abstractVersions = $this->makeGetRequest('/abstracts/' . $id . '/versions', [], false);
            log_message('debug', "Abstract versions API response: " . json_encode($abstractVersions));

            // Find the specific version based on version ID
            $currentVersion = null;
            $latestVersion = null;

            if (!empty($abstractVersions)) {
                // Sort versions to ensure we have the latest one
                usort($abstractVersions, function ($a, $b) {
                    $a_version = isset($a['version_number']) ? (int)$a['version_number'] : 0;
                    $b_version = isset($b['version_number']) ? (int)$b['version_number'] : 0;
                    return $b_version - $a_version; // Descending order (latest first)
                });

                // The latest version will be the first one after sorting
                $latestVersion = $abstractVersions[0];
                $latestVersionNumber = isset($latestVersion['version_number']) ? (int)$latestVersion['version_number'] : 1;

                // If a specific version was requested
                if ($versionId > 1) {
                    // Search for the specific version by version_number
                    foreach ($abstractVersions as $version) {
                        if ($version['version_number'] == $versionId) {
                            $currentVersion = $version;
                            break;
                        }
                    }

                    // If the requested version is not the latest, redirect to the latest version
                    // with a notice that they should be working with the latest version
                    if ($currentVersion && (int)$currentVersion['version_number'] < $latestVersionNumber) {
                        return redirect()->to('/abstract-paper/edit/' . $id . '/' . $latestVersionNumber)
                            ->with('warning', 'You have been redirected to the latest version (' . $latestVersionNumber . ') of this abstract. Always edit the most recent version to avoid conflicts.')
                            ->with('warning_title', 'Using Latest Version');
                    }

                    // If requested version not found, use the latest one
                    if (!$currentVersion) {
                        $currentVersion = $latestVersion;
                        log_message('warning', 'Requested version ' . $versionId . ' not found for abstract ' . $id . '. Using latest version instead.');
                    }
                } else {
                    // Default to latest version if no specific version requested
                    $currentVersion = $latestVersion;
                }

                // Replace abstract data with the selected version data
                $abstract['current_version'] = $currentVersion;
            }            
            
            // Get available topics
            $topics = $this->getAvailableTopics();

            // abstract settings data
            $programId = $abstract['program_id'] ?? null;
            
            $abstractSettings = $this->makeGetRequest('/abstract-settings/program/' . $programId, [], false);

            $data = [
                'title' => 'Edit Abstract',
                'abstract' => $abstract,
                'abstractVersions' => $abstractVersions,
                'topics' => $topics,
                'abstractSettings' => $abstractSettings
            ];

            log_message('debug', "Rendering edit view with data: " . json_encode([
                'abstract_id' => $abstract['id'] ?? null,
                'version_count' => count($abstractVersions),
                'current_version_id' => $abstract['current_version']['id'] ?? null,
                'current_version_number' => $abstract['current_version']['version_number'] ?? null
            ]));

            return $this->render('participant/abstract-paper/manage-abstract', $data);
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch abstract: ' . $e->getMessage());
            return redirect()->to('/abstract-paper')->with('error', 'Unable to load abstract. Please try again later.');
        }
    }
    public function save()
    {
        // Check if this is a draft
        $isDraft = $this->request->getPost('status') === 'draft';

        // Define validation rules based on whether it's a draft or final submission
        if ($isDraft) {
            // For drafts, we only require topic and title
            $rules = [
                'abstract_topic_id' => 'required',
                'title' => 'required'
            ];
        } else {
            // For final submission, apply full validation
            $rules = [
                'abstract_topic_id' => 'required',
                'title' => 'required|min_length[5]',
                'content' => 'required|min_length[100]'
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Process form data using the required fields for API
        $data = [
            'program_id' => $this->request->getPost('program_id'),
            'primary_participant_id' => $this->request->getPost('primary_participant_id'),
            'abstract_topic_id' => $this->request->getPost('abstract_topic_id'),
            'title' => $this->request->getPost('title'),
            'keywords' => $this->request->getPost('keywords'),
            'content' => $this->request->getPost('content'),
            'status' => $this->request->getPost('status')
        ];

        try {
            // Call the API endpoint to save the abstract
            $response = $this->makePostRequest('/abstracts', $data, [], false, false);

            // Check if the response indicates an error
            if (isset($response['error'])) {
                $errorMessage = isset($response['message']) ? $response['message'] : 'An error occurred while saving your abstract.';
                $errorTitle = 'Submission Failed';

                log_message('error', 'API Error: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }

            // Check if we have a successful response with abstract data
            if (!isset($response['abstract'])) {
                $errorMessage = 'The server returned an unexpected response. Please try again later.';
                $errorTitle = 'Unexpected Response';

                log_message('error', 'Unexpected API Response: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }

            // Store abstract ID in session for reference
            if (isset($response['abstract']['id'])) {
                session()->set('last_abstract_id', $response['abstract']['id']);
            }

            // If save is successful, redirect with success message
            $message = $isDraft ?
                'Your abstract draft has been saved successfully. You can continue editing it later when you are ready to complete your submission.' :
                'Congratulations! Your abstract has been submitted successfully and is now pending review. You will be notified once the review process is complete.';

            // Include abstract ID in message if available
            if (isset($response['abstract']['id'])) {
                $abstractId = $response['abstract']['id'];
                $message .= $isDraft ?
                    " (Draft ID: {$abstractId})" :
                    " (Submission ID: {$abstractId})";
            }

            $title = $isDraft ? 'Draft Saved' : 'Submission Complete';

            // Get the first version for the flash data
            $currentVersion = !empty($response['abstract']['versions']) ? $response['abstract']['versions'][0] : null;

            // Save the abstract details in flash data to display in SweetAlert
            session()->setFlashdata('abstract_data', [
                'id' => $response['abstract']['id'] ?? 'N/A',
                'title' => $currentVersion['title'] ?? 'Your Abstract',
                'status' => $response['abstract']['status'] ?? ($isDraft ? 'draft' : 'submitted'),
            ]);

            return redirect()->to('/abstract-paper')->with('success', $message)->with('success_title', $title);
        } catch (\Exception $e) {
            // Get a user-friendly error message
            $errorMessage = $this->handleApiError($e, 'We encountered a problem while saving your abstract. Please try again later or contact support if the issue persists.');
            $errorTitle = 'Submission Error';

            log_message('error', 'Exception during abstract save: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
        }
    }
    public function update($id)
    {
        // Add debugging
        log_message('info', 'Update method called with ID: ' . $id);
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

        // Get abstract versions to verify we're updating the latest version
        $abstractVersions = $this->makeGetRequest('/abstracts/' . $id . '/versions', [], false);

        if (!empty($abstractVersions)) {
            // Sort versions by version_number in descending order
            usort($abstractVersions, function ($a, $b) {
                $a_version = isset($a['version_number']) ? (int)$a['version_number'] : 0;
                $b_version = isset($b['version_number']) ? (int)$b['version_number'] : 0;
                return $b_version - $a_version; // Descending order (latest first)
            });

            // Get the latest version number
            $latestVersionNumber = isset($abstractVersions[0]['version_number']) ? (int)$abstractVersions[0]['version_number'] : 1;
            $currentVersionNumber = (int)$this->request->getPost('version_number');

            // If the version being updated is not the latest, redirect to edit the latest version
            if ($currentVersionNumber < $latestVersionNumber) {
                return redirect()->to('/abstract-paper/edit/' . $id . '/' . $latestVersionNumber)
                    ->with('warning', 'You attempted to update an older version (' . $currentVersionNumber . ') of this abstract. 
                    You have been redirected to the latest version (' . $latestVersionNumber . '). Always edit the most recent version to avoid conflicts.')
                    ->with('warning_title', 'Using Latest Version');
            }
        }

        // Check if this is a draft
        $isDraft = $this->request->getPost('status') === 'draft'; // Define validation rules based on whether it's a draft or final submission
        if ($isDraft) {
            // For drafts, we only require topic and title
            $rules = [
                'abstract_topic_id' => 'required',
                'title' => 'required'
            ];
        } else {
            // For final submission, apply full validation (keywords are permit_empty according to API)
            $rules = [
                'abstract_topic_id' => 'required',
                'title' => 'required|min_length[5]',
                'content' => 'required|min_length[100]'
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Process form data using the required fields for API
        $data = [
            'program_id' => $this->request->getPost('program_id'),
            'primary_participant_id' => $this->request->getPost('primary_participant_id'),
            'abstract_topic_id' => $this->request->getPost('abstract_topic_id'),
            'title' => $this->request->getPost('title'),
            'keywords' => $this->request->getPost('keywords'),
            'content' => $this->request->getPost('content'),
            'status' => $this->request->getPost('status'),
            'version_id' => $this->request->getPost('version_id')
        ];
        try {
            // Use the correct API endpoint for saving/updating abstract versions
            $endpoint = '/abstracts/' . $id . '/save-version';
            log_message('info', 'Updating abstract version with endpoint: ' . $endpoint);
            log_message('info', 'Sending data: ' . json_encode($data));

            $response = $this->makePostRequest($endpoint, $data, [], false, false);

            // Check if the response indicates an error
            if (isset($response['error'])) {
                $errorMessage = isset($response['message']) ? $response['message'] : 'An error occurred while updating your abstract.';
                $errorTitle = 'Update Failed';

                log_message('error', 'API Error during update: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }

            // Check if we have a successful response with abstract data
            if (!isset($response['abstract'])) {
                $errorMessage = 'The server returned an unexpected response. Please try again later.';
                $errorTitle = 'Unexpected Response';

                log_message('error', 'Unexpected API Response during update: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }            // If update is successful, redirect with success message
            $message = $isDraft ?
                'Your abstract draft has been updated successfully. You can continue editing it later when you are ready to complete your submission.' :
                'Your abstract has been updated successfully and will be reviewed. You will be notified once the review process is complete.';

            // Include abstract ID in message if available
            if (isset($response['abstract']['id'])) {
                $abstractId = $response['abstract']['id'];
                $message .= $isDraft ?
                    " (Draft ID: {$abstractId})" :
                    " (Submission ID: {$abstractId})";
            }

            // Add version information to the message
            $versionId = $this->request->getPost('version_id');
            $versionNumber = $this->request->getPost('version_number');
            if ($versionId && $versionNumber) {
                log_message('info', "Updated abstract version {$versionNumber} (ID: {$versionId}) for abstract {$id}");
                $message .= " (Version: {$versionNumber})";
            }
            $title = $isDraft ? 'Draft Updated' : 'Update Complete';

            // Get the updated version details
            $currentVersion = null;
            if (!empty($response['abstract']['versions'])) {
                // Look for the version we just updated
                $versionId = $this->request->getPost('version_id');
                foreach ($response['abstract']['versions'] as $version) {
                    if ($version['id'] == $versionId) {
                        $currentVersion = $version;
                        break;
                    }
                }

                // If not found, use the first version
                if (!$currentVersion && !empty($response['abstract']['versions'])) {
                    $currentVersion = $response['abstract']['versions'][0];
                }
            }

            // Save the abstract details in flash data to display in SweetAlert
            session()->setFlashdata('abstract_data', [
                'id' => $response['abstract']['id'] ?? $id,
                'title' => $currentVersion['title'] ?? $data['title'],
                'status' => $response['abstract']['status'] ?? $data['status'],
            ]);

            return redirect()->to('/abstract-paper')->with('success', $message)->with('success_title', $title);
        } catch (\Exception $e) {
            // Get a user-friendly error message
            $errorMessage = $this->handleApiError($e, 'We encountered a problem while updating your abstract. Please try again later or contact support if the issue persists.');
            $errorTitle = 'Update Error';

            log_message('error', 'Exception during abstract update: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
        }
    }

    /**
     * Add a new author to an abstract
     *
     * @return mixed
     */
    public function addAuthor()
    {
        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            // Regular form submission
            $abstractId = $this->request->getPost('abstract_id');

            // Validate input
            $rules = [
                'full_name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|max_length[255]',
                'institution' => 'required|min_length[3]|max_length[255]',
                'role' => 'required|in_list[co_author,presenting]',
            ];

            if (!$this->validate($rules)) {
                // Return with validation errors
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Prepare data for API
            $authorData = [
                'full_name' => $this->request->getPost('full_name'),
                'email' => $this->request->getPost('email'),
                'institution' => $this->request->getPost('institution'),
                'address' => $this->request->getPost('address'),
                'is_presenting' => ($this->request->getPost('role') === 'presenting') ? 1 : 0
            ];

            // Make API request to add author
            $response = $this->makePostRequest('/abstracts/' . $abstractId . '/authors', $authorData);

            if (isset($response['success']) && $response['success']) {
                return redirect()->to(base_url('abstract-paper'))->with('success', 'Author added successfully.');
            }

            return redirect()->back()->withInput()->with('error', $response['message'] ?? 'Failed to add author.');
        }

        // Handle AJAX request
        $abstractId = $this->request->getPost('abstract_id');

        // Validate input
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|max_length[255]',
            'institution' => 'required|min_length[3]|max_length[255]',
            'role' => 'required|in_list[co_author,presenting]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
                'message' => 'Please fix the errors in the form.'
            ]);
        }

        // Prepare data for API
        $authorData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'institution' => $this->request->getPost('institution'),
            'address' => $this->request->getPost('address'),
            'is_presenting' => ($this->request->getPost('role') === 'presenting') ? 1 : 0
        ];

        // Make API request to add author
        $response = $this->makePostRequest('/abstracts/' . $abstractId . '/authors', $authorData);

        return $this->response->setJSON($response);
    }

    /**
     * Update an existing author
     *
     * @return mixed
     */
    public function updateAuthor()
    {
        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            // Regular form submission
            $authorId = $this->request->getPost('author_id');
            $abstractId = $this->request->getPost('abstract_id');

            // Validate input
            $rules = [
                'full_name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|max_length[255]',
                'institution' => 'required|min_length[3]|max_length[255]',
                'role' => 'permit_empty|in_list[co_author,presenting]',
            ];

            if (!$this->validate($rules)) {
                // Return with validation errors
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Prepare data for API
            $authorData = [
                'full_name' => $this->request->getPost('full_name'),
                'email' => $this->request->getPost('email'),
                'institution' => $this->request->getPost('institution'),
                'address' => $this->request->getPost('address')
            ];

            // Only update role if provided and not primary author
            if ($this->request->getPost('role')) {
                $authorData['is_presenting'] = ($this->request->getPost('role') === 'presenting') ? 1 : 0;
            }

            // Make API request to update author
            $response = $this->makePutRequest('/abstracts/' . $abstractId . '/authors/' . $authorId, $authorData);

            if (isset($response['success']) && $response['success']) {
                return redirect()->to(base_url('abstract-paper'))->with('success', 'Author updated successfully.');
            }

            return redirect()->back()->withInput()->with('error', $response['message'] ?? 'Failed to update author.');
        }

        // Handle AJAX request
        $authorId = $this->request->getPost('author_id');
        $abstractId = $this->request->getPost('abstract_id');

        // Validate input
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|max_length[255]',
            'institution' => 'required|min_length[3]|max_length[255]',
            'role' => 'permit_empty|in_list[co_author,presenting]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
                'message' => 'Please fix the errors in the form.'
            ]);
        }

        // Prepare data for API
        $authorData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'institution' => $this->request->getPost('institution'),
            'address' => $this->request->getPost('address')
        ];

        // Only update role if provided and not primary author
        if ($this->request->getPost('role')) {
            $authorData['is_presenting'] = ($this->request->getPost('role') === 'presenting') ? 1 : 0;
        }

        // Make API request to update author
        $response = $this->makePutRequest('/abstracts/' . $abstractId . '/authors/' . $authorId, $authorData);

        return $this->response->setJSON($response);
    }

    /**
     * Delete an author
     *
     * @return mixed
     */
    public function deleteAuthor()
    {
        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            // Regular form submission
            $authorId = $this->request->getPost('author_id');
            $abstractId = $this->request->getPost('abstract_id');

            // Make API request to delete author
            $response = $this->makeDeleteRequest('/abstracts/' . $abstractId . '/authors/' . $authorId);

            if (isset($response['success']) && $response['success']) {
                return redirect()->to(base_url('abstract-paper'))->with('success', 'Author deleted successfully.');
            }

            return redirect()->back()->with('error', $response['message'] ?? 'Failed to delete author.');
        }

        // Handle AJAX request
        $authorId = $this->request->getPost('author_id');
        $abstractId = $this->request->getPost('abstract_id');

        // Make API request to delete author
        $response = $this->makeDeleteRequest('/abstracts/' . $abstractId . '/authors/' . $authorId);

        return $this->response->setJSON($response);
    }

    /**
     * Get available topics for abstract submission
     */
    private function getAvailableTopics()
    {
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        try {
            // In a real application, you would fetch topics from an API
            $topics = $this->makeGetRequest('/abstract-topics/program/' . $currentProgramId, [], false, false);
            return $topics;
        } catch (\Exception $e) {
            // Handle error (e.g., log it)
            log_message('error', 'Failed to fetch topics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper method to handle API errors and extract meaningful messages
     */
    private function handleApiError(\Exception $e, $defaultMessage = 'An error occurred. Please try again later.')
    {
        log_message('error', 'API Error: ' . $e->getMessage());

        // First, check if message contains specific errors we can interpret
        $message = $e->getMessage();

        // Handle timeout errors
        if (
            strpos($message, 'timeout') !== false ||
            strpos($message, 'Connection timed out') !== false
        ) {
            return 'The server is taking too long to respond. This could be due to high traffic or connectivity issues. Please try again later.';
        }

        // Handle connection errors
        if (
            strpos($message, 'Connection refused') !== false ||
            strpos($message, 'Could not resolve host') !== false
        ) {
            return 'Unable to connect to the server. Please check your internet connection and try again later.';
        }

        // Check if the message contains JSON response
        if (strpos($message, '{') !== false) {
            try {
                // Extract the JSON portion
                preg_match('/{.*}/s', $message, $matches);
                if (!empty($matches[0])) {
                    $responseData = json_decode($matches[0], true);

                    // If we have a structured error message from the API
                    if (isset($responseData['message'])) {
                        return $responseData['message'];
                    } elseif (isset($responseData['error'])) {
                        return $responseData['error'];
                    } elseif (isset($responseData['errors']) && is_array($responseData['errors'])) {
                        // Join multiple errors
                        return implode(', ', $responseData['errors']);
                    }
                }
            } catch (\Exception $parseException) {
                // If we can't parse the error, just use the default message
                log_message('error', 'Failed to parse API error response: ' . $parseException->getMessage());
            }
        }

        return $defaultMessage;
    }
}
