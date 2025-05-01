<!-- Program Sidebar -->

<!-- Application CTA Card -->
<?php
// Use the application CTA component with the card style
echo $this->include('landing/program-detail/components/application-cta', [
    'program' => $program,
    'style' => 'card',
    'buttonText' => 'Register Now'
]);
?>

<!-- Program Video Card -->
<?php if (!empty($program['registration_video_url'])): ?>
    <div class="card mb-4 video-card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-primary text-white py-3">
            <h3 class="card-title mb-0 fw-semibold">
                <i class="ri-video-line me-2"></i>Registration Video
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="ratio ratio-16x9 video-wrapper">
                <?php 
                $videoUrl = esc($program['registration_video_url']);
                // Check if video URL is from YouTube or Vimeo and add parameters
                if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                    $videoUrl .= (strpos($videoUrl, '?') !== false ? '&' : '?') . 'rel=0&modestbranding=1';
                } elseif (strpos($videoUrl, 'vimeo.com') !== false) {
                    $videoUrl .= (strpos($videoUrl, '?') !== false ? '&' : '?') . 'title=0&byline=0';
                }
                ?>
                <iframe 
                    src="<?= $videoUrl ?>"
                    title="Program Registration Instructions"
                    class="w-100 video-frame"
                    frameborder="0"
                    loading="lazy"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    aria-label="Video showing how to register for this program"
                ></iframe>
            </div>
            <div class="video-info p-3 bg-light">
                <p class="text-dark mb-0 small">
                    <i class="ri-information-line me-1 text-primary"></i> 
                    <strong>Important:</strong> Please watch this video for complete registration instructions and requirements.
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Share Card -->
<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title mb-3">Share This Program</h3>
        <div class="d-flex gap-2">
            <a href="javascript:void(0);" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-primary btn-icon" title="Share on Facebook">
                <i class="ri-facebook-fill fs-16"></i>
            </a>
            <a href="javascript:void(0);" onclick="window.open('https://twitter.com/intent/tweet?text=<?= urlencode($program['name'] ?? 'Check out this program') ?>&url='+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-info btn-icon" title="Share on Twitter">
                <i class="ri-twitter-fill fs-16"></i>
            </a>
            <a href="mailto:?subject=<?= urlencode($program['name'] ?? 'Check out this program') ?>&body=<?= urlencode('I thought you might be interested in this program: ' . (isset($program['name']) ? $program['name'] . ' - ' : '') . (current_url())) ?>" class="btn btn-soft-danger btn-icon" title="Share via Email">
                <i class="ri-mail-line fs-16"></i>
            </a>
            <a href="javascript:void(0);" onclick="window.open('https://api.whatsapp.com/send?text=<?= urlencode($program['name'] ?? 'Check out this program') ?>: '+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-success btn-icon" title="Share on WhatsApp">
                <i class="ri-whatsapp-line fs-16"></i>
            </a>
            <a href="javascript:void(0);" onclick="window.open('https://www.linkedin.com/sharing/share-offsite/?url='+encodeURIComponent(window.location.href), '_blank')" class="btn btn-soft-secondary btn-icon" title="Share on LinkedIn">
                <i class="ri-linkedin-fill fs-16"></i>
            </a>
        </div>
    </div>
</div>

<!-- End Program Sidebar -->