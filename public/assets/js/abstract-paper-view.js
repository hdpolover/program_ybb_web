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

    // Initialize author management
    initializeAuthorManagement();
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

    // Validate version IDs
    if (!versionId1 || !versionId2) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Version IDs',
            text: 'Both version IDs are required for comparison.',
            confirmButtonColor: '#5156be'
        });
        return;
    }

    if (versionId1 === versionId2) {
        Swal.fire({
            icon: 'warning',
            title: 'Same Version Selected',
            text: 'Cannot compare a version with itself. Please select two different versions.',
            confirmButtonColor: '#5156be'
        });
        return;
    }

    // Close any existing modals
    const versionHistoryModal = document.getElementById('versionHistoryModal');
    if (versionHistoryModal) {
        const modal = bootstrap.Modal.getInstance(versionHistoryModal);
        if (modal) {
            modal.hide();
        }
    }

    // Close any existing SweetAlert
    if (Swal.isVisible()) {
        Swal.close();
    }

    // Construct comparison URL
    const comparisonUrl = `/abstract-paper/compare/${versionId1}/${versionId2}`;
    console.log('Navigating to comparison page:', comparisonUrl);

    // Try the alternate AJAX approach first
    tryAjaxComparison(versionId1, versionId2, comparisonUrl);
}

/**
 * Try AJAX comparison first, fallback to navigation if needed
 * @param {string} versionId1 - The ID of the first version
 * @param {string} versionId2 - The ID of the second version
 * @param {string} fallbackUrl - The URL to navigate to if AJAX fails
 */
function tryAjaxComparison(versionId1, versionId2, fallbackUrl) {
    // Show loading indicator
    Swal.fire({
        title: 'Comparing Versions',
        html: `
            <div class="text-start">
                <p>Loading version details for comparison...</p>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
                <small class="text-muted mt-2">Press Escape to cancel</small>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: true,
        timer: 10000 // 10 second timeout
    });

    // Set up AJAX request with jQuery fallback
    const ajaxUrl = `/abstract-paper/compare/${versionId1}/${versionId2}`;
    
    // Try using fetch API first
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 8000); // 8 second timeout for fetch

    fetch(ajaxUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        },
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.text();
    })
    .then(html => {
        // Successfully got HTML response, inject it
        Swal.close();
        
        // Create a new page with the comparison content
        const newDocument = document.open("text/html", "replace");
        newDocument.write(html);
        newDocument.close();
        
        // Update browser history
        history.pushState(null, null, fallbackUrl);
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.log('AJAX comparison failed, falling back to navigation:', error);
        
        // Close loading and try direct navigation
        Swal.close();
        
        // Show a brief transition message
        Swal.fire({
            title: 'Redirecting...',
            text: 'Loading comparison page...',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: 500
        }).then(() => {
            window.location.href = fallbackUrl;
        });
    });
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
            }            // Check if we have all required data
            if (!response.data.version1 || !response.data.version2) {
                throw new Error('Missing version data in the response');
            }

            // Safe access to possibly missing properties
            const version1 = response.data.version1 || {};
            const version2 = response.data.version2 || {};
            const differences = response.data.differences || {};

            // Create HTML for diff view with actual differences highlighting
            let diffHtml = `
                <div class="diff-container">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <span class="badge bg-secondary me-2">v${version1.version_number || '?'}</span>
                                        ${highlightChanges(version1.title || 'Untitled', differences.title, 'old')}
                                    </h6>
                                    <small class="text-muted">${version1.created_at ? new Date(version1.created_at).toLocaleString() : 'No date'}</small>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-semibold">Content</h6>
                                    <div class="bg-light p-3 rounded mb-3 comparison-content">
                                        ${highlightChanges(version1.content || 'No content available', differences.content, 'old')}
                                    </div>
                                    
                                    <h6 class="fw-semibold">Keywords</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        ${renderKeywords(version1.keywords, differences.keywords, 'old')}
                                    </div>

                                    <h6 class="fw-semibold">References</h6>
                                    <div class="bg-light p-3 rounded">
                                        ${highlightChanges(version1.refs || 'No references provided', differences.refs, 'old')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <span class="badge bg-success me-2">v${version2.version_number || '?'}</span>
                                        ${highlightChanges(version2.title || 'Untitled', differences.title, 'new')}
                                    </h6>
                                    <small class="text-muted">${version2.created_at ? new Date(version2.created_at).toLocaleString() : 'No date'}</small>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-semibold">Content</h6>
                                    <div class="bg-light p-3 rounded mb-3 comparison-content">
                                        ${highlightChanges(version2.content || 'No content available', differences.content, 'new')}
                                    </div>
                                    
                                    <h6 class="fw-semibold">Keywords</h6>
                                    <div class="bg-light p-3 rounded mb-3">
                                        ${renderKeywords(version2.keywords, differences.keywords, 'new')}
                                    </div>

                                    <h6 class="fw-semibold">References</h6>
                                    <div class="bg-light p-3 rounded">
                                        ${highlightChanges(version2.refs || 'No references provided', differences.refs, 'new')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bx bx-list-ul me-1"></i> Changes Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            ${renderChangesSummary(differences)}
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

/**
 * Highlight changes in text for comparison
 * @param {string} text - The text to display
 * @param {object} difference - The difference object for this field
 * @param {string} type - 'old' or 'new' to determine which value to show
 * @returns {string} HTML with highlighted changes
 */
function highlightChanges(text, difference, type) {
    if (!difference || !difference.changed) {
        return text;
    }

    const value = type === 'old' ? difference.old_value : difference.new_value;
    const changeType = difference.change_type;

    let className = '';
    switch (changeType) {
        case 'added':
            className = type === 'new' ? 'bg-success-subtle text-success' : 'text-muted';
            break;
        case 'removed':
            className = type === 'old' ? 'bg-danger-subtle text-danger' : 'text-muted';
            break;
        case 'modified':
            className = type === 'old' ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info';
            break;
    }

    if (className) {
        return `<span class="${className}">${value || text}</span>`;
    }

    return value || text;
}

/**
 * Render keywords with change highlighting
 * @param {string} keywords - Comma-separated keywords
 * @param {object} difference - The difference object for keywords
 * @param {string} type - 'old' or 'new'
 * @returns {string} HTML for keywords
 */
function renderKeywords(keywords, difference, type) {
    if (!keywords) {
        return '<span class="text-muted">No keywords</span>';
    }

    const keywordArray = keywords.split(',').map(k => k.trim()).filter(k => k);

    if (!difference || !difference.changed) {
        return keywordArray.map(keyword =>
            `<span class="badge bg-soft-primary text-primary me-1 mb-1">${keyword}</span>`
        ).join('');
    }

    // For changed keywords, highlight the entire set
    const changeType = difference.change_type;
    let badgeClass = 'bg-soft-primary text-primary';

    switch (changeType) {
        case 'added':
            badgeClass = type === 'new' ? 'bg-success text-white' : 'bg-light text-muted';
            break;
        case 'removed':
            badgeClass = type === 'old' ? 'bg-danger text-white' : 'bg-light text-muted';
            break;
        case 'modified':
            badgeClass = type === 'old' ? 'bg-warning text-dark' : 'bg-info text-white';
            break;
    }

    return keywordArray.map(keyword =>
        `<span class="badge ${badgeClass} me-1 mb-1">${keyword}</span>`
    ).join('');
}

/**
 * Render changes summary
 * @param {object} differences - All field differences
 * @returns {string} HTML for changes summary
 */
function renderChangesSummary(differences) {
    const stats = {
        added: 0,
        removed: 0,
        modified: 0,
        unchanged: 0,
        total: 0
    };

    const changedFields = [];
    const unchangedFields = [];

    // Calculate statistics
    Object.keys(differences).forEach(field => {
        stats.total++;
        if (differences[field].changed) {
            const change = differences[field];
            switch (change.change_type) {
                case 'added':
                    stats.added++;
                    break;
                case 'removed':
                    stats.removed++;
                    break;
                case 'modified':
                    stats.modified++;
                    break;
            }
            changedFields.push({
                field: field.charAt(0).toUpperCase() + field.slice(1),
                change: change
            });
        } else {
            stats.unchanged++;
            unchangedFields.push(field.charAt(0).toUpperCase() + field.slice(1));
        }
    });

    let summaryHtml = `
        <div class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h5 class="mb-1">${stats.added}</h5>
                        <p class="text-muted mb-0 small">Added</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h5 class="mb-1">${stats.removed}</h5>
                        <p class="text-muted mb-0 small">Removed</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h5 class="mb-1">${stats.modified}</h5>
                        <p class="text-muted mb-0 small">Modified</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <h5 class="mb-1">${stats.unchanged}</h5>
                        <p class="text-muted mb-0 small">Unchanged</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    if (changedFields.length > 0) {
        summaryHtml += `
            <div class="mb-3">
                <h6 class="text-primary">
                    <i class="bx bx-info-circle me-1"></i> Changed Fields (${changedFields.length})
                </h6>
                <div class="list-group list-group-flush">
        `;

        changedFields.forEach(item => {
            const iconClass = getChangeIcon(item.change.change_type);
            const badgeClass = getChangeBadgeClass(item.change.change_type);

            let changeDetails = '';
            // Add word count comparison for content changes
            if (item.field.toLowerCase() === 'content') {
                const oldWords = item.change.old_value ? countWords(item.change.old_value) : 0;
                const newWords = item.change.new_value ? countWords(item.change.new_value) : 0;
                const wordDiff = newWords - oldWords;
                const wordDiffText = wordDiff > 0 ? `+${wordDiff}` : wordDiff;
                changeDetails = `<small class="text-muted ms-2">(${oldWords} → ${newWords} words, ${wordDiffText})</small>`;
            }
            // Add keyword count for keyword changes
            else if (item.field.toLowerCase() === 'keywords') {
                const oldCount = item.change.old_value ? item.change.old_value.split(',').filter(k => k.trim()).length : 0;
                const newCount = item.change.new_value ? item.change.new_value.split(',').filter(k => k.trim()).length : 0;
                const countDiff = newCount - oldCount;
                const countDiffText = countDiff > 0 ? `+${countDiff}` : countDiff;
                changeDetails = `<small class="text-muted ms-2">(${oldCount} → ${newCount} keywords, ${countDiffText})</small>`;
            }

            summaryHtml += `
                <div class="list-group-item px-0 py-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong><i class="${iconClass} me-1"></i>${item.field}</strong>
                            <span class="badge ${badgeClass} ms-2">${item.change.change_type}</span>
                            ${changeDetails}
                        </div>
                    </div>
                </div>
            `;
        });

        summaryHtml += `
                </div>
            </div>
        `;
    }

    if (unchangedFields.length > 0) {
        summaryHtml += `
            <div class="mb-0">
                <h6 class="text-success">
                    <i class="bx bx-check-circle me-1"></i> Unchanged Fields (${unchangedFields.length})
                </h6>
                <div class="d-flex flex-wrap gap-1">
                    ${unchangedFields.map(field =>
            `<span class="badge bg-soft-success text-success">${field}</span>`
        ).join('')}
                </div>
            </div>
        `;
    }

    if (changedFields.length === 0 && unchangedFields.length === 0) {
        summaryHtml = `
            <div class="alert alert-info mb-0">
                <i class="bx bx-info-circle me-1"></i> No comparison data available.
            </div>
        `;
    }

    return summaryHtml;
}

/**
 * Count words in a text string
 * @param {string} text 
 * @returns {number}
 */
function countWords(text) {
    if (!text || typeof text !== 'string') return 0;
    return text.trim().split(/\s+/).filter(word => word.length > 0).length;
}

/**
 * Author Management Functionality
 */

// Initialize author management when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    // ...existing initialization code...
    
    // Initialize author management
    initializeAuthorManagement();
});

/**
 * Initialize author management functionality
 */
function initializeAuthorManagement() {
    console.log('Initializing author management...');
    
    // Check if modal exists
    const modal = document.getElementById('addCoAuthorModal');
    if (!modal) {
        console.warn('Author modal not found. Author management features will not be available.');
        return;
    }
    
    // Check for required elements
    const requiredElements = [
        'search_participant_btn',
        'search_email', 
        'search_result',
        'addAuthorForm',
        'full_name',
        'email',
        'institution'
    ];
    
    const missingElements = requiredElements.filter(id => !document.getElementById(id));
    if (missingElements.length > 0) {
        console.warn('Missing required elements for author management:', missingElements);
    }
    
    // Initialize author type selection cards
    initializeAuthorTypeCards();
    
    // Initialize participant search
    initializeParticipantSearch();
    
    // Initialize author form handling
    initializeAuthorForm();
    
    // Initialize author action buttons
    initializeAuthorActions();
    
    // Initialize modal event handlers
    initializeModalHandlers();
    
    // Initialize Bootstrap tooltips
    initializeTooltips();
    
    console.log('Author management initialization complete.');
}

/**
 * Initialize modal event handlers
 */
function initializeModalHandlers() {
    const modal = document.getElementById('addCoAuthorModal');
    
    if (modal) {
        // Reset form when modal is hidden
        modal.addEventListener('hidden.bs.modal', function () {
            resetAuthorForm();
        });
        
        // Initialize default state when modal is shown
        modal.addEventListener('shown.bs.modal', function () {
            // Set default to "New Author" and hide search section
            const newAuthorCard = modal.querySelector('.author-type-card[data-type="new"]');
            const participantCard = modal.querySelector('.author-type-card[data-type="participant"]');
            const searchSection = document.getElementById('participant_search_section');
            
            if (newAuthorCard && participantCard && searchSection) {
                // Remove selected from participant card and add to new author card
                participantCard.classList.remove('selected');
                newAuthorCard.classList.add('selected');
                  // Check the correct radio button
                document.getElementById('is_participant_no').checked = true;
                document.getElementById('is_participant_yes').checked = false;
                
                // Hide search section
                searchSection.style.display = 'none';
            }
            
            // Focus on first input
            const firstInput = modal.querySelector('#full_name');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
        });
    }
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        console.warn('Bootstrap Tooltip not available. Tooltips will not be initialized.');
        return;
    }
    
    try {
        // Initialize tooltips for author action buttons
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        console.log('Tooltips initialized successfully.');
    } catch (error) {
        console.warn('Failed to initialize tooltips:', error);
    }
}

/**
 * Reset author form to initial state
 */
function resetAuthorForm() {
    // Clear form fields
    clearAuthorForm();
    
    // Clear search results
    clearSearchResults();
    
    // Reset search input
    const searchInput = document.getElementById('search_email');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset button states
    setSearchButtonLoading(false);
      // Reset to default tab
    const authorListTab = document.querySelector('[href="#authorList"]');
    if (authorListTab && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
        try {
            const tabInstance = new bootstrap.Tab(authorListTab);
            tabInstance.show();
        } catch (error) {
            console.warn('Failed to reset to author list tab:', error);
            // Fallback: manually trigger click
            authorListTab.click();
        }
    }
}

/**
 * Reset modal for adding new author
 */
function resetAuthorModal() {
    const form = document.getElementById('addAuthorForm');
    const modalTitle = document.querySelector('#addCoAuthorModal .modal-title');
    
    // Reset modal title and form action
    modalTitle.textContent = 'Add Co-Author';
    form.action = '/abstract-paper/add-author';
    
    // Remove edit author ID field if it exists
    const authorIdField = document.getElementById('edit_author_id');
    if (authorIdField) {
        authorIdField.remove();
    }
    
    // Clear all form fields
    form.reset();
    
    // Reset to default state (new author)
    document.getElementById('is_participant_yes').checked = false;
    document.getElementById('is_participant_no').checked = true;
    
    // Update card selection visually
    const participantCard = document.querySelector('[data-type="participant"]');
    const newAuthorCard = document.querySelector('[data-type="new"]');
    
    if (participantCard) participantCard.classList.remove('selected');
    if (newAuthorCard) newAuthorCard.classList.add('selected');
    
    // Enable email field and remove any restrictions
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.readOnly = false;
        emailField.classList.remove('bg-light');
    }
    
    // Remove any email notes
    const emailNote = document.getElementById('participant_email_note');
    if (emailNote) {
        emailNote.remove();
    }
    
    // Clear participant_id
    const participantIdField = document.getElementById('selected_participant_id');
    if (participantIdField) {
        participantIdField.value = '';
    }
    
    // Clear any validation feedback
    const feedbackElement = document.getElementById('email_validation_feedback');
    if (feedbackElement) {
        feedbackElement.remove();
    }
    
    // Remove validation classes
    const inputs = form.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

/**
 * Initialize author type selection cards
 */
function initializeAuthorTypeCards() {
    const authorTypeCards = document.querySelectorAll('.author-type-card');
    
    authorTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            authorTypeCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Update radio button
            const radioInput = this.querySelector('input[type="radio"]');
            if (radioInput) {
                radioInput.checked = true;
            }
            
            // Show/hide participant search section
            const searchSection = document.getElementById('participant_search_section');
            const authorDetailsSection = document.getElementById('author_details_section');
            
            if (this.getAttribute('data-type') === 'participant') {
                searchSection.style.display = 'block';
                // Clear any previous search results
                clearSearchResults();
            } else {
                searchSection.style.display = 'none';
                // Clear form when switching to manual entry
                clearAuthorForm();
            }
        });
    });
}

/**
 * Initialize participant search functionality
 */
function initializeParticipantSearch() {
    const searchBtn = document.getElementById('search_participant_btn');
    const searchInput = document.getElementById('search_email');
    
    if (searchBtn && searchInput) {
        // Handle search button click
        searchBtn.addEventListener('click', function() {
            const email = searchInput.value.trim();
            if (!email) {
                showAlert('warning', 'Please enter an email address to search.');
                return;
            }
            
            if (!isValidEmail(email)) {
                showAlert('error', 'Please enter a valid email address.');
                return;
            }
            
            searchParticipant(email);
        });
        
        // Handle Enter key in search input
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchBtn.click();
            }
        });
    }
}

/**
 * Search for participant by email
 */
function searchParticipant(email) {
    const searchBtn = document.getElementById('search_participant_btn');
    const searchResult = document.getElementById('search_result');
    const programId = document.querySelector('input[name="program_id"]')?.value;
    
    console.log('[searchParticipant] Starting search for email:', email, 'in program:', programId);
    
    if (!programId) {
        console.error('[searchParticipant] Program ID not found');
        showAlert('error', 'Program ID not found. Please refresh the page and try again.');
        return;
    }
    
    // Show loading state
    setSearchButtonLoading(true);
    clearSearchResults();
    
    // Construct the URL
    const searchUrl = `/abstract-paper/search-participant?email=${encodeURIComponent(email)}&program_id=${encodeURIComponent(programId)}`;
    console.log('[searchParticipant] Making request to:', searchUrl);
    
    // Make AJAX request
    fetch(searchUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('[searchParticipant] Received response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('[searchParticipant] Received data:', data);
        setSearchButtonLoading(false);
        
        if (data.success === false) {
            console.error('[searchParticipant] Search failed:', data.message);
            // Show error message
            searchResult.innerHTML = `
                <div class="alert alert-danger fade show">
                    <i class="bx bx-error-circle me-2"></i>
                    ${data.message || 'An error occurred while searching.'}
                </div>
            `;
        } else if (data.found && data.participant) {
            console.log('[searchParticipant] Participant found:', data.participant);
            // Participant found - populate form
            populateAuthorForm(data.participant);
            searchResult.innerHTML = `
                <div class="alert alert-success fade show">
                    <i class="bx bx-check-circle me-2"></i>
                    ${data.message || 'Participant found and details loaded.'}
                </div>
            `;
        } else {
            console.log('[searchParticipant] No participant found');
            // No participant found
            searchResult.innerHTML = `
                <div class="alert alert-info fade show">
                    <i class="bx bx-info-circle me-2"></i>
                    ${data.message || 'No registered participant found with this email address.'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('[searchParticipant] Request failed:', error);
        setSearchButtonLoading(false);
        searchResult.innerHTML = `
            <div class="alert alert-danger fade show">
                <i class="bx bx-error-circle me-2"></i>
                An error occurred while searching. Please try again.
            </div>
        `;
    });
}

/**
 * Set search button loading state
 */
function setSearchButtonLoading(loading) {
    const searchBtn = document.getElementById('search_participant_btn');
    const btnText = searchBtn.querySelector('.btn-text');
    const spinner = searchBtn.querySelector('.spinner-border');
    
    if (loading) {
        searchBtn.disabled = true;
        btnText.textContent = 'Searching...';
        spinner.classList.remove('d-none');
    } else {
        searchBtn.disabled = false;
        btnText.textContent = 'Search';
        spinner.classList.add('d-none');
    }
}

/**
 * Populate author form with participant data
 */
function populateAuthorForm(participant) {
    document.getElementById('full_name').value = participant.full_name || '';
    document.getElementById('email').value = participant.email || '';
    document.getElementById('institution').value = participant.institution || '';
    document.getElementById('selected_participant_id').value = participant.id || '';
}

/**
 * Clear author form
 */
function clearAuthorForm() {
    document.getElementById('full_name').value = '';
    document.getElementById('email').value = '';
    document.getElementById('institution').value = '';
    document.getElementById('selected_participant_id').value = '';
}

/**
 * Clear search results
 */
function clearSearchResults() {
    const searchResult = document.getElementById('search_result');
    if (searchResult) {
        searchResult.innerHTML = '';
    }
}

/**
 * Initialize author form handling
 */
function initializeAuthorForm() {
    const form = document.getElementById('addAuthorForm');
    const emailInput = document.getElementById('email');
    const modal = document.getElementById('addCoAuthorModal');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Submit form via AJAX (validation is handled in submitAuthorForm)
            submitAuthorForm();
        });
    }
    
    // Add modal event listeners
    if (modal) {
        // Reset modal when opened via "Add Co-Author" button (not edit)
        modal.addEventListener('show.bs.modal', function(event) {
            // Check if this is triggered by the add button (not edit)
            const button = event.relatedTarget;
            if (!button || !button.classList.contains('edit-author')) {
                // This is for adding a new author, so reset the modal
                setTimeout(() => resetAuthorModal(), 50); // Small delay to ensure modal is fully initialized
            }
        });
    }
}

/**
 * Validate author form
 */
function validateAuthorForm() {
    const fullName = document.getElementById('full_name').value.trim();
    const email = document.getElementById('email').value.trim();
    const institution = document.getElementById('institution').value.trim();
    
    if (!fullName) {
        showAlert('error', 'Full name is required.');
        document.getElementById('full_name').focus();
        return false;
    }
    
    if (!email) {
        showAlert('error', 'Email address is required.');
        document.getElementById('email').focus();
        return false;
    }
    
    if (!isValidEmail(email)) {
        showAlert('error', 'Please enter a valid email address.');
        document.getElementById('email').focus();
        return false;
    }
    
    if (!institution) {
        showAlert('error', 'Institution is required.');
        document.getElementById('institution').focus();
        return false;
    }
    
    return true;
}

/**
 * Simplified email validation (basic format check only)
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Submit author form
 */
function submitAuthorForm() {
    const form = document.getElementById('addAuthorForm');
    const abstractId = document.querySelector('input[name="abstract_id"]')?.value;
    const authorId = document.getElementById('edit_author_id')?.value;
    const isEditing = !!authorId;
    
    if (!abstractId) {
        showAlert('error', 'Abstract ID not found. Please refresh the page and try again.');
        return;
    }
    
    // First validate the form locally
    if (!validateAuthorForm()) {
        return;
    }
    
    // Show loading for form submission
    const actionText = isEditing ? 'Updating' : 'Adding';
    Swal.fire({
        title: `${actionText} Author...`,
        html: `Please wait while we ${actionText.toLowerCase()} the author ${isEditing ? 'information' : 'to your abstract'}.`,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
      // Submit via AJAX
    const formData = new FormData(form);
    
    // Ensure is_participant field is correctly set based on radio selection
    const isParticipantYes = document.getElementById('is_participant_yes').checked;
    const isParticipantNo = document.getElementById('is_participant_no').checked;
    
    // Set the correct value: "1" for participant, "0" for new author
    if (isParticipantYes) {
        formData.set('is_participant', '1');
        // Note: participant_id should be set by participant search functionality
        // Backend will only include participant_id in API call if is_participant="1" AND participant_id is provided
    } else if (isParticipantNo) {
        formData.set('is_participant', '0');
        // Note: participant_id is not needed for new authors (is_participant="0")
    } else {
        // Default to "0" (new author) if neither is selected
        formData.set('is_participant', '0');
    }
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())    .then(data => {
        Swal.close();
          // Handle both success formats: data.success (our format) or data.status === 'success' (API format)
        if (data.success === true || data.status === 'success') {
            // Success - show message and reload page
            const successTitle = isEditing ? 'Author Updated Successfully!' : 'Author Added Successfully!';
            const successText = data.message || (isEditing ? 'The author information has been updated.' : 'The author has been added to your abstract.');
            
            Swal.fire({
                icon: 'success',
                title: successTitle,
                text: successText,
                confirmButtonColor: '#5156be'
            }).then(() => {
                // Close modal and reload page
                const modal = bootstrap.Modal.getInstance(document.getElementById('addCoAuthorModal'));
                if (modal) {
                    modal.hide();
                }
                location.reload();
            });
        } else {            // Error - show detailed error message with SweetAlert
            let title = isEditing ? 'Unable to Update Author' : 'Unable to Add Author';
            let errorMessage = data.message || (isEditing ? 'An error occurred while updating the author.' : 'An error occurred while adding the author.');
            let icon = 'error';
            
            // Handle specific conflict errors that might still occur
            if (errorMessage.includes('already assigned to another abstract')) {
                title = 'Email Already in Use';
                errorMessage = 'This author email is already assigned to another abstract in the same program. One participant can only be assigned to one abstract at a time per program.';
                icon = 'warning';
            }
            
            Swal.fire({
                icon: icon,
                title: title,
                text: errorMessage,
                confirmButtonColor: '#5156be',
                confirmButtonText: 'Understood'
            });
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'An error occurred while adding the author. Please check your connection and try again.',
            confirmButtonColor: '#5156be'
        });
    });
}

/**
 * Initialize author action buttons (edit, delete, view)
 */
function initializeAuthorActions() {
    // View author buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-author')) {
            e.preventDefault();
            const btn = e.target.closest('.view-author');
            const authorData = JSON.parse(btn.getAttribute('data-author'));
            viewAuthorDetails(authorData);
        }
        
        if (e.target.closest('.edit-author')) {
            e.preventDefault();
            const btn = e.target.closest('.edit-author');
            const authorData = JSON.parse(btn.getAttribute('data-author'));
            editAuthorDetails(authorData);
        }
        
        if (e.target.closest('.delete-author')) {
            e.preventDefault();
            const btn = e.target.closest('.delete-author');
            const authorId = btn.getAttribute('data-author-id');
            const authorName = btn.getAttribute('data-author-name');
            deleteAuthor(authorId, authorName);
        }
    });
}

/**
 * View author details
 */
function viewAuthorDetails(author) {
    Swal.fire({
        title: 'Author Details',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <strong>Name:</strong> ${author.full_name || 'N/A'}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> ${author.email || 'N/A'}
                </div>
                <div class="mb-3">
                    <strong>Institution:</strong> ${author.institution || 'N/A'}
                </div>
                <div class="mb-3">
                    <strong>Type:</strong> ${author.is_participant == '1' ? 'Registered Participant' : 'External Author'}
                </div>
            </div>
        `,
        confirmButtonColor: '#5156be',
        confirmButtonText: 'Close'
    });
}

/**
 * Edit author details
 */
function editAuthorDetails(author) {
    // Reuse the add modal for editing
    const modal = new bootstrap.Modal(document.getElementById('addCoAuthorModal'));
    const form = document.getElementById('addAuthorForm');
    const modalTitle = document.querySelector('#addCoAuthorModal .modal-title');
    
    // Change modal title and form action for editing
    modalTitle.textContent = 'Edit Co-Author';
    form.action = '/abstract-paper/update-author';
    
    // Add hidden fields for author ID
    let authorIdField = document.getElementById('edit_author_id');
    if (!authorIdField) {
        authorIdField = document.createElement('input');
        authorIdField.type = 'hidden';
        authorIdField.name = 'author_id';
        authorIdField.id = 'edit_author_id';
        form.appendChild(authorIdField);
    }
    authorIdField.value = author.id;
    
    // Populate form fields
    document.getElementById('full_name').value = author.full_name || '';
    document.getElementById('email').value = author.email || '';
    document.getElementById('institution').value = author.institution || '';
    
    // Handle participant vs non-participant
    const isParticipant = author.is_participant === '1' || author.is_participant === 1;
    
    if (isParticipant) {
        // Select participant card and disable email field
        document.getElementById('is_participant_yes').checked = true;
        document.getElementById('is_participant_no').checked = false;
        
        // Update card selection visually
        const participantCard = document.querySelector('[data-type="participant"]');
        const newAuthorCard = document.querySelector('[data-type="new"]');
        
        participantCard.classList.add('selected');
        newAuthorCard.classList.remove('selected');
        
        // Disable email field for participants (their email is managed in participant system)
        const emailField = document.getElementById('email');
        emailField.readOnly = true;
        emailField.classList.add('bg-light');
        
        // Add note about email
        let emailNote = document.getElementById('participant_email_note');
        if (!emailNote) {
            emailNote = document.createElement('small');
            emailNote.id = 'participant_email_note';
            emailNote.className = 'text-muted';
            emailNote.innerHTML = '<i class="bx bx-info-circle me-1"></i>Email cannot be changed for registered participants.';
            emailField.parentNode.appendChild(emailNote);
        }
        
        // Set participant_id if available
        if (author.participant_id) {
            document.getElementById('selected_participant_id').value = author.participant_id;
        }
    } else {
        // Select new author card and enable email field
        document.getElementById('is_participant_yes').checked = false;
        document.getElementById('is_participant_no').checked = true;
        
        // Update card selection visually
        const participantCard = document.querySelector('[data-type="participant"]');
        const newAuthorCard = document.querySelector('[data-type="new"]');
        
        participantCard.classList.remove('selected');
        newAuthorCard.classList.add('selected');
        
        // Enable email field for new authors
        const emailField = document.getElementById('email');
        emailField.readOnly = false;
        emailField.classList.remove('bg-light');
        
        // Remove email note if it exists
        const emailNote = document.getElementById('participant_email_note');
        if (emailNote) {
            emailNote.remove();
        }
        
        // Clear participant_id
        document.getElementById('selected_participant_id').value = '';
    }
    
    // Show the modal
    modal.show();
}

/**
 * Delete author
 */
function deleteAuthor(authorId, authorName) {
    Swal.fire({
        title: 'Remove Author?',
        text: `Are you sure you want to remove "${authorName}" from this abstract?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f46a6a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Remove',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Perform delete action
            performDeleteAuthor(authorId);
        }
    });
}

/**
 * Perform delete author action
 */
function performDeleteAuthor(authorId) {
    const abstractId = document.querySelector('input[name="abstract_id"]')?.value;
    
    if (!abstractId) {
        showAlert('error', 'Abstract ID not found. Please refresh the page and try again.');
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Removing Author...',
        html: 'Please wait while we remove the author.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Prepare form data
    const formData = new FormData();
    formData.append('author_id', authorId);
    formData.append('abstract_id', abstractId);
    
    // Submit delete request
    fetch('/abstract-paper/delete-author', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            // Success
            Swal.fire({
                icon: 'success',
                title: 'Author Removed!',
                text: data.message || 'The author has been removed from your abstract.',
                confirmButtonColor: '#5156be'
            }).then(() => {
                location.reload();
            });
        } else {
            showAlert('error', data.message || 'An error occurred while removing the author.');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        Swal.close();
        showAlert('error', 'An error occurred while removing the author. Please try again.');
    });
}


/**
 * Utility function to show alerts
 */
function showAlert(type, message) {
    const iconMap = {
        'success': 'success',
        'error': 'error',
        'warning': 'warning',
        'info': 'info'
    };
    
    Swal.fire({
        icon: iconMap[type] || 'info',
        title: type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        confirmButtonColor: '#5156be'
    });
}
