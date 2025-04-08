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
                                <p class="text-muted"><?= !empty($participant['twibbon_link']) ? $participant['twibbon_link'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Requirement Link</h6>
                                <p class="text-muted"><?= !empty($participant['requirement_link']) ? $participant['requirement_link'] : '-' ?></p>
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

