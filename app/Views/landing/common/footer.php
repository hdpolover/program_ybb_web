<!-- start footer -->
<footer class="custom-footer bg-dark py-5 position-relative">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="mb-4">
                    <a href="<?= base_url() ?>" class="d-flex align-items-center mb-3">
                        <?php if (isset($program_info['logo_url']) && !empty($program_info['logo_url'])) : ?>
                            <img src="<?= $program_info['logo_url'] ?>" alt="Logo" height="40" class="me-2">
                        <?php else : ?>
                            <i class="ri-graduation-cap-line text-primary fs-24 me-2"></i>
                        <?php endif; ?>
                        <h3 class="text-white fw-semibold mb-0"><?= $program_info['name'] ?? 'Program Name' ?></h3>
                    </a>
                    <p class="text-white-50 mb-4 fs-15"><?= $program_info['tagline'] ?? 'Empowering through education' ?></p>
                    
                    <h5 class="text-white mb-3">Connect with us</h5>
                    <div class="d-flex gap-2">
                        <?php if (isset($program_info['social_facebook']) && !empty($program_info['social_facebook'])) : ?>
                            <a href="<?= $program_info['social_facebook'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-facebook-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($program_info['social_twitter']) && !empty($program_info['social_twitter'])) : ?>
                            <a href="<?= $program_info['social_twitter'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-twitter-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($program_info['social_linkedin']) && !empty($program_info['social_linkedin'])) : ?>
                            <a href="<?= $program_info['social_linkedin'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-linkedin-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($program_info['social_instagram']) && !empty($program_info['social_instagram'])) : ?>
                            <a href="<?= $program_info['social_instagram'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-instagram-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($program_info['social_youtube']) && !empty($program_info['social_youtube'])) : ?>
                            <a href="<?= $program_info['social_youtube'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-youtube-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <div class="mb-4">
                    <h5 class="text-white mb-3">Quick Links</h5>
                    <ul class="list-unstyled footer-list">
                        <li><a href="<?= base_url() ?>"><i class="ri-arrow-right-s-line me-1 align-middle"></i> Home</a></li>
                        <li><a href="<?= base_url('about') ?>"><i class="ri-arrow-right-s-line me-1 align-middle"></i> About</a></li>
                        <li><a href="<?= base_url('programs') ?>"><i class="ri-arrow-right-s-line me-1 align-middle"></i> Programs</a></li>
                        <li><a href="<?= base_url('insights') ?>"><i class="ri-arrow-right-s-line me-1 align-middle"></i> Insights</a></li>
                        <li><a href="<?= base_url('contact') ?>"><i class="ri-arrow-right-s-line me-1 align-middle"></i> Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="mb-4">
                    <h5 class="text-white mb-3">Useful Resources</h5>
                    <ul class="list-unstyled footer-list">
                        <li>
                            <a href="<?= base_url('faq') ?>">
                                <i class="ri-arrow-right-s-line me-1 align-middle"></i> FAQ
                            </a>
                        </li>
                        <li>
                            <a href="<?= $program_info['guideline'] ?? '#' ?>" target="_blank">
                                <i class="ri-arrow-right-s-line me-1 align-middle"></i> Program Guidelines
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('privacy-policy') ?>">
                                <i class="ri-arrow-right-s-line me-1 align-middle"></i> Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('terms-conditions') ?>">
                                <i class="ri-arrow-right-s-line me-1 align-middle"></i> Terms & Conditions
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('sitemap.xml') ?>">
                                <i class="ri-arrow-right-s-line me-1 align-middle"></i> Sitemap
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="mb-4">
                    <h5 class="text-white mb-3">Subscribe to Our Newsletter</h5>
                    <p class="text-white-50 mb-3 fs-15">Subscribe to our newsletter to receive updates and news about our programs.</p>
                    <form id="newsletterForm" action="<?= base_url('subscribe') ?>" method="post">
                        <div class="position-relative">
                            <input type="email" class="form-control" placeholder="Enter your email" required>
                            <button type="submit" class="btn btn-primary position-absolute top-0 end-0">
                                <i class="ri-send-plane-2-fill"></i>
                            </button>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="privacyCheck" required>
                            <label class="form-check-label text-white-50 fs-13" for="privacyCheck">
                                I agree to the <a href="<?= base_url('privacy-policy') ?>" class="text-white">privacy policy</a>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="mt-4 pt-4 border-top border-white-50">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="text-white-50">
                                <p class="mb-0">&copy; <?= date('Y') ?> <?= $program_info['name'] ?? 'Program Name' ?>. All rights reserved.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end text-white-50">
                                <p class="mb-0">Designed with <i class="mdi mdi-heart text-danger"></i> by <a href="https://themesbrand.com/" target="_blank" class="text-reset text-decoration-underline">Themesbrand</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- end footer -->

<button class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>

<style>
/* Footer custom styling */
.custom-footer {
    background-color: #0b1729 !important;
    position: relative;
}

.custom-footer::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('/assets/images/footer-bg.png');
    background-size: cover;
    background-position: center;
    opacity: 0.05;
}

.footer-list li {
    margin-bottom: 10px;
}

.footer-list li a {
    color: rgba(255, 255, 255, 0.5);
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-list li a:hover {
    color: #fff;
    transform: translateX(5px);
    text-decoration: none;
}

#back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 99;
    display: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    justify-content: center;
    align-items: center;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Back to top button
    var backToTopBtn = document.getElementById("back-to-top");
    
    window.addEventListener("scroll", function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = "flex";
        } else {
            backToTopBtn.style.display = "none";
        }
    });
    
    backToTopBtn.addEventListener("click", function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    // Newsletter form 
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would normally send the form data via AJAX
            const email = this.querySelector('input[type="email"]').value;
            
            // For demo: Add success message
            const formHTML = this.innerHTML;
            this.innerHTML = '<div class="alert alert-success mb-0">Thank you for subscribing!</div>';
            
            // Reset form after 3 seconds
            setTimeout(() => {
                this.innerHTML = formHTML;
                this.querySelector('input[type="email"]').value = '';
            }, 3000);
        });
    }
});
</script>