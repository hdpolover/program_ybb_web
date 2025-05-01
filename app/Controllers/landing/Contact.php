<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Contact extends BaseController
{
    public function index()
    {
        // Get program information that might be needed for the contact page
        $programInfo = [];
        try {
            // If you have program-specific information, fetch it here
            // This would be similar to how other controllers are getting such info
            $apiUrl = '/landing/program-info?web_url=' . $this->currentUrl;
            $programInfo = $this->makeGetRequest($apiUrl, [], false) ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Error fetching program info for contact page: ' . $e->getMessage());
        }

        // Pass any success or error messages from the form submission
        $data = [
            'title' => 'Contact Us',
            'program_info' => $programInfo,
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
        ];

        return $this->render('landing/contact', $data);
    }

    public function submit()
    {
        // Process form submission
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'subject' => $this->request->getPost('subject'),
            'interest' => $this->request->getPost('interest'),
            'message' => $this->request->getPost('message'),
            'website_url' => $this->currentUrl, // Include the website URL
        ];

        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return redirect()->to('/contact')->with('error', 'Please fill all required fields.');
        }

        // Send data to API endpoint
        try {
            $response = $this->makePostRequest('/contact/submit', $data);
            
            // Check if the submission was successful
            if (isset($response['success']) && $response['success']) {
                return redirect()->to('/contact')->with('success', 'Thank you for your message. We will get back to you shortly!');
            } else {
                $errorMessage = $response['message'] ?? 'Something went wrong. Please try again later.';
                return redirect()->to('/contact')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error submitting contact form: ' . $e->getMessage());
            return redirect()->to('/contact')->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
}
