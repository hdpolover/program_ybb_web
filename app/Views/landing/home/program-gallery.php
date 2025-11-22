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
                    <h2 class="mb-3 fw-semibold">Photo Gallery</h2>
                    <?php
                    // Set the description based on whether the program has photos
                    $hasPhotos = $program_info['hasPhotos'] ?? false;
                    $programName = $program_info['name'] ?? 'This Program';
                    
                    if ($hasPhotos) {
                        $description = "Explore memorable moments from {$programName}";
                    } else {
                        $description = "Discover visual highlights from our various programs";
                    }
                    ?>
                    <p class="text-muted"><?= $description ?></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-body">
                        <?php 
                        // Get photos from the array or use fallback images
                        $gallery_photos = $photos ?? [];
                        $years = [];
                        
                        // If photos exist, organize them by year
                        if (!empty($gallery_photos) && is_array($gallery_photos)) {
                            $years = array_keys($gallery_photos);
                            // Make sure "Unknown" is at the end if it exists
                            if (in_array('Unknown', $years)) {
                                $unknown_key = array_search('Unknown', $years);
                                unset($years[$unknown_key]);
                                $years[] = 'Unknown';
                            }
                        }
                        
                        // Show tabs only if we have photos
                        if (!empty($years)):
                        ?>
                        
                        <ul class="nav nav-pills nav-success mb-3" role="tablist">
                            <?php foreach ($years as $index => $year): ?>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link <?= ($index === 0) ? 'active' : '' ?>" data-bs-toggle="tab" href="#year-<?= $year ?>" role="tab"><?= $year ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <!-- Tab panes -->
                        <div class="tab-content text-muted">
                            <?php foreach ($years as $index => $year): ?>
                            <div class="tab-pane <?= ($index === 0) ? 'active' : '' ?>" id="year-<?= $year ?>" role="tabpanel">
                                <div class="row g-3">
                                    <?php
                                    $year_photos = $gallery_photos[$year] ?? [];
                                    
                                    if (empty($year_photos)): ?>
                                        <div class="col-12 text-center py-5">
                                            <div class="text-muted">
                                                <i class="ri-image-line fs-2 mb-2"></i>
                                                <p>No photos available for this period.</p>
                                            </div>
                                        </div>
                                    <?php else:
                                    // Get the first 4 photos only for display in the tab
                                    $display_photos = array_slice($year_photos, 0, 4);
                                    
                                    foreach ($display_photos as $photo_index => $photo):
                                        // Skip if photo is not an array
                                        if (!is_array($photo)) continue;
                                        
                                        // First 2 photos get larger columns, remaining get smaller columns
                                        $size_class = ($photo_index < 2) ? 'col-lg-6' : 'col-lg-6 col-md-6';
                                        $title = !empty($photo['title']) ? $photo['title'] : (($program_info['name'] ?? 'Program') . ' Photo ' . ((int)$photo_index + 1));
                                        $description = $photo['description'] ?? 'Program photo';
                                        $img_url = $photo['img_url'] ?? '';
                                        
                                        // Skip if no image URL is available
                                        if (empty($img_url)) continue;
                                    ?>
                                        <div class="<?= $size_class ?>">
                                            <div class="gallery-box card border-0 overflow-hidden">
                                                <div class="gallery-container">
                                                    <a href="javascript:void(0);" class="gallery-popup"
                                                        data-title="<?= htmlspecialchars($title) ?>"
                                                        data-description="<?= htmlspecialchars($description) ?>"
                                                        data-src="<?= $img_url ?>">
                                                        <img src="<?= function_exists('compress_gallery_image') ? compress_gallery_image($img_url) : $img_url; ?>"
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
                                    <?php endforeach; 
                                    endif; ?>
                                </div>
                                <?php if (count($year_photos) > 4): ?>
                                    <div class="row mt-4">
                                        <div class="col-12 text-center">
                                            <a href="<?= site_url('gallery') ?>" class="btn btn-outline-primary">
                                                <i class="ri-image-line me-1"></i> View All Photos
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="ri-image-line fs-1 mb-3"></i>
                                <h5>No Photos Available</h5>
                                <p>There are no photos available for this program at the moment.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div>
            <!--end col-->
        </div>
    </div>
</section>
<!-- End program gallery section -->

<!-- Include the gallery modal -->
<?= $this->include('partials/gallery-modal') ?>