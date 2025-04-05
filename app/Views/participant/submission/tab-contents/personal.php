<div class="tab-pane active" id="personal-details" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Full Name</h6>
                                <p class="text-muted"><?= $currentParticipant['full_name'] ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Gender</h6>
                                <p class="text-muted"><?= $currentParticipant['gender'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Birthdate</h6>
                                <p class="text-muted"><?= date('d M, Y', strtotime($currentParticipant['birthdate'])) ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Nationality</h6>
                                <p class="text-muted"><?= $currentParticipant['nationality'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Origin Address</h6>
                                <p class="text-muted"><?= $currentParticipant['origin_address'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Current Address</h6>
                                <p class="text-muted"><?= $currentParticipant['current_address'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Phone Number</h6>
                                <p class="text-muted"><?= $currentParticipant['phone_number'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Emergency Phone Number</h6>
                                <p class="text-muted"><?= $currentParticipant['emergency_account'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Emergency Contact Relationship</h6>
                                <p class="text-muted"><?= $currentParticipant['contact_relation'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">T-Shirt Size</h6>
                                <p class="text-muted"><?= $currentParticipant['tshirt_size'] ?? '-' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Diseases History</h6>
                                <p class="text-muted"><?= $currentParticipant['diseases_history'] ?? '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>