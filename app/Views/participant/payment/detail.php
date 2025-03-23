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
                                                    <h5 class="fs-14">#YBB-REG-001</h5>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Status</div>
                                                    <span class="badge badge-soft-success fs-11">Paid</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="pt-2">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Payment Type</div>
                                                    <h5 class="fs-14">Program Registration Fee</h5>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Amount</div>
                                                    <h5 class="fs-14">$200.00</h5>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-4">
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Due Date</div>
                                                    <h5 class="fs-14">March 01, 2025</h5>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="text-muted mb-2">Payment Date</div>
                                                    <h5 class="fs-14">February 25, 2025</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end card-->
                            
                            <!-- Program Details -->
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Program Details</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <div class="d-flex mb-3">
                                            <div class="flex-shrink-0">
                                                <i class="ri-calendar-event-line text-primary fs-24 align-middle"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">Program Duration</h5>
                                                <p class="text-muted mb-0">March 15, 2025 - September 15, 2025</p>
                                            </div>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <div class="flex-shrink-0">
                                                <i class="ri-map-pin-line text-primary fs-24 align-middle"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">Location</h5>
                                                <p class="text-muted mb-0">Youth Center, 123 Main St</p>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="ri-user-3-line text-primary fs-24 align-middle"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">Program Coordinator</h5>
                                                <p class="text-muted mb-0">Sarah Johnson</p>
                                                <a href="mailto:sarah@example.com" class="btn btn-link p-0">sarah@example.com</a>
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
                                        <a href="<?= site_url('participant/payment/receipt/1'); ?>" class="btn btn-info">
                                            <i class="ri-download-cloud-line align-bottom me-1"></i> Download Receipt
                                        </a>
                                        <button type="button" class="btn btn-primary">
                                            <i class="ri-printer-line align-bottom me-1"></i> Print Details
                                        </button>
                                        <button type="button" class="btn btn-soft-danger">
                                            <i class="ri-question-line align-bottom me-1"></i> Need Help?
                                        </button>
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
                                            <div class="acitivity-item d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs acitivity-avatar">
                                                        <div class="avatar-title rounded-circle bg-success-subtle text-success">
                                                            <i class="ri-checkbox-circle-fill"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">Payment Completed</h6>
                                                    <p class="text-muted mb-2">Payment of $200.00 was successfully processed via Credit Card</p>
                                                    <small class="mb-0 text-muted">Feb 25, 2025 | 10:30 AM</small>
                                                </div>
                                            </div>
                                            <div class="acitivity-item d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs acitivity-avatar">
                                                        <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                                            <i class="ri-bank-card-2-line"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">Payment Initiated</h6>
                                                    <p class="text-muted mb-2">Payment of $200.00 was initiated via Credit Card</p>
                                                    <small class="mb-0 text-muted">Feb 25, 2025 | 10:28 AM</small>
                                                </div>
                                            </div>
                                            <div class="acitivity-item d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs acitivity-avatar">
                                                        <div class="avatar-title rounded-circle bg-warning-subtle text-warning">
                                                            <i class="ri-calendar-todo-line"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">Payment Due Reminder</h6>
                                                    <p class="text-muted mb-2">Reminder email sent for registration payment</p>
                                                    <small class="mb-0 text-muted">Feb 20, 2025 | 09:00 AM</small>
                                                </div>
                                            </div>
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
                                                    <p class="text-muted mb-2">Registration payment requirement created</p>
                                                    <small class="mb-0 text-muted">Jan 15, 2025 | 03:45 PM</small>
                                                </div>
                                            </div>
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
                                                    <td class="text-end text-dark">TXN-25022025-0001</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Payment Method:</th>
                                                    <td class="text-end text-dark">Credit Card - VISA (ending in 4157)</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Currency:</th>
                                                    <td class="text-end text-dark">USD</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Date:</th>
                                                    <td class="text-end text-dark">February 25, 2025 at 10:30 AM</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Subtotal:</th>
                                                    <td class="text-end text-dark">$200.00</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Processing Fee:</th>
                                                    <td class="text-end text-dark">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0 text-muted" scope="row">Total Amount:</th>
                                                    <td class="text-end text-dark fw-semibold">$200.00</td>
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
                                    <p class="text-muted mb-2">This payment covers the registration fee for the Youth Beyond Boundaries program. The registration fee grants you access to:</p>
                                    <ul class="vstack gap-2 mb-4">
                                        <li class="text-muted"><i class="ri-checkbox-circle-line align-middle text-success me-2"></i>Six months of program activities</li>
                                        <li class="text-muted"><i class="ri-checkbox-circle-line align-middle text-success me-2"></i>Weekly workshops and mentoring sessions</li>
                                        <li class="text-muted"><i class="ri-checkbox-circle-line align-middle text-success me-2"></i>Certificate of completion at the end of the program</li>
                                    </ul>
                                    <p class="text-muted">Please note that additional payments for materials and field trips will be required as the program progresses. You will be notified in advance of these requirements.</p>
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

    <?= $this->include('partials/customizer') ?>
    <?= $this->include('partials/vendor-scripts') ?>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>