<!-- start Gallery section -->
<section class="section py-5 position-relative" id="gallery">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Our Gallery</h1>
                    <p class="text-muted">Explore images from our campus, events, and student activities.</p>
                </div>
            </div>
        </div>

        <!-- Gallery Filter Buttons -->
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-4">
                    <div class="filter-buttons">
                        <button class="btn btn-primary rounded-pill me-2 mb-2 filter-btn active" data-filter="all">All</button>
                        <button class="btn btn-outline-primary rounded-pill me-2 mb-2 filter-btn" data-filter="campus">Campus</button>
                        <button class="btn btn-outline-primary rounded-pill me-2 mb-2 filter-btn" data-filter="events">Events</button>
                        <button class="btn btn-outline-primary rounded-pill me-2 mb-2 filter-btn" data-filter="students">Students</button>
                        <button class="btn btn-outline-primary rounded-pill me-2 mb-2 filter-btn" data-filter="activities">Activities</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="row gallery-wrapper">
            <?php
            // Check if gallery data exists
            if (isset($gallery) && !empty($gallery)) {
                foreach ($gallery as $image) {
                    $category = isset($image['category']) ? strtolower($image['category']) : 'campus';
                    $title = isset($image['title']) ? $image['title'] : 'Gallery Image';
                    $description = isset($image['description']) ? $image['description'] : '';
                    $img_url = isset($image['image_url']) ? $image['image_url'] : '/assets/images/small/img-1.jpg';
            ?>
                    <div class="col-lg-4 col-md-6 gallery-item <?= $category ?> mb-4">
                        <div class="card gallery-card h-100 border-0 shadow-sm">
                            <div class="gallery-img-container position-relative overflow-hidden">
                                <img src="<?= $img_url ?>" class="card-img-top gallery-img" alt="<?= $title ?>">
                                <div class="gallery-overlay">
                                    <div class="gallery-actions">
                                        <a href="<?= $img_url ?>" class="btn btn-icon btn-sm btn-soft-light gallery-popup">
                                            <i class="ri-zoom-in-line"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-sm btn-soft-light gallery-info" data-title="<?= $title ?>" data-description="<?= $description ?>">
                                            <i class="ri-information-line"></i>
                                        </a>
                                    </div>
                                    <div class="category-badge">
                                        <span class="badge rounded-pill bg-primary"><?= ucfirst($category) ?></span>
                                    </div>
                                    <h5 class="gallery-title text-white"><?= $title ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                // Dummy gallery images if no data provided
                $categories = ['campus', 'events', 'students', 'activities'];
                $titles = [
                    'campus' => ['Main Campus Building', 'Library Interior', 'Student Center', 'Technology Lab', 'Campus Garden'],
                    'events' => ['Graduation Ceremony', 'Annual Conference', 'Guest Speaker Series', 'Workshop Session', 'Networking Event'],
                    'students' => ['Group Project Work', 'Student Lounge', 'Study Session', 'International Students', 'Student Awards'],
                    'activities' => ['Hackathon Competition', 'Team Building Exercise', 'Community Service', 'Sports Tournament', 'Art Exhibition']
                ];

                for ($i = 1; $i <= 12; $i++) {
                    $randomCat = $categories[array_rand($categories)];
                    $randomTitle = $titles[$randomCat][array_rand($titles[$randomCat])];
                ?>
                    <div class="col-lg-4 col-md-6 gallery-item <?= $randomCat ?> mb-4">
                        <div class="card gallery-card h-100 border-0 shadow-sm">
                            <div class="gallery-img-container position-relative overflow-hidden">
                                <img src="/assets/images/small/img-<?= $i ?>.jpg" class="card-img-top gallery-img" alt="<?= $randomTitle ?>">
                                <div class="gallery-overlay">
                                    <div class="gallery-actions">
                                        <a href="/assets/images/small/img-<?= $i ?>.jpg" class="btn btn-icon btn-sm btn-soft-light gallery-popup">
                                            <i class="ri-zoom-in-line"></i>
                                        </a>
                                        <a href="#" class="btn btn-icon btn-sm btn-soft-light gallery-info" data-title="<?= $randomTitle ?>" data-description="Discover the vibrant environment and engaging experiences at our institution.">
                                            <i class="ri-information-line"></i>
                                        </a>
                                    </div>
                                    <div class="category-badge">
                                        <span class="badge rounded-pill bg-primary"><?= ucfirst($randomCat) ?></span>
                                    </div>
                                    <h5 class="gallery-title text-white"><?= $randomTitle ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>

        <!-- Gallery Info Modal -->
        <div class="modal fade" id="galleryInfoModal" tabindex="-1" aria-labelledby="galleryInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="galleryInfoModalLabel">Image Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 id="modal-image-title"></h5>
                        <p id="modal-image-description" class="text-muted"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        <div class="row mt-4">
            <div class="col-lg-12 text-center">
                <button id="load-more" class="btn btn-primary rounded-pill">
                    Load More <i class="ri-arrow-down-line align-middle ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</section>
<!-- end Gallery section -->

<style>
    /* Gallery Card Styles */
    .gallery-card {
        transition: all 0.3s ease;
        overflow: hidden;
        border-radius: 10px;
    }

    .gallery-img-container {
        height: 250px;
    }

    .gallery-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .gallery-card:hover .gallery-img {
        transform: scale(1.1);
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.7) 100%);
        opacity: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .gallery-card:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .gallery-title {
        margin: 0;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .gallery-card:hover .gallery-title {
        transform: translateY(0);
    }

    .category-badge {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateY(-10px);
    }

    .gallery-card:hover .category-badge {
        opacity: 1;
        transform: translateY(0);
    }

    .filter-buttons {
        margin-bottom: 2rem;
    }

    .filter-btn {
        transition: all 0.3s ease;
    }

    .btn-soft-light {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: none;
    }

    .btn-soft-light:hover {
        background-color: rgba(255, 255, 255, 0.4);
        color: #fff;
    }

    /* Animation for items when filtered */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fadeIn {
        animation: fadeIn 0.5s forwards;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize lightbox for gallery images
        const lightbox = GLightbox({
            selector: '.gallery-popup',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });

        // Gallery filtering functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(innerBtn => {
                    innerBtn.classList.remove('active');
                    if (innerBtn.classList.contains('btn-primary')) {
                        innerBtn.classList.remove('btn-primary');
                        innerBtn.classList.add('btn-outline-primary');
                    }
                });

                // Add active class to clicked button
                this.classList.add('active');
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');

                const filter = this.getAttribute('data-filter');

                // Show/hide gallery items based on filter
                let delay = 0;
                galleryItems.forEach(item => {
                    if (filter === 'all' || item.classList.contains(filter)) {
                        item.style.display = 'block';
                        item.style.opacity = '0';
                        setTimeout(() => {
                            item.classList.add('fadeIn');
                            item.style.opacity = '1';
                        }, delay);
                        delay += 100;
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('fadeIn');
                    }
                });
            });
        });

        // Gallery info modal functionality
        const galleryInfoBtns = document.querySelectorAll('.gallery-info');
        const galleryInfoModal = new bootstrap.Modal(document.getElementById('galleryInfoModal'));

        galleryInfoBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const title = this.getAttribute('data-title');
                const description = this.getAttribute('data-description');

                document.getElementById('modal-image-title').textContent = title;
                document.getElementById('modal-image-description').textContent = description;

                galleryInfoModal.show();
            });
        });

        // Load more functionality - simulation
        const loadMoreBtn = document.getElementById('load-more');
        let clickCount = 0;

        loadMoreBtn.addEventListener('click', function() {
            clickCount++;
            if (clickCount >= 2) {
                this.disabled = true;
                this.innerHTML = 'No More Images <i class="ri-check-line align-middle ms-1"></i>';
                this.classList.remove('btn-primary');
                this.classList.add('btn-light');
                return;
            }

            // Simulate loading more images
            const galleryWrapper = document.querySelector('.gallery-wrapper');
            const loadingIndicator = document.createElement('div');
            loadingIndicator.className = 'text-center py-4';
            loadingIndicator.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

            galleryWrapper.after(loadingIndicator);

            setTimeout(() => {
                loadingIndicator.remove();

                const categories = ['campus', 'events', 'students', 'activities'];
                const titles = {
                    'campus': ['Modern Auditorium', 'Science Building', 'Recreation Center', 'Outdoor Study Area'],
                    'events': ['Career Fair', 'Alumni Reunion', 'Cultural Festival', 'Technology Exhibition'],
                    'students': ['Classroom Discussion', 'Research Presentation', 'Student Council', 'International Exchange'],
                    'activities': ['Debate Club', 'Robotics Competition', 'Music Performance', 'Volunteer Project']
                };

                let html = '';

                for (let i = 1; i <= 6; i++) {
                    const randomCat = categories[Math.floor(Math.random() * categories.length)];
                    const randomTitle = titles[randomCat][Math.floor(Math.random() * titles[randomCat].length)];
                    const imgNum = Math.floor(Math.random() * 12) + 1;

                    html += `
                <div class="col-lg-4 col-md-6 gallery-item ${randomCat} mb-4 fadeIn">
                    <div class="card gallery-card h-100 border-0 shadow-sm">
                        <div class="gallery-img-container position-relative overflow-hidden">
                            <img src="/assets/images/small/img-${imgNum}.jpg" class="card-img-top gallery-img" alt="${randomTitle}">
                            <div class="gallery-overlay">
                                <div class="gallery-actions">
                                    <a href="/assets/images/small/img-${imgNum}.jpg" class="btn btn-icon btn-sm btn-soft-light gallery-popup">
                                        <i class="ri-zoom-in-line"></i>
                                    </a>
                                    <a href="#" class="btn btn-icon btn-sm btn-soft-light gallery-info" data-title="${randomTitle}" data-description="Discover the vibrant environment and engaging experiences at our institution.">
                                        <i class="ri-information-line"></i>
                                    </a>
                                </div>
                                <div class="category-badge">
                                    <span class="badge rounded-pill bg-primary">${randomCat.charAt(0).toUpperCase() + randomCat.slice(1)}</span>
                                </div>
                                <h5 class="gallery-title text-white">${randomTitle}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                }

                galleryWrapper.insertAdjacentHTML('beforeend', html);

                // Reinitialize lightbox for new gallery items
                lightbox.reload();

                // Attach event listeners to new info buttons
                const newGalleryInfoBtns = document.querySelectorAll('.gallery-info:not([data-initialized])');
                newGalleryInfoBtns.forEach(btn => {
                    btn.setAttribute('data-initialized', 'true');
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const title = this.getAttribute('data-title');
                        const description = this.getAttribute('data-description');

                        document.getElementById('modal-image-title').textContent = title;
                        document.getElementById('modal-image-description').textContent = description;

                        galleryInfoModal.show();
                    });
                });

                // Apply filter if one is active
                const activeFilter = document.querySelector('.filter-btn.active');
                if (activeFilter) {
                    const filter = activeFilter.getAttribute('data-filter');
                    if (filter !== 'all') {
                        document.querySelectorAll('.gallery-item').forEach(item => {
                            if (!item.classList.contains(filter)) {
                                item.style.display = 'none';
                            }
                        });
                    }
                }
            }, 1500);
        });
    });
</script>