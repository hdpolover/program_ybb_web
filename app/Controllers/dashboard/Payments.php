<?php

namespace App\Controllers\dashboard;
use App\Controllers\BaseController;

class Payments extends BaseController
{
    /**
     * Display the list of program payments required from the participant
     */
    public function index()
    {
        $programPayments = $this->makeGetRequest('/program-payments/program/' . session()->get('current_program_id'), [], false);
        $participantPayments = $this->makeGetRequest('/payments/participant/' . session()->get('current_participant_id'), [], false);
       
        $data = [
            'title' => 'Payments',
            'programPayments' => $programPayments,
            'participantPayments' => $participantPayments,
        ];

        return $this->render('participant/payment/index', $data);
    }

    /**
     * Display the payment details and attempt history for a specific program payment
     * 
     * @param int $id The program payment ID
     */
    public function detail($id = null)
    {
        // In a real implementation, you would fetch the payment details and history
        // For example:
        // $paymentModel = new \App\Models\ProgramPaymentModel();
        // $paymentDetails = $paymentModel->getPaymentById($id);
        // $paymentHistory = $paymentModel->getPaymentHistory($id);
        
        return $this->render('participant/payment/detail', ['paymentId' => $id]);
    }
    
    /**
     * Process a new payment attempt for a specific program payment
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
        
        // Process the payment (in a real implementation, this would connect to a payment gateway)
        // $paymentModel = new \App\Models\ProgramPaymentModel();
        // $result = $paymentModel->processPayment(
        //     $this->request->getPost('paymentId'),
        //     $this->request->getPost('paymentMethod'),
        //     $this->request->getPost('amount')
        // );
        
        // Simulate successful payment for demonstration
        return redirect()->back()->with('success', 'Payment processed successfully');
    }
    
    /**
     * Download payment receipt for a completed payment attempt
     * 
     * @param int $id The payment attempt ID
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