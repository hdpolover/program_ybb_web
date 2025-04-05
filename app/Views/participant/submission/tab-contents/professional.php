<div class="tab-pane" id="professional" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="fw-semibold">Education Level</h6>
                                    <div class="text-muted">
                                        <?= !empty($currentParticipant['education_level']) ? $currentParticipant['education_level'] : '-' ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="fw-semibold">Institution</h6>
                                    <div class="text-muted">
                                        <?= $currentParticipant['institution'] ?? '-' ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="fw-semibold">Major</h6>
                                    <div class="text-muted">
                                        <?= !empty($currentParticipant['major']) ? $currentParticipant['major'] : '-'  ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="fw-semibold">Organizations</h6>
                                    <div class="text-muted">
                                        <?= nl2br($currentParticipant['organizations'] ?? '-') ?>
                                    </div>
                                </div>
                            </div>

                     
                    </div>


                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="fw-semibold">Experiences</h6>
                                    <div class="text-muted">
                                        <?= nl2br($currentParticipant['experiences'] ?? '-')  ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <h6 class="fw-semibold">Achievements & Awards</h6>
                                    <div class="text-muted">
                                        <?= nl2br($currentParticipant['achievements'] ?? '-') ?>
                                    </div>
                                </div>
                            </div>

                        <div class="col-lg-6">
                            <div class="mb-0">
                                <h6 class="fw-semibold">Resume</h6>
                                <?php if (isset($currentParticipant['resume_url']) && !empty($currentParticipant['resume_url'])): ?>
                                    <div class="d-flex align-items-center mt-2">
                                        <i class="ri-file-text-line text-primary fs-17 me-2"></i>
                                        <a href="<?= base_url($currentParticipant['resume_url']) ?>" class="text-muted" target="_blank"><?= basename($currentParticipant['resume_url']) ?></a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted">No resume uploaded.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>