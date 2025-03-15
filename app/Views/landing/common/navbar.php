<nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/'); ?>">
            <img src="<?= $program_info['logo_url'] ?>" class="card-logo" alt="logo" height="50">
        </a>
        <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="mdi mdi-menu"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example">
                <li class="nav-item">
                    <a class="nav-link <?= current_url() === base_url() ? 'active' : ''; ?>" href="<?= base_url(); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() === base_url('/about-us') ? 'active' : ''; ?>" href="<?= base_url("/about-us"); ?>">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() === base_url('/sponsorships') ? 'active' : ''; ?>" href="<?= base_url("/sponsorships"); ?>">Partnerships & Sponsorships</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() === base_url('/announcements') ? 'active' : ''; ?>" href="<?= base_url("/announcements"); ?>">Announcements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() === base_url('/faqs') ? 'active' : ''; ?>" href="<?= base_url("/faqs"); ?>">FAQs</a>
                </li>
            </ul>

            <div class="">
                <?php
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                ?>
                <a href="<?= $protocol . 'app.' . $program_info['web_url']; ?>" class="btn btn-primary" target="_blank">Get Started</a>
            </div>
        </div>

    </div>
</nav>
<!-- end navbar -->
<div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>