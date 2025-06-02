<!-- Abstract exists - Show the detailed view -->
<?php
// Helper function to check if content is effectively empty (handles Quill's empty states)
function isContentEmpty($content) {
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
    <div class="card-header bg-primary py-3"> <!-- Title with Edit Button at Top Right -->        <div class="d-flex justify-content-between align-items-start mb-3">            <h1 class="card-title mb-0 text-white fw-bold">
                <?= esc($latestVersion['title'] ?? 'Untitled Abstract') ?>
            </h1>
            <a href="<?= base_url('abstract-paper/edit/' . $participant_data['abstract']['id'] . '/' . $latestVersionNumber) ?>" 
               class="btn btn-light btn-sm edit-abstract-btn" 
               data-abstract-id="<?= $participant_data['abstract']['id'] ?>"
               data-version-id="<?= $latestVersionNumber ?>"
               data-ajax="false">
                <i class="bx bx-edit me-1"></i> Edit Abstract
            </a>
        </div>

        <!-- Status and Topic with Dates Below -->
        <div class="row">
            <div class="col-md-8">
                <!-- Status and Topic Badges -->
                <div class="mb-2">
                    <span class="badge <?= isset($participant_data['abstract']['status']) ?
                                            (strtolower($participant_data['abstract']['status']) == 'approved' ? 'bg-success' : (strtolower($participant_data['abstract']['status']) == 'rejected' ? 'bg-danger' : (strtolower($participant_data['abstract']['status']) == 'draft' ? 'bg-secondary' : 'bg-warning')))
                                            : 'bg-secondary' ?> me-2">
                        <i class="bx bx-check-circle me-1"></i> <?= ucfirst(esc($participant_data['abstract']['status'] ?? 'Draft')) ?>
                    </span>
                    <span class="badge bg-info">
                        <i class="bx bx-category me-1"></i> <?= esc($participant_data['abstract']['abstract_topic_id'] ?? 'Uncategorized') ?>
                    </span>
                </div> <!-- Beautified Dates -->
                <div class="text-white-50 d-flex flex-wrap">
                    <div class="me-3 mb-1">
                        <i class="bx bx-calendar-plus me-1"></i> Created:
                        <span class="text-white-75 fst-italic"><?= date('M d, Y h:i A', strtotime($participant_data['abstract']['created_at'] ?? 'now')) ?></span>
                    </div>
                    <div>
                        <i class="bx bx-calendar-check me-1"></i> Updated:
                        <span class="text-white-75 fst-italic"><?= date('M d, Y h:i A', strtotime($latestVersion['updated_at'] ?? ($participant_data['abstract']['updated_at'] ?? 'now'))) ?></span>
                    </div>
                </div>
            </div> <!-- Version Number and History Button at Bottom Right -->
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

<div class="row mb-4">
    <!-- First Row: Abstract Information (8) + Reviewer Feedback (4) -->
    <div class="col-lg-8">
        <!-- Combined Abstract Information Card -->
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-light d-flex align-items-center">
                <div class="flex-grow-1">
                    <h5 class="card-title text-dark mb-0">
                        <i class="bx bx-file-find me-1"></i> Abstract Information
                    </h5>
                </div>
            </div>
            <div class="card-body">
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
                </div>                <!-- Abstract Content -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-file-blank text-primary me-2"></i>Content</h6>
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

                <!-- References Section -->
                <div class="mb-4">
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
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark mb-0">
                    <i class="bx bx-comment-detail me-1"></i> Reviewer Feedback
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($participant_data['abstract']['reviewers'])): ?>
                    <?= $this->include('participant/abstract-paper/components/reviewer-feedback') ?>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-1"></i> No feedback available for this abstract at this time.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Second Row: Authors Information (6) + Paper Upload (6) -->
    <div class="col-lg-6">
        <!-- Authors Information Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title text-dark mb-0">
                    <i class="bx bx-user-circle me-1"></i> Authors Information
                </h5>
                <div>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCoAuthorModal">
                        <i class="bx bx-cog me-1"></i> Manage
                    </button>
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

    <div class="col-lg-6">
        <!-- Paper Upload Section (if approved) -->
        <div class="card border shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark mb-0">
                    <i class="bx bx-file me-1"></i> Paper Information
                </h5>
            </div>
            <div class="card-body">
                <?php if (isset($participant_data['abstract']['status']) && strtolower($participant_data['abstract']['status']) === 'approved'): ?>
                    <?php if (empty($participant_data['abstract']['paper_file'])): ?>
                        <div class="alert alert-info mb-3" role="alert">
                            <i class="bx bx-info-circle me-1"></i> Your abstract has been approved. You can now upload your full paper.
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
                <?php else: ?>
                    <div class="alert alert-warning mb-0" role="alert">
                        <i class="bx bx-info-circle me-1"></i> You will be able to upload your full paper once your abstract is approved.
                    </div>
                <?php endif; ?>
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
            <div class="modal-body"> 
                <?php if (!empty($participant_data['abstract']['versions'])): ?>
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        <span>Only the latest version can be edited. Previous versions are available for viewing and comparison purposes.</span>
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
                                </h2>                                <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $index ?>" data-bs-parent="#versionAccordion">
                                    <div class="accordion-body">
                                        <div class="card border-0">
                                            <!-- Version Status -->
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center">                                                <span class="badge bg-<?= isset($version['status']) && $version['status'] === 'submitted' ? 'success' : 'warning' ?> mb-2">
                                                        <i class="bx <?= isset($version['status']) && $version['status'] === 'submitted' ? 'bx-check-circle' : 'bx-time' ?> me-1"></i>
                                                        <?= isset($version['status']) ? ucfirst($version['status']) : 'Draft' ?>
                                                    </span>
                                                    <div class="btn-group btn-group-sm" role="group">                                                        <?php if ($index === 0): // Only show edit button for the latest version ?>
                                                        <a href="<?= base_url('abstract-paper/edit/' . $participant_data['abstract']['id'] . '/' . $versionNum) ?>" 
                                                           class="btn btn-primary btn-sm view-version-btn"
                                                           data-abstract-id="<?= $participant_data['abstract']['id'] ?>"
                                                           data-version-id="<?= $version['id'] ?>">
                                                            <i class="bx bx-edit me-1"></i> Edit
                                                        </a>
                                                        <?php else: ?>
                                                        <button type="button" class="btn btn-secondary btn-sm view-version-btn"
                                                                data-abstract-id="<?= $participant_data['abstract']['id'] ?>"
                                                                data-version-id="<?= $version['id'] ?>">
                                                            <i class="bx bx-show me-1"></i> View
                                                        </button>
                                                        <?php endif; ?>
                                                        <?php if ($index > 0): // Show compare button for previous versions ?>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm compare-version-btn"
                                                                data-version-id="<?= $version['id'] ?>"
                                                                data-compare-with="<?= $sortedVersions[0]['id'] ?>">
                                                            <i class="bx bx-git-compare me-1"></i> Compare with Latest
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>                                            <!-- Abstract Content -->
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

<!-- Author Management Modal -->
<div class="modal fade" id="addCoAuthorModal" tabindex="-1" aria-labelledby="addCoAuthorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addCoAuthorModalLabel">
                    <i class="bx bx-user-circle me-1"></i> Manage Authors
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Author List Tab -->
                <ul class="nav nav-tabs nav-primary mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#authorList" role="tab">
                            <i class="bx bx-list-ul me-1"></i> Author List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#addAuthor" role="tab">
                            <i class="bx bx-plus-circle me-1"></i> Add Author
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Author List Tab Content -->
                    <div class="tab-pane fade show active" id="authorList" role="tabpanel">
                        <?php if (!empty($participant_data['abstract']['authors'])): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Institution</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participant_data['abstract']['authors'] as $index => $author): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= esc($author['full_name']) ?></td>
                                                <td><?= esc($author['institution'] ?? 'Not specified') ?></td>
                                                <td><?= esc($author['email'] ?? 'No email provided') ?></td>
                                                <td>
                                                    <?php if (isset($author['is_participant']) && $author['is_participant'] == '1'): ?>
                                                        <span class="badge bg-primary">Primary Author</span>
                                                    <?php elseif (isset($author['is_presenting']) && $author['is_presenting'] == '1'): ?>
                                                        <span class="badge bg-info">Presenting Author</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Co-Author</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-info view-author"
                                                            data-author='<?= json_encode($author) ?>'
                                                            data-bs-toggle="tooltip" title="View Details">
                                                            <i class="bx bx-show"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-primary edit-author"
                                                            data-author='<?= json_encode($author) ?>'
                                                            data-bs-toggle="tooltip" title="Edit">
                                                            <i class="bx bx-edit"></i>
                                                        </button>
                                                        <?php if (!(isset($author['is_participant']) && $author['is_participant'] == '1')): ?>
                                                            <button type="button" class="btn btn-danger delete-author"
                                                                data-author-id="<?= $author['id'] ?>"
                                                                data-author-name="<?= esc($author['full_name']) ?>"
                                                                data-bs-toggle="tooltip" title="Delete">
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
                            <div class="alert alert-info mb-0">
                                <i class="bx bx-info-circle me-1"></i> No authors information available. Please add author details.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Add Author Tab Content -->
                    <div class="tab-pane fade" id="addAuthor" role="tabpanel">
                        <form id="addAuthorForm" action="<?= base_url('abstract-paper/add-author') ?>" method="post">
                            <input type="hidden" name="abstract_id" value="<?= $participant_data['abstract']['id'] ?>">

                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="institution" class="form-label">Institution <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="institution" name="institution" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="co_author">Co-Author</option>
                                    <option value="presenting">Presenting Author</option>
                                </select>
                                <small class="text-muted">Primary author cannot be changed.</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-user-plus me-1"></i> Add Author
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

                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_role" name="role">
                            <option value="co_author">Co-Author</option>
                            <option value="presenting">Presenting Author</option>
                        </select>
                        <div id="primaryAuthorMsg" class="text-muted d-none">
                            <small>Primary author role cannot be changed.</small>
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
    });
    
    // Initialize all the functions after ensuring jQuery is available
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
</script>