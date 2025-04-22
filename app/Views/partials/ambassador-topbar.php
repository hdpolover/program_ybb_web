<?php
// No direct logic here - data is now provided by TopbarController
?>

<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="/" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?= isset($websetting['logo_url']) ? $websetting['logo_url'] : '/assets/images/logo-sm.png' ?>" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= isset($websetting['logo_url']) ? $websetting['logo_url'] : '/assets/images/logo-dark.png' ?>" alt="" height="17">
                        </span>
                    </a>

                    <a href="/" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?= isset($websetting['logo_url']) ? $websetting['logo_url'] : '/assets/images/logo-sm.png' ?>" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= isset($websetting['logo_url']) ? $websetting['logo_url'] : '/assets/images/logo-light.png' ?>" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <div class="d-flex align-items-center">

                <div class="d-flex align-items-center">

                    <div class="ms-1 header-item">
                        <div class="program-info px-3 py-2">
                            <div class="d-flex align-items-center">
                                <div class="text-start">
                                    <div class="fw-medium fs-14"><?= isset($currentProgram) && isset($currentProgram['name']) ? esc($currentProgram['name']) : 'No Program Selected' ?></div>

                                    <?php if (isset($currentProgram)): ?>
                                        <div class="d-flex align-items-center mt-1">
                                            <?php if (isset($currentProgram['is_active']) && $currentProgram['is_active']): ?>
                                                <span class="badge bg-success-subtle text-success fs-11 me-2">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger fs-11 me-2">Inactive</span>
                                            <?php endif; ?>

                                            <?php if (isset($currentProgram['start_date']) && !empty($currentProgram['start_date'])): ?>
                                                <span class="badge bg-light-subtle text-dark fs-11 me-2 py-1 px-2 border">
                                                    <i class="ri-calendar-line align-bottom"></i>
                                                    <?= date('M d, Y', strtotime($currentProgram['start_date'])) ?>
                                                    <?php if (isset($currentProgram['end_date']) && !empty($currentProgram['end_date'])): ?>
                                                        - <?= date('M d, Y', strtotime($currentProgram['end_date'])) ?>
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn btn-soft-primary rounded-pill px-3" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-user-id="<?= session()->get('user')['id'] ?? '' ?>">
                            <span class="d-flex align-items-center">
                                <span class="text-start">
                                    <span class="d-none d-xl-inline-block fw-semibold user-name-text"><?= isset($ambassador['name']) ? strtoupper($ambassador['name']) : 'Guest' ?></span>
                                    <span class="d-none d-xl-block fs-12 text-muted">
                                        <?php
                                        // Get email from user session data
                                        $email = $ambassador['email'];

                                        // Obscure the middle part of email
                                        if (!empty($email)) {
                                            $parts = explode('@', $email);
                                            if (count($parts) == 2) {
                                                $username = $parts[0];
                                                $domain = $parts[1];
                                                $len = strlen($username);
                                                if ($len > 3) {
                                                    $visible = min(3, $len - 3);
                                                    $stars = str_repeat('*', $len - $visible);
                                                    $obscuredEmail = substr($username, 0, $visible) . $stars . '@' . $domain;
                                                    echo $obscuredEmail;
                                                } else {
                                                    echo $username[0] . '***@' . $domain;
                                                }
                                            } else {
                                                echo 'User';
                                            }
                                        } else {
                                            echo 'User';
                                        }
                                        ?>
                                    </span>
                                </span>
                                <i class="ri-arrow-down-s-line ms-2 fs-18"></i>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg py-2">
                            <!-- User info section -->
                            <div class="p-3 border-bottom">
                                <div class="text-center">

                                    <h5 class="mb-1 fs-16"><?= isset($ambassador['name']) ? strtoupper($ambassador['name']) : 'Guest' ?></h5>
                                    <p class="text-muted mb-0 fs-13">
                                        Program Ambassador
                                    </p>
                                </div>
                            </div>

                            <!-- <a class="dropdown-item d-flex align-items-center px-3 py-2" href="#">
                                <i class="mdi mdi-account-outline text-primary fs-18 me-2"></i>
                                <span class="align-middle">My Profile</span>
                            </a>
                            <a class="dropdown-item d-flex align-items-center px-3 py-2" href="#">
                                <i class="mdi mdi-cog-outline text-primary fs-18 me-2"></i>
                                <span class="align-middle">Settings</span>
                            </a> -->
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item d-flex align-items-center px-3 py-2" href="<?= base_url('sign-out') ?>">
                                <i class="mdi mdi-logout text-primary fs-18 me-2"></i>
                                <span class="align-middle">Sign Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</header>

<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Topbar JavaScript -->
<script src="<?= base_url('assets/js/topbar.js') ?>"></script>