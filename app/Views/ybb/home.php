<?= $this->include('partials/main') ?>

<head>

    <?php echo view(
        'partials/title-meta',
        array(
            'program_info' => $program_info,
            'title' => $title,
            'meta_title' => $title,
            'meta_description' => $title,
            'tags' => $title,
            'slug' => $title,
        )
    ); ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Add to head -->
    <link title="timeline-styles" rel="stylesheet" href="https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css">
    <script src="https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js"></script>

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('ybb/common/navbar') ?>

        <!-- start hero section -->
        <section class="section pb-0 hero-section" id="hero">
            <div class="carousel slide carousel-fade" data-bs-ride="carousel">
                <div class="carousel-inner shadow-lg p-2 bg-white rounded">
                    <div class="carousel-item active" data-bs-interval="3000">
                        <div class="position-relative">
                            <img src="<?= $home_details['banner1_img_url']; ?>" class="d-block w-100" alt="...">
                            <!-- <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50"></div>
                            <div class="position-absolute top-50 start-20 ms-4 text-white" style="width: 33%;">
                                <h1 class="mb-4 text-white text-start"><?= $program_info['name'] ?></h1>
                                <div class="countdown-timer">
                                    <div id="countdown" class="h4 text-white text-start"></div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="carousel-item" data-bs-interval="3000">
                        <img src="<?= $home_details['banner2_img_url']; ?>" class="d-block w-100" alt="...">
                    </div>
                </div>
            </div>

        </section>
        <!-- end hero section -->

        <!-- start event info -->
        <section class="section bg-light" id="event-info">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold lh-base"><?= $program_info['name'] ?></h1>
                            <p class="text-muted"><?= $program_info['tagline']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 justify-content-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                <i class="ri-calendar-line fs-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="fs-18">Event Date</h5>
                                <p class="text-muted mb-0" id="event_date"></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                <i class="ri-map-pin-line fs-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="fs-18">Location</h5>
                                <p class="text-muted mb-0"><?= $program_info['location'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                <i class="ri-phone-line fs-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="fs-18">Contact</h5>
                                <p class="text-muted mb-0"><?= $program_info['contact'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-success rounded-circle">
                                                <i class="ri-mail-line fs-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="fs-18">Email</h5>
                                <p class="text-muted mb-0"><?= $program_info['email'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end event info -->

        <!-- start cta -->
        <section class="py-5 bg-primary position-relative">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-sm">
                        <div>
                            <h2 class="text-white mb-0 fw-semibold"><?= $program_info['name']; ?> Guideline</h2>
                            <br>
                            <p class="text-white-50 mb-0">Learn more about our program guideline</p>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-sm-auto">
                        <div>
                            <a href="<?= $program_info['guideline']; ?>" target="_blank" class="btn bg-gradient btn-danger"><i class="ri-file-download-line align-middle me-1"></i> View & Download</a>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end cta -->

        <!-- start Work Process -->
        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div>
                            <img src="<?= $program_photos[0]['img_url']; ?>" alt="" class="img-fluid mx-auto">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-muted">
                            <div class="avatar-sm icon-effect mb-4">
                                <div class="avatar-title bg-transparent rounded-circle text-success h1">
                                    <i class="ri-collage-line fs-36"></i>
                                </div>
                            </div>
                            <h3 class="mb-3 fs-20"><?= $program_info['name']; ?></h3>
                            <p class="mb-4 ff-secondary fs-16"><?= $home_details['introduction']; ?></p>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                </br>
                <!-- end row -->
                <div class="row text-center">
                    <div class="col-lg-4">
                        <div class="process-card mt-4">
                            <div class="process-arrow-img d-none d-lg-block">
                                <img src="/assets/images/landing/process-arrow-img.png" alt="" class="img-fluid">
                            </div>
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent text-success rounded-circle h1">
                                    <i class="ri-question-answer-line"></i>
                                </div>
                            </div>

                            <h5>Benefits & Opportunities</h5>
                            <p class="text-muted ff-secondary"><?= $home_details['reason']; ?></p>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4">
                        <div class="process-card mt-4">
                            <div class="process-arrow-img d-none d-lg-block">
                                <img src="/assets/images/landing/process-arrow-img.png" alt="" class="img-fluid">
                            </div>
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent text-success rounded-circle h1">
                                    <i class="ri-target-line"></i>
                                </div>
                            </div>

                            <h5>Program Goals</h5>
                            <p class="text-muted ff-secondary"><?= $home_details['summary']; ?></p>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4">
                        <div class="process-card mt-4">
                            <div class="avatar-sm icon-effect mx-auto mb-4">
                                <div class="avatar-title bg-transparent text-success rounded-circle h1">
                                    <i class="ri-calendar-todo-line"></i>
                                </div>
                            </div>

                            <h5>Agenda</h5>
                            <p class="text-muted ff-secondary"><?= $home_details['agenda']; ?></p>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end Work Process -->

        <!-- start random photos section -->
        <section class="py-5 bg-primary position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div style="height: 400px; overflow: hidden;">
                            <img src="<?= $program_photos[array_rand($program_photos)]['img_url']; ?>" alt="Random Program Photo" class="img-fluid rounded shadow w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div style="height: 400px; overflow: hidden;">
                            <img src="<?= $program_photos[array_rand($program_photos)]['img_url']; ?>" alt="Random Program Photo" class="img-fluid rounded shadow w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end random photos section -->

        <!-- start video section -->
        <section class="section bg-light" id="video">
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
                        <div class="ratio ratio-16x9">
                            <iframe src="<?= $program_info['registration_video_url']; ?>" title="Program Video" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end video section -->

        <!-- start timeline section -->
        <section class="section" id="timeline">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold">Program Schedule</h2>
                            <p class="text-muted">Timeline of key program events and activities</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- <div class="col-lg-12">
                        <div id="timeline-embed" style="width: 100%; height: 100vh"></div>
                    </div> -->
                    <?= $this->include('ybb/timeline') ?>
                </div>
            </div>
        </section>
        <!-- end timeline section -->

        <!-- start testimonial section -->
        <section class="section bg-primary" id="testimonials">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold text-white">What Our Participants Say</h2>
                            <p class="text-white">Discover the impact of our program through the experiences of our participants</p>
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
                                                        <img src="<?= $testimony['img_url']; ?>" alt="<?= $testimony['person_name']; ?>"
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
        <!-- end testimonial section -->

        <?= $this->include('ybb/common/footer') ?>

    </div>
    <!-- end layout wrapper -->



    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>

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


        function formatEventDate() {
            const startDate = new Date("<?= $program_info['start_date']; ?>");
            const endDate = new Date("<?= $program_info['end_date']; ?>");

            const options = {
                month: 'long',
                day: '2-digit',
                year: 'numeric'
            };
            const startFormatted = startDate.toLocaleDateString('en-US', options);
            const endFormatted = endDate.toLocaleDateString('en-US', options);

            const [startMonth, startDay, startYear] = startFormatted.split(' ');
            const [endMonth, endDay, endYear] = endFormatted.split(' ');

            let dateRange;
            if (startYear === endYear) {
                if (startMonth === endMonth) {
                    dateRange = `${startMonth} ${startDay.replace(',', '')} - ${endDay.replace(',', '')}, ${endYear}`;
                } else {
                    dateRange = `${startMonth} ${startDay.replace(',', '')} - ${endMonth} ${endDay.replace(',', '')}, ${endYear}`;
                }
            } else {
                dateRange = `${startMonth} ${startDay.replace(',', '')}, ${startYear} - ${endMonth} ${endDay.replace(',', '')}, ${endYear}`;
            }

            document.getElementById("event_date").innerHTML = dateRange;
        }

        formatEventDate(); // Call the function when page loads

        function updateCountdown() {
            const eventDate = new Date("<?= $home_details['banner1_date']; ?>").getTime();
            const now = new Date().getTime();
            const diff = eventDate - now;

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerHTML = `Registration ends in ${days} days ${hours} hours ${minutes} minutes ${seconds} seconds`;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call to display the timer immediately
    </script>

    <script type="text/javascript">
        // Convert PHP array to JavaScript array
        var events = [
            <?php foreach ($program_schedules as $schedule): ?> {
                    "start_date": {
                        "year": <?= date('Y', strtotime($schedule['start_date'])) ?>,
                        "month": <?= date('m', strtotime($schedule['start_date'])) ?>,
                        "day": <?= date('d', strtotime($schedule['start_date'])) ?>
                    },
                    "end_date": {
                        "year": <?= date('Y', strtotime($schedule['end_date'])) ?>,
                        "month": <?= date('m', strtotime($schedule['end_date'])) ?>,
                        "day": <?= date('d', strtotime($schedule['end_date'])) ?>
                    },
                    "text": {
                        "headline": "<?= trim($schedule['name']) ?>",
                    }
                },
            <?php endforeach; ?>
        ];

        // Create the TimelineJS configuration
        var timelineConfig = {
            "title": {
                "text": {
                    "headline": "<?= $program_info['name'] ?> Timeline",
                    "text": "Key events and activities"
                }
            },
            "events": events
        };

        // Initialize the Timeline
        window.addEventListener('load', function() {
            console.log('Window loaded');
            console.log('TL object exists:', typeof TL !== 'undefined');
            console.log('Timeline events:', events);

            try {
                window.timeline = new TL.Timeline('timeline-embed', timelineConfig);
                console.log('Timeline created successfully');
            } catch (error) {
                console.error('Timeline creation error:', error);
            }
        });
    </script>

</body>

</html>