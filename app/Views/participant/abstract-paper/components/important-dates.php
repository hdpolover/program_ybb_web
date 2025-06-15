<div class="card border">
    <div class="card-body">
        <h5 class="font-size-16 mb-4">
            <i class="bx bx-info-circle text-success me-1"></i>Important Dates & Templates
        </h5>

        <!-- Dynamic dates from abstract settings -->
        <?php if (isset($abstract_settings) && !empty($abstract_settings)): ?>
            
            <!-- Abstract Submission Deadline -->
            <?php if (!empty($abstract_settings['abstract_submission_deadline'])): ?>
                <div class="mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <i class="mdi mdi-calendar-clock text-danger font-size-18"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="font-size-14 mb-1">Abstract Submission Deadline</h6>
                            <p class="text-muted mb-0 fw-medium"><?= date('M d, Y g:i A', strtotime($abstract_settings['abstract_submission_deadline'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Full Paper Submission Deadline -->
            <?php if (!empty($abstract_settings['full_paper_submission_deadline'])): ?>
                <div class="mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <i class="mdi mdi-file-document text-warning font-size-18"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="font-size-14 mb-1">Full Paper Submission Deadline</h6>
                            <p class="text-muted mb-0 fw-medium"><?= date('M d, Y g:i A', strtotime($abstract_settings['full_paper_submission_deadline'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Template Downloads -->
            <?php if (!empty($abstract_settings['abstract_template_url']) || !empty($abstract_settings['paper_template_url'])): ?>
                <div class="mb-0">
                    <h6 class="font-size-14 mb-3">
                        <i class="mdi mdi-download text-info me-1"></i>Download Templates
                    </h6>
                    <div class="d-grid gap-2">
                        <?php if (!empty($abstract_settings['abstract_template_url'])): ?>
                            <a href="<?= esc($abstract_settings['abstract_template_url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-download me-1"></i>Abstract Template
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($abstract_settings['paper_template_url'])): ?>
                            <a href="<?= esc($abstract_settings['paper_template_url']) ?>" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="bx bx-download me-1"></i>Paper Template
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Fallback static dates when settings are not available -->
            <div class="mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <i class="mdi mdi-calendar-clock text-primary font-size-18"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="font-size-14 mb-1">Abstract Submission Deadline</h6>
                        <p class="text-muted mb-0">Check announcement for deadline</p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <i class="mdi mdi-file-document text-primary font-size-18"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="font-size-14 mb-1">Full Paper Submission</h6>
                        <p class="text-muted mb-0">Check announcement for deadline</p>
                    </div>
                </div>
            </div>

            <div class="mb-0">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                        <i class="mdi mdi-email-outline text-primary font-size-18"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="font-size-14 mb-1">Notification of Acceptance</h6>
                        <p class="text-muted mb-0">Check announcement for dates</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
