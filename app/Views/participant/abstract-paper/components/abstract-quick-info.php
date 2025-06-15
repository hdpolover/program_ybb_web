<?php
// Define essential variables for this component with safety checks
$participant_data = $participant_data ?? [];
$abstract = $participant_data['abstract'] ?? [];
$versions = !empty($abstract['versions']) ? $abstract['versions'] : [];

// Sort versions by version_number in descending order to ensure latest is first
if (!empty($versions)) {
    usort($versions, function ($a, $b) {
        $a_version = isset($a['version_number']) ? (int)$a['version_number'] : 0;
        $b_version = isset($b['version_number']) ? (int)$b['version_number'] : 0;
        return $b_version - $a_version; // Descending order
    });
}

// Get the latest version (first after sorting)
$latestVersion = !empty($versions) ? $versions[0] : null;
$versionCount = count($versions);
$latestVersionNumber = isset($latestVersion['version_number']) ? $latestVersion['version_number'] : 1;

$abstractStatus = isset($abstract['status']) ? strtolower($abstract['status']) : 'draft';

// Status configuration for badges
$statusConfig = [
    'draft' => ['color' => 'bg-secondary', 'icon' => 'bx-edit'],
    'submitted' => ['color' => 'bg-info', 'icon' => 'bx-check-circle'],
    'under_review' => ['color' => 'bg-warning', 'icon' => 'bx-time'],
    'accepted' => ['color' => 'bg-success', 'icon' => 'bx-check']
];
?>

<div class="h-100">
    <div class="card border shadow-sm h-100">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">
                <i class="bx bx-info-circle text-primary me-2"></i>Quick Information
            </h6>
        </div>

        <div class="card-body">
            <!-- Compact Information Grid -->
            <div class="row g-3 mb-4">
                <!-- Abstract ID -->
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-hash text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Abstract ID</small>
                            <span class="fw-semibold font-monospace">#<?= $abstract['id'] ?? 'N/A' ?></span>
                        </div>
                    </div>
                </div>

                <!-- Version -->
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-git-branch text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Version</small>
                            <span class="badge bg-primary">v<?= $latestVersionNumber ?></span>
                            <?php if ($versionCount > 1): ?>
                                <small class="text-muted ms-1">(<?= $versionCount ?> total)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-flag text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Current Status</small>
                            <span class="badge bg-<?= $statusConfig[$abstractStatus]['color'] === 'bg-secondary' ? 'secondary' : ($statusConfig[$abstractStatus]['color'] === 'bg-info' ? 'info' : ($statusConfig[$abstractStatus]['color'] === 'bg-warning' ? 'warning' : 'success')) ?> text-white">
                                <?= ucfirst(str_replace('_', ' ', esc($abstract['status'] ?? 'Draft'))) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Subtheme -->
                <?php if (!empty($participant_data['abstract']['subtheme_name'])): ?>
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-2">
                                <i class="bx bx-category text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Sub-theme</small>
                                <p class="mb-0 small"><?= esc($participant_data['abstract']['subtheme_name']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Dates -->
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-calendar-plus text-secondary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Created</small>
                            <small class="fw-medium"><?= date('M d, Y', strtotime($participant_data['abstract']['created_at'] ?? 'now')) ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-calendar-check text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Updated</small>
                            <small class="fw-medium"><?= date('M d, Y', strtotime($latestVersion['updated_at'] ?? ($participant_data['abstract']['updated_at'] ?? 'now'))) ?></small>
                        </div>
                    </div>
                </div>            </div>

            <!-- Abstract Settings Information -->
            <?php if (isset($abstract_settings) && !empty($abstract_settings)): ?>
                <div class="border-top pt-3 mb-3">
                    <h6 class="mb-3 fw-semibold">
                        <i class="bx bx-info-circle text-success me-1"></i>Important Deadlines & Templates
                    </h6>
                    <div class="row g-2">
                        <!-- Abstract Submission Deadline -->
                        <?php if (!empty($abstract_settings['abstract_submission_deadline'])): ?>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <i class="bx bx-time-five text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Abstract Deadline</small>
                                        <small class="fw-medium text-danger"><?= date('M d, Y g:i A', strtotime($abstract_settings['abstract_submission_deadline'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Full Paper Submission Deadline -->
                        <?php if (!empty($abstract_settings['full_paper_submission_deadline'])): ?>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <i class="bx bx-calendar-x text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Paper Deadline</small>
                                        <small class="fw-medium text-warning"><?= date('M d, Y g:i A', strtotime($abstract_settings['full_paper_submission_deadline'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Template Downloads -->
                        <?php if (!empty($abstract_settings['abstract_template_url']) || !empty($abstract_settings['paper_template_url'])): ?>
                            <div class="col-12 mt-2">
                                <small class="text-muted d-block mb-2">Download Templates</small>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php if (!empty($abstract_settings['abstract_template_url'])): ?>
                                        <a href="<?= esc($abstract_settings['abstract_template_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="bx bx-download me-1"></i>Abstract Template
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($abstract_settings['paper_template_url'])): ?>
                                        <a href="<?= esc($abstract_settings['paper_template_url']) ?>" target="_blank" class="btn btn-outline-info btn-sm flex-fill">
                                            <i class="bx bx-download me-1"></i>Paper Template
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Guidelines Section -->
            <!-- <div class="border-top pt-3">
                <h6 class="mb-3 fw-semibold">
                    <i class="bx bx-book-open text-info me-1"></i>Guidelines & Resources
                </h6>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-file-doc me-1"></i> Abstract Guidelines
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm">
                        <i class="bx bx-edit me-1"></i> Writing Tips
                    </a>
                    <a href="#" class="btn btn-outline-success btn-sm">
                        <i class="bx bx-help-circle me-1"></i> FAQ
                    </a>
                </div>
            </div> -->
        </div>
    </div>
</div>
