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
