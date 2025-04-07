<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController;

class Documents extends BaseController
{
    public function index()
    {
        // Get current program ID from session
        $currentProgramId = session()->get('current_program_id');

        // Safety check - if no program ID, redirect to home
        if (empty($currentProgramId)) {
            return redirect()->to(base_url('dashboard'));
        }

        // CRITICAL FIX: Force refresh of API data for the current program
        // 1. Get all documents for current user
        $documentsData = $this->makeGetRequest('/program-documents/program/' . $currentProgramId, [], false);

        $data = [
            'title' => 'Documents',
            'documents' => $documentsData,
        ];

        return $this->render('participant/documents/program-docs', $data);
    }

    /**
     * Display details for a specific document
     * 
     * @param int $id The document ID
     */
    public function details($id = null)
    {
        if (empty($id)) {
            return redirect()->to(base_url('documents/program'));
        }

        // Fetch specific document details from API
        $documentData = $this->makeGetRequest('/program-documents/' . $id, [], false);
        
        if (empty($documentData)) {
            session()->setFlashdata('error', 'Document not found.');
            return redirect()->to(base_url('documents/program'));
        }

        $data = [
            'title' => 'Document Details',
            'document' => $documentData,
        ];

        return $this->render('participant/documents/document-details', $data);
    }

    public function certificates()
    {
        return $this->render('participant/documents/certificates');
    }
}
