<!-- Start testimonial section -->
<section class="section bg-primary" id="testimonials">
    <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold text-white">What People Say About Our Program</h2>
                    <p class="text-white-50">Discover the impact of our program through the experiences of our participants</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="swiper testimonial-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($testimonies as $testimony) : ?>
                            <div class="swiper-slide">
                                <div class="card testimonial-card shadow-lg border-0">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-shrink-0">
                                                <img src="<?= compress_image($testimony['img_url'], 100, 100, 80, true); ?>" alt="<?= $testimony['person_name']; ?>"
                                                    class="avatar-lg rounded-circle border border-primary p-1">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1 text-primary"><?= $testimony['person_name']; ?></h5>
                                                <p class="text-muted mb-0"><?= $testimony['occupation']; ?></p>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0"><?= $testimony['testimony']; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination position-relative mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End testimonial section -->