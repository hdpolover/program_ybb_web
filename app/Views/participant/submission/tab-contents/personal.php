<div class="tab-pane active" id="personal-details" role="tabpanel">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Account ID</h6>
                                <p class="text-muted"><?= isset($account_id) ? $account_id : 'YBB-2025-001' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Full Name</h6>
                                <p class="text-muted"><?= isset($full_name) ? $full_name : 'Anna Adame' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Birthdate</h6>
                                <p class="text-muted"><?= isset($birthdate) ? date('d M, Y', strtotime($birthdate)) : '15 Mar, 1995' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Gender</h6>
                                <p class="text-muted"><?= isset($gender) ? $gender : 'Female' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Phone Number</h6>
                                <p class="text-muted">
                                    <?= isset($country_code) ? '+' . $country_code . ' ' : '+1 ' ?>
                                    <?= isset($phone_number) ? $phone_number : '(123) 456-7890' ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Instagram Account</h6>
                                <p class="text-muted"><?= isset($instagram_account) ? '@' . $instagram_account : '@annaadame' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Nationality</h6>
                                <p class="text-muted"><?= isset($nationality) ? $nationality : 'United States' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Origin Address</h6>
                                <p class="text-muted"><?= isset($origin_address) ? $origin_address : 'California, United States' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-0">
                                <h6 class="form-label fw-semibold">Current Address</h6>
                                <p class="text-muted"><?= isset($current_address) ? $current_address : '2910 Scenic Way, Los Angeles, CA 90012, United States' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Emergency Contact</h6>
                                <p class="text-muted"><?= isset($emergency_account) ? $emergency_account : 'John Adame' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Relationship</h6>
                                <p class="text-muted"><?= isset($contact_relation) ? $contact_relation : 'Father' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">Disease History</h6>
                                <p class="text-muted"><?= isset($disease_history) ? $disease_history : 'None' ?></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <h6 class="form-label fw-semibold">T-Shirt Size</h6>
                                <p class="text-muted"><?= isset($tshirt_size) ? $tshirt_size : 'Medium' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>