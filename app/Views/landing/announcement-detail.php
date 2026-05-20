<?php
$announcement = $announcement ?? [];
$title = trim((string)($announcement['title'] ?? 'Announcement'));
$imageUrl = trim((string)($announcement['img_url'] ?? ''));
$metaTitle = trim((string)($announcement['meta_title'] ?? $title));
$tagsValue = trim((string)($announcement['tags'] ?? ''));
$plainContent = trim(strip_tags((string)($announcement['content'] ?? '')));
$metaDescription = trim((string)($announcement['meta_description'] ?? ''));
$description = $metaDescription !== '' ? $metaDescription : ($plainContent !== '' ? mb_strimwidth($plainContent, 0, 180, '...') : 'Stay updated with our latest news, events, and important information.');
$categoryName = is_array($category ?? null) ? trim((string)($category['name'] ?? '')) : '';
$tags = array_values(array_filter(array_map('trim', explode(',', $tagsValue))));
$hasImage = $imageUrl !== '';
?>
<?= $this->include('partials/main') ?>

<head>
    <?php echo view('partials/title-meta', array(
        'img_url' => $imageUrl,
        'title' => $title,
        'meta_title' => $metaTitle,
        'meta_description' => $description,
        'tags' => $tagsValue,
        'slug' => $announcement['slug'] ?? ''
    )); ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>
    <style>
        .announcement-detail-media {
            min-height: 320px;
            background: linear-gradient(135deg, rgba(64, 81, 137, 0.08), rgba(10, 179, 156, 0.12));
        }

        .announcement-detail-media img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .announcement-content {
            color: #495057;
            line-height: 1.8;
        }

        .announcement-content > *:last-child {
            margin-bottom: 0;
        }

        .announcement-content img,
        .announcement-content video,
        .announcement-content iframe {
            max-width: 100%;
            height: auto;
            border-radius: 1rem;
        }

        .announcement-content iframe {
            width: 100%;
            min-height: 360px;
        }

        .announcement-content table {
            display: block;
            width: 100%;
            overflow-x: auto;
        }
    </style>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">

        <?= $this->include('landing/common/navbar') ?>

        <!-- start Announcement title section -->
        <section class="section position-relative pb-5" id="announcement-title" style="background-color: #f8f9fa;">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Announcement</h1>
                            <p class="text-muted fs-16">Stay updated with our latest news, events, and important information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end title section -->

        <!-- start announcement content -->
        <section class="section py-5" id="announcement-detail">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="card-img-top position-relative announcement-detail-media overflow-hidden">
                                <?php if ($hasImage): ?>
                                    <img src="<?= esc($imageUrl); ?>" alt="<?= esc($title); ?>" class="img-fluid mx-auto d-block" />
                                <?php else: ?>
                                    <div class="h-100 d-flex align-items-center justify-content-center px-4 text-center">
                                        <div>
                                            <div class="avatar-lg mx-auto mb-3">
                                                <div class="avatar-title bg-white text-primary rounded-circle fs-24 shadow-sm">
                                                    <i class="ri-megaphone-line"></i>
                                                </div>
                                            </div>
                                            <h5 class="mb-2">Announcement image unavailable</h5>
                                            <p class="text-muted mb-0">This update is still available below without a banner image.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row g-0">
                                <div class="col-12">
                                    <div class="card-body p-4 p-lg-5">
                                        <a href="<?= base_url('announcements'); ?>" class="btn btn-light btn-sm mb-4">
                                            <i class="ri-arrow-left-line align-bottom me-1"></i> Back to announcements
                                        </a>

                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="badge bg-soft-primary text-primary">Announcement</span>
                                            <?php if ($categoryName !== ''): ?>
                                                <span class="badge bg-light text-body"><?= esc($categoryName); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3 text-muted">
                                            <span><i class="ri-calendar-line fs-16 align-bottom me-1"></i><?= date('F j, Y', strtotime($announcement['created_at'] ?? 'now')); ?></span>
                                            <?php if (!empty($announcement['updated_at'])): ?>
                                                <span><i class="ri-time-line fs-16 align-bottom me-1"></i>Updated <?= date('F j, Y', strtotime($announcement['updated_at'])); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <h2 class="fw-bold mb-3"><?= esc($title); ?></h2>

                                        <?php if ($description !== ''): ?>
                                            <p class="text-muted fs-16 mb-4"><?= esc($description); ?></p>
                                        <?php endif; ?>

                                        <div class="announcement-content fs-15 text-muted">
                                            <?= $announcement['content'] ?? ''; ?>
                                        </div>

                                        <?php if (!empty($tags)): ?>
                                        <div class="mt-4 pt-2">
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($tags as $tag): ?>
                                                    <span class="badge bg-light text-muted fs-13"><?= esc($tag); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end container -->
        </section>
        <!-- end announcement content -->

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
