<?php

/**
 * Payment Modal Widget
 * Reusable modal for making payments with various payment methods                           <div class="mb-3">
 */
?>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-labelledby="makePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="makePaymentModalLabel">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= site_url('payments/make'); ?>" method="post" id="paymentForm" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="program_payment_id" id="program_payment_id" value="<?= isset($selectedProgramPayment) ? $selectedProgramPayment['id'] : '' ?>">
                    <input type="hidden" name="amount" id="payment_amount" value="<?= isset($selectedProgramPayment) ? $selectedProgramPayment['usd_amount'] : '' ?>">
                    <input type="hidden" name="paymentType" id="payment_type" value="gateway">
                    <input type="hidden" name="payment_method_id" id="payment_method_id" value="">
                    <?php if (isset($selectedProgramPayment)): ?>
                        <input type="hidden" name="paymentName" value="<?= esc($selectedProgramPayment['name']) ?>">
                        <?php if (isset($selectedProgramPayment['category'])): ?>
                            <input type="hidden" name="paymentCategory" value="<?= esc($selectedProgramPayment['category']) ?>">
                        <?php endif; ?>
                    <?php endif; ?><!-- Program Payment Details Section -->
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
                            <label for="paymentMethod" class="form-label fw-medium">Payment Method</label> <select class="form-select" id="paymentMethod" name="paymentMethod">
                                <option value="">Select Payment Method</option>
                                <?php if (isset($paymentMethods) && !empty($paymentMethods)): ?>
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <?php if (isset($method['type']) && $method['type'] == 'manual'): ?>
                                            <option value="<?= $method['id'] ?>"
                                                data-description="<?= isset($method['description']) ? esc($method['description']) : '' ?>">
                                                <?= esc($method['name']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div> <!-- Manual Payment Fields -->
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
                                <input type="file" class="form-control" id="manualProof" name="proof_url" accept="image/*" required>
                                <div class="form-text">Upload a photo of your receipt or payment proof</div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional information about your payment"></textarea>
                            </div>
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

<!-- Payment Modal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(event) {
                const paymentType = document.getElementById('payment_type').value;

                // Only validate manual payment fields if payment type is manual
                if (paymentType === 'manual') {
                    if (!this.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        this.classList.add('was-validated');
                        return;
                    }

                    // Add validation classes to show error messages
                    this.classList.add('was-validated');
                }                // If form is valid, show loading indicator
                if (this.checkValidity()) {
                    event.preventDefault();
                    
                    // Close the modal first
                    let paymentModal = bootstrap.Modal.getInstance(document.getElementById('makePaymentModal'));
                    paymentModal.hide();
                    
                    // Show loading indicator
                    Swal.fire({
                        title: 'Processing Payment',
                        html: 'Please wait while we process your payment...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit the form after showing loading indicator
                    setTimeout(() => {
                        this.submit();
                    }, 500);
                }
            });
        }

        // Payment modal data
        const makePaymentModal = document.getElementById('makePaymentModal');
        if (makePaymentModal) {
            makePaymentModal.addEventListener('show.bs.modal', function(event) {
                // Check if we have a button that triggered this modal                // If not, assume we're using pre-populated data from selectedProgramPayment
                if (event.relatedTarget) {
                    const button = event.relatedTarget;
                    const paymentId = button.getAttribute('data-payment-id');
                    const paymentData = button.getAttribute('data-payment-object');
                    let selectedPayment = null;

                    // Try parsing the full payment object if available
                    if (paymentData) {
                        try {
                            selectedPayment = JSON.parse(paymentData);
                        } catch (e) {
                            console.error('Error parsing payment object:', e);
                        }
                    }

                    // Fallback to programPayments array if available
                    if (!selectedPayment && typeof programPayments !== 'undefined') {
                        const paymentIndex = button.getAttribute('data-payment-index');
                        if (paymentIndex) {
                            selectedPayment = programPayments[paymentIndex] || null;
                        }
                    }

                    if (selectedPayment) {
                        // Set data from the payment object
                        document.getElementById('program_payment_id').value = selectedPayment.id;
                        document.getElementById('payment_amount').value = selectedPayment.usd_amount;

                        if (document.getElementById('payment_description')) {
                            document.getElementById('payment_description').textContent = selectedPayment.name || 'Program Payment';
                        }

                        if (document.getElementById('payment_amount_display')) {
                            document.getElementById('payment_amount_display').textContent = '$' + parseFloat(selectedPayment.usd_amount).toFixed(2);
                        }
                    } else {
                        // Final fallback to individual attributes
                        document.getElementById('program_payment_id').value = paymentId;
                        document.getElementById('payment_amount').value = button.getAttribute('data-payment-amount');

                        if (document.getElementById('payment_description')) {
                            document.getElementById('payment_description').textContent = button.getAttribute('data-payment-description') || 'Program Payment';
                        }

                        if (document.getElementById('payment_amount_display')) {
                            const amount = button.getAttribute('data-payment-amount') || '0.00';
                            document.getElementById('payment_amount_display').textContent = '$' + parseFloat(amount).toFixed(2);
                        }
                    }

                    // Only update the description if it's empty
                    const descElement = document.getElementById('payment_description');
                    if (descElement && !descElement.innerText.trim()) {
                        descElement.textContent = paymentDescription || 'Program Payment';
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

                // Reset payment type to gateway by default
                const paymentTypeSelect = document.getElementById('paymentType');
                if (paymentTypeSelect) {
                    paymentTypeSelect.value = 'gateway';
                    document.getElementById('payment_type').value = 'gateway';
                }

                // Update help text visibility
                document.querySelector('.gateway-help').classList.remove('d-none');
                document.querySelector('.manual-help').classList.add('d-none');

                // Hide manual payment options by default
                const manualOptionsSection = document.getElementById('manualPaymentOptions');
                if (manualOptionsSection) {
                    manualOptionsSection.style.display = 'none';
                }

                // Reset payment method selection
                const paymentMethodSelect = document.getElementById('paymentMethod');
                if (paymentMethodSelect) {
                    paymentMethodSelect.selectedIndex = 0;
                    paymentMethodSelect.required = false; // Not required for gateway payment
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
                if (manualOptionsSection) {
                    manualOptionsSection.style.display = selectedType === 'manual' ? 'block' : 'none';
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
        } // Payment method fields toggle
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
        } // Function to filter payment methods based on selected type
        function filterPaymentMethodsByType(type) {
            const paymentMethodSelect = document.getElementById('paymentMethod');
            if (!paymentMethodSelect) return;

            // Hide or show the payment method section based on type
            const paymentMethodSection = document.getElementById('paymentMethodSection');

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

        // Initialize the payment type on page load
        filterPaymentMethodsByType('gateway');
    });
</script>