<?php

// Use centralized helper functions
require_once(__DIR__ . '/helpers/payment_helpers.php');
?>

<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/title-meta', array('title' => 'Payments')); ?>

    <?= $this->include('partials/head-css') ?> <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <style>
        /* Custom styles for DataTables responsiveness */
        .dtr-bs-modal .modal-body {
            padding: 0;
        }

        .dtr-bs-modal .table.dtr-details {
            margin-bottom: 0;
        }

        .dtr-bs-modal .table.dtr-details tr td,
        .dtr-bs-modal .table.dtr-details tr th {
            padding: 0.75rem 1rem;
            border-top: 1px solid #dee2e6;
        }

        table.dataTable>tbody>tr.child ul.dtr-details>li {
            border-bottom: 1px solid #efefef;
            padding: 0.75em 0;
        }

        /* Make sure datatable adjusts to different screen sizes */
        .dataTables_wrapper {
            width: 100% !important;
        }

        .dataTables_wrapper .row {
            width: 100%;
            margin: 0;
        }

        /* Ensure the table stays within its container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Fix table width issues */
        table.dataTable {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        @media (max-width: 767.98px) {

            div.dataTables_wrapper div.dataTables_paginate,
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter {
                text-align: center;
                margin-bottom: 10px;
            }            /* Mobile scroll warning styles */
            #mobileScrollWarning {
                border-left: 4px solid #0dcaf0;
                animation: pulse 2s infinite;
                margin-bottom: 15px;
                background-color: rgba(13, 202, 240, 0.15);
                border-radius: 6px;
            }

            #mobileScrollWarning .ri-arrow-left-right-fill {
                animation: leftRight 1.5s infinite;
                display: inline-block;
            }
            
            @keyframes leftRight {
                0% {
                    transform: translateX(-3px);
                }
                50% {
                    transform: translateX(3px);
                }
                100% {
                    transform: translateX(-3px);
                }
            }

            @keyframes pulse {
                0% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.9;
                }

                100% {
                    opacity: 1;
                }
            }

            /* Show horizontal scroll indicator on mobile */
            .table-responsive:after {
                content: "← Swipe to see more →";
                display: block;
                text-align: center;
                font-size: 12px;
                color: #6c757d;
                padding: 5px 0;
                margin-top: 5px;
            }
        }
    </style>


</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?= $this->include('partials/menu') ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php echo view('partials/page-title', array('pagetitle' => 'Payments', 'title' => 'Program Payments')); ?>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Required Program Payments</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Access and manage your required program payments. You can view payment details, check payment status, and process payments for your program through this dashboard.</p>

                                    <!-- Payment Status Overview -->
                                    <div class="row mb-4">
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card mini-stats-wid border-success shadow-none card-h-100">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium mb-2">Complete Payments</p>
                                                            <h4 class="mb-0">
                                                                <?php
                                                                $completePayments = 0;
                                                                // Count successful program payments instead of participant payments
                                                                if (!empty($programPayments)) {
                                                                    foreach ($programPayments as $programPayment) {
                                                                        $isPaid = false;
                                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                                            foreach ($participantPayments as $payment) {
                                                                                // Safety check for program_payment_id existence
                                                                                if (isset($payment['program_payment_id']) && $payment['program_payment_id'] == $programPayment['id'] && $payment['status'] == 2) {
                                                                                    $isPaid = true;
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                        if ($isPaid) {
                                                                            $completePayments++;
                                                                        }
                                                                    }
                                                                }
                                                                echo $completePayments;
                                                                ?>
                                                            </h4>
                                                        </div>
                                                        <div class="flex-shrink-0 align-self-center">
                                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success-subtle">
                                                                <span class="avatar-title bg-success-subtle text-success">
                                                                    <i class="ri-checkbox-circle-line font-size-20"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card mini-stats-wid border-warning shadow-none card-h-100">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium mb-2">Pending Payments</p>
                                                            <h4 class="mb-0">
                                                                <?php
                                                                $pendingPayments = 0;
                                                                $currentDate = new DateTime();

                                                                if (!empty($programPayments)) {
                                                                    foreach ($programPayments as $programPayment) {
                                                                        $startDate = new DateTime($programPayment['start_date']);
                                                                        $endDate = new DateTime($programPayment['end_date']);
                                                                        $isPending = false;
                                                                        $isPaid = false;

                                                                        // Check if this payment is already in progress or paid
                                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                                            foreach ($participantPayments as $payment) {
                                                                                if (isset($payment['program_payment_id']) && $payment['program_payment_id'] == $programPayment['id']) {
                                                                                    if ($payment['status'] == 2) {
                                                                                        $isPaid = true;
                                                                                        break;
                                                                                    } else if ($payment['status'] == 0 || $payment['status'] == 1) {
                                                                                        $isPending = true;
                                                                                    }
                                                                                }
                                                                            }
                                                                        }

                                                                        // Count as pending if it's not paid, within the payment period,
                                                                        // and either has a pending payment attempt or is due now
                                                                        if (
                                                                            !$isPaid && $currentDate >= $startDate && $currentDate <= $endDate &&
                                                                            ($isPending || !isset($isPending))
                                                                        ) {
                                                                            $pendingPayments++;
                                                                        }
                                                                    }
                                                                }
                                                                echo $pendingPayments;
                                                                ?>
                                                            </h4>
                                                        </div>
                                                        <div class="flex-shrink-0 align-self-center">
                                                            <div class="avatar-sm rounded-circle bg-warning-subtle mini-stat-icon">
                                                                <span class="avatar-title bg-warning-subtle text-warning">
                                                                    <i class="ri-time-line font-size-20"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card mini-stats-wid border-danger shadow-none card-h-100">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium mb-2">Overdue Payments</p>
                                                            <h4 class="mb-0">
                                                                <?php
                                                                $overduePayments = 0;
                                                                $currentDate = new DateTime();

                                                                if (!empty($programPayments)) {
                                                                    foreach ($programPayments as $programPayment) {
                                                                        $endDate = new DateTime($programPayment['end_date']);
                                                                        $isPaid = false;

                                                                        // Check if this program payment has been completed
                                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                                            foreach ($participantPayments as $payment) {
                                                                                if (isset($payment['program_payment_id']) && $payment['program_payment_id'] == $programPayment['id'] && $payment['status'] == 2) {
                                                                                    $isPaid = true;
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }

                                                                        // Count as overdue if payment deadline has passed and payment is not completed
                                                                        if (!$isPaid && $currentDate > $endDate) {
                                                                            $overduePayments++;
                                                                        }
                                                                    }
                                                                }
                                                                echo $overduePayments;
                                                                ?>
                                                            </h4>
                                                        </div>
                                                        <div class="flex-shrink-0 align-self-center">
                                                            <div class="avatar-sm rounded-circle bg-danger-subtle mini-stat-icon">
                                                                <span class="avatar-title bg-danger-subtle text-danger">
                                                                    <i class="ri-alert-line font-size-20"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card mini-stats-wid border-info shadow-none card-h-100">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium mb-2">Total Required</p>
                                                            <h4 class="mb-0">
                                                                <?php
                                                                $totalAmount = 0;
                                                                $currentDate = new DateTime();

                                                                if (!empty($programPayments)) {
                                                                    foreach ($programPayments as $programPayment) {
                                                                        $endDate = new DateTime($programPayment['end_date']);
                                                                        $isPaid = false;

                                                                        // Check if this payment is already successfully paid
                                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                                            foreach ($participantPayments as $payment) {
                                                                                if (isset($payment['program_payment_id']) && $payment['program_payment_id'] == $programPayment['id'] && $payment['status'] == 2) {
                                                                                    $isPaid = true;
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }

                                                                        // Only include in total if payment is not overdue and not already paid
                                                                        if (!$isPaid && $currentDate <= $endDate) {
                                                                            $totalAmount += (float)$programPayment['usd_amount'];
                                                                        }
                                                                    }
                                                                }
                                                                echo '$' . number_format($totalAmount, 2);
                                                                ?>
                                                            </h4>
                                                        </div>
                                                        <div class="flex-shrink-0 align-self-center">
                                                            <div class="avatar-sm rounded-circle bg-info-subtle mini-stat-icon">
                                                                <span class="avatar-title bg-info-subtle text-info">
                                                                    <i class="ri-wallet-3-line font-size-20"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    <!-- Mobile scroll warning - only visible on small screens -->
                                    <div class="alert alert-info d-block d-md-none mb-3" id="mobileScrollWarning">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-information-line me-2 fs-16"></i>
                                            <div>
                                                <strong>Mobile Users:</strong> Please scroll horizontally to see all payment details and action buttons.
                                                <div class="mt-2 d-flex align-items-center justify-content-start">
                                                    <i class="ri-arrow-left-right-fill me-1 fs-16"></i> 
                                                    <span class="fw-bold">Swipe right to see payment buttons</span>
                                                </div>
                                                <div class="mt-1">
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        <i class="ri-hand-coin-line me-1"></i> Payment buttons are on the right
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="paymentDatatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" style="width: 50px;" data-priority="1">
                                                        #
                                                    </th>
                                                    <th scope="col" data-priority="1">Payment Information</th>
                                                    <th scope="col" data-priority="3">Period</th>
                                                    <th scope="col" data-priority="2">Amount</th>
                                                    <th scope="col" data-priority="2">Payment Status</th>
                                                    <th scope="col" data-priority="1">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($programPayments)): ?>
                                                    <?php foreach ($programPayments as $key => $programPayment): ?>
                                                        <?php $startDate = new DateTime($programPayment['start_date']);
                                                        $endDate = new DateTime($programPayment['end_date']);
                                                        $currentDate = new DateTime();

                                                        // Calculate days remaining until deadline
                                                        $daysRemaining = $currentDate->diff($endDate)->format('%r%a'); // %r preserves the sign, %a gives days
                                                        if ($daysRemaining >= 0) {
                                                            $daysRemaining++; // Include the last day in the calculation
                                                        }

                                                        // Format the period string with the date range
                                                        $period = $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');

                                                        // Get current payment status from participant payments
                                                        $status = 'unpaid'; // Default status
                                                        $latestPayment = null;
                                                        $latestTimestamp = 0;

                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                            foreach ($participantPayments as $participantPayment) {
                                                                if (isset($participantPayment['program_payment_id']) && $participantPayment['program_payment_id'] == $programPayment['id']) {
                                                                    // Track the latest payment attempt by timestamp
                                                                    $paymentTimestamp = strtotime($participantPayment['created_at'] ?? '0');
                                                                    if ($paymentTimestamp > $latestTimestamp) {
                                                                        $latestTimestamp = $paymentTimestamp;
                                                                        $latestPayment = $participantPayment;
                                                                        // Update status based on latest payment
                                                                        $status = $participantPayment['status'] == 0 ? 'created' : ($participantPayment['status'] == 1 ? 'pending' : ($participantPayment['status'] == 2 ? 'paid' : ($participantPayment['status'] == 3 ? 'cancelled' : 'rejected')));
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        // Add days remaining indicator - only if payment isn't completed
                                                        if ($daysRemaining > 0 && $status != 'paid' && $status != 'complete') {
                                                            $periodBadgeClass = ($daysRemaining <= 3) ? 'bg-danger-subtle text-danger' : (($daysRemaining <= 7) ? 'bg-warning-subtle text-warning' :
                                                                'bg-info-subtle text-info');
                                                        }

                                                        // Check for all payments related to this program payment
                                                        $payment = null;
                                                        $latestPayment = null;
                                                        $hasSuccessfulPayment = false;
                                                        $latestTimestamp = 0;

                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                            foreach ($participantPayments as $participantPayment) {
                                                                if (isset($participantPayment['program_payment_id']) && $participantPayment['program_payment_id'] == $programPayment['id']) {
                                                                    // Check if this is a successful payment
                                                                    if ($participantPayment['status'] == 2) {
                                                                        $hasSuccessfulPayment = true;
                                                                        $payment = $participantPayment; // Store successful payment for receipt link
                                                                    }

                                                                    // Track the latest payment attempt by timestamp (if available) or just the last one we encounter
                                                                    if (isset($participantPayment['created_at'])) {
                                                                        $paymentTimestamp = strtotime($participantPayment['created_at']);
                                                                        if ($paymentTimestamp > $latestTimestamp) {
                                                                            $latestTimestamp = $paymentTimestamp;
                                                                            $latestPayment = $participantPayment;
                                                                        }
                                                                    } else {
                                                                        $latestPayment = $participantPayment;
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        // Set payment reference for receipt if successful payment found
                                                        if (!$payment && $latestPayment) {
                                                            $payment = $latestPayment;
                                                        }

                                                        // Check if payment status is (0: created, 1: pending, 2: success, 3: cancelled, 4: rejected),	
                                                        // if payment is not found, set status to unpaid
                                                        if ($hasSuccessfulPayment) {
                                                            $status = 'paid';
                                                        } else if (isset($latestPayment)) {
                                                            $status = $latestPayment['status'] == 0 ? 'created' : ($latestPayment['status'] == 1 ? 'pending' : ($latestPayment['status'] == 2 ? 'paid' : ($latestPayment['status'] == 3 ? 'cancelled' : 'rejected')));
                                                        } else {
                                                            $status = 'unpaid';
                                                        }

                                                        // Determine due status
                                                        $dueStatus = '';
                                                        $dueClass = '';

                                                        if ($status == 'paid') {
                                                            $dueStatus = 'Completed';
                                                            $dueClass = 'success';
                                                        } else if ($currentDate > $endDate) {
                                                            $dueStatus = 'Overdue';
                                                            $dueClass = 'danger';
                                                        } else if ($currentDate >= $startDate) {
                                                            $dueStatus = 'Ongoing';
                                                            $dueClass = 'warning';
                                                        } else {
                                                            $dueStatus = 'Upcoming';
                                                            $dueClass = 'info';
                                                        }

                                                        $programPayment['status'] = $status;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= $key + 1; ?>
                                                            </td>
                                                            <td> <strong><?= $programPayment['name']; ?></strong>
                                                                <?php
                                                                // Normalize category names
                                                                $normalizedCategory = $programPayment['category'];

                                                                // Convert to lowercase first to standardize
                                                                $lowerCategory = strtolower($normalizedCategory);

                                                                // Handle specific conversions
                                                                if ($lowerCategory === 'registration') {
                                                                    $normalizedCategory = 'Registration Fee';
                                                                } elseif (strpos($lowerCategory, 'program_fee') !== false) {
                                                                    $normalizedCategory = 'Program Fee';
                                                                } elseif ($lowerCategory === 'deposit') {
                                                                    $normalizedCategory = 'Deposit';
                                                                } else {
                                                                    // Capitalize first letter of each word for other categories
                                                                    $normalizedCategory = ucwords($normalizedCategory);
                                                                }
                                                                ?>
                                                                <span class="badge bg-secondary-subtle text-secondary fs-11 ms-1"><?= $normalizedCategory; ?></span>
                                                            </td>
                                                            <td>
                                                                <?= $period; ?>
                                                                <?php if (isset($daysRemaining) && $daysRemaining > 0 && $programPayment['status'] != 'paid' && $programPayment['status'] != 'complete'): ?>
                                                                    <div class="mt-1">
                                                                        <span class="badge <?= $periodBadgeClass; ?> fs-11">
                                                                            <i class="ri-time-line align-bottom me-1"></i>
                                                                            <?= $daysRemaining; ?> day<?= ($daysRemaining != 1) ? 's' : ''; ?> remaining
                                                                        </span>
                                                                    </div>
                                                                <?php elseif (isset($daysRemaining) && $daysRemaining <= 0 && $programPayment['status'] != 'paid' && $programPayment['status'] != 'complete'): ?>
                                                                    <div class="mt-1">
                                                                        <span class="badge bg-danger-subtle text-danger fs-11">
                                                                            <i class="ri-error-warning-line align-bottom me-1"></i>
                                                                            Deadline passed
                                                                        </span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= formatCurrency($programPayment['usd_amount'], 'USD'); ?></td>
                                                            <td>
                                                                <?php if ($programPayment['status'] == 'unpaid'): ?>
                                                                    <span class="badge bg-danger-subtle text-danger"><?= ucfirst($programPayment['status']); ?></span>
                                                                <?php elseif ($programPayment['status'] == 'pending'): ?>
                                                                    <span class="badge bg-warning-subtle text-warning"><?= ucfirst($programPayment['status']); ?></span>
                                                                <?php elseif ($programPayment['status'] == 'paid' || $programPayment['status'] == 'complete'): ?>
                                                                    <span class="badge bg-success-subtle text-success"><?= ucfirst($programPayment['status']); ?></span>
                                                                <?php elseif ($programPayment['status'] == 'cancelled'): ?>
                                                                    <span class="badge bg-danger-subtle text-danger"><?= ucfirst($programPayment['status']); ?></span>
                                                                <?php elseif ($programPayment['status'] == 'rejected'): ?>
                                                                    <span class="badge bg-danger-subtle text-danger"><?= ucfirst($programPayment['status']); ?></span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary-subtle text-secondary"><?= ucfirst($programPayment['status']); ?></span>
                                                                <?php endif; ?>
                                                            </td>

                                                            <td>
                                                                <div class="d-flex gap-2 justify-content-start align-items-center">
                                                                    <a href="<?= site_url('payments/detail/' . $programPayment['id']); ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Details">
                                                                        <i class="ri-eye-fill align-middle"></i>
                                                                    </a>
                                                                    <?php if ($programPayment['status'] == 'unpaid'): ?>
                                                                        <button type="button" class="btn btn-sm btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                                                            data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                                                            data-payment-name="<?= $programPayment['name'] ?? 'Program Payment'; ?>"
                                                                            data-payment-amount="<?= $programPayment['usd_amount'] ?? '0.00'; ?>"
                                                                            data-payment-category="<?= $programPayment['category'] ?? ''; ?>"
                                                                            data-payment-index="<?= $key; ?>"
                                                                            title="Make Payment">
                                                                            <i class="ri-bank-card-line align-middle me-1"></i> Pay Now
                                                                        </button>
                                                                    <?php elseif ($programPayment['status'] == 'pending'): ?>
                                                                        <button type="button" class="btn btn-sm btn-warning" disabled title="Payment Processing">
                                                                            <i class="ri-time-line align-middle me-1"></i> Processing
                                                                        </button>
                                                                    <?php elseif ($programPayment['status'] == 'paid' || $programPayment['status'] == 'complete'): ?> <a target="_blank" href="<?= site_url('payments/receipt/' . $payment['id']); ?>" class="btn btn-sm btn-info receipt-button" title="Download Receipt">
                                                                            <i class="ri-download-2-line align-middle me-1"></i> Receipt
                                                                        </a>
                                                                    <?php elseif (($programPayment['status'] == 'cancelled' || $programPayment['status'] == 'rejected') && $dueStatus != 'Overdue'): ?>
                                                                        <button type="button" class="btn btn-sm btn-danger payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                                                            data-payment-id="<?= $programPayment['id']; ?>"
                                                                            data-payment-name="<?= $programPayment['name'] ?? 'Program Payment'; ?>"
                                                                            data-payment-amount="<?= $programPayment['usd_amount'] ?? '0.00'; ?>"
                                                                            data-payment-category="<?= $programPayment['category'] ?? ''; ?>"
                                                                            data-payment-index="<?= $key; ?>"
                                                                            title="Try Payment Again">
                                                                            <i class="ri-refresh-line align-middle me-1"></i> Try Again
                                                                        </button>
                                                                    <?php elseif ($programPayment['status'] == 'cancelled' || $programPayment['status'] == 'rejected'): ?>
                                                                        <button type="button" class="btn btn-sm btn-secondary" disabled title="Payment Expired">
                                                                            <i class="ri-time-line align-middle me-1"></i> Expired
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">No payment records found</td>
                                                    </tr>
                                                <?php endif; ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Include Payment Modal Widget -->
                        <?php echo view('participant/payment/widgets/payment_modal', [
                            'paymentMethods' => $paymentMethods ?? null,
                            'selectedProgramPayment' => $selectedProgramPayment ?? null,
                        ]); ?>

                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                <?= $this->include('partials/footer') ?>
            </div>
            <!-- end main content-->
        </div>
        <!-- END layout-wrapper -->

        <?= $this->include('partials/vendor-scripts') ?>

        <!-- App js -->
        <script src="/assets/js/app.js"></script>
        <!-- Add SweetAlert2 library for better user notifications -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!--datatable js-->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

        <!-- Note: We're not using the global datatables.init.js as we need custom initialization -->
        <!-- <script src="/assets/js/pages/datatables.init.js"></script> -->


        <script>
            // Store program payments in a JavaScript variable for access in client-side code
            const programPayments = <?= json_encode($programPayments ?? []); ?>;
            const paymentMethods = <?= json_encode($paymentMethods ?? []); ?>;

            document.addEventListener('DOMContentLoaded', function() {
                $(document).ready(function() {                    // Check if we're on a mobile device and make sure warning is visible
                    function checkMobileView() {
                        if (window.innerWidth < 768) {
                            $('#mobileScrollWarning').addClass('d-block').removeClass('d-none');
                        } else {
                            $('#mobileScrollWarning').removeClass('d-block').addClass('d-none d-md-none');
                        }
                    }
                    
                    // Run check on page load
                    checkMobileView();
                    
                    // Check on window resize
                    $(window).on('resize', function() {
                        checkMobileView();
                    });
                    
                    // Initialize the DataTable with proper responsive settings
                    var paymentTable = $('#paymentDatatable').DataTable({
                        responsive: {
                            details: {
                                type: 'column',
                                target: 'tr',
                                renderer: function(api, rowIdx, columns) {
                                    var data = $.map(columns, function(col, i) {
                                        return col.hidden ?
                                            '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                            '<td class="fw-medium">' + col.title + ':</td> ' +
                                            '<td>' + col.data + '</td>' +
                                            '</tr>' :
                                            '';
                                    }).join('');

                                    return data ? $('<table class="table table-sm m-0"></table>').append(data) : false;
                                }
                            }
                        },
                        columnDefs: [{
                                responsivePriority: 1,
                                targets: [0, 1, 5]
                            }, // Always visible
                            {
                                responsivePriority: 2,
                                targets: [3, 4]
                            }, // Next priority
                            {
                                responsivePriority: 3,
                                targets: 2
                            }, // Lowest priority
                            // Set width for specific columns
                            {
                                width: "50px",
                                targets: 0
                            }, // # column
                            {
                                width: "30%",
                                targets: 1
                            }, // Payment Info column
                            {
                                width: "15%",
                                targets: 2
                            } // Period column
                        ],
                        paging: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        autoWidth: false,
                        scrollX: true,
                        scrollCollapse: true,
                        language: {
                            paginate: {
                                previous: "<i class='mdi mdi-chevron-left'>",
                                next: "<i class='mdi mdi-chevron-right'>"
                            },
                            search: "<i class='ri-search-line me-1'></i> Search:",
                            lengthMenu: "Show _MENU_ entries"
                        },
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, "All"]
                        ],
                        drawCallback: function() {
                            $('[data-bs-toggle="tooltip"]').tooltip();
                            // Force the table to recalculate its layout
                            $(window).trigger('resize');
                            // Reinitialize any components that might be in the table
                            if (typeof initComponents === 'function') {
                                initComponents();
                            }
                        }
                    });

                    // Handle window resize to properly adjust the table
                    $(window).on('resize', function() {
                        // Adjust column widths and recalculate responsive layout
                        paymentTable.columns.adjust().responsive.recalc();
                    });

                    // Initial adjustment
                    setTimeout(function() {
                        paymentTable.columns.adjust().responsive.recalc();
                    }, 100);
                });

                // Check if there are any flash messages set in session
                <?php if (session()->has('swal')): ?>
                    // Parse the JSON flash data
                    const swalData = JSON.parse('<?= session()->getFlashdata('swal') ?>');

                    // Display SweetAlert2 notification
                    Swal.fire({
                        title: swalData.title || '',
                        text: swalData.text || '',
                        icon: swalData.icon || 'info',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        position: 'center',
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                <?php endif; ?>

                // Add click handler for receipt download buttons
                const receiptButtons = document.querySelectorAll('.receipt-button');
                receiptButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault(); // Prevent default to handle the navigation manually

                        // Get the original href
                        const downloadUrl = this.getAttribute('href');

                        // Show loading notification
                        Swal.fire({
                            title: 'Generating Receipt',
                            html: 'Please wait while we generate your receipt...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Use fetch to request the receipt generation
                        fetch(downloadUrl)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Failed to generate receipt');
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                // Create blob URL
                                const blobUrl = URL.createObjectURL(blob);

                                // Close the loading modal
                                Swal.close();

                                // Show success message with option to open/download
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Receipt Ready',
                                    text: 'Your receipt has been generated successfully!',
                                    footer: '<small>You can view or save the receipt using the buttons below.</small>',
                                    showCancelButton: true,
                                    confirmButtonText: 'View Receipt',
                                    cancelButtonText: 'Download Receipt',
                                    showCloseButton: true,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Open in new tab
                                        window.open(blobUrl, '_blank');
                                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                                        // Create temporary link for download
                                        const a = document.createElement('a');
                                        a.href = blobUrl;
                                        a.download = 'receipt_' + Date.now() + '.pdf';
                                        document.body.appendChild(a);
                                        a.click();
                                        document.body.removeChild(a);
                                    }
                                });
                            })
                            .catch(error => {
                                console.error('Error generating receipt:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error Generating Receipt',
                                    text: 'There was a problem generating your receipt. Please try again later.',
                                    showCloseButton: true
                                });
                            });
                    });
                });

                // Payment modal data
                const makePaymentModal = document.getElementById('makePaymentModal');
                if (makePaymentModal) {
                    makePaymentModal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const paymentId = button.getAttribute('data-payment-id');
                        const paymentIndex = button.getAttribute('data-payment-index');

                        // Get the full program payment object using the index
                        const selectedPayment = programPayments[paymentIndex] || null;
                        if (selectedPayment) {
                            // Set the selected program payment data to hidden input to be sent to the server
                            document.getElementById('program_payment_id').value = selectedPayment.id;
                            document.getElementById('payment_amount').value = selectedPayment.usd_amount;

                            // Update display elements
                            if (document.getElementById('payment_description')) {
                                document.getElementById('payment_description').textContent = selectedPayment.name || 'Program Payment';
                            }

                            if (document.getElementById('payment_amount_display')) {
                                document.getElementById('payment_amount_display').textContent = '$' + parseFloat(selectedPayment.usd_amount).toFixed(2);
                            }

                            if (document.getElementById('payment_reference')) {
                                document.getElementById('payment_reference').textContent = 'YBB-' + selectedPayment.id;
                            }

                            // Set hidden form field with the complete payment object
                            const paymentDataField = document.createElement('input');
                            paymentDataField.type = 'hidden';
                            paymentDataField.name = 'selectedProgramPayment';
                            paymentDataField.value = JSON.stringify(selectedPayment);

                            // Replace existing field if it exists, or add a new one
                            const existingField = document.querySelector('input[name="selectedProgramPayment"]');
                            if (existingField) {
                                existingField.value = JSON.stringify(selectedPayment);
                            } else {
                                document.getElementById('paymentForm').appendChild(paymentDataField);
                            }
                        }
                    });
                }

                // Payment method fields toggle
                const paymentMethodSelect = document.getElementById('paymentMethod');
                if (paymentMethodSelect) {
                    paymentMethodSelect.addEventListener('change', function() {
                        // Hide all programPayment method fields
                        document.querySelectorAll('.programPayment-method-fields').forEach(function(field) {
                            field.style.display = 'none';
                        });

                        // Show selected programPayment method fields
                        if (this.value === 'credit_card' || this.value === 'debit_card') {
                            document.getElementById('creditCardFields').style.display = 'block';
                        } else if (this.value === 'bank_transfer') {
                            document.getElementById('bankTransferFields').style.display = 'block';
                        }
                    });
                }
            });
        </script>

        <script src="/assets/js/pages/payment-gateway-handler.js"></script>


</body>

</html>