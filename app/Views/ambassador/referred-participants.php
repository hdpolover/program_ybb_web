<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Referred Participants')); ?>

    <?= $this->include('partials/head-css') ?>

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">



</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?= $this->include('partials/ambassador-menu') ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">                <div class="container-fluid"> <?php echo view('partials/page-title', array('pagetitle' => 'Ambassador', 'title' => 'Referred Participants')); ?>

                    <!-- Statistics Cards -->
                    <div class="row">
                        <!-- Total Referrals Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Total Referrals</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-group-line text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                <?= !empty($referredParticipants) ? count($referredParticipants) : '0' ?>
                                            </h4>
                                            <span class="badge bg-info-subtle text-info"><i class="ri-user-add-line align-bottom me-1"></i> Total participants</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Countries Represented Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Countries</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-earth-line text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <?php
                                            $countries = [];
                                            if (!empty($referredParticipants)) {
                                                foreach ($referredParticipants as $participant) {
                                                    if (isset($participant['nationality']) && !empty($participant['nationality'])) {
                                                        $countries[$participant['nationality']] = 1;
                                                    }
                                                }
                                            }
                                            ?>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><?= count($countries) ?></h4>
                                            <span class="badge bg-success-subtle text-success"><i class="ri-flag-line align-bottom me-1"></i> Nationalities</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- This Month Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">This Month</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-calendar-check-line text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <?php
                                            $thisMonth = 0;
                                            $currentMonth = date('m');
                                            $currentYear = date('Y');
                                            
                                            if (!empty($referredParticipants)) {
                                                foreach ($referredParticipants as $participant) {
                                                    if (isset($participant['created_at'])) {
                                                        $regMonth = date('m', strtotime($participant['created_at']));
                                                        $regYear = date('Y', strtotime($participant['created_at']));
                                                        
                                                        if ($regMonth == $currentMonth && $regYear == $currentYear) {
                                                            $thisMonth++;
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><?= $thisMonth ?></h4>
                                            <span class="badge bg-warning-subtle text-warning"><i class="ri-time-line align-bottom me-1"></i> This month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Program Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <p class="text-uppercase fw-medium text-muted mb-0">Program</p>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-light rounded fs-3">
                                                <i class="ri-award-line text-danger"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-16 fw-semibold ff-secondary mb-4 text-truncate">
                                                <?= isset($currentProgram['name']) ? $currentProgram['name'] : 'N/A' ?>
                                            </h4>
                                            <span class="badge bg-danger-subtle text-danger"><i class="ri-medal-line align-bottom me-1"></i> Current program</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Statistics Cards -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Your Referred Participants</h5>
                                </div>
                                <div class="card-body">
                                    <table id="referred-participants-table" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Name</th>
                                                <th>Nationality</th>
                                                <th>Registration Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($referredParticipants)): ?>
                                                <?php $i = 1;
                                                foreach ($referredParticipants as $participant): ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td><?= $participant['full_name'] ?? 'N/A' ?></td>
                                                        <td><?= $participant['nationality'] ?? 'N/A' ?></td>
                                                        <td><?= isset($participant['created_at']) ? date('d M, Y', strtotime($participant['created_at'])) : 'N/A' ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No referred participants found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->

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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            $('#referred-participants-table').DataTable({
                responsive: true,
                lengthChange: true,
                autoWidth: false,
                language: {
                    search: "<i class='ri-search-line'></i>",
                    searchPlaceholder: "Search participants...",
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                }
            });
        });
    </script>
</body>

</html>