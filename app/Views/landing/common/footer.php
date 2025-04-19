<!-- start footer -->
<footer class="custom-footer bg-dark py-5 position-relative">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="mb-4">
                    <a href="<?= base_url() ?>" class="d-flex align-items-center mb-3">
                        <?php if (isset($category['logo_url']) && !empty($category['logo_url'])) : ?>
                            <img src="<?= $category['logo_url'] ?>" alt="Logo" height="40" class="me-2">
                        <?php else : ?>
                            <i class="ri-graduation-cap-line text-primary fs-24 me-2"></i>
                        <?php endif; ?>
                    </a>

                    <h4 class="text-white fw-semibold mb-0"><?= $category['name'] ?? 'Program Name' ?></h3>

                    <br>

                    <p class="text-white-50 mb-4 fs-15"><?= $category['tagline'] ?? 'Empowering through education' ?></p>
                    
                    <p class="text-white mb-3">Connect with us</p>
                    <div class="d-flex gap-2">
                        <?php if (isset($category['email']) && !empty($category['email'])) : ?>
                            <a href="mailto:<?= $category['email'] ?>" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-mail-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['instagram']) && !empty($category['instagram'])) : ?>
                            <a href="<?= $category['instagram'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-instagram-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['tiktok']) && !empty($category['tiktok'])) : ?>
                            <a href="<?= $category['tiktok'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-tiktok-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['youtube']) && !empty($category['youtube'])) : ?>
                            <a href="<?= $category['youtube'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-youtube-fill"></i>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($category['telegram']) && !empty($category['telegram'])) : ?>
                            <a href="<?= $category['telegram'] ?>" target="_blank" class="avatar-xs d-block">
                                <span class="avatar-title rounded-circle bg-soft-light text-white fs-16">
                                    <i class="ri-telegram-fill"></i>
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
                        <li><a href="<?= base_url() ?>">Home</a></li>
                        <li><a href="<?= base_url('about') ?>">About</a></li>
                        <li><a href="<?= base_url('programs') ?>">Programs</a></li>
                        <li><a href="<?= base_url('insights') ?>">Insights</a></li>
                        <li><a href="<?= base_url('contact') ?>">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="mb-4">
                    <h5 class="text-white mb-3">Useful Resources</h5>
                    <ul class="list-unstyled footer-list">
                        <li>
                            <a href="<?= base_url('privacy-policy') ?>">Privacy Policy</a>
                        </li>
                        <li>
                            <a href="<?= base_url('terms-conditions') ?>">Terms & Conditions</a>
                        </li>
                        <li>
                            <a href="<?= base_url('sitemap.xml') ?>">Sitemap</a>
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
                                <p class="mb-0">&copy; <?= date('Y') ?> <?= $category['name'] ?? 'Program Name' ?>. All rights reserved.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end text-white-50">
                                <p class="mb-0">Designed with <i class="mdi mdi-heart text-danger"></i> by <a href="#" class="text-reset text-decoration-underline">YBB Dev Team</a></p>
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

<!-- Toast Notification Container -->
<div id="toast-container"></div>

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

/* Custom toast styling */
.toastify.notification-toast {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    border-left: 4px solid #1e40af;
    padding: 12px 20px;
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
    font-family: inherit;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toastify.notification-toast:hover {
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);
    transform: translateY(-2px);
}

.toastify.notification-toast .toast-content {
    display: flex;
    align-items: center;
}

.toastify.notification-toast .toast-icon {
    margin-right: 16px;
    background-color: rgba(255, 255, 255, 0.25);
    height: 34px;
    width: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.toastify.notification-toast .toast-icon i {
    font-size: 18px;
}

.toastify.notification-toast .toast-message {
    flex-grow: 1;
    line-height: 1.4;
}

.toastify.notification-toast .notification-message {
    font-size: 0.95rem;
    font-weight: 500;
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
    }    // Registration Toast Notifications
    function showRegistrationToast() {
        // Fetch recent registration data
        fetch('<?= base_url('popup-notification/getRecentRegistrations') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.notif) {
                    const notifMessage = data.data.notif;
                    
                    // Create toast content with HTML - simple message display
                    const toastContent = `
                        <div class="toast-content">
                            <div class="toast-icon">
                                <i class="ri-notification-line"></i>
                            </div>
                            <div class="toast-message">
                                <span class="notification-message">${notifMessage}</span>
                            </div>
                        </div>
                    `;
                      // Show toast notification
                    Toastify({
                        node: (() => {
                            const div = document.createElement("div");
                            div.innerHTML = toastContent;
                            return div;
                        })(),
                        className: "notification-toast",
                        gravity: "bottom",
                        position: "left",
                        duration: 5000,
                        close: false, // Remove close button
                        stopOnFocus: true,
                      
                    }).showToast();
                }
            })
            .catch(error => console.error('Error fetching notification data:', error));
        
        // Schedule next toast with random interval between 30 seconds and 1 minute
        const nextInterval = Math.floor(Math.random() * (60000 - 30000 + 1)) + 30000;
        setTimeout(showRegistrationToast, nextInterval);
    }
    
    // Start showing registration toasts after a short delay
    setTimeout(showRegistrationToast, 3000);
});
</script>