<nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/'); ?>">
            <img src="<?= $category['logo_url'] ?? '/assets/images/logo-dark.png' ?>" class="card-logo" alt="logo" height="50">
        </a>
        <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="mdi mdi-menu"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == '' || uri_string() == 'home' || strpos(uri_string(), 'home') === 0 ? 'active text-primary' : ''; ?>" href="<?= base_url(); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'programs') === 0 ? 'active text-primary' : ''; ?>" href="<?= base_url("programs"); ?>">Programs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'insights') === 0 ? 'active text-primary' : ''; ?>" href="<?= base_url("insights"); ?>">Insights</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'partners-sponsors') === 0 ? 'active text-primary' : ''; ?>" href="<?= base_url("partners-sponsors"); ?>">Partners & Sponsors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'help-news') === 0 ? 'active text-primary' : ''; ?>" href="<?= base_url("help-news"); ?>">Help & News</a>
                </li>
            </ul>

            <div class="">
                <a href="<?= base_url('sign-in'); ?>" class="btn btn-primary">Get Started</a>
            </div>
        </div>

    </div>
</nav>
<!-- end navbar -->
<div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>