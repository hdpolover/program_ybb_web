<!-- Start program category title and tagline section -->
<section class="section bg-primary" id="program-category">
    <div class="bg-overlay bg-overlay-pattern" style="opacity: 0.4;"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="mb-3 fw-semibold text-white"><?= $category['name'] ?? 'Program Name' ?></h2>
                    <p class="text-white-50 fs-16"><?= $category['tagline'] ?? '' ?></p>
                </div>
            </div>
        </div>

        <!-- Program information cards -->
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="card card-animate h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-primary text-white rounded-circle fs-4">
                                        <i class="ri-map-pin-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5 class="fs-16 text-center">Location</h5>
                        <p class="text-muted mb-0 text-center"><?= $category['location'] ?? 'Location not available' ?></p>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card card-animate h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-primary text-white rounded-circle fs-4">
                                        <i class="ri-phone-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5 class="fs-16 text-center">Contact</h5>
                        <p class="text-muted mb-0 text-center"><?= $category['contact'] ?? 'Contact not available' ?></p>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card card-animate h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-primary text-white rounded-circle fs-4">
                                        <i class="ri-mail-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5 class="fs-16 text-center">Email</h5>
                        <p class="text-muted mb-0 text-center"><?= $category['email'] ?? 'Email not available' ?></p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End program category section -->