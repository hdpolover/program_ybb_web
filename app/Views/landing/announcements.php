<?php
$pageCategoryName = is_array($category ?? null) ? trim((string) ($category['name'] ?? '')) : '';
?>
<?= $this->include('partials/main') ?>

<head>

    <?= $this->include('partials/title-meta', ['meta_title' => "Announcements"]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>
    <style>
        .announcement-grid-media {
            height: 220px;
        }

        .announcement-grid-placeholder {
            background: linear-gradient(135deg, rgba(64, 81, 137, 0.08), rgba(10, 179, 156, 0.12));
        }

        .announcement-grid-summary {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            overflow: hidden;
            min-height: 4.5rem;
        }
    </style>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Announcements title section -->
        <section class="section position-relative pb-5 bg-light" id="announcements-title">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Announcements</h1>
                            <p class="text-muted fs-16">Stay updated with the latest news about our programs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Announcements title section -->

        <!-- start Announcements section -->
        <section class="section mt-5 py-5 position-relative bg-light" id="announcements">
            <div class="container">

                <div class="row">
                    <?php if (isset($announcements) && !empty($announcements)) : ?>
                        <?php foreach ($announcements as $item) : ?>
                            <?php
                            $title = trim((string)($item['title'] ?? 'Announcement'));
                            $imageUrl = trim((string)($item['img_url'] ?? ''));
                            $identifier = (string)($item['slug'] ?? $item['id'] ?? '');
                            $detailUrl = $identifier !== '' ? base_url('announcements/' . rawurlencode($identifier)) : base_url('announcements');
                            $summarySource = trim((string)($item['meta_description'] ?? ''));
                            if ($summarySource === '') {
                                $summarySource = trim(strip_tags((string)($item['content'] ?? '')));
                            }
                            $summary = $summarySource !== '' ? mb_strimwidth($summarySource, 0, 150, '...') : 'Stay tuned for the latest update from our program.';
                            $badgeLabel = trim((string)($item['category'] ?? $pageCategoryName ?: 'Announcement'));
                            ?>
                            <div class="col-xxl-3 col-lg-4 col-md-6 mb-4">
                                <div class="card border-0 shadow-sm overflow-hidden blog-grid-card h-100">
                                    <?php if ($imageUrl !== ''): ?>
                                        <div class="position-relative overflow-hidden announcement-grid-media">
                                            <img src="<?= esc($imageUrl) ?>" alt="<?= esc($title) ?>" class="blog-img object-fit-cover w-100 h-100">
                                            <?php if (isset($item['is_new']) && $item['is_new']): ?>
                                                <div class="badge bg-danger position-absolute top-0 end-0 m-2">New</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="announcement-grid-media announcement-grid-placeholder d-flex align-items-center justify-content-center px-4 text-center">
                                            <div>
                                                <div class="avatar-md mx-auto mb-3">
                                                    <div class="avatar-title bg-white text-primary rounded-circle fs-24 shadow-sm">
                                                        <i class="ri-megaphone-line"></i>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-0">Announcement image will appear here when available.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <div class="badge bg-soft-primary text-primary mb-2"><?= esc($badgeLabel) ?></div>
                                        <h5 class="card-title mb-2">
                                            <a href="<?= esc($detailUrl) ?>" class="text-reset"><?= esc($title) ?></a>
                                        </h5>
                                        <p class="text-muted mb-3 announcement-grid-summary"><?= esc($summary) ?></p>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <small class="text-muted">
                                                    <i class="ri-calendar-line align-bottom me-1"></i>
                                                    <?= date('M d, Y', strtotime($item['created_at'] ?? 'now')) ?>
                                                </small>
                                            </div>
                                            <div>
                                                <a href="<?= esc($detailUrl) ?>" class="link link-primary text-decoration-underline">Read More <i class="ri-arrow-right-line align-bottom"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col-12">
                            <div class="text-center p-4">
                                <div class="avatar-lg mx-auto mb-4">
                                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                        <i class="ri-megaphone-line"></i>
                                    </div>
                                </div>
                                <h5>No announcements available</h5>
                                <p class="text-muted">Check back later for announcements about our programs.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($total) && $total > 0) : ?>
                    <?php
                    $start = (($currentPage - 1) * $perPage) + 1;
                    $end = min($currentPage * $perPage, $total);
                    ?>
                    <div class="row g-0 text-center text-sm-start align-items-center mb-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0 text-muted">Showing <span class="fw-semibold"><?= $start ?></span> to <span class="fw-semibold"><?= $end ?></span> of <span class="fw-semibold text-decoration-underline"><?= $total ?></span> entries</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <ul class="pagination pagination-separated justify-content-center justify-content-sm-end mb-sm-0">
                                <?php
                                // Build the query string for pagination links
                                $queryParams = [];
                                if (!empty($search)) $queryParams['search'] = $search;
                                if (!empty($selectedCategory)) $queryParams['category'] = $selectedCategory;
                                if (!empty($selectedSort)) $queryParams['sort'] = $selectedSort;

                                // Helper function to build pagination URLs
                                $buildUrl = function ($page) use ($queryParams) {
                                    $queryParams['page'] = $page;
                                    return base_url('announcements') . '?' . http_build_query($queryParams);
                                };
                                ?>

                                <!-- Previous Button -->
                                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                    <a href="<?= ($currentPage > 1) ? $buildUrl($currentPage - 1) : '#' ?>" class="page-link">Previous</a>
                                </li>

                                <!-- Page Numbers -->
                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) : ?>
                                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                        <a href="<?= $buildUrl($i) ?>" class="page-link"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next Button -->
                                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                    <a href="<?= ($currentPage < $totalPages) ? $buildUrl($currentPage + 1) : '#' ?>" class="page-link">Next</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row mt-5">
                    <?= $this->include('landing/common/contact-widget') ?>
                </div>
            </div>
        </section>
        <!-- end Announcements section -->

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
