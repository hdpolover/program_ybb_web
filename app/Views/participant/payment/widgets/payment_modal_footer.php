<!-- Payment Gateway Handler Script -->
<script src="/assets/js/pages/payment-gateway-handler.js"></script>

<!-- Define site URL for payment handler -->
<script>
    // This will be used by the payment gateway handler
    document.addEventListener('DOMContentLoaded', function() {
        // Add a hidden field with the site URL for use in redirects
        const siteUrlInput = document.createElement('input');
        siteUrlInput.type = 'hidden';
        siteUrlInput.id = 'site_url';
        siteUrlInput.value = '<?= site_url() ?>';
        document.getElementById('paymentForm')?.appendChild(siteUrlInput);
    });
</script>
