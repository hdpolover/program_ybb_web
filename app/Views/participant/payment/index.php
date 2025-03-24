<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/simple-title-meta', array('title' => 'Profile')); ?>
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
                                    <p class="text-muted">View and manage your program payment requirements. Click on a payment to see details or make a payment.</p>

                                    <!-- Payment Status Overview -->
                                    <div class="row mb-4">
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card mini-stats-wid border-success shadow-none card-h-100">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted fw-medium mb-2">Complete Payments</p>
                                                            <h4 class="mb-0">1</h4>
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
                                                            <h4 class="mb-0">2</h4>
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
                                                            <h4 class="mb-0">0</h4>
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
                                                            <h4 class="mb-0">$370.00</h4>
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
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                                            <label class="form-check-label" for="checkAll"></label>
                                                        </div>
                                                    </th>
                                                    <th scope="col">Payment ID</th>
                                                    <th scope="col">Description</th>
                                                    <th scope="col">Due Date</th>
                                                    <th scope="col">Amount</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col" style="width: 150px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="payment1">
                                                            <label class="form-check-label" for="payment1"></label>
                                                        </div>
                                                    </td>
                                                    <td><a href="<?= site_url('payments/detail/1'); ?>" class="fw-medium">#YBB-REG-001</a></td>
                                                    <td>Program Registration Fee</td>
                                                    <td>Mar 01, 2025</td>
                                                    <td>$200.00</td>
                                                    <td><span class="badge badge-soft-success">Paid</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="<?= site_url('payments/detail/1'); ?>" class="btn btn-sm btn-primary">View Details</a>
                                                            <a href="<?= site_url('payments/receipt/1'); ?>" class="btn btn-sm btn-info">Receipt</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="payment2">
                                                            <label class="form-check-label" for="payment2"></label>
                                                        </div>
                                                    </td>
                                                    <td><a href="<?= site_url('payments/detail/2'); ?>" class="fw-medium">#YBB-MAT-002</a></td>
                                                    <td>Program Materials Fee</td>
                                                    <td>Mar 15, 2025</td>
                                                    <td>$120.00</td>
                                                    <td><span class="badge badge-soft-warning">Pending</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="<?= site_url('payments/detail/2'); ?>" class="btn btn-sm btn-primary">View Details</a>
                                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#makePaymentModal" data-payment-id="2" data-payment-amount="120.00" data-payment-description="Program Materials Fee">
                                                                Make Payment
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="payment3">
                                                            <label class="form-check-label" for="payment3"></label>
                                                        </div>
                                                    </td>
                                                    <td><a href="<?= site_url('participant/payment/detail/3'); ?>" class="fw-medium">#YBB-ACT-003</a></td>
                                                    <td>Activity & Field Trip Fees</td>
                                                    <td>Apr 05, 2025</td>
                                                    <td>$50.00</td>
                                                    <td><span class="badge badge-soft-warning">Pending</span></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="<?= site_url('participant/payment/detail/3'); ?>" class="btn btn-sm btn-primary">View Details</a>
                                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#makePaymentModal" data-payment-id="3" data-payment-amount="50.00" data-payment-description="Activity & Field Trip Fees">
                                                                Make Payment
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
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
                                    <form action="<?= site_url('participant/payment/make'); ?>" method="post" id="paymentForm">
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

                                        <div id="creditCardFields" class="payment-method-fields" style="display: none;">
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

                                        <div id="bankTransferFields" class="payment-method-fields" style="display: none;">
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
                    const paymentId = button.getAttribute('data-payment-id');
                    const paymentAmount = button.getAttribute('data-payment-amount');
                    const paymentDescription = button.getAttribute('data-payment-description');

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
                    // Hide all payment method fields
                    document.querySelectorAll('.payment-method-fields').forEach(function(field) {
                        field.style.display = 'none';
                    });

                    // Show selected payment method fields
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