<!-- Start hero section with full-width banner -->
<section class="section pb-0 hero-section" id="hero">
    <div class="bg-overlay bg-overlay-pattern"></div>
    <div class="position-relative">
        <img src="<?= function_exists('compress_image') && isset($photos[0]['img_url']) ? compress_image($photos[0]['img_url'], 1920, 600, 80, true) : (isset($photos[0]['img_url']) ? $photos[0]['img_url'] : '/assets/images/default-banner.jpg'); ?>" class="d-block w-100" alt="Program Banner" style="height: 80vh; object-fit: cover;">
    </div>
</section>
<!-- End hero section -->