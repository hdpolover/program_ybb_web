<!-- Start testimonial section -->
<section class="section bg-primary" id="testimonials">
    <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold text-white">Voices of Success: Our Community Speaks</h2>
                    <p class="text-white-50">Real stories from participants who've experienced transformational results with our program</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8 position-relative"> <!-- Reduced from col-lg-10 to col-lg-8 to make card wider relatively -->
                <?php if (empty($testimonies)): ?>
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5 text-center">
                            <div class="py-4">
                                <i class="ri-message-3-line display-4 text-muted mb-3"></i>
                                <h4 class="text-dark fw-semibold mb-3">No testimonials yet</h4>
                                <p class="text-muted mb-0">Our community testimonials will be available soon. Check back later!</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Navigation buttons outside the swiper for better visibility -->
                    <div class="swiper-navs">
                        <div class="swiper-button-prev testimonial-prev-btn"></div>
                        <div class="swiper-button-next testimonial-next-btn"></div>
                    </div>

                    <div class="swiper testimonial-swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($testimonies as $key => $testimony) : ?>
                                <div class="swiper-slide">
                                    <div class="card testimonial-card shadow-lg border-0">
                                        <div class="card-body p-5">
                                            <div class="d-flex flex-column align-items-center text-center mb-4">
                                                <div class="testimonial-img-wrapper mb-4" style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%;">
                                                    <img src="<?= $testimony['img_url'] ?>" alt="<?= $testimony['person_name']; ?>"
                                                        class="testimonial-img border border-primary p-2" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                                <div class="mt-3 mb-3">
                                                    <h4 class="mb-1 text-primary fw-semibold"><?= $testimony['person_name']; ?></h4>
                                                    <p class="text-muted mb-0"><?= $testimony['occupation']; ?></p>
                                                    <?php if (!empty($testimony['institution'])): ?>
                                                        <p class="text-muted mb-0 fst-italic"><?= $testimony['institution'] ?? ''; ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="testimonial-content">
                                                <?php
                                                $truncated = strlen($testimony['testimony']) > 180 ?
                                                    substr($testimony['testimony'], 0, 180) . '...' :
                                                    $testimony['testimony'];
                                                ?>
                                                <p class="text-muted testimony-text mb-0 text-center"><?= $truncated ?></p>
                                                <p class="text-muted text-center mt-2 fst-italic">— <?= $testimony['person_name']; ?>, <?= $testimony['occupation']; ?></p>

                                                <?php if (strlen($testimony['testimony']) > 180): ?>
                                                    <div class="text-center mt-4">
                                                        <button type="button" class="btn btn-primary read-more-btn"
                                                            data-bs-toggle="modal" data-bs-target="#testimonialModal<?= $testimony['id'] ?? $key ?>">
                                                            Read Full Testimonial
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination position-relative mt-4"></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- All Testimonial Modals Outside Swiper -->
    <?php if (!empty($testimonies)): ?>
        <?php foreach ($testimonies as $key => $testimony) : ?>
            <?php if (strlen($testimony['testimony']) > 180): ?>
                <div class="modal fade" id="testimonialModal<?= $testimony['id'] ?? $key ?>" tabindex="-1"
                    aria-labelledby="testimonialModalLabel<?= $testimony['id'] ?? $key ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header p-4">
                                <h5 class="modal-title" id="testimonialModalLabel<?= $testimony['id'] ?? $key ?>">
                                    Testimonial from <?= $testimony['person_name']; ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 p-lg-5">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0" style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%;">
                                        <img src="<?= $testimony['img_url'] ?>" alt="<?= $testimony['person_name']; ?>"
                                            class="img-fluid border border-primary p-3" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1 ms-4">
                                        <h4 class="mb-1 text-primary"><?= $testimony['person_name']; ?></h4>
                                        <p class="text-muted mb-0"><?= $testimony['occupation']; ?></p>
                                        <?php if (!empty($testimony['institution'])): ?>
                                            <p class="text-muted mb-0 fst-italic"><?= $testimony['institution'] ?? ''; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="testimonial-full-content">
                                    <p class="text-muted"><?= $testimony['testimony']; ?></p>
                                    <p class="text-muted fst-italic text-end">— <?= $testimony['person_name']; ?>, <?= $testimony['occupation']; ?></p>
                                </div>
                            </div>
                            <div class="modal-footer p-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div> <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<!-- End testimonial section -->