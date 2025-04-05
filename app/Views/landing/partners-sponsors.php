<?= $this->include('partials/main') ?>

<head>

    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', [
        'meta_title' => "Program Insights - ",
    ]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>


        <!-- start hero section -->
        <section class="section bg-light">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Partners & Sponsors</h1>
                            <p class="text-muted fs-16">Meet the organizations and individuals who support and collaborate with us to make our mission possible.</p>
                        </div>
                    </div>
                </div>

                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end hero section -->

        <!-- start canva embed section -->
        <section class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                            <div class="card-body p-0">
                                <?php if (!empty($category['sponsor_url'])): ?>
                                <div class="ratio ratio-16x9" style="min-height: 600px;">
                                    <iframe loading="lazy" style="border: none; padding: 0; margin: 0;"
                                        src="<?= $category['sponsor_url'] ?>"
                                        allowfullscreen="allowfullscreen" allow="fullscreen">
                                    </iframe>
                                </div>
                                <?php else: ?>
                                <div class="p-5 text-center">
                                    <div class="avatar-lg mx-auto mb-4">
                                        <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                                            <i class="ri-information-line"></i>
                                        </div>
                                    </div>
                                    <h5>No content available</h5>
                                    <p class="text-muted">Sponsor information will appear here when available.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end canva embed section -->


        <!-- start Partners & Sponsors section -->
        <section class="section py-5 position-relative bg-light" id="partners-sponsors">
            <div class="bg-overlay bg-overlay-pattern opacity-25"></div>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-primary border-0 rounded-4 overflow-hidden shadow-lg">
                            <div class="card-body p-4 position-relative">
                                <div class="bg-overlay bg-overlay-pattern opacity-20"></div>
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <h3 class="text-white fw-bold mb-3">Join Our Community of Partners</h3>
                                        <p class="text-white mb-lg-0">We collaborate with forward-thinking organizations 
                                            and passionate individuals who share our vision. Discover the meaningful 
                                            impact you can make as a partner or sponsor today.</p>
                                    </div>
                                    <div class="col-lg-4 text-lg-end">
                                        <a href="javascript:void(0);" class="btn btn-light shadow-lg fw-semibold">
                                            Contact Us <i class="ri-arrow-right-line align-bottom ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Partners & Sponsors section -->

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sponsor filtering
        const filterBtns = document.querySelectorAll('.sponsor-filter-btn');
        const sponsorItems = document.querySelectorAll('.sponsor-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(innerBtn => {
                    innerBtn.classList.remove('btn-primary');
                    innerBtn.classList.add('btn-outline-primary');
                });

                // Add active class to clicked button
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');

                const filter = this.getAttribute('data-filter');

                // Show/hide sponsor items based on filter
                sponsorItems.forEach(item => {
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
        
        .card-animate {
            transition: all 0.3s ease;
        }
        
        .card-animate:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    `;
        document.head.appendChild(style);
    });
</script>