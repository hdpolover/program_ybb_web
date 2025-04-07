<div class="tab-pane fade" id="steparrow-misc" role="tabpanel" aria-labelledby="steparrow-misc-tab">
    <div>
        <div class="mb-3">
            <label class="form-label" for="instagram-account">Instagram Account</label>
            <div class="input-group">
                <span class="input-group-text">https://instagram.com/</span>
                <input type="text" class="form-control" id="instagram-account" placeholder="Enter your username" value="<?= $currentParticipant['instagram_account'] ?? '' ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="knowledge-source">Knowledge Source</label>
            <select class="form-select" id="knowledge-source">
                <option value="" selected disabled>Select knowledge source</option>
                <option value="instagram" <?= (isset($currentParticipant['knowledge_source']) && $currentParticipant['knowledge_source'] == 'instagram') ? 'selected' : '' ?>>Instagram</option>
                <option value="website" <?= (isset($currentParticipant['knowledge_source']) && $currentParticipant['knowledge_source'] == 'website') ? 'selected' : '' ?>>Website</option>
                <option value="friend" <?= (isset($currentParticipant['knowledge_source']) && $currentParticipant['knowledge_source'] == 'friend') ? 'selected' : '' ?>>Friend</option>
                <option value="other" <?= (isset($currentParticipant['knowledge_source']) && $currentParticipant['knowledge_source'] == 'other') ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="source-account-name">Source Account Name</label>
            <input type="text" class="form-control" id="source-account-name" placeholder="Enter source account name" value="<?= $currentParticipant['source_account_name'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="twibbon-link">Twibbon Link</label>
            <div class="input-group">
                <input type="url" class="form-control" id="twibbon-link" placeholder="Enter twibbon link" value="<?= $currentParticipant['twibbon_link'] ?? '' ?>">
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
                            <li>Copy and paste your twibbon post link to this input</li>
                        </ol>
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
            <input type="url" class="form-control" id="requirement-link" placeholder="Enter requirement link" value="<?= $currentParticipant['requirement_link'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label" for="ambassador-code">Ambassador Referral Code <span class="text-muted">(Optional)</span></label>
            <div class="input-group">
                <input type="text" class="form-control" id="ambassador-code" placeholder="Enter ambassador referral code" value="<?= $currentParticipant['ambassador_code'] ?? '' ?>">
                <button class="btn btn-primary" type="button" id="validate-ambassador-code">
                    <i class="ri-check-line me-1"></i>Validate
                </button>
            </div>
            <div id="ambassador-code-feedback" class="form-text mt-1"></div>
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
    document.getElementById('validate-ambassador-code').addEventListener('click', function() {
        const code = document.getElementById('ambassador-code').value.trim();
        const feedbackEl = document.getElementById('ambassador-code-feedback');

        if (code === '') {
            feedbackEl.innerHTML = 'Code is empty. This field is optional.';
            feedbackEl.className = 'form-text mt-1 text-muted';
            return;
        }

        // Show loading state
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Validating...';
        this.disabled = true;

        // AJAX request to check code validity
        fetch('/submission/validateAmbassadorCode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    code: code
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    feedbackEl.innerHTML = 'Valid ambassador code!';
                    feedbackEl.className = 'form-text mt-1 text-success';
                } else {
                    feedbackEl.innerHTML = 'Invalid ambassador code. Please check and try again.';
                    feedbackEl.className = 'form-text mt-1 text-danger';
                }
            })
            .catch(error => {
                feedbackEl.innerHTML = 'Error validating code. Please try again later.';
                feedbackEl.className = 'form-text mt-1 text-danger';
            })
            .finally(() => {
                // Reset button state
                this.innerHTML = '<i class="ri-check-line me-1"></i>Validate';
                this.disabled = false;
            });
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.getElementById('save-misc-btn');

        saveButton.addEventListener('click', function() {
            // Show loading state
            const spinner = this.querySelector('.loading-spinner');
            spinner.classList.remove('d-none');
            this.disabled = true;

            // Collect form data
            const formData = {
                instagram_account: document.getElementById('instagram-account').value,
                knowledge_source: document.getElementById('knowledge-source').value,
                source_account_name: document.getElementById('source-account-name').value,
                twibbon_link: document.getElementById('twibbon-link').value,
                requirement_link: document.getElementById('requirement-link').value,
                ambassador_code: document.getElementById('ambassador-code').value
            };

            // Send API request
            fetch('/submission/updateMisc', {
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