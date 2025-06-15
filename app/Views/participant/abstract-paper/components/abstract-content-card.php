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

// Note: isContentEmpty() function is defined in abstract-view-helpers.php
?>

<!-- Combined Abstract Information Card -->
<div class="card border shadow-sm">
    <div class="card-header bg-light d-flex align-items-center">
        <i class="bx bx-file-blank text-primary me-2 fs-5"></i>
        <h5 class="mb-0 fw-semibold">Abstract Content</h5>
    </div>

    <div class="card-body">
        <div class="row">
            <!-- Main Abstract Content Column -->
            <div class="col-lg-8">
                <div class="abstract-content-area p-4 rounded mb-4">
                    <?php
                    $abstractContent = $latestVersion['content'] ?? '';

                    if (isContentEmpty($abstractContent)):
                    ?>
                        <div class="text-center py-4">
                            <i class="bx bx-file-blank text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mb-2">No Abstract Content</h6>
                            <p class="text-muted small mb-0">
                                The abstract content has not been added yet. Please edit the abstract to add content.
                            </p>
                        </div>
                    <?php else: ?>
                        <?= $abstractContent ?>
                    <?php endif; ?>
                </div>

                <!-- Keywords Section -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="bx bx-key text-primary me-2"></i>Keywords
                    </h6>
                    <?php if (!empty($latestVersion['keywords'])): ?>
                        <div class="keywords-container">
                            <?php foreach (explode(',', $latestVersion['keywords']) as $keyword): ?>
                                <span class="badge bg-soft-primary text-primary me-2 mb-2 px-3 py-2"><?= trim(esc($keyword)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No keywords provided</p>
                    <?php endif; ?>
                </div>

                <!-- References Section -->
                <div class="mb-0">
                    <h6 class="fw-semibold mb-3">
                        <i class="bx bx-book-content text-primary me-2"></i>References
                    </h6>
                    <?php if (!empty($latestVersion['refs'])): ?>
                        <div class="references-content bg-light p-3 rounded">
                            <div class="text-break" style="white-space: pre-line;"><?= esc($latestVersion['refs']) ?></div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No references provided</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Feedback Column -->
            <div class="col-lg-4">
                <div class="card border-0 bg-light h-100">
                    <div class="card-header bg-transparent border-0 pb-2">
                        <h6 class="mb-0 fw-semibold text-primary">
                            <i class="bx bx-message-dots me-2"></i>Reviewer Feedback
                            <?php
                            $feedbackCount = count($participant_data['abstract']['feedbacks'] ?? []);
                            if ($feedbackCount > 0):
                            ?>
                                <span class="badge bg-soft-warning text-warning feedback-badge ms-2"><?= $feedbackCount ?></span>
                            <?php endif; ?>
                        </h6>
                    </div>

                    <div class="card-body pt-0" style="max-height: 400px; overflow-y: auto;">
                        <?php if (!empty($participant_data['abstract']['feedbacks'])): ?>
                            <div class="feedback-list">
                                <?php
                                // Sort feedback by created_at descending (newest first)
                                $feedbacks = $participant_data['abstract']['feedbacks'];
                                usort($feedbacks, function($a, $b) {
                                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                                });

                                foreach ($feedbacks as $index => $feedback):
                                ?>
                                    <div class="feedback-item mb-3 p-3 bg-white rounded border-start border-3 border-warning shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="reviewer-info min-width-0 flex-grow-1">
                                                <h6 class="mb-1 text-dark text-truncate">
                                                    <i class="bx bx-user-circle me-1 text-primary"></i>
                                                    <?= esc($feedback['reviewer_name']) ?>
                                                </h6>
                                                <small class="text-muted text-truncate d-block">
                                                    <i class="bx bx-envelope me-1"></i>
                                                    <?= esc($feedback['reviewer_email']) ?>
                                                </small>
                                            </div>
                                            <span class="badge bg-secondary flex-shrink-0 ms-2">
                                                v<?= $feedback['version_number'] ?? '1' ?>
                                            </span>
                                        </div>

                                        <div class="feedback-content">
                                            <div class="bg-soft-warning p-2 rounded">
                                                <p class="mb-0 text-dark small"><?= nl2br(esc($feedback['feedback'])) ?></p>
                                            </div>
                                        </div>

                                        <div class="text-end mt-2">
                                            <small class="text-muted">
                                                <i class="bx bx-time me-1"></i>
                                                <?= date('M d, Y h:i A', strtotime($feedback['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bx bx-message text-muted mb-3" style="font-size: 2.5rem;"></i>
                                <h6 class="text-muted mb-2">No Feedback Yet</h6>
                                <p class="text-muted small mb-0">
                                    Reviewer feedback will appear here once your abstract has been reviewed.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
