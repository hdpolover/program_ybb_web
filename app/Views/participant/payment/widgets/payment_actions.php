<?php

/**
 * Payment Actions Widget
 * Displays action buttons and payment status based on the payment state
 * Enhanced with more actionable features and better UI
 */

// Include helper functions
require_once(__DIR__ . '/../helpers/payment_helpers.php');

// Get payment status
$paymentStatus = getPaymentStatus($payments ?? []);
$paymentCompleted = $paymentStatus['completed'];
$latestPayment = $paymentStatus['latestPayment'];
?>

<div class="card border shadow-none mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Payment Actions</h5>
    </div>
    <div class="card-body">
        <?php if ($paymentCompleted): ?>
            <!-- Completed Payment UI -->
            <div class="mb-4 text-center">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-success-subtle text-success rounded-circle display-5">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                </div>
                <h5 class="fs-16 mb-2">Payment Completed</h5>
                <p class="text-muted mb-4">Thank you for your payment. Your transaction has been completed successfully.</p>

                <!-- Quick Action Buttons -->
                <div class="d-grid gap-2">
                    <a target="_blank" href="<?= site_url('payments/receipt/' . $programPayment['id']); ?>" class="btn btn-success">
                        <i class="ri-download-2-line align-middle me-1"></i> Download Receipt
                    </a>
                </div>
            </div>

            <!-- Next Payment Option (if applicable) -->
            <?php if (isset($related_payments) && !empty($related_payments)): ?>
                <?php
                $hasUnpaidRelatedPayment = false;
                $nextPayment = null;

                foreach ($related_payments as $payment) {
                    // Find the first unpaid or pending payment
                    if (isset($payment['status']) && ($payment['status'] == 0 || $payment['status'] == 1)) {
                        $hasUnpaidRelatedPayment = true;
                        $nextPayment = $payment;
                        break;
                    }
                }

                if ($hasUnpaidRelatedPayment && $nextPayment):
                ?>
                    <div class="alert alert-info mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-information-line fs-18"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="alert-heading mb-1">Next Payment Due</h6>
                                <p class="mb-2 fs-13">You have another payment due: <strong><?= $nextPayment['name'] ?></strong></p>
                                <a href="<?= site_url('payments/detail/' . $nextPayment['id']); ?>" class="btn btn-sm btn-soft-info">View Payment</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        <?php elseif ($latestPayment && isset($latestPayment['status'])): ?>
            <?php if ($latestPayment['status'] == 0 || $latestPayment['status'] == 'created' || $latestPayment['status'] == 'unpaid'): ?>
                <!-- Payment Required UI -->
                <div class="mb-4">
                    <div class="text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <div class="avatar-title bg-warning-subtle text-warning rounded-circle display-5">
                                <i class="ri-bank-card-line"></i>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-2">Payment Required</h5>
                        <p class="text-muted mb-4">This payment requires your attention.</p>

                        <?php
                        // Check if due date is approaching
                        $isDueSoon = false;
                        $isOverdue = false;

                        if (isset($programPayment['end_date'])) {
                            $today = new DateTime();
                            $dueDate = new DateTime($programPayment['end_date']);
                            $interval = $today->diff($dueDate);

                            if ($dueDate < $today) {
                                $isOverdue = true;
                            } elseif ($interval->days <= 7) {
                                $isDueSoon = true;
                            }
                        }
                        ?>

                        <?php if ($isOverdue): ?>
                            <div class="alert alert-danger mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-error-warning-line fs-18"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="alert-heading mb-1">Payment Overdue</h6>
                                        <p class="mb-0 fs-13">This payment is past the due date. Please make your payment as soon as possible.</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($isDueSoon): ?>
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-timer-line fs-18"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="alert-heading mb-1">Due Soon</h6>
                                        <p class="mb-0 fs-13">This payment is due within 7 days. Please make your payment soon.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Methods Section -->
                    <?php if (isset($paymentMethods) && !empty($paymentMethods)): ?>
                        <div class="mb-4">
                            <h6 class="fs-14 mb-3">Select Payment Method:</h6>
                            <div class="d-grid gap-2">
                                <?php foreach ($paymentMethods as $index => $method): ?>
                                    <a href="<?= site_url('participant/programPayment?pay=' . (isset($programPayment['id']) ? $programPayment['id'] : '') . '&method=' . $method['id']); ?>" class="btn btn-outline-primary position-relative payment-method-btn">
                                        <div class="d-flex align-items-center">
                                            <?php
                                            // Define icons for different payment methods
                                            $methodIcon = 'ri-bank-card-line';
                                            $methodName = strtolower($method['name'] ?? '');

                                            if (strpos($methodName, 'paypal') !== false) {
                                                $methodIcon = 'ri-paypal-line';
                                            } elseif (strpos($methodName, 'bank') !== false || strpos($methodName, 'transfer') !== false) {
                                                $methodIcon = 'ri-bank-line';
                                            } elseif (strpos($methodName, 'credit') !== false || strpos($methodName, 'card') !== false) {
                                                $methodIcon = 'ri-bank-card-line';
                                            } elseif (strpos($methodName, 'cash') !== false) {
                                                $methodIcon = 'ri-money-dollar-box-line';
                                            }
                                            ?>
                                            <i class="<?= $methodIcon ?> fs-18 me-2"></i>
                                            <span><?= esc($method['name']) ?></span>
                                        </div>
                                        <?php if ($index === 0): ?>
                                            <span class="badge bg-success position-absolute top-0 end-0 translate-middle-y me-2">Recommended</span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-grid">
                            <button type="button" class="btn btn-sm btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                data-payment-index="<?= $programPayment; ?>"
                                title="Make Payment">
                                <i class="ri-bank-card-line align-middle me-1"></i> Make Payment
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif ($latestPayment['status'] == 1 || $latestPayment['status'] == 'pending'): ?>
                <!-- Payment Processing UI -->
                <div class="mb-4 text-center">
                    <div class="avatar-md mx-auto mb-3">
                        <div class="avatar-title bg-warning-subtle text-warning rounded-circle display-5">
                            <i class="ri-time-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-16 mb-2">Payment Processing</h5>
                    <div class="alert alert-warning mb-3">
                        <p class="mb-0">Your payment is being processed. Contact administrator if your payment is not completed within 1x12 hours.</p>
                    </div>

                    <!-- Processing animation -->
                    <div class="mb-4">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Refresh button -->
                    <button type="button" class="btn btn-soft-primary" onclick="window.location.reload();">
                        <i class="ri-refresh-line align-middle me-1"></i> Refresh Status
                    </button>
                </div> <?php elseif ($latestPayment['status'] == 3 || $latestPayment['status'] == 'cancelled'): ?>
                <?php
                        // Check if payment is overdue
                        $isOverdue = false;
                        if (isset($programPayment['end_date'])) {
                            $today = new DateTime();
                            $dueDate = new DateTime($programPayment['end_date']);
                            if ($dueDate < $today) {
                                $isOverdue = true;
                            }
                        }
                ?>
                <!-- Payment Cancelled UI -->
                <div class="mb-4 text-center">
                    <div class="avatar-md mx-auto mb-3">
                        <div class="avatar-title bg-danger-subtle text-danger rounded-circle display-5">
                            <i class="ri-close-circle-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-16 mb-2">Payment Cancelled</h5>
                    <div class="alert alert-danger mb-3">
                        <p class="mb-0">This payment has been cancelled. Contact support for more information.</p>
                    </div>
                    <div class="d-grid gap-2"> <?php if (!$isOverdue): ?>
                            <button type="button" class="btn btn-sm btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                data-payment-amount="<?= $programPayment['usd_amount'] ?? ''; ?>"
                                data-payment-description="<?= $programPayment['name'] ?? ''; ?>"
                                title="Make Payment">
                                <i class="ri-bank-card-line align-middle me-1"></i> Try Again
                            </button>

                        <?php endif; ?>
                        <a href="<?= site_url('participant/support/programPayment/' . (isset($programPayment['id']) ? $programPayment['id'] : '')) ?>" class="btn btn-outline-danger">
                            <i class="ri-customer-service-2-line align-middle me-1"></i> Contact Support
                        </a>
                    </div>
                </div> <?php elseif ($latestPayment['status'] == 4 || $latestPayment['status'] == 'rejected'): ?>
                <?php
                        // Check if payment is overdue
                        $isOverdue = false;
                        if (isset($programPayment['end_date'])) {
                            $today = new DateTime();
                            $dueDate = new DateTime($programPayment['end_date']);
                            if ($dueDate < $today) {
                                $isOverdue = true;
                            }
                        }
                ?>
                <!-- Payment Rejected UI -->
                <div class="mb-4 text-center">
                    <div class="avatar-md mx-auto mb-3">
                        <div class="avatar-title bg-danger-subtle text-danger rounded-circle display-5">
                            <i class="ri-close-circle-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-16 mb-2">Payment Rejected</h5>
                    <div class="alert alert-danger mb-3">
                        <p class="mb-0">Your payment was rejected. Please try a different payment method.</p>
                        <?php if (isset($latestPayment['rejection_reason']) && !empty($latestPayment['rejection_reason'])): ?>
                            <hr>
                            <h6 class="alert-heading mb-1">Reason:</h6>
                            <p class="mb-0"><?= $latestPayment['rejection_reason'] ?></p>
                        <?php endif; ?>
                    </div> <?php if (!$isOverdue): ?>
                        <div class="d-grid">
                            <button type="button" class="btn btn-sm btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                data-payment-amount="<?= $programPayment['usd_amount'] ?? ''; ?>"
                                data-payment-description="<?= $programPayment['name'] ?? ''; ?>"
                                title="Make Payment">
                                <i class="ri-bank-card-line align-middle me-1"></i> Try Again
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary mb-0">
                            <p class="mb-0">This payment is overdue and can no longer be processed online. Please contact support for assistance.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?> <!-- Default Payment Required UI -->
            <div class="mb-4 text-center">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-warning-subtle text-warning rounded-circle display-5">
                        <i class="ri-bank-card-line"></i>
                    </div>
                </div>
                <h5 class="fs-16 mb-2">Payment Required</h5>
                <p class="text-muted mb-4">This payment requires your attention.</p>

                <?php
                // Check if payment is overdue
                $isOverdue = false;
                if (isset($programPayment['end_date'])) {
                    $today = new DateTime();
                    $dueDate = new DateTime($programPayment['end_date']);
                    if ($dueDate < $today) {
                        $isOverdue = true;
                    }
                }
                ?>

                <?php if (!$isOverdue): ?>
                    <button type="button" class="btn btn-lg btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                        data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                        data-payment-amount="<?= $programPayment['usd_amount'] ?? ''; ?>"
                        data-payment-description="<?= $programPayment['name'] ?? ''; ?>"
                        title="Make Payment">
                        <i class="ri-bank-card-line align-middle me-1"></i> Make Payment
                    </button>
                <?php else: ?>
                    <div class="alert alert-danger mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-error-warning-line fs-18"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="alert-heading mb-1">Payment Overdue</h6>
                                <p class="mb-0 fs-13">This payment is past the due date and can no longer be processed online. Please contact support for assistance.</p>
                            </div>
                        </div>
                    </div>
                    <a href="<?= site_url('participant/support/programPayment/' . (isset($programPayment['id']) ? $programPayment['id'] : '')) ?>" class="btn btn-danger btn-lg w-100">
                        <i class="ri-customer-service-2-line align-middle me-1"></i> Contact Support
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <hr class="my-4">

        <!-- Additional Actions -->
        <h6 class="fs-14 mb-3">Quick Actions</h6>
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-primary btn-print-details">
                <i class="ri-printer-line align-middle me-1"></i> Print Details
            </button>

            <!-- Share Payment Link (if not completed) -->
            <!-- <?php if (!$paymentCompleted): ?>
                <button type="button" class="btn btn-soft-info" data-bs-toggle="modal" data-bs-target="#sharePaymentModal">
                    <i class="ri-share-line align-middle me-1"></i> Share Payment Link
                </button>
            <?php endif; ?> -->

            <!-- Contact Administrator Button -->
            <div class="dropdown">
                <button class="btn btn-info dropdown-toggle w-100" type="button" id="contactAdminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-admin-line align-middle me-1"></i>Contact Administrator
                </button>
                <ul class="dropdown-menu w-100" aria-labelledby="contactAdminDropdown">
                    <li>
                        <a class="dropdown-item" href="mailto:<?= isset($webSettings['email']) ? $webSettings['email'] : 'admin@example.com' ?>" target="_blank">
                            <i class="ri-mail-line me-2"></i> Via Email
                        </a>
                    </li>
                    <?php
                    $whatsappNumber = isset($webSettings['contact']) ? $webSettings['contact'] : '1234567890';

                    // remove plus from numbers
                    $cleanedNumbers = preg_replace('/[^0-9]/', '', $whatsappNumber);

                    $message = urlencode('Hello, I need assistance with my payment.');
                    $whatsappUrl = "https://api.whatsapp.com/send?phone={$cleanedNumbers}&text={$message}";
                    ?>
                    <li>
                        <a class="dropdown-item" href="<?= $whatsappUrl ?>" target="_blank">
                            <i class="ri-whatsapp-line me-2"></i> Via WhatsApp
                        </a>
                    </li>
                </ul>
            </div>

            <!-- <a href="<?= site_url('participant/support/programPayment/' . (isset($programPayment['id']) ? $programPayment['id'] : '')) ?>" class="btn btn-soft-danger">
                <i class="ri-question-line align-middle me-1"></i> Need Help?
            </a> -->
        </div>
    </div>
</div>

<!-- Payment Schedule Section (if installments) -->
<?php
// Check if payment is part of an installment plan
$isInstallmentPayment = isset($programPayment['is_installment']) && $programPayment['is_installment'];
$installmentCount = isset($programPayment['installment_count']) ? $programPayment['installment_count'] : 0;

if ($isInstallmentPayment && $installmentCount > 0):
?>
    <div class="card border shadow-none mb-3">
        <div class="card-header bg-soft-info">
            <h5 class="card-title mb-0">
                <i class="ri-calendar-check-line me-1 align-middle"></i> Payment Schedule
            </h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <?php
                // Example installment schedule - replace with actual data
                $installmentTotal = isset($programPayment['usd_amount']) ? (float)$programPayment['usd_amount'] : 0;
                $installmentAmount = $installmentTotal / $installmentCount;
                $startDate = isset($programPayment['created_at']) ? new DateTime($programPayment['created_at']) : new DateTime();

                for ($i = 0; $i < $installmentCount; $i++):
                    // Calculate installment date
                    $installmentDate = clone $startDate;
                    $installmentDate->modify('+' . ($i * 30) . ' days');

                    // Determine status (past, current, upcoming)
                    $today = new DateTime();
                    $isPast = $installmentDate < $today;
                    $isNext = !$isPast && ($i === 0 || (isset($lastWasPast) && $lastWasPast));
                    $lastWasPast = $isPast;

                    $statusClass = $isPast ? 'bg-success-subtle text-success' : ($isNext ? 'bg-warning-subtle text-warning' : 'bg-light text-muted');
                    $statusText = $isPast ? 'Paid' : ($isNext ? 'Next Payment' : 'Upcoming');
                    $installmentFormattedAmount = formatCurrency($installmentAmount, $programPayment['currency'] ?? 'USD');
                ?>
                    <li class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fs-14 mb-1">Installment #<?= $i + 1 ?></h6>
                                <p class="text-muted mb-0">Due: <?= $installmentDate->format('F d, Y') ?></p>
                            </div>
                            <div class="text-end">
                                <p class="mb-1 fw-medium"><?= $installmentFormattedAmount ?></p>
                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </div>
                        </div>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- Share Payment Modal -->
<div class="modal fade" id="sharePaymentModal" tabindex="-1" aria-labelledby="sharePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sharePaymentModalLabel">Share Payment Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Share this payment link with others who need to make this payment:</p>
                <div class="input-group mb-3">
                    <?php
                    $shareUrl = site_url('participant/programPayment?pay=' . (isset($programPayment['id']) ? $programPayment['id'] : ''));
                    ?>
                    <input type="text" class="form-control" id="paymentLinkInput" value="<?= $shareUrl ?>" readonly>
                    <button class="btn btn-primary" type="button" id="copyLinkBtn" onclick="copyPaymentLink()">Copy</button>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <a href="https://api.whatsapp.com/send?text=<?= urlencode('Please complete this payment: ' . $shareUrl) ?>" class="btn btn-soft-success mx-1" target="_blank">
                        <i class="ri-whatsapp-line fs-16"></i>
                    </a>
                    <a href="mailto:?subject=<?= urlencode('Payment Link') ?>&body=<?= urlencode('Please complete this payment: ' . $shareUrl) ?>" class="btn btn-soft-primary mx-1">
                        <i class="ri-mail-line fs-16"></i>
                    </a>
                    <a href="sms:?body=<?= urlencode('Please complete this payment: ' . $shareUrl) ?>" class="btn btn-soft-info mx-1">
                        <i class="ri-message-2-line fs-16"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy link script -->
<script>
    function copyPaymentLink() {
        var copyText = document.getElementById("paymentLinkInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        var copyLinkBtn = document.getElementById("copyLinkBtn");
        copyLinkBtn.innerHTML = "Copied!";
        copyLinkBtn.classList.remove("btn-primary");
        copyLinkBtn.classList.add("btn-success");

        setTimeout(function() {
            copyLinkBtn.innerHTML = "Copy";
            copyLinkBtn.classList.remove("btn-success");
            copyLinkBtn.classList.add("btn-primary");
        }, 2000);
    }
</script>