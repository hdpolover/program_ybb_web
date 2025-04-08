<div class="tab-pane fade" id="steparrow-preview" role="tabpanel" aria-labelledby="steparrow-preview-tab">
    <div class="preview-container">
        <!-- Header Section -->
        <div class="text-center mb-4">
            <div class="avatar-md mt-5 mb-4 mx-auto">
                <div class="avatar-title bg-light text-success display-4 rounded-circle">
                    <i class="ri-file-list-3-line"></i>
                </div>
            </div>
            <h4 class="fw-semibold">Application Preview</h4>
            <p class="text-muted">
                <?= isset($currentProgram['confirmation_desc']) ? $currentProgram['confirmation_desc'] : 'Please review your submission details before submitting.' ?>
            </p>
        </div>

          <!-- Application Summary Button -->
        <!-- <div class="text-center mb-4">
            <button type="button" class="btn btn-primary btn-lg" id="view-summary-btn">
                <i class="ri-file-list-3-line me-2"></i> View Application Summary
            </button>
        </div> -->
        
        <!-- Application Summary Modal -->
        <div class="modal fade" id="applicationSummaryModal" tabindex="-1" aria-labelledby="applicationSummaryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="applicationSummaryModalLabel">Application Summary</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Personal Details Section -->
                        <div class="mb-4">
                            <h6 class="fw-semibold d-flex align-items-center border-bottom pb-2 mb-3">
                                <i class="ri-user-line me-2"></i> Personal Details
                            </h6>
                            <div id="personal-details-summary">
                                <!-- Content will be loaded via JavaScript -->
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Professional Details Section -->
                        <div class="mb-4">
                            <h6 class="fw-semibold d-flex align-items-center border-bottom pb-2 mb-3">
                                <i class="ri-briefcase-line me-2"></i> Professional Details
                            </h6>
                            <div id="application-details-summary">
                                <!-- Content will be loaded via JavaScript -->
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="print-summary-btn">
                            <i class="ri-printer-line me-1"></i> Print Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Section -->
        <div class="text-center mb-4">
            <?php if (isset($hasPayments['has_payment']) && $hasPayments['has_payment']): ?>
                <div class="d-grid gap-2 col-lg-6 mx-auto">
                    <button type="submit" class="btn btn-success btn-lg" id="submit-application-btn">
                        <span class="loading-spinner d-none spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Submit Application
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="review-again-btn">
                        <i class="ri-arrow-left-line me-1"></i> Review Again
                    </button>
                </div>
            <?php else: ?>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-warning mb-3">
                            <i class="ri-error-warning-line me-2"></i> Payment Required
                        </h5>
                        <p class="card-text">Payment is required before submission. Please complete your payment to proceed.</p>
                        <a href="<?= base_url('payments') ?>" class="btn btn-primary btn-lg">
                            <i class="ri-secure-payment-line me-1"></i> Go to Payment Page
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- end tab pane -->

<script>    document.addEventListener('DOMContentLoaded', function() {
        const submitBtn = document.querySelector('#submit-application-btn');
        const reviewAgainBtn = document.querySelector('#review-again-btn');
        const viewSummaryBtn = document.querySelector('#view-summary-btn');
        const printSummaryBtn = document.querySelector('#print-summary-btn');
        
        // Initialize modal
        const summaryModal = new bootstrap.Modal(document.getElementById('applicationSummaryModal'), {
            backdrop: 'static'
        });
        
        // View Summary button event
        if (viewSummaryBtn) {
            viewSummaryBtn.addEventListener('click', function() {
                // Load data first
                loadApplicationSummary();
                // Then show modal
                summaryModal.show();
            });
        }
        
        // Print Summary button event
        if (printSummaryBtn) {
            printSummaryBtn.addEventListener('click', function() {
                printApplicationSummary();
            });
        }
        
        // Submit button event
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Show detailed confirmation modal
                YBBAlerts.custom({
                    title: 'Confirm Submission',
                    html: `
                        <div class="text-start">
                            <p>You are about to submit your application. Please confirm that all information is correct.</p>
                            <div class="alert alert-info">
                                <i class="ri-information-line me-2"></i> After submission, you won't be able to make further changes.
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="confirm-checkbox">
                                <label class="form-check-label" for="confirm-checkbox">
                                    I confirm that all information provided is accurate and complete.
                                </label>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Submit Application',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    preConfirm: () => {
                        const checkbox = document.getElementById('confirm-checkbox');
                        if (!checkbox.checked) {
                            Swal.showValidationMessage('Please confirm that your information is accurate');
                            return false;
                        }
                        return true;
                    },
                    callback: function() {
                        // Show loading state
                        const spinner = submitBtn.querySelector('.loading-spinner');
                        spinner.classList.remove('d-none');
                        submitBtn.disabled = true;
                        
                        // Add submission animation
                        const previewContainer = document.querySelector('.preview-container');
                        previewContainer.classList.add('submitting');
                        
                        // Submit the form
                        document.querySelector('form').submit();
                    }
                });
            });
        }
        
        // Review again button event
        if (reviewAgainBtn) {
            reviewAgainBtn.addEventListener('click', function() {
                // Find the first tab and activate it
                const firstTab = document.querySelector('[data-bs-toggle="tab"][href*="steparrow"]:not([href="#steparrow-preview"])');
                if (firstTab) {
                    const tabTrigger = new bootstrap.Tab(firstTab);
                    tabTrigger.show();
                }
            });
        }

        // Load application summary data
        function loadApplicationSummary() {
            try {
                // Get form data from previous tabs
                const formData = collectFormData();
                
                // Display personal details
                displayPersonalDetails(formData);
                
                // Display application details
                displayApplicationDetails(formData);
                
            } catch (error) {
                console.error('Error loading application summary:', error);
                document.getElementById('personal-details-summary').innerHTML = 
                    '<div class="alert alert-danger">Failed to load application details. Please try refreshing the page.</div>';
            }
        }        // Collect form data from previous tabs
        function collectFormData() {
            const formData = {};
            
            // Helper function to get value by ID safely
            function getValueById(id) {
                const el = document.getElementById(id);
                if (!el) return '';
                
                if (el.tagName === 'SELECT' && el.selectedIndex >= 0) {
                    return el.options[el.selectedIndex].text;
                } else {
                    return el.value || '';
                }
            }
            
            // Helper function to log when we find a value
            function addField(key, value) {
                if (value) {
                    formData[key] = value;
                    console.log(`Found ${key}: ${value}`);
                }
            }
            
            console.log('Collecting form data from specific fields...');
            
            // ------ Personal Tab Fields ------
            // Directly target the specific form elements we know exist in personal.php
            addField('full_name', getValueById('personal-fullname'));
            addField('birthdate', getValueById('personal-birthdate'));
            addField('gender', getValueById('personal-gender'));
            
            // Handle nationality (may be custom widget)
            try {
                if (window.YBBFlagInput && typeof window.YBBFlagInput.getNationality === 'function') {
                    addField('nationality', window.YBBFlagInput.getNationality());
                }
            } catch (e) {
                console.warn('Could not get nationality', e);
            }
            
            addField('origin_address', getValueById('personal-origin-address'));
            addField('current_address', getValueById('personal-current-address'));
            addField('phone_number', getValueById('personal-phone'));
            addField('emergency_phone', getValueById('emergency-phone'));
            addField('emergency_relationship', getValueById('emergency-relationship'));
            addField('tshirt_size', getValueById('personal-tshirt'));
            addField('disease_history', getValueById('personal-disease'));
            
            // ------ Professional Tab Fields ------
            addField('education_level', getValueById('professional-education'));
            addField('institution', getValueById('professional-institution'));
            addField('major', getValueById('professional-major'));
            addField('occupation', getValueById('professional-occupation'));
            addField('organization', getValueById('professional-organization'));
            addField('professional_experiences', getValueById('professional-experiences'));
            addField('achievements', getValueById('professional-achievements'));
            addField('resume_url', getValueById('professional-resume-link'));
            
            // ------ Try to get other input elements from the form ------
            console.log('Looking for additional form fields...');
            
            // Get all inputs including by ID
            const form = document.querySelector('form');
            if (form) {
                const allInputs = form.querySelectorAll('input, select, textarea');
                console.log(`Found ${allInputs.length} total form elements`);
                
                allInputs.forEach(input => {
                    // Skip elements we've already captured
                    if (!input.id || formData[input.id]) return;
                    
                    // Get a readable field name
                    let fieldName = input.id
                        .replace('personal-', '')
                        .replace('professional-', '')
                        .replace(/-/g, '_');
                    
                    // Get the value based on input type
                    let value = '';
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        if (input.checked) value = input.value;
                    } else if (input.tagName === 'SELECT') {
                        const selectedOption = input.options[input.selectedIndex];
                        value = selectedOption ? selectedOption.text : '';
                    } else {
                        value = input.value;
                    }
                    
                    // Add if we have a value
                    if (value) {
                        addField(fieldName, value);
                    }
                });
            }
            
            // If we still don't have data, add some debug data for testing
            if (Object.keys(formData).length === 0) {
                console.log('No form data found. Adding sample data for testing...');
                
                // Add some sample personal data
                formData.full_name = 'John Doe (Sample)';
                formData.gender = 'Male';
                formData.birthdate = '1990-01-01';
                formData.nationality = 'United States';
                formData.phone_number = '+1 555-123-4567';
                formData.current_address = '123 Main St, Anytown, USA';
                
                // Add some sample professional data
                formData.education_level = "Bachelor's Degree";
                formData.institution = 'Example University';
                formData.occupation = 'Software Developer';
                formData.organization = 'Tech Company Inc.';
                
                formData._is_sample_data = true;
            }
            
            console.log('Form data collected:', formData);
            return formData;
        }
          // Display personal details in the summary
        function displayPersonalDetails(formData) {
            const container = document.getElementById('personal-details-summary');
            let html = '<div class="row g-3">';
            
            // Known personal field mappings - adjust based on your actual form fields
            const personalFieldMappings = {
                // Common personal info field patterns
                'name': 'Full Name',
                'fullname': 'Full Name',
                'first_name': 'First Name',
                'last_name': 'Last Name',
                'email': 'Email Address',
                'phone': 'Phone Number',
                'mobile': 'Mobile Number',
                'address': 'Address',
                'city': 'City',
                'state': 'State/Province',
                'country': 'Country',
                'postal': 'Postal/Zip Code',
                'zip': 'Zip Code',
                'gender': 'Gender',
                'birthdate': 'Date of Birth',
                'birth_date': 'Date of Birth',
                'dob': 'Date of Birth',
                'id_number': 'ID Number',
                'nationality': 'Nationality',
                'profile': 'Profile'
            };
            
            // Look for fields matching personal info patterns
            let fieldsFound = false;
            
            // First display known personal fields
            Object.keys(formData).forEach(key => {
                // Check if this key matches any of our known personal fields or contains these terms
                const keyLower = key.toLowerCase();
                const matchedKey = Object.keys(personalFieldMappings).find(pattern => 
                    keyLower === pattern || keyLower.includes(pattern)
                );
                
                if (matchedKey && formData[key]) {
                    const label = personalFieldMappings[matchedKey] || key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    html += `
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <small class="text-muted">${label}</small>
                                <span class="fw-semibold">${formData[key] || '-'}</span>
                            </div>
                        </div>
                    `;
                    fieldsFound = true;
                }
            });
            
            html += '</div>';
            
            // If no data found
            if (!fieldsFound) {
                html = `
                    <div class="alert alert-info mb-0">
                        <i class="ri-information-line me-2"></i>
                        <span>No personal details found. Please ensure you have completed the personal information section.</span>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        }
          // Display application details in the summary
        function displayApplicationDetails(formData) {
            const container = document.getElementById('application-details-summary');
            let html = '<div class="row g-3">';
            
            // Define application field patterns and their display labels
            const applicationFieldPatterns = {
                'program': 'Program',
                'category': 'Category',
                'submission_date': 'Submission Date',
                'apply_date': 'Application Date',
                'application_date': 'Application Date',
                'status': 'Status',
                'type': 'Type',
                'role': 'Role',
                'position': 'Position',
                'level': 'Level',
                'title': 'Title',
                'course': 'Course',
                'department': 'Department',
                'division': 'Division',
                'location': 'Location',
                'batch': 'Batch',
                'group': 'Group',
                'id': 'Application ID',
                'reference': 'Reference',
                'ref': 'Reference',
                'requirements': 'Requirements',
                'experience': 'Experience',
                'qualification': 'Qualification',
                'education': 'Education',
                'skills': 'Skills',
                'language': 'Language',
                'preference': 'Preference',
                'comment': 'Comments',
                'note': 'Notes',
                // Add more patterns as needed
            };
            
            // Personal fields to exclude from application details
            const personalFields = ['name', 'email', 'phone', 'address', 'city', 'state', 'country', 'zip', 'postal', 'gender', 'birthdate', 'dob', 'id_number'];
            
            let fieldsFound = false;
            
            // First display fields that match our known application field patterns
            Object.keys(formData).forEach(key => {
                const keyLower = key.toLowerCase();
                
                // Skip if this is a personal field (already displayed in personal section)
                if (personalFields.some(field => keyLower.includes(field))) {
                    return;
                }
                
                // Check if this key matches any of our application field patterns
                const matchedPattern = Object.keys(applicationFieldPatterns).find(pattern => 
                    keyLower === pattern || keyLower.includes(pattern)
                );
                
                if (matchedPattern && formData[key]) {
                    const label = applicationFieldPatterns[matchedPattern] || key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    html += `
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <small class="text-muted">${label}</small>
                                <span class="fw-semibold">${formData[key] || '-'}</span>
                            </div>
                        </div>
                    `;
                    fieldsFound = true;
                }
            });
            
            // Now display any remaining fields that haven't been shown yet
            // and don't match personal patterns (as a fallback)
            if (!fieldsFound) {
                Object.keys(formData).forEach(key => {
                    const keyLower = key.toLowerCase();
                    
                    // Skip if already displayed in personal section or if it's an internal field
                    if (personalFields.some(field => keyLower.includes(field)) || key.startsWith('_')) {
                        return;
                    }
                    
                    // Skip if it's a meta field or common form field we don't need to display
                    if (['csrf', 'token', 'submit', 'action', 'method'].some(pattern => keyLower.includes(pattern))) {
                        return;
                    }
                    
                    // Generate a label from the field name
                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                    
                    html += `
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <small class="text-muted">${label}</small>
                                <span class="fw-semibold">${formData[key] || '-'}</span>
                            </div>
                        </div>
                    `;
                    fieldsFound = true;
                });
            }
            
            html += '</div>';
            
            // If no data found
            if (!fieldsFound) {
                html = `
                    <div class="alert alert-info mb-0">
                        <i class="ri-information-line me-2"></i>
                        <span>No application details found. Please ensure you have completed all required sections.</span>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        }
        
        // Add event handler for tab show to refresh preview
        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                if (event.target.getAttribute('href') === '#steparrow-preview') {
                    loadApplicationSummary();
                }
            });
        });
    });
</script>

<style>
    /* Styling for the preview tab */
    #steparrow-preview .preview-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 1rem;
    }
    
    #steparrow-preview .accordion-button:not(.collapsed) {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
    }
    
    #steparrow-preview .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(var(--bs-primary-rgb), 0.5);
    }
    
    /* Animation for submission */
    #steparrow-preview .submitting {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
        100% {
            opacity: 1;
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #steparrow-preview .card-header h5 {
            font-size: 1rem;
        }
        
        #steparrow-preview .accordion-button {
            padding: 0.75rem;
            font-size: 0.9rem;
        }
    }
</style>