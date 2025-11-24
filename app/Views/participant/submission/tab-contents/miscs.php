<div class="tab-pane" id="miscs" role="tabpanel">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Knowledge Source</h6>
                                <p class="text-muted"><?= !empty($participant['knowledge_source']) ? $participant['knowledge_source'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Source Account Name</h6>
                                <p class="text-muted"><?= !empty($participant['source_account_name']) ? $participant['source_account_name'] : '-' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Twibbon Link</h6>
                                <p class="text-muted">
                                    <?php if (!empty($participant['twibbon_link'])): ?>
                                        <a href="<?= $participant['twibbon_link'] ?>" target="_blank" rel="noopener noreferrer">
                                            <i class="ri-external-link-line me-1"></i><?= $participant['twibbon_link'] ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Requirement Link</h6>
                                <p class="text-muted">
                                    <?php if (!empty($participant['requirement_link'])): ?>
                                        <a href="<?= $participant['requirement_link'] ?>" target="_blank" rel="noopener noreferrer">
                                            <i class="ri-external-link-line me-1"></i><?= $participant['requirement_link'] ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end card-body-->
            </div><!-- end card -->

        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div>

