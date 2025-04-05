<?= $this->include('partials/main') ?>

<head>

    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', ['meta_title' => "Insights"]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Insights title section -->
        <section class="section position-relative pb-5" id="insights-title" style="background-color: #f8f9fa;">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Program Insights</h1>
                            <p class="text-muted fs-16">Explore key metrics and performance data showcasing the impact and success of our educational initiatives.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end title section -->
        <!-- start Insights section -->
        <section class="section py-5 position-relative" id="insights">
            <div class="container">
                <!-- Key Metrics Row with Counter Cards -->
                <div class="row mb-5">
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-animate border-0 overflow-hidden">
                            <div class="card-body bg-soft-primary">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-15 text-uppercase fw-semibold mb-3">Total Programs</h5>
                                        <h2 class="counter-value mb-0" data-target="<?= $stats['total_programs'] ?? 15 ?>">0</h2>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-3">
                                            <i class="ri-stack-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card card-animate border-0 overflow-hidden">
                            <div class="card-body bg-soft-success">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-15 text-uppercase fw-semibold mb-3">Participants</h5>
                                        <h2 class="counter-value mb-0" data-target="<?= $stats['total_participants'] ?? 1250 ?>">0</h2>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <div class="avatar-title bg-soft-success text-success rounded-circle fs-3">
                                            <i class="ri-team-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card card-animate border-0 overflow-hidden">
                            <div class="card-body bg-soft-info">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-15 text-uppercase fw-semibold mb-3">Countries</h5>
                                        <h2 class="counter-value mb-0" data-target="<?= $stats['total_countries'] ?? 18 ?>">0</h2>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <div class="avatar-title bg-soft-info text-info rounded-circle fs-3">
                                            <i class="ri-global-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card card-animate border-0 overflow-hidden">
                            <div class="card-body bg-soft-danger">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-15 text-uppercase fw-semibold mb-3">Success Rate</h5>
                                        <h2 class="counter-value mb-0" data-target="<?= $stats['success_rate'] ?? 95 ?>">0<span class="counter-value-suffix">%</span></h2>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <div class="avatar-title bg-soft-danger text-danger rounded-circle fs-3">
                                            <i class="ri-award-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program Impact Section -->
                <div class="row align-items-center mb-5">
                    <div class="col-lg-6">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-soft-primary">
                                <h5 class="card-title mb-0">Program Impact</h5>
                            </div>
                            <div class="card-body">
                                <div id="program-impact-chart" class="apex-charts" dir="ltr" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-lg-4">
                            <h2 class="mb-4">Our Program Impact</h2>
                            <p class="text-muted fs-16 mb-4">Our programs have shown significant improvements in various areas for our participants, demonstrating the effectiveness of our approach.</p>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mt-4">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <i class="ri-check-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-16 mb-1">Skill Development</h5>
                                            <p class="text-muted mb-0">85% improvement</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mt-4">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <i class="ri-check-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-16 mb-1">Career Advancement</h5>
                                            <p class="text-muted mb-0">78% success rate</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mt-4">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <i class="ri-check-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-16 mb-1">Knowledge Gain</h5>
                                            <p class="text-muted mb-0">92% improvement</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mt-4">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    <i class="ri-check-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-16 mb-1">Networking</h5>
                                            <p class="text-muted mb-0">65% expansion</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program Feedback Section -->
                <div class="row mb-5">
                    <div class="col-lg-12">
                        <div class="card shadow-lg border-0">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title flex-grow-1 mb-0">Participant Feedback</h5>
                                <div class="flex-shrink-0">
                                    <div class="dropdown">
                                        <a class="btn btn-soft-primary btn-sm" href="javascript:void(0);">
                                            <i class="ri-more-2-fill align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-borderless table-nowrap align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">Program</th>
                                                <th scope="col">Rating</th>
                                                <th scope="col">Participants</th>
                                                <th scope="col">Completion</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Leadership Development</td>
                                                <td>
                                                    <div class="text-warning fs-12">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-half-fill"></i>
                                                    </div>
                                                </td>
                                                <td>120</td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-soft-success">Completed</span></td>
                                            </tr>
                                            <tr>
                                                <td>Digital Marketing</td>
                                                <td>
                                                    <div class="text-warning fs-12">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-line"></i>
                                                    </div>
                                                </td>
                                                <td>85</td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-soft-primary">In Progress</span></td>
                                            </tr>
                                            <tr>
                                                <td>Web Development</td>
                                                <td>
                                                    <div class="text-warning fs-12">
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                        <i class="ri-star-fill"></i>
                                                    </div>
                                                </td>
                                                <td>72</td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td><span class="badge badge-soft-success">Completed</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call-to-Action -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card bg-gradient text-white border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="fw-semibold mb-3">Ready to explore our programs?</h4>
                                        <p class="mb-md-0">Join our programs and take advantage of the opportunities we offer to enhance your skills and knowledge.</p>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="<?= base_url('programs') ?>" class="btn btn-light">Browse Programs <i class="ri-arrow-right-line ms-1 align-bottom"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Insights section -->

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- end layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- apexcharts -->
    <script src="/assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>

    <script>
        // Counter animation
        document.addEventListener("DOMContentLoaded", function() {
            const counterElements = document.querySelectorAll(".counter-value");

            counterElements.forEach(function(element) {
                const target = parseInt(element.getAttribute("data-target"));
                const hasSuffix = element.querySelector(".counter-value-suffix") !== null;
                const duration = 2000; // 2 seconds
                const step = Math.ceil(target / (duration / 16)); // 16ms per frame (approx. 60fps)
                let current = 0;

                const updateCounter = function() {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(interval);
                    }

                    if (hasSuffix) {
                        element.childNodes[0].nodeValue = current;
                    } else {
                        element.textContent = current;
                    }
                };

                const interval = setInterval(updateCounter, 16);
            });

            // Initialize chart
            var options = {
                series: [{
                    name: 'Skill Development',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }, {
                    name: 'Career Growth',
                    data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                yaxis: {
                    title: {
                        text: 'Participant Growth (%)'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + "%"
                        }
                    }
                },
                colors: ['#0ab39c', '#299cdb']
            };

            var chart = new ApexCharts(document.querySelector("#program-impact-chart"), options);
            chart.render();
        });
    </script>