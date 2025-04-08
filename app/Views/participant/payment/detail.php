<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/title-meta', array('title' => 'Payment Details')); ?>
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
                    <?php echo view('partials/page-title', array('pagetitle' => 'Payments', 'title' => 'Payment Details')); ?>

                    <!-- Payment Details Card -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <!-- Card Header with Back Button -->
                                <div class="card-header d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="card-title mb-0">
                                            <?= isset($programPayment['name']) ? $programPayment['name'] : 'Payment Details' ?>
                                            <?php if (isset($programPayment['status'])): ?>
                                                <?php
                                                // Include payment helpers
                                                require_once(__DIR__ . '/helpers/payment_helpers.php');
                                                $statusInfo = getPaymentStatusInfo($programPayment['status']);
                                                ?>
                                                <span class="badge bg-<?= $statusInfo['statusBadge'] ?> ms-2"><?= $statusInfo['statusText'] ?></span>
                                            <?php endif; ?>
                                        </h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="<?= site_url('payments') ?>" class="btn btn-primary btn-sm">
                                            <i class="ri-arrow-left-line align-middle me-1"></i> Back to Payments
                                        </a>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Payment Information -->
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <!-- Payment Details Section -->
                                            <div class="mb-4">
                                                <?php include_once(__DIR__ . '/widgets/payment_information.php'); ?>
                                            </div>

                                            <!-- Payment History Timeline -->
                                            <?php include_once(__DIR__ . '/widgets/payment_history.php'); ?>
                                        </div>

                                        <!-- Sidebar/Action Card -->
                                        <div class="col-lg-4">
                                            <!-- Payment Actions Widget -->
                                            <?php include_once(__DIR__ . '/widgets/payment_actions.php'); ?>

                                            <!-- Related Payments Widget -->
                                            <?php include_once(__DIR__ . '/widgets/related_payments.php'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <!-- Include Payment Modal Widget -->
            <?php echo view('participant/payment/widgets/payment_modal', [
                'paymentMethods' => $paymentMethods ?? null
            ]); ?>

            <?= $this->include('partials/footer') ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <!-- Payment Details Page js -->
    <script src="/assets/js/pages/payment-details.js"></script>
</body>

</html>