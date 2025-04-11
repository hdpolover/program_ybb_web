<div class="tab-pane active" id="personal-details" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Full Name</h6>
                                <p class="text-muted mb-1"><?= strtoupper($participant['full_name']) ?></p>
                                <small class="d-block text-info fst-italic"><i class="mdi mdi-information-outline me-1"></i>This name will appear on all certificates</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Gender</h6>
                                <p class="text-muted"><?= $participant['gender'] ?? '-' ?></p>
                            </div>
                        </div>                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Birthdate</h6>
                                <p class="text-muted"><?= isset($participant['birthdate']) && $participant['birthdate'] ? date('d M, Y', strtotime($participant['birthdate'])) : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Nationality</h6>
                                <p class="text-muted"><?= $participant['nationality'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Origin Address</h6>
                                <p class="text-muted"><?= $participant['origin_address'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Current Address</h6>
                                <p class="text-muted"><?= $participant['current_address'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Phone Number</h6>
                                <p class="text-muted">
                                    <?= isset($participant['country_code']) && isset($participant['phone_number']) 
                                        ? $participant['country_code'] . ' ' . $participant['phone_number'] 
                                        : ($participant['phone_number'] ?? '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Emergency Phone Number</h6>
                                <p class="text-muted">
                                    <?= isset($participant['emergency_country_code']) && isset($participant['emergency_account']) 
                                        ? $participant['emergency_country_code'] . ' ' . $participant['emergency_account'] 
                                        : ($participant['emergency_account'] ?? '-') ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Emergency Contact Relationship</h6>
                                <p class="text-muted"><?= $participant['contact_relation'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">T-Shirt Size</h6>
                                <p class="text-muted"><?= $participant['tshirt_size'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Diseases History</h6>
                                <p class="text-muted"><?= $participant['diseases_history'] ?? '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>