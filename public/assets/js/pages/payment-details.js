/**
 * Payment Details Page JavaScript
 * Handles client-side functionality for payment details page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(tooltip) {
            return new bootstrap.Tooltip(tooltip);
        });
    }

    // Handle the print detail functionality
    const printDetailButtons = document.querySelectorAll('.btn-print-details');
    if (printDetailButtons) {
        printDetailButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // Add print-specific styling
                document.body.classList.add('printing-payment-details');
                window.print();
                // Remove print-specific styling after printing
                setTimeout(() => document.body.classList.remove('printing-payment-details'), 500);
            });
        });
    }

    // Payment history details toggle (for accessibility)
    const detailsButtons = document.querySelectorAll('[data-bs-toggle="collapse"]');
    if (detailsButtons) {
        detailsButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const expanded = this.getAttribute('aria-expanded') === 'true';
                if (button.classList.contains('btn-outline-secondary')) {
                    if (expanded) {
                        this.innerHTML = '<i class="ri-close-line align-middle"></i> Hide Details';
                    } else {
                        this.innerHTML = '<i class="ri-information-line align-middle"></i> View Details';
                    }
                }
            });
        });
    }
    
    // Animate progress bars on page load
    const animateProgressBars = function() {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(function(bar) {
            const width = bar.getAttribute('aria-valuenow') + '%';
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.transition = 'width 1s ease-in-out';
                bar.style.width = width;
            }, 200);
        });
    };
    
    animateProgressBars();
    
    // Payment Method Selection Highlight
    const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
    if (paymentMethodBtns) {
        paymentMethodBtns.forEach(function(btn) {
            btn.addEventListener('mouseover', function() {
                this.classList.add('border-primary');
            });
            
            btn.addEventListener('mouseout', function() {
                this.classList.remove('border-primary');
            });
        });
    }
    
    // Enable auto refresh on pending payments
    const autoRefreshPaymentStatus = function() {
        // Check if there's a pending payment status
        const pendingStatusElement = document.querySelector('.progress-bar-animated');
        if (pendingStatusElement) {
            // Set a timer to refresh the page after 30 seconds
            setTimeout(() => {
                window.location.reload();
            }, 30000); // 30 seconds
        }
    };
    
    autoRefreshPaymentStatus();
    
    // Add visual feedback when copying payment link
    const paymentLinkInput = document.getElementById('paymentLinkInput');
    if (paymentLinkInput) {
        paymentLinkInput.addEventListener('click', function() {
            this.select();
        });
    }
    
    // Support for mobile device detection and optimization
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        // Adjust UI for mobile devices
        document.body.classList.add('is-mobile-device');
        
        // Make telephone links clickable on mobile
        const phoneLinks = document.querySelectorAll('.phone-link');
        if (phoneLinks) {
            phoneLinks.forEach(function(link) {
                link.setAttribute('href', 'tel:' + link.innerText.trim());
            });
        }
    }
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#' && document.querySelector(targetId)) {
                e.preventDefault();
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add print styles dynamically
    const addPrintStyles = function() {
        const printStyleSheet = document.createElement('style');
        printStyleSheet.id = 'payment-details-print-styles';
        printStyleSheet.innerHTML = `
            @media print {
                body.printing-payment-details .main-content {
                    padding: 0 !important;
                }
                body.printing-payment-details .navbar-header,
                body.printing-payment-details .app-menu,
                body.printing-payment-details .footer,
                body.printing-payment-details .btn-print-details,
                body.printing-payment-details .dropdown,
                body.printing-payment-details button:not(.btn-expand-details) {
                    display: none !important;
                }
                body.printing-payment-details .card {
                    border: 1px solid #dee2e6 !important;
                    box-shadow: none !important;
                }
                body.printing-payment-details .container-fluid {
                    padding: 0 !important;
                }
                body.printing-payment-details .card-body {
                    padding: 1rem !important;
                }
                body.printing-payment-details .collapse {
                    display: block !important;
                }
            }
        `;
        document.head.appendChild(printStyleSheet);
    };
    
    addPrintStyles();
});