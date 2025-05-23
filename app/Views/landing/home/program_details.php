<!-- Start program details section with enhanced UI -->
<section class="section py-5 bg-light" id="program-details">
    <div class="bg-overlay bg-overlay-pattern opacity-50"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold">About Our Program</h2>
                    <p class="text-muted">Learn about our mission, vision, and goals</p>
                </div>
            </div>
        </div>

        <?php
        // Function to extract a random photo from any year
        function getRandomPhoto($photos, $index = 0) {
            if (empty($photos) || !is_array($photos)) {
                return null;
            }
            
            // Flatten the photos array by concatenating all years' photos
            $all_photos = [];
            foreach ($photos as $year_photos) {
                if (is_array($year_photos)) {
                    $all_photos = array_merge($all_photos, $year_photos);
                }
            }
            
            // Shuffle to randomize
            shuffle($all_photos);
            
            // Return the photo at the specified index or null if not enough photos
            return isset($all_photos[$index]) ? $all_photos[$index] : null;
        }
        
        // Get needed photos
        $photo1 = getRandomPhoto($photos, 0);
        $photo2 = getRandomPhoto($photos, 1);
        $photo3 = getRandomPhoto($photos, 2);
        $photo4 = getRandomPhoto($photos, 3);
        $photo5 = getRandomPhoto($photos, 4);
        ?>

        <!-- Introduction with photo -->
        <div class="row align-items-center mb-5 pb-lg-5 border-bottom">
            <div class="col-lg-6 order-lg-2">
                <div class="position-relative mb-4 mb-lg-0">
                    <?php if ($photo1): ?>
                        <img src="<?= compress_image($photo1['img_url'], 600, 400, 80, true); ?>" alt="<?= htmlspecialchars($photo1['title'] ?? 'Program image') ?>" class="img-fluid rounded-4 shadow-lg">
                        <?php if ($photo2): ?>
                            <img src="<?= compress_image($photo2['img_url'], 300, 200, 80, true); ?>" alt="<?= htmlspecialchars($photo2['title'] ?? 'Program image') ?>" class="img-fluid rounded-4 shadow-lg position-absolute" style="bottom: -25%; right: -10%; max-width: 50%; border: 5px solid #fff;">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="/assets/images/default-program.jpg" alt="Default program image" class="img-fluid rounded-4 shadow-lg">
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="mt-4 mt-lg-0">
                    <p class="text-muted fs-16"><?= $category['about'] ?? 'Program description not available'; ?></p>
                </div>
            </div>
        </div>

        <!-- Benefits, Goals, and Agenda in cards with icons -->
        <div class="row g-4 mt-4">

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="mx-auto mb-4" style="width: 70px; height: 70px;">
                                <div class="avatar-title bg-warning-subtle text-warning rounded-circle fs-2 h-100 d-flex align-items-center justify-content-center">
                                    <i class="ri-focus-2-line"></i>
                                </div>
                            </div>
                            <h4 class="fs-18 mb-3">Program Values</h4>
                        </div>
                        <div class="position-relative">
                            <div class="p-2 px-3 rounded-3 mb-3">
                                <p class="text-muted mb-0"><?= $category['core_values'] ?? 'Goals information not available'; ?></p>
                            </div>

                            <?php if ($photo4): ?>
                                <div class="mt-4">
                                    <img src="<?= compress_image($photo4['img_url'], 400, 200, 80, true); ?>" alt="<?= htmlspecialchars($photo4['title'] ?? 'Goals') ?>" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="mx-auto mb-4" style="width: 70px; height: 70px;">
                                <div class="avatar-title bg-success-subtle text-success rounded-circle fs-2 h-100 d-flex align-items-center justify-content-center">
                                    <i class="ri-calendar-todo-line"></i>
                                </div>
                            </div>
                            <h4 class="fs-18 mb-3">Program Objectives</h4>
                        </div>
                        <div class="position-relative">
                            <div class="p-2 px-3 rounded-3 mb-3">
                                <p class="text-muted mb-0"><?= $category['objectives'] ?? 'Agenda information not available'; ?></p>
                            </div>

                            <?php if ($photo5): ?>
                                <div class="mt-4">
                                    <img src="<?= compress_image($photo5['img_url'], 400, 200, 80, true); ?>" alt="<?= htmlspecialchars($photo5['title'] ?? 'Agenda') ?>" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="mx-auto mb-4" style="width: 70px; height: 70px;">
                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-2 h-100 d-flex align-items-center justify-content-center">
                                    <i class="ri-gift-line"></i>
                                </div>
                            </div>
                            <h4 class="fs-18 mb-3">Benefits & Opportunities</h4>
                        </div>
                        <div class="position-relative">
                            <div class="p-2 px-3 rounded-3 mb-3">
                                <p class="text-muted mb-0"><?= $category['benefits'] ?? 'Information not available'; ?></p>
                            </div>

                            <?php if ($photo3): ?>
                                <div class="mt-4">
                                    <img src="<?= compress_image($photo3['img_url'], 400, 200, 80, true); ?>" alt="<?= htmlspecialchars($photo3['title'] ?? 'Benefits') ?>" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End enhanced program details section -->