<?= $this->include('partials/main') ?>

<head>

     <?php echo view('partials/title-meta', array('title' => 'Abstract and Paper')); ?>

    <!-- Sweet Alert css-->
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- swiper css -->
    <link rel="stylesheet" href="/assets/libs/swiper/swiper-bundle.min.css">

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

                    <?php echo view('partials/page-title', array('pagetitle'=>'Participant', 'title'=>'Abstract & Paper')); ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                
                                    <?php if (!isset($hasAbstract) || $hasAbstract === false): ?>
                                        <?= $this->include('participant/abstract-paper/components/empty-state') ?>
                                    <?php else: ?>
                                        <?= $this->include('participant/abstract-paper/components/abstract-view') ?>
                                    <?php endif; ?>
                                    
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

    <!-- Sweet Alert js-->
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Display sweet alert for success messages
            <?php if (session()->has('success')): ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= session('success') ?>',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?>

            // Display sweet alert for error messages
            <?php if (session()->has('error')): ?>
                Swal.fire({
                    title: 'Error!',
                    text: '<?= session('error') ?>',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            <?php endif; ?>
        });
    </script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
</body>

</html>