<?php

/**
 * Payment Modal Widget
 * Reusable modal for making payments with various payment methods                           <div class="mb-3">
 */
?>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="makePaymentModalLabel">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= site_url('payments/make'); ?>" method="post" id="paymentForm" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="program_payment_id" id="program_payment_id" value="<?= isset($selectedProgramPayment) ? $selectedProgramPayment['id'] : '-' ?>">
                    <input type="hidden" name="amount" id="payment_amount" value="<?= isset($selectedProgramPayment) ? $selectedProgramPayment['usd_amount'] : '-' ?>">
                    <input type="hidden" name="paymentType" id="payment_type" value="gateway">
                    <input type="hidden" name="payment_method_id" id="payment_method_id" value="">
                    <?php if (isset($selectedProgramPayment)): ?>
                        <input type="hidden" name="paymentName" value="<?= esc($selectedProgramPayment['name']) ?>">
                        <?php if (isset($selectedProgramPayment['category'])): ?>
                            <input type="hidden" name="paymentCategory" value="<?= esc($selectedProgramPayment['category']) ?>">
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Program Payment Details Section -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="form-label text-muted small">Program Payment</label>
                                <?php if (isset($selectedProgramPayment) && !empty($selectedProgramPayment)): ?>
                                    <h5 id="payment_description" class="mb-1 fw-semibold"><?= esc($selectedProgramPayment['name']) ?></h5>
                                    <?php if (isset($selectedProgramPayment['category']) && !empty($selectedProgramPayment['category'])): ?>
                                        <span class="badge bg-soft-secondary text-secondary"><?= esc($selectedProgramPayment['category']) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <h5 id="payment_description" class="mb-1 fw-semibold"></h5>
                                <?php endif; ?>
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-muted small">Amount to Pay</label>
                                <?php if (isset($selectedProgramPayment) && !empty($selectedProgramPayment)): ?>
                                    <h4 id="payment_amount_display" class="mb-0 fw-bold text-primary">$<?= number_format($selectedProgramPayment['usd_amount'], 2) ?></h4>
                                <?php else: ?>
                                    <h4 id="payment_amount_display" class="mb-0 fw-bold text-primary"></h4>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Currency Warning -->
                    <div class="alert alert-warning mb-4">
                        <div class="d-flex align-items-center">
                            <i class="ri-exchange-dollar-line fs-18 me-2"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Currency Conversion Notice</h6>
                                <p class="mb-0">Although the amount is displayed in USD, payments will be processed in IDR (Indonesian Rupiah).
                                    The current conversion rate is 1 USD = <?= number_format($webSettings['usd_in_idr'], 2) ?> IDR.</p>

                                <div class="mt-3 pt-2 border-top">
                                    <h5 class="mb-0">Amount in IDR: <span id="amount_in_idr" class="fw-bold fs-4">
                                            <?php if (isset($selectedProgramPayment) && !empty($selectedProgramPayment)): ?>
                                                <?php $idrAmount = $selectedProgramPayment['usd_amount'] * $webSettings['usd_in_idr']; ?>
                                                <?= number_format($idrAmount, 0, ',', '.') ?> IDR
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Type Selection -->
                    <div class="mb-4">
                        <label for="paymentType" class="form-label fw-medium">Payment Type</label>
                        <select class="form-select" id="paymentType" name="paymentType" required>
                            <option value="gateway" selected>Payment Gateway (Credit/Debit Card, Virtual Account, QRIS, etc.)</option>
                            <option value="manual">Manual Payment (Bank Transfer, PayPal, etc.)</option>
                        </select>
                        <div class="form-text" id="paymentTypeHelp">
                            <span class="gateway-help">Proceed to pay securely with our payment gateway</span>
                            <span class="manual-help d-none">Submit payment proof after making a manual payment</span>
                        </div>
                    </div>

                    <!-- Manual Payment Methods - only shown for manual payment type -->
                    <div id="manualPaymentOptions" style="display: none;">
                        <div class="mb-4">
                            <label for="paymentMethod" class="form-label fw-medium">Payment Method</label>
                            <select class="form-select payment-method-select" id="paymentMethod" name="paymentMethod">
                                <option value="">Select Payment Method</option>
                                <?php if (isset($paymentMethods) && !empty($paymentMethods)): ?>
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <?php if (isset($method['type']) && $method['type'] == 'manual'): ?>
                                            <option value="<?= $method['id'] ?>"
                                                data-description="<?= isset($method['description']) ? esc($method['description']) : '' ?>"
                                                data-img-url="<?= isset($method['img_url']) ? esc($method['img_url']) : '' ?>">
                                                <?= esc($method['name']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <script>
                            $(document).ready(function() {
                                // Initialize Select2 for payment methods dropdown
                                $('#paymentMethod').select2({
                                    templateResult: formatPaymentMethod,
                                    templateSelection: formatPaymentMethod,
                                    escapeMarkup: function(m) {
                                        return m;
                                    }
                                });

                                // Format payment method options with images
                                function formatPaymentMethod(method) {
                                    if (!method.id) return method.text; // Skip placeholder

                                    const imgUrl = $(method.element).data('img-url');
                                    const imgHtml = imgUrl ?
                                        `<img src="${imgUrl}" class="payment-method-img" alt="${method.text}" />` :
                                        '<div class="payment-method-img-placeholder"></div>';

                                    return $(`<span>${imgHtml} ${method.text}</span>`);
                                }
                            });
                        </script>
                        <!-- Manual Payment Fields -->
                        <div id="manualPaymentFields" class="payment-method-fields" style="display: none;">
                            <div class="mb-3">
                                <div class="alert alert-info">
                                    <label for="manualInstructions" class="form-label">Instructions</label>
                                    <p id="manualInstructions" name="manualInstructions"></p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="account_name" class="form-label">Account Name</label>
                                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Name on your bank account/payment source" required>
                                <div class="invalid-feedback">Please enter the account name</div>
                            </div>
                            <div class="mb-3">
                                <label for="source_name" class="form-label">Source Name</label>
                                <input type="text" class="form-control" id="source_name" name="source_name" placeholder="Bank name or payment source" required>
                                <div class="invalid-feedback">Please enter the source name</div>
                            </div>
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" required max="<?= date('Y-m-d') ?>">
                                <div class="invalid-feedback">Please select a valid payment date (today or earlier)</div>
                            </div>
                            <div class="mb-3">
                                <label for="manualProof" class="form-label">Payment Proof (Required)</label>
                                <input type="file" class="form-control" id="manualProof" name="proof_url" accept="image/*" required max-size="1024">
                                <div class="form-text">Upload a photo of your receipt or payment proof (Max: 1 MB)</div>
                                <div class="invalid-feedback" id="filesize-error">The file is too large. Maximum allowed size is 1 MB.</div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional information about your payment"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Gateway Payment Methods - only shown for gateway payment type -->
                    <div id="gatewayPaymentOptions" style="display: block;">
                        <div class="mb-4">
                            <?php
                            // Find the active gateway payment method (should be only one)
                            $gatewayMethod = null;
                            if (isset($paymentMethods) && !empty($paymentMethods)):
                                foreach ($paymentMethods as $method):
                                    if (isset($method['type']) && $method['type'] == 'gateway' && isset($method['is_active']) && $method['is_active'] == 1):
                                        $gatewayMethod = $method;
                                        break;
                                    endif;
                                endforeach;
                            endif;
                            ?>

                            <?php if ($gatewayMethod): ?>
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ri-bank-card-line fs-24 me-2"></i>
                                        <h6 class="mb-0">Automatic Secure Payment Gateway</h6>
                                    </div>
                                    <?php if (isset($gatewayMethod['description']) && !empty($gatewayMethod['description'])): ?>
                                        <p class="mb-0"><?= $gatewayMethod['description'] ?></p>
                                    <?php else: ?>
                                        <p class="mb-0">You'll be redirected to our secure payment provider to complete this transaction.</p>
                                    <?php endif; ?>
                                    <input type="hidden" id="gatewayPaymentMethodId" value="<?= $gatewayMethod['id'] ?>">
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <p class="mb-0">No active payment gateway is available. Please try manual payment instead or contact support.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Complete Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include Select2 for enhanced dropdowns with images -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* Custom styles for payment method select with images */
    .payment-method-img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        margin-right: 8px;
        vertical-align: middle;
    }

    .select2-container--default .select2-results__option {
        padding: 8px 12px;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

<!-- Payment Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Log to confirm the initialization
        console.log('Payment Modal initialized');

        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            console.log('Payment form found');

            // Make sure the payment gateway handler is loaded
            if (typeof window.gatewayHandlerLoaded === 'undefined') {
                console.log('Gateway handler not detected. Form may not work correctly.');
            }

            // Track if payment is in progress to prevent duplicates
            let isPaymentInProgress = false;

            paymentForm.addEventListener('submit', function(event) {
                console.log('Form submit event fired');

                const paymentType = document.getElementById('payment_type').value;
                console.log('Payment type in modal handler:', paymentType);

                // Only handle manual payments here - gateway payments are handled by payment-gateway-handler.js
                if (paymentType !== 'manual') {
                    console.log('This is a gateway payment - letting the gateway handler take over');
                    // Let payment-gateway-handler.js handle gateway payments
                    return;
                }

                event.preventDefault(); // Prevent default form submission for manual payments

                // Prevent multiple submissions
                if (isPaymentInProgress) {
                    console.log('Payment already in progress, ignoring duplicate submission');
                    return;
                } // Check file size limit (1 MB = 1024 * 1024 bytes)
                const fileInput = document.getElementById('manualProof');
                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    const fileSize = fileInput.files[0].size;
                    const maxSize = 1024 * 1024; // 1 MB in bytes

                    if (fileSize > maxSize) {
                        console.log('File size exceeds limit:', fileSize, 'bytes');
                        document.getElementById('filesize-error').style.display = 'block';
                        fileInput.setCustomValidity('File size exceeds the maximum limit of 1 MB');
                        this.classList.add('was-validated');
                        return;
                    } else {
                        fileInput.setCustomValidity(''); // Clear any previous validation errors
                        document.getElementById('filesize-error').style.display = 'none';
                    }
                }

                // Validate manual payment fields
                if (!this.checkValidity()) {
                    this.classList.add('was-validated');
                    return;
                }

                // Add validation classes to show error messages
                this.classList.add('was-validated');

                // Set flag to prevent duplicate submissions
                isPaymentInProgress = true;

                // Close the modal first
                let paymentModal = bootstrap.Modal.getInstance(document.getElementById('makePaymentModal'));
                paymentModal.hide();

                // Show loading indicator
                const loadingAlert = Swal.fire({
                    title: 'Processing Manual Payment',
                    html: 'Please wait while we process your payment submission...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // For manual payments, use traditional form submission
                setTimeout(() => {
                    this.submit();
                }, 500);
            });
        }

        // Payment modal data
        const makePaymentModal = document.getElementById('makePaymentModal');
        if (makePaymentModal) {
            makePaymentModal.addEventListener('show.bs.modal', function(event) {
                // Always make sure we have a button that triggered the modal
                if (event.relatedTarget) {
                    const button = event.relatedTarget;
                    const paymentId = button.getAttribute('data-payment-id');
                    const paymentName = button.getAttribute('data-payment-name');
                    const paymentAmount = button.getAttribute('data-payment-amount');
                    const paymentCategory = button.getAttribute('data-payment-category');
                    const paymentData = button.getAttribute('data-payment-object');
                    let selectedPayment = null;

                    console.log('Modal triggered with payment ID:', paymentId);
                    console.log('Payment name:', paymentName);
                    console.log('Payment amount:', paymentAmount);

                    // multipy by usd_in_idr to get the amount in IDR
                    const usdInIdr = <?= $webSettings['usd_in_idr'] ?>;
                    const idrAmount = paymentAmount * usdInIdr;
                    console.log('IDR amount:', idrAmount);

                    // set the IDR amount in the modal
                    const amountInIdrElement = document.getElementById('amount_in_idr');

                    if (amountInIdrElement) {
                        amountInIdrElement.textContent = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(idrAmount);
                    }

                    // Try parsing the full payment object if available
                    if (paymentData) {
                        try {
                            selectedPayment = JSON.parse(paymentData);
                            console.log('Parsed payment object:', selectedPayment);
                        } catch (e) {
                            console.error('Error parsing payment object:', e);
                        }
                    }

                    // Fallback to programPayments array if available
                    if (!selectedPayment && typeof programPayments !== 'undefined') {
                        const paymentIndex = button.getAttribute('data-payment-index');
                        if (paymentIndex) {
                            selectedPayment = programPayments[paymentIndex] || null;
                            console.log('Using payment from programPayments array:', selectedPayment);
                        }
                    }

                    if (selectedPayment) {
                        // Set data from the payment object
                        document.getElementById('program_payment_id').value = selectedPayment.id;
                        document.getElementById('payment_amount').value = selectedPayment.usd_amount;

                        if (document.getElementById('payment_description')) {
                            document.getElementById('payment_description').textContent = selectedPayment.name || 'Program Payment';
                            console.log('Setting payment description from selectedPayment:', selectedPayment.name);
                        }

                        if (document.getElementById('payment_amount_display')) {
                            document.getElementById('payment_amount_display').textContent = '$' + parseFloat(selectedPayment.usd_amount).toFixed(2);
                        }
                    } else {
                        // Final fallback to individual attributes
                        document.getElementById('program_payment_id').value = paymentId;
                        document.getElementById('payment_amount').value = paymentAmount;

                        if (document.getElementById('payment_description')) {
                            document.getElementById('payment_description').textContent = paymentName || 'Program Payment';
                        }

                        if (document.getElementById('payment_amount_display')) {
                            const amount = paymentAmount || '0.00';
                            document.getElementById('payment_amount_display').textContent = '$' + parseFloat(amount).toFixed(2);
                        }
                    }

                    // Only update the description if it's empty
                    const descElement = document.getElementById('payment_description');
                    if (descElement && !descElement.innerText.trim()) {
                        // Use paymentName instead of undefined paymentDescription variable
                        descElement.textContent = paymentName || 'Program Payment';
                    }

                    // Only update the amount display if it's empty
                    const amountElement = document.getElementById('payment_amount_display');
                    if (amountElement && !amountElement.innerText.trim()) {
                        amountElement.textContent = '$' + (paymentAmount || '0.00');
                    }

                    if (document.getElementById('payment_reference')) {
                        document.getElementById('payment_reference').textContent = 'YBB-' + paymentId;
                    }
                }

                // Ensure payment type is set to gateway by default (both select and hidden input)
                const paymentTypeSelect = document.getElementById('paymentType');
                if (paymentTypeSelect) {
                    paymentTypeSelect.value = 'gateway';
                    document.getElementById('payment_type').value = 'gateway';
                    console.log('Payment type set to gateway');
                }

                // Update help text visibility
                document.querySelector('.gateway-help').classList.remove('d-none');
                document.querySelector('.manual-help').classList.add('d-none');

                // Hide manual payment options by default
                const manualOptionsSection = document.getElementById('manualPaymentOptions');
                if (manualOptionsSection) {
                    manualOptionsSection.style.display = 'none';
                }

                // Show gateway payment options by default
                const gatewayOptionsSection = document.getElementById('gatewayPaymentOptions');
                if (gatewayOptionsSection) {
                    gatewayOptionsSection.style.display = 'block';
                }

                // Reset payment method selection
                const paymentMethodSelect = document.getElementById('paymentMethod');
                if (paymentMethodSelect) {
                    paymentMethodSelect.selectedIndex = 0;
                    paymentMethodSelect.required = false; // Not required for gateway payment
                }

                // Set the gateway payment method ID if available
                const gatewayPaymentMethodId = document.getElementById('gatewayPaymentMethodId');
                if (gatewayPaymentMethodId && gatewayPaymentMethodId.value) {
                    document.getElementById('payment_method_id').value = gatewayPaymentMethodId.value;
                    console.log('Gateway payment method ID set to:', gatewayPaymentMethodId.value);
                }

                // Hide all payment method fields
                document.querySelectorAll('.payment-method-fields').forEach(field => {
                    field.style.display = 'none';
                });
            });
        }

        // Payment type toggle
        const paymentTypeSelect = document.getElementById('paymentType');
        if (paymentTypeSelect) {
            paymentTypeSelect.addEventListener('change', function() {
                // Set the hidden type field
                const selectedType = this.value;
                document.getElementById('payment_type').value = selectedType;

                // Toggle help text
                if (selectedType === 'gateway') {
                    document.querySelector('.gateway-help').classList.remove('d-none');
                    document.querySelector('.manual-help').classList.add('d-none');
                } else {
                    document.querySelector('.gateway-help').classList.add('d-none');
                    document.querySelector('.manual-help').classList.remove('d-none');
                }

                // Toggle manual payment options visibility
                const manualOptionsSection = document.getElementById('manualPaymentOptions');
                const gatewayOptionsSection = document.getElementById('gatewayPaymentOptions');
                if (manualOptionsSection && gatewayOptionsSection) {
                    manualOptionsSection.style.display = selectedType === 'manual' ? 'block' : 'none';
                    gatewayOptionsSection.style.display = selectedType === 'gateway' ? 'block' : 'none';
                }

                // Update payment method requirement
                const paymentMethodSelect = document.getElementById('paymentMethod');
                if (paymentMethodSelect) {
                    paymentMethodSelect.required = selectedType === 'manual';
                    paymentMethodSelect.selectedIndex = 0;
                }

                // Hide all payment method fields
                document.querySelectorAll('.payment-method-fields').forEach(field => {
                    field.style.display = 'none';
                });
            });
        }

        // Payment method fields toggle
        const paymentMethodSelect = document.getElementById('paymentMethod');
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                // Hide all payment method fields
                document.querySelectorAll('.payment-method-fields').forEach(field => {
                    field.style.display = 'none';
                });

                // Set payment_method_id hidden field value
                document.getElementById('payment_method_id').value = this.value;

                document.getElementById('manualPaymentFields').style.display = 'block';
                document.getElementById('manualInstructions').style.display = 'block';

                // set manual instructions
                const instructions = document.getElementById('manualInstructions'); // get selected payment method
                const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
                const selectedMethod = selectedOption.textContent || selectedOption.innerText;
                const paymentDescription = selectedOption.getAttribute('data-description');

                // Populate instructions with description from payment method
                if (paymentDescription) {
                    // Display HTML content properly
                    instructions.innerHTML = paymentDescription;
                } else {
                    instructions.textContent = 'Please complete the payment using ' + selectedMethod + ' and upload proof of your payment.';
                }

            });
        }

        // Automatically set the gateway payment method ID when available
        const gatewayPaymentMethodId = document.getElementById('gatewayPaymentMethodId');
        if (gatewayPaymentMethodId) {
            // Set payment_method_id hidden field value on page load
            document.getElementById('payment_method_id').value = gatewayPaymentMethodId.value;
        }

        // Function to filter payment methods based on selected type
        function filterPaymentMethodsByType(type) {
            const paymentMethodSelect = document.getElementById('paymentMethod');
            if (!paymentMethodSelect) return;

            // Hide or show the payment method section based on type
            const paymentMethodSection = document.getElementById('paymentMethodSection');

            // Skip if paymentMethodSection doesn't exist
            if (!paymentMethodSection) {
                console.log('Payment method section not found, skipping display toggle');
                return;
            }

            // For gateway payment, just show the section (continue button will be shown)
            // For manual payment, show detailed form fields
            if (type === 'gateway') {
                paymentMethodSection.style.display = 'block';

                // Show only gateway payment methods
                Array.from(paymentMethodSelect.options).forEach(option => {
                    const optionType = option.getAttribute('data-type') || '';
                    if (option.value === '') {
                        // Always show the placeholder
                        option.style.display = '';
                    } else {
                        // Show only options that match the selected type
                        option.style.display = (optionType === 'gateway') ? '' : 'none';
                    }
                });
            } else if (type === 'manual') {
                paymentMethodSection.style.display = 'block';

                // Show only manual payment methods
                Array.from(paymentMethodSelect.options).forEach(option => {
                    const optionType = option.getAttribute('data-type') || '';
                    if (option.value === '') {
                        // Always show the placeholder
                        option.style.display = '';
                    } else {
                        // Show only options that match the selected type
                        option.style.display = (optionType === 'manual') ? '' : 'none';
                    }
                });

                // For manual payment, always show the manual payment fields
                document.getElementById('manualPaymentFields').style.display = 'block';
            }
        }

        // Skip initialize the filtering on page load since the section might not exist in all contexts
        // Instead, just ensure the gateway payment method is properly set
        if (gatewayPaymentMethodId && gatewayPaymentMethodId.value) {
            document.getElementById('payment_method_id').value = gatewayPaymentMethodId.value;
            console.log('Gateway payment method ID set to:', gatewayPaymentMethodId.value);
        }
    });
</script>

<?php include(__DIR__ . '/payment_modal_footer.php'); ?>