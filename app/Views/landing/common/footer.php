<!-- Start footer -->
<footer class="custom-footer bg-dark py-5 position-relative">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 mt-4">
                <div>
                    <div>
                        <img src="<?= $program_info['logo_url']; ?>" alt="logo light" height="50">
                    </div>
                    <div class="mt-4 fs-13 text-white">
                        <h5 class="text-white"><?= $program_info['name']; ?></h5>
                        <p class="ff-secondary text-white"><?= $program_info['tagline']; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 ms-lg-auto">
                <div class="row">
                    <div class="col-sm-7 mt-4">
                        <h5 class="text-white mb-0">Program Details</h5>
                        <div class="text-muted mt-3">
                            <ul class="list-unstyled ff-secondary footer-list">
                                <li>Location: <?= $program_info['location']; ?></li>
                                <li>Date: <?= date('F j, Y', strtotime($program_info['start_date'])); ?> - <?= date('F j, Y', strtotime($program_info['end_date'])); ?></li>
                                <li>Contact: <a href="mailto:<?= $program_info['email']; ?>"><?= $program_info['email']; ?></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-sm-5 text-center mt-4">
                        <h5 class="text-white mb-0">Contact Us</h5>
                        <div class="text-center mt-3">
                            <ul class="list-inline mb-0 footer-social-link">
                                <li class="list-inline-item">
                                    <a href="<?= $program_info['contact']; ?>" class="avatar-xs d-block" target="_blank">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-whatsapp-line"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="mailto:<?= $program_info['email']; ?>" class="avatar-xs d-block">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-mail-line"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="<?= $program_info['tiktok']; ?>" class="avatar-xs d-block" target="_blank">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-tiktok-line"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="<?= $program_info['instagram']; ?>" class="avatar-xs d-block" target="_blank">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-instagram-line"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="<?= $program_info['youtube']; ?>" class="avatar-xs d-block" target="_blank">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-youtube-line"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="<?= $program_info['telegram']; ?>" class="avatar-xs d-block" target="_blank">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-telegram-line"></i>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="row text-center text-sm-start align-items-center mt-5">
            <div class="col-sm-12">

                <div>
                    <p class="copy-rights mb-0">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © Youth Break the Boundaries Foundation
                    </p>
                </div>
            </div>

        </div>
    </div>
</footer>
<!-- end footer -->

<!--start back-to-top-->
<!-- <button onclick="topFunction()" class="btn btn-danger btn-icon landing-back-top" id="back-to-top">
            <i class="ri-arrow-up-line"></i>
        </button> -->
<!--end back-to-top-->

<!-- Floating buttons -->
<div class="floating-buttons">
    <a href="https://wa.me/yourwhatsappnumber" class="btn btn-success btn-icon" target="_blank" title="Chat on WhatsApp">
        <i class="ri-whatsapp-line"></i>
    </a>
    <a href="mailto:youremail@example.com" class="btn btn-primary btn-icon" title="Send Email">
        <i class="ri-mail-line"></i>
    </a>
</div>

<style>
    .floating-buttons {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .floating-buttons .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50px;
        padding: 15px 30px;
        color: #fff;
        text-decoration: none;
        font-size: 16px;
    }

    .floating-buttons .btn-icon {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .floating-buttons .btn .btn-text {
        display: inline;
    }
</style>

<style>
    .floating-buttons {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .floating-buttons .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50px;
        padding: 10px 20px;
        color: #fff;
        text-decoration: none;
    }

    .floating-buttons .btn-icon {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .floating-buttons .btn .btn-text {
        display: inline;
    }
</style>