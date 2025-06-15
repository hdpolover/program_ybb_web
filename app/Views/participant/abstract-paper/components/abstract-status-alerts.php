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

<?php if ($abstractStatus === 'accepted'): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm">
                    <div class="avatar-title bg-success text-white rounded-circle">
                        <i class="bx bx-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">
                    <i class="bx bx-trophy me-2"></i>Congratulations! Your Abstract has been Accepted
                </h5>
                <p class="mb-0">
                    Your abstract has been reviewed and accepted for presentation.
                    You will receive further instructions about the presentation schedule and requirements.
                </p>
            </div>
        </div>
    </div>

<?php elseif ($abstractStatus === 'under_review'): ?>
    <!-- Enhanced Under Review Status - Show Active vs Latest Version -->
    <?php
    // Determine active version (the one being reviewed) vs latest version
    $activeVersionId = $participant_data['abstract']['active_version_id'] ?? null;
    $activeVersion = null;
    $latestVersion = !empty($versions) ? $versions[0] : null;

    // Find the active version being reviewed
    if ($activeVersionId && !empty($versions)) {
        foreach ($versions as $version) {
            if ($version['id'] == $activeVersionId) {
                $activeVersion = $version;
                break;
            }
        }
    }

    // If no active version found, assume latest submitted version is being reviewed
    if (!$activeVersion && !empty($versions)) {
        $activeVersion = $latestVersion;
    }

    $isNewVersionCreated = $latestVersion && $activeVersion && $latestVersion['id'] !== $activeVersion['id'];
    ?>

    <div class="row g-3 mb-4">
        <!-- Active Version Card (Being Reviewed) -->
        <div class="col-lg-6">
            <div class="alert alert-warning border-0 shadow-sm h-100" role="alert">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm">
                            <div class="avatar-title bg-warning text-dark rounded-circle">
                                <i class="bx bx-time-five fs-4"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-2">
                            <i class="bx bx-target-lock me-1"></i>Under Review
                        </h6>
                        <p class="mb-2 small">
                            <strong>Version <?= $activeVersion['version_number'] ?? '1' ?></strong> is currently being reviewed by our expert panel.
                        </p>
                        <?php if ($isNewVersionCreated): ?>
                            <div class="alert alert-info p-2 mb-0">
                                <small>
                                    <i class="bx bx-info-circle me-1"></i>
                                    You've created a newer version, but reviewers are still evaluating this version.
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Version Card (if different from active) -->
        <div class="col-lg-6">
            <?php if ($isNewVersionCreated): ?>
                <div class="alert alert-info border-0 shadow-sm h-100" role="alert">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <div class="avatar-title bg-info text-white rounded-circle">
                                    <i class="bx bx-file-blank fs-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2">
                                <i class="bx bx-star me-1"></i>Latest Version
                            </h6>
                            <p class="mb-2 small">
                                <strong>Version <?= $latestVersion['version_number'] ?? '1' ?></strong> is your most recent update.
                            </p>
                            <small class="text-muted">
                                <i class="bx bx-calendar me-1"></i>
                                Updated: <?= date('M d, Y h:i A', strtotime($latestVersion['updated_at'] ?? 'now')) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-light border-0 shadow-sm h-100" role="alert">
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="bx bx-check-circle text-success fs-3 mb-2"></i>
                            <h6 class="mb-1">Latest Version Under Review</h6>
                            <small class="text-muted">Your most recent version is being evaluated</small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>    <!-- General Review Information -->
    <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm">
                    <div class="avatar-title bg-primary text-white rounded-circle">
                        <i class="bx bx-info-circle fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading mb-1">Review Process Information</h6>
                <p class="mb-0 small">
                    Our expert reviewers are carefully evaluating your abstract.
                    You'll receive detailed feedback once the review is complete.
                </p>
            </div>
        </div>
    </div>

<?php elseif ($abstractStatus === 'submitted' && !$hasFeedback): ?>
    <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm">
                    <div class="avatar-title bg-warning text-dark rounded-circle">
                        <i class="bx bx-paper-plane fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">Abstract Submitted Successfully</h5>
                <p class="mb-0">
                    Your abstract has been submitted and is waiting for review assignment.
                    You'll be notified once the review process begins.
                </p>
            </div>
        </div>
    </div>

<?php elseif ($abstractStatus === 'submitted' && $latestVersionHasFeedback): ?>
    <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm">
                    <div class="avatar-title bg-info text-white rounded-circle">
                        <i class="bx bx-message-dots fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">Reviewer Feedback Available</h5>
                <p class="mb-0">
                    Your latest abstract version has been reviewed and feedback is available.
                    Please review the comments and make necessary revisions if required.
                </p>
            </div>
        </div>
    </div>

<?php elseif ($abstractStatus === 'submitted' && $hasFeedback && !$latestVersionHasFeedback): ?>
    <div class="alert alert-light border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="avatar-sm">
                    <div class="avatar-title bg-secondary text-white rounded-circle">
                        <i class="bx bx-clock fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">New Version Awaiting Review</h5>
                <p class="mb-0">
                    Your latest version is awaiting review. Previous versions have feedback available which you can view in the version history.
                </p>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#versionHistoryModal">
                    <i class="bx bx-history me-1"></i> View Version History & Feedback
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Abstract Status Summary Card (for under_review status) -->
<?php if ($abstractStatus === 'under_review'): ?>    <?php
    $activeVersionId = $participant_data['abstract']['active_version_id'] ?? null;
    $activeVersion = null;
    $totalVersions = count($versions);

    // Find active version
    foreach ($versions as $version) {
        if ($activeVersionId && $version['id'] == $activeVersionId) {
            $activeVersion = $version;
        }
    }

    // Get total feedback count
    $totalFeedback = count($participant_data['abstract']['feedbacks'] ?? []);
    ?><div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="card-title mb-2 text-dark">
                        <i class="bx bx-bar-chart-alt-2 me-2 text-primary"></i>Review Status Summary
                    </h6>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="mb-1 fw-bold text-primary"><?= $totalVersions ?></h5>
                                <small class="text-muted">Total Versions</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-1 fw-bold text-info"><?= $totalFeedback ?></h5>
                            <small class="text-muted">Feedbacks Received</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <?php if ($activeVersion): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="switchToActiveVersion('<?= $activeVersion['id'] ?>')">
                            <i class="bx bx-target-lock me-1"></i>View Active Version
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Version Information Header -->
<?php if ($abstractStatus === 'under_review'): ?>
    <?php
    // Get active version info for under review status
    $activeVersionId = $participant_data['abstract']['active_version_id'] ?? null;
    $activeVersion = null;
    $displayedVersion = $latestVersion;

    if ($activeVersionId && !empty($versions)) {
        foreach ($versions as $version) {
            if ($version['id'] == $activeVersionId) {
                $activeVersion = $version;
                break;
            }
        }
    }

    $isViewingActiveVersion = $activeVersion && $latestVersion && $latestVersion['id'] === $activeVersion['id'];
    ?>
    <div class="alert alert-light border shadow-sm mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-1">
                    <i class="bx bx-info-circle me-2 text-primary"></i>Version Information
                </h6>
                <p class="mb-0 small text-muted">
                    <?php if ($isViewingActiveVersion): ?>
                        You are viewing the <strong>active version</strong> that is currently under review.
                    <?php else: ?>
                        You are viewing the <strong>latest version</strong>. The active version under review is Version <?= $activeVersion['version_number'] ?? 'N/A' ?>.
                    <?php endif; ?>
                </p>
            </div> <?php if (!$isViewingActiveVersion && $activeVersion): ?>
                <button type="button" class="btn btn-outline-primary btn-sm"
                    onclick="switchToActiveVersion('<?= $activeVersion['id'] ?>')">
                    <i class="bx bx-target-lock me-1"></i>Switch to Active Version </button>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>