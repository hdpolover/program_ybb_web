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
                .then(response => {
                    console.log('Response received, status:', response.status);
                    return response.json();
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
                    Swal.fire({
                        title: 'Payment Error',
                        text: 'Failed to connect to payment gateway. Please try again.',
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
                        <p class="mb-0">Please complete the payment process in the gateway tab.</p>
                        <p class="text-muted small mt-3">You can safely close this message and check your payment status later.</p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Check Payment Status',
                cancelButtonText: 'Close',
                allowOutsideClick: true,
                didOpen: () => {
                    // Only open the redirect URL after Swal is shown, and only once                   
                    if (!hasOpenedRedirectTab && redirectUrl) {
                        setTimeout(() => {
                            console.log('Opening new tab with URL:', redirectUrl);
                            try {
                                const newTab = window.open(redirectUrl, '_blank', 'noopener');
                                hasOpenedRedirectTab = true;                                // Don't show the popup blocked message at all
                                // Even if window.open() returns null, modern browsers often
                                // still open the tab but don't return a reference to it
                                // So we'll just assume the tab opened successfully and continue                                // Don't show fallback at all - the tab has very likely opened successfully
                                // Modern browsers either open the tab or they completely block it (showing their own UI)
                                // Just log that we attempted to open the tab
                                console.log('Payment gateway tab should be open now');
                            } catch (e) {
                                console.error('Error opening payment gateway tab:', e);
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
                }
            });
        }

        console.log('Payment Gateway Handler setup completed');
    });
})();
