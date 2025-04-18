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