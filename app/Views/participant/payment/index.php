<?php

// Use centralized helper functions
require_once(__DIR__ . '/helpers/payment_helpers.php');
?>

<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/title-meta', array('title' => 'Payments')); ?>
    <?= $this->include('partials/head-css') ?>
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
                                    <p class="text-muted">View and manage your program payment requirements. Click on a programPayment to see details or make a programPayment.</p>

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
                                                                                if ($payment['program_payment_id'] == $programPayment['id'] && $payment['status'] == 2) {
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
                                                                if (isset($participantPayments) && !empty($participantPayments)) {
                                                                    foreach ($participantPayments as $payment) {
                                                                        if ($payment['status'] == 0 || $payment['status'] == 1) {
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

                                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                                            foreach ($participantPayments as $payment) {
                                                                                if ($payment['program_payment_id'] == $programPayment['id'] && $payment['status'] == 2) {
                                                                                    $isPaid = true;
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }

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
                                                                                if ($payment['program_payment_id'] == $programPayment['id'] && $payment['status'] == 2) {
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
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table align-middle table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" style="width: 50px;">
                                                        #
                                                    </th>
                                                    <th scope="col">Payment Information</th>
                                                    <th scope="col">Period</th>
                                                    <th scope="col">Amount</th>
                                                    <th scope="col">Payment Status</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($programPayments)): ?>
                                                    <?php foreach ($programPayments as $key => $programPayment): ?>
                                                        <?php

                                                        $startDate = new DateTime($programPayment['start_date']);
                                                        $endDate = new DateTime($programPayment['end_date']);
                                                        $currentDate = new DateTime();

                                                        $period = $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');

                                                        // loop through participantPayments to check if programPayment exists
                                                        $payment = null;

                                                        if (isset($participantPayments) && !empty($participantPayments)) {
                                                            foreach ($participantPayments as $participantPayment) {
                                                                if ($participantPayment['program_payment_id'] == $programPayment['id']) {
                                                                    $payment = $participantPayment;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        // Check if payment status is (0: created, 1: pending, 2: success, 3: cancelled, 4: rejected),	
                                                        // if payment is not found, set status to unpaid
                                                        if (isset($payment)) {
                                                            $status = $payment['status'] == 0 ? 'created' : ($payment['status'] == 1 ? 'pending' : ($payment['status'] == 2 ? 'paid' : ($payment['status'] == 3 ? 'cancelled' : 'rejected')));
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
                                                            <td>
                                                                <strong><?= $programPayment['name']; ?></strong>
                                                                <span class="badge bg-soft-secondary text-secondary ms-1"><?= $programPayment['category']; ?></span>
                                                            </td>
                                                            <td><?= $period; ?></td>
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
                                                                    </a> <?php if ($programPayment['status'] == 'unpaid'): ?> <button type="button" class="btn btn-sm btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                                                            data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                                                            data-payment-index="<?= $key; ?>"
                                                                            title="Make Payment">
                                                                            <i class="ri-bank-card-line align-middle me-1"></i> Pay Now
                                                                        </button>
                                                                    <?php elseif ($programPayment['status'] == 'pending'): ?>
                                                                        <button type="button" class="btn btn-sm btn-warning" disabled title="Payment Processing">
                                                                            <i class="ri-time-line align-middle me-1"></i> Processing
                                                                        </button> <?php elseif ($programPayment['status'] == 'paid' || $programPayment['status'] == 'complete'): ?>
                                                                        <a href="<?= site_url('payments/receipt/' . $payment['id']); ?>" class="btn btn-sm btn-info" title="Download Receipt">
                                                                            <i class="ri-download-2-line align-middle me-1"></i> Receipt
                                                                        </a> <?php elseif (($programPayment['status'] == 'cancelled' || $programPayment['status'] == 'rejected') && $dueStatus != 'Overdue'): ?><button type="button" class="btn btn-sm btn-danger payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                                                            data-payment-id="<?= $programPayment['id']; ?>"
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

    <script>
        // Store program payments in a JavaScript variable for access in client-side code
        const programPayments = <?= json_encode($programPayments ?? []); ?>;
        const paymentMethods = <?= json_encode($paymentMethods ?? []); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Add click handler for receipt download buttons
            const receiptButtons = document.querySelectorAll('a[href^="<?= site_url('payments/receipt/') ?>"]');
            receiptButtons.forEach(button => {
                button.addEventListener('click', function(e) {
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

                    // Continue with download - we don't prevent default here to allow normal link behavior
                    // The page will navigate away to start the download
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
                        document.getElementById('payment_id').value = selectedPayment.id;
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
</body>

</html>