<?php
// Set a default greeting that will be replaced by JavaScript
$greetingText = "Hello";
$full_name = isset($currentParticipant['full_name']) ? $currentParticipant['full_name'] : 'Participant';
?>

<?= $this->include('partials/main') ?>

<head>
    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', ['meta_title' => "Dashboard"]) ?>

    <!-- jsvectormap css -->
    <link href="/assets/libs/jsvectormap/jsvectormap.min.css" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

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

                    <div class="row">
                        <div class="col">

                            <div class="h-100">
                                <div class="row mb-3 pb-1">
                                    <div class="col-12">
                                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                            <div class="flex-grow-1">
                                                <h3><?= $greetingText ?>, <?= $full_name ?>!</h3>

                                            </div>
                                        </div><!-- end card header -->
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->

                                <!-- Include Guideline Widget -->
                                <?php echo view('participant/dashboard/guideline-small-card', [
                                    'program' => $currentProgram ?? null,
                                ]); ?>

                                <!-- Include Participant Category Card -->
                                <?php if (isset($detailedParticipant)): ?>
                                    <?php echo view('participant/dashboard/participant_category_card', [
                                        'currentParticipant' => $currentParticipant ?? null,
                                        'detailedParticipant' => $detailedParticipant ?? null,
                                        'switchEligibility' => $switchEligibility ?? null,
                                    ]); ?>
                                <?php endif; ?>

                                <!-- Notification Center -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Notifications & Alerts</h4>
                                                <div class="flex-shrink-0">
                                                    <button type="button" class="btn btn-sm btn-ghost-primary" id="markAllRead">
                                                        <i class="ri-check-double-line align-bottom"></i> Mark All as Read
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div id="notificationList" class="notification-list">
                                                    <?php
                                                    // Add dynamic notifications based on participant status
                                                    $hasNotifications = false;
                                                    ?>

                                                    <!-- Payment notification -->
                                                    <?php if (isset($paymentStatus) && $paymentStatus === 'pending'): ?>
                                                        <?php $hasNotifications = true; ?>
                                                        <div class="notification-item d-flex p-3 border-bottom">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar-sm bg-danger-subtle rounded-circle text-center">
                                                                    <i class="ri-money-dollar-circle-fill text-danger fs-4 mt-2"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1 lh-base">Payment Required</h6>
                                                                <p class="text-muted mb-1">Please complete your payment to continue with the program.</p>
                                                                <small class="mb-0 text-muted">Due date: <?= isset($paymentDueDate) ? date('d M Y', strtotime($paymentDueDate)) : 'As soon as possible' ?></small>
                                                            </div>
                                                            <div class="flex-shrink-0 align-self-center">
                                                                <a href="<?= base_url('payments') ?>" class="btn btn-sm btn-primary">Pay Now</a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Incomplete form submission notification -->
                                                    <?php if (isset($hasSubmittedForm) && $hasSubmittedForm === false): ?>
                                                        <?php $hasNotifications = true; ?>
                                                        <div class="notification-item d-flex p-3 border-bottom">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar-sm bg-warning-subtle rounded-circle text-center">
                                                                    <i class="ri-file-text-line text-warning fs-4 mt-2"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1 lh-base">Incomplete Registration</h6>
                                                                <p class="text-muted mb-1">You have not completed your registration form yet.</p>
                                                                <small class="mb-0 text-muted">Please complete your submission to be eligible for the program.</small>
                                                            </div>
                                                            <div class="flex-shrink-0 align-self-center">
                                                                <a href="<?= base_url('submission/edit') ?>" class="btn btn-sm btn-warning">Complete Form</a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Program deadline notification -->
                                                    <?php if (isset($currentProgram) && isset($currentProgram['deadline'])): ?>
                                                        <?php $hasNotifications = true; ?>
                                                        <div class="notification-item d-flex p-3 border-bottom">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar-sm bg-info-subtle rounded-circle text-center">
                                                                    <i class="ri-calendar-event-line text-info fs-4 mt-2"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1 lh-base">Upcoming Deadline</h6>
                                                                <p class="text-muted mb-1">Program registration closes soon.</p>
                                                                <small class="mb-0 text-muted">Deadline: <?= date('d M Y', strtotime($currentProgram['deadline'])) ?></small>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Empty state -->
                                                    <?php if (!$hasNotifications): ?>
                                                        <div class="text-center p-4">
                                                            <div class="avatar-md mx-auto mb-4">
                                                                <div class="avatar-title bg-light rounded-circle text-primary">
                                                                    <i class="ri-checkbox-circle-line fs-1"></i>
                                                                </div>
                                                            </div>
                                                            <h5 class="mb-1">All Caught Up!</h5>
                                                            <p class="text-muted">You have no pending notifications at this time.</p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Notification Center -->

                            </div> <!-- end .h-100-->

                        </div> <!-- end col -->
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

    <!-- apexcharts -->
    <script src="/assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Vector map-->
    <script src="/assets/libs/jsvectormap/jsvectormap.min.js"></script>
    <script src="/assets/libs/jsvectormap/maps/world-merc.js"></script>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Dashboard init -->
    <script src="/assets/js/pages/dashboard-ecommerce.init.js"></script> <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <!-- Loading Manager js (handles loading overlays) -->
    <script src="/assets/js/loading-manager.js"></script>

    <script>
        // Set greeting based on user's local time
        document.addEventListener('DOMContentLoaded', function() {
            const hour = new Date().getHours();
            let greetingText = "";

            if (hour >= 0 && hour < 12) {
                greetingText = "Good Morning";
            } else if (hour >= 12 && hour < 17) {
                greetingText = "Good Afternoon";
            } else {
                greetingText = "Good Evening";
            }

            // Find and update the greeting element
            const greetingElement = document.querySelector('.col-12 h3');
            if (greetingElement) {
                const fullName = greetingElement.textContent.split(',')[1] || '';
                greetingElement.textContent = `${greetingText},${fullName.toUpperCase()}`; // Corrected to use toUpperCase()
            }
        });
    </script>

    <?php if (isset($footer_scripts)): ?>
        <?= $footer_scripts ?>
    <?php endif; ?>
</body>

<!-- Add SweetAlert2 library for better user notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>