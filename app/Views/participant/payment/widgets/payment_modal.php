<?php

/**
 * Payment Modal Widget
 * Reusable modal for making payments with various payment methods
 */

// Pre-compute gateway method for PHP rendering
$_gatewayMethod = null;
if (isset($paymentMethods) && !empty($paymentMethods)) {
    foreach ($paymentMethods as $_m) {
        if (isset($_m['type']) && $_m['type'] == 'gateway' && isset($_m['is_active']) && $_m['is_active'] == 1) {
            $_gatewayMethod = $_m;
            break;
        }
    }
}
?>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-secure-payment-line fs-20"></i>
                    <h5 class="modal-title mb-0" id="makePaymentModalLabel">Complete Your Payment</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Form wraps body + footer so the submit button is inside the form -->
            <form action="<?= site_url('payments/make'); ?>" method="post" id="paymentForm" enctype="multipart/form-data" novalidate>
                <?= csrf_field() ?>
                <!-- Hidden fields — participant_id is retrieved from session server-side -->
                <input type="hidden" name="program_payment_id" id="program_payment_id" value="<?= isset($selectedProgramPayment) ? $selectedProgramPayment['id'] : '-' ?>">
                <input type="hidden" name="amount" id="payment_amount" value="<?= isset($selectedProgramPayment) ? $selectedProgramPayment['usd_amount'] : '-' ?>">
                <input type="hidden" name="paymentType" id="payment_type" value="<?= $_gatewayMethod ? 'gateway' : 'manual' ?>">
                <input type="hidden" name="payment_method_id" id="payment_method_id" value="">
                <?php if (isset($selectedProgramPayment)): ?>
                    <input type="hidden" name="paymentName" value="<?= esc($selectedProgramPayment['name']) ?>">
                    <?php if (isset($selectedProgramPayment['category'])): ?>
                        <input type="hidden" name="paymentCategory" value="<?= esc($selectedProgramPayment['category']) ?>">
                    <?php endif; ?>
                <?php endif; ?>

                <div class="modal-body px-4 py-3">

                    <!-- 1. Payment Summary -->
                    <div class="payment-summary-card rounded-3 p-3 mb-4">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <p class="text-muted small text-uppercase fw-semibold mb-1 ls-1">You are paying for</p>
                                <h5 id="payment_description" class="fw-semibold mb-1">
                                    <?= isset($selectedProgramPayment) ? esc($selectedProgramPayment['name']) : '' ?>
                                </h5>
                                <?php if (isset($selectedProgramPayment['category']) && !empty($selectedProgramPayment['category'])): ?>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= esc($selectedProgramPayment['category']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <p class="text-muted small text-uppercase fw-semibold mb-1 ls-1">Amount</p>
                                <h3 id="payment_amount_display" class="fw-bold text-primary mb-0">
                                    $<?= isset($selectedProgramPayment) ? number_format($selectedProgramPayment['usd_amount'], 2) : '0.00' ?>
                                </h3>
                            </div>
                        </div>
                        <!-- IDR conversion row -->
                        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span class="text-muted small">
                                <i class="ri-exchange-dollar-line me-1"></i>
                                Charged in IDR &nbsp;·&nbsp; 1 USD = <?= number_format($webSettings['usd_in_idr'], 0, ',', '.') ?> IDR
                            </span>
                            <span class="fw-bold text-dark fs-15" id="amount_in_idr">
                                <?php if (isset($selectedProgramPayment) && !empty($selectedProgramPayment)): ?>
                                    <?php $idrAmount = $selectedProgramPayment['usd_amount'] * $webSettings['usd_in_idr']; ?>
                                    Rp <?= number_format($idrAmount, 0, ',', '.') ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </span>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="ri-information-line me-1"></i>Final amount on the gateway may slightly differ due to processing fees or updated rates.
                        </p>
                    </div>

                    <!-- 2. Payment Type (hidden select kept for JS backward-compat) -->
                    <select class="d-none" id="paymentType" name="paymentType" required>
                        <option value="gateway" selected>Payment Gateway</option>
                        <option value="manual">Manual Payment</option>
                    </select>
                    <!-- Hidden help text spans also kept for JS compat -->
                    <span class="gateway-help d-none"></span>
                    <span class="manual-help d-none"></span>

                    <?php if ($_gatewayMethod): ?>
                    <div class="mb-4">
                        <p class="fw-medium mb-2">How would you like to pay?</p>
                        <div class="row g-3" id="paymentTypeCards">
                            <div class="col-6">
                                <div class="payment-type-card active" data-type="gateway">
                                    <i class="ri-bank-card-2-line payment-type-icon fs-28 mb-1 d-block"></i>
                                    <h6 class="fw-semibold mb-0">Online Payment</h6>
                                    <small>Card · VA · QRIS · E-wallet</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="payment-type-card" data-type="manual">
                                    <i class="ri-money-dollar-box-line payment-type-icon fs-28 mb-1 d-block"></i>
                                    <h6 class="fw-semibold mb-0">Manual Transfer</h6>
                                    <small>Bank · PayPal · Other</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- 3. Gateway Payment Section -->
                    <div id="gatewayPaymentOptions" style="display: <?= $_gatewayMethod ? 'block' : 'none' ?>;">  
                        <?php if ($_gatewayMethod): ?>
                            <div class="gateway-info-box rounded-3 p-3 mb-2">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ri-shield-check-line text-primary fs-20"></i>
                                    <h6 class="mb-0 text-primary fw-semibold">Secure Payment Gateway</h6>
                                </div>
                                <div class="text-muted small mb-3 payment-method-desc">
                                    <?php if (!empty($_gatewayMethod['description'])): ?>
                                        <?= $_gatewayMethod['description'] ?>
                                    <?php else: ?>
                                        You'll be redirected to our secure payment provider to complete your transaction safely.
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge bg-white border text-dark fw-normal"><i class="ri-bank-card-line me-1 text-primary"></i>Credit / Debit Card</span>
                                    <span class="badge bg-white border text-dark fw-normal"><i class="ri-building-line me-1 text-primary"></i>Virtual Account</span>
                                    <span class="badge bg-white border text-dark fw-normal"><i class="ri-qr-code-line me-1 text-primary"></i>QRIS</span>
                                    <span class="badge bg-white border text-dark fw-normal"><i class="ri-smartphone-line me-1 text-primary"></i>E-wallet</span>
                                </div>
                                <input type="hidden" id="gatewayPaymentMethodId" value="<?= $_gatewayMethod['id'] ?>">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Manual Payment Section -->
                    <div id="manualPaymentOptions" style="display: <?= $_gatewayMethod ? 'none' : 'block' ?>;">
                        <!-- Hidden select kept for JS/form compat -->
                        <select class="d-none" id="paymentMethod" name="paymentMethod"></select>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Select Payment Method <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-2" id="manualMethodCards">
                                <?php if (isset($paymentMethods) && !empty($paymentMethods)): ?>
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <?php if (isset($method['type']) && $method['type'] == 'manual' && isset($method['is_active']) && $method['is_active'] == 1): ?>
                                        <div>
                                            <div class="manual-method-card"
                                                data-id="<?= $method['id'] ?>"
                                                data-name="<?= esc($method['name']) ?>"
                                                data-description="<?= isset($method['description']) ? esc($method['description']) : '' ?>"
                                                data-img-url="<?= isset($method['img_url']) ? esc($method['img_url']) : '' ?>">
                                                <?php if (!empty($method['img_url'])): ?>
                                                <img src="<?= esc($method['img_url']) ?>" alt="<?= esc($method['name']) ?>" class="manual-method-card-img" onerror="this.style.display='none'">
                                                <?php else: ?>
                                                <div class="manual-method-card-icon"><i class="ri-money-dollar-box-line"></i></div>
                                                <?php endif; ?>
                                                <div class="manual-method-card-body">
                                                    <div class="fw-semibold"><?= esc($method['name']) ?></div>
                                                </div>
                                                <i class="ri-check-line manual-method-card-check"></i>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                        </div>

                        <!-- Manual Payment Fields -->
                    <div id="manualPaymentFields" class="payment-method-fields mt-3" style="display: none;">
                            <div class="mb-3">
                                <div class="alert alert-info py-2">
                                    <p class="fw-medium mb-1 small text-uppercase">Instructions</p>
                                    <p id="manualInstructions" class="mb-0 small"></p>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="account_name" class="form-label">Account Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Name on your account" required>
                                    <div class="invalid-feedback">Please enter the account name</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="source_name" class="form-label">Bank / Source <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="source_name" name="source_name" placeholder="e.g. BCA, Mandiri, PayPal" required>
                                    <div class="invalid-feedback">Please enter the source name</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" required max="<?= date('Y-m-d') ?>">
                                <div class="invalid-feedback">Please select a valid payment date (today or earlier)</div>
                            </div>
                            <div class="mb-3">
                                <label for="manualProof" class="form-label">Payment Proof <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="manualProof" name="proof_url" accept="image/jpeg,image/png,image/jpg,application/pdf" required max-size="5120">
                                <div class="form-text"><i class="ri-information-line me-1"></i>JPG, PNG, or PDF — max 5 MB</div>
                                <div class="invalid-feedback" id="filesize-error">The file is too large. Maximum allowed size is 5 MB.</div>
                                <div class="invalid-feedback" id="filetype-error">Only JPG, PNG, and PDF files are allowed.</div>
                            </div>
                            <div class="mb-1">
                                <label for="notes" class="form-label">Additional Notes <span class="text-muted fw-normal">(optional)</span></label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional information about your payment"></textarea>
                            </div>
                            <div class="alert alert-info py-2 mt-3 mb-0">
                                <i class="ri-time-line me-1"></i>
                                After submitting, your proof will be reviewed by our team. You'll receive a confirmation within <strong>1–3 business days</strong>.
                            </div>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <!-- Modal Footer — outside modal-body so it's always anchored at the bottom -->
                <div class="modal-footer border-top bg-light px-4 flex-column align-items-stretch">
                    <!-- "What happens next" hint — updates per payment type -->
                    <p class="text-muted small text-center mb-2 w-100" id="payNextStepHint">
                        <i class="ri-arrow-right-circle-line me-1 text-primary"></i>
                        <span id="payNextStepText"><?= $_gatewayMethod ? "You'll be redirected to our secure payment gateway to complete the transaction." : "We'll review your proof within 1–3 business days and notify you by email." ?></span>
                    </p>
                    <div class="d-flex gap-2 justify-content-end w-100">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4" id="paySubmitBtn">
                            <i class="ri-secure-payment-line me-1"></i>
                            <span id="paySubmitText"><?= $_gatewayMethod ? 'Pay' : 'Submit Payment' ?></span>
                            <span class="ms-1 fw-bold" id="paySubmitAmount">
                                <?php if (isset($selectedProgramPayment) && $selectedProgramPayment['usd_amount'] > 0): ?>
                                    $<?= number_format($selectedProgramPayment['usd_amount'], 2) ?>
                                <?php endif; ?>
                            </span>
                        </button>
                    </div>
                </div>

            </form><!-- /form -->
        </div>
    </div>
</div>

<style>
    /* ── Fix: form between modal-content and modal-body breaks scrollable modal flex chain ── */
    #makePaymentModal .modal-content > form {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden;
    }

    /* ── Payment summary card ── */
    .payment-summary-card {
        background: linear-gradient(135deg, #f0f3ff 0%, #f8f9ff 100%);
        border: 1px solid rgba(64, 81, 137, 0.15);
    }

    .ls-1 { letter-spacing: 0.04em; }

    /* ── Payment type cards ── */
    .payment-type-card {
        border: 2px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease, color 0.15s ease;
        user-select: none;
    }
    .payment-type-card .payment-type-icon { color: #adb5bd; transition: color 0.15s ease; }
    .payment-type-card h6 { color: #6c757d; transition: color 0.15s ease; }
    .payment-type-card small { color: #adb5bd; }

    .payment-type-card.active {
        border-color: #405189;
        background-color: rgba(64, 81, 137, 0.06);
    }
    .payment-type-card.active .payment-type-icon { color: #405189; }
    .payment-type-card.active h6 { color: #405189; }
    .payment-type-card.active small { color: #405189; opacity: 0.7; }

    .payment-type-card:hover:not(.active) {
        border-color: #adb5bd;
        background-color: #f8f9fa;
    }
    .payment-type-card:hover:not(.active) .payment-type-icon { color: #6c757d; }
    .payment-type-card:hover:not(.active) h6 { color: #495057; }

    /* ── Gateway info box ── */
    .gateway-info-box {
        background-color: rgba(64, 81, 137, 0.05);
        border: 1px solid rgba(64, 81, 137, 0.2);
    }

    /* ── Manual payment method cards ── */
    .manual-method-card {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border: 2px solid #dee2e6;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
        background: #fff;
        white-space: nowrap;
    }
    .manual-method-card:hover:not(.active) {
        border-color: #adb5bd;
        background-color: #f8f9fa;
    }
    .manual-method-card.active {
        border-color: #405189;
        background-color: rgba(64, 81, 137, 0.06);
    }
    .manual-method-card-img {
        width: 40px;
        height: 40px;
        object-fit: contain;
        border-radius: 6px;
        flex-shrink: 0;
    }
    .manual-method-card-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: #f0f3ff;
        color: #405189;
        font-size: 20px;
        flex-shrink: 0;
    }
    .manual-method-card-body { flex: 1; min-width: 0; }
    .manual-method-card-check {
        font-size: 18px;
        color: #405189;
        opacity: 0;
        transition: opacity 0.15s ease;
        flex-shrink: 0;
    }
    .manual-method-card.active .manual-method-card-check { opacity: 1; }
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
                    
                    // Check file type and size limit to match backend validation
                    const fileInput = document.getElementById('manualProof');
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                        const typeErrorDiv = document.getElementById('filetype-error');

                        if (!allowedTypes.includes(file.type)) {
                            console.log('File type is not allowed:', file.type);
                            if (typeErrorDiv) typeErrorDiv.style.display = 'block';
                            fileInput.setCustomValidity('Only JPG, PNG, and PDF files are allowed');
                            this.classList.add('was-validated');
                            return;
                        }

                        fileInput.setCustomValidity('');
                        if (typeErrorDiv) typeErrorDiv.style.display = 'none';

                        const fileSize = fileInput.files[0].size;
                        const maxSize = 5 * 1024 * 1024; // 5 MB in bytes

                        if (fileSize > maxSize) {
                            console.log('File size exceeds limit:', fileSize, 'bytes');
                            const errorDiv = document.getElementById('filesize-error');
                            if (errorDiv) errorDiv.style.display = 'block';
                            fileInput.setCustomValidity('File size exceeds the maximum limit of 5 MB');
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

                    console.log('=== PAYMENT MODAL DEBUG ===');
                    console.log('Modal triggered with payment ID:', paymentId);
                    console.log('Payment amount received:', paymentAmount);
                    console.log('Payment name:', paymentName);
                    console.log('Payment category:', paymentCategory);
                    console.log('Button element:', button);

                    // Calculate IDR amount - ensure paymentAmount is a valid number
                    const usdInIdr = <?= $webSettings['usd_in_idr'] ?>;
                    const usdAmount = parseFloat(paymentAmount) || 0;
                    const idrAmount = usdAmount * usdInIdr;
                    
                    console.log('USD Amount:', usdAmount, 'IDR Rate:', usdInIdr, 'IDR Amount:', idrAmount);
                    
                    // Set the IDR amount in the modal
                    const amountInIdrElement = document.getElementById('amount_in_idr');
                    if (amountInIdrElement) {
                        amountInIdrElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(idrAmount);
                    }

                    // Update submit button amount
                    const paySubmitAmount = document.getElementById('paySubmitAmount');
                    if (paySubmitAmount && usdAmount > 0) {
                        paySubmitAmount.textContent = '$' + usdAmount.toFixed(2);
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
                        console.log('Using parsed payment object:', selectedPayment);
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
                        console.log('Using individual attributes - ID:', paymentId, 'Amount:', paymentAmount, 'Name:', paymentName);
                        document.getElementById('program_payment_id').value = paymentId || '';
                        document.getElementById('payment_amount').value = paymentAmount || '0.00';
                        
                        const descElement = document.getElementById('payment_description');
                        if (descElement) {
                            descElement.textContent = paymentName || 'Program Payment';
                        }
                        
                        const amountElement = document.getElementById('payment_amount_display');
                        if (amountElement) {
                            const displayAmount = parseFloat(paymentAmount || 0).toFixed(2);
                            amountElement.textContent = '$' + displayAmount;
                            console.log('Set display amount to:', displayAmount);
                        }
                    }
                }

                // Set default payment type based on gateway availability
                const _hasGateway = <?= $_gatewayMethod ? 'true' : 'false' ?>;
                const paymentTypeSelect = document.getElementById('paymentType');
                const manualOptions = document.getElementById('manualPaymentOptions');
                const gatewayOptions = document.getElementById('gatewayPaymentOptions');
                const paySubmitTextReset = document.getElementById('paySubmitText');
                const payNextStepReset = document.getElementById('payNextStepText');

                if (_hasGateway) {
                    if (paymentTypeSelect) { paymentTypeSelect.value = 'gateway'; }
                    document.getElementById('payment_type').value = 'gateway';
                    document.querySelector('.gateway-help').classList.remove('d-none');
                    document.querySelector('.manual-help').classList.add('d-none');
                    if (manualOptions) manualOptions.style.display = 'none';
                    if (gatewayOptions) gatewayOptions.style.display = 'block';
                    document.querySelectorAll('#paymentTypeCards .payment-type-card').forEach(function(c) {
                        c.classList.toggle('active', c.getAttribute('data-type') === 'gateway');
                    });
                    if (paySubmitTextReset) paySubmitTextReset.textContent = 'Pay';
                    if (payNextStepReset) payNextStepReset.textContent = 'You\'ll be redirected to our secure payment gateway to complete the transaction.';
                    const _gId = document.getElementById('gatewayPaymentMethodId');
                    if (_gId && _gId.value) { document.getElementById('payment_method_id').value = _gId.value; }
                } else {
                    if (paymentTypeSelect) { paymentTypeSelect.value = 'manual'; }
                    document.getElementById('payment_type').value = 'manual';
                    document.querySelector('.gateway-help').classList.add('d-none');
                    document.querySelector('.manual-help').classList.remove('d-none');
                    if (manualOptions) manualOptions.style.display = 'block';
                    if (gatewayOptions) gatewayOptions.style.display = 'none';
                    if (paySubmitTextReset) paySubmitTextReset.textContent = 'Submit Payment';
                    if (payNextStepReset) payNextStepReset.textContent = 'We\'ll review your proof within 1–3 business days and notify you by email.';
                }

                // Reset manual method card selection
                document.querySelectorAll('#manualMethodCards .manual-method-card').forEach(function(c) {
                    c.classList.remove('active');
                });
                document.getElementById('payment_method_id').value = '';

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

                // Reset manual method card selection when switching type
                document.querySelectorAll('#manualMethodCards .manual-method-card').forEach(function(c) {
                    c.classList.remove('active');
                });
                document.getElementById('payment_method_id').value = '';

                // Hide all payment method fields
                document.querySelectorAll('.payment-method-fields').forEach(field => {
                    field.style.display = 'none';
                });
            });
        }

        // Manual payment method card click handler
        document.querySelectorAll('#manualMethodCards .manual-method-card').forEach(function(card) {
            card.addEventListener('click', function() {
                // Visual state
                document.querySelectorAll('#manualMethodCards .manual-method-card').forEach(function(c) {
                    c.classList.remove('active');
                });
                this.classList.add('active');

                const methodId = this.getAttribute('data-id');
                const methodName = this.getAttribute('data-name');
                const methodDesc = this.getAttribute('data-description');

                // Set hidden values
                document.getElementById('payment_method_id').value = methodId;

                // Show payment fields
                const manualFields = document.getElementById('manualPaymentFields');
                if (manualFields) manualFields.style.display = 'block';

                // Set instructions
                const instructions = document.getElementById('manualInstructions');
                if (instructions) {
                    if (methodDesc) {
                        instructions.innerHTML = methodDesc;
                    } else {
                        instructions.textContent = 'Please complete the payment using ' + methodName + ' and upload proof of your payment.';
                    }
                }
            });
        });

        // Set initial state based on gateway availability
        const gatewayMethodId = document.getElementById('gatewayPaymentMethodId');
        if (<?= $_gatewayMethod ? 'true' : 'false' ?>) {
            if (gatewayMethodId && gatewayMethodId.value) {
                document.getElementById('payment_method_id').value = gatewayMethodId.value;
            }
        } else {
            // No gateway available — default to manual
            document.getElementById('payment_type').value = 'manual';
            document.getElementById('gatewayPaymentOptions').style.display = 'none';
            document.getElementById('manualPaymentOptions').style.display = 'block';
            const paySubmitText = document.getElementById('paySubmitText');
            if (paySubmitText) paySubmitText.textContent = 'Submit Payment';
            const payNextStepText = document.getElementById('payNextStepText');
            if (payNextStepText) payNextStepText.textContent = 'We\'ll review your proof within 1–3 business days and notify you by email.';
        }
    });

    // ── Payment type card click handler ──────────────────────────────────────
    document.querySelectorAll('#paymentTypeCards .payment-type-card').forEach(function(card) {
        card.addEventListener('click', function() {
            // Visual state
            document.querySelectorAll('#paymentTypeCards .payment-type-card').forEach(function(c) {
                c.classList.remove('active');
            });
            this.classList.add('active');

            // Sync hidden select → triggers the existing paymentType 'change' handler
            const type = this.getAttribute('data-type');
            const hiddenSelect = document.getElementById('paymentType');
            if (hiddenSelect) {
                hiddenSelect.value = type;
                hiddenSelect.dispatchEvent(new Event('change'));
            }

            // Update submit button text
            const submitText = document.getElementById('paySubmitText');
            if (submitText) {
                submitText.textContent = type === 'manual' ? 'Submit Payment' : 'Pay';
            }

            // Update "What happens next" hint
            const nextStepText = document.getElementById('payNextStepText');
            if (nextStepText) {
                nextStepText.textContent = type === 'manual'
                    ? 'We\'ll review your proof within 1–3 business days and notify you by email.'
                    : 'You\'ll be redirected to our secure payment gateway to complete the transaction.';
            }
        });
    });
</script>

<?php include(__DIR__ . '/payment_modal_footer.php'); ?>