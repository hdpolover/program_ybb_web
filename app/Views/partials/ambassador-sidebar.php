<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?= isset($webSettings['logo_url']) ? $webSettings['logo_url'] : '/assets/images/logo-sm.png' ?>" alt="" height="35">
            </span>
            <span class="logo-lg">
                <img src="<?= isset($webSettings['logo_url']) ? $webSettings['logo_url'] : '/assets/images/logo-dark.png' ?>" alt="" height="35">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="<?= isset($webSettings['logo_url']) ? $webSettings['logo_url'] : '/assets/images/logo-sm.png' ?>" alt="" height="35">
            </span>
            <span class="logo-lg">
                <img src="<?= isset($webSettings['logo_url']) ? $webSettings['logo_url'] : '/assets/images/logo-light.png' ?>" alt="" height="35">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <span class="hamburger-icon d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <i class="ri-record-circle-line d-none d-lg-block"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('ambassadors/dashboard*') ? 'active' : '' ?>" href="<?= base_url() ?>ambassadors/dashboard">
                        <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('ambassadors/referred-participants*') ? 'active' : '' ?>" href="<?= base_url() ?>ambassadors/referred-participants">
                        <i class="ri-user-line"></i> <span>Referred Participants</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('ambassadors/profile*') ? 'active' : '' ?>" href="<?= base_url() ?>ambassadors/profile">
                        <i class="ri-user-settings-line"></i> <span>Profile</span>
                    </a>
                </li>


            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>