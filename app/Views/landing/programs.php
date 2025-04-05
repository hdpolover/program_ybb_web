<?= $this->include('partials/main') ?>

<head>

    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', ['meta_title' => "Programs"]) ?>

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
                        <div class="col-12 text-center">
                            <p class="text-muted fs-16">No programs are available at this time. Please check back later.</p>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <!-- <div class="row justify-content-center mt-5 mb-4">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold">Our Other Programs</h2>
                            <p class="text-muted">Explore other programs initiated by Youth Break the Boundaries Foundation</p>
                        </div>
                    </div>
                </div> -->

                <!-- other program grid -->

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