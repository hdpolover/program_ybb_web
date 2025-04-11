/**
 * Payment Gateway Handler
 * Handles the payment gateway process to prevent duplicate submissions
 * and properly open the gateway in a new tab
 */

document.addEventListener('DOMContentLoaded', function () {
    // Payment form submission handler
    const paymentForm = document.getElementById('paymentForm');
    if (!paymentForm) return;

    // Track if payment is in progress to prevent duplicates
    let isPaymentInProgress = false;

    paymentForm.addEventListener('submit', function (event) {
        const paymentType = document.getElementById('payment_type').value;

        // Only intercept gateway payments - let manual payments use traditional form submission
        if (paymentType !== 'gateway') {
            return; // Let the form submit normally
        }

        event.preventDefault(); // Prevent default form submission

        // Prevent multiple submissions
        if (isPaymentInProgress) {
            console.log('Payment already in progress, ignoring duplicate submission');
            return;
        }

        // Validate fields if needed
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }

        // Set flag to prevent duplicate submissions
        isPaymentInProgress = true;

        // Close the modal
        const paymentModal = bootstrap.Modal.getInstance(document.getElementById('makePaymentModal'));
        if (paymentModal) {
            paymentModal.hide();
        }

        // Show processing message
        const loadingSwal = Swal.fire({
            title: 'Processing Payment',
            html: 'Please wait while we connect to the payment gateway...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Get form data
        const formData = new FormData(this);

        // Send form data via AJAX
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .catch(error => {
                console.error('Error processing payment request:', error);
                return {
                    status: 'error',
                    message: 'Failed to connect to payment gateway. Please try again.'
                };
            })
            .then(data => {
                console.log('Payment gateway response:', data); isPaymentInProgress = false; // Reset flag

                if (data.status === 'success' && data.redirect_url) {
                    // Create a temporary anchor element to ensure new tab opening
                    const link = document.createElement('a');
                    link.href = data.redirect_url;
                    link.target = '_blank'; // Open in new tab
                    link.rel = 'noopener noreferrer'; // Security best practice

                    // Must be added to document for Firefox compatibility
                    document.body.appendChild(link);

                    // Click the link programmatically - this is more reliable for opening in new tab
                    link.click();

                    // Clean up the DOM
                    document.body.removeChild(link);

                    // Always show payment in progress message - don't rely on checking if new tab opened
                    showPaymentInProgressMessage(data.program_payment_id);

                    // Also provide a fallback in case the link approach didn't work
                    setTimeout(() => {
                        // Pop-up was likely blocked, show instructions to user
                        Swal.fire({
                            title: 'Payment Gateway Ready',
                            html: `
                            <p>The payment gateway is ready, but pop-ups may be blocked by your browser.</p>
                            <p class="mt-3">Please click the button below to open the payment gateway in a new tab.</p>
                            <p class="small text-muted mt-4">Do not close this window. You will be redirected back here when payment is complete.</p>
                        `,
                            icon: 'info',
                            showConfirmButton: true,
                            confirmButtonText: 'Open Payment Gateway',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel',
                            allowOutsideClick: true,
                            allowEscapeKey: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Try to open the payment gateway again when user clicks button
                                window.open(data.redirect_url, '_blank');

                                // Show payment in progress message
                                showPaymentInProgressMessage(data.program_payment_id);
                            }
                        });
                    });
                } else if (data.status === 'success' && data.payment_id) {
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
                    // Error handling
                    Swal.fire({
                        title: 'Payment Error',
                        text: data.message || 'There was an error processing your payment. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
    });

    // Function to show payment in progress message after opening gateway in new tab
    function showPaymentInProgressMessage(programPaymentId) {
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
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = window.location.origin + '/payments/detail/' + programPaymentId;
            }
        });
    }
});
