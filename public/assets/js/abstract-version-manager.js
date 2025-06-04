/**
 * Abstract Version Management JavaScript
 * 
 * This file contains functions to interact with the abstract version API endpoint
 * for saving draft versions and submitting finalized versions of abstracts.
 */

// Global variable to track the prevent unload function
let preventPageUnload = function(e) {
    e.preventDefault();
    e.returnValue = '';
};

/**
 * Save an abstract version using AJAX
 * 
 * @param {number} abstractId - The ID of the abstract
 * @param {Object} formData - The form data containing title, content, keywords
 * @param {string} status - Either 'draft' or 'submitted'
 * @param {number|null} versionId - Optional ID of an existing version to update
 * @param {Function} onSuccess - Callback function on successful save
 * @param {Function} onError - Callback function on error
 */
function saveAbstractVersion(abstractId, formData, status, versionId = null, onSuccess, onError) {
    // Check if abstractId is empty or null (for new abstracts)
    if (!abstractId || abstractId === '') {
        // For new abstracts, we need to use the traditional form submission to create the abstract first
        console.log('No abstract ID found - this is a new abstract. Using form submission instead.');
        
        // Show error message that this should use form submission
        if (typeof onError === 'function') {
            onError({
                responseText: JSON.stringify({
                    message: 'New abstracts must be created using the form submission. Please save the abstract first to create it.'
                })
            });
        }
        return;
    }    // Prepare the request data
    const requestData = {
        program_id: formData.program_id || $('input[name="program_id"]').val(),
        primary_participant_id: formData.primary_participant_id || $('input[name="primary_participant_id"]').val(),
        abstract_topic_id: formData.abstract_topic_id || $('select[name="abstract_topic_id"]').val(),
        title: formData.title,
        content: formData.content,
        keywords: formData.keywords,
        refs: formData.refs,
        status: status // Must be either 'draft' or 'submitted'
    };

    // Add version_id if editing an existing version
    if (versionId) {
        requestData.version_id = versionId;
    }

    console.log('Request data being sent:', requestData);

    // Show appropriate saving message based on status
    let savingMessage = (status === 'draft') ? 'Saving Draft' : 'Submitting Abstract';
    let savingIcon = (status === 'draft') ? 'bx-save' : 'bx-paper-plane';
    let progressBarClass = (status === 'draft') ? 'bg-primary' : 'bg-success';
    let savingText = (status === 'draft') ? 'Saving your draft abstract...' : 'Submitting your abstract for review...';    // Show a saving dialog with progress indicator and better styling
    const saveAlert = Swal.fire({
        title: savingMessage,
        html: `
            <div class="text-start">
                <p class="mb-2"><i class="bx ${savingIcon} me-2"></i> ${savingText}</p>
                <p class="mb-3"><small class="text-muted">This may take a few moments. Please don't close this window.</small></p>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated ${progressBarClass}" 
                         role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" 
                         style="width: 100%"></div>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="spinner-border spinner-border-sm ${progressBarClass.replace('bg-', 'text-')}" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        `,
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,        allowEscapeKey: false,
        didOpen: () => {
            // Prevent accidental page close
            window.addEventListener('beforeunload', preventPageUnload);
        }
    });
    
    // Set up timeout handling
    const timeoutId = setTimeout(() => {
        // Check if the SweetAlert is still visible
        if (Swal.isVisible()) {
            Swal.update({
                title: 'Still Processing',
                html: `
                    <div class="text-start">
                        <p class="mb-2"><i class="bx bx-time me-2"></i> The server is taking longer than expected to respond.</p>
                        <p class="mb-3">Your request is still being processed. You can:</p>
                        <ul class="text-start mb-3">
                            <li>Continue waiting</li>
                            <li>Check your abstract list in a few minutes to see if it was saved</li>
                            <li>Try again if you don't see your abstract in the list</li>
                        </ul>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" 
                                 role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" 
                                 style="width: 100%"></div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border spinner-border-sm text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'Continue Waiting',
                showCancelButton: true,
                cancelButtonText: 'Close and Check Later',
                confirmButtonColor: '#f7b84b',
                cancelButtonColor: '#fd625e'
            }).then((result) => {
                if (result.isDismissed || result.dismiss === 'cancel') {
                    // User chose to close, cleanup
                    window.removeEventListener('beforeunload', preventPageUnload);
                    if (typeof onError === 'function') {
                        onError('Request timed out - user chose to close');
                    }
                }
            });
        }
    }, 20000); // Show timeout message after 20 seconds
      // Check if jQuery is available
    if (typeof jQuery !== 'undefined' && jQuery.ajax) {
        // Use jQuery AJAX with form data (not JSON)
        jQuery.ajax({
            url: `/api/abstracts/${abstractId}/save-version`,
            type: 'POST',
            data: requestData, // Send as form data, not JSON
            dataType: 'json',
            success: function(response) {
                handleSaveResponse(response, status, onSuccess, onError);
            },
            error: function(xhr, status, error) {
                handleSaveError(xhr, status, error, onError);
            },
            // Set a timeout for the request
            timeout: 30000
        });
    } else {
        // Use vanilla JavaScript fetch with FormData
        const formData = new FormData();
        Object.keys(requestData).forEach(key => {
            if (requestData[key] !== null && requestData[key] !== undefined) {
                formData.append(key, requestData[key]);
            }
        });
        
        fetch(`/api/abstracts/${abstractId}/save-version`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
                // Don't set Content-Type when using FormData, let browser set it
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(response => {
            handleSaveResponse(response, status, onSuccess, onError);
        })
        .catch(error => {
            handleSaveError({ responseText: JSON.stringify({ message: error.message }) }, 'error', error, onError);        });
    }
}

/**
 * Handle the form submission for saving abstract versions
 * 
 * @param {string} formSelector - The jQuery selector for the abstract form
 * @param {string} saveDraftBtnSelector - The jQuery selector for the Save Draft button
 * @param {string} submitBtnSelector - The jQuery selector for the Submit button
 * @param {Function} contentExtractor - Function to extract content from rich text editor
 * @param {Function} validateForm - Function to validate the form (returns boolean)
 * @param {Object} options - Additional options
 */
function setupAbstractVersionHandlers(
    formSelector, 
    saveDraftBtnSelector, 
    submitBtnSelector, 
    contentExtractor, 
    validateForm,
    options = {}
) {
    const $form = $(formSelector);
    const $saveDraftBtn = $(saveDraftBtnSelector);
    const $submitBtn = $(submitBtnSelector);
    
    // Default options
    const defaultOptions = {
        redirectUrl: '/abstract-paper', // URL to redirect to after successful save
        autoRedirect: true, // Whether to automatically redirect after successful save        abstractIdField: 'input[name="abstract_id"]', // Selector for abstract ID field
        versionIdField: 'input[name="version_id"]', // Selector for version ID field
        titleField: 'input[name="title"]', // Selector for title field
        keywordsField: 'input[name="keywords"]', // Selector for keywords field
        refsField: 'textarea[name="refs"]', // Selector for references field
        contentField: 'input[name="content"]' // Selector for content hidden field
    };
    
    // Merge default options with provided options
    const settings = { ...defaultOptions, ...options };

    // Handle Save Draft button click
    $saveDraftBtn.on('click', function(e) {
        e.preventDefault();
        
        // Validate the form for draft (minimal validation)
        if (typeof validateForm === 'function' && !validateForm(false)) {
            Swal.fire({
                title: 'Form Error!',
                text: 'Please select a topic and provide a title for your draft.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#5156be'
            });
            return;
        }
        
        // Ensure content is extracted from the editor and set in the hidden field
        if (typeof contentExtractor === 'function') {
            const content = contentExtractor();
            $(settings.contentField).val(content);
        }
          // Disable buttons to prevent multiple submissions
        $saveDraftBtn.prop('disabled', true);
        $submitBtn.prop('disabled', true);
        
        // Show loading spinner on the save draft button
        $saveDraftBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');
        
        // Get form data
        const formData = {
            program_id: $(settings.abstractIdField).closest('form').find('input[name="program_id"]').val(),
            primary_participant_id: $(settings.abstractIdField).closest('form').find('input[name="primary_participant_id"]').val(),
            abstract_topic_id: $(settings.abstractIdField).closest('form').find('select[name="abstract_topic_id"]').val(),
            title: $(settings.titleField).val(),
            keywords: $(settings.keywordsField).val(),
            refs: $(settings.refsField).val(),
            content: $(settings.contentField).val()
        };
        
        // Get abstract ID and version ID
        const abstractId = $(settings.abstractIdField).val();
        const versionId = $(settings.versionIdField).length ? $(settings.versionIdField).val() : null;
          // Save the abstract version
        saveAbstractVersion(
            abstractId,
            formData,
            'draft',
            versionId,
            function(data) {
                // Success callback
                console.log('Draft saved successfully:', data);
                    
                    // Redirect if auto-redirect is enabled
                    if (settings.autoRedirect) {
                        // Small delay to let user see the success message
                        setTimeout(() => {
                            window.location.href = settings.redirectUrl;
                        }, 1500);
                    } else {
                        // Re-enable buttons and restore text
                        $saveDraftBtn.prop('disabled', false);
                        $submitBtn.prop('disabled', false);
                        $saveDraftBtn.html('<i class="bx bx-save me-1"></i> Save Draft');
                        
                        // Update version ID field if present
                        if ($(settings.versionIdField).length && data.abstract_version && data.abstract_version.id) {
                            $(settings.versionIdField).val(data.abstract_version.id);
                        }
                    }
                },
                function(error) {
                    // Error callback
                    console.error('Error saving draft:', error);
                    
                    // Re-enable buttons and restore text
                    $saveDraftBtn.prop('disabled', false);
                    $submitBtn.prop('disabled', false);
                    $saveDraftBtn.html('<i class="bx bx-save me-1"></i> Save Draft');
                }
        );
    });
    
    // Handle Submit button click
    $submitBtn.on('click', function(e) {
        e.preventDefault();
        
        // Validate the form for submission (full validation)
        if (typeof validateForm === 'function' && !validateForm(true)) {
            Swal.fire({
                title: 'Form Error!',
                text: 'Please fill in all required fields correctly.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#5156be'
            });
            return;
        }
        
        // Ensure content is extracted from the editor and set in the hidden field
        if (typeof contentExtractor === 'function') {
            const content = contentExtractor();
            $(settings.contentField).val(content);
        }
        
        // Show confirmation dialog
        Swal.fire({
            title: 'Submit Abstract',
            html: `
                <div class="text-start">
                    <p>Are you sure you want to submit this abstract? This will finalize your submission.</p>
                    <p class="mb-0"><strong>Note:</strong></p>
                    <ul class="text-start mb-0">
                        <li>All required fields must be completed.</li>
                        <li>After submission, your abstract will be sent for review.</li>
                        <li>You can still view your submission and track its status.</li>
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'No, review again',
            confirmButtonColor: '#5156be',
            cancelButtonColor: '#fd625e'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable buttons to prevent multiple submissions
                $submitBtn.prop('disabled', true);                $saveDraftBtn.prop('disabled', true);
                  // Show loading spinner on the submit button
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...');
                
                // Get form data
                const formData = {
                    program_id: $(settings.abstractIdField).closest('form').find('input[name="program_id"]').val(),
                    primary_participant_id: $(settings.abstractIdField).closest('form').find('input[name="primary_participant_id"]').val(),
                    abstract_topic_id: $(settings.abstractIdField).closest('form').find('select[name="abstract_topic_id"]').val(),
                    title: $(settings.titleField).val(),
                    keywords: $(settings.keywordsField).val(),
                    refs: $(settings.refsField).val(),
                    content: $(settings.contentField).val()
                };
                // Get abstract ID and version ID
                const abstractId = $(settings.abstractIdField).val();
                const versionId = $(settings.versionIdField).length ? $(settings.versionIdField).val() : null;
                
                // Save the abstract version as submitted
                saveAbstractVersion(
                    abstractId,
                    formData,
                    'submitted',
                    versionId,                    function(data) {
                        // Success callback
                        console.log('Abstract submitted successfully:', data);
                        
                        // Redirect if auto-redirect is enabled
                        if (settings.autoRedirect) {
                            // Small delay to let user see the success message
                            setTimeout(() => {
                                window.location.href = settings.redirectUrl;
                            }, 1500);
                        } else {
                            // Re-enable buttons and restore text
                            $submitBtn.prop('disabled', false);
                            $saveDraftBtn.prop('disabled', false);
                            $submitBtn.html('<i class="bx bx-check-circle me-1"></i> Submit Abstract');
                            
                            // Update version ID field if present
                            if ($(settings.versionIdField).length && data.abstract_version && data.abstract_version.id) {
                                $(settings.versionIdField).val(data.abstract_version.id);
                            }
                        }
                    },
                    function(error) {
                        // Error callback
                        console.error('Error submitting abstract:', error);
                        
                        // Re-enable buttons and restore text
                        $submitBtn.prop('disabled', false);
                        $saveDraftBtn.prop('disabled', false);
                        $submitBtn.html('<i class="bx bx-check-circle me-1"></i> Submit Abstract');
                    }
                );
            }
        });
    });
}

/**
 * Handle the form submission for new abstracts (traditional form submission)
 * 
 * @param {string} formSelector - The jQuery selector for the abstract form
 * @param {string} saveDraftBtnSelector - The jQuery selector for the Save Draft button
 * @param {string} submitBtnSelector - The jQuery selector for the Submit button
 * @param {Function} contentExtractor - Function to extract content from rich text editor
 * @param {Function} validateForm - Function to validate the form (returns boolean)
 */
function setupNewAbstractHandlers(
    formSelector, 
    saveDraftBtnSelector, 
    submitBtnSelector, 
    contentExtractor, 
    validateForm
) {    
    console.log('setupNewAbstractHandlers called with:', {
        formSelector,
        saveDraftBtnSelector, 
        submitBtnSelector
    });
    
    // Check if jQuery is available
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not available!');
        return;
    }
    
    const $form = $(formSelector);
    const $saveDraftBtn = $(saveDraftBtnSelector);
    const $submitBtn = $(submitBtnSelector);
    
    console.log('Form element found:', $form.length > 0);
    console.log('Save Draft button found:', $saveDraftBtn.length > 0);
    console.log('Submit button found:', $submitBtn.length > 0);
    
    // Check if this is an existing abstract (has abstract_id) or new one
    const abstractId = $form.find('input[name="abstract_id"]').val();
    console.log('Abstract ID:', abstractId);
    
    // For existing abstracts, use AJAX approach for better UX
    if (abstractId && abstractId !== '') {
        console.log('Setting up AJAX handlers for existing abstract');
        setupAbstractVersionHandlers(
            formSelector, 
            saveDraftBtnSelector, 
            submitBtnSelector, 
            contentExtractor, 
            validateForm,
            {
                redirectUrl: '/abstract-paper',
                autoRedirect: true
            }
        );
        return;
    }
      // For new abstracts, use traditional form submission
    console.log('Setting up form submission handlers for new abstract');
    
    // Set the form action for new abstract if not already set
    if (!$form.attr('action') || $form.attr('action') === '') {
        $form.attr('action', '/abstract-paper/save');
    }
    $form.attr('method', 'POST');
    
    console.log('Form action set to:', $form.attr('action'));
      // Handle Save Draft button click
    $saveDraftBtn.on('click', function(e) {
        console.log('Save Draft button clicked - event handler working!');
        e.preventDefault();
        
        // Validate the form for draft (minimal validation)
        if (typeof validateForm === 'function' && !validateForm(false)) {
            Swal.fire({
                title: 'Form Error!',
                text: 'Please select a topic and provide a title for your draft.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#5156be'
            });
            return;
        }
        
        // Ensure content is extracted from the editor and set in the hidden field
        if (typeof contentExtractor === 'function') {
            const content = contentExtractor();
            $('input[name="content"]').val(content);
        }
        
        // Set status to draft
        if ($('input[name="status"]').length === 0) {
            $form.append('<input type="hidden" name="status" value="draft">');
        } else {
            $('input[name="status"]').val('draft');
        }
        
        // Show loading spinner on the save draft button
        $saveDraftBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');
        $saveDraftBtn.prop('disabled', true);
        $submitBtn.prop('disabled', true);
        
        // Submit the form
        $form.submit();
    });
    
    // Handle Submit button click
    $submitBtn.on('click', function(e) {
        e.preventDefault();
        
        // Validate the form for submission (full validation)
        if (typeof validateForm === 'function' && !validateForm(true)) {
            Swal.fire({
                title: 'Form Error!',
                text: 'Please fill in all required fields correctly.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#5156be'
            });
            return;
        }
        
        // Ensure content is extracted from the editor and set in the hidden field
        if (typeof contentExtractor === 'function') {
            const content = contentExtractor();
            $('input[name="content"]').val(content);
        }
        
        // Show confirmation dialog
        Swal.fire({
            title: 'Submit Abstract',
            html: `
                <div class="text-start">
                    <p>Are you sure you want to submit this abstract? This will finalize your submission.</p>
                    <p class="mb-0"><strong>Note:</strong></p>
                    <ul class="text-start mb-0">
                        <li>All required fields must be completed.</li>
                        <li>After submission, your abstract will be sent for review.</li>
                        <li>You can still view your submission and track its status.</li>
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'No, review again',
            confirmButtonColor: '#5156be',
            cancelButtonColor: '#fd625e'
        }).then((result) => {
            if (result.isConfirmed) {
                // Set status to submitted
                if ($('input[name="status"]').length === 0) {
                    $form.append('<input type="hidden" name="status" value="submitted">');
                } else {
                    $('input[name="status"]').val('submitted');
                }
                
                // Show loading spinner on the submit button
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...');
                $submitBtn.prop('disabled', true);
                $saveDraftBtn.prop('disabled', true);
                
                // Submit the form
                $form.submit();
            }
        });
    });
}

// Helper function to handle save response
function handleSaveResponse(response, status, onSuccess, onError) {
    // Close the saving dialog and cleanup
    Swal.close();
    
    // Remove beforeunload listener
    window.removeEventListener('beforeunload', preventPageUnload);

    // Check if the response is valid and has the expected structure
    // The API returns: { "status": "success", "message": "...", "data": { "abstract_version": {...}, "status": "..." } }
    if (response && response.status === 'success' && response.data && response.data.abstract_version) {
        // Create a success message based on the status
        let successTitle = (status === 'draft') ? 'Draft Saved Successfully' : 'Abstract Submitted Successfully';
        let successIcon = 'success';
        let successButtonText = 'OK';
        
        // Format the date properly
        const formatDate = (dateString) => {
            try {
                const date = new Date(dateString);
                return date.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return dateString; // Return original if formatting fails
            }
        };
        
        // Different messages for draft vs submission
        let successMessage;
        if (status === 'draft') {
            successMessage = `
                <div class="text-start">
                    <p class="mb-3">${response.message || 'Your draft has been saved successfully. You can continue editing it later.'}</p>
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2">Draft Details</h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Title:</strong> ${response.data.abstract_version.title || 'Untitled'}</li>
                                <li><strong>Version:</strong> ${response.data.abstract_version.version_number || '1'}</li>
                                <li><strong>Status:</strong> <span class="badge bg-warning">Draft</span></li>
                                <li><strong>Last Updated:</strong> ${formatDate(response.data.abstract_version.updated_at)}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
        } else {
            successMessage = `
                <div class="text-start">
                    <p class="mb-3">${response.message || 'Your abstract has been submitted successfully and is now pending review.'}</p>
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2">Submission Details</h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Title:</strong> ${response.data.abstract_version.title || 'Untitled'}</li>
                                <li><strong>Version:</strong> ${response.data.abstract_version.version_number || '1'}</li>
                                <li><strong>Status:</strong> <span class="badge bg-success">Submitted</span></li>
                                <li><strong>Submission Date:</strong> ${formatDate(response.data.abstract_version.updated_at)}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        <small>You will receive notifications about the review process via email.</small>
                    </div>
                </div>
            `;
        }

        // Show the success message
        Swal.fire({
            title: successTitle,
            html: successMessage,
            icon: successIcon,
            confirmButtonText: successButtonText,
            confirmButtonColor: '#5156be',
            allowOutsideClick: false
        }).then(() => {
            // Call the success callback with the response data
            if (typeof onSuccess === 'function') {
                onSuccess(response.data);
            }
        });
    } else if (response && response.status === 'error') {
        // Handle API error response
        Swal.fire({
            title: 'Save Failed',
            html: `
                <div class="text-start">
                    <p>${response.message || 'An error occurred while saving your abstract.'}</p>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        <small>Please try again or contact support if this problem persists.</small>
                    </div>
                </div>
            `,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#5156be'
        });

        // Call the error callback
        if (typeof onError === 'function') {
            onError(response.message || 'Save failed');
        }
    } else {
        // Handle unexpected response format
        Swal.fire({
            title: 'Unexpected Response',
            html: `
                <div class="text-start">
                    <p>The server response was not in the expected format.</p>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        <small>Please try again or contact support if this problem persists.</small>
                    </div>
                </div>
            `,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#5156be'
        });

        // Call the error callback
        if (typeof onError === 'function') {
            onError('Unexpected response format');
        }
    }
}

// Helper function to handle save errors
function handleSaveError(xhr, status, error, onError) {
    // Close the saving dialog and cleanup
    Swal.close();
    
    // Remove beforeunload listener
    window.removeEventListener('beforeunload', preventPageUnload);

    // Try to parse the error response
    let errorMessage = 'An unknown error occurred while saving your abstract.';
    let errorDetails = '';
    
    try {
        // Try to parse as JSON first
        let errorResponse;
        if (xhr.responseText) {
            errorResponse = JSON.parse(xhr.responseText);
        } else if (typeof xhr === 'string') {
            errorResponse = JSON.parse(xhr);
        }
        
        if (errorResponse) {
            if (errorResponse.message) {
                errorMessage = errorResponse.message;
            }
            // Include additional error details if available
            if (errorResponse.data && errorResponse.data.errors) {
                errorDetails = '<ul class="mt-2 mb-0">';
                for (const key in errorResponse.data.errors) {
                    errorDetails += `<li>${errorResponse.data.errors[key]}</li>`;
                }
                errorDetails += '</ul>';
            } else if (errorResponse.errors) {
                errorDetails = '<ul class="mt-2 mb-0">';
                for (const key in errorResponse.errors) {
                    if (Array.isArray(errorResponse.errors[key])) {
                        errorResponse.errors[key].forEach(err => {
                            errorDetails += `<li>${err}</li>`;
                        });
                    } else {
                        errorDetails += `<li>${errorResponse.errors[key]}</li>`;
                    }
                }
                errorDetails += '</ul>';
            }
        }
    } catch (e) {
        // If parsing fails, check for common HTTP status codes
        console.error('Error parsing error response:', e);
        
        if (xhr.status) {
            switch (xhr.status) {
                case 422:
                    errorMessage = 'Validation error: Please check your input and try again.';
                    break;
                case 500:
                    errorMessage = 'Server error: Please try again later or contact support.';
                    break;
                case 404:
                    errorMessage = 'The requested resource was not found. Please refresh the page and try again.';
                    break;
                case 403:
                    errorMessage = 'You do not have permission to perform this action.';
                    break;
                default:
                    errorMessage = `Server returned error ${xhr.status}. Please try again later.`;
            }
        } else if (error && error.message) {
            errorMessage = error.message;
        }
    }

    // Show error message with SweetAlert2
    Swal.fire({
        title: 'Error Saving Abstract',
        html: `
            <div class="text-start">
                <p>${errorMessage}</p>
                ${errorDetails}
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bx bx-info-circle me-1"></i>
                    <small>If this problem persists, please contact support with reference to the time of this error.</small>
                </div>
            </div>
        `,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#5156be'
    });

    // Call the error callback
    if (typeof onError === 'function') {
        onError(errorMessage);
    }
}
