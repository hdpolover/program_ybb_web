<!-- Start video section -->
<section class="section bg-primary" id="program-video">
    <div class="bg-overlay bg-overlay-pattern opacity-50"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold text-white">Watch Our Video</h2>
                    <p class="text-white-50">Learn more about our program through this video</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="ratio ratio-16x9 rounded-4 shadow-lg overflow-hidden">
                    <iframe src="<?= $programs[0]['registration_video_url'] ?? ''; ?>" title="Program Video" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End video section -->