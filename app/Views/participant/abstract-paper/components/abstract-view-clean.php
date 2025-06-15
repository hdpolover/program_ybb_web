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
    </div>

    <!-- Second Row: Quick Info (4) + Authors (4) + Paper (4) - Equal Width -->
    <div class="row">
        <!-- Quick Info Card -->
        <div class="col-lg-4">
            <?= $this->include('participant/abstract-paper/components/abstract-quick-info') ?>
        </div>

        <!-- Authors Information Card -->
        <div class="col-lg-4">
            <?= $this->include('participant/abstract-paper/components/abstract-authors-card') ?>
        </div>

        <!-- Paper Upload Section -->
        <div class="col-lg-4">
            <?= $this->include('participant/abstract-paper/components/abstract-paper-card') ?>
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
                    <?php if (!empty($versions)): ?>
                        <div class="accordion" id="versionAccordion">
                            <?php foreach ($versions as $index => $version): ?>
                                <div class="accordion-item" data-version-id="<?= $version['id'] ?>">
                                    <h2 class="accordion-header" id="heading<?= $version['id'] ?>">
                                        <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse<?= $version['id'] ?>" 
                                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $version['id'] ?>">
                                            <div class="d-flex align-items-center w-100">
                                                <span class="badge bg-primary me-2">v<?= $version['version_number'] ?></span>
                                                <span class="fw-semibold me-auto"><?= esc($version['title']) ?></span>
                                                <small class="text-muted"><?= date('M j, Y g:i A', strtotime($version['created_at'])) ?></small>
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
                                                        <?= $version['content'] ?: '<em class="text-muted">No content available</em>' ?>
                                                    </div>
                                                    
                                                    <?php if (!empty($version['keywords'])): ?>
                                                        <h6>Keywords:</h6>
                                                        <p class="mb-3"><?= esc($version['keywords']) ?></p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($version['references'])): ?>
                                                        <h6>References:</h6>
                                                        <div class="bg-light p-3 rounded mb-3" style="max-height: 150px; overflow-y: auto;">
                                                            <?= nl2br(esc($version['references'])) ?>
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
                                                    </ul>
                                                    
                                                    <div class="mt-3">
                                                        <?php if ($index > 0 && !empty($versions[$index - 1])): ?>
                                                            <a href="/participant/abstract-paper/compare/<?= $version['id'] ?>/<?= $versions[$index - 1]['id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary mb-2 w-100" onclick="showComparisonLoading(event)">
                                                                <i class="bx bx-git-compare me-1"></i> Compare with Previous
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($version['id'] !== $latestVersion['id']): ?>
                                                            <a href="/participant/abstract-paper/view/<?= $version['id'] ?>" 
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
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Authors Modal -->
    <div class="modal fade" id="addCoAuthorModal" tabindex="-1" aria-labelledby="addCoAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <?= $this->include('participant/abstract-paper/components/add-coauthor-modal') ?>
        </div>
    </div>

    <!-- Edit Author Modal -->
    <div class="modal fade" id="editAuthorModal" tabindex="-1" aria-labelledby="editAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <?= $this->include('participant/abstract-paper/components/edit-author-modal') ?>
        </div>
    </div>

    <!-- View Author Modal -->
    <div class="modal fade" id="viewAuthorModal" tabindex="-1" aria-labelledby="viewAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
    </div>

    <!-- Delete Author Confirmation Modal -->
    <div class="modal fade" id="deleteAuthorModal" tabindex="-1" aria-labelledby="deleteAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
    </div>

    <!-- Version Compare Modal -->
    <div class="modal fade" id="versionCompareModal" tabindex="-1" aria-labelledby="versionCompareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
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
