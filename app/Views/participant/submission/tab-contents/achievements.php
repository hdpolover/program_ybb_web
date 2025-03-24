<div class="tab-pane" id="achievements" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Achievements</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-semibold">Achievements & Awards</h6>
                        <div class="text-muted">
                            <?php if(isset($achievements) && !empty($achievements)): ?>
                                <?= nl2br($achievements) ?>
                            <?php else: ?>
                                <ul class="ps-3 mb-0">
                                    <li>First Place in National Innovation Competition 2023</li>
                                    <li>Dean's List for Academic Excellence 2022-2024</li>
                                    <li>Published research paper in International Journal of Technology</li>
                                    <li>Community Leadership Award 2023</li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if(isset($resume_url) && !empty($resume_url)): ?>
                    <div class="mb-0">
                        <h6 class="fw-semibold">Resume</h6>
                        <div class="d-flex align-items-center mt-2">
                            <i class="ri-file-pdf-line fs-17 text-danger me-2"></i>
                            <div class="flex-grow-1">
                                <a href="<?= $resume_url ?>" target="_blank" class="text-reset">View Resume</a>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="<?= $resume_url ?>" download class="btn btn-sm btn-soft-info"><i class="ri-download-2-line"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="mb-0">
                        <h6 class="fw-semibold">Resume</h6>
                        <div class="d-flex align-items-center mt-2">
                            <i class="ri-file-pdf-line fs-17 text-danger me-2"></i>
                            <div class="flex-grow-1">
                                <span class="text-muted">Anna_Adame_Resume.pdf</span>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" class="btn btn-sm btn-soft-info"><i class="ri-download-2-line"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Education & Work</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Occupation</h6>
                                <p class="text-muted"><?= isset($occupation) ? $occupation : 'Software Developer' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Institution</h6>
                                <p class="text-muted"><?= isset($institution) ? $institution : 'Tech Innovations Inc.' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Major/Field of Study</h6>
                                <p class="text-muted"><?= isset($major) ? $major : 'Computer Science' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Organizations</h6>
                                <p class="text-muted"><?= isset($organizations) ? $organizations : 'IEEE, ACM, Women in Tech' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Experience</h5>
                </div>
                <div class="card-body">
                    <div class="mb-0">
                        <h6 class="fw-semibold">Professional & Volunteer Experience</h6>
                        <div class="text-muted">
                            <?php if(isset($experiences) && !empty($experiences)): ?>
                                <?= nl2br($experiences) ?>
                            <?php else: ?>
                                <ul class="ps-3 mb-0">
                                    <li class="mb-2">
                                        <div class="fw-medium">Senior Software Developer - Tech Innovations Inc.</div>
                                        <div class="text-muted">Jan 2022 - Present</div>
                                        <div class="text-muted">Led development team for enterprise applications and cloud solutions</div>
                                    </li>
                                    <li class="mb-2">
                                        <div class="fw-medium">Full Stack Developer - Digital Solutions Co.</div>
                                        <div class="text-muted">May 2020 - Dec 2021</div>
                                        <div class="text-muted">Developed responsive web applications and handled database management</div>
                                    </li>
                                    <li>
                                        <div class="fw-medium">Volunteer - Tech for Community</div>
                                        <div class="text-muted">2019 - Present</div>
                                        <div class="text-muted">Teaching coding skills to underprivileged students</div>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>