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

$full_name = $ambassador['name'];
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

                                <!-- Information Card -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header border-bottom-dashed">
                                                <h4 class="card-title mb-0">Development Notice</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="ri-information-line text-primary fs-2"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="mb-2">Features Still in Development</h5>
                                                        <p class="text-muted mb-3">
                                                            Thank you for your participation in our ambassador program. 
                                                            Some features are still under development, but you can currently view your referred participants and basic details on this ambassador dashboard.
                                                            Please wait as we continue to enhance your experience with additional features.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Information Card -->

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
</body>

</html>