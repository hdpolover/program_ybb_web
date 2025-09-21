<div class="tab-pane fade" id="steparrow-professional" role="tabpanel" aria-labelledby="steparrow-professional-tab">
    <div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-education">Education Level</label>
                    <select class="form-select" id="professional-education" required>
                        <option value="">Select education level</option>
                        <option value="high-school" <?= (isset($participant['education_level']) && $participant['education_level'] == 'high-school') ? 'selected' : '' ?>>High School</option>
                        <option value="diploma" <?= (isset($participant['education_level']) && $participant['education_level'] == 'diploma') ? 'selected' : '' ?>>Diploma</option>
                        <option value="bachelors" <?= (isset($participant['education_level']) && $participant['education_level'] == 'bachelors') ? 'selected' : '' ?>>Bachelor's Degree</option>
                        <option value="masters" <?= (isset($participant['education_level']) && $participant['education_level'] == 'masters') ? 'selected' : '' ?>>Master's Degree</option>
                        <option value="doctorate" <?= (isset($participant['education_level']) && $participant['education_level'] == 'doctorate') ? 'selected' : '' ?>>Doctorate</option>
                        <option value="other" <?= (isset($participant['education_level']) && $participant['education_level'] == 'other') ? 'selected' : '' ?>>Other</option>
                    </select>
                    <div class="invalid-feedback">Please select your education level</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-institution">Institution</label>
                    <input type="text" class="form-control" id="professional-institution" placeholder="Enter your institution name" value="<?= $participant['institution'] ?? '' ?>" required>
                    <div class="invalid-feedback">Please enter your institution name</div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="professional-major">Major/Field of Study</label>
            <input type="text" class="form-control" id="professional-major" placeholder="Enter your major or field of study" value="<?= $participant['major'] ?? '' ?>" required>
            <div class="invalid-feedback">Please enter your major</div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-occupation">Occupation</label>
                    <input type="text" class="form-control" id="professional-occupation" placeholder="Enter your occupation" value="<?= $participant['occupation'] ?? '' ?>" required>
                    <div class="invalid-feedback">Please enter your occupation</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="professional-organization">Organization</label>
                    <input type="text" class="form-control" id="professional-organization" placeholder="Enter organization name" value="<?= $participant['organizations'] ?? '' ?>" required>
                    <div class="invalid-feedback">Please enter your organization</div>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="professional-experiences">Professional Experiences</label>
            <div id="professional-experiences-editor" class="snow-editor" style="height: 200px;"></div>
            <input type="hidden" id="professional-experiences" name="professional-experiences">
            <div class="form-text">Describe your relevant professional experiences</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="professional-achievements">Achievements</label>
            <div id="professional-achievements-editor" class="snow-editor" style="height: 200px;"></div>
            <input type="hidden" id="professional-achievements" name="professional-achievements">
            <div class="form-text">List your key achievements and recognitions</div>
        </div>
        <div class="mb-3">
            <label for="professional-resume-link" class="form-label">CV/Resume Link (Optional)</label>
            <input type="url" class="form-control" id="professional-resume-link" placeholder="https://example.com/my-resume" value="<?= $participant['resume_url'] ?? '' ?>">
            <div class="form-text">Please enter a public URL to your CV/Resume that is accessible to anyone. Make sure privacy settings allow public access (Google Drive, Dropbox, OneDrive, etc.).</div>
        </div>
    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" id="save-professional-btn">
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
        // Professional Experiences editor
        var experiencesQuill = new Quill('#professional-experiences-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            },
            placeholder: 'Describe your relevant professional experiences...'
        });

        // Set existing content if available
        if (typeof <?= json_encode($participant['experiences'] ?? '') ?> === 'string' && <?= json_encode($participant['experiences'] ?? '') ?>.length > 0) {
            experiencesQuill.root.innerHTML = <?= json_encode($participant['experiences'] ?? '') ?>;
            document.getElementById('professional-experiences').value = experiencesQuill.root.innerHTML;
        }

        // Achievements editor
        var achievementsQuill = new Quill('#professional-achievements-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            },
            placeholder: 'List your key achievements and recognitions...'
        });

        // Set existing content if available
        if (typeof <?= json_encode($participant['achievements'] ?? '') ?> === 'string' && <?= json_encode($participant['achievements'] ?? '') ?>.length > 0) {
            achievementsQuill.root.innerHTML = <?= json_encode($participant['achievements'] ?? '') ?>;
            document.getElementById('professional-achievements').value = achievementsQuill.root.innerHTML;
        }

        // Store content in hidden inputs when form is submitted
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('professional-experiences').value = experiencesQuill.root.innerHTML;
            document.getElementById('professional-achievements').value = achievementsQuill.root.innerHTML;
        });

        // Also update the hidden inputs when moving to next steps
        document.querySelectorAll('.nexttab').forEach(function(button) {
            button.addEventListener('click', function() {
                document.getElementById('professional-experiences').value = experiencesQuill.root.innerHTML;
                document.getElementById('professional-achievements').value = achievementsQuill.root.innerHTML;
            });
        });

        const saveButton = document.getElementById('save-professional-btn');

        saveButton.addEventListener('click', function() {
            // Show loading state
            const spinner = this.querySelector('.loading-spinner');
            spinner.classList.remove('d-none');
            this.disabled = true; // Get content from Quill editors and update hidden inputs
            const experiencesEditor = document.querySelector('#professional-experiences-editor .ql-editor');
            const achievementsEditor = document.querySelector('#professional-achievements-editor .ql-editor');

            // Update hidden inputs with Quill content
            document.getElementById('professional-experiences').value = experiencesEditor.innerHTML;
            document.getElementById('professional-achievements').value = achievementsEditor.innerHTML;

            // Collect form data
            const formData = {
                participant: {
                    education_level: document.getElementById('professional-education').value,
                    institution: document.getElementById('professional-institution').value,
                    major: document.getElementById('professional-major').value,
                    occupation: document.getElementById('professional-occupation').value,
                    organizations: document.getElementById('professional-organization').value,
                    experiences: document.getElementById('professional-experiences').value,
                    achievements: document.getElementById('professional-achievements').value,
                    resume_url: document.getElementById('professional-resume-link').value
                }
            };

            // Get participant ID from session
            const participant_id = <?= isset($participant['id']) ? $participant['id'] : 'null' ?>;

            if (!participant_id) {
                console.error('Participant ID not found');
                return;
            }

            // Send the data to the server
            fetch(`/submission/professional/${participant_id}/update`, {
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
                        YBBAlerts.success('Data Saved', 'Your professional information has been saved successfully.', function() {
                            document.getElementById('steparrow-entry-tab').click();
                        });
                    } else {
                        // Show error with details from the server
                        const errorMessage = data.message || 'There was a problem saving your professional information.';
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