/**
 * Payment Gateway Handler
 * Handles the payment gateway process to prevent duplicate submissions
 * and properly open the gateway in a new tab
 */

// Use an immediately-invoked function expression (IIFE) to avoid global scope pollution
(function () {
    // Track if we've already set up event handlers on this page
    // This prevents duplicate handlers on the same elements
    if (window.gatewayHandlerInitialized) {
        console.log('Payment gateway handler already initialized, skipping');
        return;
    }

    document.addEventListener('DOMContentLoaded', function () {
        console.log('Payment Gateway Handler initialized');

        // Mark that the gateway handler is loaded and initialized
        window.gatewayHandlerLoaded = true;
        window.gatewayHandlerInitialized = true;

        // Payment form submission handler
        const paymentForm = document.getElementById('paymentForm');
        if (!paymentForm) {
            console.error('Payment form not found');
            return;
        }

        // Track if payment is in progress to prevent duplicates
        let isPaymentInProgress = false;
        // Track if we've already opened a redirect tab
        let hasOpenedRedirectTab = false;

        // Track if this submission has been handled by this script
        paymentForm.addEventListener('submit', function (event) {
            console.log('Form submitted');
            const paymentType = document.getElementById('payment_type')?.value;
            console.log('Payment type at submission:', paymentType);

            // Only intercept gateway payments - let manual payments use traditional form submission
            if (paymentType !== 'gateway') {
                console.log('Not a gateway payment, letting form submit normally');
                return; // Let the form submit normally
            }

            // Stop the form from submitting normally
            console.log('Intercepting gateway payment submission');
            event.preventDefault();
            event.stopImmediatePropagation(); // This prevents other handlers from running

            // Prevent multiple submissions
            if (isPaymentInProgress) {
                console.log('Payment already in progress, ignoring duplicate submission');
                return;
            }

            // Reset the redirect tab tracker for this new submission
            hasOpenedRedirectTab = false;

            // Skip form validation for gateway payments since we only need minimal fields
            // We just need the payment_method_id for gateway payments
            console.log('Skipping detailed validation for gateway payment');

            // Check if payment method ID is set
            const paymentMethodId = document.getElementById('payment_method_id').value;
            console.log('Payment method ID:', paymentMethodId);

            if (!paymentMethodId) {
                console.error('Payment method ID is not set');
                Swal.fire({
                    title: 'Payment Error',
                    text: 'Payment method is not selected. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Set flag to prevent duplicate submissions
            isPaymentInProgress = true;
            console.log('Setting payment in progress flag');

            // Close the modal
            const paymentModal = bootstrap.Modal.getInstance(document.getElementById('makePaymentModal'));
            if (paymentModal) {
                console.log('Closing payment modal');
                paymentModal.hide();
            }

            // Show processing message
            console.log('Showing processing message');
            Swal.fire({
                title: 'Processing Payment',
                html: 'Please wait while we connect to the payment gateway...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create a transaction ID to track this specific payment attempt
            const transactionId = 'tx_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            console.log('Generated transaction ID:', transactionId);

            // Get form data
            const formData = new FormData(paymentForm);
            formData.append('client_transaction_id', transactionId); // Add the transaction ID
            console.log('Form action URL:', paymentForm.action);

            // For debugging, log all form data
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Send form data via AJAX
            console.log('Sending AJAX request');
            fetch(paymentForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Transaction-ID': transactionId // Add as header too for backup
                }
            })
                .then(async response => {
                    console.log('Response received, status:', response.status);

                    const contentType = response.headers.get('content-type') || '';
                    let responsePayload = null;

                    if (contentType.includes('application/json')) {
                        responsePayload = await response.json();
                    } else {
                        const text = await response.text();
                        if (!response.ok) {
                            console.error('Non-JSON error response received:', text.substring(0, 200) + '...');
                            const nonJsonError = new Error(`HTTP ${response.status}: ${response.statusText}`);
                            nonJsonError.status = response.status;
                            nonJsonError.serverMessage = 'Server returned a non-JSON error response.';
                            throw nonJsonError;
                        }

                        console.error('Non-JSON success response received:', text.substring(0, 200) + '...');
                        throw new Error('Server returned non-JSON response (possibly an error page)');
                    }

                    if (!response.ok) {
                        console.error('HTTP Error:', response.status, response.statusText, responsePayload);
                        const httpError = new Error(`HTTP ${response.status}: ${response.statusText}`);
                        httpError.status = response.status;
                        httpError.serverMessage = responsePayload?.message || null;
                        httpError.responsePayload = responsePayload;
                        throw httpError;
                    }

                    return responsePayload;
                })
                .then(data => {
                    console.log('Payment gateway response:', data);
                    isPaymentInProgress = false; // Reset flag                   
                    if (data.status === 'success' && data.redirect_url) {
                        console.log('Success with redirect URL:', data.redirect_url);

                        // Get program payment ID either from the response or from the form
                        const programPaymentId = data.program_payment_id || document.getElementById('program_payment_id')?.value;
                        console.log('Using program payment ID for redirect:', programPaymentId);

                        // First show payment in progress message and only then open the redirect URL
                        showPaymentInProgressMessage(programPaymentId, data.redirect_url);
                    } else if (data.status === 'success' && data.payment_id) {
                        console.log('Success with payment ID but no redirect URL');
                        // Payment initiated but no redirect URL
                        Swal.fire({
                            title: 'Payment Initiated',
                            text: 'Your payment has been initiated. Please check payment status.',
                            icon: 'success',
                            confirmButtonText: 'Check Status',
                        }).then(() => {
                            window.location.href = window.location.origin + '/payments/detail/' +
                                (data.program_payment_id || document.getElementById('program_payment_id').value);
                        });
                    } else {
                        console.log('Payment error or unknown response structure');
                        // Error handling
                        Swal.fire({
                            title: 'Payment Error',
                            text: data.message || 'There was an error processing your payment. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error processing payment request:', error);
                    isPaymentInProgress = false;
                    
                    let errorMessage = 'Failed to connect to payment gateway. Please try again.';
                    
                    if (error.serverMessage) {
                        errorMessage = error.serverMessage;
                    }

                    // Provide more specific error messages based on the error type
                    if (!error.serverMessage && error.message.includes('HTTP 400')) {
                        errorMessage = 'Invalid payment data. Please check your information and try again.';
                    } else if (!error.serverMessage && error.message.includes('HTTP 401')) {
                        errorMessage = 'Authentication error. Please refresh the page and try again.';
                    } else if (!error.serverMessage && error.message.includes('HTTP 403')) {
                        errorMessage = 'Access denied. Please contact support.';
                    } else if (!error.serverMessage && error.message.includes('HTTP 404')) {
                        errorMessage = 'Payment service not found. Please contact support.';
                    } else if (!error.serverMessage && error.message.includes('HTTP 500')) {
                        errorMessage = 'Server error. Please try again in a few minutes or contact support.';
                    } else if (error.message.includes('non-JSON response')) {
                        errorMessage = 'Server configuration error. Please contact support with error code: JSON_ERROR';
                    } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                        errorMessage = 'Network connection error. Please check your internet connection and try again.';
                    }
                    
                    Swal.fire({
                        title: 'Payment Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }, true); // Use capturing phase to ensure this handler runs first

        // Function to show payment in progress message and then open the redirect URL
        function showPaymentInProgressMessage(programPaymentId, redirectUrl) {
            console.log('Showing payment in progress message, ID:', programPaymentId);

            // Ensure programPaymentId is not undefined
            const paymentId = programPaymentId || document.getElementById('program_payment_id')?.value || '';
            console.log('Using payment ID for status check:', paymentId);

            // Store redirectUrl in localStorage to prevent duplicate tabs
            const tabOpenKey = 'payment_tab_' + paymentId;
            const redirectTimestamp = Date.now();

            // Check if we've already opened this URL recently (within last 10 seconds)
            const lastOpen = localStorage.getItem(tabOpenKey);
            if (lastOpen && (redirectTimestamp - parseInt(lastOpen)) < 10000) {
                console.log('Preventing duplicate tab open - already opened within 10 seconds');
                hasOpenedRedirectTab = true;
            } else {
                // Mark this URL as opened with current timestamp
                localStorage.setItem(tabOpenKey, redirectTimestamp.toString());
            }

            // First, show the payment in progress message
            Swal.fire({
                title: 'Payment In Progress',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                                     role="progressbar" style="width: 100%" aria-valuenow="100" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <p>Your payment is being processed in the other tab.</p>
                        <p class="mb-0">Please complete the payment process in the gateway tab.</p>                        <div id="payment-link-container" class="mt-3 d-none">
                            <div class="alert alert-warning">
                                <p><strong>Payment tab not opening?</strong></p>
                                <p><a href="${redirectUrl}" target="_blank" class="btn btn-sm btn-primary mt-2">Click here to open payment gateway</a></p>
                                <p class="mt-2">
                                    <button class="btn btn-sm btn-outline-secondary copy-link-btn" 
                                            data-url="${redirectUrl}" 
                                            onclick="copyPaymentLink(this)">
                                        <i class="bi bi-clipboard"></i> Copy payment link
                                    </button>
                                    <span class="copy-success-message d-none text-success ms-2" style="font-size: 0.9em;">
                                        <i class="bi bi-check-circle"></i> Link copied!
                                    </span>
                                </p>
                                <p class="text-muted small mt-2">For the best experience, we recommend using Google Chrome browser.</p>
                            </div>
                        </div>
                        <p class="text-muted small mt-3">You can safely close this message and check your payment status later.</p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Check Payment Status',
                cancelButtonText: 'Close',
                allowOutsideClick: true, didOpen: () => {
                    // Only open the redirect URL after Swal is shown, and only once                   
                    if (!hasOpenedRedirectTab && redirectUrl) {
                        setTimeout(() => {
                            console.log('Opening new tab with URL:', redirectUrl);
                            try {
                                const newTab = window.open(redirectUrl, '_blank', 'noopener');
                                hasOpenedRedirectTab = true;

                                // Even if window.open() returns null, modern browsers often
                                // still open the tab but don't return a reference to it
                                console.log('Payment gateway tab should be open now');

                                // Show the manual payment link after a short delay in case the tab didn't open
                                setTimeout(() => {
                                    // Show the payment link container
                                    const linkContainer = document.getElementById('payment-link-container');
                                    if (linkContainer) {
                                        linkContainer.classList.remove('d-none');
                                        console.log('Showing manual payment link as fallback');
                                    }
                                }, 2000); // Show the link after 2 seconds to give time for the tab to open
                            } catch (e) {
                                console.error('Error opening payment gateway tab:', e);
                                // Show the payment link immediately if there's an error
                                const linkContainer = document.getElementById('payment-link-container');
                                if (linkContainer) {
                                    linkContainer.classList.remove('d-none');
                                    console.log('Showing manual payment link due to error');
                                }
                            }
                        }, 500);
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('User clicked to check payment status');
                    // Use the same fallback mechanism for the redirect
                    const paymentIdForRedirect = paymentId || document.getElementById('program_payment_id')?.value || '';
                    if (paymentIdForRedirect) {
                        window.location.href = window.location.origin + '/payments/detail/' + paymentIdForRedirect;
                    } else {
                        console.error('No payment ID available for status check redirect');
                        Swal.fire({
                            title: 'Error',
                            text: 'Unable to check payment status. Please go to the Payments page manually.',
                            icon: 'error'
                        });
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // If user clicked the "Close" button, reload the page to refresh the state
                    console.log('User clicked Close, reloading the page');
                    window.location.reload();
                }
            });
        }        console.log('Payment Gateway Handler setup completed');
        
        // Define copy payment link function in global scope so it can be called from inline onclick
        window.copyPaymentLink = function(element) {
            const url = element.getAttribute('data-url');
            if (!url) {
                console.error('No URL to copy');
                return;
            }
            
            // Create a temporary textarea element to copy text to clipboard
            const textarea = document.createElement('textarea');
            textarea.value = url;
            textarea.style.position = 'fixed'; // Prevent scrolling to the element
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            
            try {
                // Execute copy command
                const successful = document.execCommand('copy');
                if (successful) {
                    console.log('Payment URL copied to clipboard');
                    
                    // Show success message
                    const successMsg = element.parentNode.querySelector('.copy-success-message');
                    if (successMsg) {
                        successMsg.classList.remove('d-none');
                        // Hide the message after 3 seconds
                        setTimeout(() => {
                            successMsg.classList.add('d-none');
                        }, 3000);
                    }
                } else {
                    console.error('Copy command failed');
                }
            } catch (err) {
                console.error('Error copying URL to clipboard:', err);
                
                // Fallback for modern browsers
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(url).then(() => {
                        console.log('Payment URL copied to clipboard using Clipboard API');
                        
                        // Show success message
                        const successMsg = element.parentNode.querySelector('.copy-success-message');
                        if (successMsg) {
                            successMsg.classList.remove('d-none');
                            // Hide the message after 3 seconds
                            setTimeout(() => {
                                successMsg.classList.add('d-none');
                            }, 3000);
                        }
                    });
                }
            }
            
            // Remove the temporary textarea
            document.body.removeChild(textarea);
        };
    });
})();
