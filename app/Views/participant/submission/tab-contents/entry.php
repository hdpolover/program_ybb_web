<div class="tab-pane" id="entry" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Program Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Program ID</h6>
                                <p class="text-muted"><?= isset($program_id) ? $program_id : 'YBB-PROG-2025' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Category</h6>
                                <p class="text-muted">
                                    <?php 
                                    if(isset($category)) {
                                        echo $category;
                                    } else {
                                        echo '<span class="badge bg-success-subtle text-success">Advanced</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Ambassador Referral Code</h6>
                                <p class="text-muted"><?= isset($ref_code_ambassador) ? $ref_code_ambassador : 'YBB-AMB-0023' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Questions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-semibold">How did you learn about our program?</h6>
                        <div class="d-flex mt-2">
                            <div class="flex-shrink-0">
                                <i class="ri-information-line text-primary fs-17 me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-0">
                                    <?php if(isset($knowledge_source) && !empty($knowledge_source)): ?>
                                        <?= $knowledge_source ?>
                                        <?php if(isset($source_account_name) && !empty($source_account_name)): ?>
                                            - <?= $source_account_name ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        Social Media - Instagram
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <h6 class="fw-semibold">Why do you want to join this program?</h6>
                        <div class="d-flex mt-2">
                            <div class="flex-shrink-0">
                                <i class="ri-question-answer-line text-primary fs-17 me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-0">
                                    I am deeply passionate about this field and believe this program offers unique growth opportunities. The program's focus on hands-on experience and mentorship aligns perfectly with my career goals. I hope to gain valuable skills, expand my professional network, and contribute meaningfully to the program's objectives.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>