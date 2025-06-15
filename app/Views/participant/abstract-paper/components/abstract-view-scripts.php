<script>
// Store version data safely in a JavaScript variable
window.abstractVersions = <?= json_encode($versions ?? []) ?>;
window.versionCount = <?= $versionCount ?? 0 ?>;

// Load external abstract-paper-view.js file first
document.addEventListener('DOMContentLoaded', function() {
    // Check if the external JS file functions are available
    if (typeof setupVersionHistory === 'undefined') {
        // Load the external JS file
        const externalScript = document.createElement('script');
        externalScript.src = '/assets/js/abstract-paper-view.js';
        externalScript.onload = function() {
            console.log('External abstract-paper-view.js loaded successfully');
            initAbstractViewFunctions();
        };
        externalScript.onerror = function() {
            console.warn('External abstract-paper-view.js failed to load, using fallback functions');
            initAbstractViewFunctions();
        };
        document.head.appendChild(externalScript);
    } else {
        initAbstractViewFunctions();
    }
    
    // Load paper upload handler
    const paperUploadScript = document.createElement('script');
    paperUploadScript.src = '/assets/js/paper-upload-handler.js';
    paperUploadScript.onload = function() {
        console.log('Paper upload handler loaded successfully');
    };
    paperUploadScript.onerror = function() {
        console.warn('Paper upload handler failed to load');
    };
    document.head.appendChild(paperUploadScript);
    
    // Ensure jQuery is loaded
    if (typeof jQuery === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        script.onload = function() {
            initAbstractViewFunctions();
        };
        document.head.appendChild(script);
    } else {
        initAbstractViewFunctions();
    }
});

// Initialize all the functions after ensuring jQuery is available
function initAbstractViewFunctions() {
    // Initialize version history functionality
    if (typeof setupVersionHistory === 'function') {
        setupVersionHistory();
    } else {
        // Fallback version history setup
        setupVersionHistoryFallback();
    }

    // Initialize edit button behavior
    if (typeof setupEditButtonBehavior === 'function') {
        setupEditButtonBehavior();
    }

    // Initialize tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize tooltips for disabled buttons
    const disabledButtons = document.querySelectorAll('button[disabled][title]');
    disabledButtons.forEach(function(button) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(button);
        }
    });

    // Initialize author management functionality
    initAuthorManagement();

    // Initialize enhanced author type cards
    initAuthorTypeCards();

    // Initialize enhanced search functionality
    initEnhancedSearch();

    // Initialize email field requirements based on default selection
    const defaultRadio = document.querySelector('input[name="is_participant"]:checked');
    if (defaultRadio) {
        handleParticipantSelection(defaultRadio);
    }
}

// Fallback version history setup if external JS fails to load
function setupVersionHistoryFallback() {
    const versionHistoryModal = document.getElementById('versionHistoryModal');
    
    if (versionHistoryModal) {
        // Add event listeners for the modal
        versionHistoryModal.addEventListener('shown.bs.modal', function () {
            console.log('Version history modal shown (fallback)');
            
            // Highlight the latest version
            const firstItem = versionHistoryModal.querySelector('.accordion-item:first-child');
            if (firstItem) {
                firstItem.style.backgroundColor = '#f8f9fa';
                firstItem.style.border = '2px solid #007bff';
            }
        });
        
        // Setup accordion functionality
        const accordionButtons = versionHistoryModal.querySelectorAll('.accordion-button');
        accordionButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                // Bootstrap should handle this, but ensure it works
                const targetId = this.getAttribute('data-bs-target');
                const targetCollapse = document.querySelector(targetId);
                
                if (targetCollapse) {
                    // Toggle the collapse
                    if (targetCollapse.classList.contains('show')) {
                        targetCollapse.classList.remove('show');
                        this.setAttribute('aria-expanded', 'false');
                        this.classList.add('collapsed');
                    } else {
                        // Close other open collapses
                        versionHistoryModal.querySelectorAll('.accordion-collapse.show').forEach(function(openCollapse) {
                            openCollapse.classList.remove('show');
                        });
                        versionHistoryModal.querySelectorAll('.accordion-button').forEach(function(btn) {
                            btn.setAttribute('aria-expanded', 'false');
                            btn.classList.add('collapsed');
                        });
                        
                        // Open this one
                        targetCollapse.classList.add('show');
                        this.setAttribute('aria-expanded', 'true');
                        this.classList.remove('collapsed');
                    }
                }
            });
        });
    }
}

// Function to initialize author management functionality
function initAuthorManagement() {
    // Handle participant search radio buttons
    const participantRadios = document.querySelectorAll('input[name="is_participant"]');
    const searchSection = document.getElementById('participant_search_section');
    const searchBtn = document.getElementById('search_participant_btn');
    const searchEmail = document.getElementById('search_email');
    const searchResult = document.getElementById('search_result');

    // Form fields
    const fullNameField = document.getElementById('full_name');
    const emailField = document.getElementById('email');
    const institutionField = document.getElementById('institution');
    const addressField = document.getElementById('address');
    const participantIdField = document.getElementById('selected_participant_id');

    // Toggle participant search section
    participantRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            handleParticipantSelection(this);
        });
    });

    function handleParticipantSelection(radio) {
        const isParticipant = radio.value === '1';
        
        if (searchSection) {
            searchSection.style.display = isParticipant ? 'block' : 'none';
        }

        // Clear search results when switching
        if (searchResult) {
            searchResult.innerHTML = '';
        }

        // Handle email field requirements
        if (emailField) {
            if (isParticipant) {
                emailField.removeAttribute('required');
                emailField.closest('.mb-3').querySelector('label').innerHTML = 'Email Address <span class="text-muted">(optional for participants)</span>';
            } else {
                emailField.setAttribute('required', 'required');
                emailField.closest('.mb-3').querySelector('label').innerHTML = 'Email Address <span class="text-danger">*</span>';
            }
        }

        // Clear form when switching types
        clearAuthorForm();
    }

    // Handle participant search
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            performParticipantSearch();
        });
    }

    // Allow search on Enter key
    if (searchEmail) {
        searchEmail.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performParticipantSearch();
            }
        });
    }

    // Handle view author button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-author-btn')) {
            const authorData = JSON.parse(e.target.closest('.view-author-btn').dataset.author);
            showAuthorDetails(authorData);
        }
    });

    function performParticipantSearch() {
        const email = searchEmail.value.trim();
        const programId = document.querySelector('input[name="program_id"]')?.value;

        if (!email) {
            showEnhancedSearchResult('warning', 
                '<strong>Email required</strong><br><span class="small">Please enter an email address to search.</span>', 
                'bx-info-circle');
            return;
        }

        if (!isValidEmail(email)) {
            showEnhancedSearchResult('error', 
                '<strong>Invalid email format</strong><br><span class="small">Please enter a valid email address.</span>', 
                'bx-x-circle');
            return;
        }

        // Show loading state
        const btnText = searchBtn.querySelector('.btn-text');
        const btnSpinner = searchBtn.querySelector('.spinner-border');
        
        searchBtn.disabled = true;
        btnText.textContent = 'Searching...';
        btnSpinner.classList.remove('d-none');

        // Clear previous results
        clearSearchResult();

        // Perform search
        fetch('/participant/search-by-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `email=${encodeURIComponent(email)}&program_id=${encodeURIComponent(programId)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.found) {
                    const participant = data.participant;
                    fillAuthorForm(participant);
                    showEnhancedSearchResult('success',
                        `<strong>Participant found!</strong><br>
                         <span class="small">Details for <strong>${participant.full_name}</strong> have been loaded automatically.</span>`,
                        'bx-check-circle');
                } else {
                    showEnhancedSearchResult('warning',
                        `<strong>No participant found</strong><br>
                         <span class="small">No registered participant found with email: <strong>${email}</strong></span>`,
                        'bx-info-circle');
                    clearAuthorForm();
                }
            } else {
                showEnhancedSearchResult('error',
                    `<strong>Search failed</strong><br>
                     <span class="small">${data.message || 'An error occurred while searching.'}</span>`,
                    'bx-x-circle');
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            showEnhancedSearchResult('error',
                `<strong>Connection error</strong><br>
                 <span class="small">Please check your connection and try again.</span>`,
                'bx-wifi-off');
        })
        .finally(() => {
            // Reset button state
            searchBtn.disabled = false;
            btnText.textContent = 'Search';
            btnSpinner.classList.add('d-none');
        });
    }

    function showEnhancedSearchResult(type, message, icon) {
        const searchResult = document.getElementById('search_result');
        const alertClasses = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };

        const iconClasses = {
            'success': 'text-success',
            'error': 'text-danger',
            'warning': 'text-warning',
            'info': 'text-info'
        };

        searchResult.innerHTML = `
            <div class="alert ${alertClasses[type]} border-0 shadow-sm fade show" role="alert">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-2">
                        <i class="bx ${icon} fs-5 ${iconClasses[type]}"></i>
                    </div>
                    <div class="flex-grow-1">
                        ${message}
                    </div>
                </div>
            </div>
        `;
    }

    function clearSearchResult() {
        const searchResult = document.getElementById('search_result');
        if (searchResult) {
            searchResult.innerHTML = '';
        }
    }

    // Function to show author details in modal
    function showAuthorDetails(author) {
        const modal = document.getElementById('viewAuthorModal');
        if (!modal) return;

        const modalBody = modal.querySelector('.modal-body');
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-12">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                <i class="bx bx-user fs-2"></i>
                            </div>
                        </div>
                        <h5 class="mb-1">${author.full_name}</h5>
                        ${author.is_participant == '1' ? '<span class="badge bg-primary">Primary Author</span>' : 
                          (author.is_presenting == '1' ? '<span class="badge bg-success">Presenting Author</span>' : 
                           '<span class="badge bg-secondary">Co-Author</span>')}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <p class="text-muted">${author.email || 'Not provided'}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Institution</label>
                                <p class="text-muted">${author.institution || 'Not specified'}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Address</label>
                                <p class="text-muted">${author.address || 'Not provided'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper functions
    function fillAuthorForm(participant) {
        if (fullNameField) fullNameField.value = participant.full_name || '';
        if (emailField) emailField.value = participant.email || '';
        if (institutionField) institutionField.value = participant.institution || '';
        if (addressField) addressField.value = participant.address || '';
        if (participantIdField) participantIdField.value = participant.id || '';
    }

    function clearAuthorForm() {
        if (fullNameField) fullNameField.value = '';
        if (emailField) emailField.value = '';
        if (institutionField) institutionField.value = '';
        if (addressField) addressField.value = '';
        if (participantIdField) participantIdField.value = '';
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    function isValidEmail(email) {
        return emailRegex.test(email);
    }
}

function initAuthorTypeCards() {
    // Enhanced author type card selection
    const authorTypeCards = document.querySelectorAll('.author-type-card');
    
    authorTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            authorTypeCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Update radio button
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        });
    });
}

function initEnhancedSearch() {
    // Enhanced search functionality with better UX
    const searchInput = document.getElementById('search_email');
    const searchBtn = document.getElementById('search_participant_btn');
    
    if (searchInput && searchBtn) {
        // Add real-time validation
        searchInput.addEventListener('input', function() {
            const email = this.value.trim();
            const isValid = email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            
            searchBtn.disabled = !isValid;
            
            if (email && !isValid) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
}

// Function to show loading overlay when navigating to edit page
function showLoading(event) {
    // Create and add loading overlay to the body
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loadingOverlay';
    loadingOverlay.style.position = 'fixed';
    loadingOverlay.style.top = '0';
    loadingOverlay.style.left = '0';
    loadingOverlay.style.width = '100%';
    loadingOverlay.style.height = '100%';
    loadingOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    loadingOverlay.style.zIndex = '9999';
    loadingOverlay.style.display = 'flex';
    loadingOverlay.style.justifyContent = 'center';
    loadingOverlay.style.alignItems = 'center';

    // Create spinner
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border text-light';
    spinner.setAttribute('role', 'status');
    spinner.style.width = '3rem';
    spinner.style.height = '3rem';

    // Add spinner to loading overlay
    loadingOverlay.appendChild(spinner);

    // Add loading overlay to body
    document.body.appendChild(loadingOverlay);
}

function showComparisonLoading(event) {
    event.preventDefault();
    const href = event.currentTarget.getAttribute('href');

    Swal.fire({
        title: 'Comparing Versions',
        html: 'Please wait while we analyze the differences...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
            // Navigate to comparison page after showing loading
            window.location.href = href;
        }
    });
}

// Function to switch to the active version under review
function switchToActiveVersion(versionId) {
    if (!versionId) {
        console.error('No version ID provided');
        return;
    }

    // Show the version history modal to view all versions
    const versionHistoryModal = document.getElementById('versionHistoryModal');
    if (versionHistoryModal) {
        // Create Bootstrap modal instance and show it
        const modal = new bootstrap.Modal(versionHistoryModal);
        modal.show();

        // Find and expand the accordion item for the active version
        setTimeout(() => {
            const accordionItems = versionHistoryModal.querySelectorAll('.accordion-item');
            let targetFound = false;

            accordionItems.forEach((item, index) => {
                const itemVersionId = item.getAttribute('data-version-id');
                if (itemVersionId && itemVersionId === versionId) {
                    targetFound = true;

                    // Collapse all other items first
                    accordionItems.forEach(otherItem => {
                        const collapseElement = otherItem.querySelector('.accordion-collapse');
                        const buttonElement = otherItem.querySelector('.accordion-button');
                        if (collapseElement && buttonElement) {
                            collapseElement.classList.remove('show');
                            buttonElement.classList.add('collapsed');
                            buttonElement.setAttribute('aria-expanded', 'false');
                        }
                    });

                    // Expand the target item
                    const targetCollapse = item.querySelector('.accordion-collapse');
                    const targetButton = item.querySelector('.accordion-button');
                    if (targetCollapse && targetButton) {
                        targetCollapse.classList.add('show');
                        targetButton.classList.remove('collapsed');
                        targetButton.setAttribute('aria-expanded', 'true');

                        // Scroll to the active version
                        item.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });

                        // Add a highlight effect
                        item.style.background = '#fff3cd';
                        item.style.transition = 'background-color 0.3s ease';
                        setTimeout(() => {
                            item.style.background = '';
                        }, 3000);
                    }
                }
            });

            if (!targetFound) {
                console.warn('Active version not found in version history');
            }
        }, 300);
    } else {
        console.error('Version history modal not found');
    }
}
</script>
