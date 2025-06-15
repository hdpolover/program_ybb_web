<?php
// Define essential variables for this component with safety checks
$participant_data = $participant_data ?? [];
$abstract = $participant_data['abstract'] ?? [];
$abstractStatus = isset($abstract['status']) ? strtolower($abstract['status']) : 'draft';
?>

<div class="card border shadow-sm h-100">    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
            <i class="bx bx-file text-primary me-2"></i>Full Paper
        </h6>
        <?php if ($abstractStatus === 'accepted'): ?>
            <span class="badge bg-danger">Required</span>
        <?php else: ?>
            <span class="badge bg-secondary">Not Available</span>
        <?php endif; ?>
    </div>

    <div class="card-body paper-upload-state">
        <?php
        $hasPaper = !empty($participant_data['abstract']['paper_file_path']);
        $paperFileName = $participant_data['abstract']['paper_file_name'] ?? '';
        $paperUploadDate = $participant_data['abstract']['paper_uploaded_at'] ?? '';
        ?>

        <?php if ($hasPaper): ?>
            <!-- Paper Uploaded State -->
            <div class="text-center">
                <div class="mb-3">
                    <div class="avatar-lg mx-auto">
                        <div class="avatar-title bg-soft-success text-success rounded-circle">
                            <i class="bx bx-file-pdf fs-2"></i>
                        </div>
                    </div>
                </div>

                <h6 class="fw-semibold text-success mb-2">
                    <i class="bx bx-check-circle me-1"></i>Paper Uploaded
                </h6>

                <div class="mb-3">
                    <p class="text-muted mb-1 fw-medium"><?= esc($paperFileName) ?></p>
                    <small class="text-muted">
                        <i class="bx bx-calendar me-1"></i>
                        Uploaded: <?= date('M d, Y h:i A', strtotime($paperUploadDate)) ?>
                    </small>
                </div>                <div class="d-grid gap-2">
                    <a href="<?= base_url('abstract-paper/download-paper/' . $participant_data['abstract']['id']) ?>"
                        class="btn btn-primary btn-sm">
                        <i class="bx bx-download me-1"></i> Download Paper
                    </a>

                    <?php if ($abstractStatus === 'accepted'): ?>
                        <button type="button" class="btn btn-outline-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#replacePaperModal">
                            <i class="bx bx-edit me-1"></i> Replace Paper
                        </button>

                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#deletePaperModal">
                            <i class="bx bx-trash me-1"></i> Remove Paper
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                            title="Paper management is only available when your abstract is accepted">
                            <i class="bx bx-lock me-1"></i> Replace Restricted
                        </button>

                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                            title="Paper management is only available when your abstract is accepted">
                            <i class="bx bx-lock me-1"></i> Remove Restricted
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- No Paper State -->
            <div class="text-center">
                <div class="mb-3">
                    <div class="avatar-lg mx-auto">
                        <div class="avatar-title bg-soft-secondary text-secondary rounded-circle">
                            <i class="bx bx-file-plus fs-2"></i>
                        </div>
                    </div>
                </div>

                <?php if ($abstractStatus === 'accepted'): ?>
                    <h6 class="fw-semibold text-warning mb-2">
                        <i class="bx bx-exclamation-triangle me-1"></i>Paper Required
                    </h6>
                    <p class="text-muted small mb-3">
                        Your abstract has been accepted! Please upload your full paper to complete the submission process.
                    </p>                <?php else: ?>
                    <h6 class="fw-semibold text-muted mb-2">
                        <i class="bx bx-info-circle me-1"></i>No Paper Uploaded
                    </h6>
                    <p class="text-muted small mb-3">
                        <?php if ($abstractStatus === 'accepted'): ?>
                            You can now upload your full paper since your abstract has been accepted.
                        <?php else: ?>
                            Paper upload will be available once your abstract is accepted.
                        <?php endif; ?>
                    </p>
                <?php endif; ?><div class="d-grid">
                    <?php if ($abstractStatus === 'accepted'): ?>
                        <button type="button" class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#uploadPaperModal">
                            <i class="bx bx-upload me-1"></i> Upload Paper
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-sm" disabled
                            title="Paper upload is only available when your abstract is accepted">
                            <i class="bx bx-lock me-1"></i> Upload Restricted
                        </button>
                    <?php endif; ?>
                    </button>
                </div>

                <!-- Upload Guidelines -->
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted">
                        <strong>Guidelines:</strong><br>
                        • PDF format only<br>
                        • Maximum size: 10MB<br>
                        • Include all references
                    </small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
