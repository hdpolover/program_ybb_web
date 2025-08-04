<?= $this->include('partials/main') ?>

<head>

    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', [
        'meta_title' => "Gallery",
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
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Photo Gallery</h1>
                            <p class="text-muted fs-16">Explore our visual journey through captivating moments and memorable experiences from our programs and events.</p>
                        </div>
                    </div>
                </div>

                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end hero section -->

        <!-- Start program gallery section -->
        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                // Get photos from the array or use fallback images
                                $gallery_photos = $photos ?? [];
                                $years = [];

                                // If photos exist, organize them by year
                                if (!empty($gallery_photos) && is_array($gallery_photos)) {
                                    $years = array_keys($gallery_photos);
                                    // Make sure "Unknown" is at the end if it exists
                                    if (in_array('Unknown', $years)) {
                                        $unknown_key = array_search('Unknown', $years);
                                        unset($years[$unknown_key]);
                                        $years[] = 'Unknown';
                                    }
                                }

                                // Show tabs only if we have photos
                                if (!empty($years)):
                                ?>

                                    <ul class="nav nav-pills nav-success mb-3" role="tablist">
                                        <?php foreach ($years as $index => $year): ?>
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link <?= ($index === 0) ? 'active' : '' ?>" data-bs-toggle="tab" href="#year-<?= $year ?>" role="tab"><?= $year ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content text-muted">
                                        <?php foreach ($years as $index => $year): ?>
                                            <div class="tab-pane <?= ($index === 0) ? 'active' : '' ?>" id="year-<?= $year ?>" role="tabpanel">
                                                <div class="row g-3">
                                                    <?php
                                                    $year_photos = $gallery_photos[$year] ?? [];

                                                    if (empty($year_photos)): ?>
                                                        <div class="col-12 text-center py-5">
                                                            <div class="text-muted">
                                                                <i class="ri-image-line fs-2 mb-2"></i>
                                                                <p>No photos available for this period.</p>
                                                            </div>
                                                        </div>
                                                        <?php else:
                                                        // Display all photos for the year (no limit)
                                                        foreach ($year_photos as $photo_index => $photo):
                                                            // First 2 photos get larger columns, remaining get smaller columns
                                                            $size_class = ($photo_index < 2) ? 'col-lg-6' : 'col-lg-4 col-md-6';
                                                            $title = !empty($photo['title']) ? $photo['title'] : (($program_info['name'] ?? 'Program') . ' Photo ' . ($photo_index + 1));
                                                            $description = $photo['description'] ?? 'Program photo';
                                                            $img_url = $photo['img_url'] ?? '';

                                                            // Skip if no image URL is available
                                                            if (empty($img_url)) continue;
                                                        ?>
                                                            <div class="<?= $size_class ?>">
                                                                <div class="gallery-box card border-0 overflow-hidden">
                                                                    <div class="gallery-container">
                                                                        <a href="javascript:void(0);" class="gallery-popup"
                                                                            data-title="<?= htmlspecialchars($title) ?>"
                                                                            data-description="<?= htmlspecialchars($description) ?>"
                                                                            data-src="<?= $img_url ?>">
                                                                            <img src="<?= function_exists('compress_image') ? compress_image($img_url, 600, 400, 80, true) : $img_url; ?>"
                                                                                alt="<?= htmlspecialchars($title) ?>"
                                                                                class="img-fluid gallery-img"
                                                                                style="width: 100%; height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                                                                            <div class="gallery-overlay">
                                                                                <div class="overlay-content">
                                                                                    <h5 class="text-white"><?= htmlspecialchars($title) ?></h5>
                                                                                    <i class="ri-search-eye-line text-white fs-24 mt-2"></i>
                                                                                </div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    <?php endforeach;
                                                    endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ri-image-line fs-1 mb-3"></i>
                                            <h5>No Photos Available</h5>
                                            <p>There are no photos available at the moment.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div>
                    <!--end col-->
                </div> <!-- Other Programs Photo Section -->
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="text-center mb-5">
                            <h2 class="mb-3 fw-semibold">Other Programs</h2>
                            <p class="text-muted">Discover photos from our other exciting events and initiatives</p>
                        </div>

                        <!-- Swiper -->
                        <div class="swiper mySwiper pb-4">
                            <div class="swiper-wrapper">
                                <?php
                                // Get other program photos
                                $otherPrograms = $otherProgramPhotos ?? [];

                                if (!empty($otherPrograms) && is_array($otherPrograms)):
                                    foreach ($otherPrograms as $program):
                                        $programName = $program['name'] ?? 'Program';
                                        $website = $program['web_url'] ?? '';
                                        $programPhotos = $program['photos'] ?? [];

                                        // Get up to 4 photos for each program
                                        $displayPhotos = array_slice($programPhotos, 0, 4);

                                        if (!empty($displayPhotos)):
                                ?>
                                            <div class="swiper-slide" style="height: auto;">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="row g-1 mb-3" style="height: 300px; overflow: hidden;">
                                                            <?php if (count($displayPhotos) === 1): ?>
                                                                <!-- Single photo layout -->
                                                                <div class="col-12 h-100">
                                                                    <img src="<?= $displayPhotos[0]['img_url'] ?>" alt="" class="img-fluid rounded h-100 w-100" style="object-fit: cover;">
                                                                </div>
                                                            <?php elseif (count($displayPhotos) === 2): ?>
                                                                <!-- Two photos layout -->
                                                                <div class="col-6 h-100">
                                                                    <img src="<?= $displayPhotos[0]['img_url'] ?>" alt="" class="img-fluid rounded h-100 w-100" style="object-fit: cover;">
                                                                </div>
                                                                <div class="col-6 h-100">
                                                                    <img src="<?= $displayPhotos[1]['img_url'] ?>" alt="" class="img-fluid rounded h-100 w-100" style="object-fit: cover;">
                                                                </div>
                                                            <?php elseif (count($displayPhotos) === 3): ?>
                                                                <!-- Three photos layout -->
                                                                <div class="col-6 d-flex flex-column h-100">
                                                                    <img src="<?= $displayPhotos[0]['img_url'] ?>" alt="" class="img-fluid rounded mb-1 flex-grow-1 w-100" style="object-fit: cover; height: calc(50% - 2px);">
                                                                    <img src="<?= $displayPhotos[1]['img_url'] ?>" alt="" class="img-fluid rounded flex-grow-1 w-100" style="object-fit: cover; height: calc(50% - 2px);">
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-6 h-100">
                                                                    <img src="<?= $displayPhotos[2]['img_url'] ?>" alt="" class="img-fluid rounded h-100 w-100" style="object-fit: cover;">
                                                                </div>
                                                                <!--end col-->
                                                            <?php else: ?>
                                                                <!-- Four photos layout -->
                                                                <div class="col-6 d-flex flex-column h-100">
                                                                    <img src="<?= $displayPhotos[0]['img_url'] ?>" alt="" class="img-fluid rounded mb-1 flex-grow-1 w-100" style="object-fit: cover; height: calc(50% - 2px);">
                                                                    <img src="<?= $displayPhotos[1]['img_url'] ?>" alt="" class="img-fluid rounded flex-grow-1 w-100" style="object-fit: cover; height: calc(50% - 2px);">
                                                                </div>
                                                                <!--end col-->
                                                                <div class="col-6 d-flex flex-column h-100">
                                                                    <img src="<?= $displayPhotos[2]['img_url'] ?>" alt="" class="img-fluid rounded mb-1 flex-grow-1 w-100" style="object-fit: cover; height: calc(50% - 2px);">
                                                                    <img src="<?= $displayPhotos[3]['img_url'] ?>" alt="" class="img-fluid rounded flex-grow-1 w-100" style="object-fit: cover; height: calc(50% - 2px);">
                                                                </div>
                                                                <!--end col-->
                                                            <?php endif; ?>
                                                        </div>
                                                        <!--end row-->
                                                        <h5 class="mb-0 fs-16">
                                                            <center>
                                                                <?= htmlspecialchars($programName) ?>
                                                                <?php if (!empty($website)): ?>
                                                                    <a href="https://<?= $website ?>" target="_blank"
                                                                        class="btn btn-sm btn-outline-primary mt-3">
                                                                        <i class="ri-external-link-line me-1"></i> Visit Website
                                                                    </a>
                                                                <?php endif; ?>
                                                            </center>
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                else:
                                    ?> <div class="swiper-slide" style="height: auto;">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="text-center py-5" style="height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                    <div class="text-muted">
                                                        <i class="ri-image-line fs-1 mb-3"></i>
                                                        <h5>No Other Programs Available</h5>
                                                        <p>There are no other program photos available at the moment.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="swiper-pagination swiper-pagination-dark"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- End program gallery section -->

        <!-- Include the gallery modal -->
        <?= $this->include('partials/gallery-modal') ?>

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

    <!-- Gallery modal initialization -->
    <script src="/assets/js/pages/gallery-modal.init.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>
    <script src="/assets/js/pages/nft-landing.init.js"></script>

</body>

</html>