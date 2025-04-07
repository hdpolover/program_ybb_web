<?php

namespace App\Controllers\dashboard;

use App\Controllers\BaseController;

class Payments extends BaseController
{
    public function getVisibleProgramPayments($allPayments, $participantPayments)
{
    $today = date('Y-m-d H:i:s');

    // Group participant payments by program_payment_id
    $participantPaymentMap = [];
    foreach ($participantPayments as $pmt) {
        $participantPaymentMap[$pmt['program_payment_id']][] = $pmt;
    }

    $visiblePayments = [];

    // Track completed statuses
    $completed = [
        'registration' => false,
        'program_fee_1' => false,
        'program_fee_2' => false,
    ];

    // Helper to check if a payment is completed
    $isCompleted = function ($paymentId) use ($participantPaymentMap) {
        if (empty($participantPaymentMap[$paymentId])) return false;
        foreach ($participantPaymentMap[$paymentId] as $pmt) {
            if ($pmt['status'] == '2') return true; // 2 = success
        }
        return false;
    };

    // Helper to check if user has paid anything for this payment
    $hasAnyAttempt = function ($paymentId) use ($participantPaymentMap) {
        return !empty($participantPaymentMap[$paymentId]);
    };

    // Separate payments by category
    $byCategory = [
        'registration' => [],
        'program_fee_1' => [],
        'program_fee_2' => [],
    ];

    foreach ($allPayments as $payment) {
        $byCategory[$payment['category']][] = $payment;
    }

    // Sort each category by start_date (especially important for registration: early/late bid)
    foreach ($byCategory as &$group) {
        usort($group, function ($a, $b) {
            return strtotime($a['start_date']) - strtotime($b['start_date']);
        });
    }

    // Handle registration
    $registrationDone = false;
    foreach ($byCategory['registration'] as $payment) {
        $paymentId = $payment['id'];
        $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
        $isPaid = $isCompleted($paymentId);
        $hasTried = $hasAnyAttempt($paymentId);

        if ($isPaid) {
            $visiblePayments[] = $payment;
            $registrationDone = true;
            break; // No need to show other registration payments
        }

        if ($hasTried || $isWithinDateRange) {
            $visiblePayments[] = $payment;
        }
    }

    $completed['registration'] = $registrationDone;

    // Handle program_fee_1
    if ($completed['registration']) {
        foreach ($byCategory['program_fee_1'] as $payment) {
            $paymentId = $payment['id'];
            $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
            $isPaid = $isCompleted($paymentId);
            $hasTried = $hasAnyAttempt($paymentId);

            if ($isPaid) {
                $completed['program_fee_1'] = true;
            }

            if ($isWithinDateRange || $hasTried) {
                $visiblePayments[] = $payment;
            }
        }
    }

    // Handle program_fee_2
    if ($completed['program_fee_1']) {
        foreach ($byCategory['program_fee_2'] as $payment) {
            $paymentId = $payment['id'];
            $isWithinDateRange = $payment['start_date'] <= $today && $payment['end_date'] >= $today;
            $hasTried = $hasAnyAttempt($paymentId);

            if ($isWithinDateRange || $hasTried) {
                $visiblePayments[] = $payment;
            }
        }
    }

    // Remove duplicates just in case
    $visiblePayments = array_unique($visiblePayments, SORT_REGULAR);

    // Sort all by start date for final display
    usort($visiblePayments, function ($a, $b) {
        return strtotime($a['start_date']) - strtotime($b['start_date']);
    });

    return array_values($visiblePayments);
}


    /**
     * Display the list of program payments required from the participant
     */
    public function index()
    {
        $programPayments = $this->makeGetRequest('/program-payments/program/' . session()->get('current_program_id'), [], false);
        $participantPayments = $this->makeGetRequest('/payments/participant/' . session()->get('current_participant_id'), [], false);


        // Safety check - if not participant payments, skip loop
        if (empty($participantPayments)) {
            $participantPayments = [];
        } else {
            // Convert participant payments to a more usable format
            $participantPayments = array_column($participantPayments, null, 'program_payment_id');
        }

        // Get visible program payments
        $programPayments = $this->getVisibleProgramPayments($programPayments, $participantPayments);

        $data = [
            'title' => 'Payments',
            'programPayments' => $programPayments,
            'participantPayments' => $participantPayments,
        ];

        return $this->render('participant/payment/index', $data);
    }

    /**
     * Display the programPayment details and attempt history for a specific program programPayment
     * 
     * @param int $id The program programPayment ID
     */
    public function detail($id = null)
    {
        if (empty($id)) {
            return redirect()->to(base_url('payments'));
        }

        $participantId = session()->get('current_participant_id');
        $programId = session()->get('current_program_id');

        // Fetch programPayment details from API
        $paymentDetails = $this->makeGetRequest('/payments/program-payment/' . $id . '/participant/' . $participantId, [], false);

        if (empty($paymentDetails)) {
            session()->setFlashdata('error', 'Program payment not found.');
            return redirect()->to(base_url('payments'));
        }

        $programPayment = $paymentDetails['program_payment'] ?? null;
        if (empty($programPayment)) {
            session()->setFlashdata('error', 'Program payment details not found.');
            return redirect()->to(base_url('payments'));
        }

        // Fetch programPayment attempts for the participant
        $payments = $paymentDetails['payments'] ?? [];

        if (empty($payments)) {
            $payments = [];
        } else {
            // Convert payments to a more usable format
            $payments = array_column($payments, null, 'id');
        }

        // get payment methods
        $paymentMethods = $this->makeGetRequest('/payment-methods/program/' . $programId, [], false);

        $data = [
            'title' => 'Payment Details',
            'programPayment' => $programPayment,
            'payments' => $payments,
            'paymentMethods' => $paymentMethods,
        ];

        return $this->render('participant/payment/detail', $data);
    }

    /**
     * Process a new programPayment attempt for a specific program programPayment
     */
    public function makePayment()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'paymentId' => 'required|numeric',
            'paymentMethod' => 'required',
            'amount' => 'required|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        // Process the programPayment (in a real implementation, this would connect to a programPayment gateway)
        // $paymentModel = new \App\Models\ProgramPaymentModel();
        // $result = $paymentModel->processPayment(
        //     $this->request->getPost('paymentId'),
        //     $this->request->getPost('paymentMethod'),
        //     $this->request->getPost('amount')
        // );

        // Simulate successful programPayment for demonstration
        return redirect()->back()->with('success', 'Payment processed successfully');
    }

    /**
     * Download programPayment receipt for a completed programPayment attempt
     * 
     * @param int $id The programPayment attempt ID
     */
    public function downloadReceipt($id)
    {
        // This would generate and return a receipt file
        // For example:
        // $paymentModel = new \App\Models\ProgramPaymentModel();
        // $paymentData = $paymentModel->getPaymentAttemptById($id);

        // $pdf = new \TCPDF();
        // Generate PDF receipt
        // return $this->response->download('payment_receipt.pdf', $pdf->Output('', 'S'));

        // For now, just redirect back
        return redirect()->back()->with('info', 'Receipt download functionality will be implemented soon');
    }
}
