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
        // Get user's abstracts - for now using dummy data
        // In production, you would call an API endpoint to fetch abstracts
        
        // Check if the user has any abstracts
        $hasAbstract = false;
        $abstractData = null;
        
        // Example abstract data for display
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
        
        // For demonstration purposes, we'll show a dummy abstract
        // In production, check response from API
        $hasAbstract = false;
        $abstractData = $dummyAbstract;

        // Build view data
        $data = [
            'title' => 'Abstract and Paper',
            'hasAbstract' => $hasAbstract,
            'abstractData' => $abstractData
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
        // Validate form input
        $rules = [
            'topic' => 'required',
            'title' => 'required|min_length[5]',
            'keywords' => 'required',
            'content' => 'required|min_length[100]'
        ];
        
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
            'created_at' => date('Y-m-d H:i:s')
        ];

        // In production, you would call an API endpoint to save the data
        // For now, we'll simulate a successful save
        
        // If save is successful, redirect with success message
        return redirect()->to('/abstract-paper')->with('success', 'Abstract submitted successfully.');
    }

    public function update($id)
    {
        // Validate form input
        $rules = [
            'topic' => 'required',
            'title' => 'required|min_length[5]',
            'keywords' => 'required',
            'content' => 'required|min_length[100]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Process form data
        $data = [
            'topic_id' => $this->request->getPost('topic'),
            'title' => $this->request->getPost('title'),
            'keywords' => $this->request->getPost('keywords'),
            'content' => $this->request->getPost('content'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // In production, you would call an API endpoint to update the data
        // For now, we'll simulate a successful update
        
        // If update is successful, redirect with success message
        return redirect()->to('/abstract-paper')->with('success', 'Abstract updated successfully.');
    }

    /**
     * Get available topics for abstract submission
     * In production, you would fetch this from an API
     */
    private function getAvailableTopics()
    {
        // Example data - replace with actual API call
        return [
            ['id' => 1, 'name' => 'Machine Learning'],
            ['id' => 2, 'name' => 'Data Science'],
            ['id' => 3, 'name' => 'Artificial Intelligence'],
            ['id' => 4, 'name' => 'Computer Vision'],
            ['id' => 5, 'name' => 'Natural Language Processing']
        ];
    }

    /**
     * Get abstract by ID
     * In production, you would fetch this from an API
     */
    private function getAbstractById($id)
    {
        // Example data - replace with actual API call
        if ($id == 1) {
            return [
                'id' => 1,
                'topic_id' => 1,
                'title' => 'Deep Learning for Image Recognition',
                'keywords' => 'deep learning, CNN, image recognition, computer vision',
                'content' => '<p>Deep learning techniques utilize convolutional neural networks (CNNs) to analyze images and extract meaningful features. This paper explores the application of ResNet architectures for improving image recognition accuracy in real-world scenarios.</p><p>Our methodology involves preprocessing techniques, data augmentation, and transfer learning approaches to maximize model performance with limited training data.</p>',
                'created_at' => '2024-05-10 14:30:00',
                'updated_at' => '2024-05-15 09:45:00'
            ];
        }
        
        return null;
    }
}