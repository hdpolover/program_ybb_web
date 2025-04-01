<!-- Start current programs section -->
<section class="section" id="current-programs">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold"><?= $category['name'] . ' Programs ' ?? 'Programs' ?></h2>
                    <p class="text-muted">Explore our active programs and initiatives</p>
                </div>
            </div>
        </div>

        <!-- Programs horizontal list -->
        <?php
        // Filter for upcoming programs only - programs where end date is in the future
        $upcoming_programs = array_filter($programs ?? [], function ($program) {
            $current_time = time();
            return isset($program['end_date']) && strtotime($program['end_date']) >= $current_time;
        });

        if (!empty($upcoming_programs)):
        ?>
            <div class="row">
                <?php foreach ($upcoming_programs as $index => $program): ?>

                    <div class="col-12 mb-4">
                        <div class="card ribbon-box h-100 border-0 shadow-sm">
                            <div class="row g-0 h-100">
                                <!-- Left column: Program details with ribbons -->
                                <div class="col-md-8 position-relative ribbon-box">

                                    <div class="card-body p-4 mt-5">
                                        <span class="ribbon-three ribbon-three-danger"><span>Upcoming</span></span>

                                        <h2 class="mb-2"><?= $program['name'] ?? 'Program Name' ?></h2>
                                        <p class="card-text text-muted mb-3"><?= substr($program['description'] ?? '', 0, 150) . (strlen($program['description'] ?? '') > 150 ? '...' : '') ?></p>

                                        <!-- Program details with enhanced styling -->
                                        <div class="mt-3 pt-2 border-top">
                                            <div class="row">
                                                <?php if (isset($program['start_date']) && isset($program['end_date'])): ?>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-2">
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                        <i class="ri-calendar-line"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0 fs-13">Program Duration</h6>
                                                                <small class="text-muted">
                                                                    <?= date('M d, Y', strtotime($program['start_date'])) ?> -
                                                                    <?= date('M d, Y', strtotime($program['end_date'])) ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (isset($program['location'])): ?>
                                                    <div class="col-md-6 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-2">
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                        <i class="ri-map-pin-line"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-0 fs-13">Location</h6>
                                                                <small class="text-muted"><?= $program['location'] ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Two buttons: Learn More and Apply Now -->
                                        <div class="mt-4 d-flex gap-2">
                                            <a href="<?= site_url('programs/' . url_title(strtolower($program['name']), '-', true) . '/details') ?>" class="btn btn-primary">
                                                <i class="ri-information-line me-1"></i> Learn More
                                            </a>
                                            <a href="<?= site_url('programs/' . url_title(strtolower($program['name']), '-', true) . '/apply') ?>" class="btn btn-success">
                                                <i class="ri-user-add-line me-1"></i> Apply Now
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right column: Image -->
                                <div class="col-md-4 position-relative">
                                    <?php if (isset($program['logo_url'])): ?>
                                        <img src="<?= compress_image($program['logo_url'], 600, 400, 80, true); ?>" alt="<?= $program['name'] ?>"
                                            class="img-fluid h-100 w-100 program-img"
                                            style="object-fit: cover; border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="ri-image-line display-4 text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center p-4 rounded-3 bg-light border">
                <div class="avatar-lg mx-auto mb-3">
                    <div class="avatar-title bg-soft-primary text-primary display-5 rounded-circle">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                </div>
                <h5>No Upcoming Programs</h5>
                <p class="text-muted">Check back soon for upcoming programs and initiatives.</p>
            </div>
        <?php endif; ?>

        <!-- Button for Previous Programs -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="<?= site_url('programs') ?>" class="btn btn-outline-primary">
                    <i class="ri-history-line me-1"></i> View Other and Previous Programs
                </a>
            </div>
        </div>
    </div>
</section>
<!-- End current programs section -->