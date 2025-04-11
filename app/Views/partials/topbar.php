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

                    <div class="dropdown ms-1 topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-ghost-secondary px-3" id="program-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="text-start">
                                    <span class="fw-medium fs-14"><?= isset($currentProgram) && isset($currentProgram['name']) ? esc($currentProgram['name']) : 'Select Program' ?></span>
                                    <i class="mdi mdi-chevron-down ms-1"></i>
                                </div>
                            </div>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="program-dropdown">
                            <!-- Program Header -->
                            <h6 class="dropdown-header">Select Program</h6>

                            <!-- List of Programs -->
                            <div class="dropdown-programs-container" style="max-height: 350px; overflow-y: auto; padding: 8px 0;">
                                <?php if (isset($sorted_programs) && is_array($sorted_programs) && count($sorted_programs) > 0): ?>
                                    <?php foreach ($sorted_programs as $program): ?>
                                        <?php
                                        // Check if participant is registered for this program
                                        $isRegistered = false;
                                        if (isset($participant_programs) && is_array($participant_programs)) {
                                            $isRegistered = in_array($program['id'] ?? null, $participant_programs);
                                        }
                                        ?>
                                        <a class="dropdown-item d-flex align-items-center <?= (isset($program['id']) && isset($currentProgramId) && $program['id'] == $currentProgramId) ? 'active' : '' ?>"
                                            href="<?= site_url('topbar/setProgram/' . $program['id']) ?>"
                                            data-program-id="<?= $program['id'] ?? '' ?>"
                                            data-registered="<?= $isRegistered ? '1' : '0' ?>"
                                            style="padding: 12px 15px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <div style="max-width: 85%;">
                                                    <span class="fw-medium"><?= isset($program['name']) ? esc($program['name']) : 'Unnamed Program' ?></span>

                                                    <div class="d-flex align-items-center mt-1">
                                                        <?php if (isset($program['is_active']) && $program['is_active']): ?>
                                                            <span class="badge bg-success-subtle text-success fs-11 me-2">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger-subtle text-danger fs-11 me-2">Inactive</span>
                                                        <?php endif; ?>
                                                        <?php if (!$isRegistered): ?>
                                                            <span class="badge bg-warning-subtle text-warning fs-11 me-2">Not Registered</span>
                                                        <?php endif; ?>

                                                        <?php if (isset($program['start_date']) && !empty($program['start_date'])): ?>
                                                            <span class="badge bg-light-subtle text-dark fs-11 me-2 py-1 px-2 border">
                                                                <i class="ri-calendar-line align-bottom"></i>
                                                                <?= date('M d, Y', strtotime($program['start_date'])) ?>
                                                                <?php if (isset($program['end_date']) && !empty($program['end_date'])): ?>
                                                                    - <?= date('M d, Y', strtotime($program['end_date'])) ?>
                                                                <?php endif; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>
                                            </div> <?php if (isset($program['id']) && isset($currentProgramId) && $program['id'] == $currentProgramId): ?>
                                                <i class="ri-checkbox-circle-fill text-white ms-2 fs-17"></i>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="dropdown-item py-3 text-center">No programs available</div>
                                <?php endif; ?>
                            </div>
                        </div>                    </div>
                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn btn-soft-primary rounded-pill px-3" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-user-id="<?= session()->get('user')['id'] ?? '' ?>">
                            <span class="d-flex align-items-center">
                                <span class="text-start">
                                    <span class="d-none d-xl-inline-block fw-semibold user-name-text"><?= isset($name) ? strtoupper($name) : 'Guest' ?></span>
                                    <span class="d-none d-xl-block fs-12 text-muted">
                                        <?php
                                        // Get email from user session data
                                        $email = session()->get('user')['email'] ?? '';
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
                                    <!-- <div class="avatar-md mx-auto mb-2">
                                        <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-2">
                                            <span><?= isset($name) && !empty($name) ? mb_substr($name, 0, 1) : 'G' ?></span>
                                        </div>
                                    </div> -->
                                    <h5 class="mb-1 fs-16"><?= isset($name) ? strtoupper($name) : 'Guest' ?></h5>
                                    <p class="text-muted mb-0 fs-13">
                                        <?= isset($currentProgram['name']) ? esc($currentProgram['name']) : 'Program Participant' ?>
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