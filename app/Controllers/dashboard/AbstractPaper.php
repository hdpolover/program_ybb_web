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
        // Get participant data from database or API
        // In production, you would fetch this from your participant model/database

        // Example scenarios:
        // 1. Participant not eligible for abstract submission (registration pending)
        // 2. Participant eligible but no abstract submitted yet
        // 3. Participant eligible with abstract submitted
        // Toggle between scenarios by changing this value: 1, 2, or 3
        // $scenario = 3; // Change this to test different scenarios

        // if ($scenario === 1) {
        //     // Scenario 1: Not eligible for abstract submission
        //     $participant_data = [
        //         'participant_id' => '32045',
        //         'eligible_for_abstract' => false,
        //         'abstract' => null
        //     ];
        // } elseif ($scenario === 2) {
        //     // Scenario 2: Eligible but no abstract submitted
        //     $participant_data = [
        //         'participant_id' => '32045',
        //         'eligible_for_abstract' => true,
        //         'abstract' => null
        //     ];
        // } else {
        //     // Scenario 3: Eligible with abstract submitted
        //     $participant_data = [
        //         'participant_id' => '32045',
        //         'eligible_for_abstract' => true,
        //         'abstract' => true // This will be replaced with actual abstract data below
        //     ];
        // }

        // // Example abstract data for display when abstract exists
        // $dummyAbstract = [
        //     'id' => 1,
        //     'title' => 'Deep Learning for Image Recognition',
        //     'content' => 'Deep learning techniques utilize convolutional neural networks (CNNs) for image recognition. This research explores advanced methodologies and experimental results in implementing these techniques.',
        //     'references' => 'K. He, X. Zhang, S. Ren, and J. Sun, "Deep Residual Learning for Image Recognition," in Proceedings of the IEEE Conference on Computer Vision.',
        //     'status' => 'Under Review',
        //     'category' => 'Machine Learning Track',
        //     'topic' => 'Artificial Intelligence',
        //     'keywords' => 'deep learning, CNN, image recognition, computer vision',
        //     'lastUpdated' => '2024-05-15 14:30',
        //     'is_draft' => false,
        //     'authors' => [
        //         [
        //             'name' => 'Alice Smith',
        //             'affiliation' => 'University of Example',
        //             'isPrimary' => true
        //         ],
        //         [
        //             'name' => 'Bob Johnson',
        //             'affiliation' => 'Example institute',
        //             'isPrimary' => false
        //         ]
        //     ],
        //     'reviewers' => [
        //         [
        //             'name' => 'John Doe',
        //             'status' => 'Minor revision',
        //             'comments' => 'The introduction needs more details on prior work.',
        //             'date' => '2024-05-10'
        //         ]
        //     ]
        // ];

        // // get participant ID from session
        $participantId = session()->get('current_participant_id');

        $participant_data = $this->makeGetRequest('/abstracts/participant/' . $participantId . '/details', [], false);

        log_message('info', 'Participant Data: ' . print_r($participant_data, true));

        // If participant has an abstract, assign it to the participant data
        if ($participant_data['eligible_for_abstract'] && isset($participant_data['abstract'])) {
            $participant_data['abstract'] = [];
        }

        // Build view data
        $data = [
            'title' => 'Abstract and Paper',
            'participant_data' => $participant_data,
            'abstractData' => $participant_data['abstract'] ?? []    // Pass the abstract data if it exists
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

    public function edit($id)
    {
        // Get abstract data by ID
        // In production, you would call an API endpoint
        $abstract = $this->getAbstractById($id);

        if (!$abstract) {
            return redirect()->to('/abstract-paper')->with('error', 'Abstract not found.');
        }

        // Get available topics
        $topics = $this->getAvailableTopics();

        $data = [
            'title' => 'Edit Abstract',
            'abstract' => $abstract,
            'topics' => $topics
        ];

        return $this->render('participant/abstract-paper/manage-abstract', $data);
    }

    public function save()
    {
        // Check if this is a draft
        $isDraft = $this->request->getPost('status') === 'draft';

        // Define validation rules based on whether it's a draft or final submission
        if ($isDraft) {
            // For drafts, we'll only require the title
            $rules = [
                'title' => 'required'
            ];
        } else {
            // For final submission, apply full validation
            $rules = [
                'abstract_topic_id' => 'required',
                'title' => 'required|min_length[5]',
                'keywords' => 'required',
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
        ];        try {
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
            
            // Save the abstract details in flash data to display in SweetAlert
            session()->setFlashdata('abstract_data', [
                'id' => $response['abstract']['id'] ?? 'N/A',
                'title' => $response['abstract']['title'] ?? 'Your Abstract',
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
        // Check if this is a draft
        $isDraft = $this->request->getPost('status') === 'draft';

        // Define validation rules based on whether it's a draft or final submission
        if ($isDraft) {
            // For drafts, we'll only require the title
            $rules = [
                'title' => 'required'
            ];
        } else {
            // For final submission, apply full validation
            $rules = [
                'abstract_topic_id' => 'required',
                'title' => 'required|min_length[5]',
                'keywords' => 'required',
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
        ];        try {
            // Call the API endpoint to update the abstract - using PUT method
            $response = $this->makePutRequest('/api/abstracts/' . $id, $data, [], true, true);
            
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
            }
            
            // If update is successful, redirect with success message
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
            
            $title = $isDraft ? 'Draft Updated' : 'Update Complete';
            
            // Save the abstract details in flash data to display in SweetAlert
            session()->setFlashdata('abstract_data', [
                'id' => $response['abstract']['id'] ?? $id,
                'title' => $response['abstract']['title'] ?? $data['title'],
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
     * Get available topics for abstract submission
     * In production, you would fetch this from an API
     */
    private function getAvailableTopics()
    {
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        try {
            // In a real application, you would fetch topics from an API
            // For example:
            $topics = $this->makeGetRequest('/abstract-topics/program/' . $currentProgramId, [], false, false);

            // // Sample data for demonstration - in production, use API data
            // $topics = [
            //     [
            //         'id' => 1,
            //         'program_id' => $currentProgramId,
            //         'name' => 'Machine Learning',
            //         'description' => 'Research related to machine learning algorithms, neural networks, deep learning, and AI applications.'
            //     ],
            //     [
            //         'id' => 2,
            //         'program_id' => $currentProgramId,
            //         'name' => 'Data Science',
            //         'description' => 'Studies involving data collection, preprocessing, visualization, and statistical analysis for deriving insights.'
            //     ],
            //     [
            //         'id' => 3,
            //         'program_id' => $currentProgramId,
            //         'name' => 'Artificial Intelligence',
            //         'description' => 'Research in AI theory, cognitive computing, natural language processing, and intelligent systems.'
            //     ],
            //     [
            //         'id' => 4,
            //         'program_id' => $currentProgramId,
            //         'name' => 'Computer Vision',
            //         'description' => 'Studies focused on enabling computers to gain high-level understanding from digital images or videos.'
            //     ],
            //     [
            //         'id' => 5,
            //         'program_id' => $currentProgramId,
            //         'name' => 'Natural Language Processing',
            //         'description' => 'Research on interactions between computers and human language, text analysis, and language generation.'
            //     ]
            // ];

            return $topics;
        } catch (\Exception $e) {
            // Handle error (e.g., log it)
            log_message('error', 'Failed to fetch topics: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * Get abstract by ID
     * In production, you would fetch this from an API
     */
    private function getAbstractById($id)
    {
        try {
            // Call API to get abstract data
            $abstract = $this->makeGetRequest('/api/abstracts/' . $id, [], true, false);
            return $abstract;
        } catch (\Exception $e) {
            log_message('error', 'Failed to fetch abstract: ' . $e->getMessage());
            return null;
        }
    }    /**
     * Helper method to handle API errors and extract meaningful messages
     */
    private function handleApiError(\Exception $e, $defaultMessage = 'An error occurred. Please try again later.')
    {
        log_message('error', 'API Error: ' . $e->getMessage());
        
        // First, check if message contains specific errors we can interpret
        $message = $e->getMessage();
        
        // Handle timeout errors
        if (strpos($message, 'timeout') !== false || 
            strpos($message, 'Connection timed out') !== false) {
            return 'The server is taking too long to respond. This could be due to high traffic or connectivity issues. Please try again later.';
        }
        
        // Handle connection errors
        if (strpos($message, 'Connection refused') !== false || 
            strpos($message, 'Could not resolve host') !== false) {
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
