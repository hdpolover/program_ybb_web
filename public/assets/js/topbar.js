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
            // Optionally prevent default if you want to handle via AJAX instead of full page reload
            // e.preventDefault();
            
            // Get the program ID from the href attribute
            const href = this.getAttribute('href');
            const programId = href.split('/').pop();
            
            // You could use AJAX to update the program here if needed
            // For example:
            // updateProgramViaAjax(programId);
            
            // For now, we'll just let the default link behavior redirect
        });
    });
}

/**
 * Update the program via AJAX (alternative to page reload)
 * Uncomment and use this if you prefer dynamic updates
 */
/*
function updateProgramViaAjax(programId) {
    // Create an AJAX request
    fetch(`/topbar/setProgram/${programId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ program_id: programId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI with new program data
            updateTopbarUI(data);
        }
    })
    .catch(error => {
        console.error('Error updating program:', error);
    });
}

/**
 * Update the topbar UI with new data
 */
/*
function updateTopbarUI(data) {
    // Update program name in dropdown button
    const programName = document.querySelector('#program-dropdown .fw-medium');
    if (programName) {
        programName.textContent = data.currentProgram.name;
    }
    
    // Update program logo if it exists
    const programLogo = document.querySelector('#program-dropdown img');
    if (programLogo && data.currentProgram.logo_url) {
        programLogo.src = data.currentProgram.logo_url;
    }
    
    // Update active state in dropdown menu
    document.querySelectorAll('.dropdown-programs-container .dropdown-item').forEach(item => {
        const itemProgramId = item.getAttribute('href').split('/').pop();
        
        // Remove active class from all items
        item.classList.remove('active');
        
        // Remove checkmark icon
        const checkIcon = item.querySelector('.ri-checkbox-circle-fill');
        if (checkIcon) {
            checkIcon.remove();
        }
        
        // Add active class and checkmark to the selected item
        if (itemProgramId === data.currentProgramId.toString()) {
            item.classList.add('active');
            
            // Add checkmark icon
            const flexDiv = item.querySelector('.d-flex.align-items-center.flex-grow-1');
            if (flexDiv) {
                const icon = document.createElement('i');
                icon.className = 'ri-checkbox-circle-fill text-success ms-2 fs-17';
                item.appendChild(icon);
            }
        }
    });
    
    // Optionally reload the page content if needed
    // window.location.reload();
}
*/