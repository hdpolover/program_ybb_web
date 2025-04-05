<?= $this->include('partials/main') ?>

<head>

    <?php echo view(
        'partials/title-meta',
        array(
            'meta_title' => "Partners & Sponsors | " . $webSettings['name'],
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


        <!-- start hero section -->
        <section class="section bg-light">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="text-center mt-lg-5">
                            <h1 class="display-4 fw-bold mb-4 lh-base"><span class="text-success"><?= $title; ?></span>
                            </h1>
                            <p class="lead text-muted mb-4 lh-base">Discover detailed answers to common questions about
                                our sponsorship program.</p>
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
                        <div class="embed-responsive embed-responsive-16by9" style="height: 100vh;">
                            <?= $webSettings['sponsor_canva_url']; ?>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
        </section>
        <!-- end canva embed section -->


        <!-- start Partners & Sponsors section -->
        <section class="section py-5 position-relative bg-light" id="partners-sponsors">
            <div class="bg-overlay bg-overlay-pattern opacity-25"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Partners & Sponsors</h1>
                            <p class="text-muted mb-4">Meet the organizations who make our programs possible through
                                their generous support and collaboration.</p>
                        </div>
                    </div>
                </div>

                <!-- Partners Section with Tabs -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-lg border-0 rounded-4 mb-5">
                            <div class="card-header border-bottom-dashed">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                                <i class="ri-team-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-1">Our Partners</h5>
                                        <p class="text-muted mb-0">Organizations working with us to achieve our mission
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <?php if (isset($partners) && !empty($partners)) : ?>
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                                        <?php foreach ($partners as $partner) : ?>
                                            <div class="col">
                                                <div class="card card-animate border h-100">
                                                    <div class="card-body text-center">
                                                        <div class="mx-auto mb-4"
                                                            style="height: 120px; display: flex; align-items: center; justify-content: center;">
                                                            <?php if (isset($partner['logo_url']) && !empty($partner['logo_url'])) : ?>
                                                                <img src="<?= function_exists('compress_image') ? compress_image($partner['logo_url'], 200, 120, 80, true) : $partner['logo_url'] ?>"
                                                                    alt="<?= $partner['name'] ?? 'Partner' ?> Logo"
                                                                    class="img-fluid" style="max-height: 120px;">
                                                            <?php else : ?>
                                                                <div class="avatar-lg">
                                                                    <div class="avatar-title bg-light text-primary fs-24 rounded">
                                                                        <i class="ri-building-line"></i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <h5 class="mb-1"><?= $partner['name'] ?? 'Partner Name' ?></h5>
                                                        <p class="text-muted mb-3">
                                                            <?= $partner['description'] ?? 'Partner Description' ?></p>
                                                        <?php if (isset($partner['website']) && !empty($partner['website'])) : ?>
                                                            <a href="<?= $partner['website'] ?>" target="_blank"
                                                                class="btn btn-soft-primary btn-sm">
                                                                <i class="ri-links-line me-1 align-middle"></i> Visit Website
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="text-center p-4">
                                        <div class="avatar-lg mx-auto mb-4">
                                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                                <i class="ri-information-line"></i>
                                            </div>
                                        </div>
                                        <h5>No partners available at the moment.</h5>
                                        <p class="text-muted">Please check back later for information about our partners.
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sponsor Levels -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header border-bottom-dashed">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-soft-warning text-warning rounded-circle fs-4">
                                                <i class="ri-award-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-1">Our Sponsors</h5>
                                        <p class="text-muted mb-0">Organizations providing financial support to our
                                            programs</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <!-- Sponsor Level Buttons -->
                                <?php
                                $sponsorLevels = ['All', 'Platinum', 'Gold', 'Silver', 'Bronze'];
                                ?>
                                <div class="d-flex flex-wrap gap-2 mb-4 sponsor-filter">
                                    <?php foreach ($sponsorLevels as $index => $level) : ?>
                                        <button
                                            class="btn <?= $index === 0 ? 'btn-primary' : 'btn-outline-primary' ?> sponsor-filter-btn"
                                            data-filter="<?= strtolower($level) ?>">
                                            <?php if ($level !== 'All') : ?>
                                                <i class="ri-award-fill me-1 align-bottom"></i>
                                            <?php endif; ?>
                                            <?= $level ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (isset($sponsors) && !empty($sponsors)) : ?>
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 sponsor-items">
                                        <?php foreach ($sponsors as $sponsor) :
                                            $sponsorLevel = isset($sponsor['contribution_level']) ? strtolower($sponsor['contribution_level']) : 'default';
                                        ?>
                                            <div class="col sponsor-item <?= $sponsorLevel ?>">
                                                <div class="card card-animate border h-100">
                                                    <?php if (isset($sponsor['contribution_level'])) :
                                                        $badgeClass = '';
                                                        switch (strtolower($sponsor['contribution_level'])) {
                                                            case 'platinum':
                                                                $badgeClass = 'bg-gradient-primary';
                                                                break;
                                                            case 'gold':
                                                                $badgeClass = 'bg-gradient-warning';
                                                                break;
                                                            case 'silver':
                                                                $badgeClass = 'bg-gradient-info';
                                                                break;
                                                            case 'bronze':
                                                                $badgeClass = 'bg-gradient-danger';
                                                                break;
                                                            default:
                                                                $badgeClass = 'bg-gradient-success';
                                                        }
                                                    ?>
                                                        <div class="ribbon-box">
                                                            <div class="ribbon ribbon-shape <?= $badgeClass ?>">
                                                                <span
                                                                    class="text-white text-uppercase"><?= $sponsor['contribution_level'] ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="card-body text-center p-4">
                                                        <div class="mx-auto mb-4"
                                                            style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                                            <?php if (isset($sponsor['logo_url']) && !empty($sponsor['logo_url'])) : ?>
                                                                <img src="<?= function_exists('compress_image') ? compress_image($sponsor['logo_url'], 180, 100, 80, true) : $sponsor['logo_url'] ?>"
                                                                    alt="<?= $sponsor['name'] ?? 'Sponsor' ?> Logo"
                                                                    class="img-fluid" style="max-height: 100px;">
                                                            <?php else : ?>
                                                                <div class="avatar-lg">
                                                                    <div class="avatar-title bg-light text-primary fs-24 rounded">
                                                                        <i class="ri-award-line"></i>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <h5 class="mb-1"><?= $sponsor['name'] ?? 'Sponsor Name' ?></h5>
                                                        <p class="text-muted mb-3">
                                                            <?= $sponsor['description'] ?? 'Sponsor Description' ?></p>
                                                        <?php if (isset($sponsor['website']) && !empty($sponsor['website'])) : ?>
                                                            <a href="<?= $sponsor['website'] ?>" target="_blank"
                                                                class="btn btn-soft-primary btn-sm">
                                                                <i class="ri-links-line me-1 align-middle"></i> Visit Website
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="text-center p-4">
                                        <div class="avatar-lg mx-auto mb-4">
                                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                                <i class="ri-information-line"></i>
                                            </div>
                                        </div>
                                        <h5>No sponsors available at the moment.</h5>
                                        <p class="text-muted">Please check back later for information about our sponsors.
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Become a Partner/Sponsor Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-primary border-0 rounded-4 overflow-hidden shadow-lg">
                            <div class="card-body p-4 position-relative">
                                <div class="bg-overlay bg-overlay-pattern opacity-20"></div>
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <h3 class="text-white mb-3">Interested in partnering with us?</h3>
                                        <p class="text-white-75 mb-lg-0">We are always looking for organizations and
                                            individuals who share our mission and want to make a positive impact. Learn
                                            how you can become a partner or sponsor.</p>
                                    </div>
                                    <div class="col-lg-4 text-lg-end">
                                        <a href="javascript:void(0);" class="btn btn-light shadow-lg">Contact Us <i
                                                class="ri-arrow-right-line align-bottom ms-1"></i></a>
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