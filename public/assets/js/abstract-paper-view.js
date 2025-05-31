/**
 * Abstract Paper View Functionality
 * 
 * This file contains JavaScript for the abstract paper view, including:
 * - Version history interactions
 * - Abstract status updates
 * - AJAX calls to fetch version details
 */

// Check if jQuery is available
const jQueryAvailable = typeof jQuery !== 'undefined';

if (!jQueryAvailable) {
    console.warn('jQuery is not loaded. Some features like version comparison may not work properly.');

    // Dynamically load jQuery if it's not available
    const script = document.createElement('script');
    script.src = '/assets/libs/jquery/jquery.min.js';
    script.onload = function () {
        console.log('jQuery has been dynamically loaded');
    };
    document.head.appendChild(script);
}

// Initialization when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // Setup version history interactions
    setupVersionHistory();

    // Setup edit button behavior
    setupEditButtonBehavior();

    // Ensure the version compare modal exists
    ensureVersionCompareModalExists();
});

/**
 * Setup version history interactions
 */
function setupVersionHistory() {
    // Get the version history modal
    const versionHistoryModal = document.getElementById('versionHistoryModal');

    // If the modal exists, set up event listeners for version switching
    if (versionHistoryModal) {
        // When the modal is shown, load version details if needed
        versionHistoryModal.addEventListener('shown.bs.modal', function () {
            console.log('Version history modal shown');

            // Highlight the latest version
            const firstItem = versionHistoryModal.querySelector('.accordion-item:first-child');
            if (firstItem) {
                firstItem.classList.add('highlight-latest');
            }

            // Add click event listeners to accordion headers
            const accordionButtons = versionHistoryModal.querySelectorAll('.accordion-button');
            accordionButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // This prevents multiple click events due to event delegation
                    if (this.classList.contains('has-click-handler')) {
                        return;
                    }

                    this.classList.add('has-click-handler');

                    // Get the target collapse element
                    const targetId = this.getAttribute('data-bs-target');
                    const targetCollapse = document.querySelector(targetId);

                    // If it's not already open, add a class for animation
                    if (!targetCollapse.classList.contains('show')) {
                        setTimeout(() => {
                            targetCollapse.querySelectorAll('.accordion-body > *').forEach(el => {
                                el.classList.add('highlight-change');
                                setTimeout(() => {
                                    el.classList.remove('highlight-change');
                                }, 2000);
                            });
                        }, 300);
                    }
                });
            });
        });

        // Setup version comparison buttons if they exist
        const compareButtons = versionHistoryModal.querySelectorAll('.compare-version-btn');
        compareButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const versionId = this.getAttribute('data-version-id');
                const compareWithId = this.getAttribute('data-compare-with');

                if (versionId && compareWithId) {
                    compareVersions(versionId, compareWithId);
                }
            });
        });

        // Setup version switch/view buttons
        const viewVersionButtons = versionHistoryModal.querySelectorAll('.view-version-btn');
        viewVersionButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const versionId = this.getAttribute('data-version-id');
                const abstractId = this.getAttribute('data-abstract-id');

                if (versionId && abstractId) {
                    switchToVersion(abstractId, versionId);
                }
            });
        });
    }
}

/**
 * Setup edit button behavior
 */
function setupEditButtonBehavior() {
    // Get all edit abstract buttons
    const editButtons = document.querySelectorAll('.edit-abstract-btn');

    editButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            // If editing is being done via AJAX, show loading
            if (this.getAttribute('data-ajax') === 'true') {
                e.preventDefault();
                const abstractId = this.getAttribute('data-abstract-id');
                const versionId = this.getAttribute('data-version-id');

                if (abstractId) {
                    editAbstractVersion(abstractId, versionId);
                }
            } else {
                // Standard link behavior will apply
                showLoading(e);
            }
        });
    });
}

/**
 * Show loading indicator
 * @param {Event} e - The click event
 */
function showLoading(e) {
    // Only show loading if not prevented already
    if (!e.defaultPrevented) {
        Swal.fire({
            title: 'Loading...',
            html: `
                <div class="text-start">
                    <p>Preparing the abstract editor...</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                // Allow the default navigation to proceed
            }
        });
    }
}

/**
 * Compare two versions of an abstract
 * @param {string} versionId1 - The ID of the first version
 * @param {string} versionId2 - The ID of the second version
 */
function compareVersions(versionId1, versionId2) {
    console.log('Compare function called with IDs:', versionId1, versionId2);

    // Ensure the version compare modal exists
    ensureVersionCompareModalExists();

    // Show loading indicator
    Swal.fire({
        title: 'Comparing Versions',
        html: `
            <div class="text-start">
                <p>Loading version details for comparison...</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });

    // Check if jQuery is available and use appropriate method
    if (typeof jQuery !== 'undefined') {
        // Use jQuery AJAX
        jQuery.ajax({
            url: `/api/abstracts/versions/compare`,
            type: 'GET',
            data: {
                version1: versionId1,
                version2: versionId2
            },
            dataType: 'json',
            success: function (response) {
                console.log('Comparison API response (jQuery):', response);
                handleComparisonResponse(response);
            },
            error: function (xhr, status, error) {
                console.error('jQuery AJAX Error:', error);

                Swal.close();

                // Show error message
                Swal.fire({
                    title: 'Comparison Failed',
                    html: `
                        <div class="text-start">
                            <p>An error occurred while comparing versions: ${error}</p>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="bx bx-error-circle me-1"></i>
                                <small>Please try again later or contact support if this problem persists.</small>
                            </div>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            }
        });
    } else {
        // Use vanilla JavaScript fetch
        fetch(`/api/abstracts/versions/compare?version1=${versionId1}&version2=${versionId2}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(response => {
                console.log('Comparison API response (fetch):', response);
                handleComparisonResponse(response);
            })
            .catch(error => {
                console.error('Fetch Error:', error);

                Swal.close();

                // Show error message
                Swal.fire({
                    title: 'Comparison Failed',
                    html: `
                    <div class="text-start">
                        <p>An error occurred while comparing versions: ${error.message}</p>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bx bx-error-circle me-1"></i>
                            <small>Please try again later or contact support if this problem persists.</small>
                        </div>
                    </div>
                `,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5156be'
                });
            });
    }
}

/**
 * Handle the comparison API response
 * @param {Object} response - The API response
 */
function handleComparisonResponse(response) {
    Swal.close();

    if (response && response.status && response.data) {
        try {
            // Get the modal
            const versionCompareModal = document.getElementById('versionCompareModal');
            if (!versionCompareModal) {
                throw new Error('Version compare modal not found in the DOM');
            }

            // Initialize the Bootstrap modal
            const diffModal = new bootstrap.Modal(versionCompareModal);

            // Populate the modal with comparison data
            const modalBody = versionCompareModal.querySelector('.modal-body');
            if (!modalBody) {
                throw new Error('Modal body not found in the version compare modal');
            }

            // Check if we have all required data
            if (!response.data.version1 || !response.data.version2) {
                throw new Error('Missing version data in the response');
            }

            // Safe access to possibly missing properties
            const version1 = response.data.version1 || {};
            const version2 = response.data.version2 || {};

            // Create HTML for diff view with fallbacks for missing data
            let diffHtml = `
                <div class="diff-container">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary me-2">v${version1.version_number || '?'}</span>
                                        ${version1.title || 'Untitled'}
                                    </h6>
                                    <small class="text-muted">${version1.created_at ? new Date(version1.created_at).toLocaleString() : 'No date'}</small>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-semibold">Content</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        ${version1.content || 'No content available'}
                                    </div>
                                    
                                    <h6 class="fw-semibold">Keywords</h6>
                                    <div class="bg-light p-3 rounded">
                                        ${version1.keywords ? version1.keywords.split(',').map(keyword =>
                `<span class="badge bg-soft-primary text-primary me-1 mb-1">${keyword.trim()}</span>`
            ).join('') : '<span class="text-muted">No keywords</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <span class="badge bg-success me-2">v${version2.version_number || '?'}</span>
                                        ${version2.title || 'Untitled'}
                                    </h6>
                                    <small class="text-muted">${version2.created_at ? new Date(version2.created_at).toLocaleString() : 'No date'}</small>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-semibold">Content</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        ${version2.content || 'No content available'}
                                    </div>
                                    
                                    <h6 class="fw-semibold">Keywords</h6>
                                    <div class="bg-light p-3 rounded">
                                        ${version2.keywords ? version2.keywords.split(',').map(keyword =>
                `<span class="badge bg-soft-primary text-primary me-1 mb-1">${keyword.trim()}</span>`
            ).join('') : '<span class="text-muted">No keywords</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Changes Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-1"></i> This comparison feature is being developed.
                                For now, you can visually compare the versions side by side.
                            </div>
                        </div>
                    </div>
                </div>
            `;

            modalBody.innerHTML = diffHtml;

            // Show the modal
            diffModal.show();
        } catch (e) {
            console.error('Error rendering comparison data:', e);
            showComparisonError('An error occurred while rendering the comparison: ' + e.message);
        }
    } else {
        // Show error message
        showComparisonError('Unable to fetch version details for comparison.');
    }
}

/**
 * Show a comparison error message
 * @param {string} message - The error message to display
 */
function showComparisonError(message) {
    Swal.fire({
        title: 'Comparison Failed',
        html: `
            <div class="text-start">
                <p>${message}</p>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bx bx-error-circle me-1"></i>
                    <small>Please try again later or contact support if this problem persists.</small>
                </div>
            </div>
        `,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#5156be'
    });
}

/**
 * Create the version comparison modal if it doesn't exist
 * @returns {HTMLElement} The modal element
 */
function createVersionCompareModal() {
    const modalId = 'versionCompareModal';

    // If modal already exists, return it
    if (document.getElementById(modalId)) {
        return document.getElementById(modalId);
    }

    // Create modal structure
    const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="${modalId}Label">
                            <i class="bx bx-git-compare me-1"></i> Version Comparison
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Content will be populated dynamically -->
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Append to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    return document.getElementById(modalId);
}

/**
 * Ensure the version compare modal exists in the DOM
 */
function ensureVersionCompareModalExists() {
    if (!document.getElementById('versionCompareModal')) {
        createVersionCompareModal();
        console.log('Version compare modal created during initialization');
    }
}

/**
 * Switch to a specific version for viewing
 * @param {string} abstractId - The ID of the abstract
 * @param {string} versionId - The ID of the version to switch to
 */
function switchToVersion(abstractId, versionId) {
    console.log(`Switching to version ${versionId} of abstract ${abstractId}`);

    // Check if this is the latest version
    const isLatest = isLatestVersion(versionId);

    // If this is the latest version and the user clicked on Edit, go to edit page
    if (isLatest && event && event.target && (event.target.classList.contains('bx-edit') || event.target.closest('.btn').classList.contains('btn-primary'))) {
        // Show loading indicator
        Swal.fire({
            title: 'Loading Editor',
            html: `
                <div class="text-start">
                    <p>Preparing the abstract editor...</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        window.location.href = `/abstract-paper/edit/${abstractId}`;
    } else if (!isLatest) {
        // For older versions, show a confirmation dialog first to make it clear it's view-only
        Swal.fire({
            title: 'View Previous Version',
            html: `
                <div class="text-start">
                    <p>You are about to view a previous version of this abstract.</p>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        <small>Note: Previous versions cannot be edited. You can only edit the latest version.</small>
                    </div>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Continue to View',
            confirmButtonColor: '#5156be',
            cancelButtonText: 'Cancel',
            cancelButtonColor: '#74788d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading indicator
                Swal.fire({
                    title: 'Loading Version',
                    html: `
                        <div class="text-start">
                            <p>Loading version details...</p>
                            <div class="progress mt-3">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                // Navigate to the view page for this version
                window.location.href = `/abstract-paper/view/${abstractId}/${versionId}`;
            }
        });
    } else {
        // For the latest version with view button
        // Show loading indicator
        Swal.fire({
            title: 'Loading Version',
            html: `
                <div class="text-start">
                    <p>Loading version details...</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        window.location.href = `/abstract-paper/view/${abstractId}/${versionId}`;
    }
}

/**
 * Check if a version is the latest version
 * @param {string} versionId - The ID of the version to check
 * @returns {boolean} True if it's the latest version
 */
function isLatestVersion(versionId) {
    // Get the version history modal
    const versionHistoryModal = document.getElementById('versionHistoryModal');

    if (!versionHistoryModal) {
        return false;
    }

    // Get the first (latest) version in the accordion
    const firstVersionItem = versionHistoryModal.querySelector('.accordion-item:first-child');
    if (!firstVersionItem) {
        return false;
    }

    // Get the version ID from the first version's edit button
    const editButton = firstVersionItem.querySelector('.view-version-btn');
    if (!editButton) {
        return false;
    }

    const latestVersionId = editButton.getAttribute('data-version-id');

    // Return true if the provided versionId matches the latest version ID
    return versionId === latestVersionId;
}

/**
 * Edit an abstract version using AJAX
 * @param {string} abstractId - The ID of the abstract
 * @param {string} versionId - The ID of the version (optional)
 */
function editAbstractVersion(abstractId, versionId = null) {
    console.log(`Editing abstract ${abstractId}, version ${versionId || 'latest'}`);

    // Show loading indicator
    Swal.fire({
        title: 'Loading Editor',
        html: `
            <div class="text-start">
                <p>Preparing the abstract editor...</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });

    // Always redirect to the edit page with just the abstract ID (it will always edit the latest version)
    let editUrl = `/abstract-paper/edit/${abstractId}`;

    window.location.href = editUrl;
}
