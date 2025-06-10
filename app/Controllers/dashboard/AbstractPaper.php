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
        log_message('info', '[AbstractPaper::index] Starting index method');

        // Get participant ID from session
        $participantId = session()->get('current_participant_id');
        log_message('info', "[AbstractPaper::index] Participant ID from session: {$participantId}");

        if (!$participantId) {
            log_message('error', '[AbstractPaper::index] No participant ID found in session');
            return redirect()->to('/dashboard')->with('error', 'Participant session not found. Please login again.');
        }

        try {
            log_message('info', "[AbstractPaper::index] Making API request to: /abstracts/participant/{$participantId}/details");
            $abstractData = $this->makeGetRequest('/abstracts/participant/' . $participantId . '/details', [], false);

            log_message('info', '[AbstractPaper::index] API Response received');
            log_message('debug', '[AbstractPaper::index] Participant Data: ' . json_encode($abstractData));

            // Process subtheme data for highlighting and validation
            $selectedSubtheme = null;
            $subthemeHighlight = null;
            $subthemeWarning = null;

            if (isset($abstractData['selected_subtheme']) && !empty($abstractData['selected_subtheme'])) {
                $selectedSubtheme = $abstractData['selected_subtheme'];
                log_message('info', "[AbstractPaper::index] Selected subtheme found: {$selectedSubtheme['subtheme_name']}");

                // Create highlight data for the selected subtheme
                $subthemeHighlight = [
                    'id' => $selectedSubtheme['id'],
                    'name' => $selectedSubtheme['subtheme_name'],
                    'description' => $selectedSubtheme['subtheme_description'] ?? '',
                    'program_subtheme_id' => $selectedSubtheme['program_subtheme_id'],
                    'is_active' => $selectedSubtheme['is_active'] ?? '1'
                ];
            } else {
                log_message('warning', '[AbstractPaper::index] No selected subtheme found for participant');
                $subthemeWarning = [
                    'title' => 'Subtheme Selection Required',
                    'message' => 'You have not selected a subtheme yet. Please select a subtheme before creating or managing your abstract.',
                    'type' => 'warning'
                ];
            }

            // Check if participant is eligible for abstract submission
            $eligibleForAbstract = $abstractData['eligible_for_abstract'] ?? false;
            if (!$eligibleForAbstract && !$subthemeWarning) {
                $subthemeWarning = [
                    'title' => 'Abstract Submission Not Available',
                    'message' => 'You are not currently eligible for abstract submission. Please complete the required prerequisites.',
                    'type' => 'info'
                ];
            }

            // Build view data
            $data = [
                'title' => 'Abstract and Paper',
                'participant_data' => $abstractData,
                'selected_subtheme' => $selectedSubtheme,
                'subtheme_highlight' => $subthemeHighlight,
                'subtheme_warning' => $subthemeWarning,
                'eligible_for_abstract' => $eligibleForAbstract
            ];

            log_message('info', '[AbstractPaper::index] Rendering view with enhanced subtheme data');
            if ($subthemeHighlight) {
                log_message('info', "[AbstractPaper::index] Highlighting selected subtheme: {$subthemeHighlight['name']}");
            }
            if ($subthemeWarning) {
                log_message('info', "[AbstractPaper::index] Showing subtheme warning: {$subthemeWarning['message']}");
            }

            return $this->render('participant/abstract-paper/index', $data);
        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::index] Exception occurred: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::index] Stack trace: ' . $e->getTraceAsString());
            return redirect()->to('/dashboard')->with('error', 'Unable to load abstract data. Please try again later.');
        }
    }

    public function create()
    {
        log_message('info', '[AbstractPaper::create] Starting create method');

        try {

            // Get abstract settings for current program
            $programId = session()->get('current_program_id');
            log_message('info', "[AbstractPaper::create] Getting abstract settings for program ID: {$programId}");

            $abstractSettings = null;
            if ($programId) {
                try {
                    $abstractSettings = $this->makeGetRequest('/abstract-settings/program/' . $programId, [], false);
                    log_message('info', '[AbstractPaper::create] Abstract settings retrieved successfully');
                    log_message('debug', '[AbstractPaper::create] Abstract settings: ' . json_encode($abstractSettings));
                } catch (\Exception $e) {
                    log_message('warning', '[AbstractPaper::create] Failed to fetch abstract settings: ' . $e->getMessage());
                    // Continue without settings - use defaults in view
                }
            } else {
                log_message('warning', '[AbstractPaper::create] No program ID in session, using default abstract settings');
            }

            // Get program subtheme selected for participant
            $selectedSubtheme = null;
            $participantId = session()->get('current_participant_id');
            log_message('info', "[AbstractPaper::create] Getting participant subtheme for participant ID: {$participantId}");

            if ($participantId) {
                try {
                    $response = $this->makeGetRequest('/participants/' . $participantId . '/subthemes', [], false);
                    // The response is not an array, directly use it as selectedSubtheme
                    $selectedSubtheme = $response;
                    log_message('info', '[AbstractPaper::create] Participant subtheme retrieved successfully');
                    log_message('debug', '[AbstractPaper::create] Participant subtheme: ' . json_encode($selectedSubtheme));
                } catch (\Exception $e) {
                    log_message('warning', '[AbstractPaper::create] Failed to fetch participant subtheme: ' . $e->getMessage());
                }
            } else {
                log_message('warning', '[AbstractPaper::create] No participant ID in session, unable to fetch subtheme');
            }

            $data = [
                'title' => 'Create New Abstract',
                'selectedSubtheme' => $selectedSubtheme,
                'abstractSettings' => $abstractSettings
            ];

            log_message('info', '[AbstractPaper::create] Rendering create view');
            return $this->render('participant/abstract-paper/manage-abstract', $data);
        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::create] Exception occurred: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::create] Stack trace: ' . $e->getTraceAsString());
            return redirect()->to('/abstract-paper')->with('error', 'Unable to load the create form. Please try again later.');
        }
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

            // Check if editing is allowed
            $abstractStatus = strtolower($abstract['status'] ?? 'draft');
            $hasFeedback = !empty($abstract['reviewers']);
            $canEdit = ($abstractStatus !== 'submitted') || $hasFeedback;

            if (!$canEdit) {
                log_message('warning', "[AbstractPaper::edit] Edit access blocked - Abstract ID: {$id} is submitted without feedback");
                return redirect()->to('/abstract-paper/view/' . $id)
                    ->with('warning', 'This abstract has been submitted and cannot be edited until reviewers provide feedback requiring revisions.')
                    ->with('warning_title', 'Editing Restricted');
            }

            log_message('info', "[AbstractPaper::edit] Edit access granted - Status: {$abstractStatus}, Has feedback: " . ($hasFeedback ? 'yes' : 'no'));

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
            }            // abstract settings data
            $programId = $abstract['program_id'] ?? null;

            $abstractSettings = $this->makeGetRequest('/abstract-settings/program/' . $programId, [], false);

            // Get participant subtheme data
            $selectedSubtheme = null;
            $participantId = session()->get('current_participant_id');
            if ($participantId) {
                try {
                    $response = $this->makeGetRequest('/participants/' . $participantId . '/subthemes', [], false);
                    $selectedSubtheme = $response;
                    log_message('info', '[AbstractPaper::edit] Participant subtheme retrieved successfully');
                } catch (\Exception $e) {
                    log_message('warning', '[AbstractPaper::edit] Failed to fetch participant subtheme: ' . $e->getMessage());
                }
            }

            $data = [
                'title' => 'Edit Abstract',
                'abstract' => $abstract,
                'abstractVersions' => $abstractVersions,
                'abstractSettings' => $abstractSettings,
                'selectedSubtheme' => $selectedSubtheme
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
        log_message('info', '[AbstractPaper::save] Starting save method');
        log_message('info', '[AbstractPaper::save] Request method: ' . $this->request->getMethod());
        log_message('debug', '[AbstractPaper::save] POST data: ' . json_encode($this->request->getPost()));

        // Check if this is a draft
        $isDraft = $this->request->getPost('status') === 'draft';
        log_message('info', "[AbstractPaper::save] Is draft: " . ($isDraft ? 'yes' : 'no'));        // Define validation rules based on whether it's a draft or final submission
        if ($isDraft) {
            // For drafts, we only require title
            $rules = [
                'title' => 'required'
            ];
            log_message('info', '[AbstractPaper::save] Using draft validation rules');
        } else {
            // For final submission, apply full validation
            $rules = [
                'title' => 'required',
                'content' => 'required',
                'keywords' => 'required',
                'refs' => 'required'
            ];
            log_message('info', '[AbstractPaper::save] Using full validation rules');
        }

        log_message('debug', '[AbstractPaper::save] Validation rules: ' . json_encode($rules));

        if (!$this->validate($rules)) {
            log_message('warning', '[AbstractPaper::save] Validation failed');
            log_message('debug', '[AbstractPaper::save] Validation errors: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        log_message('info', '[AbstractPaper::save] Validation passed');        // Process form data using the required fields for API
        $data = [
            'program_id' => $this->request->getPost('program_id'),
            'primary_participant_id' => $this->request->getPost('primary_participant_id'),
            'title' => $this->request->getPost('title'),
            'keywords' => $this->request->getPost('keywords'),
            'content' => $this->request->getPost('content'),
            'refs' => $this->request->getPost('refs') ?: '', // Include refs field, default to empty string if null
            'status' => $this->request->getPost('status')
        ];

        log_message('info', '[AbstractPaper::save] Prepared data for API');
        log_message('debug', '[AbstractPaper::save] API data: ' . json_encode($data));

        try {
            // Call the API endpoint to save the abstract
            log_message('info', '[AbstractPaper::save] Making API request to: /abstracts');
            $response = $this->makePostRequest('/abstracts', $data, [], false, false);

            log_message('info', '[AbstractPaper::save] API response received');
            log_message('debug', '[AbstractPaper::save] API response: ' . json_encode($response));

            // Check if the response indicates an error
            if (isset($response['error'])) {
                $errorMessage = isset($response['message']) ? $response['message'] : 'An error occurred while saving your abstract.';
                $errorTitle = 'Submission Failed';

                log_message('error', '[AbstractPaper::save] API Error: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }

            // Check if we have a successful response with abstract data
            if (!isset($response['abstract'])) {
                $errorMessage = 'The server returned an unexpected response. Please try again later.';
                $errorTitle = 'Unexpected Response';

                log_message('error', '[AbstractPaper::save] Unexpected API Response: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }
            log_message('info', '[AbstractPaper::save] Abstract saved successfully');

            // Store abstract ID in session for reference
            $abstractId = null;
            if (isset($response['abstract']['id'])) {
                $abstractId = $response['abstract']['id'];
                session()->set('last_abstract_id', $abstractId);
                log_message('info', '[AbstractPaper::save] Stored abstract ID in session: ' . $abstractId);
            }

            // Get the first version for the flash data
            $currentVersion = !empty($response['abstract']['versions']) ? $response['abstract']['versions'][0] : null;

            // Prepare detailed success message
            $title = $isDraft ? 'Draft Saved Successfully!' : 'Abstract Submitted Successfully!';
            $abstractTitle = $currentVersion['title'] ?? $this->request->getPost('title') ?? 'Your Abstract';

            $message = $isDraft ?
                'Your abstract draft has been saved and you can continue editing it later.' :
                'Congratulations! Your abstract has been submitted and is now pending review.';

            // Save comprehensive abstract details in flash data for enhanced SweetAlert
            session()->setFlashdata('abstract_success', [
                'title' => $abstractTitle,
                'status' => $response['abstract']['status'] ?? ($isDraft ? 'draft' : 'submitted'),
                'is_draft' => $isDraft,
                'message' => $message,
                'version_number' => $currentVersion['version_number'] ?? '1',
                'created_at' => $currentVersion['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $currentVersion['updated_at'] ?? date('Y-m-d H:i:s')
            ]);

            log_message('info', '[AbstractPaper::save] Redirecting to abstract-paper with enhanced success data');
            return redirect()->to('/abstract-paper')->with('success', $message)->with('success_title', $title);
        } catch (\Exception $e) {
            // Get a user-friendly error message
            $errorMessage = $this->handleApiError($e, 'We encountered a problem while saving your abstract. Please try again later or contact support if the issue persists.');
            $errorTitle = 'Submission Error';

            log_message('error', '[AbstractPaper::save] Exception during abstract save: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::save] Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
        }
    }

    /**
     * Update an existing abstract
     *
     * @param int $id Abstract ID
     * @return mixed
     */    public function update($id)
    {
        log_message('info', "[AbstractPaper::update] Starting update method for abstract ID: {$id}");
        log_message('info', '[AbstractPaper::update] Request method: ' . $this->request->getMethod());
        log_message('debug', '[AbstractPaper::update] POST data: ' . json_encode($this->request->getPost()));

        try {
            // First, get the abstract to check its status and if editing is allowed
            log_message('info', "[AbstractPaper::update] Checking edit permissions for abstract ID: {$id}");
            $abstract = $this->makeGetRequest('/abstracts/' . $id, [], false);

            if (!$abstract) {
                log_message('error', "[AbstractPaper::update] Abstract not found with ID: {$id}");
                return redirect()->to('/abstract-paper')->with('error', 'Abstract not found.');
            }

            // Check if editing is allowed
            $abstractStatus = strtolower($abstract['status'] ?? 'draft');
            $hasFeedback = !empty($abstract['reviewers']);
            $canEdit = ($abstractStatus !== 'submitted') || $hasFeedback;

            if (!$canEdit) {
                log_message('warning', "[AbstractPaper::update] Edit attempt blocked - Abstract ID: {$id} is submitted without feedback");
                return redirect()->to('/abstract-paper/view/' . $id)
                    ->with('error', 'This abstract has been submitted and cannot be edited until reviewers provide feedback requiring revisions.');
            }

            log_message('info', "[AbstractPaper::update] Edit permission granted - Status: {$abstractStatus}, Has feedback: " . ($hasFeedback ? 'yes' : 'no'));

            // Get abstract versions to verify we're updating the latest version
            log_message('info', "[AbstractPaper::update] Fetching versions for abstract ID: {$id}");
            $abstractVersions = $this->makeGetRequest('/abstracts/' . $id . '/versions', [], false);
            log_message('debug', "[AbstractPaper::update] Abstract versions API response: " . json_encode($abstractVersions));

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

                log_message('info', "[AbstractPaper::update] Latest version: {$latestVersionNumber}, Current version: {$currentVersionNumber}");

                // If the version being updated is not the latest, redirect to edit the latest version
                if ($currentVersionNumber < $latestVersionNumber) {
                    log_message('warning', "[AbstractPaper::update] Attempt to update older version {$currentVersionNumber}, redirecting to latest version {$latestVersionNumber}");
                    return redirect()->to('/abstract-paper/edit/' . $id . '/' . $latestVersionNumber)
                        ->with('warning', 'You attempted to update an older version (' . $currentVersionNumber . ') of this abstract. 
                        You have been redirected to the latest version (' . $latestVersionNumber . '). Always edit the most recent version to avoid conflicts.')
                        ->with('warning_title', 'Using Latest Version');
                }
            } else {
                log_message('warning', "[AbstractPaper::update] No versions found for abstract ID: {$id}");
            }

            // Check if this is a draft
            $isDraft = $this->request->getPost('status') === 'draft';
            log_message('info', "[AbstractPaper::update] Is draft: " . ($isDraft ? 'yes' : 'no'));            // Define validation rules based on whether it's a draft or final submission
            if ($isDraft) {
                // For drafts, we only require title
                $rules = [
                    'title' => 'required'
                ];
                log_message('info', '[AbstractPaper::update] Using draft validation rules');
            } else {
                // For final submission, apply full validation (keywords are permit_empty according to API)
                $rules = [
                    'title' => 'required',
                    'content' => 'required',
                    'keywords' => 'required',
                    'refs' => 'required'
                ];
                log_message('info', '[AbstractPaper::update] Using full validation rules');
            }

            log_message('debug', '[AbstractPaper::update] Validation rules: ' . json_encode($rules));

            if (!$this->validate($rules)) {
                log_message('warning', '[AbstractPaper::update] Validation failed');
                log_message('debug', '[AbstractPaper::update] Validation errors: ' . json_encode($this->validator->getErrors()));
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            log_message('info', '[AbstractPaper::update] Validation passed');            // Process form data using the required fields for API
            $data = [
                'program_id' => $this->request->getPost('program_id'),
                'primary_participant_id' => $this->request->getPost('primary_participant_id'),
                'title' => $this->request->getPost('title'),
                'keywords' => $this->request->getPost('keywords'),
                'content' => $this->request->getPost('content'),
                'refs' => $this->request->getPost('refs') ?: '', // Include refs field, default to empty string if null
                'status' => $this->request->getPost('status'),
                'version_id' => $this->request->getPost('version_id')
            ];

            log_message('info', '[AbstractPaper::update] Prepared data for API');
            log_message('debug', '[AbstractPaper::update] API data: ' . json_encode($data));

            // Use the correct API endpoint for saving/updating abstract versions
            $endpoint = '/abstracts/' . $id . '/save-version';
            log_message('info', '[AbstractPaper::update] Making API request to: ' . $endpoint);

            $response = $this->makePostRequest($endpoint, $data, [], false, false);

            log_message('info', '[AbstractPaper::update] API response received');
            log_message('debug', '[AbstractPaper::update] API response: ' . json_encode($response));

            // Check if the response indicates an error
            if (isset($response['error'])) {
                $errorMessage = isset($response['message']) ? $response['message'] : 'An error occurred while updating your abstract.';
                $errorTitle = 'Update Failed';

                log_message('error', '[AbstractPaper::update] API Error during update: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }

            // Check if we have a successful response with abstract data
            if (!isset($response['abstract_version'])) {
                $errorMessage = 'The server returned an unexpected response. Please try again later.';
                $errorTitle = 'Unexpected Response';

                log_message('error', '[AbstractPaper::update] Unexpected API Response during update: ' . json_encode($response));
                return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
            }
            log_message('info', '[AbstractPaper::update] Abstract updated successfully');

            // Get abstract ID for reference
            $abstractId = $id;
            if (isset($response['abstract_version']['abstract_id'])) {
                $abstractId = $response['abstract_version']['abstract_id'];
                log_message('info', "[AbstractPaper::update] Updated abstract ID: {$abstractId}");
            }

            // Get the updated version details
            $currentVersion = $response['abstract_version'];
            $versionId = $this->request->getPost('version_id');
            $versionNumber = $this->request->getPost('version_number');

            // Prepare detailed success message
            $title = $isDraft ? 'Draft Updated Successfully!' : 'Abstract Updated Successfully!';
            $abstractTitle = $currentVersion['title'] ?? $this->request->getPost('title') ?? 'Your Abstract';

            $message = $isDraft ?
                'Your abstract draft has been updated and you can continue editing it later.' :
                'Your abstract has been updated successfully and will be reviewed.';

            if ($versionId && $versionNumber) {
                log_message('info', "[AbstractPaper::update] Updated abstract version {$versionNumber} (ID: {$versionId}) for abstract {$id}");
                $message .= " (Version: {$versionNumber})";
            }            // Save comprehensive abstract details in flash data for enhanced SweetAlert
            session()->setFlashdata('abstract_success', [
                'title' => $abstractTitle,
                'status' => $response['abstract']['status'] ?? $data['status'],
                'is_draft' => $isDraft,
                'message' => $message,
                'version_number' => $versionNumber,
                'created_at' => $currentVersion['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $currentVersion['updated_at'] ?? date('Y-m-d H:i:s')
            ]);

            log_message('info', '[AbstractPaper::update] Redirecting to abstract-paper with enhanced success data');
            return redirect()->to('/abstract-paper')->with('success', $message)->with('success_title', $title);
        } catch (\Exception $e) {
            // Get a user-friendly error message
            $errorMessage = $this->handleApiError($e, 'We encountered a problem while updating your abstract. Please try again later or contact support if the issue persists.');
            $errorTitle = 'Update Error';

            log_message('error', '[AbstractPaper::update] Exception during abstract update: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::update] Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', $errorMessage)->with('error_title', $errorTitle);
        }
    }

    /**
     * Validate Author (New endpoint)
     * Validates if an author email can be added to an abstract
     *
     * @param int $abstractId Abstract ID
     * @return mixed
     */
    public function validateAuthor($abstractId)
    {
        log_message('info', "[AbstractPaper::validateAuthor] Starting validateAuthor method for abstract ID: {$abstractId}");

        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This endpoint only accepts AJAX requests.'
            ]);
        }

        // Check if current user is the primary author and if editing is allowed
        if (!$this->canManageAuthors($abstractId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You do not have permission to manage authors for this abstract. Only the primary author can add, edit, or remove authors.'
            ]);
        }

        // Validate email input
        $email = $this->request->getPost('email');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please provide a valid email address.'
            ]);
        }        try {
            // Make API request to validate author
            $response = $this->makePostRequest('/abstracts/' . $abstractId . '/authors/validate', [
                'email' => $email
            ]);

            log_message('debug', '[AbstractPaper::validateAuthor] API response: ' . json_encode($response));

            // The external API returns the response directly, not wrapped in a status object
            // Handle the case where the response contains conflict information
            if (isset($response['can_add'])) {
                if ($response['can_add']) {
                    // Email can be added
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Author can be added to this abstract',
                        'data' => [
                            'can_add' => true,
                            'email' => $email,
                            'abstract_id' => $abstractId
                        ]
                    ]);
                } else {
                    // Email cannot be added due to conflict
                    $conflictMessage = 'This author email is already assigned to another abstract in the same program. One participant can only be assigned to one abstract at a time per program.';
                    
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $conflictMessage,
                        'data' => [
                            'can_add' => false,
                            'existing_abstract_id' => $response['existing_abstract_id'] ?? null,
                            'conflict_reason' => $response['conflict_reason'] ?? 'email_already_in_program'
                        ]
                    ]);
                }
            }

            // Handle other API errors
            if (isset($response['error'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $response['message'] ?? 'Validation failed'
                ]);
            }

            // Return the validation result from the API as-is if it follows expected format
            return $this->response->setJSON($response);

        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::validateAuthor] Exception occurred: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while validating the author email. Please try again.'
            ]);
        }
    }

    /**
     * Add a new author to an abstract
     *
     * @return mixed
     */
    public function addAuthor()
    {
        log_message('info', '[AbstractPaper::addAuthor] Starting addAuthor method');

        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            // Regular form submission
            $abstractId = $this->request->getPost('abstract_id');

            // Check if current user is the primary author and if editing is allowed
            if (!$this->canManageAuthors($abstractId)) {
                return redirect()->to('/abstract-paper/view/' . $abstractId)
                    ->with('error', 'You do not have permission to manage authors for this abstract. Only the primary author can add, edit, or remove authors.');
            }            // Validate input
            $rules = [
                'full_name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|max_length[255]',
                'institution' => 'required|min_length[3]|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                // Return with validation errors            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            // Prepare data for API
            $authorData = [
                'full_name' => $this->request->getPost('full_name'),
                'email' => $this->request->getPost('email'),
                'institution' => $this->request->getPost('institution'),
                'participant_id' => $this->request->getPost('participant_id') // Include participant_id if author is a registered participant
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

        // Check if current user is the primary author and if editing is allowed
        if (!$this->canManageAuthors($abstractId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to manage authors for this abstract. Only the primary author can add, edit, or remove authors.'
            ]);
        }        // Validate input
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|max_length[255]',
            'institution' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
                'message' => 'Please fix the errors in the form.'
            ]);
        }        // Prepare data for API
        $authorData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'institution' => $this->request->getPost('institution'),
            'participant_id' => $this->request->getPost('participant_id') // Include participant_id if author is a registered participant
        ];        // Make API request to add author
        $response = $this->makePostRequest('/abstracts/' . $abstractId . '/authors', $authorData);

        // Handle specific error cases for email conflicts
        if (isset($response['error']) && isset($response['message'])) {
            $errorMessage = $response['message'];
            
            // Check if it's an email conflict error
            if (strpos($errorMessage, 'already assigned to another abstract') !== false ||
                strpos($errorMessage, 'email_already_in_program') !== false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This author email is already assigned to another abstract in the same program. One participant can only be assigned to one abstract at a time per program.'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage
            ]);
        }

        return $this->response->setJSON($response);
    }

    /**
     * Update an existing author
     *
     * @return mixed
     */
    public function updateAuthor()
    {
        log_message('info', '[AbstractPaper::updateAuthor] Starting updateAuthor method');

        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            // Regular form submission
            $authorId = $this->request->getPost('author_id');
            $abstractId = $this->request->getPost('abstract_id');

            // Check if current user is the primary author and if editing is allowed
            if (!$this->canManageAuthors($abstractId)) {
                return redirect()->to('/abstract-paper/view/' . $abstractId)
                    ->with('error', 'You do not have permission to manage authors for this abstract. Only the primary author can add, edit, or remove authors.');
            }            // Validate input
            $rules = [
                'full_name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|max_length[255]',
                'institution' => 'required|min_length[3]|max_length[255]',
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
                'participant_id' => $this->request->getPost('participant_id') // Include participant_id if author is a registered participant
            ];

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

        // Check if current user is the primary author and if editing is allowed
        if (!$this->canManageAuthors($abstractId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to manage authors for this abstract. Only the primary author can add, edit, or remove authors.'
            ]);
        }        // Validate input
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|max_length[255]',
            'institution' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
                'message' => 'Please fix the errors in the form.'
            ]);
        }        // Prepare data for API
        $authorData = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'institution' => $this->request->getPost('institution'),
            'participant_id' => $this->request->getPost('participant_id') // Include participant_id if author is a registered participant
        ];

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
        log_message('info', '[AbstractPaper::deleteAuthor] Starting deleteAuthor method');

        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            // Regular form submission
            $authorId = $this->request->getPost('author_id');
            $abstractId = $this->request->getPost('abstract_id');

            // Check if current user is the primary author and if editing is allowed
            if (!$this->canManageAuthors($abstractId)) {
                return redirect()->to('/abstract-paper/view/' . $abstractId)
                    ->with('error', 'You do not have permission to manage authors for this abstract. Only the primary author can add, edit, or remove authors.');
            }

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

        // Check if current user is the primary author and if editing is allowed
        if (!$this->canManageAuthors($abstractId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to manage authors for this abstract. Only the primary author can add, edit, or remove authors.'
            ]);
        }

        // Make API request to delete author
        $response = $this->makeDeleteRequest('/abstracts/' . $abstractId . '/authors/' . $authorId);

        return $this->response->setJSON($response);
    }

    /**
     * Helper method to handle API errors and extract meaningful messages
     */
    private function handleApiError(\Exception $e, $defaultMessage = 'An error occurred. Please try again later.')
    {
        log_message('error', '[AbstractPaper::handleApiError] Processing API error');
        log_message('error', '[AbstractPaper::handleApiError] Exception message: ' . $e->getMessage());
        log_message('error', '[AbstractPaper::handleApiError] Exception code: ' . $e->getCode());
        log_message('debug', '[AbstractPaper::handleApiError] Stack trace: ' . $e->getTraceAsString());

        // First, check if message contains specific errors we can interpret
        $message = $e->getMessage();

        // Handle timeout errors
        if (
            strpos($message, 'timeout') !== false ||
            strpos($message, 'Connection timed out') !== false
        ) {
            log_message('warning', '[AbstractPaper::handleApiError] Timeout error detected');
            return 'The server is taking too long to respond. This could be due to high traffic or connectivity issues. Please try again later.';
        }

        // Handle connection errors
        if (
            strpos($message, 'Connection refused') !== false ||
            strpos($message, 'Could not resolve host') !== false
        ) {
            log_message('warning', '[AbstractPaper::handleApiError] Connection error detected');
            return 'Unable to connect to the server. Please check your internet connection and try again later.';
        }

        // Check if the message contains JSON response
        if (strpos($message, '{') !== false) {
            try {
                log_message('debug', '[AbstractPaper::handleApiError] Attempting to parse JSON from error message');
                // Extract the JSON portion
                preg_match('/{.*}/s', $message, $matches);
                if (!empty($matches[0])) {
                    $responseData = json_decode($matches[0], true);
                    log_message('debug', '[AbstractPaper::handleApiError] Parsed JSON: ' . json_encode($responseData));

                    // If we have a structured error message from the API
                    if (isset($responseData['message'])) {
                        log_message('info', '[AbstractPaper::handleApiError] Using API message: ' . $responseData['message']);
                        return $responseData['message'];
                    } elseif (isset($responseData['error'])) {
                        log_message('info', '[AbstractPaper::handleApiError] Using API error: ' . $responseData['error']);
                        return $responseData['error'];
                    } elseif (isset($responseData['errors']) && is_array($responseData['errors'])) {
                        // Join multiple errors
                        $errorString = implode(', ', $responseData['errors']);
                        log_message('info', '[AbstractPaper::handleApiError] Using API errors: ' . $errorString);
                        return $errorString;
                    }
                }
            } catch (\Exception $parseException) {
                // If we can't parse the error, just use the default message
                log_message('error', '[AbstractPaper::handleApiError] Failed to parse API error response: ' . $parseException->getMessage());
            }
        }

        log_message('info', '[AbstractPaper::handleApiError] Using default error message');
        return $defaultMessage;
    }

    /**
     * View abstract details (alias for edit method for better UX)
     *
     * @param int $id Abstract ID
     * @return mixed
     */
    public function view($id)
    {
        log_message('info', "[AbstractPaper::view] Redirecting to edit view for abstract ID: {$id}");
        // Redirect to edit method which serves as the detail view
        return redirect()->to("/abstract-paper/edit/{$id}");
    }

    /**
     * Compare two abstract versions
     * 
     * @param int $version1Id First version ID to compare
     * @param int $version2Id Second version ID to compare
     * @return mixed JSON response with comparison data
     */    public function compareVersions($version1Id, $version2Id)
    {
        log_message('info', "[AbstractPaper::compareVersions] Starting comparison between versions {$version1Id} and {$version2Id}");

        // Validate input parameters
        if (empty($version1Id) || empty($version2Id)) {
            log_message('error', '[AbstractPaper::compareVersions] Missing version IDs');
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Both version IDs are required.',
                    'error_code' => 'MISSING_PARAMETERS'
                ])->setStatusCode(400);
            }
            return redirect()->to('/abstract-paper')->with('error', 'Both version IDs are required for comparison.');
        }

        if ($version1Id === $version2Id) {
            log_message('error', '[AbstractPaper::compareVersions] Attempting to compare same version');
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Cannot compare a version with itself.',
                    'error_code' => 'SAME_VERSION'
                ])->setStatusCode(400);
            }
            return redirect()->to('/abstract-paper')->with('error', 'Cannot compare a version with itself.');
        }

        // Additional check: Ensure user is authenticated
        if (!session()->get('isLoggedIn') || !session()->has('jwt_token')) {
            log_message('error', '[AbstractPaper::compareVersions] User not authenticated');
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Authentication required to access this feature.'
                ])->setStatusCode(401);
            }
            return redirect()->to('/sign-in')->with('error', 'Please sign in to compare abstract versions.');
        }

        // Check if this is an AJAX request for JSON response
        if ($this->request->isAJAX()) {
            return $this->compareVersionsAjax($version1Id, $version2Id);
        }

        // Otherwise render the comparison view
        return $this->renderComparisonView($version1Id, $version2Id);
    }

    /**
     * Handle AJAX comparison request
     * 
     * @param int $version1Id First version ID to compare
     * @param int $version2Id Second version ID to compare
     * @return mixed JSON response with comparison data
     */    private function compareVersionsAjax($version1Id, $version2Id)
    {
        // Validate input parameters
        if (empty($version1Id) || empty($version2Id)) {
            log_message('error', '[AbstractPaper::compareVersionsAjax] Missing version IDs');
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Both version IDs are required.',
                'error_code' => 'MISSING_PARAMETERS'
            ]);
        }

        if ($version1Id === $version2Id) {
            log_message('error', '[AbstractPaper::compareVersionsAjax] Attempting to compare same version');
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Cannot compare a version with itself.',
                'error_code' => 'SAME_VERSION'
            ]);
        }

        try {
            // Get participant ID from session for security verification
            $participantId = session()->get('current_participant_id');
            if (!$participantId) {
                log_message('error', '[AbstractPaper::compareVersionsAjax] No participant ID in session');
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Authentication required. Please log in again.',
                    'error_code' => 'AUTHENTICATION_REQUIRED'
                ]);
            }
            log_message('info', "[AbstractPaper::compareVersionsAjax] Using comparison endpoint for versions {$version1Id} and {$version2Id}");
            // Use the existing comparison endpoint
            $comparisonData = $this->makeGetRequest("/abstract-versions/compare/{$version1Id}/{$version2Id}", [], false);

            if (!$comparisonData) {
                log_message('error', '[AbstractPaper::compareVersionsAjax] Comparison endpoint returned no data');
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Could not retrieve comparison data. Versions may not exist or you may not have access.',
                    'error_code' => 'COMPARISON_NOT_FOUND'
                ]);
            }

            // Verify participant has access to this abstract by checking the primary_participant_id
            $abstractData = $comparisonData['abstract'] ?? null;
            if (!$abstractData || ($abstractData['primary_participant_id'] ?? null) != $participantId) {
                log_message('error', "[AbstractPaper::compareVersionsAjax] Participant {$participantId} does not own this abstract");
                return $this->response->setStatusCode(403)->setJSON([
                    'status' => 'error',
                    'message' => 'You do not have permission to access these versions.',
                    'error_code' => 'ACCESS_DENIED'
                ]);
            }

            log_message('info', '[AbstractPaper::compareVersionsAjax] Comparison data retrieved successfully');

            // Format the response to match our expected structure
            $response = [
                'status' => 'success',
                'message' => 'Version comparison generated successfully.',
                'data' => $comparisonData
            ];

            log_message('info', '[AbstractPaper::compareVersionsAjax] Comparison completed successfully');
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::compareVersionsAjax] Exception occurred: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::compareVersionsAjax] Stack trace: ' . $e->getTraceAsString());

            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'An unexpected error occurred while comparing versions.',
                'error_code' => 'INTERNAL_ERROR',
                'debug' => ENVIRONMENT === 'development' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Get comparison data without JSON wrapping
     * 
     * @param int $version1Id First version ID to compare
     * @param int $version2Id Second version ID to compare
     * @return array|null Comparison data or null if error
     */
    private function getComparisonData($version1Id, $version2Id)
    {
        // Validate input parameters
        if (empty($version1Id) || empty($version2Id)) {
            log_message('error', '[AbstractPaper::getComparisonData] Missing version IDs');
            return null;
        }

        if ($version1Id === $version2Id) {
            log_message('error', '[AbstractPaper::getComparisonData] Attempting to compare same version');
            return null;
        }

        try {
            // Get participant ID from session for security verification
            $participantId = session()->get('current_participant_id');
            if (!$participantId) {
                log_message('error', '[AbstractPaper::getComparisonData] No participant ID in session');
                return null;
            }
            log_message('info', "[AbstractPaper::getComparisonData] Using comparison endpoint for versions {$version1Id} and {$version2Id}");
            // Use the existing comparison endpoint
            $comparisonData = $this->makeGetRequest("/abstract-versions/compare/{$version1Id}/{$version2Id}", [], false);

            if (!$comparisonData) {
                log_message('error', '[AbstractPaper::getComparisonData] Comparison endpoint returned no data');
                return null;
            }

            // Verify participant has access to this abstract by checking the primary_participant_id
            $abstractData = $comparisonData['abstract'] ?? null;
            if (!$abstractData || ($abstractData['primary_participant_id'] ?? null) != $participantId) {
                log_message('error', "[AbstractPaper::getComparisonData] Participant {$participantId} does not own this abstract");
                return null;
            }

            log_message('info', '[AbstractPaper::getComparisonData] Comparison data retrieved successfully');
            return $comparisonData;
        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::getComparisonData] Exception occurred: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::getComparisonData] Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    /**
     * Render comparison view for browser requests
     * 
     * @param int $version1Id First version ID to compare
     * @param int $version2Id Second version ID to compare
     * @return mixed View response or redirect
     */
    public function renderComparisonView($version1Id, $version2Id)
    {
        try {
            log_message('info', '[AbstractPaper::renderComparisonView] Starting renderComparisonView');
            log_message('info', '[AbstractPaper::renderComparisonView] Request isAJAX: ' . ($this->request->isAJAX() ? 'true' : 'false'));
            log_message('info', '[AbstractPaper::renderComparisonView] Request headers: ' . json_encode($this->request->getHeaders()));

            // Get the comparison data directly without JSON wrapping
            $comparisonData = $this->getComparisonData($version1Id, $version2Id);

            if (!$comparisonData) {
                log_message('error', '[AbstractPaper::renderComparisonView] No comparison data found');
                return redirect()->to('/abstract-paper')->with('error', 'Unable to load comparison data. Please ensure the versions exist and you have access to them.');
            }

            // Verify we have the required data structure
            if (!isset($comparisonData['version1']) || !isset($comparisonData['version2'])) {
                log_message('error', '[AbstractPaper::renderComparisonView] Invalid comparison data structure');
                return redirect()->to('/abstract-paper')->with('error', 'Invalid comparison data received. Please try again.');
            }

            // Prepare view data
            $viewData = [
                'title' => 'Abstract Version Comparison',
                'data' => $comparisonData
            ];

            log_message('info', '[AbstractPaper::renderComparisonView] Rendering comparison view with data keys: ' . implode(', ', array_keys($viewData['data'])));
            log_message('info', '[AbstractPaper::renderComparisonView] View file path: participant/abstract-paper/comparison');

            return $this->render('participant/abstract-paper/comparison', $viewData);
        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::renderComparisonView] Exception occurred: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::renderComparisonView] Stack trace: ' . $e->getTraceAsString());
            return redirect()->to('/abstract-paper')->with('error', 'An unexpected error occurred while loading the comparison. Please try again later.');
        }
    }

    /**
     * Search for participants by email and program ID
     *
     * @return mixed JSON response with participant data or null if not found
     */    public function searchParticipant()
    {
        log_message('info', '[AbstractPaper::searchParticipant] Starting participant search');

        // Check if this is an AJAX request
        if (!$this->request->isAJAX()) {
            log_message('error', '[AbstractPaper::searchParticipant] Non-AJAX request rejected');
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'This endpoint only accepts AJAX requests.'
            ]);
        }

        // Get request parameters from both GET and POST
        $email = $this->request->getGet('email') ?: $this->request->getPost('email');
        $programId = $this->request->getGet('program_id') ?: $this->request->getPost('program_id');
        
        // Log the request method and parameters
        $method = $this->request->getMethod();
        log_message('info', "[AbstractPaper::searchParticipant] Request method: {$method}");
        log_message('info', "[AbstractPaper::searchParticipant] Email: {$email}, Program ID: {$programId}");

        // Validate required parameters
        if (empty($email)) {
            log_message('warning', '[AbstractPaper::searchParticipant] Email parameter missing');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email address is required.'
            ]);
        }

        if (empty($programId)) {
            log_message('warning', '[AbstractPaper::searchParticipant] Program ID parameter missing');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program ID is required.'
            ]);
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            log_message('warning', "[AbstractPaper::searchParticipant] Invalid email format: {$email}");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address.'
            ]);
        }try {
            log_message('info', "[AbstractPaper::searchParticipant] Searching for participant with email: {$email} in program: {$programId}");

            // Construct API URL and log it
            $apiUrl = "/participants/search?email={$email}&program_id={$programId}";
            log_message('info', "[AbstractPaper::searchParticipant] Making API call to: {$apiUrl}");
            
            // Call the API to search for participant
            $response = $this->makeGetRequest($apiUrl, [], false);
            
            // Log the raw response for debugging
            log_message('info', "[AbstractPaper::searchParticipant] Raw API response: " . json_encode($response));

            // Check if we received a valid response
            if ($response === null) {
                log_message('error', "[AbstractPaper::searchParticipant] Received null response from API");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'API request failed. Please check your connection and try again.'
                ]);
            }

            // Check different response structures based on the API response format you provided
            $participant = null;
            
            // Handle the response structure from your example: {"status": "success", "data": {"id": "52624", ...}}
            if (isset($response['status']) && $response['status'] === 'success' && isset($response['data'])) {
                $participant = $response['data'];
                log_message('info', "[AbstractPaper::searchParticipant] Found participant in 'data' field");
            }
            // Handle alternative structure: {"participant": {...}}
            elseif (isset($response['participant'])) {
                $participant = $response['participant'];
                log_message('info', "[AbstractPaper::searchParticipant] Found participant in 'participant' field");
            }
            // Handle direct participant data
            elseif (isset($response['id']) && isset($response['full_name'])) {
                $participant = $response;
                log_message('info', "[AbstractPaper::searchParticipant] Found participant as direct response");
            }

            if ($participant) {
                // Extract user email from nested user object if available
                $participantEmail = $participant['email'] ?? ($participant['user']['email'] ?? $email);
                $participantName = $participant['full_name'] ?? ($participant['user']['full_name'] ?? 'Unknown');
                $participantInstitution = $participant['institution'] ?? '';
                
                log_message('info', "[AbstractPaper::searchParticipant] Participant found: {$participantName} ({$participantEmail})");
                log_message('debug', "[AbstractPaper::searchParticipant] Full participant data: " . json_encode($participant));
                
                return $this->response->setJSON([
                    'success' => true,
                    'found' => true,
                    'participant' => [
                        'id' => $participant['id'],
                        'full_name' => $participantName,
                        'email' => $participantEmail,
                        'institution' => $participantInstitution
                    ],
                    'message' => 'Participant found and details loaded.'
                ]);
            } else {
                log_message('info', "[AbstractPaper::searchParticipant] No participant found with email: {$email} in program: {$programId}");
                log_message('debug', "[AbstractPaper::searchParticipant] Response structure: " . json_encode($response));

                return $this->response->setJSON([
                    'success' => true,
                    'found' => false,
                    'message' => 'No registered participant found with this email address in the current program.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::searchParticipant] Exception occurred: ' . $e->getMessage());
            log_message('error', '[AbstractPaper::searchParticipant] Stack trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while searching for the participant. Please try again.'
            ]);
        }
    }

    /**
     * Check if the current user can manage authors for the given abstract
     * Only the primary author can manage authors
     *
     * @param int $abstractId Abstract ID
     * @return bool
     */
    private function canManageAuthors($abstractId)
    {
        log_message('info', "[AbstractPaper::canManageAuthors] Checking author management permissions for abstract ID: {$abstractId}");

        // Get current participant ID from session
        $currentParticipantId = session()->get('current_participant_id');
        if (!$currentParticipantId) {
            log_message('error', '[AbstractPaper::canManageAuthors] No current participant ID in session');
            return false;
        }

        try {
            // Get abstract data to check primary_participant_id
            $abstract = $this->makeGetRequest('/abstracts/' . $abstractId, [], false);
            if (!$abstract) {
                log_message('error', "[AbstractPaper::canManageAuthors] Abstract not found with ID: {$abstractId}");
                return false;
            }

            // Check if current user is the primary author
            $primaryParticipantId = $abstract['primary_participant_id'] ?? null;
            if ($currentParticipantId != $primaryParticipantId) {
                log_message('warning', "[AbstractPaper::canManageAuthors] Access denied - Current participant {$currentParticipantId} is not the primary author {$primaryParticipantId}");
                return false;
            }

            // Check if editing is allowed (abstract status and feedback)
            $abstractStatus = strtolower($abstract['status'] ?? 'draft');
            $hasFeedback = !empty($abstract['reviewers']);
            $canEdit = ($abstractStatus !== 'submitted') || $hasFeedback;

            if (!$canEdit) {
                log_message('warning', "[AbstractPaper::canManageAuthors] Editing not allowed - Status: {$abstractStatus}, Has feedback: " . ($hasFeedback ? 'yes' : 'no'));
                return false;
            }

            log_message('info', "[AbstractPaper::canManageAuthors] Access granted for participant {$currentParticipantId} to manage authors");
            return true;
        } catch (\Exception $e) {
            log_message('error', '[AbstractPaper::canManageAuthors] Exception occurred: ' . $e->getMessage());
            return false;
        }
    }
}
