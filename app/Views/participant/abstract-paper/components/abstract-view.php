<!-- Abstract exists - Show the detailed view -->
<style>
    /* Enhanced Status Badge with Pulse Animation */
    .status-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.05);
            opacity: 0.9;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Timeline for submission details */
    .timeline-simple .timeline-item {
        position: relative;
        padding-left: 1rem;
    }

    .timeline-simple .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 8px;
        height: 8px;
        background: #dee2e6;
        border-radius: 50%;
    }

    /* Author Type Selection Cards */
    .author-type-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
        background: #fff;
    }

    .author-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #dee2e6;
    }

    .author-type-card.selected {
        border-color: #0d6efd;
        background: linear-gradient(135deg, #f8f9ff 0%, #e7f1ff 100%);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
    }

    .author-type-card.selected[data-type="participant"] {
        border-color: #198754;
        background: linear-gradient(135deg, #f8fff9 0%, #e7f6ec 100%);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.15);
    }

    .author-type-card .avatar-title {
        transition: all 0.3s ease;
    }

    .author-type-card:hover .avatar-title {
        transform: scale(1.1);
    }

    .author-type-card .btn {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .author-type-card.selected .btn {
        transform: scale(1.05);
        font-weight: 600;
    }

    /* Search Section Enhancement */
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    /* Form Field Enhancements */
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Alert Enhancements */
    .alert {
        border-radius: 8px;
    }

    /* Card hover effects */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Avatar enhancements */
    .avatar-lg {
        width: 4rem;
        height: 4rem;
    }

    .avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
    }

    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    /* Button loading state */
    .btn:disabled {
        opacity: 0.7;
    }

    /* Input group enhancements */
    .input-group .form-control:focus {
        z-index: 3;
    }

    .input-group-text {
        border-color: #dee2e6;
    }

    /* Search result animation */
    .alert.fade.show {
        animation: slideInUp 0.3s ease;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .author-type-card {
            margin-bottom: 1rem;
        }

        .author-type-card .card-body {
            padding: 1.5rem !important;
        }

        .avatar-lg {
            width: 3rem;
            height: 3rem;
        }
    }
</style>

<?php
// Helper function to check if content is effectively empty (handles Quill's empty states)
function isContentEmpty($content)
{
    if (empty($content)) return true;

    // Remove common Quill empty states
    $cleanContent = str_replace(['<p><br></p>', '<p></p>', '<p>&nbsp;</p>', '<br>', '&nbsp;'], '', $content);
    $cleanContent = trim(strip_tags($cleanContent));

    return empty($cleanContent);
}

// Prepare sorted versions for use throughout the template
$versions = !empty($participant_data['abstract']['versions']) ? $participant_data['abstract']['versions'] : [];

// Sort versions by version_number in descending order to ensure latest is first
if (!empty($versions)) {
    usort($versions, function ($a, $b) {
        $a_version = isset($a['version_number']) ? (int)$a['version_number'] : 0;
        $b_version = isset($b['version_number']) ? (int)$b['version_number'] : 0;
        return $b_version - $a_version; // Descending order
    });

    // Update the versions array in participant_data to use our sorted version
    $participant_data['abstract']['versions'] = $versions;
}

// Get the latest version (first after sorting)
$latestVersion = !empty($versions) ? $versions[0] : null;
$versionCount = count($versions);
$latestVersionNumber = isset($latestVersion['version_number']) ? $latestVersion['version_number'] : 1;
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary py-3">
        <!-- Title with Edit Button at Top Right -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h1 class="card-title mb-0 text-white fw-bold" style="font-size: 1.75rem;">
                <?= esc($latestVersion['title'] ?? 'Untitled Abstract') ?>
            </h1>

            <?php
            $abstractStatus = strtolower($participant_data['abstract']['status'] ?? 'draft');
            $hasFeedback = !empty($participant_data['abstract']['reviewers']);

            // Participants can only edit if:
            // 1. Status is 'draft' OR 'under_review', OR
            // 2. Status is 'submitted' AND there is reviewer feedback requiring revisions
            // 3. Status is NOT 'accepted' (accepted abstracts are final)
            $canEdit = ($abstractStatus === 'draft' || $abstractStatus === 'under_review') ||
                ($abstractStatus === 'submitted' && $hasFeedback);

            // Never allow editing if status is 'accepted'
            if ($abstractStatus === 'accepted') {
                $canEdit = false;
            }
            ?>

            <?php if ($canEdit): ?>
                <a href="<?= base_url('abstract-paper/edit/' . $participant_data['abstract']['id'] . '/' . $latestVersionNumber) ?>"
                    class="btn btn-light btn-sm edit-abstract-btn"
                    data-abstract-id="<?= $participant_data['abstract']['id'] ?>"
                    data-version-id="<?= $latestVersionNumber ?>"
                    data-ajax="false">
                    <i class="bx bx-edit me-1"></i> Edit Abstract
                </a>
            <?php else: ?>
                <span class="text-white-50 small">
                    <i class="bx bx-lock me-1"></i>
                    <?php if ($abstractStatus === 'accepted'): ?>
                        Editing disabled - Abstract accepted
                    <?php elseif ($abstractStatus === 'submitted' && !$hasFeedback): ?>
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
                    $status = strtolower($participant_data['abstract']['status'] ?? 'draft');
                    $statusConfig = [
                        'draft' => ['color' => 'bg-secondary', 'icon' => 'bx-edit', 'pulse' => false],
                        'submitted' => ['color' => 'bg-info', 'icon' => 'bx-paper-plane', 'pulse' => true],
                        'under_review' => ['color' => 'bg-warning', 'icon' => 'bx-time-five', 'pulse' => true],
                        'accepted' => ['color' => 'bg-success', 'icon' => 'bx-check-circle', 'pulse' => false]
                    ];
                    $config = $statusConfig[$status] ?? $statusConfig['draft'];
                    ?> <span class="badge <?= $config['color'] ?> fs-5 px-3 py-2 me-2 <?= $config['pulse'] ? 'status-pulse' : '' ?>" style="font-size: 1rem !important;">
                        <i class="bx <?= $config['icon'] ?> me-1"></i> <?= ucfirst(esc($participant_data['abstract']['status'] ?? 'Draft')) ?>
                    </span>
                </div> <!-- Beautified Dates -->
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

            <!-- Version Number and History Button at Bottom Right -->
            <div class="col-md-4 d-flex justify-content-end align-items-end mt-3 mt-md-0">
                <div class="d-flex align-items-center">
                    <?php if (isset($latestVersionNumber)): ?>
                        <span class="text-white-50 me-2">v<?= $latestVersionNumber ?></span>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-light rounded-circle" data-bs-toggle="modal" data-bs-target="#versionHistoryModal" title="Show Version History">
                        <i class="bx bx-history"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($abstractStatus === 'accepted'): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-check-circle fs-4 me-3 text-success"></i>
            <div>
                <h6 class="alert-heading mb-1">Abstract Accepted - Next Steps Required</h6>
                <p class="mb-2">Congratulations! Your abstract has been accepted. To proceed with the next steps of your submission, please contact the conference administrator.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <small class="text-success fw-semibold">
                        <i class="bx bx-phone me-1"></i> Contact Admin for:
                    </small>
                </div>
                <ul class="small text-success mb-0 ps-3">
                    <li>Registration information and deadlines</li>
                    <li>Presentation format and requirements</li>
                    <li>Conference schedule and logistics</li>
                    <li>Payment instructions (if applicable)</li>
                </ul>
            </div>
        </div>
    </div>
<?php elseif ($abstractStatus === 'submitted' && !$hasFeedback): ?>
    <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-info-circle fs-4 me-3 text-warning"></i>
            <div>
                <h6 class="alert-heading mb-1">Abstract Submitted</h6>
                <p class="mb-0">Your abstract has been submitted and is currently under review. You cannot make changes at this time. You will be able to edit your abstract if reviewers provide feedback requiring revisions.</p>
            </div>
        </div>
    </div>
<?php elseif ($abstractStatus === 'submitted' && $hasFeedback): ?>
    <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-edit fs-4 me-3 text-info"></i>
            <div>
                <h6 class="alert-heading mb-1">Reviewer Feedback Available</h6>
                <p class="mb-0">Reviewers have provided feedback on your submitted abstract. You can now make revisions based on their comments. Please review the feedback section below and update your abstract accordingly.</p>
            </div>
        </div>
    </div>
<?php elseif ($abstractStatus === 'under_review'): ?>
    <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-time-five fs-4 me-3 text-info"></i>
            <div>
                <h6 class="alert-heading mb-1">Under Review</h6>
                <p class="mb-0">Your abstract is currently being reviewed. You can still make edits during the review process until a final decision is made.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- First Row: Abstract Content (8) + Reviewer Feedback (4) -->
<div class="row mb-4">
    <div class="col-lg-8">
        <!-- Combined Abstract Information Card -->
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-light d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="card-title text-dark mb-0">
                        <i class="bx bx-file-find me-1"></i> Abstract Content
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <!-- Abstract Content -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-file-blank text-primary me-2"></i>Abstract</h6>
                    <div class="bg-light p-3 rounded">
                        <?php
                        $content = $latestVersion['content'] ?? '';
                        if (isContentEmpty($content)):
                        ?>
                            <span class="text-muted fst-italic">No content available</span>
                        <?php else: ?>
                            <?= $content ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Keywords Section -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-key text-primary me-2"></i>Keywords</h6>
                    <div class="bg-light p-3 rounded">
                        <?php if ($latestVersion && !empty($latestVersion['keywords'])): ?>
                            <?php foreach (explode(',', $latestVersion['keywords']) as $keyword): ?>
                                <span class="badge bg-soft-primary text-primary me-1 mb-1"><?= trim(esc($keyword)) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">No keywords provided</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- References Section -->
                <div class="mb-0">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-book-content text-primary me-2"></i>References</h6>
                    <div class="bg-light p-3 rounded">
                        <?php if ($latestVersion && !empty($latestVersion['refs'])): ?>
                            <div class="text-break" style="white-space: pre-line;"><?= esc($latestVersion['refs']) ?></div>
                        <?php else: ?>
                            <span class="text-muted">No references provided</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Reviewer Feedback Card -->
        <?php if (!empty($participant_data['abstract']['reviewers'])): ?>
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-warning">
                    <h5 class="card-title text-dark mb-0">
                        <i class="bx bx-comment-detail me-1"></i> Reviewer Feedback
                        <span class="badge bg-dark ms-2">Action Required</span>
                    </h5>
                </div>
                <div class="card-body">
                    <?= $this->include('participant/abstract-paper/components/reviewer-feedback') ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title text-dark mb-0">
                        <i class="bx bx-comment-detail me-1"></i> Reviewer Feedback
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                <i class="bx bx-comment fs-3"></i>
                            </div>
                        </div>
                        <h6 class="mb-2">No Feedback Yet</h6>
                        <p class="text-muted mb-0 small">You will be notified when reviews are complete.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Second Row: Quick Info (3) + Authors (5) + Paper (4) -->
<div class="row">
    <!-- Quick Info Sidebar -->
    <div class="col-lg-3">
        <?php
        echo $this->include('participant/abstract-paper/components/quick-info-card', [
            'data' => [
                'state' => 'normal',
                'dates' => [
                    'created_at' => $participant_data['abstract']['created_at'],
                    'updated_at' => $participant_data['abstract']['updated_at'],
                    'due_date' => $participant_data['abstract']['due_date'] ?? null
                ],
                'version_number' => $latestVersionNumber
            ]
        ]);
        ?>
    </div> <!-- Authors and Paper Section -->
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-8">
                <!-- Authors Information Card -->
                <div class="card border shadow-sm mb-4">
                    <div class="card border shadow-sm mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-dark mb-0">
                                <i class="bx bx-user-circle me-1"></i> Authors Information
                            </h5>
                            <div>
                                <?php if ($canEdit): ?>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCoAuthorModal">
                                        <i class="bx bx-cog me-1"></i> Manage
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">
                                        <i class="bx bx-lock me-1"></i>
                                        <?php if ($abstractStatus === 'accepted'): ?>
                                            Cannot edit - Abstract accepted
                                        <?php elseif ($abstractStatus === 'submitted' && !$hasFeedback): ?>
                                            Cannot edit - Abstract submitted
                                        <?php else: ?>
                                            Cannot edit
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($participant_data['abstract']['authors'])): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($participant_data['abstract']['authors'] as $index => $author): ?>
                                        <li class="list-group-item px-0 <?= $index > 0 ? 'border-top' : '' ?>">
                                            <div class="d-flex align-items-start">
                                                <div class="avatar-sm bg-light rounded p-2 me-3 d-none d-sm-block">
                                                    <i class="bx bx-user-circle text-primary fs-3"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h6 class="mb-0 fw-semibold"><?= esc($author['full_name']) ?></h6>
                                                        <?php if (isset($author['is_participant']) && $author['is_participant'] == '1'): ?>
                                                            <span class="badge bg-primary">Primary Author</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Co-Author</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <p class="text-muted small mb-1">
                                                        <i class="bx bx-buildings me-1"></i> <?= esc($author['institution'] ?? 'Not specified') ?>
                                                    </p>
                                                    <p class="text-muted small mb-0">
                                                        <i class="bx bx-envelope me-1"></i> <?= esc($author['email'] ?? 'No email provided') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="alert alert-info mb-0">
                                    <i class="bx bx-info-circle me-1"></i> No authors information available. Please add author details.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Paper Upload Section -->
                    <div class="card border shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title text-dark mb-0">
                                <i class="bx bx-file me-1"></i> Paper Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($participant_data['abstract']['status']) && strtolower($participant_data['abstract']['status']) === 'accepted'): ?>
                                <?php if (empty($participant_data['abstract']['paper_file'])): ?>
                                    <div class="alert alert-success mb-3" role="alert">
                                        <i class="bx bx-check-circle me-1"></i> Your abstract has been accepted. You can now upload your full paper.
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-success w-md" data-bs-toggle="modal" data-bs-target="#uploadPaperModal">
                                            <i class="bx bx-upload me-1"></i> Upload Paper
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-sm bg-light rounded p-2 me-3">
                                            <i class="bx bx-file-blank text-primary fs-3"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1"><?= esc($participant_data['abstract']['paper_file']) ?></h6>
                                            <small class="text-muted">Uploaded on: <?= esc($participant_data['abstract']['paper_uploaded_date'] ?? 'N/A') ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex mt-3">
                                        <a href="#" class="btn btn-sm btn-info me-2">
                                            <i class="bx bx-download me-1"></i> Download
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updatePaperModal">
                                            <i class="bx bx-refresh me-1"></i> Update
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php elseif ($abstractStatus === 'submitted' || $abstractStatus === 'under_review'): ?>
                                <div class="alert alert-info mb-0" role="alert">
                                    <i class="bx bx-info-circle me-1"></i>
                                    <?php if ($abstractStatus === 'submitted'): ?>
                                        Your abstract is currently under review. Paper upload will be available once your abstract is accepted.
                                    <?php else: ?>
                                        Your abstract is being reviewed. Paper upload will be available once your abstract is accepted.
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mb-0" role="alert">
                                    <i class="bx bx-info-circle me-1"></i> You will be able to upload your full paper once your abstract is accepted.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Paper Upload Modals -->
    <?= $this->include('participant/abstract-paper/components/paper-upload-modals') ?>

    <!-- Version History Modal -->
    <div class="modal fade" id="versionHistoryModal" tabindex="-1" aria-labelledby="versionHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="versionHistoryModalLabel">
                        <i class="bx bx-history me-1"></i> Abstract Version History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"> <?php if (!empty($participant_data['abstract']['versions'])): ?> <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            <?php if ($canEdit): ?>
                                <span>Only the latest version can be edited. Previous versions are available for viewing and comparison purposes.</span>
                            <?php elseif ($abstractStatus === 'accepted'): ?>
                                <span>This abstract has been accepted and is now final. No further edits are allowed. All versions are available for viewing and comparison purposes only.</span>
                            <?php elseif ($abstractStatus === 'submitted' && !$hasFeedback): ?>
                                <span>This abstract has been submitted and cannot be edited until reviewers provide feedback. All versions are available for viewing and comparison purposes only.</span>
                            <?php else: ?>
                                <span>This abstract cannot be edited at this time. All versions are available for viewing and comparison purposes only.</span>
                            <?php endif; ?>
                        </div>
                        <div class="accordion" id="versionAccordion">
                            <?php
                                                // Sort versions by version_number in descending order
                                                $sortedVersions = $participant_data['abstract']['versions'];
                                                usort($sortedVersions, function ($a, $b) {
                                                    $a_version = isset($a['version_number']) ? (int)$a['version_number'] : 0;
                                                    $b_version = isset($b['version_number']) ? (int)$b['version_number'] : 0;
                                                    return $b_version - $a_version; // Descending order
                                                });

                                                foreach ($sortedVersions as $index => $version):
                                                    $versionNum = isset($version['version_number']) ? $version['version_number'] : ($index + 1);
                            ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $index ?>">
                                        <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $index ?>">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <span class="fw-bold"><?= esc($version['title']) ?></span>
                                                    <?php if ($index === 0): ?>
                                                        <span class="badge bg-success ms-2">Latest Version</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary ms-2">v<?= $versionNum ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted ms-3">
                                                    <i class="bx bx-calendar me-1"></i> <?= date('M d, Y h:i A', strtotime($version['created_at'] ?? 'now')) ?>
                                                </small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $index ?>" data-bs-parent="#versionAccordion">
                                        <div class="accordion-body">
                                            <div class="card border-0"> <!-- Version Status -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <?php
                                                        $versionStatus = strtolower($version['status'] ?? 'draft');
                                                        $versionStatusConfig = [
                                                            'draft' => ['color' => 'bg-secondary', 'icon' => 'bx-edit'],
                                                            'submitted' => ['color' => 'bg-primary', 'icon' => 'bx-paper-plane'],
                                                            'under_review' => ['color' => 'bg-warning', 'icon' => 'bx-time-five'],
                                                            'accepted' => ['color' => 'bg-success', 'icon' => 'bx-check-circle']
                                                        ];
                                                        $versionConfig = $versionStatusConfig[$versionStatus] ?? $versionStatusConfig['draft'];
                                                        ?>
                                                        <span class="badge <?= $versionConfig['color'] ?> mb-2">
                                                            <i class="bx <?= $versionConfig['icon'] ?> me-1"></i>
                                                            <?= ucfirst($version['status'] ?? 'Draft') ?>
                                                        </span>
                                                        <div class="btn-group btn-group-sm" role="group"> <?php if ($index === 0): // Only show edit button for the latest version 
                                                                                                            ?>
                                                                <?php if ($canEdit): ?>
                                                                    <a href="<?= base_url('abstract-paper/edit/' . $participant_data['abstract']['id'] . '/' . $versionNum) ?>"
                                                                        class="btn btn-primary btn-sm view-version-btn"
                                                                        data-abstract-id="<?= $participant_data['abstract']['id'] ?>"
                                                                        data-version-id="<?= $version['id'] ?>">
                                                                        <i class="bx bx-edit me-1"></i> Edit
                                                                    </a> <?php else: ?>
                                                                    <button type="button" class="btn btn-secondary btn-sm" disabled>
                                                                        <i class="bx bx-lock me-1"></i>
                                                                        <?php if ($abstractStatus === 'accepted'): ?>
                                                                            Edit Disabled - Accepted
                                                                        <?php elseif ($abstractStatus === 'submitted' && !$hasFeedback): ?>
                                                                            Edit Disabled - Submitted
                                                                        <?php else: ?>
                                                                            Edit Disabled
                                                                        <?php endif; ?>
                                                                    </button>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <button type="button" class="btn btn-secondary btn-sm view-version-btn"
                                                                    data-abstract-id="<?= $participant_data['abstract']['id'] ?>"
                                                                    data-version-id="<?= $version['id'] ?>">
                                                                    <i class="bx bx-show me-1"></i> View
                                                                </button>
                                                            <?php endif; ?> <?php if ($index > 0): // Show compare button for previous versions 
                                                                            ?>                                                                <a href="/abstract-paper/compare/<?= $version['id'] ?>/<?= $sortedVersions[0]['id'] ?>"
                                                                    class="btn btn-outline-secondary btn-sm compare-version-btn"
                                                                    title="Compare this version with the latest version"
                                                                    onclick="showComparisonLoading(event)">
                                                                    <i class="bx bx-git-compare me-1"></i> Compare with Latest
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div> <!-- Abstract Content -->
                                                <div class="mb-4">
                                                    <h6 class="fw-semibold mb-2"><i class="bx bx-file-blank text-primary me-2"></i>Abstract Content</h6>
                                                    <div class="bg-light p-3 rounded">
                                                        <?php
                                                        $versionContent = $version['content'] ?? '';

                                                        if (isContentEmpty($versionContent)):
                                                        ?>
                                                            <span class="text-muted fst-italic">No content available</span>
                                                        <?php else: ?>
                                                            <?= $versionContent ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div><!-- Keywords Section -->
                                                <div class="mb-4">
                                                    <h6 class="fw-semibold mb-2"><i class="bx bx-key text-primary me-2"></i>Keywords</h6>
                                                    <div class="bg-light p-3 rounded">
                                                        <?php if (!empty($version['keywords'])): ?>
                                                            <?php foreach (explode(',', $version['keywords']) as $keyword): ?>
                                                                <span class="badge bg-soft-primary text-primary me-1 mb-1"><?= trim(esc($keyword)) ?></span>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">No keywords provided</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- References Section -->
                                                <div class="mb-4">
                                                    <h6 class="fw-semibold mb-2"><i class="bx bx-book-content text-primary me-2"></i>References</h6>
                                                    <div class="bg-light p-3 rounded">
                                                        <?php if (!empty($version['refs'])): ?>
                                                            <div class="text-break" style="white-space: pre-line;"><?= esc($version['refs']) ?></div>
                                                        <?php else: ?>
                                                            <span class="text-muted">No references provided</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-1"></i> No version history available for this abstract.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <?php if (!empty($participant_data['abstract']['versions']) && count($participant_data['abstract']['versions']) > 1): ?>
                        <a href="<?= base_url('abstract-paper/versions/' . $participant_data['abstract']['id']) ?>" class="btn btn-primary">
                            <i class="bx bx-list-ul me-1"></i> View All Versions
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Authors Modal -->
    <div class="modal fade" id="addCoAuthorModal" tabindex="-1" aria-labelledby="addCoAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCoAuthorModalLabel">
                        <i class="bx bx-user-plus me-2"></i>Manage Authors
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Navigation Tabs -->
                    <div class="bg-light border-bottom">
                        <ul class="nav nav-tabs nav-tabs-custom border-0 m-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active px-4 py-3" data-bs-toggle="tab" href="#authorList" role="tab">
                                    <i class="bx bx-list-ul me-2"></i>Current Authors
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-4 py-3" data-bs-toggle="tab" href="#addAuthor" role="tab">
                                    <i class="bx bx-plus-circle me-2"></i>Add New Author
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content p-4">
                        <!-- Author List Tab Content -->
                        <div class="tab-pane fade show active" id="authorList" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="text-primary mb-0">
                                            <i class="bx bx-users me-2"></i>Authors for this Abstract
                                        </h6>
                                        <span class="badge bg-soft-primary text-primary">
                                            <?= count($participant_data['abstract']['authors'] ?? []) ?> Author(s)
                                        </span>
                                    </div>

                                    <?php if (!empty($participant_data['abstract']['authors'])): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="border-0 fw-semibold">#</th>
                                                        <th class="border-0 fw-semibold">Author Details</th>
                                                        <th class="border-0 fw-semibold">Institution</th>
                                                        <th class="border-0 fw-semibold">Role</th>
                                                        <th class="border-0 fw-semibold text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody> <?php foreach ($participant_data['abstract']['authors'] as $index => $author): ?>
                                                        <tr class="border-bottom">
                                                            <td class="fw-medium"><?= $index + 1 ?></td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-sm me-3">
                                                                        <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                            <i class="bx bx-user fs-5"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-1 fw-semibold"><?= esc($author['full_name']) ?></h6>
                                                                        <p class="text-muted mb-0 small">
                                                                            <i class="bx bx-envelope me-1"></i><?= esc($author['email'] ?? 'No email provided') ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="text-truncate" style="max-width: 200px;">
                                                                    <i class="bx bx-buildings me-1 text-muted"></i>
                                                                    <?= esc($author['institution'] ?? 'Not specified') ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="text-center">
                                                                    <?php if (isset($author['is_participant']) && $author['is_participant'] == '1'): ?>
                                                                        <span class="badge bg-primary fs-6 px-3 py-2">
                                                                            <i class="bx bx-star me-1"></i>Primary Author
                                                                        </span>
                                                                    <?php elseif (isset($author['is_presenting']) && $author['is_presenting'] == '1'): ?>
                                                                        <span class="badge bg-success fs-6 px-3 py-2">
                                                                            <i class="bx bx-microphone me-1"></i>Presenting Author
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-secondary fs-6 px-3 py-2">
                                                                            <i class="bx bx-user me-1"></i>Co-Author
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-center gap-1">
                                                                    <button type="button" class="btn btn-soft-info btn-sm view-author"
                                                                        data-author='<?= json_encode($author) ?>'
                                                                        data-bs-toggle="tooltip" title="View Details">
                                                                        <i class="bx bx-show"></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-soft-primary btn-sm edit-author"
                                                                        data-author='<?= json_encode($author) ?>'
                                                                        data-bs-toggle="tooltip" title="Edit Author">
                                                                        <i class="bx bx-edit"></i>
                                                                    </button>
                                                                    <?php if (!(isset($author['is_participant']) && $author['is_participant'] == '1')): ?>
                                                                        <button type="button" class="btn btn-soft-danger btn-sm delete-author"
                                                                            data-author-id="<?= $author['id'] ?>"
                                                                            data-author-name="<?= esc($author['full_name']) ?>"
                                                                            data-bs-toggle="tooltip" title="Remove Author">
                                                                            <i class="bx bx-trash"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <div class="avatar-lg mx-auto mb-4">
                                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                    <i class="bx bx-user-plus fs-2"></i>
                                                </div>
                                            </div>
                                            <h5 class="text-muted mb-3">No Authors Added Yet</h5>
                                            <p class="text-muted mb-4">Click "Add New Author" tab to add co-authors to your abstract.</p>
                                            <button type="button" class="btn btn-primary" onclick="document.querySelector('[href=&quot;#addAuthor&quot;]').click()">
                                                <i class="bx bx-plus me-1"></i>Add Your First Author
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div><!-- Add Author Tab Content -->
                        <div class="tab-pane fade" id="addAuthor" role="tabpanel">
                            <form id="addAuthorForm" action="<?= base_url('abstract-paper/add-author') ?>" method="post">
                                <input type="hidden" name="abstract_id" value="<?= $participant_data['abstract']['id'] ?>">
                                <input type="hidden" name="program_id" value="<?= session()->get('current_program_id') ?>">
                                <input type="hidden" name="participant_id" id="selected_participant_id">

                                <!-- Author Type Selection Cards -->
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3 text-primary">
                                        <i class="bx bx-user-plus me-2"></i>Author Information
                                    </h6>
                                    <p class="text-muted mb-3">Please specify if this author is already registered in the program or is a new contributor.</p>

                                    <div class="row g-3">
                                        <!-- Registered Participant Card -->
                                        <div class="col-md-6">
                                            <div class="card border-2 h-100 author-type-card" data-type="participant">
                                                <div class="card-body text-center p-4">
                                                    <div class="avatar-lg mx-auto mb-3">
                                                        <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                            <i class="bx bx-user-check fs-2"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="card-title mb-2">Registered Participant</h5>
                                                    <p class="card-text text-muted small">
                                                        This author is already registered in the current program.
                                                        We'll search by email to auto-fill their details.
                                                    </p>
                                                    <input type="radio" name="is_participant" value="yes" id="is_participant_yes" class="form-check-input d-none">
                                                    <label for="is_participant_yes" class="btn btn-outline-success btn-sm">
                                                        <i class="bx bx-search me-1"></i>Search Participant
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- New Author Card -->
                                        <div class="col-md-6">
                                            <div class="card border-2 h-100 author-type-card selected" data-type="new">
                                                <div class="card-body text-center p-4">
                                                    <div class="avatar-lg mx-auto mb-3">
                                                        <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                            <i class="bx bx-user-plus fs-2"></i>
                                                        </div>
                                                    </div>
                                                    <h5 class="card-title mb-2">New Author</h5>
                                                    <p class="card-text text-muted small">
                                                        This author is not registered in the program.
                                                        You'll need to manually enter their information.
                                                    </p>
                                                    <input type="radio" name="is_participant" value="no" id="is_participant_no" class="form-check-input d-none" checked>
                                                    <label for="is_participant_no" class="btn btn-primary btn-sm">
                                                        <i class="bx bx-plus me-1"></i>Add Manually
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Participant Search Section -->
                                <div id="participant_search_section" class="mb-4" style="display: none;">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-primary text-white rounded-circle">
                                                        <i class="bx bx-search fs-5"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Search Registered Participant</h6>
                                                    <p class="text-muted mb-0 small">Enter the participant's email address to search</p>
                                                </div>
                                            </div>

                                            <div class="input-group mb-3">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bx bx-envelope text-muted"></i>
                                                </span>
                                                <input type="email" class="form-control border-start-0" id="search_email"
                                                    placeholder="participant@example.com" autocomplete="off">
                                                <button type="button" class="btn btn-primary" id="search_participant_btn">
                                                    <i class="bx bx-search me-1"></i>
                                                    <span class="btn-text">Search</span>
                                                    <span class="spinner-border spinner-border-sm d-none" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </span>
                                                </button>
                                            </div>

                                            <div id="search_result"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Author Details Form -->
                                <div id="author_details_section">
                                    <div class="card">
                                        <div class="card-header bg-white">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-info text-white rounded-circle">
                                                        <i class="bx bx-user fs-5"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Author Details</h6>
                                                    <p class="text-muted mb-0 small">Enter the author's information below</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="full_name" class="form-label fw-semibold">
                                                        <i class="bx bx-user me-1 text-primary"></i>Full Name
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                                        placeholder="Enter author's full name" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="email" class="form-label fw-semibold">
                                                        <i class="bx bx-envelope me-1 text-primary"></i>Email Address
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="author@example.com" required>
                                                    <div class="form-text text-muted">
                                                        <small><i class="bx bx-info-circle me-1"></i>We'll check if this email can be added to your abstract</small>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for="institution" class="form-label fw-semibold">
                                                        <i class="bx bx-buildings me-1 text-primary"></i>Institution/Organization
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="institution" name="institution"
                                                        placeholder="University or organization name" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button type="button" class="btn btn-light me-md-2" data-bs-dismiss="modal">
                                        <i class="bx bx-x me-1"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-user-plus me-1"></i>Add Author
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Author Modal -->
    <div class="modal fade" id="editAuthorModal" tabindex="-1" aria-labelledby="editAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editAuthorModalLabel">
                        <i class="bx bx-edit me-1"></i> Edit Author
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAuthorForm" action="<?= base_url('abstract-paper/update-author') ?>" method="post">
                        <input type="hidden" name="author_id" id="edit_author_id">
                        <input type="hidden" name="abstract_id" value="<?= $participant_data['abstract']['id'] ?>">

                        <div class="mb-3">
                            <label for="edit_full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_institution" class="form-label">Institution <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_institution" name="institution" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_address" class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                        </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editAuthorForm" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- View Author Modal -->
<div class="modal fade" id="viewAuthorModal" tabindex="-1" aria-labelledby="viewAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewAuthorModalLabel">
                    <i class="bx bx-user-circle me-1"></i> Author Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="avatar avatar-lg">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                            <i class="bx bxs-user-circle fs-1"></i>
                        </span>
                    </div>
                    <h5 class="mt-3 mb-1" id="view_full_name">John Doe</h5>
                    <div class="mb-2" id="view_role_badge">
                        <span class="badge bg-secondary">Co-Author</span>
                    </div>
                </div>

                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="bx bx-envelope text-primary me-2"></i>Email</h6>
                        </div>
                        <p class="mb-1" id="view_email">example@example.com</p>
                    </div>
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="bx bx-buildings text-primary me-2"></i>Institution</h6>
                        </div>
                        <p class="mb-1" id="view_institution">University of Example</p>
                    </div>
                    <div class="list-group-item px-0" id="view_address_container">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><i class="bx bx-map text-primary me-2"></i>Address</h6>
                        </div>
                        <p class="mb-1" id="view_address">123 Example St, City, Country</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary edit-from-view">
                    <i class="bx bx-edit me-1"></i> Edit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Author Confirmation Modal -->
<div class="modal fade" id="deleteAuthorModal" tabindex="-1" aria-labelledby="deleteAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAuthorModalLabel">
                    <i class="bx bx-trash me-1"></i> Delete Author
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bx bx-error-circle text-danger fs-1"></i>
                    <h4 class="mt-2">Are you sure?</h4>
                    <p class="text-muted">Do you really want to delete <strong id="delete_author_name"></strong>? This action cannot be undone.</p>
                </div>
                <form id="deleteAuthorForm" action="<?= base_url('abstract-paper/delete-author') ?>" method="post">
                    <input type="hidden" name="author_id" id="delete_author_id">
                    <input type="hidden" name="abstract_id" value="<?= $participant_data['abstract']['id'] ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAuthorForm" class="btn btn-danger">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Version Compare Modal -->
<div class="modal fade" id="versionCompareModal" tabindex="-1" aria-labelledby="versionCompareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="versionCompareModalLabel">
                    <i class="bx bx-git-compare me-1"></i> Version Comparison
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be populated dynamically via JavaScript -->
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure jQuery is loaded
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery is not loaded in abstract-view.php. Loading it now...');

            // Dynamically load jQuery if it's not available
            const script = document.createElement('script');
            script.src = '/assets/libs/jquery/jquery.min.js';
            script.onload = function() {
                console.log('jQuery has been dynamically loaded in abstract-view.php');
                initAbstractViewFunctions();
            };
            document.head.appendChild(script);
        } else {
            // jQuery is already available
            initAbstractViewFunctions();
        }
    }); // Initialize all the functions after ensuring jQuery is available
    function initAbstractViewFunctions() {
        // Initialize version history functionality
        if (typeof setupVersionHistory === 'function') {
            setupVersionHistory();
        }

        // Initialize edit button behavior
        if (typeof setupEditButtonBehavior === 'function') {
            setupEditButtonBehavior();
        }

        // Initialize tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize author management functionality
        initAuthorManagement();

        // Initialize enhanced author type cards
        initAuthorTypeCards();

        // Initialize enhanced search functionality
        initEnhancedSearch();
    }

    // Function to initialize author management functionality
    function initAuthorManagement() {
        // Handle participant search radio buttons
        const participantRadios = document.querySelectorAll('input[name="is_participant"]');
        const searchSection = document.getElementById('participant_search_section');
        const searchBtn = document.getElementById('search_participant_btn');
        const searchEmail = document.getElementById('search_email');
        const searchResult = document.getElementById('search_result');

        // Form fields
        const fullNameField = document.getElementById('full_name');
        const emailField = document.getElementById('email');
        const institutionField = document.getElementById('institution');
        const addressField = document.getElementById('address');
        const participantIdField = document.getElementById('selected_participant_id');

        // Toggle participant search section
        participantRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'yes') {
                    searchSection.style.display = 'block';
                    searchEmail.required = true;
                } else {
                    searchSection.style.display = 'none';
                    searchEmail.required = false;
                    // Clear search results and form fields when switching to "No"
                    clearSearchResults();
                    clearAuthorForm();
                }
            });
        });

        // Handle participant search
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                const email = searchEmail.value.trim();
                const programId = document.querySelector('input[name="program_id"]').value;

                if (!email) {
                    showSearchResult('error', 'Please enter an email address to search.');
                    return;
                }

                if (!isValidEmail(email)) {
                    showSearchResult('error', 'Please enter a valid email address.');
                    return;
                }

                // Show loading state
                searchBtn.disabled = true;
                searchBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Searching...';

                // Make AJAX request to search for participant
                fetch('<?= base_url("abstract-paper/search-participant") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `email=${encodeURIComponent(email)}&program_id=${encodeURIComponent(programId)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.found) {
                                // Participant found - populate form fields
                                const participant = data.participant;
                                fillAuthorForm(participant);
                                showSearchResult('success', `Participant found: ${participant.full_name}. Details have been filled automatically.`);
                            } else {
                                // No participant found
                                showSearchResult('warning', data.message || 'No registered participant found with this email address.');
                                clearAuthorForm();
                            }
                        } else {
                            showSearchResult('error', data.message || 'An error occurred while searching for the participant.');
                            clearAuthorForm();
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        showSearchResult('error', 'An unexpected error occurred. Please try again.');
                        clearAuthorForm();
                    })
                    .finally(() => {
                        // Reset button state
                        searchBtn.disabled = false;
                        searchBtn.innerHTML = '<i class="bx bx-search me-1"></i> Search';
                    });
            });
        } // Allow search on Enter key
        if (searchEmail) {
            searchEmail.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchBtn.click();
                }
            });
        } // Handle view author button clicks
        document.addEventListener('click', function(e) {
            if (e.target.closest('.view-author')) {
                const button = e.target.closest('.view-author');
                const authorData = JSON.parse(button.getAttribute('data-author'));
                showAuthorDetails(authorData);
            }
        });

        // Function to show author details in modal
        function showAuthorDetails(author) {
            // Populate modal fields
            document.getElementById('view_full_name').textContent = author.full_name || 'N/A';
            document.getElementById('view_email').textContent = author.email || 'N/A';
            document.getElementById('view_institution').textContent = author.institution || 'N/A';
            document.getElementById('view_address').textContent = author.address || 'No address provided';

            // Set role badge
            const roleBadge = document.getElementById('view_role_badge');
            if (author.is_participant == '1') {
                roleBadge.innerHTML = '<span class="badge bg-primary fs-6"><i class="bx bx-star me-1"></i>Primary Author</span>';
            } else if (author.is_presenting == '1') {
                roleBadge.innerHTML = '<span class="badge bg-success fs-6"><i class="bx bx-microphone me-1"></i>Presenting Author</span>';
            } else {
                roleBadge.innerHTML = '<span class="badge bg-secondary fs-6"><i class="bx bx-user me-1"></i>Co-Author</span>';
            }

            // Show the modal
            const viewModal = new bootstrap.Modal(document.getElementById('viewAuthorModal'));
            viewModal.show();
        }

        // Helper functions
        function showAuthorDetails(author) {
            // Populate modal fields
            document.getElementById('view_full_name').textContent = author.full_name || 'N/A';
            document.getElementById('view_email').textContent = author.email || 'N/A';
            document.getElementById('view_institution').textContent = author.institution || 'N/A';
            document.getElementById('view_address').textContent = author.address || 'No address provided';

            // Set role badge
            const roleBadge = document.getElementById('view_role_badge');
            if (author.is_participant == '1') {
                roleBadge.innerHTML = '<span class="badge bg-primary fs-6"><i class="bx bx-star me-1"></i>Primary Author</span>';
            } else if (author.is_presenting == '1') {
                roleBadge.innerHTML = '<span class="badge bg-success fs-6"><i class="bx bx-microphone me-1"></i>Presenting Author</span>';
            } else {
                roleBadge.innerHTML = '<span class="badge bg-secondary fs-6"><i class="bx bx-user me-1"></i>Co-Author</span>';
            }

            // Show the modal
            const viewModal = new bootstrap.Modal(document.getElementById('viewAuthorModal'));
            viewModal.show();
        }

        function showSearchResult(type, message) {
            let alertClass = 'alert-info';
            let icon = 'bx-info-circle';

            switch (type) {
                case 'success':
                    alertClass = 'alert-success';
                    icon = 'bx-check-circle';
                    break;
                case 'warning':
                    alertClass = 'alert-warning';
                    icon = 'bx-error-circle';
                    break;
                case 'error':
                    alertClass = 'alert-danger';
                    icon = 'bx-x-circle';
                    break;
            }

            searchResult.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bx ${icon} me-1"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        }

        function clearSearchResults() {
            searchResult.innerHTML = '';
        }

        function fillAuthorForm(participant) {
            fullNameField.value = participant.full_name || '';
            emailField.value = participant.email || '';
            institutionField.value = participant.institution || '';
            addressField.value = participant.address || '';
            participantIdField.value = participant.id || '';

            // Make fields readonly since they're from a registered participant
            fullNameField.readOnly = true;
            emailField.readOnly = true;
            institutionField.readOnly = true;
            addressField.readOnly = true;

            // Add visual indicators
            [fullNameField, emailField, institutionField, addressField].forEach(field => {
                field.classList.add('bg-light');
                field.title = 'This field is auto-filled from registered participant data';
            });
        }

        function clearAuthorForm() {
            fullNameField.value = '';
            emailField.value = '';
            institutionField.value = '';
            addressField.value = '';
            participantIdField.value = '';

            // Make fields editable again
            fullNameField.readOnly = false;
            emailField.readOnly = false;
            institutionField.readOnly = false;
            addressField.readOnly = false;

            // Remove visual indicators
            [fullNameField, emailField, institutionField, addressField].forEach(field => {
                field.classList.remove('bg-light');
                field.removeAttribute('title');
            });
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Enhanced author type card selection functionality
        function initAuthorTypeCards() {
            const authorTypeCards = document.querySelectorAll('.author-type-card');
            const participantSearchSection = document.getElementById('participant_search_section');
            const searchEmailField = document.getElementById('search_email');

            authorTypeCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    authorTypeCards.forEach(c => c.classList.remove('selected'));

                    // Add selected class to clicked card
                    this.classList.add('selected');

                    // Get the corresponding radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;

                        // Show/hide participant search section based on selection
                        if (radio.value === 'yes') {
                            participantSearchSection.style.display = 'block';
                            searchEmailField.focus();
                            clearAuthorForm();
                        } else {
                            participantSearchSection.style.display = 'none';
                            clearSearchResult();
                            clearAuthorForm();
                            // Focus on first form field for manual entry
                            setTimeout(() => {
                                const fullNameField = document.getElementById('full_name');
                                if (fullNameField) fullNameField.focus();
                            }, 100);
                        }
                    }
                });
            });
        }

        // Enhanced search functionality with better UI feedback
        function initEnhancedSearch() {
            const searchBtn = document.getElementById('search_participant_btn');
            const searchEmail = document.getElementById('search_email');

            if (searchBtn && searchEmail) {
                searchBtn.addEventListener('click', function() {
                    performParticipantSearch();
                });

                searchEmail.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performParticipantSearch();
                    }
                });

                // Real-time email validation
                searchEmail.addEventListener('input', function() {
                    const email = this.value.trim();
                    if (email && !isValidEmail(email)) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            }
        }

        function performParticipantSearch() {
            const searchEmail = document.getElementById('search_email');
            const searchBtn = document.getElementById('search_participant_btn');
            const btnText = searchBtn.querySelector('.btn-text');
            const btnSpinner = searchBtn.querySelector('.spinner-border');

            const email = searchEmail.value.trim();
            const programId = document.querySelector('input[name="program_id"]').value;

            if (!email) {
                showEnhancedSearchResult('error', 'Please enter an email address to search.', 'bx-x-circle');
                searchEmail.focus();
                return;
            }

            if (!isValidEmail(email)) {
                showEnhancedSearchResult('error', 'Please enter a valid email address.', 'bx-x-circle');
                searchEmail.focus();
                return;
            }

            // Show loading state
            searchBtn.disabled = true;
            btnText.textContent = 'Searching...';
            btnSpinner.classList.remove('d-none');
            searchEmail.classList.remove('is-invalid');

            // Make AJAX request
            fetch('<?= base_url("abstract-paper/search-participant") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `email=${encodeURIComponent(email)}&program_id=${encodeURIComponent(programId)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.found) {
                            const participant = data.participant;
                            fillAuthorForm(participant);
                            showEnhancedSearchResult('success',
                                `<strong>Participant found!</strong><br>
                             <span class="small">Details for <strong>${participant.full_name}</strong> have been loaded automatically.</span>`,
                                'bx-check-circle');
                        } else {
                            showEnhancedSearchResult('warning',
                                `<strong>No participant found</strong><br>
                             <span class="small">No registered participant found with email: <strong>${email}</strong></span>`,
                                'bx-info-circle');
                            clearAuthorForm();
                        }
                    } else {
                        showEnhancedSearchResult('error',
                            `<strong>Search failed</strong><br>
                         <span class="small">${data.message || 'An error occurred while searching.'}</span>`,
                            'bx-x-circle');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showEnhancedSearchResult('error',
                        `<strong>Connection error</strong><br>
                     <span class="small">Please check your connection and try again.</span>`,
                        'bx-wifi-off');
                })
                .finally(() => {
                    // Reset button state
                    searchBtn.disabled = false;
                    btnText.textContent = 'Search';
                    btnSpinner.classList.add('d-none');
                });
        }

        function showEnhancedSearchResult(type, message, icon) {
            const searchResult = document.getElementById('search_result');
            const alertClasses = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            };

            const iconClasses = {
                'success': 'text-success',
                               'error': 'text-danger',
                'warning': 'text-warning',
                'info': 'text-info'
            };

            searchResult.innerHTML = `
                <div class="alert ${alertClasses[type]} border-0 shadow-sm fade show" role="alert">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx ${icon} fs-5 ${iconClasses[type]}"></i>
                        </div>
                        <div class="flex-grow-1">
                            ${message}
                        </div>
                    </div>
                </div>
            `;
        }

        function clearSearchResult() {
            const searchResult = document.getElementById('search_result');
            if (searchResult) {
                searchResult.innerHTML = '';
            }
        }
    }

    // Function to show loading overlay when navigating to edit page
    function showLoading(event) {
        // Create and add loading overlay to the body
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loadingOverlay';
        loadingOverlay.style.position = 'fixed';
        loadingOverlay.style.top = '0';
        loadingOverlay.style.left = '0';
        loadingOverlay.style.width = '100%';
        loadingOverlay.style.height = '100%';
        loadingOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        loadingOverlay.style.zIndex = '9999';
        loadingOverlay.style.display = 'flex';
        loadingOverlay.style.justifyContent = 'center';
        loadingOverlay.style.alignItems = 'center';

        // Create spinner
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border text-light';
        spinner.setAttribute('role', 'status');
        spinner.style.width = '3rem';
        spinner.style.height = '3rem';

        // Add spinner to loading overlay
        loadingOverlay.appendChild(spinner);

        // Add loading overlay to body
        document.body.appendChild(loadingOverlay);
    }

    function showComparisonLoading(event) {
        event.preventDefault();
        const href = event.currentTarget.getAttribute('href');
        
        Swal.fire({
            title: 'Comparing Versions',
            html: 'Please wait while we analyze the differences...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                // Navigate to comparison page after showing loading
                window.location.href = href;
            }
        });
    }
</script>