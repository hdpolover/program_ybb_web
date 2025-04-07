/**
 * Topbar JavaScript
 * This file handles all the dynamic functionality for the topbar component
 */
document.addEventListener('DOMContentLoaded', function() {
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
    setTimeout(function() {
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
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            
            // Get the program ID from the href attribute
            const href = this.getAttribute('href');
            const programId = href.split('/').pop();
            
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