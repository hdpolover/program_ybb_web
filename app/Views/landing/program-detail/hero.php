<!-- Start Hero Section -->
<section class="section bg-light position-relative" id="program-hero">
    <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-8 text-center mt-5">
                <div class="badge bg-danger text-white fs-14 mb-2">
                    <i class="mdi mdi-star-circle me-1"></i> Featured Program
                </div>
                <h1 class="text-black mb-3 fw-bold display-5"><?= $program['name'] ?? 'Program Details' ?></h1>
                <?php if (isset($category['tagline']) && !empty($category['tagline'])): ?>
                    <p class="text-white-75 fs-16 mb-4"><?= $category['tagline'] ?></p>
                <?php endif; ?>

                <div class="d-flex justify-content-center gap-3">
                    <div class="avatar-group">
                        <?php
                        // If we have participant profile pictures, display them here
                        $avatar_count = isset($participant_photos) ? count($participant_photos) : 0;
                        if ($avatar_count > 0):
                            for ($i = 0; $i < $avatar_count; $i++):
                        ?>
                                <div class="avatar-group-item">
                                    <?php if (function_exists('compress_image')): ?>
                                        <img src="<?= compress_image($participant_photos[$i], 40, 40, 80) ?>" alt="" class="rounded-circle avatar-sm">
                                    <?php else: ?>
                                        <img src="<?= $participant_photos[$i] ?>" alt="" class="rounded-circle avatar-sm">
                                    <?php endif; ?>
                                </div>
                            <?php
                            endfor;
                        else:
                            ?>
                            <div class="avatar-group-item">
                                <div class="avatar-md">
                                    <div class="avatar-title rounded-circle bg-soft-light text-white">
                                        <i class="ri-user-add-line fs-16"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($avatar_count > 0): ?>
                        <p class="text-white-75 fs-16 mb-0 align-self-center">Join them and hundreds more!</p>
                    <?php else: ?>
                        <p class="text-white-75 fs-16 mb-0 align-self-center">Join our program now!</p>
                    <?php endif; ?>
                </div>

                <div class="mt-4 pt-2">
                    <?php if (isset($program['registration_open']) && $program['registration_open']) : ?>
                        <a href="<?= base_url('registration/' . ($program['slug'] ?? $program['id'] ?? '')) ?>" class="btn btn-success btn-lg">
                            <i class="ri-user-add-line me-1"></i> Apply Now
                        </a>
                    <?php else : ?>
                        <button class="btn btn-light btn-lg" disabled>
                            <i class="ri-calendar-event-line me-1"></i> Registration Closed
                        </button>
                    <?php endif; ?>
                    <a href="#program-detail" class="btn btn-info btn-lg ms-2">
                        <i class="ri-information-line me-1"></i> Learn More
                    </a>
                </div>
            </div>
        </div>
        <!-- Key info badges -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6 mt-4 pt-2">
                        <div class="card bg-white bg-opacity-10 border-0 text-center">
                            <div class="card-body p-3">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-soft-light text-white rounded-circle fs-18">
                                        <i class="ri-calendar-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-white fs-16"><?= isset($program['start_date']) ? date('M d, Y', strtotime($program['start_date'])) : 'Date TBA' ?></h5>
                                <p class="text-white-75 mb-0">Start Date</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mt-4 pt-2">
                        <div class="card bg-white bg-opacity-10 border-0 text-center">
                            <div class="card-body p-3">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-soft-light text-white rounded-circle fs-18">
                                        <i class="ri-map-pin-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-white fs-16"><?= $program['location'] ?? 'Location TBA' ?></h5>
                                <p class="text-white-75 mb-0">Venue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mt-4 pt-2">
                        <div class="card bg-white bg-opacity-10 border-0 text-center">
                            <div class="card-body p-3">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-soft-light text-white rounded-circle fs-18">
                                        <i class="ri-time-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-white fs-16"><?= $program['duration'] ?? 'Duration TBA' ?></h5>
                                <p class="text-white-75 mb-0">Program Length</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mt-4 pt-2">
                        <div class="card bg-white bg-opacity-10 border-0 text-center">
                            <div class="card-body p-3">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-soft-light text-white rounded-circle fs-18">
                                        <i class="ri-group-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-white fs-16"><?= $program['capacity'] ?? 'Limited Spots' ?></h5>
                                <p class="text-white-75 mb-0">Capacity</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="position-absolute start-0 end-0 bottom-0 hero-shape-svg">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
            <g mask="url(&quot;#SvgjsMask1003&quot;)" fill="none">
                <path d="M 0,118 C 288,98.6 1152,40.4 1440,21L1440 140L0 140z" fill="rgba(255, 255, 255, 1)"></path>
            </g>
        </svg>
    </div>
</section>
<!-- End Hero Section -->