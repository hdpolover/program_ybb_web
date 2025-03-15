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
            'slug' => $title
        )
    ); ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('ybb/common/navbar') ?>

        <!-- start hero section -->
        <section class="section bg-light">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="text-center mt-lg-5">
                            <h1 class="display-4 fw-bold mb-4 lh-base"><span class="text-success"><?= $title; ?></span></h1>
                            <p class="lead text-muted mb-4 lh-base">Find answers to frequently asked questions about us. We've compiled comprehensive responses to help you better understand details about our program.</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end hero section -->

        <?php
        // Group FAQs by category
        $groupedFaqs = [];
        foreach ($faqs as $faq) {
            $category = $faq['faq_category'];
            if (!isset($groupedFaqs[$category])) {
                $groupedFaqs[$category] = [];
            }
            $groupedFaqs[$category][] = $faq;
        }

        // Sort categories alphabetically
        ksort($groupedFaqs);
        ?>

        <!-- start faqs -->
        <section class="section">
            <div class="container">
                <div class="row g-lg-5 g-4">
                    <div class="col-lg-12">
                        <?php foreach ($groupedFaqs as $category => $categoryFaqs): ?>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0 me-1">
                                    <i class="ri-question-line fs-24 align-middle text-success me-1"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 fw-semibold"><?= ucwords(str_replace('_', ' ', esc($category))) ?></h5>
                                </div>
                            </div>
                            <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box mb-3" id="accordion-<?= url_title($category, '-', true) ?>">
                                <?php foreach ($categoryFaqs as $index => $faq): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-<?= url_title($category, '-', true) ?>-<?= $index ?>">
                                            <button class="accordion-button <?= ($index === 0) ? '' : 'collapsed' ?>" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-<?= url_title($category, '-', true) ?>-<?= $index ?>"
                                                aria-expanded="<?= ($index === 0) ? 'true' : 'false' ?>"
                                                aria-controls="collapse-<?= url_title($category, '-', true) ?>-<?= $index ?>">
                                                <?= esc($faq['question']) ?>
                                            </button>
                                        </h2>
                                        <div id="collapse-<?= url_title($category, '-', true) ?>-<?= $index ?>"
                                            class="accordion-collapse collapse <?= ($index === 0) ? 'show' : '' ?>"
                                            aria-labelledby="heading-<?= url_title($category, '-', true) ?>-<?= $index ?>"
                                            data-bs-parent="#accordion-<?= url_title($category, '-', true) ?>">
                                            <div class="accordion-body ff-secondary">
                                                <?= esc($faq['answer']) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!--end accordion-->
                        <?php endforeach; ?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end faqs -->

        <?= $this->include('ybb/common/footer') ?>

    </div>
    <!-- end layout wrapper -->


    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
</body>

</html>