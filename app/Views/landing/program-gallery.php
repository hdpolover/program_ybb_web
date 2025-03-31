<?php
/**
 * Program gallery section
 * Displays program photos in a responsive grid with modal functionality
 */
?>

<!-- Start program gallery section -->
<section class="section" id="program-gallery">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold">Program Gallery</h2>
                    <p class="text-muted">Explore our program through images</p>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <?php
            // Get photos from the array or use fallback images
            $gallery_photos = $photos ?? [];
            
            // If no photos available, use fallback demo images
            if (empty($gallery_photos)) {
                $gallery_photos = [
                    [
                        'img_url' => '/assets/images/small/img-1.jpg',
                        'title' => 'Demo Photo 1',
                        'description' => 'This is a demo photo for the gallery' 
                    ],
                    [
                        'img_url' => '/assets/images/small/img-2.jpg',
                        'title' => 'Demo Photo 2',
                        'description' => 'This is a demo photo for the gallery'
                    ],
                    [
                        'img_url' => '/assets/images/small/img-3.jpg',
                        'title' => 'Demo Photo 3',
                        'description' => 'This is a demo photo for the gallery'
                    ],
                    [
                        'img_url' => '/assets/images/small/img-4.jpg',
                        'title' => 'Demo Photo 4',
                        'description' => 'This is a demo photo for the gallery'
                    ],
                    [
                        'img_url' => '/assets/images/small/img-5.jpg',
                        'title' => 'Demo Photo 5',
                        'description' => 'This is a demo photo for the gallery'
                    ],
                    [
                        'img_url' => '/assets/images/small/img-6.jpg',
                        'title' => 'Demo Photo 6',
                        'description' => 'This is a demo photo for the gallery'
                    ]
                ];
            }
            
            shuffle($gallery_photos);
            $display_photos = array_slice($gallery_photos, 0, 6);

            foreach ($display_photos as $index => $photo):
                $size_class = ($index < 2) ? 'col-lg-6' : 'col-lg-4 col-md-6';
                $title = $photo['title'] ?? ($program_info['name'] ?? 'Program') . ' Photo ' . ($index + 1);
                $description = $photo['description'] ?? 'Program photo';
                $img_url = $photo['img_url'] ?? '/assets/images/small/img-1.jpg';
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
            <?php endforeach; ?>
        </div>
        
        <?php if (count($gallery_photos) > 6): ?>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="<?= site_url('gallery') ?>" class="btn btn-outline-primary">
                        <i class="ri-image-line me-1"></i> View All Photos
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- End program gallery section -->

<!-- Include the gallery modal -->
<?= $this->include('partials/gallery-modal') ?>