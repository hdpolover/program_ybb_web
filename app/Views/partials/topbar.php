<?php
$participants = session()->get('participants') ?? [];
$participant = !empty($participants) ? $participants[0] : null;
$profileImage = ($participant && !empty($participant['picture_url']))
    ? $participant['picture_url']
    : '/assets/images/users/avatar-1.jpg';
$name = ($participant && !empty($participant['full_name']))
    ? $participant['full_name']
    : 'Guest User';
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
                    <!-- Program Selector Dropdown -->

                    <?php
                    $currentProgramId = 1;
                    $currentProgram = (object)[
                        'id' => 1,
                        'name' => 'Istanbul Youth Summit 2025',
                        'short_description' => 'This is program 1'
                    ];
                    $allPrograms = [
                        (object)[
                            'id' => 1,
                            'name' => 'Istanbul Youth Summit 2025',

                            'short_description' => 'This is program 1'
                        ],
                        (object)[
                            'id' => 2,
                            'name' => 'Program 2',
                            'short_description' => 'This is program 2'
                        ]
                    ];
                    ?>
                    <div class="dropdown ms-1 topbar-head-dropdown header-item">
                        <button type="button" class="btn btn-ghost-secondary px-3" id="program-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($currentProgram->logo_url)): ?>
                                    <img src="<?= esc($currentProgram->logo_url) ?>" alt="Program Logo" class="rounded-circle header-profile-user me-2" style="height: 36px; width: 36px;">
                                <?php endif; ?>
                                <div class="text-start">
                                    <span class="fw-medium fs-14"><?= esc($currentProgram->name) ?></span>
                                    <i class="mdi mdi-chevron-down ms-1"></i>
                                </div>
                            </div>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="program-dropdown">
                            <!-- Program Header -->
                            <h6 class="dropdown-header">Select Program</h6>

                            <!-- List of Programs -->
                            <div class="dropdown-programs-container" style="max-height: 350px; overflow-y: auto;">
                                <?php foreach ($allPrograms as $program): ?>
                                    <a class="dropdown-item d-flex align-items-center <?= ($program->id == $currentProgramId) ? 'active' : '' ?>"
                                        href="<?= site_url('welcome/set_program/' . $program->id) ?>">
                                        <div class="d-flex align-items-center flex-grow-1">

                                            <div>
                                                <span class="fw-medium"><?= esc($program->name) ?></span>
                                                <?php if (!empty($program->short_description)): ?>
                                                    <p class="text-muted mb-0 fs-12"><?= esc(substr($program->short_description, 0, 30)) ?><?= (strlen($program->short_description) > 30) ? '...' : '' ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if ($program->id == $currentProgramId): ?>
                                            <i class="ri-checkbox-circle-fill text-success ms-2 fs-17"></i>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-flex align-items-center">

                                <img class="rounded-circle header-profile-user" src="<?= esc($profileImage) ?>" alt="Header Avatar">
                                <span class="text-start ms-xl-2">
                                    <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?= esc($name) ?></span>
                                </span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <h6 class="dropdown-header">Welcome, <?= esc($name) ?>!</h6>
                            <a class="dropdown-item" href="pages-profile"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= base_url('sign-out') ?>"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Sign Out</span></a>
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