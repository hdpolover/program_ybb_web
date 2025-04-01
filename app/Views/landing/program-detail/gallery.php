<!-- Photo Gallery Section -->
<section class="section pb-0" id="photo-gallery">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="fw-semibold">Photo Gallery</h2>
                    <p class="text-muted">Explore moments from previous program events</p>
                </div>
            </div>
        </div>

        <?php
        // Check if there are gallery images available
        $hasGallery = isset($program_gallery) && !empty($program_gallery);

        // If no specific gallery images are available, you might want to use a few placeholder images
        $placeholderImages = [
            '/assets/images/small/img-1.jpg',
            '/assets/images/small/img-2.jpg',
            '/assets/images/small/img-3.jpg',
            '/assets/images/small/img-4.jpg',
            '/assets/images/small/img-5.jpg',
            '/assets/images/small/img-6.jpg',
        ];

        // Use either the actual gallery or placeholders
        $galleryImages = $hasGallery ? $program_gallery : $placeholderImages;

        if (!empty($galleryImages)):
        ?>
            <div class="row">
                <!-- Gallery image grid -->
                <?php foreach ($galleryImages as $index => $image): ?>
                    <div class="col-lg-4 col-md-6 gallery-item mb-4">
                        <div class="gallery-box card">
                            <div class="gallery-container">
                                <a class="image-popup" href="<?= $hasGallery ? $image['url'] : $image ?>" title="<?= $hasGallery ? ($image['caption'] ?? '') : 'Program Image ' . ($index + 1) ?>">
                                    <img
                                        src="<?= $hasGallery ?
                                                   (function_exists('compress_image') ? compress_image($image['url'], 600, 400, 80, true) : $image['url']) :
                                                   $image ?>"
                                        class="gallery-img img-fluid mx-auto"
                                        alt="<?= $hasGallery ? ($image['caption'] ?? 'Gallery Image') : 'Program Image ' . ($index + 1) ?>"
                                        style="height: 250px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                            <?php if ($hasGallery && isset($image['caption'])): ?>
                                <div class="box-content">
                                    <div class="gallery-overlay"></div>
                                    <div class="gallery-caption">
                                        <h5 class="title text-white fs-15"><?= $image['caption'] ?></h5>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($galleryImages) > 6): ?>
                <div class="text-center mt-3 mb-5">
                    <a href="#" class="btn btn-primary">View More Photos</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center p-5">
                <div class="avatar-lg mx-auto mb-4">
                    <div class="avatar-title bg-soft-primary text-primary display-5 rounded-circle">
                        <i class="ri-image-line"></i>
                    </div>
                </div>
                <h5>No Gallery Images Available</h5>
                <p class="text-muted">We don't have any gallery images for this program at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- End Photo Gallery Section -->