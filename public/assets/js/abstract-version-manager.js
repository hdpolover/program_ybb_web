/**
 * Abstract Version Management JavaScript
 * 
 * This file contains functions to interact with the abstract version API endpoint
 * for saving draft versions and submitting finalized versions of abstracts.
 */

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
    }

    // Prepare the request data
    const requestData = {
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

    // Show appropriate saving message based on status
    let savingMessage = (status === 'draft') ? 'Saving Draft' : 'Submitting Abstract';
    let savingIcon = (status === 'draft') ? 'bx-save' : 'bx-paper-plane';
    let progressBarClass = (status === 'draft') ? 'bg-primary' : 'bg-success';
    let savingText = (status === 'draft') ? 'Saving your draft abstract...' : 'Submitting your abstract for review...';

    // Show a saving dialog with progress indicator
    const saveAlert = Swal.fire({
        title: savingMessage,
        html: `
            <div class="text-start">
                <p><i class="bx ${savingIcon} me-1"></i> ${savingText}</p>
                <p><small>This may take a few moments. Please don't close this window.</small></p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated ${progressBarClass}" 
                         role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" 
                         style="width: 100%"></div>
                </div>
            </div>
        `,
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });    
    
    // Check if jQuery is available
    if (typeof jQuery !== 'undefined' && jQuery.ajax) {
        // Use jQuery AJAX
        jQuery.ajax({
            url: `/api/abstracts/${abstractId}/save-version`,
            type: 'POST',
            data: JSON.stringify(requestData),
            contentType: 'application/json',
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
        // Use vanilla JavaScript fetch
        fetch(`/api/abstracts/${abstractId}/save-version`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData)
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
            handleSaveError({ responseText: JSON.stringify({ message: error.message }) }, 'error', error, onError);
        });
    }

    // Set a timeout to show a "still processing" message if the server takes too long
    setTimeout(() => {
        // Check if the SweetAlert is still visible
        if (Swal.isVisible()) {
            Swal.update({
                title: 'Still Processing',
                html: `
                    <div class="text-start">
                        <p><i class="bx bx-time me-1"></i> The server is taking longer than expected to respond.</p>
                        <p>Your request is still being processed. You can:</p>
                        <ul>
                            <li>Continue waiting</li>
                            <li>Check your abstract list in a few minutes to see if it was saved</li>
                            <li>Try again if you don't see your abstract in the list</li>
                        </ul>
                    </div>
                `,
                icon: 'warning'
            });
        }
    }, 20000); // Show timeout message after 20 seconds
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
                    window.location.href = settings.redirectUrl;
                } else {
                    // Re-enable buttons and restore text
                    $saveDraftBtn.prop('disabled', false);
                    $submitBtn.prop('disabled', false);
                    $saveDraftBtn.html('<i class="bx bx-save me-1"></i> Save Draft');
                    
                    // Update version ID field if present
                    if ($(settings.versionIdField).length && data.abstract_version.id) {
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
                $submitBtn.prop('disabled', true);
                $saveDraftBtn.prop('disabled', true);
                  // Show loading spinner on the submit button
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...');
                
                // Get form data
                const formData = {
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
                    versionId,
                    function(data) {
                        // Success callback
                        console.log('Abstract submitted successfully:', data);
                        
                        // Redirect if auto-redirect is enabled
                        if (settings.autoRedirect) {
                            window.location.href = settings.redirectUrl;
                        } else {
                            // Re-enable buttons and restore text
                            $submitBtn.prop('disabled', false);
                            $saveDraftBtn.prop('disabled', false);
                            $submitBtn.html('<i class="bx bx-check-circle me-1"></i> Submit Abstract');
                            
                            // Update version ID field if present
                            if ($(settings.versionIdField).length && data.abstract_version.id) {
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
) {    console.log('setupNewAbstractHandlers called');
    const $form = $(formSelector);
    const $saveDraftBtn = $(saveDraftBtnSelector);
    const $submitBtn = $(submitBtnSelector);
    
    console.log('Form element:', $form);
    console.log('Save Draft button:', $saveDraftBtn);
    console.log('Submit button:', $submitBtn);
    
    // Check if this is an existing abstract (has abstract_id) or new one
    const abstractId = $form.find('input[name="abstract_id"]').val();
    console.log('Abstract ID:', abstractId);
    
    // Set the form action based on whether it's new or existing
    if (abstractId && abstractId !== '') {
        // Existing abstract - use update endpoint
        $form.attr('action', '/abstract-paper/update/' + abstractId);
    } else {
        // New abstract - use save endpoint
        $form.attr('action', '/abstract-paper/save');
    }
    $form.attr('method', 'POST');
    
    console.log('Form action set to:', $form.attr('action'));
    
    // Handle Save Draft button click
    $saveDraftBtn.on('click', function(e) {
        console.log('Save Draft button clicked');
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
    // Close the saving dialog
    Swal.close();

    // Check if the response is valid and has the expected structure
    if (response && response.status && response.data && response.data.abstract_version) {
        // Create a success message based on the status
        let successTitle = (status === 'draft') ? 'Draft Saved Successfully' : 'Abstract Submitted Successfully';
        let successIcon = (status === 'draft') ? 'success' : 'success';
        let successButtonText = 'OK';
        
        // Different messages for draft vs submission
        let successMessage;
        if (status === 'draft') {
            successMessage = `
                <div class="text-start">
                    <p>Your draft has been saved successfully. You can continue editing it later.</p>
                    <ul class="mt-2 mb-0">
                        <li><strong>Title:</strong> ${response.data.abstract_version.title}</li>
                        <li><strong>Version:</strong> ${response.data.abstract_version.version_number}</li>
                        <li><strong>Last Updated:</strong> ${response.data.abstract_version.updated_at}</li>
                    </ul>
                </div>
            `;
        } else {
            successMessage = `
                <div class="text-start">
                    <p>Your abstract has been submitted successfully and is now pending review.</p>
                    <ul class="mt-2 mb-0">
                        <li><strong>Title:</strong> ${response.data.abstract_version.title}</li>
                        <li><strong>Version:</strong> ${response.data.abstract_version.version_number}</li>
                        <li><strong>Submission Date:</strong> ${response.data.abstract_version.updated_at}</li>
                    </ul>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        <small>You will receive notifications about the review process.</small>
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
            confirmButtonColor: '#5156be'
        }).then(() => {
            // Call the success callback with the response data
            if (typeof onSuccess === 'function') {
                onSuccess(response.data);
            }
        });
    } else {
        // Handle unexpected response format
        Swal.fire({
            title: 'Something Went Wrong',
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
    // Close the saving dialog
    Swal.close();

    // Try to parse the error response
    let errorMessage = 'An unknown error occurred while saving your abstract.';
    let errorDetails = '';
    
    try {
        const errorResponse = JSON.parse(xhr.responseText);
        if (errorResponse && errorResponse.message) {
            errorMessage = errorResponse.message;
        }
        // Include additional error details if available
        if (errorResponse && errorResponse.data && errorResponse.data.errors) {
            errorDetails = '<ul class="mt-2 mb-0">';
            for (const key in errorResponse.data.errors) {
                errorDetails += `<li>${errorResponse.data.errors[key]}</li>`;
            }
            errorDetails += '</ul>';
        }
    } catch (e) {
        // If parsing fails, use the default error message
        console.error('Error parsing error response:', e);
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
