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
                                    <p class="text-muted">View and manage your program programPayment requirements. Click on a programPayment to see details or make a programPayment.</p>

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
                                                                if (isset($participantPayments) && !empty($participantPayments)) {
                                                                    foreach ($participantPayments as $payment) {
                                                                        if ($payment['status'] == 2) {
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
                                                                if (!empty($programPayments)) {
                                                                    foreach ($programPayments as $programPayment) {
                                                                        $totalAmount += (float)$programPayment['usd_amount'];
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
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Period</th>
                                                    <th scope="col">Amount (USD)</th>
                                                    <th scope="col">Category</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($programPayments)): ?>
                                                    <?php foreach ($programPayments as $key => $programPayment): ?>
                                                        <?php

                                                        $startDate = new DateTime($programPayment['start_date']);
                                                        $endDate = new DateTime($programPayment['end_date']);

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

                                                        $programPayment['status'] = $status;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= $key + 1; ?>
                                                            </td>
                                                            <td><strong><?= $programPayment['name']; ?></strong></td>
                                                            <td><?= $period; ?></td>
                                                            <td><?= $programPayment['usd_amount']; ?></td>
                                                            <td><?= $programPayment['category']; ?></td>
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
                                                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                                                            data-programPayment-id="<?= $programPayment['id']; ?>"
                                                                            data-programPayment-amount="<?= $programPayment['usd_amount']; ?>"
                                                                            data-programPayment-description="<?= $programPayment['name']; ?>"
                                                                            title="Make Payment">
                                                                            <i class="ri-bank-card-line align-middle me-1"></i> Pay Now
                                                                        </button>
                                                                    <?php elseif ($programPayment['status'] == 'pending'): ?>
                                                                        <button type="button" class="btn btn-sm btn-warning" disabled title="Payment Processing">
                                                                            <i class="ri-time-line align-middle me-1"></i> Processing
                                                                        </button>
                                                                    <?php elseif ($programPayment['status'] == 'paid' || $programPayment['status'] == 'complete'): ?>
                                                                        <a href="<?= site_url('participant/programPayment/receipt/' . $programPayment['id']); ?>" class="btn btn-sm btn-info" title="Download Receipt">
                                                                            <i class="ri-download-2-line align-middle me-1"></i> Receipt
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <!-- <div class="dropdown">
                                                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="actionDropdown<?= $programPayment['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                                            <i class="ri-more-2-fill align-middle"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu" aria-labelledby="actionDropdown<?= $programPayment['id']; ?>">
                                                                            <li><a class="dropdown-item" href="<?= site_url('participant/support/programPayment/' . $programPayment['id']); ?>">
                                                                                    <i class="ri-question-line align-middle me-1"></i> Get Help
                                                                                </a></li>
                                                                            <li><a class="dropdown-item" href="<?= site_url('participant/programPayment/history/' . $programPayment['id']); ?>">
                                                                                    <i class="ri-history-line align-middle me-1"></i> Payment History
                                                                                </a></li>
                                                                        </ul>
                                                                    </div> -->
                                                                </div>
                                                            </td>

                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">No programPayment records found</td>
                                                    </tr>
                                                <?php endif; ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Make Payment Modal -->
                    <div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="makePaymentModalLabel">Make Payment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="<?= site_url('participant/programPayment/make'); ?>" method="post" id="paymentForm">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="paymentId" id="payment_id" value="">
                                        <input type="hidden" name="amount" id="payment_amount" value="">

                                        <div class="mb-3">
                                            <label class="form-label">Payment Description</label>
                                            <p id="payment_description" class="form-control-static mb-0 fw-medium"></p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Amount</label>
                                            <p id="payment_amount_display" class="form-control-static mb-0 fw-medium"></p>
                                        </div>

                                        <div class="mb-3">
                                            <label for="paymentMethod" class="form-label">Payment Method</label>
                                            <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                                <option value="">Select Payment Method</option>
                                                <option value="credit_card">Credit Card</option>
                                                <option value="debit_card">Debit Card</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="paypal">PayPal</option>
                                            </select>
                                        </div>

                                        <div id="creditCardFields" class="programPayment-method-fields" style="display: none;">
                                            <div class="mb-3">
                                                <label for="cardNumber" class="form-label">Card Number</label>
                                                <input type="text" class="form-control" id="cardNumber" placeholder="XXXX XXXX XXXX XXXX">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="cardExpiry" class="form-label">Expiry Date</label>
                                                        <input type="text" class="form-control" id="cardExpiry" placeholder="MM/YY">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="cardCvv" class="form-label">CVV</label>
                                                        <input type="text" class="form-control" id="cardCvv" placeholder="XXX">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="cardName" class="form-label">Cardholder Name</label>
                                                <input type="text" class="form-control" id="cardName" placeholder="Name on card">
                                            </div>
                                        </div>

                                        <div id="bankTransferFields" class="programPayment-method-fields" style="display: none;">
                                            <div class="alert alert-info">
                                                <p class="mb-0">Please use the following details for bank transfer:</p>
                                                <p class="mb-0 mt-2">Bank: National Bank</p>
                                                <p class="mb-0">Account Name: Youth Beyond Boundaries</p>
                                                <p class="mb-0">Account Number: 1234567890</p>
                                                <p class="mb-0">Reference: <span id="payment_reference"></span></p>
                                            </div>
                                            <div class="mb-3">
                                                <label for="transferDate" class="form-label">Transfer Date</label>
                                                <input type="date" class="form-control" id="transferDate">
                                            </div>
                                            <div class="mb-3">
                                                <label for="transferReference" class="form-label">Your Transfer Reference</label>
                                                <input type="text" class="form-control" id="transferReference" placeholder="Reference number from your bank">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" form="paymentForm" class="btn btn-success">Complete Payment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment modal data
            const makePaymentModal = document.getElementById('makePaymentModal');
            if (makePaymentModal) {
                makePaymentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const paymentId = button.getAttribute('data-programPayment-id');
                    const paymentAmount = button.getAttribute('data-programPayment-amount');
                    const paymentDescription = button.getAttribute('data-programPayment-description');

                    document.getElementById('payment_id').value = paymentId;
                    document.getElementById('payment_amount').value = paymentAmount;
                    document.getElementById('payment_description').textContent = paymentDescription;
                    document.getElementById('payment_amount_display').textContent = '$' + paymentAmount;
                    document.getElementById('payment_reference').textContent = 'YBB-' + paymentId;
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