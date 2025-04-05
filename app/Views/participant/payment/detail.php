<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/simple-title-meta', array('title'=>'Payment Details')); ?>
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
                    <?php echo view('partials/page-title', array('pagetitle'=>'Payments', 'title'=>'Payment Details')); ?>

                    <div class="row">
                        <div class="col-xl-4">
                            <!-- Payment Details Card -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Payment Information</h5>
                                        <div class="flex-shrink-0">
                                            <a href="<?= site_url('payments') ?>" class="btn btn-soft-primary btn-sm">
                                                <i class="ri-arrow-left-line align-middle"></i> Back to Payments
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <div class="pb-3 border-bottom border-bottom-dashed mb-4">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Payment ID</div>
                                                    <h5 class="fs-14">#<?= isset($payment['payment_reference']) ? $payment['payment_reference'] : 'YBB-' . (isset($payment['id']) ? $payment['id'] : '000'); ?></h5>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Status</div>
                                                    <?php 
                                                        $statusClass = 'secondary';
                                                        if(isset($payment['status'])) {
                                                            switch($payment['status']) {
                                                                case 0:
                                                                case 'created':
                                                                    $status = 'Created';
                                                                    $statusClass = 'info';
                                                                    break;
                                                                case 1:
                                                                case 'pending':
                                                                    $status = 'Pending';
                                                                    $statusClass = 'warning';
                                                                    break;
                                                                case 2:
                                                                case 'paid':
                                                                case 'success':
                                                                    $status = 'Paid';
                                                                    $statusClass = 'success';
                                                                    break;
                                                                case 3:
                                                                case 'cancelled':
                                                                    $status = 'Cancelled';
                                                                    $statusClass = 'danger';
                                                                    break;
                                                                case 4:
                                                                case 'rejected':
                                                                    $status = 'Rejected';
                                                                    $statusClass = 'danger';
                                                                    break;
                                                                default:
                                                                    $status = 'Unknown';
                                                                    $statusClass = 'secondary';
                                                            }
                                                        } else {
                                                            $status = 'Unknown';
                                                        }
                                                    ?>
                                                    <span class="badge badge-soft-<?= $statusClass ?> fs-11"><?= $status ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="pt-2">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Payment Type</div>
                                                    <h5 class="fs-14"><?= isset($payment['name']) ? $payment['name'] : 'Program Payment' ?></h5>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Amount</div>
                                                    <h5 class="fs-14">$<?= isset($payment['usd_amount']) ? number_format((float)$payment['usd_amount'], 2) : '0.00' ?></h5>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-4">
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Due Date</div>
                                                    <h5 class="fs-14">
                                                        <?php if(isset($payment['end_date'])): ?>
                                                            <?= date('F d, Y', strtotime($payment['end_date'])) ?>
                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </h5>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Payment Date</div>
                                                    <h5 class="fs-14">
                                                        <?php if(isset($payment['payment_date'])): ?>
                                                            <?= date('F d, Y', strtotime($payment['payment_date'])) ?>
                                                        <?php else: ?>
                                                            Pending
                                                        <?php endif; ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end card-->
                            
                            <!-- Quick Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if(isset($payment['status']) && ($payment['status'] == 2 || $payment['status'] == 'paid' || $payment['status'] == 'success')): ?>
                                            <a href="<?= site_url('participant/payment/receipt/' . (isset($payment['id']) ? $payment['id'] : '')); ?>" class="btn btn-info">
                                                <i class="ri-download-cloud-line align-bottom me-1"></i> Download Receipt
                                            </a>
                                        <?php elseif(isset($payment['status']) && ($payment['status'] == 0 || $payment['status'] == 'created' || $payment['status'] == 'unpaid')): ?>
                                            <a href="<?= site_url('participant/payment?pay=' . (isset($payment['id']) ? $payment['id'] : '')); ?>" class="btn btn-success">
                                                <i class="ri-bank-card-line align-bottom me-1"></i> Make Payment
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-primary" onclick="window.print()">
                                            <i class="ri-printer-line align-bottom me-1"></i> Print Details
                                        </button>
                                        <a href="<?= site_url('participant/support/payment/' . (isset($payment['id']) ? $payment['id'] : '')) ?>" class="btn btn-soft-danger">
                                            <i class="ri-question-line align-bottom me-1"></i> Need Help?
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!--end card-->
                        </div>
                        <!--end col-->

                        <div class="col-xl-8">
                            <!-- Payment History Card -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Payment History</h5>
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-danger btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#paymentHistoryCollapse" aria-expanded="true">
                                                <i class="ri-history-line align-middle me-1"></i> View Timeline
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="collapse show" id="paymentHistoryCollapse">
                                    <div class="card-body border-bottom border-bottom-dashed">
                                        <div class="acitivity-timeline py-3">
                                            <?php if(isset($payment_history) && !empty($payment_history)): ?>
                                                <?php foreach($payment_history as $history): ?>
                                                    <div class="acitivity-item d-flex">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-xs acitivity-avatar">
                                                                <?php
                                                                    $iconClass = 'primary';
                                                                    $icon = 'ri-notification-2-line';
                                                                    
                                                                    switch($history['event_type']) {
                                                                        case 'payment_completed':
                                                                            $iconClass = 'success';
                                                                            $icon = 'ri-checkbox-circle-fill';
                                                                            break;
                                                                        case 'payment_initiated':
                                                                            $iconClass = 'info';
                                                                            $icon = 'ri-bank-card-2-line';
                                                                            break;
                                                                        case 'payment_reminder':
                                                                            $iconClass = 'warning';
                                                                            $icon = 'ri-calendar-todo-line';
                                                                            break;
                                                                        case 'payment_created':
                                                                            $iconClass = 'primary';
                                                                            $icon = 'ri-notification-2-line';
                                                                            break;
                                                                        case 'payment_cancelled':
                                                                            $iconClass = 'danger';
                                                                            $icon = 'ri-close-circle-line';
                                                                            break;
                                                                    }
                                                                ?>
                                                                <div class="avatar-title rounded-circle bg-<?= $iconClass ?>-subtle text-<?= $iconClass ?>">
                                                                    <i class="<?= $icon ?>"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-1"><?= $history['title'] ?></h6>
                                                            <p class="text-muted mb-2"><?= $history['description'] ?></p>
                                                            <small class="mb-0 text-muted"><?= date('M d, Y | h:i A', strtotime($history['created_at'])) ?></small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <!-- Default history items when no history data is available -->
                                                <div class="acitivity-item d-flex">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-xs acitivity-avatar">
                                                            <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                <i class="ri-notification-2-line"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-1">Payment Created</h6>
                                                        <p class="text-muted mb-2">Payment requirement was added to your account</p>
                                                        <small class="mb-0 text-muted"><?= date('M d, Y | h:i A', time() - (30 * 24 * 60 * 60)) // 30 days ago ?></small>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Transaction Details -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Transaction Details</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Transaction ID:</th>
                                                    <td class="text-end text-dark">
                                                        <?= isset($payment['transaction_id']) ? $payment['transaction_id'] : 'Transaction pending' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Payment Method:</th>
                                                    <td class="text-end text-dark">
                                                        <?php 
                                                            $payment_method = 'Not specified';
                                                            $payment_details = '';
                                                            
                                                            if(isset($payment['payment_method'])) {
                                                                switch($payment['payment_method']) {
                                                                    case 'credit_card':
                                                                        $payment_method = 'Credit Card';
                                                                        if(isset($payment['card_last4'])) {
                                                                            $payment_details = ' (ending in ' . $payment['card_last4'] . ')';
                                                                        }
                                                                        break;
                                                                    case 'debit_card':
                                                                        $payment_method = 'Debit Card';
                                                                        if(isset($payment['card_last4'])) {
                                                                            $payment_details = ' (ending in ' . $payment['card_last4'] . ')';
                                                                        }
                                                                        break;
                                                                    case 'bank_transfer':
                                                                        $payment_method = 'Bank Transfer';
                                                                        if(isset($payment['transfer_reference'])) {
                                                                            $payment_details = ' (Ref: ' . $payment['transfer_reference'] . ')';
                                                                        }
                                                                        break;
                                                                    case 'paypal':
                                                                        $payment_method = 'PayPal';
                                                                        break;
                                                                    default:
                                                                        $payment_method = ucfirst($payment['payment_method']);
                                                                }
                                                            }
                                                            
                                                            echo $payment_method . $payment_details;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Currency:</th>
                                                    <td class="text-end text-dark">
                                                        <?= isset($payment['currency']) ? strtoupper($payment['currency']) : 'USD' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Date:</th>
                                                    <td class="text-end text-dark">
                                                        <?php if(isset($payment['payment_date'])): ?>
                                                            <?= date('F d, Y \a\t h:i A', strtotime($payment['payment_date'])) ?>
                                                        <?php else: ?>
                                                            Payment not completed
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Subtotal:</th>
                                                    <td class="text-end text-dark">
                                                        $<?= isset($payment['usd_amount']) ? number_format((float)$payment['usd_amount'], 2) : '0.00' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Processing Fee:</th>
                                                    <td class="text-end text-dark">
                                                        $<?= isset($payment['fee_amount']) ? number_format((float)$payment['fee_amount'], 2) : '0.00' ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Total Amount:</th>
                                                    <td class="text-end text-dark fw-semibold">
                                                        $<?php 
                                                            $total = 0;
                                                            if(isset($payment['usd_amount'])) {
                                                                $total += (float)$payment['usd_amount'];
                                                            }
                                                            if(isset($payment['fee_amount'])) {
                                                                $total += (float)$payment['fee_amount'];
                                                            }
                                                            echo number_format($total, 2);
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Notes -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Additional Information</h5>
                                </div>
                                <div class="card-body">
                                    <?php if(isset($payment['description']) && !empty($payment['description'])): ?>
                                        <p class="text-muted mb-2"><?= $payment['description'] ?></p>
                                    <?php else: ?>
                                        <p class="text-muted mb-2">This payment covers the registration fee for the Youth Beyond Boundaries program. The registration fee grants you access to:</p>
                                        <ul class="vstack gap-2 mb-4">
                                            <li class="text-muted"><i class="ri-checkbox-circle-line align-middle text-success me-2"></i>Six months of program activities</li>
                                            <li class="text-muted"><i class="ri-checkbox-circle-line align-middle text-success me-2"></i>Weekly workshops and mentoring sessions</li>
                                            <li class="text-muted"><i class="ri-checkbox-circle-line align-middle text-success me-2"></i>Certificate of completion at the end of the program</li>
                                        </ul>
                                        <p class="text-muted">Please note that additional payments for materials and field trips will be required as the program progresses. You will be notified in advance of these requirements.</p>
                                    <?php endif; ?>
                                    
                                    <?php if(isset($payment['notes']) && !empty($payment['notes'])): ?>
                                        <div class="alert alert-info mt-3 mb-0">
                                            <h6 class="mb-1">Notes:</h6>
                                            <p class="mb-0"><?= $payment['notes'] ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
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
</body>

</html>