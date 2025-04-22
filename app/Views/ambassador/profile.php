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

                    <?php echo view('partials/page-title', array('pagetitle' => 'Ambassador', 'title' => 'Profile')); ?>                    <!-- Ambassador Profile Section -->
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
                                                    <?= strtoupper(substr($ambassador['details']['name'], 0, 1)) ?>
                                                </div>
                                            </div>
                                            <h5 class="font-size-15 text-center"><?= esc($ambassador['details']['name']) ?></h5>
                                            <p class="text-muted mb-0 text-center">
                                                <i class="ri-award-fill me-1 align-middle"></i> Ambassador
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-2 text-center border-top">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="p-1">
                                                    <h5 class="font-size-15"><?= esc($ambassador['details']['ref_code']) ?></h5>
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
                                                    <td><?= esc($ambassador['details']['name']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-mail-line me-2 text-primary"></i>Email:</th>
                                                    <td><?= esc($ambassador['details']['email']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-smartphone-line me-2 text-primary"></i>Phone Number:</th>
                                                    <td><?= esc($ambassador['details']['phone_number']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-building-line me-2 text-primary"></i>Institution:</th>
                                                    <td><?= esc($ambassador['details']['institution']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><i class="ri-user-settings-line me-2 text-primary"></i>Gender:</th>
                                                    <td><?= ucfirst(esc($ambassador['details']['gender'])) ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Ambassador Profile Section -->                    <!-- Link Sharing Section -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header bg-soft-primary">
                                    <h4 class="card-title mb-0">
                                        <i class="ri-share-line me-2"></i>Share Your Ambassador Link
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info" role="alert">
                                        <i class="ri-information-line me-2 fs-4 align-middle"></i>
                                        <span>Share this unique link with potential participants to earn referral rewards! You can also use URL shortener services for a more compact link.</span>
                                    </div>
                                    
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-control-lg" id="referralLink" value="<?= $generatedLink ?>" readonly>
                                        <button class="btn btn-primary" type="button" id="copyLinkBtn" onclick="copyReferralLink()">
                                            <i class="ri-file-copy-line align-middle me-1"></i> Copy Link
                                        </button>
                                    </div>
                                    
                                    <div id="copySuccess" class="text-success mt-2" style="display: none;">
                                        <i class="ri-check-double-line me-1"></i> Link copied successfully!
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h5 class="font-size-14 mb-3">Share via:</h5>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary waves-effect waves-light btn-sm" onclick="shareViaWhatsApp()">
                                                <i class="ri-whatsapp-line fs-5 align-middle"></i>
                                            </button>
                                            <button type="button" class="btn btn-info waves-effect waves-light btn-sm" onclick="shareViaFacebook()">
                                                <i class="ri-facebook-line fs-5 align-middle"></i>
                                            </button>                                            <button type="button" class="btn btn-purple waves-effect waves-light btn-sm" onclick="shareViaInstagram()">
                                                <i class="ri-instagram-line fs-5 align-middle"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" onclick="shareViaEmail()">
                                                <i class="ri-mail-line fs-5 align-middle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Link Sharing Section -->

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
    <script src="/assets/js/app.js"></script>    <!-- Copy Link and Social Media Sharing Scripts -->
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
            // Add fade-in animation to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
</body>

</html>