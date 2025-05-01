<!--Swiper slider js-->
<script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

<!-- Landing init -->
<script src="/assets/js/pages/landing.init.js"></script>

<!-- Gallery Modal init -->
<script src="/assets/js/pages/gallery-modal.init.js"></script>

<script>    // Initialize Swiper for testimonials
    var testimonialSwiper = new Swiper(".testimonial-swiper", {
        spaceBetween: 30,
        loop: true,
        slidesPerView: 1,
        centeredSlides: true,
        effect: "fade",
        fadeEffect: {
            crossFade: true
        },
        grabCursor: true,
        autoHeight: true,
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".testimonial-next-btn",
            prevEl: ".testimonial-prev-btn",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
            dynamicBullets: true,
        }
    });

    // Initialize Swiper for programs horizontal list
    var programSwiper = new Swiper(".programSwiper", {
        slidesPerView: 1,
        spaceBetween: 20,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".program-swiper-button-next",
            prevEl: ".program-swiper-button-prev",
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 30,
            },
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
    });

    function formatEventDate() {
        <?php if (isset($program_info['start_date']) && isset($program_info['end_date'])): ?>
            const startDate = new Date("<?= $program_info['start_date']; ?>");
            const endDate = new Date("<?= $program_info['end_date']; ?>");

            const options = {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            };

            const startFormatted = startDate.toLocaleDateString('en-US', options);
            const endFormatted = endDate.toLocaleDateString('en-US', options);

            document.getElementById("event_date_display").innerHTML = startFormatted + " - " + endFormatted;
        <?php else: ?>
            document.getElementById("event_date_display").innerHTML = "Date to be announced";
        <?php endif; ?>
    }

    formatEventDate(); // Call the function when page loads

    function updateCountdown() {
        <?php if (isset($program_info['end_date'])): ?>
            const eventDate = new Date("<?= $program_info['end_date']; ?>").getTime();
            const now = new Date().getTime();
            const diff = eventDate - now;

            if (diff > 0) {
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML = `Registration ends in ${days} days ${hours} hours ${minutes} minutes ${seconds} seconds`;
            } else {
                document.getElementById("countdown").innerHTML = "Registration has ended";
            }
        <?php else: ?>
            document.getElementById("countdown").innerHTML = "Registration deadline to be announced";
        <?php endif; ?>
    }

    setInterval(updateCountdown, 1000);
    updateCountdown(); // Initial call to display the timer immediately
</script>