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


                            </div> <!-- end .h-100-->

                        </div> <!-- end col -->
                    </div>

                    <!-- Include Payment Modal Widget -->
                    <?php echo view('landing/program-detail/registration-cta', [
                        'program' => $currentProgram ?? null,
                    ]); ?>
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
                greetingElement.textContent = `${greetingText},${fullName}`;
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