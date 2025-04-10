/**
 * Topbar JavaScript
 * This file handles all the dynamic functionality for the topbar component
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initialize the topbar
    initTopbar();
});

/**
 * Initialize the topbar functionality
 */
function initTopbar() {
    // Add event listeners for program selection
    setupProgramSelection();

    // Initialize loading overlay if not already present
    initLoadingOverlay();

    // Make sure to hide any loading overlay that might be visible
    setTimeout(hideLoading, 500);
}

/**
 * Create and initialize loading overlay element
 */
function initLoadingOverlay() {
    // Check if loading overlay already exists
    if (!document.getElementById('program-loading-overlay')) {
        // Create loading overlay
        const overlay = document.createElement('div');
        overlay.id = 'program-loading-overlay';
        overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center';
        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        overlay.style.zIndex = '9999';
        overlay.style.display = 'none';

        // Create spinner
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border text-light';
        spinner.setAttribute('role', 'status');

        // Add text
        const spinnerText = document.createElement('span');
        spinnerText.className = 'ms-2 text-light';
        spinnerText.textContent = 'Updating program data...';

        // Create container for spinner and text
        const spinnerContainer = document.createElement('div');
        spinnerContainer.className = 'd-flex align-items-center';
        spinnerContainer.appendChild(spinner);
        spinnerContainer.appendChild(spinnerText);

        overlay.appendChild(spinnerContainer);
        document.body.appendChild(overlay);
    }
}

/**
 * Show loading overlay
 */
function showLoading() {
    // Use the loading-manager if available, otherwise fall back to direct manipulation
    if (typeof showLoadingOverlay === 'function') {
        showLoadingOverlay('program-loading-overlay');
    } else {
        const overlay = document.getElementById('program-loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
            overlay.style.opacity = '1';
            overlay.style.visibility = 'visible';
        }
    }

    // Set a safety timeout to hide the overlay after 10 seconds if something goes wrong
    setTimeout(function () {
        hideLoading();
    }, 10000);
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    // Use the loading-manager if available, otherwise fall back to direct manipulation
    if (typeof hideLoadingOverlay === 'function') {
        hideLoadingOverlay('program-loading-overlay');
    } else {
        const overlay = document.getElementById('program-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
        }
    }

    // Also try to hide any other loading elements that might be present
    if (typeof hideAllLoadingOverlays === 'function') {
        hideAllLoadingOverlays();
    }
}

/**
 * Set up program selection functionality
 */
function setupProgramSelection() {
    // Get all program selection links
    const programLinks = document.querySelectorAll('.dropdown-programs-container .dropdown-item');

    // Add click event listener to each program link
    programLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default link behavior

            // Get the program ID from the href attribute
            const href = this.getAttribute('href');
            const programId = href.split('/').pop();            // Check if user is registered for this program
            const isRegistered = this.getAttribute('data-registered') === '1';

            if (!isRegistered) {
                // Get program details for better information display
                const programName = this.querySelector('.fw-medium').textContent;
                const programStatus = this.querySelector('.badge.bg-success-subtle, .badge.bg-danger-subtle')?.textContent || 'Active';
                const programDates = this.querySelector('.badge.bg-light-subtle')?.textContent || '';

                // If SweetAlert2 is available, use it for confirmation with detailed info
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Not Registered',
                        html: `<div class="text-start">
                                <p>You are not registered for this program:</p>
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><strong>Program:</strong> ${programName}</li>
                                    <li class="list-group-item"><strong>Status:</strong> ${programStatus}</li>
                                    ${programDates ? `<li class="list-group-item"><strong>Timeline:</strong> ${programDates}</li>` : ''}
                                </ul>
                                <p>Would you like to register now?</p>
                               </div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Register Me',
                        cancelButtonText: 'No, Cancel',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Register for program via API instead of redirecting
                            registerForProgram(programId, programName);
                        }
                    });
                } else {
                    // Fallback to regular confirm
                    if (confirm(`You are not registered for "${programName}". Would you like to register now?`)) {
                        // Register for program via API instead of redirecting
                        registerForProgram(programId, programName);
                    }
                }
                return;
            }

            // Show loading overlay immediately before any processing
            showLoading();

            // Update program via AJAX
            updateProgramViaAjax(programId);
        });
    });
}

/**
 * Update the program via AJAX
 */
function updateProgramViaAjax(programId) {
    // Create an AJAX request
    fetch(`/topbar/setProgram/${programId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ program_id: programId })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update the UI with new program data
                updateTopbarUI(programId);

                // Get current URL to reload the exact same page
                const currentUrl = window.location.href;

                // Add a cache-busting parameter to force a complete reload
                const timestamp = new Date().getTime();
                const separator = currentUrl.indexOf('?') !== -1 ? '&' : '?';
                const newUrl = currentUrl + separator + '_reload=' + timestamp;

                try {
                    // Make sure to clear the loading overlay just in case
                    hideLoading();

                    // Navigate to the new URL which will force a complete reload
                    window.location.href = newUrl;
                } catch (error) {
                    console.error('Error during page navigation:', error);
                    hideLoading();

                    // Fallback - if navigation fails, try a simple reload
                    window.location.reload();
                }
            } else {
                throw new Error(data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error updating program:', error);
            // Display error message to user
            showErrorNotification('Failed to update program. Please try again.');
            // Hide loading overlay on error
            hideLoading();
        });
}

/**
 * Update the topbar UI with new data
 */
function updateTopbarUI(programId) {
    // Find selected program data
    const selectedItem = document.querySelector(`.dropdown-programs-container .dropdown-item[href$="/${programId}"]`);

    if (selectedItem) {
        // Update program name in dropdown button
        const programNameInButton = document.querySelector('#program-dropdown .fw-medium');
        const programNameInDropdown = selectedItem.querySelector('.fw-medium');

        if (programNameInButton && programNameInDropdown) {
            programNameInButton.textContent = programNameInDropdown.textContent;
        }

        // Update active state in dropdown menu
        document.querySelectorAll('.dropdown-programs-container .dropdown-item').forEach(item => {
            // Remove active class from all items
            item.classList.remove('active');

            // Remove checkmark icon if it exists
            const checkIcon = item.querySelector('.ri-checkbox-circle-fill');
            if (checkIcon) {
                checkIcon.remove();
            }
        });

        // Add active class to selected item
        selectedItem.classList.add('active');

        // Add checkmark icon to selected item
        const checkIcon = document.createElement('i');
        checkIcon.className = 'ri-checkbox-circle-fill text-success ms-2 fs-17';
        selectedItem.appendChild(checkIcon);
    }
}

/**
 * Show error notification to user
 */
function showErrorNotification(message) {
    // Check if Toastr is available (common notification library)
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        // Fallback to alert
        alert(message);
    }
}

/**
 * Register a user for a program via API
 * 
 * @param {string} programId The ID of the program to register for
 * @param {string} programName The name of the program (for display purposes)
 */
function registerForProgram(programId, programName) {
    // Show loading overlay
    showLoading();    // Get current user data from session
    const userId = getUserIdFromSession();
    if (!userId) {
        // Handle error - user ID not available
        hideLoading();

        // Show more detailed error message with solution
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'User ID Not Found',
                html: `<p>We couldn't retrieve your user ID which is needed for registration.</p>
                       <p>This could be due to your session timing out or a temporary issue.</p>
                       <p>Please try refreshing the page. If the issue persists, please sign out and sign in again.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Refresh Page',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Refresh the page
                    window.location.reload();
                }
            });
        } else {
            showErrorNotification('User data not available. Please refresh the page or sign in again.');
        }
        return;
    }

    // Get user's full name if available from other participants data
    let fullName = '';
    try {
        // Try to get the user's name from any existing participant registrations
        const participantsElements = document.querySelectorAll('.dropdown-programs-container .dropdown-item[data-registered="1"]');
        if (participantsElements.length > 0) {
            // There's at least one existing registration, use that name
            const userData = JSON.parse(localStorage.getItem('userData') || '{}');
            fullName = userData.fullName || '';

            if (!fullName) {
                // Try to get name from session user data
                const sessionUser = JSON.parse(sessionStorage.getItem('user') || '{}');
                fullName = sessionUser.fullName || sessionUser.full_name || sessionUser.name || '';
            }
        }
    } catch (err) {
        console.error('Error getting user name:', err);
        // Continue with empty name - API should handle this
    }

    // Prepare data for API request
    const requestData = {
        program_id: programId,
        full_name: fullName
    };

    // Call the API endpoint to register for the program
    fetch(`/topbar/${userId}/create`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify(requestData)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
    .then(data => {
        hideLoading();
        if (data.success) {
            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Registration Successful!',
                    html: `<p>You have been successfully registered for <strong>${programName}</strong>.</p>
                           <p>We'll switch you to this program now.</p>`,
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                }).then(() => {
                    // Switch to the newly registered program
                    switchToProgram(programId, programName);
                });
            } else {
                alert(`Successfully registered for ${programName}. Switching to this program now.`);
                switchToProgram(programId, programName);
            }
        } else {
            throw new Error(data.message || 'Unknown error occurred during registration');
        }
        })
        .catch(error => {
            console.error('Error registering for program:', error);
            hideLoading();

            // Display error message to user
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Registration Failed',
                    text: error.message || 'There was a problem registering for this program. Please try again later.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            } else {
                showErrorNotification(error.message || 'Failed to register for program. Please try again.');
            }
        });
}

/**
 * Get the current user ID from session
 * 
 * @returns {string|null} The user ID, or null if not found
 */
function getUserIdFromSession() {
    try {
        // First try to get user ID from the profile dropdown button
        const profileButton = document.querySelector('#page-header-user-dropdown');
        if (profileButton && profileButton.dataset.userId) {
            return profileButton.dataset.userId;
        }

        // If we got here, try to find user ID from any other elements with data-user-id
        const userIdElements = document.querySelectorAll('[data-user-id]');
        for (let element of userIdElements) {
            if (element.dataset.userId) {
                return element.dataset.userId;
            }
        }

        // Try a different approach using the displayed user name to find the corresponding ID
        // This is useful if the user data is available but not directly exposed
        // Directly check in PHP session by making an AJAX call
        const userId = fetchUserIdSynchronously();
        if (userId) {
            return userId;
        }

        return null;
    } catch (err) {
        console.error('Error getting user ID from session:', err);
        return null;
    }
}

/**
 * Make a synchronous request to get the user ID from the server
 * 
 * @returns {string|null} The user ID
 */
function fetchUserIdSynchronously() {
    try {
        // Make a synchronous request to get the user ID
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/api/user/current', false); // Synchronous request
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send();

        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response && response.success && response.user && response.user.id) {
                // Cache the user ID in localStorage for future use
                try {
                    localStorage.setItem('cachedUserId', response.user.id);
                } catch (e) {
                    // Ignore storage errors
                }
                return response.user.id;
            }
        }

        // Try cached user ID as last resort
        return localStorage.getItem('cachedUserId') || null;
    } catch (e) {
        console.error('Error fetching user ID synchronously:', e);
        return null;
    }
}

/**
 * Switch to a specific program after registration
 * 
 * @param {string} programId The ID of the program to switch to
 * @param {string} programName The name of the program (for display purposes)
 */
function switchToProgram(programId, programName) {
    // Show loading overlay
    showLoading();
    
    // Call the API to set the program
    fetch(`/topbar/setProgram/${programId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ program_id: programId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update the dropdown menu to reflect the new program
            updateProgramDropdownUI(programId, programName);
            
            // Get current URL to reload with the new program context
            const currentUrl = window.location.href;
            
            // Add cache-busting parameter to force a complete reload with new program context
            const timestamp = new Date().getTime();
            const separator = currentUrl.indexOf('?') !== -1 ? '&' : '?';
            const newUrl = currentUrl + separator + '_reload=' + timestamp;
            
            // Redirect to refresh the page with the new program context
            window.location.href = newUrl;
        } else {
            throw new Error(data.message || 'Failed to switch program');
        }
    })
    .catch(error => {
        console.error('Error switching program:', error);
        hideLoading();
        
        // Display error message but still try to reload the page
        showErrorNotification('Error switching to new program. Refreshing page...');
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
}

/**
 * Update the program dropdown UI to reflect the newly selected program
 * 
 * @param {string} programId The ID of the program to mark as selected
 * @param {string} programName The name of the program to display
 */
function updateProgramDropdownUI(programId, programName) {
    // Find the dropdown item for this program
    const programItem = document.querySelector(`.dropdown-programs-container .dropdown-item[data-program-id="${programId}"]`);
    
    if (programItem) {
        // Update the registered status
        programItem.setAttribute('data-registered', '1');
        
        // Remove any "Not Registered" badge if it exists
        const notRegisteredBadge = programItem.querySelector('.badge.bg-warning-subtle.text-warning');
        if (notRegisteredBadge) {
            notRegisteredBadge.remove();
        }
        
        // Update active state in dropdown menu
        document.querySelectorAll('.dropdown-programs-container .dropdown-item').forEach(item => {
            // Remove active class from all items
            item.classList.remove('active');
            
            // Remove checkmark icon if it exists
            const checkIcon = item.querySelector('.ri-checkbox-circle-fill');
            if (checkIcon) {
                checkIcon.remove();
            }
        });
        
        // Add active class to the newly registered program item
        programItem.classList.add('active');
        
        // Add checkmark icon to selected item
        const checkIcon = document.createElement('i');
        checkIcon.className = 'ri-checkbox-circle-fill text-success ms-2 fs-17';
        programItem.appendChild(checkIcon);
        
        // Update program name in dropdown button
        const programNameInButton = document.querySelector('#program-dropdown .fw-medium');
        if (programNameInButton) {
            programNameInButton.textContent = programName;
        }
    }
}