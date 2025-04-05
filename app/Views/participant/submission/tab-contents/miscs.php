<div class="tab-pane" id="miscs" role="tabpanel">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Knowledge Source</h6>
                                <p class="text-muted"><?= !empty($currentParticipant['knowledge_source']) ? $currentParticipant['knowledge_source'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Source Account Name</h6>
                                <p class="text-muted"><?= !empty($currentParticipant['source_account_name']) ? $currentParticipant['source_account_name'] : '-' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Twibbon Link</h6>
                                <p class="text-muted"><?= !empty($currentParticipant['twibbon_link']) ? $currentParticipant['twibbon_link'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Requirement Link</h6>
                                <p class="text-muted"><?= !empty($currentParticipant['requirement_link']) ? $currentParticipant['requirement_link'] : '-' ?></p>
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

