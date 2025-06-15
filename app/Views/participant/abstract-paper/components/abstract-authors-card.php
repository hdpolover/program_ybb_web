<?php
// Define essential variables for this component with safety checks
$participant_data = $participant_data ?? [];
$abstract = $participant_data['abstract'] ?? [];

// Check edit permissions (same logic as other components)
$abstractStatus = isset($abstract['status']) ? strtolower($abstract['status']) : 'draft';
$hasFeedback = !empty($abstract['feedbacks']);
$canEdit = ($abstractStatus === 'draft' || $abstractStatus === 'under_review');

// Never allow editing if status is anything other than draft or under_review
?>
<div class="card border shadow-sm h-100">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h6 class="mb-0 fw-semibold me-2">
                <i class="bx bx-users text-primary me-2"></i>Authors
            </h6>
            <span class="badge bg-primary"><?= count($participant_data['abstract']['authors'] ?? []) ?></span>
        </div>
        <?php if ($canEdit): ?>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCoAuthorModal">
                <i class="bx bx-plus me-1"></i> Manage
            </button> <?php else: ?>
            <span class="text-muted small">
                <i class="bx bx-lock me-1"></i>
                <?php if ($abstractStatus === 'accepted'): ?>
                    Management disabled - Abstract accepted
                <?php elseif ($abstractStatus === 'submitted'): ?>
                    Management disabled - Abstract submitted
                <?php else: ?>
                    Management disabled
                <?php endif; ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="card-body" style="min-height: 300px; overflow-y: auto;">
        <?php if (!empty($participant_data['abstract']['authors'])): ?>
            <div class="author-list-compact">
                <?php foreach ($participant_data['abstract']['authors'] as $index => $author): ?>
                    <div class="author-item d-flex align-items-center p-2 rounded mb-2">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                    <i class="bx bx-user fs-5"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex-grow-1 min-width-0">
                            <h6 class="mb-1 text-truncate fw-semibold"><?= esc($author['full_name']) ?></h6>
                            <p class="text-muted mb-1 small text-truncate">
                                <i class="bx bx-envelope me-1"></i><?= esc($author['email'] ?? 'No email provided') ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted text-truncate me-2">
                                    <i class="bx bx-buildings me-1"></i><?= esc($author['institution'] ?? 'Not specified') ?>
                                </small>
                                <div class="flex-shrink-0">
                                    <?php if (isset($author['is_participant']) && $author['is_participant'] == '1'): ?>
                                        <span class="badge bg-primary">Primary</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Co-Author</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="flex-shrink-0">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm view-author-btn"
                                    data-author='<?= json_encode($author) ?>'
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewAuthorModal"
                                    title="View Details">
                                    <i class="bx bx-show"></i>
                                </button>
                                <?php if (!isset($author['is_participant']) || $author['is_participant'] != '1'): ?>
                                    <?php if ($canEdit): ?>
                                        <button type="button" class="btn btn-outline-warning btn-sm edit-author-btn"
                                            data-author='<?= json_encode($author) ?>'
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAuthorModal"
                                            title="Edit Author">
                                            <i class="bx bx-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-outline-danger btn-sm delete-author-btn"
                                            data-author-id="<?= $author['id'] ?>"
                                            data-author-name="<?= esc($author['full_name']) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteAuthorModal"
                                            title="Remove Author">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Editing disabled">
                                            <i class="bx bx-lock"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Primary author cannot be edited">
                                        <i class="bx bx-lock"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div> <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="bx bx-user-plus text-muted mb-3" style="font-size: 3rem;"></i>
                <h6 class="text-muted mb-2">No Authors Added</h6>
                <p class="text-muted small mb-3">
                    Add co-authors to collaborate on this abstract.
                </p>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCoAuthorModal">
                    <i class="bx bx-plus me-1"></i> Add First Author
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>