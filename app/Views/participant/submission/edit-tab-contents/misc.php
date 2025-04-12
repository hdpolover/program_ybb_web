<div class="tab-pane fade" id="steparrow-misc" role="tabpanel" aria-labelledby="steparrow-misc-tab">
    <div>
        <div class="mb-3">
            <label class="form-label" for="instagram-account">Instagram Account</label>
            <div class="input-group">
                <span class="input-group-text">https://instagram.com/</span>
                <input type="text" class="form-control" id="instagram-account" placeholder="Enter your username" value="<?= $participant['instagram_account'] ?? '' ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="knowledge-source">Knowledge Source</label>
            <select class="form-select" id="knowledge-source">
                <option value="" selected disabled>Select knowledge source</option>
                <option value="instagram" <?= (isset($participant['knowledge_source']) && $participant['knowledge_source'] == 'instagram') ? 'selected' : '' ?>>Instagram</option>
                <option value="website" <?= (isset($participant['knowledge_source']) && $participant['knowledge_source'] == 'website') ? 'selected' : '' ?>>Website</option>
                <option value="friend" <?= (isset($participant['knowledge_source']) && $participant['knowledge_source'] == 'friend') ? 'selected' : '' ?>>Friend</option>
                <option value="other" <?= (isset($participant['knowledge_source']) && $participant['knowledge_source'] == 'other') ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="source-account-name">Source Account Name</label>
            <input type="text" class="form-control" id="source-account-name" placeholder="Enter source account name" value="<?= $participant['source_account_name'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="twibbon-link">Twibbon Link</label>
            <div class="input-group">
                <input type="url" class="form-control" id="twibbon-link" placeholder="Enter twibbon link" value="<?= $participant['twibbon_link'] ?? '' ?>">
                <a href="#" class="btn btn-info" id="twibbon-guide-btn" data-bs-toggle="modal" data-bs-target="#twibbonGuideModal">
                    <i class="ri-information-line me-1"></i>Twibbon Guide
                </a>
            </div>
        </div>

        <!-- Twibbon Guide Modal -->
        <div class="modal fade" id="twibbonGuideModal" tabindex="-1" aria-labelledby="twibbonGuideModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="twibbonGuideModalLabel">Twibbon Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <!-- Add your twibbon guide content here -->
                <p>Follow these steps to use the twibbon:</p>
                <ol>
                    <li>Visit the twibbon link <a href="<?= $currentProgram['twibbon'] ?>" target="_blank">here</a></li>
                    <li>Upload your photo</li>
                    <li>Download the generated image</li>
                    <li>Share to your social media</li>
                    <li>Copy and paste your twibbon post link to the provided input</li>
                </ol>
                
                <div class="mt-4 text-center">
                    <a href="<?= $currentProgram['twibbon_video_url'] ?? '#' ?>" 
                       class="btn btn-primary" 
                       target="_blank">
                    <i class="ri-video-line me-1"></i> Watch Video Tutorial
                    </a>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
            </div>
        </div>
        <!-- End Twibbon Guide Modal -->

        <div class="mb-3">
            <label class="form-label" for="share-desc">Share Description</label>
            <div class="form-text mb-2">
                <?php echo $currentProgram['share_desc']; ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="requirement-link">Requirement Link</label>
            <input type="url" class="form-control" id="requirement-link" placeholder="Enter requirement link" value="<?= $participant['requirement_link'] ?? '' ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="ambassador-code">Ambassador Referral Code <span class="text-muted">(Optional)</span></label>
            <div class="input-group">
                <input type="text" class="form-control" id="ambassador-code" placeholder="Enter ambassador referral code"
                    value="<?= isset($referral['ambassador']['ref_code']) ? $referral['ambassador']['ref_code'] : ($participant['ambassador_code'] ?? '') ?>"
                    <?= isset($referral['ambassador']['ref_code']) ? 'readonly' : '' ?>>
                <button class="btn <?= isset($referral['ambassador']['ref_code']) ? 'btn-secondary' : 'btn-primary' ?>" type="button" id="validate-ambassador-code">
                    <?php if (isset($referral['ambassador']['ref_code'])): ?>
                        <i class="ri-edit-line me-1"></i>Change Code
                    <?php else: ?>
                        <i class="ri-check-line me-1"></i>Validate
                    <?php endif; ?>
                </button>
            </div>
            <?php if (isset($referral['referral_data']['created_at'])): ?>
                <div id="ambassador-code-feedback" class="form-text mt-1 text-success">
                    Valid ambassador code! You were referred on <?= date('F j, Y', strtotime($referral['referral_data']['created_at'])) ?>.
                </div>
            <?php else: ?>
                <div id="ambassador-code-feedback" class="form-text mt-1"></div>
            <?php endif; ?>
        </div>



    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" id="save-misc-btn">
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
<!-- end tab pane -->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        $ambassador_id = null;

        // Define the validation function separately so we can add/remove it as an event listener
        function validateAmbassadorCode() {
            const code = document.getElementById('ambassador-code').value.trim();
            const feedbackEl = document.getElementById('ambassador-code-feedback');
            const validateBtn = document.getElementById('validate-ambassador-code');

            if (code === '') {
                feedbackEl.innerHTML = 'Code is empty. This field is optional.';
                feedbackEl.className = 'form-text mt-1 text-muted';
                return;
            }

            // Show loading state
            validateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Validating...';
            validateBtn.disabled = true;

            // get program id
            const program_id = <?= $currentProgram['id'] ?>;

            // AJAX request to check code validity
            fetch('/submission/validateAmbassadorCode', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        code: code,
                        program_id: program_id
                    })
                })
                .then(response => response.json()).then(data => {
                    if (data.is_valid) {
                        // get ambassador id
                        $ambassador_id = data.ambassador.id;

                        feedbackEl.innerHTML = 'Valid ambassador code!';
                        feedbackEl.className = 'form-text mt-1 text-success';

                        // Make the input read-only
                        document.getElementById('ambassador-code').setAttribute('readonly', true);

                        // Change the validate button to "Edit" button
                        validateBtn.innerHTML = '<i class="ri-edit-line me-1"></i>Change Code';
                        validateBtn.classList.remove('btn-primary');
                        validateBtn.classList.add('btn-secondary');

                        // Change the function of the button to enable editing
                        validateBtn.removeEventListener('click', validateAmbassadorCode);
                        validateBtn.addEventListener('click', editAmbassadorCode);

                        // Important: Don't reset button in the finally block for valid codes
                        validateBtn.disabled = false;
                    } else {
                        feedbackEl.innerHTML = 'Invalid ambassador code. Please check and try again.';
                        feedbackEl.className = 'form-text mt-1 text-danger';

                        // Reset button state for invalid codes
                        validateBtn.innerHTML = '<i class="ri-check-line me-1"></i>Validate';
                        validateBtn.disabled = false;
                    }
                })
                .catch(error => {
                    feedbackEl.innerHTML = 'Error validating code. Please try again later.';
                    feedbackEl.className = 'form-text mt-1 text-danger';

                    // Reset button state on error
                    validateBtn.innerHTML = '<i class="ri-check-line me-1"></i>Validate';
                    validateBtn.disabled = false;
                });
        }

        // Function to enable editing of ambassador code
        function editAmbassadorCode() {
            const codeInput = document.getElementById('ambassador-code');
            const feedbackEl = document.getElementById('ambassador-code-feedback');
            const validateBtn = document.getElementById('validate-ambassador-code');

            // Remove read-only attribute
            codeInput.removeAttribute('readonly');

            // Clear the feedback
            feedbackEl.innerHTML = '';
            feedbackEl.className = 'form-text mt-1';

            // Change button back to "Validate"
            validateBtn.innerHTML = '<i class="ri-check-line me-1"></i>Validate';
            validateBtn.classList.remove('btn-secondary');
            validateBtn.classList.add('btn-primary');

            // Change function back to validate
            validateBtn.removeEventListener('click', editAmbassadorCode);
            validateBtn.addEventListener('click', validateAmbassadorCode);

            // Set focus to the input field
            codeInput.focus();
        }

        // Add initial event listener for validation
        document.getElementById('validate-ambassador-code').addEventListener('click', validateAmbassadorCode);

        const saveButton = document.getElementById('save-misc-btn');

        saveButton.addEventListener('click', function() {
            // Show loading state
            const spinner = this.querySelector('.loading-spinner');
            spinner.classList.remove('d-none');
            this.disabled = true;

            // Collect form data
            const formData = {
                participant: {
                    instagram_account: document.getElementById('instagram-account').value,
                    knowledge_source: document.getElementById('knowledge-source').value,
                    source_account_name: document.getElementById('source-account-name').value,
                    twibbon_link: document.getElementById('twibbon-link').value,
                    requirement_link: document.getElementById('requirement-link').value,
                },
                ambassador_id: $ambassador_id ?? null,
            };

            // Get participant ID from session
            const participant_id = <?= $participant['id'] ?>;

            // Send API request
            fetch(`/submission/miscs/${participant_id}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message with callback to navigate to next tab
                        YBBAlerts.success('Data Saved', 'Your miscellaneous information has been saved successfully.', function() {
                            document.getElementById('steparrow-preview-tab').click();
                        });
                    } else {
                        // Show error with details from the server
                        const errorMessage = data.message || 'There was a problem saving your miscellaneous information.';
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