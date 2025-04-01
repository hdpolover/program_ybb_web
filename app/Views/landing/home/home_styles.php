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