<?php

$greetingText = "";

$hour = date('H'); // Get the current hour in 24-hour format

if (($hour >= 0 && $hour < 5) || ($hour >= 5 && $hour < 12)) {
    $greetingText = "Good Morning";
} elseif ($hour >= 12 && $hour < 17) {
    $greetingText = "Good Afternoon";
} else {
    $greetingText = "Good Evening";
}

$full_name = $ambassador['full_name'] ?? $ambassador['name'] ?? 'Ambassador';
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

        <?= $this->include('partials/ambassador-menu') ?>

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

                                <!-- Dashboard Statistics Cards -->
                                <div class="row">
                                    <!-- Total Referrals Card -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted mb-0">Total Referrals</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <h5 class="text-success fs-14 mb-0">
                                                            <i class="ri-group-line align-middle"></i> 
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="total-referrals">
                                                            <span class="counter-value" data-target="<?= $overviewData['metrics']['total_referrals'] ?? 0 ?>"><?= $overviewData['metrics']['total_referrals'] ?? 0 ?></span>
                                                        </h4>
                                                        <a href="/ambassadors/referred-participants" class="text-decoration-underline">View all participants</a>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                                            <i class="ri-group-line text-success"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div>
                                    </div><!-- end col -->

                                    <!-- Completed Registrations Card -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted mb-0">Completed Registrations</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <h5 class="text-primary fs-14 mb-0">
                                                            <i class="ri-user-star-line align-middle"></i> 
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="completed-registrations">
                                                            <span class="counter-value" data-target="<?= $overviewData['metrics']['completed_registrations'] ?? 0 ?>"><?= $overviewData['metrics']['completed_registrations'] ?? 0 ?></span>
                                                        </h4>
                                                        <span class="badge bg-success-subtle text-success mb-0" id="completion-rate"><?= number_format(($overviewData['metrics']['conversion_rate'] ?? 0), 1) ?>% completion</span>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                                            <i class="ri-user-star-line text-primary"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div>
                                    </div><!-- end col -->

                                    <!-- This Month Referrals Card -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted mb-0">This Month</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <h5 class="text-warning fs-14 mb-0">
                                                            <i class="ri-calendar-check-line align-middle"></i> 
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="this-month-referrals">
                                                            <span class="counter-value" data-target="<?= $overviewData['metrics']['this_month_referrals'] ?? 0 ?>"><?= $overviewData['metrics']['this_month_referrals'] ?? 0 ?></span>
                                                        </h4>
                                                        <span class="badge bg-warning-subtle text-warning mb-0">New referrals</span>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                            <i class="ri-calendar-check-line text-warning"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div>
                                    </div><!-- end col -->

                                    <!-- Ambassador Ranking Card -->
                                    <div class="col-xl-3 col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <p class="text-uppercase fw-medium text-muted mb-0">Ranking</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <h5 class="text-info fs-14 mb-0">
                                                            <i class="ri-trophy-line align-middle"></i> 
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end justify-content-between mt-4">
                                                    <div>
                                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4" id="ambassador-ranking">
                                                            #<span class="counter-value" data-target="<?= $overviewData['quick_stats']['ranking'] ?? 0 ?>"><?= $overviewData['quick_stats']['ranking'] ?? 0 ?></span>
                                                        </h4>
                                                        <span class="badge bg-info-subtle text-info mb-0">Ambassador</span>
                                                    </div>
                                                    <div class="avatar-sm flex-shrink-0">
                                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                                            <i class="ri-trophy-line text-info"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div>
                                    </div><!-- end col -->
                                </div>
                                <!-- End Dashboard Statistics Cards -->

                                <!-- Overview Widgets -->
                                <div class="row">
                                    <!-- Participant Breakdown Overview -->
                                    <div class="col-xl-8">
                                        <div class="card">
                                            <div class="card-header border-0 align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Participant Overview</h4>
                                                <div class="flex-shrink-0">
                                                    <a href="/ambassadors/referred-participants" class="text-reset">
                                                        <span class="text-muted">View Details <i class="mdi mdi-chevron-right align-middle"></i></span>
                                                    </a>
                                                </div>
                                            </div><!-- end card header -->

                                            <div class="card-body">
                                                <!-- Participant Categories -->
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar-sm">
                                                                    <div class="avatar-title bg-success-subtle text-success rounded fs-16">
                                                                        <i class="ri-funds-line"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1"><?= $overviewData['participant_breakdown']['by_category']['fully_funded'] ?? 0 ?></h5>
                                                                <p class="text-muted mb-0">Fully Funded</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="flex-shrink-0 me-3">
                                                                <div class="avatar-sm">
                                                                    <div class="avatar-title bg-warning-subtle text-warning rounded fs-16">
                                                                        <i class="ri-wallet-line"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h5 class="mb-1"><?= $overviewData['participant_breakdown']['by_category']['self_funded'] ?? 0 ?></h5>
                                                                <p class="text-muted mb-0">Self Funded</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Top Nationalities -->
                                                <div class="mb-4">
                                                    <h6 class="mb-3">Top Nationalities</h6>
                                                    <?php if (!empty($overviewData['participant_breakdown']['by_nationality'])): ?>
                                                        <?php foreach (array_slice($overviewData['participant_breakdown']['by_nationality'], 0, 5, true) as $country => $count): ?>
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <span class="text-muted"><?= esc($country) ?></span>
                                                                <span class="badge bg-light text-dark"><?= $count ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted">No nationality data available</p>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Institution Types -->
                                                <div>
                                                    <h6 class="mb-3">Institution Types</h6>
                                                    <?php if (!empty($overviewData['participant_breakdown']['by_institution_type'])): ?>
                                                        <?php foreach (array_slice($overviewData['participant_breakdown']['by_institution_type'], 0, 4, true) as $type => $count): ?>
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <span class="text-muted"><?= esc($type) ?></span>
                                                                <span class="badge bg-primary-subtle text-primary"><?= $count ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted">No institution data available</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div> <!-- .card-->
                                    </div> <!-- .col-->

                                    <!-- Performance Chart -->
                                    <div class="col-xl-4">
                                        <div class="card card-height-100">
                                            <div class="card-header align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Performance Overview</h4>
                                            </div><!-- end card header -->

                                            <div class="card-body">
                                                <!-- Registration Rate Progress -->
                                                <div class="mb-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-0">Registration Rate</p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <p class="text-success fs-12 mb-0" id="registration-rate"><?= number_format($overviewData['metrics']['conversion_rate'] ?? 0, 1) ?>%</p>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, $overviewData['metrics']['conversion_rate'] ?? 0) ?>%" id="registration-progress"></div>
                                                    </div>
                                                </div>

                                                <!-- Payment Rate Progress -->
                                                <div class="mb-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-0">Payment Rate</p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <p class="text-primary fs-12 mb-0" id="payment-rate"><?= number_format($overviewData['payment_summary']['payment_completion_rate'] ?? 0, 1) ?>%</p>
                                                        </div>
                                                    </div>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min(100, $overviewData['payment_summary']['payment_completion_rate'] ?? 0) ?>%" id="payment-progress"></div>
                                                    </div>
                                                </div>

                                                <!-- Performance Insights -->
                                                <div class="mb-4">
                                                    <h6 class="mb-3">Performance Insights</h6>
                                                    
                                                    <!-- Quality Score -->
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="text-muted">Quality Score</span>
                                                        <span class="badge bg-info-subtle text-info"><?= $overviewData['performance_insights']['quality_score'] ?? 0 ?>/100</span>
                                                    </div>
                                                    
                                                    <!-- Best Month -->
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="text-muted">Best Month</span>
                                                        <span class="badge bg-success-subtle text-success"><?= esc($overviewData['performance_insights']['best_performing_month'] ?? 'N/A') ?></span>
                                                    </div>
                                                    
                                                    <!-- Registration Trend -->
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <span class="text-muted">Trend</span>
                                                        <?php 
                                                        $trend = $overviewData['performance_insights']['registration_trend'] ?? 'stable';
                                                        $trendClass = $trend === 'increasing' ? 'success' : ($trend === 'decreasing' ? 'warning' : 'secondary');
                                                        $trendIcon = $trend === 'increasing' ? 'ri-arrow-up-line' : ($trend === 'decreasing' ? 'ri-arrow-down-line' : 'ri-subtract-line');
                                                        ?>
                                                        <span class="badge bg-<?= $trendClass ?>-subtle text-<?= $trendClass ?>">
                                                            <i class="<?= $trendIcon ?> align-middle"></i> <?= ucfirst($trend) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div>
                                <!-- End Recent Participants & Performance -->

                                <!-- Achievement Badges Section -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header border-0 align-items-center d-flex">
                                                <h4 class="card-title mb-0 flex-grow-1">Achievement Badges</h4>
                                                <div class="flex-shrink-0">
                                                    <span class="text-muted fs-13">
                                                        <i class="ri-trophy-line me-1"></i>
                                                        <?php 
                                                        $totalAchievements = is_array($overviewData['achievements'] ?? []) ? count($overviewData['achievements']) : 0;
                                                        $earnedCount = 0;
                                                        if (!empty($overviewData['achievements']) && is_array($overviewData['achievements'])) {
                                                            $earnedCount = count(array_filter($overviewData['achievements'], fn($a) => $a['achieved'] ?? false));
                                                        }
                                                        echo "$earnedCount of $totalAchievements earned";
                                                        ?>
                                                    </span>
                                                </div>
                                            </div><!-- end card header -->

                                            <div class="card-body">
                                                <div class="row">
                                                    <?php if (!empty($overviewData['achievements']) && is_array($overviewData['achievements'])): ?>
                                                        <?php foreach ($overviewData['achievements'] as $achievement): ?>
                                                            <?php $isEarned = $achievement['achieved'] ?? false; ?>
                                                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                                                <div class="card border <?= $isEarned ? 'border-success bg-success-subtle' : 'border-light' ?> h-100">
                                                                    <div class="card-body text-center p-4">
                                                                        <div class="avatar-lg mx-auto mb-3">
                                                                            <div class="avatar-title <?= $isEarned ? 'bg-success text-white' : 'bg-light text-muted' ?> rounded-circle fs-1">
                                                                                <?= $achievement['icon'] ?? '🏆' ?>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <h5 class="fs-16 mb-2 <?= $isEarned ? 'text-success' : 'text-muted' ?>">
                                                                            <?= esc($achievement['title'] ?? 'Achievement') ?>
                                                                        </h5>
                                                                        
                                                                        <p class="text-muted mb-3 fs-14">
                                                                            <?= esc($achievement['description'] ?? 'Achievement description') ?>
                                                                        </p>
                                                                        
                                                                        <?php if ($isEarned): ?>
                                                                            <div class="d-flex align-items-center justify-content-center">
                                                                                <span class="badge bg-success fs-12">
                                                                                    <i class="ri-check-line me-1"></i>Earned
                                                                                </span>
                                                                                <?php if (!empty($achievement['date_achieved'])): ?>
                                                                                    <small class="text-muted ms-2"><?= esc($achievement['date_achieved']) ?></small>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <?php if (!empty($achievement['progress'])): ?>
                                                                                <div class="mb-2">
                                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                        <small class="text-muted">Progress</small>
                                                                                        <small class="text-muted"><?= $achievement['progress']['current'] ?? 0 ?>/<?= $achievement['progress']['target'] ?? 1 ?></small>
                                                                                    </div>
                                                                                    <div class="progress progress-sm">
                                                                                        <?php 
                                                                                        $progressPercent = 0;
                                                                                        if (!empty($achievement['progress']['target'])) {
                                                                                            $progressPercent = min(100, (($achievement['progress']['current'] ?? 0) / $achievement['progress']['target']) * 100);
                                                                                        }
                                                                                        ?>
                                                                                        <div class="progress-bar bg-warning" style="width: <?= $progressPercent ?>%"></div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <span class="badge bg-light text-muted fs-12">
                                                                                <i class="ri-lock-line me-1"></i>Not Earned
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="col-12">
                                                            <div class="text-center py-5">
                                                                <div class="avatar-xl mx-auto mb-4">
                                                                    <div class="avatar-title bg-light text-muted rounded-circle fs-2">
                                                                        <i class="ri-award-line"></i>
                                                                    </div>
                                                                </div>
                                                                <h5 class="text-muted mb-2">No Achievement System Available</h5>
                                                                <p class="text-muted mb-4">Achievement badges will appear here once the system is set up.</p>
                                                                <a href="#" class="btn btn-outline-primary">
                                                                    <i class="ri-information-line me-1"></i>Learn More
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div><!-- end card body -->
                                        </div><!-- end card -->
                                    </div><!-- end col -->
                                </div>
                                <!-- End Achievement Badges Section -->

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
    <script src="/assets/js/pages/dashboard-ecommerce.init.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <!-- Ambassador Dashboard Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize counter animations for the numbers that were loaded from server
            initializeCounters();
        });

        // Initialize counter animations
        function initializeCounters() {
            const counterElements = document.querySelectorAll('.counter-value');
            
            counterElements.forEach(element => {
                const target = parseInt(element.getAttribute('data-target')) || 0;
                const duration = 1000; // 1 second animation
                const steps = 60; // 60 frames
                const increment = target / steps;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    // Format the number based on element content
                    if (element.textContent.includes('.')) {
                        element.textContent = current.toFixed(2);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                }, duration / steps);
            });
        }
        
        // Optional: Add refresh functionality
        function refreshDashboard() {
            window.location.reload();
        }
        
        // Show toast notification if needed (for error handling)
        function showToast(title, message, type = 'info') {
            // Simple console log for now - can be enhanced with actual toast library
            console.log(`${type.toUpperCase()}: ${title} - ${message}`);
        }
    </script>
</body>

</html>