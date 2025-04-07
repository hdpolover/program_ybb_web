<div class="tab-pane fade show active" id="steparrow-personal" role="tabpanel" aria-labelledby="steparrow-personal-tab">
    <div>
        <div class="text-center">
            <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                <!-- Use a div with background image to ensure the default image always appears -->
                <div id="profile-image-container"
                    class="rounded-circle avatar-xxl img-thumbnail"
                    style="width: 120px; height: 120px; background-image: url('<?= !empty($currentParticipant['picture_url']) ? $currentParticipant['picture_url'] : 'https://storage.ybbfoundation.com/general-files/default.jpg' ?>'); background-size: cover; background-position: center;">
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
                    <input type="text" class="form-control" id="personal-fullname" placeholder="Enter your full name" value="<?= $currentParticipant['full_name'] ?? '' ?>" required>
                    <div class="invalid-feedback">Please enter your full name</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-birthdate">Birth Date</label>
                    <input type="date" class="form-control" id="personal-birthdate" value="<?= isset($currentParticipant['birthdate']) ? date('Y-m-d', strtotime($currentParticipant['birthdate'])) : '' ?>" required>
                    <div class="invalid-feedback">Please enter your birth date</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-gender">Gender</label>
                    <select class="form-select" id="personal-gender" required>
                        <option value="">Select gender</option>
                        <option value="male" <?= (isset($currentParticipant['gender']) && $currentParticipant['gender'] == 'male') ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= (isset($currentParticipant['gender']) && $currentParticipant['gender'] == 'female') ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= (isset($currentParticipant['gender']) && $currentParticipant['gender'] == 'other') ? 'selected' : '' ?>>Other</option>
                        <option value="prefer-not" <?= (isset($currentParticipant['gender']) && $currentParticipant['gender'] == 'prefer-not') ? 'selected' : '' ?>>Prefer not to say</option>
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
                        <input type="text" class="form-control rounded-end flag-input" readonly placeholder="Select nationality" data-bs-toggle="dropdown" aria-expanded="false" value="<?= $currentParticipant['nationality'] ?? '' ?>" />
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
                <div class="mb-3">
                    <label class="form-label" for="personal-origin-address">Origin Address</label>
                    <textarea class="form-control" id="personal-origin-address" rows="3" placeholder="Enter your origin address" required><?= $currentParticipant['origin_address'] ?? '' ?></textarea>
                    <div class="invalid-feedback">Please enter your origin address</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-current-address">Current Address</label>
                    <textarea class="form-control" id="personal-current-address" rows="3" placeholder="Enter your current address" required><?= $currentParticipant['current_address'] ?? '' ?></textarea>
                    <div class="invalid-feedback">Please enter your current address</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="form-label" for="personal-phone">Personal Phone Number</label>
                    <div class="input-group" data-input-flag>
                        <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="/assets/images/flags/us.svg" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno">+ 1</span></button>
                        <input type="text" class="form-control rounded-end flag-input" id="personal-phone" value="<?= $currentParticipant['phone_number'] ?? '' ?>" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
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
                        <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="/assets/images/flags/us.svg" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno">+ 1</span></button>
                        <input type="text" class="form-control rounded-end flag-input" id="emergency-phone" value="<?= $currentParticipant['emergency_account'] ?? '' ?>" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
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
                        <option value="parent" <?= (isset($currentParticipant['contact_relation']) && $currentParticipant['contact_relation'] == 'parent') ? 'selected' : '' ?>>Parent</option>
                        <option value="spouse" <?= (isset($currentParticipant['contact_relation']) && $currentParticipant['contact_relation'] == 'spouse') ? 'selected' : '' ?>>Spouse</option>
                        <option value="sibling" <?= (isset($currentParticipant['contact_relation']) && $currentParticipant['contact_relation'] == 'sibling') ? 'selected' : '' ?>>Sibling</option>
                        <option value="relative" <?= (isset($currentParticipant['contact_relation']) && $currentParticipant['contact_relation'] == 'relative') ? 'selected' : '' ?>>Other Relative</option>
                        <option value="friend" <?= (isset($currentParticipant['contact_relation']) && $currentParticipant['contact_relation'] == 'friend') ? 'selected' : '' ?>>Friend</option>
                        <option value="guardian" <?= (isset($currentParticipant['contact_relation']) && $currentParticipant['contact_relation'] == 'guardian') ? 'selected' : '' ?>>Legal Guardian</option>
                        <option value="other" <?= (isset($currentParticipant['contact_relation']) && $currentParticipant['contact_relation'] == 'other') ? 'selected' : '' ?>>Other</option>
                    </select>
                    <div class="invalid-feedback">Please select relationship</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-tshirt">T-Shirt Size</label>
                    <select class="form-select" id="personal-tshirt" required>
                        <option value="">Select T-Shirt size</option>
                        <option value="xs" <?= (isset($currentParticipant['tshirt_size']) && $currentParticipant['tshirt_size'] == 'xs') ? 'selected' : '' ?>>XS</option>
                        <option value="s" <?= (isset($currentParticipant['tshirt_size']) && $currentParticipant['tshirt_size'] == 's') ? 'selected' : '' ?>>S</option>
                        <option value="m" <?= (isset($currentParticipant['tshirt_size']) && $currentParticipant['tshirt_size'] == 'm') ? 'selected' : '' ?>>M</option>
                        <option value="l" <?= (isset($currentParticipant['tshirt_size']) && $currentParticipant['tshirt_size'] == 'l') ? 'selected' : '' ?>>L</option>
                        <option value="xl" <?= (isset($currentParticipant['tshirt_size']) && $currentParticipant['tshirt_size'] == 'xl') ? 'selected' : '' ?>>XL</option>
                        <option value="xxl" <?= (isset($currentParticipant['tshirt_size']) && $currentParticipant['tshirt_size'] == 'xxl') ? 'selected' : '' ?>>XXL</option>
                        <option value="xxxl" <?= (isset($currentParticipant['tshirt_size']) && $currentParticipant['tshirt_size'] == 'xxxl') ? 'selected' : '' ?>>XXXL</option>
                    </select>
                    <div class="invalid-feedback">Please select your T-Shirt size</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-disease">Disease History</label>
                    <textarea class="form-control" id="personal-disease" rows="3" placeholder="Enter your disease history or write 'None' if not applicable" required><?= $currentParticipant['diseases_history'] ?? '' ?></textarea>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile image handling with a completely different approach
        const profileImgInput = document.getElementById('profile-img-file-input');
        const profileImageContainer = document.getElementById('profile-image-container');
        const defaultImgSrc = 'https://storage.ybbfoundation.com/general-files/default.jpg';

        // Create a reset button
        const resetButton = document.createElement('button');
        resetButton.type = 'button';
        resetButton.className = 'btn btn-sm btn-outline-danger position-absolute bottom-0 start-0 m-1';
        resetButton.innerHTML = '<i class="ri-delete-bin-line"></i>';
        resetButton.title = 'Remove image';
        resetButton.style.display = 'none'; // Hidden by default

        // Add the reset button to the profile user container
        document.querySelector('.profile-user').appendChild(resetButton);

        // Handle image selection
        profileImgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate the file is an image
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    return;
                }

                // Size validation - limit to 5MB
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Set background image
                    profileImageContainer.style.backgroundImage = `url('${e.target.result}')`;

                    // Store in a form data field or upload immediately
                    const imgData = document.createElement('input');
                    imgData.type = 'hidden';
                    imgData.name = 'profile_image_data';
                    imgData.value = e.target.result;

                    // Remove any existing image data input
                    const existingData = document.querySelector('input[name="profile_image_data"]');
                    if (existingData) {
                        existingData.parentNode.removeChild(existingData);
                    }

                    // Add the new image data input
                    document.querySelector('form').appendChild(imgData);

                    // Show reset button
                    resetButton.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Reset button functionality
        resetButton.addEventListener('click', function() {
            // Reset to default image
            profileImageContainer.style.backgroundImage = `url('${defaultImgSrc}')`;

            // Clear file input
            profileImgInput.value = '';

            // Remove the stored image data
            const existingData = document.querySelector('input[name="profile_image_data"]');
            if (existingData) {
                existingData.parentNode.removeChild(existingData);
            }

            // Hide reset button
            resetButton.style.display = 'none';
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.getElementById('save-personal-btn');

        saveButton.addEventListener('click', function() {
            // Show loading state
            const spinner = this.querySelector('.loading-spinner');
            spinner.classList.remove('d-none');
            this.disabled = true;

            // Collect form data
            const formData = {
                participant: {
                    full_name: document.getElementById('personal-fullname').value,
                    birthdate: document.getElementById('personal-birthdate').value,
                    gender: document.getElementById('personal-gender').value,
                    nationality: window.YBBFlagInput.getNationality(),
                    origin_address: document.getElementById('personal-origin-address').value,
                    current_address: document.getElementById('personal-current-address').value,
                    country_code: getPhoneInputData('personal-phone').code,
                    phone_number: getPhoneInputData('personal-phone').number,
                    emergency_country_code: getPhoneInputData('emergency-phone').code,
                    emergency_account: getPhoneInputData('emergency-phone').number,
                    contact_relation: document.getElementById('emergency-relationship').value,
                    tshirt_size: document.getElementById('personal-tshirt').value,
                    disease_history: document.getElementById('personal-disease').value,
                    // Add profile image data if exists
                    profile_image: document.querySelector('input[name="profile_image_data"]')?.value || null
                }
            };

            // Function to get phone input data by input ID
            function getPhoneInputData(inputId) {
                const inputElement = document.getElementById(inputId);
                if (!inputElement) return { code: '', number: '', full: '' };
                
                const container = inputElement.closest('[data-input-flag]');
                if (!container) return { code: '', number: '', full: '' };
                
                const codeElement = container.querySelector('.country-codeno');
                // Get the full country code including the '+'
                const code = codeElement ? codeElement.textContent.trim() : '';
                const number = inputElement.value.trim();
                
                console.log(`Phone data for ${inputId}:`, { code, number, full: code + number });
                
                return {
                    code: code,
                    number: number,
                    full: code + number
                };
            }

            // Send API request
            // Get participant ID from session
            const participant_id = <?= $currentParticipant['id'] ?>;

            // Send the data to the server
            fetch(`/submission/updatePersonal/${participant_id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include',
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        YBBAlerts.success('Data Saved', 'Your personal information has been saved successfully.', function() {
                            document.getElementById('steparrow-professional-tab').click();
                        });
                    } else {
                        // Show error with SweetAlert
                        const errorMessage = data.message || 'There was a problem saving your personal information.';
                        YBBAlerts.error('Error Saving Data', errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error saving data:', error);
                    YBBAlerts.error('Error Saving Data', 'An unexpected error occurred while saving your data. Please try again later.');
                })
                .finally(() => {
                    // Hide loading state
                    spinner.classList.add('d-none');
                    this.disabled = false;
                });
        });
    });
</script>