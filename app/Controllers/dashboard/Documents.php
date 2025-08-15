<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Documents extends BaseController
{
    public function index()
    {
        // Get current participant ID from session
        $currentParticipantId = session()->get('current_participant_id');

        // Safety check - if no participant ID, redirect to dashboard
        if (empty($currentParticipantId)) {
            return redirect()->to(base_url('dashboard'));
        }

        // Use the simplified API endpoint to get all participant documents
        $response = $this->makeGetRequest('/participants/' . $currentParticipantId . '/documents', [], false);

        // Extract documents from the response
        $documents = [];
        
        // The makeGetRequest method returns the 'data' portion of the API response directly
        // So $response is already the documents array when the API returns success
        if (is_array($response)) {
            // Check if this is a real documents array (has document structure)
            if (!empty($response) && isset($response[0]) && isset($response[0]['id'])) {
                // This is the real documents array
                $documents = $response;
            } else {
                // Empty array or unexpected structure
                $documents = [];
            }
        } else if (isset($response['message']) && strpos($response['message'], 'working') !== false) {
            // This might be test/debug data returned directly
            $documents = [];
            session()->setFlashdata('info', 'Documents system is currently being set up. Please check back later.');
        } else {
            $documents = [];
        }

        $data = [
            'title' => 'Program Documents',
            'documents' => $documents,
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
        $params = [
            'participant_id' => session()->get('current_participant_id'),
            'program_document_id' => $id
        ];
        // Fetch specific document details from API
        $documentData = $this->makeGetRequest('/program-documents/' . $id, [], false);
        $documentFile = $this->makePostRequest('/program-documents/participant-file', $params, [], false, false);

        if (empty($documentData)) {
            session()->setFlashdata('error', 'Document not found.');
            return redirect()->to(base_url('documents/program'));
        }
        // echo '<pre>';
        // var_dump($documentFile);
        // echo '</pre>';
        // exit;
        // if document is of type loa, then get the document details from the API
        if ($documentData['type'] === 'loa') {
            $loaTemplate = $this->makeGetRequest('/loa-templates/program-documents/' . $id, [], false);
        } else {
            $loaTemplate = null;
        }

        $data = [
            'title' => 'Document Details',
            'document' => $documentData,
            'files' => $documentFile,
            'loaTemplate' => isset($loaTemplate) ? $loaTemplate : null,
        ];

        return $this->render('participant/documents/document-details', $data);
    }

    public function addDocument()
    {
        date_default_timezone_set("Asia/Jakarta");
        $file = $this->request->getFile('participant_program_documents');
        $participantId = $this->request->getPost('participant_id');
        $programdocId = $this->request->getPost('program_document_id');

        // Validasi file
        if (!$file->isValid() || $file->getClientMimeType() !== 'application/pdf') {
            session()->setFlashdata('error', 'Only PDF.');
            return redirect()->back();
        }

        // Buat nama file baru
        $timestamp = date('Ymd');
        $newFileName = "{$participantId}_{$timestamp}.pdf";

        //Buat folder tujuan: writable/uploads/[program_id]/[nama_fileTanpaExt]/
        // $fileNameWithoutExt = pathinfo($newFileName, PATHINFO_FILENAME);
        $uploadPath = WRITEPATH . "../../storage.ybbfoundation.com/program-documents/{$programdocId}";

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Jika file sudah ada, hapus dulu agar bisa direplace
        if (file_exists($uploadPath . '/' . $newFileName)) {
            unlink($uploadPath . '/' . $newFileName);
        }

        // Pindahkan file ke folder baru
        $file->move($uploadPath, $newFileName);
        $fileurl = "https://storage.ybbfoundation.com/program-documents/{$programdocId}/" . $newFileName;
        $agreementLeter = [
            'participant_id' => $participantId,
            'program_document_id' => $programdocId,
            'file_url' => $fileurl
        ];

        $response = $this->makePostRequest('/program-documents/upload', $agreementLeter, [], false, false);

        // echo '<pre>';
        // var_dump($response);
        // echo $fileurl;
        // echo '</pre>';
        // exit;
        if (!$response) {
            return redirect()->to(base_url('documents/program'))->with('error', 'Uplod Error');
        } else {
            return redirect()->back()->with('success', 'Upload Success');
        }
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
        // Get current participant ID from session
        $currentParticipantId = session()->get('current_participant_id');

        // Safety check - if no participant ID, redirect to dashboard
        if (empty($currentParticipantId)) {
            return redirect()->to(base_url('dashboard'));
        }

        // Fetch participant certificates from API
        $certificatesData = $this->makeGetRequest('/certificates/participant/' . $currentParticipantId, [], false, false);

        // Handle API response - check if data exists and has the expected structure
        $processedData = null;
        if ($certificatesData && is_array($certificatesData)) {
            if (isset($certificatesData['data'])) {
                // API returned {data: {...}} structure
                $processedData = $certificatesData['data'];
            } elseif (isset($certificatesData['participant']) || isset($certificatesData['certificates'])) {
                // API returned direct structure
                $processedData = $certificatesData;
            }
        }

        // If API returns null or unexpected structure, set empty data
        if (!$processedData) {
            $processedData = [
                'participant' => null,
                'certificates' => [],
                'total_count' => 0
            ];
        }

        $data = [
            'title' => 'My Certificates',
            'certificates_data' => $processedData,
        ];

        return $this->render('participant/documents/certificates', $data);
    }

    /**
     * Generate certificate via API call
     */
    public function generateCertificate()
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request method']);
        }

        // Get JSON input
        $input = $this->request->getJSON(true);
        
        if (empty($input['participant_id']) || empty($input['award_id'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Participant ID and Award ID are required']);
        }

        // Get current participant ID from session for validation
        $currentParticipantId = session()->get('current_participant_id');
        if (empty($currentParticipantId) || $input['participant_id'] != $currentParticipantId) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized access']);
        }

        try {
            // Call API to generate certificate with the correct request body
            $generateResult = $this->makePostRequest('/certificates/generate', [
                'participant_id' => (int)$input['participant_id'],
                'award_id' => (int)$input['award_id']
            ], [], false, false);

            if ($generateResult && isset($generateResult['status']) && $generateResult['status']) {
                return $this->response->setJSON([
                    'status' => true,
                    'data' => $generateResult['data'] ?? null,
                    'message' => $generateResult['data']['message'] ?? 'Certificate generated successfully'
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => false,
                    'message' => $generateResult['message'] ?? 'Failed to generate certificate'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Certificate Generation Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => 'An error occurred while generating the certificate'
            ]);
        }
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
