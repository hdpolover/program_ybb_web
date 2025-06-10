/**
 * Loading Manager JavaScript
 * This file handles loading overlays across the application
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the loading manager
    initLoadingManager();
});

/**
 * Initialize the loading manager functionality
 */
function initLoadingManager() {
    // Hide any loading overlays that might be active
    hideAllLoadingOverlays();

    // Add a listener to hide any overlay when the page has finished loading
    window.addEventListener('load', hideAllLoadingOverlays);
    
    // Add listener for page visibility changes to handle stuck loading states
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // Page became visible again, check for stuck loading states
            setTimeout(hideAllLoadingOverlays, 1000);
        }
    });

    // Add keyboard shortcut (Escape key) to manually close stuck loading dialogs
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && typeof Swal !== 'undefined' && Swal.isVisible()) {
            const currentTitle = document.querySelector('.swal2-title');
            if (currentTitle && (
                currentTitle.textContent.includes('Loading') || 
                currentTitle.textContent.includes('Comparing') ||
                currentTitle.textContent.includes('Preparing')
            )) {
                console.log('User pressed Escape - closing stuck loading dialog');
                Swal.close();
            }
        }
    });
}

/**
 * Hide all loading overlays in the application
 */
function hideAllLoadingOverlays() {
    // First try to find by ID (most specific approach)
    const specificOverlays = ['program-loading-overlay', 'loading-overlay', 'preloader'];
    specificOverlays.forEach(id => {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.style.display = 'none';
            overlay.style.opacity = '0';
            overlay.style.visibility = 'hidden';
        }
    });

    // Find and hide common loading overlays by class or attribute
    const overlaySelectors = [
        '.loading-overlay', 
        '#loading-overlay', 
        '#program-loading-overlay', 
        '[id$="-loading-overlay"]',
        '[class*="loading"]',
        '[class*="preloader"]',
        '.preloader',
        '#preloader',
        '.spinner-overlay',
        '.overlay-loading'
    ];
    
    overlaySelectors.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(function(element) {
            if (element) {
                element.style.display = 'none';
                element.style.opacity = '0';
                element.style.visibility = 'hidden';
            }
        });
    });

    // Also check for any elements with 'loading' in their class
    const loadingElements = document.querySelectorAll('[class*="loading"], [class*="preloader"], .spinner-border');
    loadingElements.forEach(function(element) {
        if (element.classList.contains('d-flex') || 
            element.style.display === 'flex' || 
            element.style.display === 'block') {
            element.style.display = 'none';
            element.style.opacity = '0';
            element.style.visibility = 'hidden';
        }
    });

    // Clean up any possible overlay added through topbar.js 
    document.querySelectorAll('.position-fixed.top-0.start-0.w-100.h-100').forEach(el => {
        if (el.classList.contains('d-flex') || 
            el.style.display === 'flex' || 
            el.style.display === 'block' ||
            el.style.zIndex === '9999') {
            el.style.display = 'none';
            el.style.opacity = '0';
            el.style.visibility = 'hidden';
        }
    });

    // Also close any SweetAlert loading dialogs that might be stuck
    if (typeof Swal !== 'undefined' && Swal.isVisible()) {
        const currentTitle = document.querySelector('.swal2-title');
        if (currentTitle && (
            currentTitle.textContent.includes('Loading') || 
            currentTitle.textContent.includes('Comparing') ||
            currentTitle.textContent.includes('Preparing')
        )) {
            console.log('Closing stuck SweetAlert loading dialog');
            Swal.close();
        }
    }
}

/**
 * Show a specific loading overlay by ID
 * @param {string} overlayId - The ID of the overlay to show
 */
function showLoadingOverlay(overlayId) {
    const overlay = document.getElementById(overlayId);
    if (overlay) {
        overlay.style.display = 'flex';
        overlay.style.opacity = '1';
        overlay.style.visibility = 'visible';
    }
}

/**
 * Hide a specific loading overlay by ID
 * @param {string} overlayId - The ID of the overlay to hide
 */
function hideLoadingOverlay(overlayId) {
    const overlay = document.getElementById(overlayId);
    if (overlay) {
        overlay.style.display = 'none';
        overlay.style.opacity = '0';
        overlay.style.visibility = 'hidden';
    }
}