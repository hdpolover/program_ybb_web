<?= $this->include('partials/main') ?>

<head>

    <?= $this->include('partials/title-meta', ['meta_title' => "Announcements"]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Contact title section -->
        <section class="section position-relative pb-5 bg-light" id="contact-title">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Contact Us</h1>
                            <p class="text-muted fs-16">We're here to help and answer any questions you might have.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Contact title section -->

        <!-- start Contact section -->
        <section class="section mt-5 py-5 position-relative bg-light" id="contact">

            <div class="container">

                <div class="row gy-4">
                    <div class="col-lg-4">
                        <div class="card card-animate border-0 shadow-lg h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                                <i class="ri-map-pin-2-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fs-16 mb-1">Our Location</h5>
                                        <p class="text-muted mb-0">Connect with us in person</p>
                                    </div>
                                </div>
                                <p class="text-muted mb-4"><?= $program_info['address'] ?? '123 Main Street, City, Country, 12345' ?></p>

                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                                <i class="ri-mail-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fs-16 mb-1">Email Address</h5>
                                        <p class="text-muted mb-0">Send us an email</p>
                                    </div>
                                </div>
                                <p class="text-muted mb-4"><?= $program_info['email'] ?? 'info@example.com' ?></p>

                                <div class="d-flex align-items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-4">
                                                <i class="ri-phone-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fs-16 mb-1">Phone Number</h5>
                                        <p class="text-muted mb-0">Give us a call</p>
                                    </div>
                                </div>
                                <p class="text-muted mb-0"><?= $program_info['contact'] ?? '+1 (123) 456-7890' ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 shadow-lg">
                            <div class="card-header bg-soft-primary">
                                <h5 class="card-title mb-0">Send us a Message</h5>
                            </div>
                            <div class="card-body p-4">
                                <?php if (session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="ri-check-double-line me-2"></i> <?= session()->getFlashdata('success') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="ri-error-warning-line me-2"></i> <?= session()->getFlashdata('error') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" id="contactForm" action="<?= base_url('contact') ?>">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="floatingName" name="name" placeholder="Your Name" required>
                                                <label for="floatingName">Full Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="floatingEmail" name="email" placeholder="Your Email" required>
                                                <label for="floatingEmail">Email Address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="floatingSubject" name="subject" placeholder="Subject">
                                                <label for="floatingSubject">Subject</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" id="floatingSelect" name="interest" aria-label="Floating label select example">
                                                    <option selected value="">Choose...</option>
                                                    <option value="General Inquiry">General Inquiry</option>
                                                    <option value="Program Information">Program Information</option>
                                                    <option value="Registration">Registration</option>
                                                    <option value="Partnership">Partnership</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <label for="floatingSelect">I'm interested in</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" placeholder="Leave a message here" id="floatingTextarea" name="message" style="height: 150px" required></textarea>
                                                <label for="floatingTextarea">Your Message</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="ri-send-plane-line align-bottom me-1"></i> Send Message
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <div class="card border-0 bg-gradient-primary">
                                    <div class="card-body p-4">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm">
                                                    <div class="avatar-title bg-light text-primary rounded-circle fs-4">
                                                        <i class="ri-customer-service-2-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="text-white mb-1">Need Immediate Assistance?</h5>
                                                <p class="text-white-75 mb-0">Our support team is available Monday to Friday, 9:00 AM to 5:00 PM.</p>
                                            </div>
                                            <div class="flex-shrink-0 align-self-center">
                                                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $program_info['contact'] ?? '+1 (123) 456-7890') ?>" class="btn btn-light btn-sm">
                                                    <i class="ri-phone-line align-middle me-1"></i> Call Us
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Map Integration -->
                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-0">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.400933594999!2d106.82766337488115!3d-6.208829693800658!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f40ef1f2b23d%3A0x49c3d0818cc7317a!2sMonumen%20Nasional!5e0!3m2!1sen!2sid!4v1687347713522!5m2!1sen!2sid"
                                    style="border:0; width: 100%; height: 400px;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end Contact section -->

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- end layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Form validation with animation
            const contactForm = document.getElementById('contactForm');
            if (contactForm) {
                contactForm.addEventListener('submit', function(event) {
                    if (!contactForm.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();

                        // Animate the form shake for invalid submission
                        contactForm.closest('.card').classList.add('animate__animated', 'animate__shakeX');
                        setTimeout(function() {
                            contactForm.closest('.card').classList.remove('animate__animated', 'animate__shakeX');
                        }, 1000);

                        // Focus on the first invalid field
                        const invalidField = contactForm.querySelector(':invalid');
                        if (invalidField) {
                            invalidField.focus();
                        }
                    } else {
                        // Show loading state
                        const submitButton = contactForm.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...';
                        }
                    }

                    contactForm.classList.add('was-validated');
                });
            }

            // Auto dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function() {
                    alerts.forEach(alert => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);
            }

            // Card hover effects
            const cards = document.querySelectorAll('.card-animate');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('shadow-lg');
                    this.style.transform = 'translateY(-5px)';
                    this.style.transition = 'all 0.3s ease';
                });

                card.addEventListener('mouseleave', function() {
                    this.classList.remove('shadow-lg');
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>

</body>

</html>