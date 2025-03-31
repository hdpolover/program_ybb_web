<!-- start About section -->
<section class="section py-5 position-relative" id="about">
    <div class="bg-overlay bg-overlay-pattern opacity-25"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">About Our Program</h1>
                    <p class="text-muted fs-16">Learn more about our mission, values, and the impact we're making.</p>
                </div>
            </div>
        </div>

        <!-- Program Overview Section -->
        <div class="row align-items-center gy-4 mb-5">
            <div class="col-lg-6 order-2 order-lg-1">
                <div class="text-muted">
                    <h2 class="mb-3 fw-semibold text-capitalize">Our Mission</h2>
                    <p class="mb-4 ff-secondary fs-16">
                        <?= isset($about['mission']) ? $about['mission'] : 'Our mission is to empower individuals through quality education and training programs that develop skills, knowledge, and attitudes necessary for success in a rapidly changing world.' ?>
                    </p>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar-xs">
                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            <i class="ri-check-double-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-14 mb-0">Excellence in Education</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar-xs">
                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            <i class="ri-check-double-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-14 mb-0">Innovative Approaches</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar-xs">
                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            <i class="ri-check-double-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-14 mb-0">Community Engagement</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0 me-2">
                                    <div class="avatar-xs">
                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            <i class="ri-check-double-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-14 mb-0">Global Perspective</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="<?= base_url('contact') ?>" class="btn btn-primary">Contact Us <i class="ri-arrow-right-line align-bottom ms-1"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2">
                <div class="card overflow-hidden shadow-lg">
                    <div class="position-relative">
                        <img src="<?= isset($about['image_url']) ? $about['image_url'] : '/assets/images/about-image.jpg' ?>" alt="About Image" class="card-img-top" style="height: 350px; object-fit: cover;">
                        <div class="card-img-overlay bg-dark bg-opacity-25 d-flex align-items-end">
                            <div class="badge bg-primary fs-16 position-absolute top-0 end-0 m-3">Since <?= isset($about['established_year']) ? $about['established_year'] : date('Y') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program History Timeline -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-soft-primary">
                        <h5 class="card-title mb-0">Our History</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="hori-timeline">
                            <div class="swiper timelineSwiper">
                                <div class="d-flex align-items-start gap-3 mb-4">
                                    <div>
                                        <h4 class="fw-semibold">Key Milestones</h4>
                                        <p class="text-muted mb-0">Our journey through the years</p>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="swiper-button-prev rounded-circle timeline-nav-btn" id="history-prev-btn"></div>
                                        <div class="swiper-button-next rounded-circle timeline-nav-btn" id="history-next-btn"></div>
                                    </div>
                                </div>
                                <div class="swiper-wrapper">
                                    <?php 
                                    $milestones = isset($about['milestones']) ? $about['milestones'] : [
                                        ['year' => '2018', 'title' => 'Program Founded', 'description' => 'Our program was established with a clear vision to provide quality education.'],
                                        ['year' => '2019', 'title' => 'First Batch Graduates', 'description' => 'Successfully graduated our first batch of participants with high success rate.'],
                                        ['year' => '2020', 'title' => 'Expanded Offerings', 'description' => 'Added new courses and expanded our program offerings to reach more participants.'],
                                        ['year' => '2021', 'title' => 'International Recognition', 'description' => 'Received international recognition for our educational approach and methods.'],
                                        ['year' => '2022', 'title' => 'Digital Transformation', 'description' => 'Embraced digital tools and technologies to enhance learning experiences.'],
                                        ['year' => '2023', 'title' => 'Global Partnerships', 'description' => 'Formed strategic partnerships with global organizations to broaden our impact.']
                                    ];
                                    
                                    foreach ($milestones as $milestone) :
                                    ?>
                                    <div class="swiper-slide">
                                        <div class="card timeline-card border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="timeline-year bg-soft-primary text-primary"><?= $milestone['year'] ?></div>
                                                <h5 class="mt-3"><?= $milestone['title'] ?></h5>
                                                <p class="text-muted mb-0"><?= $milestone['description'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h2 class="fw-semibold">Our Leadership Team</h2>
                    <p class="text-muted">Meet the dedicated individuals who lead our program.</p>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <?php 
            $team_members = isset($about['team']) ? $about['team'] : [
                ['name' => 'John Doe', 'position' => 'Program Director', 'bio' => 'With over 15 years of experience in educational leadership, John leads our program with vision and dedication.', 'image' => '/assets/images/users/avatar-1.jpg'],
                ['name' => 'Jane Smith', 'position' => 'Academic Coordinator', 'bio' => 'Jane brings her expertise in curriculum development and pedagogical innovations to enhance our program quality.', 'image' => '/assets/images/users/avatar-2.jpg'],
                ['name' => 'Michael Johnson', 'position' => 'Outreach Manager', 'bio' => 'Michael manages our partnerships and community relations, expanding our program reach and impact.', 'image' => '/assets/images/users/avatar-3.jpg'],
                ['name' => 'Sarah Williams', 'position' => 'Student Success Lead', 'bio' => 'Sarah ensures our participants receive the support they need to succeed in their educational journey.', 'image' => '/assets/images/users/avatar-4.jpg']
            ];
            
            foreach ($team_members as $member) :
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="card team-card card-animate border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="team-img-wrapper">
                            <img src="<?= $member['image'] ?>" alt="<?= $member['name'] ?>" class="team-img rounded-circle avatar-xl mb-3">
                            <ul class="team-social-links list-inline position-absolute top-0 end-0 mt-3 me-3">
                                <li class="list-inline-item">
                                    <a href="#" class="avatar-xs d-block bg-soft-primary text-primary rounded-circle fs-16 team-social-icon">
                                        <i class="ri-linkedin-fill"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="avatar-xs d-block bg-soft-info text-info rounded-circle fs-16 team-social-icon">
                                        <i class="ri-twitter-fill"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <h5 class="fs-16 mb-1"><?= $member['name'] ?></h5>
                        <p class="text-primary mb-3"><?= $member['position'] ?></p>
                        <p class="text-muted mb-0"><?= $member['bio'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Testimonials Section -->
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h2 class="fw-semibold">What Our Participants Say</h2>
                    <p class="text-muted">Hear from the people who have experienced our program.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="swiper testimonialsSwiper">
                    <div class="swiper-wrapper">
                        <?php 
                        $testimonials = isset($about['testimonials']) ? $about['testimonials'] : [
                            ['name' => 'Robert Chen', 'role' => 'Program Participant, 2022', 'comment' => 'This program has transformed my career opportunities and provided me with valuable skills that I use every day in my job.', 'image' => '/assets/images/users/avatar-5.jpg'],
                            ['name' => 'Maria Garcia', 'role' => 'Alumni, 2021', 'comment' => 'The mentorship I received during this program was exceptional. The instructors truly care about student success and go above and beyond.', 'image' => '/assets/images/users/avatar-6.jpg'],
                            ['name' => 'David Kim', 'role' => 'Current Participant', 'comment' => 'I\'m amazed by the quality of the curriculum and the hands-on approach to learning. This program exceeds all my expectations.', 'image' => '/assets/images/users/avatar-7.jpg'],
                            ['name' => 'Emily Johnson', 'role' => 'Program Graduate, 2020', 'comment' => 'The network I built during this program has been invaluable. I still collaborate with fellow participants on various projects.', 'image' => '/assets/images/users/avatar-8.jpg']
                        ];
                        
                        foreach ($testimonials as $testimonial) :
                        ?>
                        <div class="swiper-slide">
                            <div class="card testimonial-card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $testimonial['image'] ?>" alt="<?= $testimonial['name'] ?>" class="rounded-circle avatar-md">
                                        <div class="ms-3">
                                            <h5 class="fs-16 mb-1"><?= $testimonial['name'] ?></h5>
                                            <p class="text-muted mb-0"><?= $testimonial['role'] ?></p>
                                        </div>
                                        <div class="fs-20 ms-auto text-warning">
                                            <i class="ri-double-quotes-l"></i>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-3 mb-0"><?= $testimonial['comment'] ?></p>
                                    <div class="mt-3">
                                        <span class="badge rounded-pill bg-soft-warning text-warning">
                                            <i class="ri-star-s-fill me-1"></i>
                                            <i class="ri-star-s-fill me-1"></i>
                                            <i class="ri-star-s-fill me-1"></i>
                                            <i class="ri-star-s-fill me-1"></i>
                                            <i class="ri-star-s-fill"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination testimonial-pagination mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end About section -->

<style>
/* Timeline Styles */
.timeline-nav-btn {
    width: 36px !important;
    height: 36px !important;
    font-size: 18px !important;
    background-color: var(--vz-primary) !important;
    color: #fff !important;
    opacity: 0.65;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.timeline-nav-btn:hover {
    opacity: 1;
}

.timeline-nav-btn::after {
    font-size: 14px !important;
}

.timeline-year {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Team Card Styles */
.team-card {
    transition: all 0.3s ease;
}

.team-card:hover {
    transform: translateY(-10px);
}

.team-img-wrapper {
    position: relative;
}

.team-social-links {
    opacity: 0;
    transition: all 0.3s ease;
}

.team-card:hover .team-social-links {
    opacity: 1;
}

.team-social-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.team-social-icon:hover {
    transform: scale(1.2);
}

/* Testimonial Styles */
.testimonial-card {
    transition: all 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize the History Timeline Swiper
    var timelineSwiper = new Swiper(".timelineSwiper", {
        slidesPerView: 1,
        spaceBetween: 25,
        loop: false,
        navigation: {
            nextEl: "#history-next-btn",
            prevEl: "#history-prev-btn",
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });

    // Initialize Testimonials Swiper
    var testimonialsSwiper = new Swiper(".testimonialsSwiper", {
        slidesPerView: 1,
        spaceBetween: 25,
        loop: true,
        pagination: {
            el: ".testimonial-pagination",
            clickable: true,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
});
</script>