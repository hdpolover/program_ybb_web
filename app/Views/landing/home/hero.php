<!-- Start hero section with full-width banner -->
<section class="section pb-0 hero-section" id="hero">
    <div class="bg-overlay bg-overlay-pattern"></div>
    <div class="position-relative" style="max-height: 100vh; overflow: hidden;">
        <img src="<?= function_exists('compress_image') && !empty($webSettings['main_banner_url']) 
            ? compress_image($webSettings['main_banner_url'], 1920, 600, 80, true) 
            : (isset($webSettings['main_banner_url']) ? $webSettings['main_banner_url'] : '/assets/images/default-banner.jpg'); ?>" 
            class="d-block w-100" alt="Program Banner" style="width: 100%; max-height: 80vh; object-fit: contain;">
    </div>
</section>
<!-- End hero section -->