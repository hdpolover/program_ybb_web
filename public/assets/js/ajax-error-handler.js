/**
 * Global AJAX Error Handler
 * 
 * Provides consistent handling for various AJAX errors including timeouts.
 * Uses SweetAlert2 for user-friendly notifications.
 */
(function() {
    "use strict";

    // Wait for document to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Make sure jQuery and SweetAlert2 are available
        if (typeof jQuery === 'undefined') {
            console.error('AJAX Error Handler requires jQuery');
            return;
        }

        if (typeof Swal === 'undefined') {
            console.error('AJAX Error Handler requires SweetAlert2');
            return;
        }

        // Set default AJAX settings
        $.ajaxSetup({
            timeout: 60000, // 60 seconds default timeout
            retryAfter: 2000 // Wait 2 seconds before retrying
        });

        // Global AJAX error handler
        $(document).ajaxError(function(event, jqXHR, settings, thrownError) {
            console.log('AJAX Error:', thrownError, 'Status:', jqXHR.status);
            
            // Don't show multiple error messages for the same request
            if (settings.suppressErrors === true) {
                return;
            }

            // Handle specific error cases
            if (jqXHR.status === 0) {
                // Connection refused, timeout, or CORS error
                if (thrownError === 'timeout') {
                    // Request timeout
                    Swal.fire({
                        title: 'Request Timeout',
                        text: 'The server is taking too long to respond. This could be due to high server load or connection issues.',
                        icon: 'warning',
                        confirmButtonText: 'Try Again',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Retry the request - but mark it to suppress errors on subsequent failures
                            settings.suppressErrors = true;
                            $.ajax(settings);
                        }
                    });
                } else {
                    // Network error or CORS issue
                    Swal.fire({
                        title: 'Connection Error',
                        text: 'Cannot connect to the server. Please check your internet connection and try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                return;
            }
            
            // Server returned an error response
            if (jqXHR.responseJSON) {
                // Try to use the structured error from the server
                const errorData = jqXHR.responseJSON;
                Swal.fire({
                    title: errorData.title || 'Error',
                    text: errorData.message || 'An unexpected error occurred.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Check for timeout in response text
            if (jqXHR.responseText && jqXHR.responseText.includes('Maximum execution time')) {
                Swal.fire({
                    title: 'Server Timeout',
                    text: 'The server took too long to process your request. Please try again later or contact support if this persists.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Generic error handling based on HTTP status code
            const errorMessages = {
                400: 'Bad request. Please check your data and try again.',
                401: 'You need to log in again to continue.',
                403: 'You don\'t have permission to perform this action.',
                404: 'The requested resource was not found.',
                500: 'The server encountered an error. Please try again later.',
                504: 'Gateway timeout. The server took too long to respond.'
            };
            
            Swal.fire({
                title: 'Error',
                text: errorMessages[jqXHR.status] || 'An unexpected error occurred.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
        
        // Handle fetch API errors for modern JS
        window.addEventListener('unhandledrejection', function(event) {
            if (event.reason instanceof Error && event.reason.message === 'Failed to fetch') {
                Swal.fire({
                    title: 'Connection Error',
                    text: 'Could not connect to the server. Please check your internet connection and try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
        
        // Check URL for timeout parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error') === 'timeout') {
            Swal.fire({
                title: 'Request Timeout',
                text: 'The previous request took too long to complete. Please try again or contact support if the problem persists.',
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then(() => {
                // Remove the error parameter from URL to prevent showing the message again on refresh
                const newParams = new URLSearchParams(urlParams);
                newParams.delete('error');
                
                const newUrl = window.location.pathname + 
                    (newParams.toString() ? '?' + newParams.toString() : '');
                
                window.history.replaceState({}, document.title, newUrl);
            });
        }
    });
    
    // Intercept fetch calls to handle timeouts
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        // Extract request and options
        const [resource, initOptions = {}] = args;
        
        // Set default timeout if not provided
        const timeoutDuration = initOptions.timeout || 60000;
        
        return new Promise((resolve, reject) => {
            // Create abort controller for timeout
            const controller = new AbortController();
            const signal = controller.signal;
            
            // Merge signal with existing options
            const options = {
                ...initOptions,
                signal
            };
            
            // Set timeout
            const timeoutId = setTimeout(() => {
                controller.abort();
                const timeoutError = new Error('Timeout');
                timeoutError.name = 'TimeoutError';
                reject(timeoutError);
            }, timeoutDuration);
            
            // Make the fetch call
            originalFetch(resource, options)
                .then(response => {
                    clearTimeout(timeoutId);
                    resolve(response);
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    
                    // Handle aborted requests due to timeout
                    if (error.name === 'AbortError' || error.name === 'TimeoutError') {
                        console.error('Fetch request timeout:', resource);
                        // Don't show alert here - let the unhandledrejection handler deal with it
                    }
                    
                    reject(error);
                });
        });
    };
})();
