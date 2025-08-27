<?php

/**
 * Video testimonies section
 * Displays video testimonies in a responsive grid with modal functionality
 */
?>

<!-- Start video testimonies section -->
<section class="section" id="video-testimonies">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold">Video Testimonies</h2>
                    <?php
                    // Set the description based on whether the program has video testimonies
                    $hasVideoTestimonies = $hasVideoTestimonies ?? false;
                    $programName = !empty($programs) ? $programs[0]['name'] ?? 'This Program' : 'Our Programs';
                    
                    if ($hasVideoTestimonies) {
                        $description = "Watch inspiring testimonials from {$programName} participants";
                    } else {
                        $description = "Discover inspiring stories from our program participants";
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
                        // Get video testimonies from the array
                        $video_testimonies = $videoTestimonies ?? [];
                        
                        // Show content if we have video testimonies
                        if (!empty($video_testimonies) && is_array($video_testimonies)):
                        ?>
                        
                        <div class="row g-4">
                            <?php 
                            // Process each program's video testimonies
                            foreach ($video_testimonies as $program_videos):
                                $program_name = $program_videos['program_name'] ?? 'Program';
                                $videos = $program_videos['videos'] ?? [];
                                
                                if (empty($videos)) continue;
                                
                                foreach ($videos as $video_index => $video):
                                    $youtube_video_id = $video['youtube_video_id'] ?? '';
                                    $description = $video['description'] ?? 'Video testimony';
                                    $youtube_url = $video['youtube_url'] ?? '';
                                    
                                    // Skip if no video ID is available
                                    if (empty($youtube_video_id)) continue;
                                    
                                    // Create thumbnail URL from YouTube video ID
                                    $thumbnail_url = "https://img.youtube.com/vi/{$youtube_video_id}/maxresdefault.jpg";
                                    $embed_url = "https://www.youtube.com/embed/{$youtube_video_id}";
                                ?>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="video-box card border-0 overflow-hidden h-100 shadow-sm">
                                            <div class="video-container position-relative">
                                                <div class="video-thumbnail" style="position: relative; height: 280px; background: linear-gradient(45deg, #f8f9fa, #e9ecef); overflow: hidden; border-radius: 8px 8px 0 0;">
                                                    <img src="<?= $thumbnail_url ?>" 
                                                         alt="<?= htmlspecialchars($description) ?>"
                                                         class="img-fluid video-thumb"
                                                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    
                                                    <!-- Fallback for when thumbnail fails to load -->
                                                    <div class="video-fallback d-none align-items-center justify-content-center h-100" style="position: absolute; top: 0; left: 0; width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <div class="text-center text-white">
                                                            <i class="ri-video-line fs-1 mb-2"></i>
                                                            <p class="mb-0">Video Testimony</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Play button overlay -->
                                                    <div class="video-overlay d-flex align-items-center justify-content-center" 
                                                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.2); cursor: pointer; transition: background 0.3s ease;"
                                                         onclick="openVideoModal('<?= $embed_url ?>', '<?= htmlspecialchars($description) ?>', '<?= htmlspecialchars($program_name) ?>')">
                                                        <div class="play-button text-center">
                                                            <div class="play-icon d-flex align-items-center justify-content-center mx-auto" 
                                                                 style="width: 60px; height: 60px; background: rgba(255,255,255,0.95); border-radius: 50%; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                                                <i class="ri-play-fill text-primary fs-2"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-body p-4">
                                                    <h6 class="card-title mb-1 text-dark"><?= htmlspecialchars($program_name) ?></h6>
                                                    <p class="card-text text-muted small mb-0"><?= htmlspecialchars($description) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; 
                            endforeach; ?>
                        </div>
                        
                        <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="ri-video-line fs-1 mb-3"></i>
                                <h5>No Video Testimonies Available</h5>
                                <p>There are no video testimonies available for this program at the moment.</p>
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
<!-- End video testimonies section -->

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel">Video Testimony</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe id="videoIframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <div class="modal-info">
                    <h6 id="videoTitle" class="mb-1"></h6>
                    <p id="videoDescription" class="text-muted mb-0 small"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.video-box {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 12px;
}

.video-box:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.video-overlay:hover {
    background: rgba(0,0,0,0.4) !important;
}

.video-overlay:hover .play-icon {
    transform: scale(1.1);
    background: rgba(255,255,255,1) !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.video-thumb {
    transition: transform 0.3s ease;
}

.video-container:hover .video-thumb {
    transform: scale(1.02);
}

.card-title {
    font-weight: 600;
    color: #2c3e50;
}

/* Modal responsiveness */
@media (max-width: 768px) {
    .modal-lg {
        max-width: 90%;
        margin: 1rem auto;
    }
    
    .video-thumbnail {
        height: 200px !important;
    }
    
    .play-icon {
        width: 50px !important;
        height: 50px !important;
    }
    
    .play-icon i {
        font-size: 1.5rem !important;
    }
}
</style>

<script>
function openVideoModal(embedUrl, description, programName) {
    const modal = new bootstrap.Modal(document.getElementById('videoModal'));
    const iframe = document.getElementById('videoIframe');
    const title = document.getElementById('videoTitle');
    const desc = document.getElementById('videoDescription');
    
    // Set video source with autoplay
    iframe.src = embedUrl + '?autoplay=1&rel=0';
    title.textContent = programName;
    desc.textContent = description;
    
    modal.show();
    
    // Clear iframe when modal is closed to stop video
    document.getElementById('videoModal').addEventListener('hidden.bs.modal', function() {
        iframe.src = '';
    });
}
</script>