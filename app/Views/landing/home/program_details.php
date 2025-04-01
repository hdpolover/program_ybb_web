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

        <!-- Introduction with photo -->
        <div class="row align-items-center mb-5 pb-lg-5 border-bottom">
            <div class="col-lg-6 order-lg-2">
                <div class="position-relative mb-4 mb-lg-0">
                    <?php if (isset($photos[0])): ?>
                        <img src="<?= compress_image($photos[0]['img_url'], 600, 400, 80, true); ?>" alt="" class="img-fluid rounded-4 shadow-lg">
                        <?php if (isset($photos[1])): ?>
                            <img src="<?= compress_image($photos[1]['img_url'], 300, 200, 80, true); ?>" alt="" class="img-fluid rounded-4 shadow-lg position-absolute" style="bottom: -25%; right: -10%; max-width: 50%; border: 5px solid #fff;">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="/assets/images/default-program.jpg" alt="" class="img-fluid rounded-4 shadow-lg">
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

                            <?php
                            // Display an image if available (using 4th image from array if exists)
                            if (isset($photos[3])):
                            ?>
                                <div class="mt-4">
                                    <img src="<?= compress_image($photos[3]['img_url'], 400, 200, 80, true); ?>" alt="Goals" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
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

                            <?php
                            // Display an image if available (using 5th image from array if exists)
                            if (isset($photos[4])):
                            ?>
                                <div class="mt-4">
                                    <img src="<?= compress_image($photos[4]['img_url'], 400, 200, 80, true); ?>" alt="Agenda" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
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

                            <?php
                            // Display an image if available (using 3rd image from array if exists)
                            if (isset($photos[2])):
                            ?>
                                <div class="mt-4">
                                    <img src="<?= compress_image($photos[2]['img_url'], 400, 200, 80, true); ?>" alt="Benefits" class="img-fluid rounded-3 w-100" style="height: 140px; object-fit: cover;">
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