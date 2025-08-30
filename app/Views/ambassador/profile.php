<?= $this->include('partials/main') ?>

<head>

    <?php echo view('partials/title-meta', array('title' => 'Ambassador Profile')); ?>

    <?= $this->include('partials/head-css') ?>

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?= $this->include('partials/ambassador-menu') ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <?php echo view('partials/page-title', array('pagetitle' => 'Ambassador', 'title' => 'Profile')); ?>                    
                    <!-- Link Sharing Section -->
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <div class="card border-primary shadow">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title mb-0 text-white">
                                        <i class="ri-share-line me-2"></i>Share Your Ambassador Link
                                        <span class="badge bg-warning text-dark ms-2">Important</span>
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-success border-start border-4 border-success" role="alert">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="ri-gift-line fs-3 text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="alert-heading mb-1 text-success">🎉 Earn Rewards by Sharing!</h6>
                                                <span>Share this unique referral link with potential participants to earn referral rewards! Your referral code is <span class="badge bg-success"><?= esc($ambassador['details']['ref_code'] ?? $ambassador['ref_code'] ?? 'N/A') ?></span></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-control-lg" id="referralLink" value="<?= esc($generatedLink) ?>" readonly style="font-weight: 500; background-color: #f8f9fa;">
                                        <button class="btn btn-success btn-lg" type="button" id="copyLinkBtn" onclick="copyReferralLink()">
                                            <i class="ri-file-copy-line align-middle me-1"></i> Copy Link
                                        </button>
                                    </div>
                                    
                                    <div id="copySuccess" class="text-success mt-2" style="display: none;">
                                        <i class="ri-check-double-line me-1"></i> Link copied successfully!
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-light rounded border">
                                        <h6 class="mb-3 text-center">
                                            <i class="ri-share-forward-line me-2 text-primary"></i>Share via Social Media:
                                        </h6>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <button type="button" class="btn btn-success" onclick="shareViaWhatsApp()" data-bs-toggle="tooltip" title="Share on WhatsApp">
                                                <i class="ri-whatsapp-line align-middle"></i>
                                                <span class="d-none d-sm-inline ms-1">WhatsApp</span>
                                            </button>
                                            <button type="button" class="btn btn-primary" onclick="shareViaFacebook()" data-bs-toggle="tooltip" title="Share on Facebook">
                                                <i class="ri-facebook-line align-middle"></i>
                                                <span class="d-none d-sm-inline ms-1">Facebook</span>
                                            </button>
                                            <button type="button" class="btn btn-dark" onclick="shareViaInstagram()" data-bs-toggle="tooltip" title="Share on Instagram">
                                                <i class="ri-instagram-line align-middle"></i>
                                                <span class="d-none d-sm-inline ms-1">Instagram</span>
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="shareViaEmail()" data-bs-toggle="tooltip" title="Share via Email">
                                                <i class="ri-mail-line align-middle"></i>
                                                <span class="d-none d-sm-inline ms-1">Email</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Link Sharing Section -->
                    
                    <!-- Ambassador Profile Section -->
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card overflow-hidden">
                                <div class="bg-primary bg-soft">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="text-primary p-3">
                                                <h5 class="text-primary">Welcome Back!</h5>
                                                <p class="mb-0">Ambassador Dashboard</p>
                                            </div>
                                        </div>
                                        <div class="col-5 align-self-end">
                                            <img src="/assets/images/profile-img.png" alt="" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="avatar-md profile-user-wid mb-4 mt-2 mx-auto d-block">
                                                <div class="avatar-title rounded-circle bg-light text-primary">
                                                    <?= strtoupper(substr($ambassador['details']['name'] ?? $ambassador['full_name'] ?? $ambassador['name'] ?? 'Ambassador', 0, 1)) ?>
                                                </div>
                                            </div>
                                            <h5 class="font-size-15 text-center"><?= esc($ambassador['details']['name'] ?? $ambassador['full_name'] ?? $ambassador['name'] ?? 'Ambassador') ?></h5>
                                            <p class="text-muted mb-0 text-center">
                                                <i class="ri-award-fill me-1 align-middle"></i> Ambassador
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-2 text-center border-top">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="p-1">
                                                    <h5 class="font-size-15"><?= esc($ambassador['details']['ref_code'] ?? $ambassador['ref_code'] ?? 'N/A') ?></h5>
                                                    <p class="text-muted mb-0">Referral Code</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">
                                        <i class="ri-user-3-line me-2"></i>Personal Information
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" width="35%"><i class="ri-user-3-line me-2 text-primary"></i>Full Name:</th>
                                                    <td><?= esc($ambassador['details']['name'] ?? $ambassador['full_name'] ?? $ambassador['name'] ?? 'Ambassador') ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-mail-line me-2 text-primary"></i>Email:</th>
                                                    <td><?= esc($ambassador['details']['email'] ?? $ambassador['email'] ?? 'Not available') ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-smartphone-line me-2 text-primary"></i>Phone Number:</th>
                                                    <td><?= esc($ambassador['details']['phone_number'] ?? 'Not available') ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-building-line me-2 text-primary"></i>Institution:</th>
                                                    <td><?= esc($ambassador['details']['institution'] ?? 'Not available') ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-user-settings-line me-2 text-primary"></i>Gender:</th>
                                                    <td><?= ucfirst(esc($ambassador['details']['gender'] ?? 'Not specified')) ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-calendar-line me-2 text-primary"></i>Member Since:</th>
                                                    <td>
                                                        <?php 
                                                        $createdAt = $ambassador['details']['created_at'] ?? null;
                                                        if ($createdAt) {
                                                            echo date('F j, Y', strtotime($createdAt));
                                                        } else {
                                                            echo 'Not available';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-check-line me-2 text-primary"></i>Status:</th>
                                                    <td>
                                                        <?php 
                                                        $isActive = $ambassador['details']['is_active'] ?? true;
                                                        if ($isActive) {
                                                            echo '<span class="badge bg-success-subtle text-success"><i class="ri-check-line me-1"></i>Active</span>';
                                                        } else {
                                                            echo '<span class="badge bg-danger-subtle text-danger"><i class="ri-close-line me-1"></i>Inactive</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Ambassador Profile Section -->
                    
                    <!-- Program Information Section -->
                    <?php if (isset($ambassador['program']) && $ambassador['program']): ?>
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header bg-soft-success">
                                    <h4 class="card-title mb-0">
                                        <i class="ri-trophy-line me-2"></i>Current Program
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="text-success mb-2"><?= esc($ambassador['program']['name'] ?? 'N/A') ?></h5>
                                            <p class="text-muted mb-3">
                                                <?php 
                                                $description = $ambassador['program']['description'] ?? '';
                                                if ($description) {
                                                    // Strip HTML tags and limit to 200 characters
                                                    $cleanDescription = strip_tags($description);
                                                    $shortDescription = strlen($cleanDescription) > 200 ? substr($cleanDescription, 0, 200) . '...' : $cleanDescription;
                                                    echo esc($shortDescription);
                                                } else {
                                                    echo 'No description available';
                                                }
                                                ?>
                                            </p>
                                            <?php if (isset($ambassador['program']['start_date']) && isset($ambassador['program']['end_date'])): ?>
                                            <div class="d-flex gap-3 mb-2">
                                                <small class="text-muted">
                                                    <i class="ri-calendar-event-line me-1"></i>
                                                    <strong>Start:</strong> <?= date('M j, Y', strtotime($ambassador['program']['start_date'])) ?>
                                                </small>
                                                <small class="text-muted">
                                                    <i class="ri-calendar-check-line me-1"></i>
                                                    <strong>End:</strong> <?= date('M j, Y', strtotime($ambassador['program']['end_date'])) ?>
                                                </small>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (isset($ambassador['program']['status'])): ?>
                                            <span class="badge bg-info-subtle text-info">
                                                <i class="ri-information-line me-1"></i><?= ucfirst(esc($ambassador['program']['status'])) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <?php if (isset($ambassador['program']['category']['web_url'])): ?>
                                            <a href="https://<?= esc($ambassador['program']['category']['web_url']) ?>" target="_blank" class="btn btn-success">
                                                <i class="ri-external-link-line me-1"></i>Visit Program
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?= $this->include('partials/footer') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->


    <?= $this->include('partials/vendor-scripts') ?>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
    
    <!-- Minimal Custom Styles -->
    <style>
        .border-primary {
            border-color: #0d6efd !important;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            transition: transform 0.2s ease;
        }
        
        #referralLink {
            user-select: all;
        }
        
        .social-media-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
    </style>
    
    <!-- Copy Link and Social Media Sharing Scripts -->
    <script>
        function copyReferralLink() {
            var linkInput = document.getElementById("referralLink");
            linkInput.select();
            linkInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");

            // Show success message
            var copySuccess = document.getElementById("copySuccess");
            copySuccess.style.display = "block";

            // Hide message after 3 seconds
            setTimeout(function() {
                copySuccess.style.display = "none";
            }, 3000);
        }
        
        function shareViaWhatsApp() {
            var link = document.getElementById("referralLink").value;
            var message = "Join me on this amazing program! Register using my referral link: " + link;
            var whatsappURL = "https://api.whatsapp.com/send?text=" + encodeURIComponent(message);
            window.open(whatsappURL, '_blank');
        }
        
        function shareViaFacebook() {
            var link = document.getElementById("referralLink").value;
            var facebookURL = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(link);
            window.open(facebookURL, '_blank');
        }
          function shareViaInstagram() {
            // Instagram doesn't have a direct share API like other platforms
            // This will copy the link and text to clipboard and notify user to paste in Instagram
            var link = document.getElementById("referralLink").value;
            var message = "Join me on this amazing program! Register using my referral link: " + link;
            
            // Copy to clipboard
            navigator.clipboard.writeText(message).then(function() {
                alert("Link copied! You can now paste it in your Instagram post or story.");
            }).catch(function() {
                // Fallback for older browsers
                var tempInput = document.createElement("textarea");
                tempInput.value = message;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                alert("Link copied! You can now paste it in your Instagram post or story.");
            });
        }
        
        function shareViaEmail() {
            var link = document.getElementById("referralLink").value;
            var subject = "Join me as an Ambassador";
            var body = "Hello,\n\nI'm participating in this amazing program as an Ambassador. Join using my referral link: " + link + "\n\nThanks!";
            var mailtoURL = "mailto:?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);
            window.open(mailtoURL);
        }
    </script>
    
    <!-- Dashboard Animation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple fade-in animation
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transition = 'opacity 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                }, 100 * index);
            });
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>

</html>