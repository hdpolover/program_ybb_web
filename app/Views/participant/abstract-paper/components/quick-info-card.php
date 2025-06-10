<?php

/**
 * Quick Info Card Component
 * 
 * @param array $data {
 *      @var string $title Card title (optional, defaults to "Quick Info")
 *      @var array $dates {
 *          @var string $created_at Created date
 *          @var string $updated_at Updated date
 *          @var string $due_date Due date (optional)
 *      }
 *      @var string $status Current status (optional)
 *      @var int $version_number Version number (optional)
 *      @var string $state Card state ('normal', 'empty', 'not_eligible', defaults to 'normal')
 * }
 */

// Sample data for demonstration
$dummyData = [
    'normal' => [
        'dates' => [
            'created_at' => '2025-06-08 09:30:00',
            'updated_at' => '2025-06-10 14:15:00',
            'due_date' => '2025-07-15 23:59:59'
        ],
        'version_number' => 3
    ]
];

$title = $data['title'] ?? 'Quick Info';
$state = $data['state'] ?? 'normal';

// Use provided data or fallback to dummy data for normal state
if ($state === 'normal') {
    $dates = $data['dates'] ?? $dummyData['normal']['dates'];
    $version = $data['version_number'] ?? $dummyData['normal']['version_number'];
} else {
    $dates = $data['dates'] ?? [];
    $version = $data['version_number'] ?? null;
}
?>

<div class="card border shadow-sm mb-4 h-100">
    <div class="card-header bg-light">
        <h6 class="card-title text-dark mb-0">
            <i class="bx bx-info-circle me-1"></i> <?= esc($title) ?>
        </h6>
    </div>
    <div class="card-body p-3">
        <?php if ($state === 'empty'): ?>
            <!-- Empty State -->
            <div class="text-center py-4">
                <div class="avatar avatar-lg bg-light-subtle mb-3">
                    <i class="bx bx-file-blank fs-2 text-muted"></i>
                </div>
                <h6 class="fw-semibold">No Abstract Created</h6>
                <p class="text-muted small mb-3">You haven't created any abstract yet.</p>
                <a href="<?= base_url('abstract-paper/create') ?>" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> Create Abstract
                </a>
            </div>
        <?php elseif ($state === 'not_eligible'): ?>
            <!-- Not Eligible State -->
            <div class="text-center py-4">
                <div class="avatar avatar-lg bg-warning-subtle mb-3">
                    <i class="bx bx-block fs-2 text-warning"></i>
                </div>
                <h6 class="fw-semibold text-warning">Not Eligible</h6>
                <p class="text-muted small mb-3">You are not eligible to submit an abstract at this time.</p>
                <a href="<?= base_url('participant/dashboard') ?>" class="btn btn-warning btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Back to Dashboard
                </a>
            </div>
        <?php else: ?>
            <!-- Normal State with Dates and Info -->
            <!-- Important Dates -->
            <div class="mb-3">
                <h6 class="fw-bold text-primary mb-2 small text-uppercase">Important Dates</h6>
                <div class="d-flex flex-column gap-2">
                    <?php if (isset($dates['created_at'])): ?>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Created:</small>
                            <small class="fw-semibold"><?= date('M d, Y', strtotime($dates['created_at'])) ?></small>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($dates['updated_at'])): ?>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Last Updated:</small>
                            <small class="fw-semibold"><?= date('M d, Y', strtotime($dates['updated_at'])) ?></small>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($dates['due_date'])): ?>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Submission Due:</small>
                            <small class="fw-semibold"><?= date('M d, Y', strtotime($dates['due_date'])) ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Info -->
            <div class="mb-3">
                <h6 class="fw-semibold mb-2 small text-uppercase text-muted">Status Info</h6>
                <?php if (isset($dates['created_at'])): ?>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Created At:</small>
                        <small class="fw-semibold"><?= date('h:i A', strtotime($dates['created_at'])) ?></small>
                    </div>
                <?php endif; ?>

                <?php if (isset($dates['updated_at'])): ?>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Updated At:</small>
                        <small class="fw-semibold"><?= date('h:i A', strtotime($dates['updated_at'])) ?></small>
                    </div>
                <?php endif; ?>

                <?php if ($version): ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Version:</small>
                        <small class="fw-semibold">v<?= $version ?></small>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div>
                <h6 class="fw-semibold mb-2 small text-uppercase text-muted">Quick Links</h6>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('abstract-paper/guidelines') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-book-open me-1"></i> Abstract Guidelines
                    </a>
                    <a href="<?= base_url('abstract-paper/paper-guidelines') ?>" class="btn btn-sm btn-outline-info">
                        <i class="bx bx-file me-1"></i> Paper Guidelines
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>