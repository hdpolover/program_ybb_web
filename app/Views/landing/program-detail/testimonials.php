<!-- Program Testimonials Section -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="flex-shrink-0">
                <div class="avatar-sm">
                    <div class="avatar-title bg-warning-subtle text-warning rounded-circle fs-18">
                        <i class="ri-message-2-line"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h3 class="card-title mb-0">What Participants Say</h3>
            </div>
        </div>

        <div class="row g-3">
            <?php foreach ($testimonials as $testimonial) : ?>
                <div class="col-md-6">
                    <div class="card h-100 border bg-light-subtle">
                        <div class="card-body">
                            <div class="text-warning mb-2">
                                <?php
                                $rating = isset($testimonial['rating']) ? (int)$testimonial['rating'] : 5;
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '<i class="ri-star-fill"></i>' : '<i class="ri-star-line"></i>';
                                }
                                ?>
                            </div>
                            <p class="text-muted fst-italic mb-3"><?= $testimonial['testimonial'] ?? '' ?></p>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <?php if (isset($testimonial['avatar_url']) && !empty($testimonial['avatar_url'])) : ?>
                                        <img src="<?= function_exists('compress_image') ? compress_image($testimonial['avatar_url'], 100, 100, 80, true) : $testimonial['avatar_url'] ?>" alt="<?= $testimonial['name'] ?? 'Participant' ?>" class="avatar-sm rounded-circle">
                                    <?php else : ?>
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-primary text-white rounded-circle">
                                                <?= strtoupper(substr($testimonial['name'] ?? 'P', 0, 1)) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fs-14 mb-0"><?= $testimonial['name'] ?? 'Participant' ?></h5>
                                    <?php if (isset($testimonial['position'])) : ?>
                                        <p class="text-muted mb-0 fs-12"><?= $testimonial['position'] ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- End Program Testimonials Section -->