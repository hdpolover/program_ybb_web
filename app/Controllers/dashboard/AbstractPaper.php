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
        $scenario = 3; // Change this to test different scenarios
        
        if ($scenario === 1) {
            // Scenario 1: Not eligible for abstract submission
            $participant_data = [
                'participant_id' => '32045',
                'eligible_for_abstract' => false,
                'abstract' => null
            ];
        } elseif ($scenario === 2) {
            // Scenario 2: Eligible but no abstract submitted
            $participant_data = [
                'participant_id' => '32045',
                'eligible_for_abstract' => true,
                'abstract' => null
            ];
        } else {
            // Scenario 3: Eligible with abstract submitted
            $participant_data = [
                'participant_id' => '32045',
                'eligible_for_abstract' => true,
                'abstract' => true // This will be replaced with actual abstract data below
            ];
        }
        
        // Example abstract data for display when abstract exists
        $dummyAbstract = [
            'id' => 1,
            'title' => 'Deep Learning for Image Recognition',
            'content' => 'Deep learning techniques utilize convolutional neural networks (CNNs) for image recognition. This research explores advanced methodologies and experimental results in implementing these techniques.',
            'references' => 'K. He, X. Zhang, S. Ren, and J. Sun, "Deep Residual Learning for Image Recognition," in Proceedings of the IEEE Conference on Computer Vision.',
            'status' => 'Under Review',
            'category' => 'Machine Learning Track',
            'topic' => 'Artificial Intelligence',
            'keywords' => 'deep learning, CNN, image recognition, computer vision',
            'lastUpdated' => '2024-05-15 14:30',
            'is_draft' => false,
            'authors' => [
                [
                    'name' => 'Alice Smith',
                    'affiliation' => 'University of Example',
                    'isPrimary' => true
                ],
                [
                    'name' => 'Bob Johnson',
                    'affiliation' => 'Example institute',
                    'isPrimary' => false
                ]
            ],
            'reviewers' => [
                [
                    'name' => 'John Doe',
                    'status' => 'Minor revision',
                    'comments' => 'The introduction needs more details on prior work.',
                    'date' => '2024-05-10'
                ]
            ]
        ];
        
        // If participant has an abstract, assign it to the participant data
        if ($participant_data['eligible_for_abstract'] && $participant_data['abstract'] === true) {
            $participant_data['abstract'] = $dummyAbstract;
        }

        // Build view data
        $data = [
            'title' => 'Abstract and Paper',
            'participant_data' => $participant_data,
            'abstractData' => $participant_data['abstract'] // Pass the abstract data if it exists
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
        $isDraft = $this->request->getPost('is_draft') === '1';
        
        // Define validation rules based on whether it's a draft or final submission
        if ($isDraft) {
            // For drafts, we'll only require the title
            $rules = [
                'title' => 'required'
            ];
        } else {
            // For final submission, apply full validation
            $rules = [
                'topic' => 'required',
                'title' => 'required|min_length[5]',
                'keywords' => 'required',
                'content' => 'required|min_length[100]'
            ];
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Process form data
        $data = [
            'topic_id' => $this->request->getPost('topic'),
            'title' => $this->request->getPost('title'),
            'keywords' => $this->request->getPost('keywords'),
            'content' => $this->request->getPost('content'),
            'user_id' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'is_draft' => $isDraft ? 1 : 0
        ];

        // In production, you would call an API endpoint to save the data
        // For now, we'll simulate a successful save
        
        // If save is successful, redirect with success message
        $message = $isDraft ? 
            'Abstract draft saved successfully. You can continue editing it later.' : 
            'Abstract submitted successfully. Thank you for your submission!';
        return redirect()->to('/abstract-paper')->with('success', $message);
    }

    public function update($id)
    {
        // Check if this is a draft
        $isDraft = $this->request->getPost('is_draft') === '1';
        
        // Define validation rules based on whether it's a draft or final submission
        if ($isDraft) {
            // For drafts, we'll only require the title
            $rules = [
                'title' => 'required'
            ];
        } else {
            // For final submission, apply full validation
            $rules = [
                'topic' => 'required',
                'title' => 'required|min_length[5]',
                'keywords' => 'required',
                'content' => 'required|min_length[100]'
            ];
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Process form data
        $data = [
            'topic_id' => $this->request->getPost('topic'),
            'title' => $this->request->getPost('title'),
            'keywords' => $this->request->getPost('keywords'),
            'content' => $this->request->getPost('content'),
            'updated_at' => date('Y-m-d H:i:s'),
            'is_draft' => $isDraft ? 1 : 0
        ];

        // In production, you would call an API endpoint to update the data
        // For now, we'll simulate a successful update
        
        // If update is successful, redirect with success message
        $message = $isDraft ? 
            'Abstract draft updated successfully. You can continue editing it later.' : 
            'Abstract updated successfully. Thank you for your submission!';
        return redirect()->to('/abstract-paper')->with('success', $message);
    }    /**
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
    }    /**
     * Get abstract by ID
     * In production, you would fetch this from an API
     */
    private function getAbstractById($id)
    {
        // Example data - replace with actual API call
        if ($id == 1) {
            return [
                'id' => 1,
                'topic_id' => 3, // Using topic ID 3 (Artificial Intelligence) from our sample topics
                'title' => 'Deep Learning for Image Recognition',
                'keywords' => 'deep learning, CNN, image recognition, computer vision',
                'content' => '<p>Deep learning techniques utilize convolutional neural networks (CNNs) to analyze images and extract meaningful features. This paper explores the application of ResNet architectures for improving image recognition accuracy in real-world scenarios.</p><p>Our methodology involves preprocessing techniques, data augmentation, and transfer learning approaches to maximize model performance with limited training data.</p>',
                'created_at' => '2024-05-10 14:30:00',
                'updated_at' => '2024-05-15 09:45:00',
                'is_draft' => false
            ];
        }
        
        return null;
    }
}