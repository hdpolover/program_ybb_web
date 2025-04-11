/**
 * Gateway Payment Handler
 * Handles the payment gateway AJAX requests and response processing
 */

function handleGatewayPaymentSubmit(form) {
    // Get form data
    const formData = new FormData(form);

    // Show loading indicator
    Swal.fire({
        title: 'Processing Payment',
        html: 'Please wait while we process your payment...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Make the AJAX request
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    // Return both the parsed JSON and the response object
                    return { data, isJson: true, response };
                });
            } else {
                // Handle non-JSON responses (like redirects)
                return { data: null, isJson: false, response };
            }
        })
        .then(({ data, isJson, response }) => {
            console.log('Payment response:', data, 'Is JSON:', isJson);

            // If it's not JSON, we might be getting redirected
            if (!isJson) {
                console.log('Response is not JSON, redirected:', response.redirected, 'URL:', response.url);
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                } else if (response.status >= 300 && response.status < 400) {
                    // Handle redirect status codes
                    const redirectUrl = response.headers.get('Location');
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                        return;
                    }
                }
            }

            // Check for nested data structure (data.data.redirect_url)
            if (data && data.data && data.data.redirect_url) {
                handleRedirect(data.data.redirect_url);
            }
            // Check for direct redirect_url
            else if (data && data.redirect_url) {
                handleRedirect(data.redirect_url);
            }
            // Check for nested payment_id
            else if (data && data.data && data.data.payment_id) {
                handlePaymentSuccess(document.getElementById('program_payment_id').value);
            }
            // Check for direct payment_id
            else if (data && data.payment_id) {
                handlePaymentSuccess(document.getElementById('program_payment_id').value);
            }
            else {
                // Show error message
                const errorMessage = data && data.message ? data.message : 'There was an error processing your payment. Please try again.';
                handlePaymentError(errorMessage);
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            handlePaymentError('There was an error processing your payment. Please try again.');
        });
}

function handleRedirect(redirectUrl) {
    console.log('Handling redirect to:', redirectUrl);

    // Create a temporary anchor element
    const link = document.createElement('a');
    link.href = redirectUrl;
    link.target = '_blank'; // Open in new tab
    link.rel = 'noopener noreferrer'; // Security best practice

    // Add to document body (required for Firefox)
    document.body.appendChild(link);

    // Click the link programmatically
    link.click();

    // Clean up
    document.body.removeChild(link);

    // Always show payment in progress message
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
                    <p class="mb-0">Please complete the payment process in the payment gateway tab.</p>
                    <p class="text-muted small mt-3">You can check your payment status after completing payment.</p>
                </div>
            `,
        icon: 'info',
        showConfirmButton: true,
        confirmButtonText: 'Check Payment Status',
        showCancelButton: true,
        cancelButtonText: 'Close',
        allowOutsideClick: true,
    }).then((result) => {
        if (result.isConfirmed) {
            // If they want to check status, redirect to the payment details page
            const programPaymentId = document.getElementById('program_payment_id').value;
            window.location.href = SITE_URL + '/payments/detail/' + programPaymentId;
        } else {
            // If pop-up was blocked, show manual instructions
            Swal.fire({
                title: 'Payment Gateway Ready',
                html: `
                        <p>The payment gateway is ready, but pop-ups may be blocked by your browser.</p>
                        <p class="mt-3">Please click the button below to open the payment gateway in a new tab.</p>
                        <p class="small text-muted mt-4">Do not close this window. You can return here after completing payment.</p>
                    `,
                icon: 'info',
                showConfirmButton: true,
                confirmButtonText: 'Open Payment Gateway',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                allowOutsideClick: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Try to open again when user explicitly clicks
                    window.open(redirectUrl, '_blank');

                    // Show "payment in progress" message
                    Swal.fire({
                        title: 'Payment In Progress',
                        html: 'Please complete your payment in the new tab.',
                        icon: 'info',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });

}

function handlePaymentSuccess(programPaymentId) {
    console.log('Payment initiated successfully');
    Swal.fire({
        title: 'Payment Initiated',
        text: 'Your payment has been initiated. Please check payment status.',
        icon: 'success',
        timer: 2000,
        timerProgressBar: true,
        willClose: () => {
            // Redirect to payment detail page
            window.location.href = SITE_URL + '/payments/detail/' + programPaymentId;
        }
    });
}

function handlePaymentError(message) {
    console.error('Payment error:', message);
    Swal.fire({
        title: 'Payment Error',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}
