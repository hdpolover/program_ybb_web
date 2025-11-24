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
<script>
    // Show available programs dialog on every sign in
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($availablePrograms) && is_array($availablePrograms) && count($availablePrograms) > 0): ?>
                
                // Build programs list HTML
                let programsHtml = '<div class="available-programs-list" style="max-height: 400px; overflow-y: auto;">';
                
                <?php foreach ($availablePrograms as $program): ?>
                    programsHtml += `
                        <div class="program-card border rounded mb-3 overflow-hidden" style="background: #ffffff;">
                            <?php if (isset($program['banner_url']) && !empty($program['banner_url'])): ?>
                                <div class="program-banner" style="height: 150px; overflow: hidden; position: relative;">
                                    <img src="<?= esc($program['banner_url']) ?>" 
                                         alt="<?= esc($program['name'] ?? 'Program') ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                    <div style="position: absolute; top: 10px; right: 10px;">
                                        <span class="badge bg-success shadow-sm">Active</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0 fw-bold"><?= esc($program['name'] ?? 'Program') ?></h5>
                                    <?php if (!isset($program['banner_url']) || empty($program['banner_url'])): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (isset($program['tagline']) && !empty($program['tagline'])): ?>
                                    <p class="text-primary mb-2 small fw-semibold">
                                        <i class="ri-quote-text me-1"></i><?= esc($program['tagline']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <?php if (isset($program['start_date']) && !empty($program['start_date'])): ?>
                                        <div class="badge bg-light text-dark border">
                                            <i class="ri-calendar-event-line me-1"></i>
                                            <?= date('M d, Y', strtotime($program['start_date'])) ?>
                                            <?php if (isset($program['end_date']) && !empty($program['end_date'])): ?>
                                                - <?= date('M d', strtotime($program['end_date'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($program['location']) && !empty($program['location'])): ?>
                                        <div class="badge bg-light text-dark border">
                                            <i class="ri-map-pin-line me-1"></i><?= esc($program['location']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (isset($program['description']) && !empty($program['description'])): ?>
                                    <?php 
                                        // Strip HTML tags and get plain text description
                                        $plainDescription = strip_tags($program['description']);
                                        $truncatedDesc = substr($plainDescription, 0, 120);
                                        $truncatedDesc = htmlspecialchars($truncatedDesc, ENT_QUOTES, 'UTF-8');
                                    ?>
                                    <p class="text-muted mb-3 small" style="line-height: 1.5;">
                                        <?= $truncatedDesc ?><?= strlen($plainDescription) > 120 ? '...' : '' ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <a href="<?= base_url('topbar/' . ($program['id'] ?? '') . '/create') ?>" 
                                           class="btn btn-primary w-100 btn-sm">
                                            <i class="ri-user-add-line me-1"></i> Register for this Program
                                        </a>
                                    </div>
                                    <?php if (isset($program['slug']) && !empty($program['slug'])): ?>
                                        <div class="col-12">
                                            <a href="<?= base_url('programs/' . $program['slug']) ?>" 
                                               class="btn btn-outline-secondary w-100 btn-sm" 
                                               target="_blank">
                                                <i class="ri-information-line me-1"></i> View Details
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    `;
                <?php endforeach; ?>
                
                programsHtml += '</div>';
                
                // Show SweetAlert dialog
                Swal.fire({
                    title: '<i class="ri-information-line text-primary"></i> New Programs Available',
                    html: '<div class="text-start">' +
                          '<p class="mb-3 text-muted">We have ' + <?= count($availablePrograms) ?> + ' active program(s) that you haven\'t joined yet. Would you like to participate?</p>' +
                          programsHtml +
                          '</div>',
                    icon: null,
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: '<i class="ri-close-line me-1"></i> Maybe Later',
                    width: '700px',
                    customClass: {
                        container: 'available-programs-modal',
                        cancelButton: 'btn btn-secondary',
                        htmlContainer: 'text-start'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    didOpen: () => {
                        // Ensure HTML is properly rendered
                        const container = Swal.getHtmlContainer();
                        if (container) {
                            container.style.textAlign = 'left';
                        }
                    }
                });
        <?php endif; ?>
    });
</script>

<style>
    .available-programs-modal .program-card {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .available-programs-modal .program-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        transform: translateY(-2px);
    }
    
    .available-programs-modal .swal2-html-container {
        overflow: visible !important;
    }
    
    .available-programs-modal .program-banner img {
        transition: transform 0.3s ease;
    }
    
    .available-programs-modal .program-card:hover .program-banner img {
        transform: scale(1.05);
    }
</style>
</html>