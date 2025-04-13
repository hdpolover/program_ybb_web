<?php
// This file is a minimal version of the guideline card for the participant dashboard
// It displays a compact card with essential information about program guidelines

$program = $program ?? [];
$guidelineLink = $program['guideline'] ?? '#';
?>

<!-- Minimal Guidelines Card -->
<div class="card border-0 bg-primary position-relative overflow-hidden mb-3">
    <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
    <div class="card-body p-3 position-relative">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="avatar-sm">
                    <div class="avatar-title bg-white bg-opacity-25 rounded-circle">
                        <i class="ri-file-list-3-line text-white fs-18"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="text-white mb-1">Registration Guideline</h5>
                        <p class="text-white-50 mb-0 fs-12">Important information about registration and participation</p>
                    </div>
                    <a href="<?= $guidelineLink ?>" class="btn btn-sm btn-info" target="_blank">
                        <i class="ri-eye-line me-1 align-bottom"></i> View
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>