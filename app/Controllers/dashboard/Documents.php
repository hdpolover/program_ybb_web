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

        // Get current participant ID from session
        $currentParticipantId = session()->get('current_participant_id');

        // CRITICAL FIX: Force refresh of API data for the current program
        // 1. Get all documents for current user
        $documentsData = $this->makeGetRequest('/program-documents/program/' . $currentProgramId, [], false);

        // check for any successful payments
        $participantPayments = $this->makeGetRequest('/payments/participants/' . $currentParticipantId, [], false, false);

        // loop through payments and check if any are successful
        $hasSuccessfulPayment = false;

        if (isset($participantPayments) && is_array($participantPayments)) {
            foreach ($participantPayments as $payment) {
                if (isset($payment['status']) && $payment['status'] === '2') {
                    $hasSuccessfulPayment = true;
                    break;
                }
            }
        }

        // get participant statuses
        $participantStatuses = $this->makeGetRequest('/participants/' . $currentParticipantId . '/status', [], false, false);

        $hasSubmittedForm = false;

        // check if participant form_status from $participantStatuses equals to 2. $participantStatuse is one object. not an array
        if (isset($participantStatuses)) {
            if (isset($participantStatuses['form_status']) && $participantStatuses['form_status'] === '2') {
                $hasSubmittedForm = true;
            }
        }

        $visibleDocuments = [];

        // check if participant has submitted form and has successful payment
        if ($hasSubmittedForm && $hasSuccessfulPayment) {
            // show documents only if participant has submitted form and has successful payment
            if (isset($documentsData) && is_array($documentsData)) {
                foreach ($documentsData as $document) {
                    $visibleDocuments[] = $document;
                }
            }
        } else {
            // do not show any documents
            $visibleDocuments = [];
        }


        $data = [
            'title' => 'Program Documents',
            'documents' => $visibleDocuments,
        ];

        // var_dump($hasSubmittedForm, $hasSuccessfulPayment, $documentsData, $participantStatuses, $visibleDocuments); // Debugging output

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

        // if document is of type loa, then get the document details from the API
        if ($documentData['type'] === 'loa') {
            $loaTemplate = $this->makeGetRequest('/loa-templates/program-documents/' . $id, [], false);
        } else {
            $loaTemplate = null;
        }

        $data = [
            'title' => 'Document Details',
            'document' => $documentData,
            'loaTemplate' => isset($loaTemplate) ? $loaTemplate : null,
        ];


        return $this->render('participant/documents/document-details', $data);
    }

    public function addAgreement()
    {
        $file = $this->request->getFile('participant_program_documents');
        $participantId = $this->request->getPost('participant_id');
        $programdocId = $this->request->getPost('program_document_id');

        // Validasi file
        if (!$file->isValid() || $file->getClientMimeType() !== 'application/pdf') {
            session()->setFlashdata('error', 'Only PDF.');
            return redirect()->back();
        }

        // Simpan file sementara untuk dikirim ke endpoint
        $tempPath = WRITEPATH . 'temp_uploads/';
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
        $fileName = time() . '_' . $file->getRandomName();
        $file->move($tempPath, $fileName);
        $agreementLeter = [
            'participant_id' => $participantId,
            'program_document_id' => $programdocId
        ];
        $response = $this->makePostRequest('/program-documents/upload', $agreementLeter,[],false,false);

        // Optional: Hapus file sementara
        //unlink($tempPath . $fileName);
            // echo '<pre>';
            // var_dump($response);
            // echo '</pre>';
            // exit;
        // if (!$response) {
        //     return redirect()->to(base_url('documents/program'));
        // } else {
        //     return redirect()->to(base_url('dashboard'));
        // }
    }

    // public function addAgreement()
    // {
    //     $file = $this->request->getFile('participant_program_documents');
    //     $participantId = $this->request->getPost('participant_id');
    //     $programdocId = $this->request->getPost('program_document_id');

    //     // Validasi file
    //     if (!$file->isValid() || $file->getClientMimeType() !== 'application/pdf') {
    //         session()->setFlashdata('error', 'Only PDF.');
    //         return redirect()->back();
    //     }

    //     // Buat nama file baru
    //     $timestamp = time();
    //     $newFileName = "{$participantId}_{$timestamp}.pdf";

    //     // Buat folder tujuan: writable/uploads/[program_id]/[nama_fileTanpaExt]/
    //     $fileNameWithoutExt = pathinfo($newFileName, PATHINFO_FILENAME);
    //     $uploadPath = WRITEPATH . "uploads/{$programdocId}/{$fileNameWithoutExt}/";

    //     if (!is_dir($uploadPath)) {
    //         mkdir($uploadPath, 0777, true);
    //     }

    //     // Pindahkan file ke folder baru
    //     $file->move($uploadPath, $newFileName);

    //     return redirect()->back()->with('success', 'Proposal berhasil diupload.');
    // }

    public function certificates()
    {
        return $this->render('participant/documents/certificates');
    }

    /**
     * Generate Letter of Acceptance document
     * 
     * @param int $documentId The document ID
     * @param int $participantId The participant ID
     */
    public function generateLoa($documentId = null, $participantId = null)
    {
        if (empty($documentId) || empty($participantId)) {
            log_message('error', 'LOA Generation: Missing parameters - Document ID: ' . ($documentId ?? 'null') . ', Participant ID: ' . ($participantId ?? 'null'));
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request. Document ID and Participant ID are required.']);
        }

        // Get the current participant ID from the session for validation
        $currentParticipantId = session()->get('current_participant_id');
        log_message('debug', 'LOA Generation: Current participant ID from session: ' . ($currentParticipantId ?? 'null'));

        // Security check - ensure the participant ID in the request matches the session
        if ($participantId != $currentParticipantId) {
            log_message('error', 'LOA Generation: Security check failed - Request participant ID: ' . $participantId . ', Session participant ID: ' . $currentParticipantId);
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized access.']);
        }

        try {
            log_message('info', 'LOA Generation: Starting - Document ID: ' . $documentId . ', Participant ID: ' . $participantId);

            // Attempt to generate the LOA document via API

            $loaResult = $this->makeGetRequest('/program-documents/' . $documentId . '/participants/' . $participantId . '/generate', [], false);

            if (isset($loaResult)) {
                // print data
                return $this->response->setJSON(['success' => true, 'message' => 'success', 'loa' => $loaResult]);
            }
        } catch (\Exception $e) {
            log_message('error', 'LOA Generation: Exception - ' . $e->getMessage());
            log_message('error', 'LOA Generation: Stack trace - ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'An error occurred while generating the document: ' . $e->getMessage()]);
        }
    }
}