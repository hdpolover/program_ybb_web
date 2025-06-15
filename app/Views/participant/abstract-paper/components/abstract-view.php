<!-- Abstract exists - Show the detailed view -->
<div class="abstract-layout">
    <?= $this->include('participant/abstract-paper/components/abstract-view-styles') ?>

    <?= $this->include('participant/abstract-paper/components/abstract-view-helpers') ?>

    <?= $this->include('participant/abstract-paper/components/abstract-header') ?>

    <?= $this->include('participant/abstract-paper/components/abstract-status-alerts') ?>

    <!-- First Row: Abstract Content (12) - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <?= $this->include('participant/abstract-paper/components/abstract-content-card') ?>
        </div>
    </div>    <!-- Second Row: Quick Info Card - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <?= $this->include('participant/abstract-paper/components/abstract-quick-info') ?>
        </div>
    </div>    <!-- Third Row: Authors (4) + Paper (8) - Paper Card is Wider -->
    <div class="row">
        <!-- Authors Information Card -->
        <div class="col-lg-4">
            <?= $this->include('participant/abstract-paper/components/abstract-authors-card') ?>
        </div>

        <!-- Paper Upload Section -->
        <div class="col-lg-8">
            <?= $this->include('participant/abstract-paper/components/abstract-paper-card') ?>
        </div>
    </div>

    <!-- Include Paper Upload Modals -->
    <?= $this->include('participant/abstract-paper/components/paper-upload-modals') ?>    <!-- Version History Modal -->
    <div class="modal fade" id="versionHistoryModal" tabindex="-1" aria-labelledby="versionHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 900px;">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="versionHistoryModalLabel">
                        <i class="bx bx-history me-1"></i> Abstract Version History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                <div class="modal-body">                    <?php 
                    // Ensure we have the versions data - try multiple sources
                    $modalVersions = $versions ?? $participant_data['abstract']['versions'] ?? [];
                    
                    // Ensure it's an array
                    if (!is_array($modalVersions)) {
                        $modalVersions = [];
                    }
                    
                    // Sort versions by version_number descending (latest first)
                    if (!empty($modalVersions)) {
                        usort($modalVersions, function($a, $b) {
                            // Primary sort by version_number descending
                            $versionCompare = intval($b['version_number']) - intval($a['version_number']);
                            if ($versionCompare !== 0) {
                                return $versionCompare;
                            }
                            // Secondary sort by created_at descending
                            return strtotime($b['created_at']) - strtotime($a['created_at']);
                        });
                    }
                    ?>
                    
                    <!-- Simple debug info (remove this after testing) -->
                    <?php if (empty($modalVersions)): ?>
                        <div class="alert alert-warning mb-3">
                            <strong>Debug:</strong> No versions found. 
                            Versions count: <?= count($versions ?? []) ?>, 
                            Abstract versions count: <?= count($participant_data['abstract']['versions'] ?? []) ?>
                        </div>
                    <?php endif; ?>                    <?php if (!empty($modalVersions) && is_array($modalVersions)): ?>
                        <div class="version-timeline-info">
                            <div class="d-flex align-items-center text-muted">
                                <i class="bx bx-info-circle me-2 text-primary"></i>
                                <small><strong><?= count($modalVersions) ?></strong> version<?= count($modalVersions) > 1 ? 's' : '' ?> available • Latest to oldest</small>
                            </div>
                        </div>
                        <div class="accordion" id="versionAccordion">                            <?php foreach ($modalVersions as $index => $version): ?>
                                <?php 
                                // Check if this version has feedback
                                $hasVersionFeedback = false;
                                if (!empty($participant_data['abstract']['feedbacks'])) {
                                    foreach ($participant_data['abstract']['feedbacks'] as $feedback) {
                                        if ((isset($feedback['version_id']) && $feedback['version_id'] == $version['id']) ||
                                            (!isset($feedback['version_id']) && isset($feedback['version_number']) && $feedback['version_number'] == $version['version_number'])) {
                                            $hasVersionFeedback = true;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <div class="accordion-item" data-version-id="<?= $version['id'] ?>">
                                    <h2 class="accordion-header" id="heading<?= $version['id'] ?>">                                        <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse<?= $version['id'] ?>"
                                            aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $version['id'] ?>">                                            <div class="d-flex align-items-center w-100">
                                                <span class="badge <?= $index === 0 ? 'bg-success' : 'bg-info' ?> me-2">
                                                    v<?= $version['version_number'] ?>
                                                    <?php if ($index === 0): ?>
                                                        (Latest)
                                                    <?php elseif ($index === count($modalVersions) - 1): ?>
                                                        (Original)
                                                    <?php endif; ?>
                                                </span>
                                                <span class="fw-semibold me-auto"><?= esc($version['title']) ?></span>                                                <div class="text-end">
                                                    <small class="text-muted d-block"><?= date('M j, Y g:i A', strtotime($version['created_at'])) ?></small>
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <?php if ($index === 0): ?>
                                                            <span class="badge bg-success-subtle text-success">Current Version</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary-subtle text-secondary">
                                                                <?= $index === count($modalVersions) - 1 ? 'Original Version' : 'Previous Version' ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if ($hasVersionFeedback): ?>
                                                            <span class="badge bg-warning-subtle text-warning">
                                                                <i class="bx bx-message-dots me-1"></i>Feedback
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $version['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                        aria-labelledby="heading<?= $version['id'] ?>" data-bs-parent="#versionAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6>Abstract Content:</h6>
                                                    <div class="abstract-content bg-light p-3 rounded mb-3" style="max-height: 200px; overflow-y: auto;">
                                                        <?= !empty($version['content']) ? $version['content'] : '<em class="text-muted">No content available</em>' ?>
                                                    </div>

                                                    <?php if (!empty($version['keywords'])): ?>
                                                        <h6>Keywords:</h6>
                                                        <p class="mb-3"><?= esc($version['keywords']) ?></p>
                                                    <?php endif; ?>                                                    <?php if (!empty($version['refs'])): ?>
                                                        <h6>References:</h6>
                                                        <div class="bg-light p-3 rounded mb-3" style="max-height: 150px; overflow-y: auto;">
                                                            <?= nl2br(esc($version['refs'])) ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php 
                                                    // Check for feedback specific to this version
                                                    $versionFeedbacks = [];
                                                    if (!empty($participant_data['abstract']['feedbacks'])) {
                                                        foreach ($participant_data['abstract']['feedbacks'] as $feedback) {
                                                            if ((isset($feedback['version_id']) && $feedback['version_id'] == $version['id']) ||
                                                                (!isset($feedback['version_id']) && isset($feedback['version_number']) && $feedback['version_number'] == $version['version_number'])) {
                                                                $versionFeedbacks[] = $feedback;
                                                            }
                                                        }
                                                    }
                                                    ?>

                                                    <?php if (!empty($versionFeedbacks)): ?>
                                                        <h6>Reviewer Feedback:</h6>
                                                        <div class="version-feedback-list" style="max-height: 200px; overflow-y: auto;">
                                                            <?php foreach ($versionFeedbacks as $feedback): ?>
                                                                <div class="feedback-item mb-2 p-2 bg-warning-subtle rounded border-start border-3 border-warning">
                                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                                        <small class="fw-semibold text-dark">
                                                                            <i class="bx bx-user-circle me-1"></i>
                                                                            <?= esc($feedback['reviewer_name'] ?? 'Reviewer') ?>
                                                                        </small>
                                                                        <small class="text-muted">
                                                                            <?= date('M j, Y', strtotime($feedback['created_at'])) ?>
                                                                        </small>
                                                                    </div>
                                                                    <p class="mb-0 small text-dark"><?= nl2br(esc($feedback['feedback'])) ?></p>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-muted small">
                                                            <i class="bx bx-info-circle me-1"></i>
                                                            No feedback available for this version.
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>Version Details:</h6>
                                                    <ul class="list-unstyled">
                                                        <li><strong>Status:</strong>
                                                            <span class="badge bg-<?= strtolower($version['status']) === 'draft' ? 'secondary' : (strtolower($version['status']) === 'submitted' ? 'primary' : 'success') ?>">
                                                                <?= ucfirst($version['status']) ?>
                                                            </span>
                                                        </li>
                                                        <li><strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($version['created_at'])) ?></li>
                                                        <?php if ($version['updated_at'] !== $version['created_at']): ?>
                                                            <li><strong>Updated:</strong> <?= date('M j, Y g:i A', strtotime($version['updated_at'])) ?></li>
                                                        <?php endif; ?>
                                                        <li><strong>Active:</strong> 
                                                            <?= $version['is_active'] == '1' ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                                                        </li>
                                                    </ul>

                                                    <div class="mt-3">                                                        <?php if ($index < count($modalVersions) - 1 && !empty($modalVersions[$index + 1])): ?>
                                                            <a href="/abstract-paper/compare/<?= $version['id'] ?>/<?= $modalVersions[$index + 1]['id'] ?>"
                                                                class="btn btn-sm btn-outline-primary mb-2 w-100" onclick="showComparisonLoading(event)">
                                                                <i class="bx bx-git-compare me-1"></i> Compare with v<?= $modalVersions[$index + 1]['version_number'] ?>
                                                                <small class="d-block opacity-75">(Previous version)</small>
                                                            </a>
                                                        <?php endif; ?><?php if ($version['id'] !== ($latestVersion['id'] ?? '')): ?>
                                                            <a href="/abstract-paper/view/<?= $version['id'] ?>"
                                                                class="btn btn-sm btn-outline-secondary w-100">
                                                                <i class="bx bx-show me-1"></i> View This Version
                                                            </a>
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
                        <div class="text-center text-muted py-4">
                            <i class="bx bx-history fs-1 mb-3"></i>
                            <h6>No Version History</h6>
                            <p>This abstract has no version history to display.</p>
                        </div>
                    <?php endif; ?>                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div><!-- Include Author Management Modals -->
    <?= $this->include('participant/abstract-paper/components/add-coauthor-modal') ?>

    <!-- Include Author Edit Modals -->
    <?= $this->include('participant/abstract-paper/components/edit-author-modal') ?>    <!-- View Author Modal -->
    <div class="modal fade" id="viewAuthorModal" tabindex="-1" aria-labelledby="viewAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAuthorModalLabel">Author Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>    <!-- Delete Author Confirmation Modal -->
    <div class="modal fade" id="deleteAuthorModal" tabindex="-1" aria-labelledby="deleteAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 450px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAuthorModalLabel">Confirm Author Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong id="deleteAuthorName"></strong> as a co-author?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteAuthor">Remove Author</button>
                </div>
            </div>
        </div>
    </div>    <!-- Version Compare Modal -->
    <div class="modal fade" id="versionCompareModal" tabindex="-1" aria-labelledby="versionCompareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="max-width: 1100px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="versionCompareModalLabel">Version Comparison</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('participant/abstract-paper/components/abstract-view-scripts') ?>
</div> <!-- End abstract-layout -->