<!-- Abstract exists - Show the detailed view -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary py-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="card-title mb-0 text-white"><?= esc($abstractData['title']) ?></h4>
                <div class="mt-2">
                    <?php if (isset($abstractData['is_draft']) && $abstractData['is_draft']): ?>
                        <span class="badge bg-secondary me-2">
                            <i class="bx bx-edit-alt me-1"></i> Draft
                        </span>
                    <?php endif; ?>
                    <span class="badge <?= strtolower($abstractData['status']) == 'approved' ? 'bg-success' : (strtolower($abstractData['status']) == 'rejected' ? 'bg-danger' : 'bg-warning') ?> me-2">
                        <i class="bx bx-check-circle me-1"></i> <?= esc($abstractData['status']) ?>
                    </span>
                    <span class="badge bg-info">
                        <i class="bx bx-category me-1"></i> <?= esc($abstractData['category'] ?? 'Uncategorized') ?>
                    </span>
                    <small class="text-white text-opacity-75 ms-2"><i class="bx bx-calendar me-1"></i> Updated: <?= esc($abstractData['lastUpdated']) ?></small>
                </div>
            </div>
            <div>
                <a href="<?= base_url('abstract-paper/edit/' . $abstractData['id']) ?>" class="btn btn-light btn-sm">
                    <i class="bx bx-edit me-1"></i> Edit Abstract
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Combined Abstract Information Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-header bg-light d-flex align-items-center">                <div class="flex-grow-1">
                    <h5 class="card-title text-dark mb-0">
                        <i class="bx bx-file-find me-1"></i> Abstract Information
                    </h5>
                </div>
                <div class="flex-shrink-0">
                    <a href="<?= base_url('abstract-paper/edit/1') ?>" class="btn btn-sm btn-primary">
                        <i class="bx bx-edit me-1"></i> Edit Abstract
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Abstract Content -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-file-blank text-primary me-2"></i>Abstract Content</h6>
                    <div class="bg-light p-3 rounded">
                        <?= nl2br(esc($abstractData['content'])) ?>
                    </div>
                </div>
                
                <!-- Topic Section -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-bookmark text-primary me-2"></i>Topic</h6>
                    <div class="bg-light p-3 rounded">
                        <?= esc($abstractData['topic'] ?? 'Not specified') ?>
                    </div>
                </div>
                
                <!-- Keywords Section -->
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-key text-primary me-2"></i>Keywords</h6>
                    <div class="bg-light p-3 rounded">
                        <?php if (!empty($abstractData['keywords'])): ?>
                            <?php foreach (explode(',', $abstractData['keywords']) as $keyword): ?>
                                <span class="badge bg-soft-primary text-primary me-1 mb-1"><?= trim(esc($keyword)) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">No keywords provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- References Section -->
                <div class="mb-3">
                    <h6 class="fw-semibold mb-2"><i class="bx bx-book-bookmark text-primary me-2"></i>References</h6>
                    <div class="bg-light p-3 rounded">
                        <?= nl2br(esc($abstractData['references'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mt-3 mb-4">
            <?= $this->include('participant/abstract-paper/components/action-buttons') ?>
        </div>
    </div>
    <div class="col-lg-4">
        <!-- Paper Information Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title text-dark mb-0">
                    <i class="bx bx-file me-1"></i> Paper Information
                </h5>
            </div>
            <div class="card-body">
                <?php if (strtolower($abstractData['status']) == 'approved'): ?>
                    <?php if (empty($abstractData['paper_file'])): ?>
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
                                <h6 class="mb-1"><?= esc($abstractData['paper_file']) ?></h6>
                                <small class="text-muted">Uploaded on: <?= esc($abstractData['paper_uploaded_date'] ?? 'N/A') ?></small>
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

        <!-- Authors Card -->
        <div class="card border shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title text-dark mb-0">
                    <i class="bx bx-user-circle me-1"></i> Authors
                </h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCoAuthorModal">
                    <i class="bx bx-cog me-1"></i> Manage Authors
                </button>
            </div>
            <div class="card-body">
                <?= $this->include('participant/abstract-paper/components/authors-list') ?>
            </div>
        </div>

        <!-- Reviewer Feedback Card -->
        <?php if (!empty($abstractData['reviewers'])): ?>
            <div class="card border shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title text-dark mb-0">
                        <i class="bx bx-comment-detail me-1"></i> Reviewer Feedback
                    </h5>
                </div>
                <div class="card-body">
                    <?= $this->include('participant/abstract-paper/components/reviewer-feedback') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Include Paper Upload Modals -->
<?= $this->include('participant/abstract-paper/components/paper-upload-modals') ?>
