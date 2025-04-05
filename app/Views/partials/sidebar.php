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
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('dashboard*') ? 'active' : '' ?>" href="<?= base_url() ?>dashboard">
                        <i class="ri-dashboard-line"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('submission*') ? 'active' : '' ?>" href="<?= base_url() ?>submission">
                        <i class="ri-file-upload-line"></i> <span>Submission</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('payments*') ? 'active' : '' ?>" href="<?= base_url() ?>payments">
                        <i class="ri-money-dollar-circle-line"></i> <span>Payments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('documents*') ? 'active' : '' ?>" href="#sidebarApps" data-bs-toggle="collapse" role="button"
                        aria-expanded="<?= url_is('documents*') ? 'true' : 'false' ?>" aria-controls="sidebarApps">
                        <i class="ri-apps-2-line"></i> <span>Documents</span>
                    </a>
                    <div class="collapse menu-dropdown <?= url_is('documents*') ? 'show' : '' ?>" id="sidebarApps">
                        <ul class="nav nav-sm flex-column">
                          
                            <li class="nav-item">
                                <a href="<?= base_url() ?>documents/program" class="nav-link <?= url_is('documents/program*') ? 'active' : '' ?>"> Program Documents </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= base_url() ?>documents/certificates" class="nav-link <?= url_is('documents/certificates*') ? 'active' : '' ?>"> Certificates </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- <li class="nav-item">
                    <a class="nav-link menu-link <?= url_is('settings*') ? 'active' : '' ?>" href="<?= base_url() ?>settings">
                        <i class="ri-money-dollar-circle-line"></i> <span>Settings</span>
                    </a>
                </li> -->

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>