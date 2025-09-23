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
                        <div class="card gallery-card h-100 border-0 shadow-sm">
                            <div class="gallery-img-container position-relative overflow-hidden">
                                <img
                                    src="<?= $hasGallery ?
                                               (function_exists('compress_gallery_image') ? compress_gallery_image($image['url']) : $image['url']) :
                                               $image ?>"
                                    class="card-img-top gallery-img"
                                    alt="<?= $hasGallery ? ($image['caption'] ?? 'Gallery Image') : 'Program Image ' . ($index + 1) ?>"
                                    style="height: 250px; width: 100%; object-fit: cover;">
                                <div class="gallery-overlay">
                                    <div class="gallery-actions">
                                        <a href="<?= $hasGallery ? $image['url'] : $image ?>" class="btn btn-icon btn-sm btn-soft-light gallery-popup">
                                            <i class="ri-zoom-in-line"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-sm btn-soft-light gallery-info" 
                                           data-title="<?= $hasGallery ? ($image['caption'] ?? 'Program Image ' . ($index + 1)) : 'Program Image ' . ($index + 1) ?>" 
                                           data-description="<?= $hasGallery ? ($image['description'] ?? 'Discover the vibrant environment and engaging experiences at our program.') : 'Discover the vibrant environment and engaging experiences at our program.' ?>">
                                            <i class="ri-information-line"></i>
                                        </a>
                                    </div>
                                    <div class="category-badge">
                                        <span class="badge rounded-pill bg-primary">Program</span>
                                    </div>
                                    <h5 class="gallery-title text-white"><?= $hasGallery ? ($image['caption'] ?? 'Program Image ' . ($index + 1)) : 'Program Image ' . ($index + 1) ?></h5>
                                </div>
                            </div>
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

        <!-- Gallery Info Modal -->
        <div class="modal fade" id="galleryInfoModal" tabindex="-1" aria-labelledby="galleryInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="galleryInfoModalLabel">Image Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 id="modal-image-title"></h5>
                        <p id="modal-image-description" class="text-muted"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Photo Gallery Section -->

<style>
    /* Gallery Card Styles */
    .gallery-card {
        transition: all 0.3s ease;
        overflow: hidden;
        border-radius: 10px;
    }

    .gallery-img-container {
        height: 250px;
    }

    .gallery-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .gallery-card:hover .gallery-img {
        transform: scale(1.1);
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.7) 100%);
        opacity: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .gallery-card:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .gallery-title {
        margin: 0;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .gallery-card:hover .gallery-title {
        transform: translateY(0);
    }

    .category-badge {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateY(-10px);
    }

    .gallery-card:hover .category-badge {
        opacity: 1;
        transform: translateY(0);
    }

    .btn-soft-light {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: none;
    }

    .btn-soft-light:hover {
        background-color: rgba(255, 255, 255, 0.4);
        color: #fff;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize lightbox for gallery images
        const lightbox = GLightbox({
            selector: '.gallery-popup',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });

        // Gallery info modal functionality
        const galleryInfoBtns = document.querySelectorAll('.gallery-info');
        const galleryInfoModal = new bootstrap.Modal(document.getElementById('galleryInfoModal'));

        galleryInfoBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const title = this.getAttribute('data-title');
                const description = this.getAttribute('data-description');

                document.getElementById('modal-image-title').textContent = title;
                document.getElementById('modal-image-description').textContent = description;

                galleryInfoModal.show();
            });
        });
    });
</script>