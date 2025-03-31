<?= $this->include('partials/main') ?>

<head>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Programs title section -->
        <section class="section position-relative pb-5" id="programs-title" style="background-color: #f8f9fa;">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Our Programs</h1>
                            <p class="text-muted fs-16">Discover our diverse range of educational programs designed to help you achieve your goals.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Programs title section -->

        <!-- start Programs content section -->
        <section class="section py-5 position-relative" id="programs">
            <div class="container">
                <!-- Program Filter Buttons -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mb-4">
                            <div class="filter-buttons">
                                <button class="btn btn-primary me-2 mb-2 filter-btn active" data-filter="all">All Programs</button>
                                <?php
                                $categories = [];
                                if (isset($programs) && !empty($programs)) {
                                    foreach ($programs as $program) {
                                        if (isset($program['category']) && !in_array($program['category'], $categories)) {
                                            $categories[] = $program['category'];
                                        }
                                    }
                                } else {
                                    $categories = ['Technical', 'Leadership', 'Business', 'Creative'];
                                }

                                foreach ($categories as $category) :
                                ?>
                                    <button class="btn btn-outline-primary me-2 mb-2 filter-btn" data-filter="<?= strtolower(str_replace(' ', '-', $category)) ?>"><?= $category ?></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Program Cards -->
                <div class="row program-grid">
                    <?php
                    if (isset($programs) && !empty($programs)) {
                        foreach ($programs as $program) {
                            $category = isset($program['category']) ? strtolower(str_replace(' ', '-', $program['category'])) : 'general';
                            $duration = isset($program['duration']) ? $program['duration'] : '8 weeks';
                            $level = isset($program['level']) ? $program['level'] : 'Intermediate';
                            $format = isset($program['format']) ? $program['format'] : 'Online';
                            $img_url = isset($program['image_url']) ? $program['image_url'] : '/assets/images/program-placeholder.jpg';
                            $start_date = isset($program['start_date']) ? date('M d, Y', strtotime($program['start_date'])) : 'Flexible';
                            $price = isset($program['price']) ? $program['price'] : 'Free';
                            $spots = isset($program['available_spots']) ? $program['available_spots'] : rand(5, 30);
                            $total_spots = isset($program['total_spots']) ? $program['total_spots'] : $spots + rand(0, 20);
                            $percent_filled = ($total_spots - $spots) / $total_spots * 100;
                    ?>
                            <div class="col-lg-4 col-md-6 program-item <?= $category ?> mb-4">
                                <div class="card program-card h-100 border-0 shadow-sm">
                                    <div class="position-relative">
                                        <img src="<?= $img_url ?>" class="card-img-top" alt="<?= $program['name'] ?>" style="height: 200px; object-fit: cover;">
                                        <div class="program-overlay">
                                            <div class="program-badges">
                                                <?php if ($format == 'Online') : ?>
                                                    <span class="badge bg-info"><i class="ri-global-line me-1"></i> Online</span>
                                                <?php elseif ($format == 'Hybrid') : ?>
                                                    <span class="badge bg-warning"><i class="ri-compass-3-line me-1"></i> Hybrid</span>
                                                <?php else : ?>
                                                    <span class="badge bg-success"><i class="ri-map-pin-line me-1"></i> In-person</span>
                                                <?php endif; ?>

                                                <?php if ($level == 'Beginner') : ?>
                                                    <span class="badge bg-success">Beginner</span>
                                                <?php elseif ($level == 'Intermediate') : ?>
                                                    <span class="badge bg-primary">Intermediate</span>
                                                <?php else : ?>
                                                    <span class="badge bg-danger">Advanced</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge badge-soft-primary"><?= ucfirst($category) ?></span>
                                            <span class="text-muted"><i class="ri-time-line align-bottom"></i> <?= $duration ?></span>
                                        </div>
                                        <h5 class="card-title"><?= $program['name'] ?></h5>
                                        <p class="card-text text-muted mb-3"><?= isset($program['short_description']) ? substr($program['short_description'], 0, 120) . '...' : 'This program provides comprehensive training to enhance your skills and knowledge in this field.' ?></p>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted fs-12">Availability</span>
                                                <span class="text-muted fs-12"><?= $spots ?> spots left</span>
                                            </div>
                                            <div class="progress animated-progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent_filled ?>%" aria-valuenow="<?= $percent_filled ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <div class="program-info">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-calendar-line text-primary fs-17 me-2"></i>
                                                        <div>
                                                            <h6 class="fs-13 mb-0">Start Date</h6>
                                                            <p class="text-muted mb-0 fs-12"><?= $start_date ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-price-tag-3-line text-primary fs-17 me-2"></i>
                                                        <div>
                                                            <h6 class="fs-13 mb-0">Price</h6>
                                                            <p class="text-muted mb-0 fs-12"><?= $price ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                                        <div class="d-flex justify-content-between">
                                            <?php
                                            $slug = isset($program['slug']) ? $program['slug'] : strtolower(str_replace([' ', '&', '+', '/', '(', ')', ','], '-', trim($program['name'])));
                                            $slug = preg_replace('/-+/', '-', $slug); // Replace multiple dashes with single dash
                                            ?>
                                            <a href="<?= base_url('programs/' . $slug) ?>" class="btn btn-soft-primary btn-sm">Learn More</a>
                                            <a href="<?= base_url('apply/' . $slug) ?>" class="btn btn-primary btn-sm">Apply Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        // Dummy programs if no data provided
                        $dummyPrograms = [
                            [
                                'name' => 'Web Development Bootcamp',
                                'category' => 'technical',
                                'level' => 'Intermediate',
                                'format' => 'Online',
                                'duration' => '12 weeks',
                                'start_date' => 'Apr 15, 2025',
                                'price' => '$2,999',
                                'spots' => 8,
                                'total_spots' => 25,
                            ],
                            [
                                'name' => 'Leadership Excellence',
                                'category' => 'leadership',
                                'level' => 'Advanced',
                                'format' => 'Hybrid',
                                'duration' => '8 weeks',
                                'start_date' => 'May 1, 2025',
                                'price' => '$1,499',
                                'spots' => 12,
                                'total_spots' => 20,
                            ],
                            [
                                'name' => 'Digital Marketing Fundamentals',
                                'category' => 'business',
                                'level' => 'Beginner',
                                'format' => 'Online',
                                'duration' => '6 weeks',
                                'start_date' => 'Apr 10, 2025',
                                'price' => '$999',
                                'spots' => 15,
                                'total_spots' => 30,
                            ],
                            [
                                'name' => 'UX/UI Design Masterclass',
                                'category' => 'creative',
                                'level' => 'Intermediate',
                                'format' => 'Online',
                                'duration' => '10 weeks',
                                'start_date' => 'Jun 5, 2025',
                                'price' => '$1,799',
                                'spots' => 6,
                                'total_spots' => 20,
                            ],
                            [
                                'name' => 'Data Science Fundamentals',
                                'category' => 'technical',
                                'level' => 'Intermediate',
                                'format' => 'Online',
                                'duration' => '14 weeks',
                                'start_date' => 'May 20, 2025',
                                'price' => '$2,499',
                                'spots' => 10,
                                'total_spots' => 25,
                            ],
                            [
                                'name' => 'Project Management Professional',
                                'category' => 'business',
                                'level' => 'Advanced',
                                'format' => 'Hybrid',
                                'duration' => '12 weeks',
                                'start_date' => 'Jul 8, 2025',
                                'price' => '$1,999',
                                'spots' => 4,
                                'total_spots' => 15,
                            ]
                        ];

                        foreach ($dummyPrograms as $program) {
                            $percent_filled = ($program['total_spots'] - $program['spots']) / $program['total_spots'] * 100;
                        ?>
                            <div class="col-lg-4 col-md-6 program-item <?= $program['category'] ?> mb-4">
                                <div class="card program-card h-100 border-0 shadow-sm">
                                    <div class="position-relative">
                                        <img src="/assets/images/small/img-<?= rand(1, 12) ?>.jpg" class="card-img-top" alt="<?= $program['name'] ?>" style="height: 200px; object-fit: cover;">
                                        <div class="program-overlay">
                                            <div class="program-badges">
                                                <?php if ($program['format'] == 'Online') : ?>
                                                    <span class="badge bg-info"><i class="ri-global-line me-1"></i> Online</span>
                                                <?php elseif ($program['format'] == 'Hybrid') : ?>
                                                    <span class="badge bg-warning"><i class="ri-compass-3-line me-1"></i> Hybrid</span>
                                                <?php else : ?>
                                                    <span class="badge bg-success"><i class="ri-map-pin-line me-1"></i> In-person</span>
                                                <?php endif; ?>

                                                <?php if ($program['level'] == 'Beginner') : ?>
                                                    <span class="badge bg-success">Beginner</span>
                                                <?php elseif ($program['level'] == 'Intermediate') : ?>
                                                    <span class="badge bg-primary">Intermediate</span>
                                                <?php else : ?>
                                                    <span class="badge bg-danger">Advanced</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge badge-soft-primary"><?= ucfirst($program['category']) ?></span>
                                            <span class="text-muted"><i class="ri-time-line align-bottom"></i> <?= $program['duration'] ?></span>
                                        </div>
                                        <h5 class="card-title"><?= $program['name'] ?></h5>
                                        <p class="card-text text-muted mb-3">A comprehensive program designed to enhance your skills and advance your career in this field. Learn practical skills from industry experts.</p>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted fs-12">Availability</span>
                                                <span class="text-muted fs-12"><?= $program['spots'] ?> spots left</span>
                                            </div>
                                            <div class="progress animated-progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent_filled ?>%" aria-valuenow="<?= $percent_filled ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <div class="program-info">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-calendar-line text-primary fs-17 me-2"></i>
                                                        <div>
                                                            <h6 class="fs-13 mb-0">Start Date</h6>
                                                            <p class="text-muted mb-0 fs-12"><?= $program['start_date'] ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-price-tag-3-line text-primary fs-17 me-2"></i>
                                                        <div>
                                                            <h6 class="fs-13 mb-0">Price</h6>
                                                            <p class="text-muted mb-0 fs-12"><?= $program['price'] ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                                        <div class="d-flex justify-content-between">
                                            <a href="<?= base_url('program/' . strtolower(str_replace(' ', '-', $program['name']))) ?>" class="btn btn-soft-primary btn-sm">Learn More</a>
                                            <a href="<?= base_url('apply/' . strtolower(str_replace(' ', '-', $program['name']))) ?>" class="btn btn-primary btn-sm">Apply Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>

                <!-- CTA Section -->
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="card bg-gradient text-white shadow-lg border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="fw-semibold mb-3">Still have questions?</h4>
                                        <p class="mb-md-0">Our team is ready to help you find the right program for your goals. Contact us for personalized guidance.</p>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="<?= base_url('contact') ?>" class="btn btn-light">Contact Us <i class="ri-arrow-right-line ms-1 align-bottom"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Programs content section -->

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- end layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>

</body>

</html>

<style>
    /* Program Card Styles */
    .program-card {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .program-card:hover {
        transform: translateY(-10px);
    }

    .program-card .card-img-top {
        transition: all 0.5s ease;
    }

    .program-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .program-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.6) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1rem;
    }

    .program-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-buttons {
        margin-bottom: 2rem;
    }

    .filter-btn {
        border-radius: 50px;
        padding: 0.5rem 1.25rem;
        transition: all 0.3s ease;
    }

    .filter-btn.active {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .animated-progress .progress-bar {
        animation: progressAnimation 1.5s ease-in-out;
    }

    @keyframes progressAnimation {
        0% {
            width: 0;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Program filtering functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const programItems = document.querySelectorAll('.program-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(innerBtn => {
                    innerBtn.classList.remove('active');
                    if (innerBtn.classList.contains('btn-primary')) {
                        innerBtn.classList.remove('btn-primary');
                        innerBtn.classList.add('btn-outline-primary');
                    }
                });

                // Add active class to clicked button
                this.classList.add('active');
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');

                const filter = this.getAttribute('data-filter');

                // Show/hide program items based on filter
                programItems.forEach(item => {
                    if (filter === 'all' || item.classList.contains(filter)) {
                        item.style.display = 'block';

                        // Add fade-in animation
                        item.style.opacity = '0';
                        item.style.animation = 'fadeIn 0.5s forwards';
                        item.style.animationDelay = '0.1s';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Define the fade-in animation
        const style = document.createElement('style');
        style.innerHTML = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
        document.head.appendChild(style);
    });
</script>