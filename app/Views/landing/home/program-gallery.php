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
        <div class="row">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-success mb-3" role="tablist">
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link active" data-bs-toggle="tab" href="#latest" role="tab">Latest</a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#2023" role="tab">2023</a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#2024" role="tab">2024</a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#2025" role="tab">2025</a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content text-muted">
                            <div class="tab-pane active" id="latest" role="tabpanel">
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
                            <div class="tab-pane" id="2023" role="tabpanel">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        In some designs, you might adjust your tracking to create a certain artistic
                                        effect. It can also help you fix fonts that are poorly spaced to begin with.
                                    </div>
                                </div>
                                <div class="d-flex mt-2">
                                    <div class="flex-shrink-0">
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        A wonderful serenity has taken possession of my entire soul, like these sweet
                                        mornings of spring which I enjoy with my whole heart.
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="2024" role="tabpanel">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        Each design is a new, unique piece of art birthed into this world, and while you
                                        have the opportunity to be creative and make your own style choices.
                                    </div>
                                </div>
                                <div class="d-flex mt-2">
                                    <div class="flex-shrink-0">
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        For that very reason, I went on a quest and spoke to many different professional
                                        graphic designers and asked them what graphic design tips they live.
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="2025" role="tabpanel">
                                <div class="d-flex mt-2">
                                    <div class="flex-shrink-0">
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        For that very reason, I went on a quest and spoke to many different professional
                                        graphic designers and asked them what graphic design tips they live.
                                    </div>
                                </div>
                                <div class="d-flex mt-2">
                                    <div class="flex-shrink-0">
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        After gathering lots of different opinions and graphic design basics, I came up
                                        with a list of 30 graphic design tips that you can start implementing.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!--end col-->
        </div>

        <!-- <?php if (count($gallery_photos) > 6): ?>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="<?= site_url('gallery') ?>" class="btn btn-outline-primary">
                    <i class="ri-image-line me-1"></i> View All Photos
                </a>
            </div>
        </div>
        <?php endif; ?> -->
    </div>
</section>
<!-- End program gallery section -->

<!-- Include the gallery modal -->
<?= $this->include('partials/gallery-modal') ?>