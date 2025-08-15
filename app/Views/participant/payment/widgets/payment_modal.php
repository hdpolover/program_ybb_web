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
                    <!-- Note: participant_id is retrieved from session, which gets participant data from /participants/user/{user_id} API -->
                    <input type="hidden" name="program_payment_id" id="program_payment_id" value="<?= isset($selectedProgramPayment) ? $selectedProgramPayment['id'] : '-' ?>">>
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
                            <div class="payment-method-container">
                                <select class="form-select" id="paymentMethod" name="paymentMethod">
                                    <option value="">Select Payment Method</option>
                                    <?php if (isset($paymentMethods) && !empty($paymentMethods)): ?>
                                        <?php foreach ($paymentMethods as $method): ?>
                                            <?php if (isset($method['type']) && $method['type'] == 'manual'): ?>
                                                <option value="<?= $method['id'] ?>"
                                                    data-description="<?= isset($method['description']) ? esc($method['description']) : '' ?>"
                                                    data-img-url="<?= isset($method['img_url']) ? esc($method['img_url']) : '' ?>"
                                                    data-name="<?= esc($method['name']) ?>">
                                                    <?= esc($method['name']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                
                                <!-- Payment method preview with image -->
                                <div class="payment-method-preview" id="paymentMethodPreview">
                                    <img class="payment-method-img" id="paymentMethodImg" src="" alt="" style="display: none;">
                                    <div class="payment-method-info">
                                        <p class="payment-method-name" id="paymentMethodName"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

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

<style>
    /* Fix modal dialog overflow */
    .modal-body {
        overflow: visible;
    }

    .modal-dialog {
        overflow: visible;
    }

    /* Payment method select with image preview */
    .payment-method-container {
        position: relative;
    }

    .payment-method-preview {
        display: none;
        align-items: center;
        margin-top: 8px;
        padding: 8px 12px;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        background-color: #f8f9fa;
    }

    .payment-method-preview.show {
        display: flex;
    }

    .payment-method-img {
        width: 32px;
        height: 32px;
        object-fit: contain;
        margin-right: 12px;
        border-radius: 4px;
    }

    .payment-method-info {
        flex: 1;
    }

    .payment-method-name {
        font-weight: 500;
        margin: 0;
        font-size: 14px;
    }

    /* Custom select styling */
    .form-select:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

<!-- Payment Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Payment Modal initialized');

        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            console.log('Payment form found');

            // Track if payment is in progress to prevent duplicates
            let isPaymentInProgress = false;

            paymentForm.addEventListener('submit', function(event) {
                console.log('Form submit event fired');
                event.preventDefault(); // Prevent default submission initially

                // Prevent multiple submissions
                if (isPaymentInProgress) {
                    console.log('Payment already in progress, ignoring duplicate submission');
                    return;
                }

                const paymentType = document.getElementById('payment_type').value;
                console.log('Payment type in modal handler:', paymentType);

                // Handle manual payments
                if (paymentType === 'manual') {
                    console.log('Processing manual payment');
                    
                    // Check file size limit (1 MB = 1024 * 1024 bytes)
                    const fileInput = document.getElementById('manualProof');
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        const fileSize = fileInput.files[0].size;
                        const maxSize = 1024 * 1024; // 1 MB in bytes

                        if (fileSize > maxSize) {
                            console.log('File size exceeds limit:', fileSize, 'bytes');
                            const errorDiv = document.getElementById('filesize-error');
                            if (errorDiv) errorDiv.style.display = 'block';
                            fileInput.setCustomValidity('File size exceeds the maximum limit of 1 MB');
                            this.classList.add('was-validated');
                            return;
                        } else {
                            fileInput.setCustomValidity(''); // Clear any previous validation errors
                            const errorDiv = document.getElementById('filesize-error');
                            if (errorDiv) errorDiv.style.display = 'none';
                        }
                    }

                    // Validate manual payment fields
                    if (!this.checkValidity()) {
                        this.classList.add('was-validated');
                        console.log('Form validation failed');
                        return;
                    }

                    // Set flag to prevent duplicate submissions
                    isPaymentInProgress = true;

                    // Close the modal first
                    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('makePaymentModal'));
                    if (paymentModal) {
                        paymentModal.hide();
                    }

                    // Show loading indicator
                    Swal.fire({
                        title: 'Processing Manual Payment',
                        html: 'Please wait while we process your payment submission...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form after a short delay
                    setTimeout(() => {
                        this.submit();
                    }, 500);
                    
                } else {
                    // Handle gateway payments
                    console.log('Processing gateway payment');
                    
                    // Make sure we have the gateway payment method ID
                    const gatewayMethodId = document.getElementById('gatewayPaymentMethodId');
                    if (gatewayMethodId && gatewayMethodId.value) {
                        document.getElementById('payment_method_id').value = gatewayMethodId.value;
                    }

                    // Set flag to prevent duplicate submissions
                    isPaymentInProgress = true;

                    // For gateway payments, let the form submit naturally
                    // The payment gateway handler will take care of processing
                    this.submit();
                }
            });
        }

        // Payment modal setup
        const makePaymentModal = document.getElementById('makePaymentModal');
        if (makePaymentModal) {
            makePaymentModal.addEventListener('show.bs.modal', function(event) {
                // Reset payment progress flag
                let isPaymentInProgress = false;
                
                // Get payment data from the triggering button
                if (event.relatedTarget) {
                    const button = event.relatedTarget;
                    const paymentId = button.getAttribute('data-payment-id');
                    const paymentName = button.getAttribute('data-payment-name');
                    const paymentAmount = button.getAttribute('data-payment-amount');
                    const paymentCategory = button.getAttribute('data-payment-category');
                    const paymentData = button.getAttribute('data-payment-object');
                    let selectedPayment = null;

                    console.log('Modal triggered with payment ID:', paymentId);

                    // Calculate IDR amount
                    const usdInIdr = <?= $webSettings['usd_in_idr'] ?>;
                    const idrAmount = paymentAmount * usdInIdr;
                    
                    // Set the IDR amount in the modal
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

                    // Set payment data in form
                    if (selectedPayment) {
                        document.getElementById('program_payment_id').value = selectedPayment.id;
                        document.getElementById('payment_amount').value = selectedPayment.usd_amount;
                        
                        const descElement = document.getElementById('payment_description');
                        if (descElement) {
                            descElement.textContent = selectedPayment.name || 'Program Payment';
                        }
                        
                        const amountElement = document.getElementById('payment_amount_display');
                        if (amountElement) {
                            amountElement.textContent = '$' + parseFloat(selectedPayment.usd_amount).toFixed(2);
                        }
                    } else {
                        // Fallback to individual attributes
                        document.getElementById('program_payment_id').value = paymentId;
                        document.getElementById('payment_amount').value = paymentAmount;
                        
                        const descElement = document.getElementById('payment_description');
                        if (descElement) {
                            descElement.textContent = paymentName || 'Program Payment';
                        }
                        
                        const amountElement = document.getElementById('payment_amount_display');
                        if (amountElement) {
                            amountElement.textContent = '$' + parseFloat(paymentAmount || 0).toFixed(2);
                        }
                    }
                }

                // Set default to gateway payment
                const paymentTypeSelect = document.getElementById('paymentType');
                if (paymentTypeSelect) {
                    paymentTypeSelect.value = 'gateway';
                    document.getElementById('payment_type').value = 'gateway';
                }

                // Update help text
                document.querySelector('.gateway-help').classList.remove('d-none');
                document.querySelector('.manual-help').classList.add('d-none');

                // Hide manual options, show gateway options
                const manualOptions = document.getElementById('manualPaymentOptions');
                const gatewayOptions = document.getElementById('gatewayPaymentOptions');
                if (manualOptions) manualOptions.style.display = 'none';
                if (gatewayOptions) gatewayOptions.style.display = 'block';

                // Reset payment method selection
                const paymentMethodSelect = document.getElementById('paymentMethod');
                if (paymentMethodSelect) {
                    paymentMethodSelect.selectedIndex = 0;
                    paymentMethodSelect.required = false;
                }

                // Hide payment method preview
                const preview = document.getElementById('paymentMethodPreview');
                if (preview) {
                    preview.classList.remove('show');
                }

                // Set gateway payment method ID
                const gatewayMethodId = document.getElementById('gatewayPaymentMethodId');
                if (gatewayMethodId && gatewayMethodId.value) {
                    document.getElementById('payment_method_id').value = gatewayMethodId.value;
                }

                // Hide all payment method fields
                document.querySelectorAll('.payment-method-fields').forEach(field => {
                    field.style.display = 'none';
                });
            });
        }

        // Payment type change handler
        const paymentTypeSelect = document.getElementById('paymentType');
        if (paymentTypeSelect) {
            paymentTypeSelect.addEventListener('change', function() {
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

                // Toggle options sections
                const manualOptions = document.getElementById('manualPaymentOptions');
                const gatewayOptions = document.getElementById('gatewayPaymentOptions');
                
                if (selectedType === 'manual') {
                    if (manualOptions) manualOptions.style.display = 'block';
                    if (gatewayOptions) gatewayOptions.style.display = 'none';
                } else {
                    if (manualOptions) manualOptions.style.display = 'none';
                    if (gatewayOptions) gatewayOptions.style.display = 'block';
                }

                // Update payment method requirement
                const paymentMethodSelect = document.getElementById('paymentMethod');
                if (paymentMethodSelect) {
                    paymentMethodSelect.required = selectedType === 'manual';
                    paymentMethodSelect.selectedIndex = 0;
                }

                // Hide payment method preview when switching types
                const preview = document.getElementById('paymentMethodPreview');
                if (preview) {
                    preview.classList.remove('show');
                }

                // Hide all payment method fields
                document.querySelectorAll('.payment-method-fields').forEach(field => {
                    field.style.display = 'none';
                });
            });
        }

        // Payment method change handler
        const paymentMethodSelect = document.getElementById('paymentMethod');
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function() {
                console.log('Payment method changed to:', this.value);
                
                // Hide all payment method fields first
                document.querySelectorAll('.payment-method-fields').forEach(field => {
                    field.style.display = 'none';
                });

                // Set payment method ID
                document.getElementById('payment_method_id').value = this.value;

                // Handle payment method preview with image
                const preview = document.getElementById('paymentMethodPreview');
                const previewImg = document.getElementById('paymentMethodImg');
                const previewName = document.getElementById('paymentMethodName');

                if (this.value && preview && previewImg && previewName) {
                    const selectedOption = this.options[this.selectedIndex];
                    const imgUrl = selectedOption.getAttribute('data-img-url');
                    const methodName = selectedOption.getAttribute('data-name') || selectedOption.textContent;

                    // Show the preview container
                    preview.classList.add('show');
                    
                    // Set the method name
                    previewName.textContent = methodName;

                    // Handle the image
                    if (imgUrl && imgUrl.trim()) {
                        previewImg.src = imgUrl;
                        previewImg.alt = methodName;
                        previewImg.style.display = 'block';
                        
                        // Handle image load errors
                        previewImg.onerror = function() {
                            this.style.display = 'none';
                        };
                    } else {
                        previewImg.style.display = 'none';
                    }
                } else if (preview) {
                    // Hide preview if no selection
                    preview.classList.remove('show');
                }

                // Show manual payment fields if a method is selected
                if (this.value) {
                    const manualFields = document.getElementById('manualPaymentFields');
                    if (manualFields) {
                        manualFields.style.display = 'block';
                    }

                    // Set instructions
                    const instructions = document.getElementById('manualInstructions');
                    if (instructions) {
                        const selectedOption = this.options[this.selectedIndex];
                        const paymentDescription = selectedOption.getAttribute('data-description');
                        const selectedMethod = selectedOption.textContent || selectedOption.innerText;

                        if (paymentDescription) {
                            instructions.innerHTML = paymentDescription;
                        } else {
                            instructions.textContent = 'Please complete the payment using ' + selectedMethod + ' and upload proof of your payment.';
                        }
                    }
                }
            });
        }

        // Set initial gateway payment method ID if available
        const gatewayMethodId = document.getElementById('gatewayPaymentMethodId');
        if (gatewayMethodId && gatewayMethodId.value) {
            document.getElementById('payment_method_id').value = gatewayMethodId.value;
            console.log('Initial gateway payment method ID set to:', gatewayMethodId.value);
        }
    });
</script>

<?php include(__DIR__ . '/payment_modal_footer.php'); ?>