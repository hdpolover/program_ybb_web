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

// Abstract status and editing permissions
$abstractStatus = isset($abstract['status']) ? strtolower($abstract['status']) : 'draft';
$hasFeedback = !empty($abstract['feedbacks']);

// Check if there's feedback for the latest version
$latestVersionHasFeedback = false;
if (!empty($abstract['feedbacks']) && !empty($latestVersion)) {
    foreach ($abstract['feedbacks'] as $feedback) {
        if (isset($feedback['version_id']) && $feedback['version_id'] == $latestVersion['id']) {
            $latestVersionHasFeedback = true;
            break;
        }
        // Fallback: check by version_number if version_id is not available
        if (!isset($feedback['version_id']) && isset($feedback['version_number']) && 
            $feedback['version_number'] == $latestVersion['version_number']) {
            $latestVersionHasFeedback = true;
            break;
        }
    }
}

// Participants can only edit when:
// 1. Status is 'draft' OR 'under_review' ONLY
// 2. Status is NOT 'submitted', 'accepted', or any other status
$canEdit = ($abstractStatus === 'draft' || $abstractStatus === 'under_review');

// Never allow editing if status is anything other than draft or under_review
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary py-3">
        <!-- Title with Edit Button at Top Right -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h1 class="card-title mb-0 text-white fw-bold" style="font-size: 1.75rem;">
                <?= esc($latestVersion['title'] ?? 'Untitled Abstract') ?>
            </h1>

            <?php if ($canEdit): ?>                <a href="<?= base_url('abstract-paper/edit/' . ($abstract['id'] ?? '') . '/' . $latestVersionNumber) ?>"
                    class="btn btn-light btn-sm edit-abstract-btn"
                    data-abstract-id="<?= $abstract['id'] ?? '' ?>"
                    data-version-id="<?= $latestVersionNumber ?>"
                    data-ajax="false">
                    <i class="bx bx-edit me-1"></i> Edit Abstract
                </a>            <?php else: ?>
                <span class="text-white-50 small">
                    <i class="bx bx-lock me-1"></i>
                    <?php if ($abstractStatus === 'accepted'): ?>
                        Editing disabled - Abstract accepted
                    <?php elseif ($abstractStatus === 'submitted'): ?>
                        Editing disabled - Abstract submitted
                    <?php else: ?>
                        Editing disabled
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Enhanced Status and Subtheme with Dates Below -->
        <div class="row">
            <div class="col-md-8">
                <!-- Enhanced Status and Subtheme Badges -->
                <div class="mb-2">
                    <?php
                    $status = strtolower($abstract['status'] ?? 'draft');
                    $statusConfig = [
                        'draft' => ['color' => 'bg-secondary', 'icon' => 'bx-edit', 'pulse' => false],
                        'submitted' => ['color' => 'bg-info', 'icon' => 'bx-paper-plane', 'pulse' => true],
                        'under_review' => ['color' => 'bg-warning', 'icon' => 'bx-time-five', 'pulse' => true, 'display_name' => 'In Review'],
                        'accepted' => ['color' => 'bg-success', 'icon' => 'bx-check-circle', 'pulse' => false]
                    ];
                    $config = $statusConfig[$status] ?? $statusConfig['draft'];
                    ?>
                    <span class="badge <?= $config['color'] ?> fs-5 px-3 py-2 me-2 <?= $config['pulse'] ? 'status-pulse' : '' ?>" style="font-size: 1rem !important;">
                        <i class="bx <?= $config['icon'] ?> me-1"></i> <?= $config['display_name'] ?? ucfirst(esc($abstract['status'] ?? 'Draft')) ?>
                    </span>

                    <?php if (!empty($abstract['subtheme_name'])): ?>
                        <span class="badge bg-light text-dark fs-6 px-3 py-2" style="font-size: 0.875rem !important;">
                            <i class="bx bx-category me-1"></i> <?= esc($abstract['subtheme_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Beautified Dates -->
                <div class="text-white d-flex flex-wrap">
                    <div class="me-3 mb-1">
                        <span class="fw-semibold text-white">
                            <i class="bx bx-calendar-plus me-1"></i> Created:
                        </span>
                        <span class="text-white-50 fst-italic ms-1"><?= date('M d, Y h:i A', strtotime($participant_data['abstract']['created_at'] ?? 'now')) ?></span>
                    </div>
                    <div>
                        <span class="fw-semibold text-white">
                            <i class="bx bx-calendar-check me-1"></i> Updated:
                        </span>
                        <span class="text-white-50 fst-italic ms-1"><?= date('M d, Y h:i A', strtotime($latestVersion['updated_at'] ?? ($participant_data['abstract']['updated_at'] ?? 'now'))) ?></span>
                    </div>
                </div>
            </div>

            <!-- Version Navigation (Right side) -->
            <div class="col-md-4 text-md-end">
                <div class="d-flex flex-column align-items-md-end">
                    <!-- Version Information -->
                    <div class="text-white mb-2">
                        <span class="fw-semibold">
                            <i class="bx bx-git-branch me-1"></i> Version:
                        </span>
                        <span class="badge bg-light text-dark ms-1"><?= $latestVersionNumber ?></span>
                        <?php if ($versionCount > 1): ?>
                            <small class="text-white-50 ms-2">
                                (<?= $versionCount ?> total)
                            </small>
                        <?php endif; ?>
                    </div>                    <!-- View History Button -->
                    <?php if ($versionCount > 1): ?>
                        <button type="button" class="btn btn-light btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#versionHistoryModal"
                            data-version-count="<?= $versionCount ?>"
                            onclick="console.log('Version history button clicked', {versionCount: <?= $versionCount ?>, modalExists: !!document.getElementById('versionHistoryModal')});">
                            <i class="bx bx-history me-1"></i> View History (<?= $versionCount ?>)
                        </button>
                    <?php else: ?>
                        <small class="text-white-50">
                            <i class="bx bx-info-circle me-1"></i>Only 1 version
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
