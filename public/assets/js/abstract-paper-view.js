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
    console.log('Redirecting to comparison page:', comparisonUrl);

    // Show loading indicator and redirect
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
        allowEscapeKey: false,
        willClose: () => {
            // Handle cleanup when alert is closed
            console.log('SweetAlert closing');
        },
        didClose: () => {
            // Navigate after alert is fully closed
            window.location.href = comparisonUrl;
        }
    })
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
