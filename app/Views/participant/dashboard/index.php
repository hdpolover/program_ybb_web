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


                            </div> <!-- end .h-100-->

                        </div> <!-- end col -->

                    </div>

                    <?= $this->include('landing/program-detail/registration-cta') ?>

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