<div class="tab-pane fade show active" id="steparrow-personal" role="tabpanel" aria-labelledby="steparrow-personal-tab">
    <div>
        <div class="text-center">
            <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                <!-- Use a div with background image to ensure the default image always appears -->
                <div id="profile-image-container"
                    class="rounded-circle avatar-xxl img-thumbnail"
                    style="width: 120px; height: 120px; background-image: url('<?= !empty($participant['picture_url']) ? $participant['picture_url'] : 'https://storage.ybbfoundation.com/general-files/default.jpg' ?>'); background-size: cover; background-position: center;">
                </div>
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                    <input id="profile-img-file-input" type="file" class="profile-img-file-input" accept="image/*">
                    <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                        <span class="avatar-title rounded-circle bg-light text-body">
                            <i class="ri-camera-fill"></i>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="form-label" for="personal-fullname">Full Name</label>
                    <input type="text" class="form-control text-uppercase" id="personal-fullname" placeholder="Enter your full name" value="<?= strtoupper($participant['full_name'] ?? '') ?>" required maxlength="25" oninput="this.value = this.value.toUpperCase(); updateCharCount(this, 'fullname-char-count')">
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted text-blue"><i class="ri-information-line me-1 text-blue"></i>Please ensure correct spelling as this will appear on all certificates</small>
                        <small id="fullname-char-count" class="text-muted"><?= strlen($participant['full_name'] ?? '') ?>/25</small>
                    </div>
                    <div class="invalid-feedback">Please enter your full name</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-birthdate">Birth Date</label>
                    <input type="date" class="form-control" id="personal-birthdate" value="<?= isset($participant['birthdate']) ? date('Y-m-d', strtotime($participant['birthdate'])) : '' ?>" required>
                    <div class="invalid-feedback">Please enter your birth date</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-gender">Gender</label>
                    <select class="form-select" id="personal-gender" required>
                        <option value="">Select gender</option>
                        <option value="male" <?= (isset($participant['gender']) && $participant['gender'] == 'male') ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= (isset($participant['gender']) && $participant['gender'] == 'female') ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= (isset($participant['gender']) && $participant['gender'] == 'other') ? 'selected' : '' ?>>Other</option>
                        <option value="prefer-not" <?= (isset($participant['gender']) && $participant['gender'] == 'prefer-not') ? 'selected' : '' ?>>Prefer not to say</option>
                    </select>
                    <div class="invalid-feedback">Please select your gender</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="form-label" for="personal-nationality">Nationality</label>
                    <div data-input-flag data-option-flag-img-name>
                        <input type="text" class="form-control rounded-end flag-input" readonly placeholder="Select nationality" data-bs-toggle="dropdown" aria-expanded="false" value="<?= $participant['nationality'] ?? '' ?>" />
                        <div class="dropdown-menu w-100">
                            <div class="p-2 px-3 pt-1 searchlist-input">
                                <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country..." />
                            </div>
                            <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3"> <label class="form-label" for="personal-origin-address">Origin Address</label>
                    <textarea class="form-control" id="personal-origin-address" rows="3" placeholder="Enter your origin address" required><?php echo htmlspecialchars($participant['origin_address'] ?? ''); ?></textarea>
                    <div class="invalid-feedback">Please enter your origin address</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3"> <label class="form-label" for="personal-current-address">Current Address</label>
                    <textarea class="form-control" id="personal-current-address" rows="3" placeholder="Enter your current address" required><?php echo htmlspecialchars($participant['current_address'] ?? ''); ?></textarea>
                    <div class="invalid-feedback">Please enter your current address</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="form-label" for="personal-phone">Personal Phone Number</label>
                    <div class="input-group" data-input-flag>
                        <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="<?= base_url('/assets/images/flags/' . ($participant['phone_flag'] ?? 'us') . '.svg') ?>" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno">+ <?= $participant['phone_code'] ?? '1' ?></span></button>
                        <input type="text" class="form-control rounded-end flag-input" id="personal-phone" value="<?= $participant['phone_number'] ?? '' ?>" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
                        <div class="dropdown-menu w-100">
                            <div class="p-2 px-3 pt-1 searchlist-input">
                                <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country name or code..." />
                            </div>
                            <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                        </div>
                        <div class="invalid-feedback">Please enter your phone number</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="emergency-phone">Emergency Contact Phone Number</label>
                    <div class="input-group" data-input-flag>
                        <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="<?= base_url('/assets/images/flags/' . ($participant['emergency_phone_flag'] ?? 'us') . '.svg') ?>" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno"><?= $participant['emergency_country_code'] ?? '1' ?></span></button>
                        <input type="text" class="form-control rounded-end flag-input" id="emergency-phone" value="<?= $participant['emergency_account'] ?? '' ?>" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
                        <div class="dropdown-menu w-100">
                            <div class="p-2 px-3 pt-1 searchlist-input">
                                <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country name or code..." />
                            </div>
                            <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                        </div>
                        <div class="invalid-feedback">Please enter emergency contact phone number</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="emergency-relationship">Relationship</label>
                    <select class="form-select" id="emergency-relationship" required>
                        <option value="">Select relationship</option>
                        <option value="parent" <?= (isset($participant['contact_relation']) && $participant['contact_relation'] == 'parent') ? 'selected' : '' ?>>Parent</option>
                        <option value="spouse" <?= (isset($participant['contact_relation']) && $participant['contact_relation'] == 'spouse') ? 'selected' : '' ?>>Spouse</option>
                        <option value="sibling" <?= (isset($participant['contact_relation']) && $participant['contact_relation'] == 'sibling') ? 'selected' : '' ?>>Sibling</option>
                        <option value="relative" <?= (isset($participant['contact_relation']) && $participant['contact_relation'] == 'relative') ? 'selected' : '' ?>>Other Relative</option>
                        <option value="friend" <?= (isset($participant['contact_relation']) && $participant['contact_relation'] == 'friend') ? 'selected' : '' ?>>Friend</option>
                        <option value="guardian" <?= (isset($participant['contact_relation']) && $participant['contact_relation'] == 'guardian') ? 'selected' : '' ?>>Legal Guardian</option>
                        <option value="other" <?= (isset($participant['contact_relation']) && $participant['contact_relation'] == 'other') ? 'selected' : '' ?>>Other</option>
                    </select>
                    <div class="invalid-feedback">Please select relationship</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-tshirt">T-Shirt Size</label>
                    <div class="input-group">
                        <select class="form-select" id="personal-tshirt" required>
                            <option value="">Select T-Shirt size</option>
                            <option value="xs" <?= (isset($participant['tshirt_size']) && $participant['tshirt_size'] == 'xs') ? 'selected' : '' ?>>XS</option>
                            <option value="s" <?= (isset($participant['tshirt_size']) && $participant['tshirt_size'] == 's') ? 'selected' : '' ?>>S</option>
                            <option value="m" <?= (isset($participant['tshirt_size']) && $participant['tshirt_size'] == 'm') ? 'selected' : '' ?>>M</option>
                            <option value="l" <?= (isset($participant['tshirt_size']) && $participant['tshirt_size'] == 'l') ? 'selected' : '' ?>>L</option>
                            <option value="xl" <?= (isset($participant['tshirt_size']) && $participant['tshirt_size'] == 'xl') ? 'selected' : '' ?>>XL</option>
                            <option value="xxl" <?= (isset($participant['tshirt_size']) && $participant['tshirt_size'] == 'xxl') ? 'selected' : '' ?>>XXL</option>
                            <option value="xxxl" <?= (isset($participant['tshirt_size']) && $participant['tshirt_size'] == 'xxxl') ? 'selected' : '' ?>>XXXL</option>
                        </select>
                        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#tshirtSizeChartModal">
                            <i class="ri-ruler-line me-1"></i> Size Guide
                        </button>
                    </div>
                    <small class="text-muted mt-1 d-block"><i class="ri-information-line me-1"></i> Select the appropriate size based on your measurements</small>
                    <div class="invalid-feedback">Please select your T-Shirt size</div>
                </div>

                <!-- T-Shirt Size Chart Modal -->
                <div class="modal fade" id="tshirtSizeChartModal" tabindex="-1" aria-labelledby="tshirtSizeChartModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tshirtSizeChartModalLabel">T-Shirt Size Guide</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="<?= $currentProgram['tshirt_chart_url'] ?>" alt="T-Shirt Size Chart" class="img-fluid">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-disease">Disease History</label>
                    <textarea class="form-control" id="personal-disease" rows="3" placeholder="Enter your disease history or write 'None' if not applicable" required><?php echo htmlspecialchars($participant['disease_history'] ?? ''); ?></textarea>
                    <div class="invalid-feedback">Please provide disease history information</div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" id="save-personal-btn">
            <span class="d-flex align-items-center">
                <span>Save and Continue</span>
                <i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>
                <span class="loading-spinner d-none ms-2">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </span>
            </span>
        </button>
    </div>
</div>

<!-- Hidden fields for data storage -->
<input type="hidden" id="participant-id-holder" value="<?= $participant['id'] ?>">
<input type="hidden" id="saved-nationality" value="<?= $participant['nationality'] ?? '' ?>">
<input type="hidden" id="saved-nationality-code" value="<?= $participant['nationality_code'] ?? '' ?>">
<input type="hidden" id="saved-nationality-flag" value="<?= $participant['nationality_flag'] ?? '' ?>">
<input type="hidden" id="saved-country-code" value="<?= $participant['country_code'] ?? '' ?>">
<input type="hidden" id="saved-phone-flag" value="<?= $participant['phone_flag'] ?? '' ?>">
<input type="hidden" id="saved-emergency-country-code" value="<?= $participant['emergency_country_code'] ?? '' ?>">
<input type="hidden" id="saved-emergency-phone-flag" value="<?= $participant['emergency_phone_flag'] ?? '' ?>">

<!-- Include the consolidated form handler -->
<script>
    function updateCharCount(input, counterId) {
        const maxLength = input.getAttribute('maxlength');
        const currentLength = input.value.length;
        document.getElementById(counterId).textContent = currentLength + '/' + maxLength;
    }
</script>

<script src="<?= base_url('assets/js/custom/personal-form-handler.js') ?>"></script>