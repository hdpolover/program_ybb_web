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

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Program Detail section -->
        <section class="section py-5 position-relative bg-light" id="program-detail">
            <div class="container">
                <!-- Program Header Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card overflow-hidden border-0 shadow-lg rounded-4">
                            <?php if (isset($program['banner_image_url']) && !empty($program['banner_image_url'])) : ?>
                                <div class="position-relative">
                                    <img src="<?= function_exists('compress_image') ? compress_image($program['banner_image_url'], 1200, 300, 80, true) : $program['banner_image_url'] ?>" alt="<?= $program['name'] ?? 'Program' ?>" class="card-img-top" style="height: 300px; object-fit: cover;">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb mb-2">
                                                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-white">Home</a></li>
                                                <li class="breadcrumb-item"><a href="<?= base_url('programs') ?>" class="text-white">Programs</a></li>
                                                <li class="breadcrumb-item active text-white" aria-current="page"><?= $program['name'] ?? 'Program Detail' ?></li>
                                            </ol>
                                        </nav>
                                        <h1 class="text-white mb-0"><?= $program['name'] ?? 'Program Detail' ?></h1>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="card-body">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                                            <li class="breadcrumb-item"><a href="<?= base_url('programs') ?>">Programs</a></li>
                                            <li class="breadcrumb-item active" aria-current="page"><?= $program['name'] ?? 'Program Detail' ?></li>
                                        </ol>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Program Content Column -->
                    <div class="col-lg-8">
                        <!-- Program Overview -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h2 class="card-title mb-3">Overview</h2>
                                <?php if (isset($program['description']) && !empty($program['description'])) : ?>
                                    <p class="card-text"><?= $program['description'] ?></p>
                                <?php else : ?>
                                    <p class="card-text text-muted">No description available for this program.</p>
                                <?php endif; ?>

                                <?php if (isset($program['objectives']) && !empty($program['objectives'])) : ?>
                                    <h4 class="mt-4 mb-3">Program Objectives</h4>
                                    <div class="d-flex flex-column gap-2">
                                        <?php 
                                            $objectives = is_array($program['objectives']) ? $program['objectives'] : explode("\n", $program['objectives']);
                                            foreach ($objectives as $objective) :
                                                if (trim($objective)) :
                                        ?>
                                            <div class="d-flex align-items-start">
                                                <i class="ri-check-line text-success me-2 fs-17"></i>
                                                <p class="mb-0"><?= trim($objective) ?></p>
                                            </div>
                                        <?php 
                                                endif;
                                            endforeach; 
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Program Schedules -->
                        <?php if (isset($schedules) && !empty($schedules)) : ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h2 class="card-title mb-3">Schedule</h2>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Topic</th>
                                                <th scope="col">Location</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($schedules as $schedule) : ?>
                                            <tr>
                                                <td>
                                                    <?php if (isset($schedule['start_date'])) : ?>
                                                        <?= date('M d, Y', strtotime($schedule['start_date'])) ?>
                                                        <?php if (isset($schedule['end_date']) && $schedule['end_date'] != $schedule['start_date']) : ?>
                                                            to <?= date('M d, Y', strtotime($schedule['end_date'])) ?>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        TBA
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($schedule['start_time'])) : ?>
                                                        <?= date('h:i A', strtotime($schedule['start_time'])) ?>
                                                        <?php if (isset($schedule['end_time'])) : ?>
                                                            - <?= date('h:i A', strtotime($schedule['end_time'])) ?>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        TBA
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $schedule['title'] ?? 'TBA' ?></td>
                                                <td><?= $schedule['location'] ?? 'TBA' ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Program Testimonials -->
                        <?php if (isset($testimonials) && !empty($testimonials)) : ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h2 class="card-title mb-4">What Participants Say</h2>
                                <div class="row">
                                    <?php foreach ($testimonials as $testimonial) : ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <div class="d-flex mb-3">
                                                    <div class="flex-shrink-0">
                                                        <?php if (isset($testimonial['avatar_url']) && !empty($testimonial['avatar_url'])) : ?>
                                                            <img src="<?= function_exists('compress_image') ? compress_image($testimonial['avatar_url'], 100, 100, 80, true) : $testimonial['avatar_url'] ?>" alt="<?= $testimonial['name'] ?? 'Participant' ?>" class="avatar-sm rounded-circle">
                                                        <?php else : ?>
                                                            <div class="avatar-sm">
                                                                <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-16">
                                                                    <i class="ri-user-line"></i>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="fs-15 mb-0"><?= $testimonial['name'] ?? 'Participant' ?></h5>
                                                        <p class="text-muted mb-1"><?= $testimonial['position'] ?? '' ?></p>
                                                        <div class="text-warning">
                                                            <?php 
                                                                $rating = isset($testimonial['rating']) ? (int)$testimonial['rating'] : 5;
                                                                for ($i = 1; $i <= 5; $i++) {
                                                                    echo $i <= $rating ? '<i class="ri-star-fill"></i>' : '<i class="ri-star-line"></i>';
                                                                }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-0"><?= $testimonial['testimonial'] ?? '' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar Column -->
                    <div class="col-lg-4">
                        <!-- Program Details Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title mb-3">Program Details</h3>
                                <ul class="list-group list-group-flush">
                                    <?php if (isset($program['duration']) && !empty($program['duration'])) : ?>
                                    <li class="list-group-item px-0 d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-time-line text-primary me-2 fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-15 mb-1">Duration</h5>
                                            <p class="text-muted mb-0"><?= $program['duration'] ?></p>
                                        </div>
                                    </li>
                                    <?php endif; ?>

                                    <?php if (isset($program['start_date']) && !empty($program['start_date'])) : ?>
                                    <li class="list-group-item px-0 d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-calendar-line text-primary me-2 fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-15 mb-1">Start Date</h5>
                                            <p class="text-muted mb-0"><?= date('F d, Y', strtotime($program['start_date'])) ?></p>
                                        </div>
                                    </li>
                                    <?php endif; ?>

                                    <?php if (isset($program['location']) && !empty($program['location'])) : ?>
                                    <li class="list-group-item px-0 d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-map-pin-line text-primary me-2 fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-15 mb-1">Location</h5>
                                            <p class="text-muted mb-0"><?= $program['location'] ?></p>
                                        </div>
                                    </li>
                                    <?php endif; ?>

                                    <?php if (isset($program['price']) || isset($program['fee'])) : ?>
                                    <li class="list-group-item px-0 d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-money-dollar-circle-line text-primary me-2 fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-15 mb-1">Fee</h5>
                                            <p class="text-muted mb-0"><?= $program['price'] ?? $program['fee'] ?? 'Contact for pricing' ?></p>
                                        </div>
                                    </li>
                                    <?php endif; ?>

                                    <?php if (isset($program['capacity']) && !empty($program['capacity'])) : ?>
                                    <li class="list-group-item px-0 d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-group-line text-primary me-2 fs-16"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-15 mb-1">Capacity</h5>
                                            <p class="text-muted mb-0"><?= $program['capacity'] ?> participants</p>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>

                                <div class="mt-4">
                                    <?php if (isset($program['registration_open']) && $program['registration_open']) : ?>
                                        <a href="<?= base_url('registration/' . ($program['slug'] ?? $program['id'] ?? '')) ?>" class="btn btn-primary w-100 mb-2">Register Now</a>
                                    <?php else : ?>
                                        <button class="btn btn-soft-primary w-100 mb-2" disabled>Registration Closed</button>
                                    <?php endif; ?>
                                    
                                    <a href="javascript:void(0);" class="btn btn-soft-info w-100 mb-2">Download Brochure</a>
                                    <a href="javascript:void(0);" class="btn btn-soft-secondary w-100">Contact Program Coordinator</a>
                                </div>
                            </div>
                        </div>

                        <!-- Share Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title mb-3">Share This Program</h3>
                                <div class="d-flex gap-2">
                                    <a href="javascript:void(0);" class="btn btn-soft-primary btn-icon">
                                        <i class="ri-facebook-fill fs-16"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-soft-info btn-icon">
                                        <i class="ri-twitter-fill fs-16"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon">
                                        <i class="ri-mail-line fs-16"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-soft-success btn-icon">
                                        <i class="ri-whatsapp-line fs-16"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-soft-secondary btn-icon">
                                        <i class="ri-linkedin-fill fs-16"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Related Programs Card -->
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-3">You May Also Like</h3>
                                <div class="related-programs">
                                    <!-- This would ideally be populated with other programs -->
                                    <div class="d-flex mb-3 p-2 bg-light rounded">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-primary rounded">
                                                    <i class="ri-award-line text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-15 mb-1">Leadership Development</h5>
                                            <p class="text-muted mb-0">Enhance your leadership skills</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3 p-2 bg-light rounded">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-danger rounded">
                                                    <i class="ri-book-open-line text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-15 mb-1">Digital Marketing</h5>
                                            <p class="text-muted mb-0">Learn modern marketing techniques</p>
                                        </div>
                                    </div>
                                    <div class="d-flex p-2 bg-light rounded">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-success rounded">
                                                    <i class="ri-code-s-slash-line text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-15 mb-1">Web Development</h5>
                                            <p class="text-muted mb-0">Build responsive websites</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Program Detail section -->

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