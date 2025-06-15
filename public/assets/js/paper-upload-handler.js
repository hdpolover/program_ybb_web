/**
 * Paper Upload JavaScript
 * Handles paper upload forms, validation, progress feedback, and SweetAlert integration
 * Author: System
 * Date: 2024
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initializePaperUploadHandlers();
    });

    /**
     * Initialize all paper upload related handlers
     */
    function initializePaperUploadHandlers() {
        // File input validation for all paper upload forms
        setupFileValidation();
        
        // Form submission handlers with loading states
        setupFormSubmissionHandlers();
        
        // Modal event handlers
        setupModalHandlers();
        
        // File drag and drop handlers (if needed)
        setupDragDropHandlers();
        
        console.log('Paper upload handlers initialized');
    }

    /**
     * Setup file validation for all paper file inputs
     */
    function setupFileValidation() {
        const fileInputs = document.querySelectorAll('input[name="paper_file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                validatePaperFile(this);
            });
        });
    }

    /**
     * Validate a paper file input
     * @param {HTMLInputElement} fileInput 
     */
    function validatePaperFile(fileInput) {
        const file = fileInput.files[0];
        
        // Clear previous validation
        clearFileValidation(fileInput);
        
        if (!file) {
            return true; // No file selected is okay for optional validation
        }

        // Check file type
        if (file.type !== 'application/pdf') {
            showFileValidationError(fileInput, 'Only PDF files are allowed.');
            fileInput.value = '';
            return false;
        }

        // Check file size (10MB limit)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            showFileValidationError(fileInput, `File size (${fileSizeMB}MB) exceeds the maximum limit of 10MB.`);
            fileInput.value = '';
            return false;
        }

        // Show success validation
        showFileValidationSuccess(fileInput, `Valid PDF file selected (${(file.size / (1024 * 1024)).toFixed(2)}MB)`);
        return true;
    }

    /**
     * Show file validation error
     * @param {HTMLInputElement} fileInput 
     * @param {string} message 
     */
    function showFileValidationError(fileInput, message) {
        fileInput.classList.remove('is-valid');
        fileInput.classList.add('is-invalid');
        
        const feedback = getOrCreateFeedbackElement(fileInput, 'invalid-feedback');
        feedback.textContent = message;
    }

    /**
     * Show file validation success
     * @param {HTMLInputElement} fileInput 
     * @param {string} message 
     */
    function showFileValidationSuccess(fileInput, message) {
        fileInput.classList.remove('is-invalid');
        fileInput.classList.add('is-valid');
        
        const feedback = getOrCreateFeedbackElement(fileInput, 'valid-feedback');
        feedback.textContent = message;
    }

    /**
     * Clear file validation
     * @param {HTMLInputElement} fileInput 
     */
    function clearFileValidation(fileInput) {
        fileInput.classList.remove('is-valid', 'is-invalid');
        
        const invalidFeedback = fileInput.parentNode.querySelector('.invalid-feedback');
        const validFeedback = fileInput.parentNode.querySelector('.valid-feedback');
        
        if (invalidFeedback) invalidFeedback.remove();
        if (validFeedback) validFeedback.remove();
    }

    /**
     * Get or create feedback element
     * @param {HTMLInputElement} fileInput 
     * @param {string} className 
     * @returns {HTMLElement}
     */
    function getOrCreateFeedbackElement(fileInput, className) {
        let feedback = fileInput.parentNode.querySelector('.' + className);
        
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = className;
            fileInput.parentNode.appendChild(feedback);
        }
        
        return feedback;
    }

    /**
     * Setup form submission handlers
     */
    function setupFormSubmissionHandlers() {
        // Upload paper form
        const uploadForm = document.querySelector('#uploadPaperModal form');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                if (!handlePaperFormSubmission(this, 'Uploading Paper', 'Please wait while we upload your paper...')) {
                    e.preventDefault();
                }
            });
        }

        // Update paper form
        const updateForm = document.querySelector('#updatePaperModal form');
        if (updateForm) {
            updateForm.addEventListener('submit', function(e) {
                if (!handlePaperFormSubmission(this, 'Updating Paper', 'Please wait while we update your paper...')) {
                    e.preventDefault();
                }
            });
        }

        // Replace paper form
        const replaceForm = document.querySelector('#replacePaperModal form');
        if (replaceForm) {
            replaceForm.addEventListener('submit', function(e) {
                if (!handlePaperFormSubmission(this, 'Replacing Paper', 'Please wait while we replace your paper...')) {
                    e.preventDefault();
                }
            });
        }

        // Delete paper form
        const deleteForm = document.querySelector('#deletePaperModal form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                if (!handleDeleteFormSubmission(this)) {
                    e.preventDefault();
                }
            });
        }
    }

    /**
     * Handle paper form submission (upload, update, replace)
     * @param {HTMLFormElement} form 
     * @param {string} title 
     * @param {string} message 
     * @returns {boolean}
     */
    function handlePaperFormSubmission(form, title, message) {
        // Validate form first
        if (!validatePaperForm(form)) {
            return false;
        }

        // Show loading state
        showLoadingState(form, title, message);
        
        return true; // Let the form submit normally
    }

    /**
     * Handle delete form submission
     * @param {HTMLFormElement} form 
     * @returns {boolean}
     */
    function handleDeleteFormSubmission(form) {
        // Show confirmation dialog
        if (window.Swal) {
            Swal.fire({
                title: 'Confirm Paper Deletion',
                text: 'Are you sure you want to delete your paper? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete Paper',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoadingState(form, 'Deleting Paper', 'Please wait while we delete your paper...');
                    form.submit();
                }
            });
            return false; // Prevent default submission, we'll handle it in the callback
        }
        
        // Fallback for browsers without SweetAlert
        return confirm('Are you sure you want to delete your paper? This action cannot be undone.');
    }

    /**
     * Validate paper form
     * @param {HTMLFormElement} form 
     * @returns {boolean}
     */
    function validatePaperForm(form) {
        let isValid = true;
        const formData = new FormData(form);
        
        // Check file input
        const fileInput = form.querySelector('input[name="paper_file"]');
        if (fileInput && fileInput.required) {
            if (!validatePaperFile(fileInput)) {
                isValid = false;
            }
        }

        // Check version input
        const versionInput = form.querySelector('input[name="paper_version"]');
        if (versionInput && versionInput.required) {
            const version = versionInput.value.trim();
            if (!version) {
                showInputValidationError(versionInput, 'Version is required.');
                isValid = false;
            } else if (!/^[\d\w\.-]+$/.test(version)) {
                showInputValidationError(versionInput, 'Version should contain only letters, numbers, dots, and hyphens.');
                isValid = false;
            } else {
                showInputValidationSuccess(versionInput, 'Valid version format.');
            }
        }

        // Show validation error if form is invalid
        if (!isValid && window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Form Validation Error',
                text: 'Please correct the errors and try again.',
                confirmButtonColor: '#5156be'
            });
        }

        return isValid;
    }

    /**
     * Show input validation error
     * @param {HTMLInputElement} input 
     * @param {string} message 
     */
    function showInputValidationError(input, message) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        
        const feedback = getOrCreateFeedbackElement(input, 'invalid-feedback');
        feedback.textContent = message;
    }

    /**
     * Show input validation success
     * @param {HTMLInputElement} input 
     * @param {string} message 
     */
    function showInputValidationSuccess(input, message) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        
        const feedback = getOrCreateFeedbackElement(input, 'valid-feedback');
        feedback.textContent = message;
    }

    /**
     * Show loading state using SweetAlert
     * @param {HTMLFormElement} form 
     * @param {string} title 
     * @param {string} message 
     */
    function showLoadingState(form, title, message) {
        if (!window.Swal) {
            return; // No SweetAlert available
        }

        // Disable form elements
        disableFormElements(form, true);

        Swal.fire({
            title: title,
            html: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Auto-close after timeout (fallback)
        setTimeout(() => {
            if (Swal.isVisible()) {
                Swal.close();
                disableFormElements(form, false);
            }
        }, 30000); // 30 seconds timeout
    }

    /**
     * Disable/enable form elements
     * @param {HTMLFormElement} form 
     * @param {boolean} disable 
     */
    function disableFormElements(form, disable) {
        const elements = form.querySelectorAll('input, button, textarea, select');
        elements.forEach(element => {
            element.disabled = disable;
        });
    }

    /**
     * Setup modal handlers
     */
    function setupModalHandlers() {
        // Reset forms when modals are hidden
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                if (form) {
                    resetPaperForm(form);
                }
            });
        });

        // Handle modal show events
        const uploadModal = document.getElementById('uploadPaperModal');
        if (uploadModal) {
            uploadModal.addEventListener('shown.bs.modal', function() {
                const fileInput = this.querySelector('input[name="paper_file"]');
                if (fileInput) {
                    fileInput.focus();
                }
            });
        }
    }

    /**
     * Reset paper form
     * @param {HTMLFormElement} form 
     */
    function resetPaperForm(form) {
        // Reset form
        form.reset();
        
        // Clear validation states
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });

        // Remove feedback elements
        const feedbacks = form.querySelectorAll('.valid-feedback, .invalid-feedback');
        feedbacks.forEach(feedback => feedback.remove());

        // Re-enable form elements
        disableFormElements(form, false);
    }

    /**
     * Setup drag and drop handlers (optional enhancement)
     */
    function setupDragDropHandlers() {
        const fileInputs = document.querySelectorAll('input[name="paper_file"]');
        
        fileInputs.forEach(input => {
            const container = input.closest('.mb-3, .form-group');
            if (!container) return;

            // Add drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                container.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                container.addEventListener(eventName, () => highlight(container), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                container.addEventListener(eventName, () => unhighlight(container), false);
            });

            container.addEventListener('drop', (e) => handleDrop(e, input), false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(container) {
            container.classList.add('drag-over');
        }

        function unhighlight(container) {
            container.classList.remove('drag-over');
        }

        function handleDrop(e, input) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                input.files = files;
                validatePaperFile(input);
            }
        }
    }

    /**
     * Handle server response feedback
     * This can be called from the parent page after form submission
     */
    window.handlePaperUploadResponse = function(success, message, data) {
        if (window.Swal) {
            Swal.close(); // Close any loading dialogs
            
            if (success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: message || 'Paper processed successfully.',
                    confirmButtonColor: '#5156be'
                }).then(() => {
                    // Reload the page or update the UI
                    if (data && data.reload) {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message || 'An error occurred while processing your paper.',
                    confirmButtonColor: '#5156be'
                });
            }
        }
    };

    /**
     * Utility function to show success message
     */
    window.showPaperUploadSuccess = function(message) {
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                confirmButtonColor: '#5156be'
            });
        }
    };

    /**
     * Utility function to show error message
     */
    window.showPaperUploadError = function(message) {
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#5156be'
            });
        }
    };

})();

/* CSS for drag and drop enhancement */
const dragDropCSS = `
.drag-over {
    border: 2px dashed #5156be !important;
    background-color: rgba(81, 86, 190, 0.1) !important;
    border-radius: 5px;
}

.paper-upload-progress {
    display: none;
    margin-top: 10px;
}

.paper-upload-progress .progress {
    height: 20px;
}

.paper-upload-progress .progress-bar {
    transition: width 0.3s ease;
}

.file-validation-feedback {
    font-size: 0.875rem;
    margin-top: 5px;
}

.paper-upload-state .loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 5px;
}
`;

// Inject CSS
const styleSheet = document.createElement('style');
styleSheet.textContent = dragDropCSS;
document.head.appendChild(styleSheet);
