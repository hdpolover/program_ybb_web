<?= $this->include('partials/main') ?>

<head>

    <?= $this->include('partials/title-meta', ['meta_title' => "Help & News"]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Help & News title section -->
        <section class="section position-relative pb-5 bg-light" id="help-news-title">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                <div class="text-center pt-5 mt-5">
                    <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Help & News</h1>
                    <p class="text-muted fs-16">Access helpful resources and stay updated with the latest news about our programs.</p>
                </div>
                </div>
            </div>
            </div>
        </section>
        <!-- end Help & News title section -->

        <!-- start Help & News section -->
        <section class="section py-5 position-relative bg-light" id="help-news">

            <div class="container">

                <!-- Tabs for Help and News -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header bg-transparent border-bottom border-bottom-dashed">
                                <ul class="nav nav-tabs-custom nav-success nav-justified mb-0" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#faqs-tab" role="tab">
                                            <i class="ri-question-line me-1 align-bottom"></i> FAQs
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#news-tab" role="tab">
                                            <i class="ri-megaphone-line me-1 align-bottom"></i> Announcements
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body p-4">
                                <div class="tab-content">
                                    <!-- FAQs Tab -->
                                    <div class="tab-pane active" id="faqs-tab" role="tabpanel">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-soft-success text-success rounded-circle fs-4">
                                                        <i class="ri-questionnaire-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="card-title mb-1">Frequently Asked Questions</h5>
                                                <p class="text-muted mb-0">Find answers to common questions about our programs</p>
                                            </div>
                                        </div>

                                        <div class="accordion custom-accordionwithicon accordion-border-box" id="accordionFAQs">
                                            <?php if (isset($faqs) && !empty($faqs)) : ?>
                                                <?php foreach ($faqs as $index => $faq) : ?>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading<?= $index ?>">
                                                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $index ?>">
                                                                <?= $faq['question'] ?? 'Question' ?>
                                                            </button>
                                                        </h2>
                                                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $index ?>" data-bs-parent="#accordionFAQs">
                                                            <div class="accordion-body">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="ri-information-line text-primary fs-15 align-middle"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-2">
                                                                        <?= $faq['answer'] ?? 'Answer' ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <div class="text-center p-4">
                                                    <div class="avatar-lg mx-auto mb-4">
                                                        <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                                            <i class="ri-information-line"></i>
                                                        </div>
                                                    </div>
                                                    <h5>No FAQs available</h5>
                                                    <p class="text-muted">Check back later for frequently asked questions about our programs.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Announcements Tab -->
                                    <div class="tab-pane" id="news-tab" role="tabpanel">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-soft-success text-success rounded-circle fs-4">
                                                        <i class="ri-volume-up-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="card-title mb-1">Recent Announcements</h5>
                                                <p class="text-muted mb-0">Stay updated with the latest news about our programs</p>
                                            </div>
                                        </div>

                                        <?php if (isset($news) && !empty($news)) : ?>
                                            <div class="row">
                                                <?php foreach ($news as $item) : ?>
                                                    <div class="col-lg-6 mb-4">
                                                        <div class="card border card-animate h-100">
                                                            <div class="card-body">
                                                                <div class="ribbon ribbon-info ribbon-shape trending-ribbon">
                                                                    <span class="trending-ribbon-text">New</span> <i class="ri-flashlight-fill text-white align-bottom"></i>
                                                                </div>
                                                                <div class="d-flex align-items-center mb-3">
                                                                    <div class="flex-shrink-0">
                                                                        <div class="avatar-sm">
                                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-18">
                                                                                <i class="ri-item-line"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h5 class="fs-16 mb-1"><?= $item['title'] ?? 'Announcement Title' ?></h5>
                                                                        <?php if (isset($item['published_date'])) : ?>
                                                                            <p class="text-muted mb-0">
                                                                                <i class="ri-calendar-event-line me-1 align-bottom"></i>
                                                                                <?= date('M d, Y', strtotime($item['published_date'])) ?>
                                                                            </p>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                                <p class="text-muted mb-3"><?= $item['short_description'] ?? substr($item['description'] ?? 'Announcement Description', 0, 150) . '...' ?></p>
                                                                <div>
                                                                    <a href="javascript:void(0);" class="link-primary">Read More <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="text-center p-4">
                                                <div class="avatar-lg mx-auto mb-4">
                                                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                                        <i class="ri-megaphone-line"></i>
                                                    </div>
                                                </div>
                                                <h5>No news available</h5>
                                                <p class="text-muted">Check back later for news and news about our programs.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?= $this->include('landing/common/contact-widget') ?>

                
            </div>
        </section>
        <!-- end Help & News section -->

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