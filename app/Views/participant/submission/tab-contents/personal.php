<div class="tab-pane active" id="personal-details" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Full Name</h6>
                                <p class="text-muted mb-1"><?= !empty($participant['full_name']) ? strtoupper($participant['full_name']) : '-' ?></p>
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
                                <p class="text-muted"><?= (isset($participant['birthdate']) && !empty($participant['birthdate']) && $participant['birthdate'] !== '0000-00-00') ? date('d M, Y', strtotime($participant['birthdate'])) : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Nationality</h6>
                                <p class="text-muted"><?= (!empty($participant['nationality'])) ? $participant['nationality'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Origin Address</h6>
                                <p class="text-muted"><?= (!empty($participant['origin_address'])) ? $participant['origin_address'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Current Address</h6>
                                <p class="text-muted"><?= (!empty($participant['current_address'])) ? $participant['current_address'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Phone Number</h6>
                                <p class="text-muted">
                                    <?= (isset($participant['country_code']) && !empty($participant['country_code']) && 
                                         isset($participant['phone_number']) && !empty($participant['phone_number'])) 
                                        ? $participant['country_code'] . ' ' . $participant['phone_number'] 
                                        : '-' ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Emergency Phone Number</h6>
                                <p class="text-muted">
                                    <?= (isset($participant['emergency_country_code']) && !empty($participant['emergency_country_code']) && 
                                         isset($participant['emergency_account']) && !empty($participant['emergency_account'])) 
                                        ? $participant['emergency_country_code'] . ' ' . $participant['emergency_account'] 
                                        : '-' ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Emergency Contact Relationship</h6>
                                <p class="text-muted"><?= (!empty($participant['contact_relation'])) ? $participant['contact_relation'] : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">T-Shirt Size</h6>
                                <p class="text-muted"><?= (!empty($participant['tshirt_size'])) ? strtoupper($participant['tshirt_size']) : '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Disease History</h6>
                                <p class="text-muted"><?= (!empty($participant['disease_history'])) ? $participant['disease_history'] : '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>