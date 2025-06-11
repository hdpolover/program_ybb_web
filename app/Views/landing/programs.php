<?= $this->include('partials/main') ?>

<head>    <!-- Title Meta -->    <?php
    $siteName = env('DEFAULT_SITE_NAME', 'Japan Youth Summit');
    echo view('partials/landing-meta', array(
        'title' => 'Our Programs',
        'meta_description' => 'Explore ' . $siteName . ' programs and opportunities. From cultural exchange to leadership development, discover programs designed for your personal and professional growth.',
        'meta_keywords' => strtolower($siteName) . ' programs, ' . strtolower($siteName) . ' opportunities, cultural exchange japan, youth development programs'
    )); ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Programs title section -->
        <section class="section position-relative pb-5 bg-light" id="programs-title">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base"><?= $webSettings['name'] ?> Programs</h1>
                            <p class="text-muted fs-16">Explore our innovative programs designed to empower youth with the skills and knowledge needed for tomorrow's challenges.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Programs title section -->

        <!-- start Programs content section -->
        <section class="section py-5 position-relative" id="programs">
            <div class="container">

                <!-- Program Cards -->
                <div class="row program-grid">
                    <?php
                    if (isset($programs) && !empty($programs)) {
                        // if programs is just one, make it an array
                        if (isset($programs['programs']) && !empty($programs['programs'])) {
                            $programs = $programs['programs'];
                        } else {
                            $programs = [$programs];
                        }

                        // loop through programs
                        if (isset($programs) && !empty($programs)) {
                            foreach ($programs as $program) { ?>
                                <div class="col-lg-12 col-md-12 mb-4 <?= isset($program['category']) ? strtolower(str_replace(' ', '-', $program['category'])) : '' ?>">
                                    <?= view('landing/common/program_card_widget', ['program' => $program]); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="col-12 text-center py-5">
                            <div class="empty-state-container">
                                <i class="ri-calendar-event-line empty-state-icon mb-4" style="font-size: 4rem; color: #adb5bd;"></i>
                                <h4 class="fw-semibold text-muted mb-3">No Programs Available</h4>
                                <p class="text-muted fs-16 mb-4">We're currently working on exciting new programs for you.</p>
                                <p class="text-muted">Please check back later or contact us for more information.</p>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <!-- Other Programs Section -->
                <div class="row mt-5 mb-4">
                    <div class="col-12 mb-3">
                        <div class="text-center">
                            <h2 class="fw-semibold">Our Additional Programs</h2>
                            <p class="text-muted">Discover more educational opportunities we offer to develop future leaders</p>
                        </div>
                    </div> <?php if (isset($otherPrograms) && !empty($otherPrograms)): ?>
                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                            <?php foreach ($otherPrograms as $program): ?>
                                <div class="col">
                                    <div class="card h-100 shadow-sm border-0 overflow-hidden other-program-card">
                                        <?php if (!empty($program['banner_url'])): ?> <div class="position-relative">
                                                <img src="<?= esc($program['banner_url']) ?>" class="card-img-top" alt="<?= esc($program['name'] ?? 'Program') ?>" style="height: 120px; object-fit: cover;">
                                                <?php if (!empty($program['logo_url'])): ?>
                                                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n3">
                                                        <div class="avatar-sm bg-white rounded-circle p-1 shadow">
                                                            <img src="<?= esc($program['logo_url']) ?>" class="img-fluid rounded-circle" alt="<?= esc($program['name'] ?? 'Logo') ?>">
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body pt-4 pb-2">
                                            <h5 class="card-title text-center mt-2 mb-2"><?= esc($program['name'] ?? 'Program Name') ?></h5>

                                            <?php if (!empty($program['start_date']) || !empty($program['end_date'])): ?>
                                                <div class="d-flex align-items-center justify-content-center mb-3">
                                                    <i class="ri-calendar-event-line text-primary me-2"></i>
                                                    <small class="text-muted">
                                                        <?php if (!empty($program['start_date']) && !empty($program['end_date'])): ?>
                                                            <?= date('M d, Y', strtotime($program['start_date'])) ?> - <?= date('M d, Y', strtotime($program['end_date'])) ?>
                                                        <?php elseif (!empty($program['start_date'])): ?>
                                                            From <?= date('M d, Y', strtotime($program['start_date'])) ?>
                                                        <?php elseif (!empty($program['end_date'])): ?>
                                                            Until <?= date('M d, Y', strtotime($program['end_date'])) ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div> <?php endif; ?>

                                            <div class="text-center mt-auto">
                                                <?php if (!empty($program['web_url'])): ?>
                                                    <a href="<?= esc($program['web_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="ri-external-link-line me-1"></i> Visit Website
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="ri-information-line me-2"></i> No additional programs available at this time.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?= $this->include('landing/common/contact-widget') ?>

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

    /* Other Programs Styles */
    .other-program-card {
        transition: all 0.3s ease;
    }

    .other-program-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .other-program-card .card-img-top {
        transition: all 0.5s ease;
    }

    .other-program-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
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