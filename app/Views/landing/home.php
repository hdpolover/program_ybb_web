<?php
// Extract the current active program (usually the latest one)
$program_info = !empty($programs) ? $programs[0] : [];
$program_testimonies = $testimonies ?? [];
$program_photos = $photos ?? [];
?>

<?= $this->include('partials/main') ?>

<head>
    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    
    <!-- Gallery styles -->
    <link href="/assets/css/gallery-styles.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">
    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- Start hero section with full-width banner -->
        <section class="section pb-0 hero-section" id="hero">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <div class="position-relative">
                <img src="<?= function_exists('compress_image') && isset($program_photos[0]['img_url']) ? compress_image($program_photos[0]['img_url'], 1920, 600, 80, true) : (isset($program_photos[0]['img_url']) ? $program_photos[0]['img_url'] : '/assets/images/default-banner.jpg'); ?>" class="d-block w-100" alt="Program Banner" style="height: 80vh; object-fit: cover;">
            </div>
        </section>
        <!-- End hero section -->

        <!-- Start program category title and tagline section -->
        <section class="section bg-primary" id="program-category">
            <div class="bg-overlay bg-overlay-pattern" style="opacity: 0.4;"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold text-white"><?= $category['name'] ?? 'Program Name' ?></h2>
                            <p class="text-white-50 fs-16"><?= $category['tagline'] ?? '' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Program information cards -->
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="card card-animate h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-primary text-white rounded-circle fs-4">
                                                <i class="ri-map-pin-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="fs-16 text-center">Location</h5>
                                <p class="text-muted mb-0 text-center"><?= $category['location'] ?? 'Location not available' ?></p>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card card-animate h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-primary text-white rounded-circle fs-4">
                                                <i class="ri-phone-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="fs-16 text-center">Contact</h5>
                                <p class="text-muted mb-0 text-center"><?= $category['contact'] ?? 'Contact not available' ?></p>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card card-animate h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-primary text-white rounded-circle fs-4">
                                                <i class="ri-mail-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="fs-16 text-center">Email</h5>
                                <p class="text-muted mb-0 text-center"><?= $category['email'] ?? 'Email not available' ?></p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End program category section -->

        <!-- Start current programs section -->
        <section class="section" id="current-programs">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold"><?= $category['name'] . ' Programs ' ?? 'Programs' ?></h2>
                            <p class="text-muted">Explore our active programs and initiatives</p>
                        </div>
                    </div>
                </div>

                <!-- Programs horizontal list -->
                <?php 
                // Filter for upcoming programs only - programs where end date is in the future
                $upcoming_programs = array_filter($programs ?? [], function($program) {
                    $current_time = time();
                    return isset($program['end_date']) && strtotime($program['end_date']) >= $current_time;
                });
                
                if (!empty($upcoming_programs)): 
                ?>
                <div class="position-relative mb-4">
                    <div class="swiper programSwiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($upcoming_programs as $index => $program): ?>
                                <div class="swiper-slide">
                                    <div class="card h-100 border-0 shadow-sm position-relative">
                                        <!-- Ribbon -->
                                        <div class="ribbon ribbon-primary ribbon-shape position-absolute">
                                            <span class="d-flex align-items-center"><i class="ri-calendar-line me-1"></i>Upcoming</span>
                                        </div>

                                        <?php if(isset($program['featured']) && $program['featured']): ?>
                                            <div class="ribbon ribbon-danger position-absolute" style="top: 40px;">
                                                <span class="d-flex align-items-center"><i class="ri-star-fill me-1"></i>Featured</span>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Card image (if available) -->
                                        <?php if(isset($program['logo_url'])): ?>
                                            <div class="position-relative program-img-wrapper">
                                                <img src="<?= compress_image($program['logo_url'], 600, 300, 80, true); ?>" alt="<?= $program['name'] ?>" class="card-img-top program-img">
                                                <div class="program-img-overlay"></div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="avatar-sm icon-effect">
                                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                        <i class="ri-team-line"></i>
                                                    </div>
                                                </div>
                                                <div class="badge bg-soft-primary text-primary fs-12 px-3 py-2 rounded-pill">
                                                    <?= isset($program['category_name']) ? $program['category_name'] : 'Program' ?>
                                                </div>
                                            </div>
                                            
                                            <h4 class="card-title mb-2"><?= $program['name'] ?? 'Program Name' ?></h4>
                                            <p class="card-text text-muted mb-3"><?= substr($program['description'] ?? '', 0, 100) . (strlen($program['description'] ?? '') > 100 ? '...' : '') ?></p>
                                            
                                            <!-- Program details with enhanced styling -->
                                            <div class="mt-3 pt-2 border-top">
                                                <?php if(isset($program['start_date']) && isset($program['end_date'])): ?>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                    <i class="ri-calendar-line"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0 fs-13">Program Duration</h6>
                                                            <small class="text-muted">
                                                                <?= date('M d, Y', strtotime($program['start_date'])) ?> - 
                                                                <?= date('M d, Y', strtotime($program['end_date'])) ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($program['location'])): ?>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title bg-soft-danger text-danger rounded-circle">
                                                                    <i class="ri-map-pin-line"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0 fs-13">Location</h6>
                                                            <small class="text-muted"><?= $program['location'] ?></small>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if(isset($program['participants_count'])): ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                                    <i class="ri-user-line"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0 fs-13">Participants</h6>
                                                            <small class="text-muted"><?= $program['participants_count'] ?? '0' ?> enrolled</small>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent pt-0 pb-4 px-4">
                                            <a href="<?= site_url('programs/' . url_title(strtolower($program['name']), '-', true)) ?>" class="btn btn-primary w-100">
                                                <i class="ri-arrow-right-line me-1"></i> Learn More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination position-relative mt-4"></div>
                    </div>
                    
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next program-swiper-button-next"></div>
                    <div class="swiper-button-prev program-swiper-button-prev"></div>
                </div>
                <?php else: ?>
                    <div class="text-center p-4 rounded-3 bg-light border">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-soft-primary text-primary display-5 rounded-circle">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                        </div>
                        <h5>No Upcoming Programs</h5>
                        <p class="text-muted">Check back soon for upcoming programs and initiatives.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Button for Previous Programs -->
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <a href="<?= site_url('programs/previous') ?>" class="btn btn-outline-primary">
                            <i class="ri-history-line me-1"></i> View Previous Programs
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- End current programs section -->

        <!-- Start program guideline section -->
        <section class="py-5 bg-primary position-relative">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-sm">
                        <div>
                            <h2 class="text-white mb-0 fw-semibold"><?= $program_info['name']; ?> Guideline</h2>
                            <p class="text-white-50 mb-0 mt-2">Learn more about our program guideline</p>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div>
                            <a href="<?= $program_info['guideline']; ?>" target="_blank" class="btn bg-gradient btn-danger"><i class="ri-file-download-line align-middle me-1"></i> View & Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End program guideline section -->

        <!-- Start program details section with enhanced UI -->
        <section class="section py-5" id="program-details">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold">About Our Program</h2>
                            <p class="text-muted">Learn about our mission, vision, and goals</p>
                        </div>
                    </div>
                </div>

                <!-- Introduction with photo -->
                <div class="row align-items-center mb-5 pb-lg-5 border-bottom">
                    <div class="col-lg-6 order-lg-2">
                        <div class="position-relative mb-4 mb-lg-0">
                            <?php if (isset($program_photos[0])): ?>
                                <img src="<?= compress_image($program_photos[0]['img_url'], 600, 400, 80, true); ?>" alt="" class="img-fluid rounded-4 shadow-lg">
                                <?php if (isset($program_photos[1])): ?>
                                    <img src="<?= compress_image($program_photos[1]['img_url'], 300, 200, 80, true); ?>" alt="" class="img-fluid rounded-4 shadow-lg position-absolute" style="bottom: -25%; right: -10%; max-width: 50%; border: 5px solid #fff;">
                                <?php endif; ?>
                            <?php else: ?>
                                <img src="/assets/images/default-program.jpg" alt="" class="img-fluid rounded-4 shadow-lg">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="mt-4 mt-lg-0">
                            <p class="text-muted fs-16"><?= $category['about'] ?? 'Program description not available'; ?></p>

                        </div>
                    </div>
                </div>

                <!-- Benefits, Goals, and Agenda in cards with icons -->
                <div class="row g-4 mt-4">
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="mx-auto mb-4" style="width: 70px; height: 70px;">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-2 h-100 d-flex align-items-center justify-content-center">
                                            <i class="ri-gift-line"></i>
                                        </div>
                                    </div>
                                    <h4 class="fs-18 mb-3">Benefits & Opportunities</h4>
                                </div>
                                <div class="position-relative">
                                    <div class="p-2 px-3 rounded-3 mb-3">
                                        <p class="text-muted mb-0"><?= $category['benefits'] ?? 'Information not available'; ?></p>
                                    </div>

                                    <?php
                                    // Display an image if available (using 3rd image from array if exists)
                                    if (isset($program_photos[2])):
                                    ?>
                                        <div class="mt-4">
                                            <img src="<?= compress_image($program_photos[2]['img_url'], 400, 200, 80, true); ?>" alt="Benefits" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="mx-auto mb-4" style="width: 70px; height: 70px;">
                                        <div class="avatar-title bg-warning-subtle text-warning rounded-circle fs-2 h-100 d-flex align-items-center justify-content-center">
                                            <i class="ri-focus-2-line"></i>
                                        </div>
                                    </div>
                                    <h4 class="fs-18 mb-3">Program Values</h4>
                                </div>
                                <div class="position-relative">
                                    <div class="p-2 px-3 rounded-3 mb-3">
                                        <p class="text-muted mb-0"><?= $category['core_values'] ?? 'Goals information not available'; ?></p>
                                    </div>

                                    <?php
                                    // Display an image if available (using 4th image from array if exists)
                                    if (isset($program_photos[3])):
                                    ?>
                                        <div class="mt-4">
                                            <img src="<?= compress_image($program_photos[3]['img_url'], 400, 200, 80, true); ?>" alt="Goals" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="mx-auto mb-4" style="width: 70px; height: 70px;">
                                        <div class="avatar-title bg-success-subtle text-success rounded-circle fs-2 h-100 d-flex align-items-center justify-content-center">
                                            <i class="ri-calendar-todo-line"></i>
                                        </div>
                                    </div>
                                    <h4 class="fs-18 mb-3">Program Objectives</h4>
                                </div>
                                <div class="position-relative">
                                    <div class="p-2 px-3 rounded-3 mb-3">
                                        <p class="text-muted mb-0"><?= $category['objectives'] ?? 'Agenda information not available'; ?></p>
                                    </div>

                                    <?php
                                    // Display an image if available (using 5th image from array if exists)
                                    if (isset($program_photos[4])):
                                    ?>
                                        <div class="mt-4">
                                            <img src="<?= compress_image($program_photos[4]['img_url'], 400, 200, 80, true); ?>" alt="Agenda" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End enhanced program details section -->

        <!-- Start video section -->
        <section class="section bg-light" id="program-video">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold">Watch Our Video</h2>
                            <p class="text-muted">Learn more about our program through this video</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="ratio ratio-16x9 rounded-4 shadow-lg overflow-hidden">
                            <iframe src="<?= $program_info['registration_video_url']; ?>" title="Program Video" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End video section -->

        <!-- Include program gallery section -->
        <?= $this->include('landing/program-gallery') ?>

        <!-- Start testimonial section -->
        <section class="section bg-primary" id="testimonials">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold text-white">What People Say About Our Program</h2>
                            <p class="text-white-50">Discover the impact of our program through the experiences of our participants</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="swiper testimonial-swiper">
                            <div class="swiper-wrapper">
                                <?php foreach ($program_testimonies as $testimony) : ?>
                                    <div class="swiper-slide">
                                        <div class="card testimonial-card shadow-lg border-0">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div class="flex-shrink-0">
                                                        <img src="<?= compress_image($testimony['img_url'], 100, 100, 80, true); ?>" alt="<?= $testimony['person_name']; ?>"
                                                            class="avatar-lg rounded-circle border border-primary p-1">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="mb-1 text-primary"><?= $testimony['person_name']; ?></h5>
                                                        <p class="text-muted mb-0"><?= $testimony['occupation']; ?></p>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-0"><?= $testimony['testimony']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-pagination position-relative mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End testimonial section -->

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- End layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
    
    <!-- Gallery Modal init -->
    <script src="/assets/js/pages/gallery-modal.init.js"></script>

    <script>
        // Initialize Swiper for testimonials
        var testimonialSwiper = new Swiper(".testimonial-swiper", {
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
        
        // Initialize Swiper for programs horizontal list
        var programSwiper = new Swiper(".programSwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".program-swiper-button-next",
                prevEl: ".program-swiper-button-prev",
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
        });

        function formatEventDate() {
            <?php if (isset($program_info['start_date']) && isset($program_info['end_date'])): ?>
                const startDate = new Date("<?= $program_info['start_date']; ?>");
                const endDate = new Date("<?= $program_info['end_date']; ?>");

                const options = {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                };

                const startFormatted = startDate.toLocaleDateString('en-US', options);
                const endFormatted = endDate.toLocaleDateString('en-US', options);

                document.getElementById("event_date_display").innerHTML = startFormatted + " - " + endFormatted;
            <?php else: ?>
                document.getElementById("event_date_display").innerHTML = "Date to be announced";
            <?php endif; ?>
        }

        formatEventDate(); // Call the function when page loads

        function updateCountdown() {
            <?php if (isset($program_info['end_date'])): ?>
                const eventDate = new Date("<?= $program_info['end_date']; ?>").getTime();
                const now = new Date().getTime();
                const diff = eventDate - now;

                if (diff > 0) {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    document.getElementById("countdown").innerHTML = `Registration ends in ${days} days ${hours} hours ${minutes} minutes ${seconds} seconds`;
                } else {
                    document.getElementById("countdown").innerHTML = "Registration has ended";
                }
            <?php else: ?>
                document.getElementById("countdown").innerHTML = "Registration deadline to be announced";
            <?php endif; ?>
        }

        setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call to display the timer immediately
    </script>

    <style>
        /* Program Styles */
        .programSwiper {
            padding: 10px 5px 30px;
        }

        .programSwiper .swiper-slide {
            height: auto;
            padding: 10px;
        }

        .program-img-wrapper {
            height: 180px;
            overflow: hidden;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .program-img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .card:hover .program-img {
            transform: scale(1.05);
        }

        .program-img-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
        }

        .ribbon-shape {
            padding-right: 15px;
            padding-left: 15px;
            clip-path: polygon(0 0, 100% 0, 90% 100%, 0 100%);
        }

        .program-swiper-button-next,
        .program-swiper-button-prev {
            background-color: var(--vz-primary);
            color: #fff;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            transform: translateY(-50%);
        }

        .program-swiper-button-next:after,
        .program-swiper-button-prev:after {
            font-size: 14px;
            font-weight: bold;
        }
        
        .program-swiper-button-next:hover,
        .program-swiper-button-prev:hover {
            background-color: var(--vz-primary-darken-5);
        }
    </style>
</body>

</html>