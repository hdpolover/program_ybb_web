/**
 * Receipt Download Handler
 * Handles receipt download and viewing functionality with loading indicators and user options
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Find all receipt buttons across the application
    const receiptButtons = document.querySelectorAll('.receipt-button');
    
    // Add click handler for receipt download buttons
    if (receiptButtons && receiptButtons.length > 0) {
        receiptButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default to handle the navigation manually

                // Get the original href
                const downloadUrl = this.getAttribute('href');

                // Show loading notification
                Swal.fire({
                    title: 'Generating Receipt',
                    html: 'Please wait while we generate your receipt...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Use fetch to request the receipt generation
                fetch(downloadUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to generate receipt');
                        }
                        return response.blob();
                    })
                    .then(blob => {
                        // Create blob URL
                        const blobUrl = URL.createObjectURL(blob);

                        // Close the loading modal
                        Swal.close();

                        // Show success message with option to open/download
                        Swal.fire({
                            icon: 'success',
                            title: 'Receipt Ready',
                            text: 'Your receipt has been generated successfully!',
                            footer: '<small>You can view or save the receipt using the buttons below.</small>',
                            showCancelButton: true,
                            confirmButtonText: 'View Receipt',
                            cancelButtonText: 'Download Receipt',
                            showCloseButton: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Open in new tab
                                window.open(blobUrl, '_blank');
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Create temporary link for download
                                const a = document.createElement('a');
                                a.href = blobUrl;
                                a.download = 'receipt_' + Date.now() + '.pdf';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error generating receipt:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Generating Receipt',
                            text: 'There was a problem generating your receipt. Please try again later.',
                            showCloseButton: true
                        });
                    });
            });
        });
    }
});
