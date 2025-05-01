<style>
    /* Program Styles */
    .programSwiper {
        padding: 10px 5px 30px;
    }

    .programSwiper .swiper-slide {
        height: auto;
        padding: 10px;
    }

    .program-img-wrapper {
        height: 180px;
        overflow: hidden;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }
      /* Custom styles for testimonials */
    .testimonial-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.98);
        max-width: 800px;
        margin: 0 auto;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
    }

    .testimonial-img-wrapper {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 0 auto;
        overflow: hidden;
    }

    .testimonial-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .testimonial-img:hover {
        transform: scale(1.05);
    }

    .testimony-text {
        font-size: 1.1rem;
        font-style: italic;
        line-height: 1.6;
        position: relative;
        max-width: 700px;
        margin: 0 auto;
    }

    .testimony-text::before,
    .testimony-text::after {
        content: '"';
        font-size: 1.5em;
        color: var(--bs-primary);
        opacity: 0.5;
        font-family: Georgia, serif;
    }

    .testimony-text::before {
        position: relative;
        margin-right: 5px;
    }

    .testimony-text::after {
        position: relative;
        margin-left: 5px;
    }

    .read-more-btn {
        transition: all 0.3s ease;
        padding: 0.5rem 1.5rem;
    }

    .read-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
    }

    /* Navigation buttons style */
    .testimonial-next-btn,
    .testimonial-prev-btn {
        color: #fff;
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .testimonial-next-btn:hover,
    .testimonial-prev-btn:hover {
        background-color: rgba(255, 255, 255, 0.6);
    }

    .testimonial-next-btn:after,
    .testimonial-prev-btn:after {
        font-size: 16px;
        font-weight: bold;
    }

    .swiper-navs {
        position: absolute;
        width: 100%;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        display: flex;
        justify-content: space-between;
        padding: 0 -10px;
    }

    /* Modal styles */
    .modal-content {
        border-radius: 15px;
        overflow: hidden;
        border: none;
    }

    .modal-header {
        background-color: var(--bs-primary);
        color: white;
        border-bottom: none;
    }

    .modal-header .modal-title {
        font-weight: 600;
    }

    .modal-header .btn-close {
        color: white;
        opacity: 1;
        box-shadow: none;
    }

    .testimonial-full-content {
        font-size: 1.1rem;
        line-height: 1.7;
    }

    /* Animation for swiper */
    .testimonial-swiper {
        padding-bottom: 50px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 767px) {
        .testimonial-img-wrapper {
            width: 150px;
            height: 150px;
        }
        
        .testimony-text {
            font-size: 1rem;
        }
        
        .testimonial-card {
            margin: 0 10px;
        }
    }

    .program-img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card:hover .program-img {
        transform: scale(1.05);
    }

    .program-img-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
    }

    .ribbon-shape {
        padding-right: 15px;
        padding-left: 15px;
        clip-path: polygon(0 0, 100% 0, 90% 100%, 0 100%);
    }

    .program-swiper-button-next,
    .program-swiper-button-prev {
        background-color: var(--vz-primary);
        color: #fff;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        transform: translateY(-50%);
    }

    .program-swiper-button-next:after,
    .program-swiper-button-prev:after {
        font-size: 14px;
        font-weight: bold;
    }

    .program-swiper-button-next:hover,
    .program-swiper-button-prev:hover {
        background-color: var(--vz-primary-darken-5);
    }
</style>