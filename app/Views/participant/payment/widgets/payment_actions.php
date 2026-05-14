<?php

/**
 * Payment Actions Widget
 * Displays action buttons and payment status based on the payment state
 * Enhanced with payment timing validation and category access control
 */

// Include helper functions
require_once(__DIR__ . '/../helpers/payment_helpers.php');

// Get payment status
$paymentStatus = getPaymentStatus($payments ?? []);
$paymentCompleted = $paymentStatus['completed'];
$latestPayment = $paymentStatus['latestPayment'];

// Get participant category and payment access validation
$participantCategory = $participantCategory ?? 'self_funded';
$paymentType = $programPayment['type'] ?? 'all';

// Check if participant can make payment for this payment type
$canMakePayment = ($paymentType === 'all') || ($paymentType === $participantCategory);

// Category mismatch messages
$categoryMismatchMessage = '';
if (!$canMakePayment) {
    if ($paymentType === 'fully_funded' && $participantCategory === 'self_funded') {
        $categoryMismatchMessage = 'This payment is only available for fully funded participants';
    } elseif ($paymentType === 'self_funded' && $participantCategory === 'fully_funded') {
        $categoryMismatchMessage = 'This payment is only available for self funded participants';
    } else {
        $categoryMismatchMessage = 'This payment is only available for ' . $paymentType . ' participants';
    }
}

// Check if participant has payment history (access through history even if category doesn't match)
$hasPaymentHistory = !empty($payments);

// Grant access if they can make payment OR have payment history
$hasAccess = $canMakePayment || $hasPaymentHistory;

// Payment timing validation
$currentDate = new DateTime();
$startDate = new DateTime($programPayment['start_date']);
$endDate = new DateTime($programPayment['end_date']);

$paymentHasStarted = $currentDate >= $startDate;
$paymentIsActive = $currentDate >= $startDate && $currentDate <= $endDate;
$paymentHasEnded = $currentDate > $endDate;

// Calculate days until start (if not started yet)
$daysUntilStart = 0;
$comingSoonMessage = '';
if (!$paymentHasStarted) {
    $interval = $currentDate->diff($startDate);
    $daysUntilStart = $interval->days;
    if ($daysUntilStart == 0) {
        $comingSoonMessage = 'Payment opens today at ' . $startDate->format('H:i');
    } elseif ($daysUntilStart == 1) {
        $comingSoonMessage = 'Payment opens tomorrow (' . $startDate->format('M d, Y') . ')';
    } else {
        $comingSoonMessage = 'Payment opens in ' . $daysUntilStart . ' days (' . $startDate->format('M d, Y') . ')';
    }
}
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
                    <?php if (isset($latestPayment) && !empty($latestPayment['id'])): ?>
                        <a target="_blank" href="<?= site_url('payments/receipt/' . $latestPayment['id']); ?>" class="btn btn-success">
                            <i class="ri-download-2-line align-middle me-1"></i> Download Receipt
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-success" disabled>
                            <i class="ri-download-2-line align-middle me-1"></i> Receipt Not Available
                        </button>
                    <?php endif; ?>
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
                <!-- Payment Required UI with timing and access validation -->
                <div class="mb-4">
                    <div class="text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <div class="avatar-title bg-warning-subtle text-warning rounded-circle display-5">
                                <i class="ri-bank-card-line"></i>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-2">Payment Required</h5>
                        <p class="text-muted mb-4">This payment requires your attention.</p>

                        <?php if (!$paymentHasStarted): ?>
                            <!-- Payment hasn't started yet -->
                            <div class="alert alert-info mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-time-line fs-18"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="alert-heading mb-1">Payment Coming Soon</h6>
                                        <p class="mb-0 fs-13"><?= htmlspecialchars($comingSoonMessage); ?></p>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-info btn-lg w-100" disabled>
                                <i class="ri-time-line align-middle me-1"></i> 
                                <?php if ($daysUntilStart == 0): ?>
                                    Opens Today
                                <?php elseif ($daysUntilStart == 1): ?>
                                    Opens Tomorrow
                                <?php else: ?>
                                    Opens in <?= $daysUntilStart ?> day<?= $daysUntilStart > 1 ? 's' : '' ?>
                                <?php endif; ?>
                            </button>
                        <?php elseif ($paymentHasEnded): ?>
                            <!-- Payment period has ended -->
                            <div class="alert alert-danger mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-error-warning-line fs-18"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="alert-heading mb-1">Payment Expired</h6>
                                        <p class="mb-0 fs-13">This payment period ended on <?= $endDate->format('M d, Y H:i'); ?> and can no longer be processed online. Please contact support for assistance.</p>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= site_url('participant/support/programPayment/' . (isset($programPayment['id']) ? $programPayment['id'] : '')) ?>" class="btn btn-danger btn-lg w-100">
                                <i class="ri-customer-service-2-line align-middle me-1"></i> Contact Support
                            </a>
                        <?php elseif (!$hasAccess): ?>
                            <!-- Payment is active but participant doesn't have access -->
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-lock-line fs-18"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="alert-heading mb-1">Access Restricted</h6>
                                        <p class="mb-0 fs-13"><?= htmlspecialchars($categoryMismatchMessage); ?></p>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                <i class="ri-lock-line align-middle me-1"></i> Restricted
                            </button>
                        <?php else: ?>
                            <!-- Payment is active and participant has access -->
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

                            <!-- Show payment history access notice if applicable -->
                            <?php if (!$canMakePayment && $hasPaymentHistory): ?>
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-history-line fs-18"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="alert-heading mb-1">Payment History Access</h6>
                                            <p class="mb-0 fs-13">You can view this payment because you have payment history for it, even though it's for <?= ucfirst($paymentType); ?> participants.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Payment Methods Section -->
                            <?php if (isset($paymentMethods) && !empty($paymentMethods)): ?>
                                <?php if ($canMakePayment && $paymentHasStarted && !$paymentHasEnded): ?>
                                    <!-- Payment is active and participant has access -->
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
                                    <!-- Payment methods not available due to timing or access restrictions -->
                                    <div class="mb-4">
                                        <h6 class="fs-14 mb-3">Payment Methods:</h6>
                                        <div class="alert alert-secondary">
                                            <p class="mb-0">
                                                <i class="ri-information-line me-1"></i>
                                                <?php if (!$paymentHasStarted): ?>
                                                    Payment methods will be available when the payment period starts.
                                                <?php elseif ($paymentHasEnded): ?>
                                                    Payment methods are no longer available as the payment period has ended.
                                                <?php elseif (!$canMakePayment): ?>
                                                    Payment methods are restricted for your participant category.
                                                <?php else: ?>
                                                    Payment methods are currently unavailable.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- No specific payment methods, use modal approach with timing validation -->
                                <?php if ($canMakePayment && $paymentHasStarted && !$paymentHasEnded): ?>
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-lg btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                            data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                            data-payment-name="<?= esc($programPayment['name'] ?? 'Program Payment'); ?>"
                                            data-payment-amount="<?= number_format((float)($programPayment['usd_amount'] ?? 0), 2, '.', ''); ?>"
                                            data-payment-category="<?= esc($programPayment['category'] ?? ''); ?>"
                                            data-payment-object="<?= esc(json_encode($programPayment)); ?>"
                                            title="Make Payment">
                                            <i class="ri-bank-card-line align-middle me-1"></i> Make Payment
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <!-- Payment not available due to timing or access restrictions -->
                                    <div class="d-grid">
                                        <?php if (!$paymentHasStarted): ?>
                                            <button type="button" class="btn btn-lg btn-info" disabled>
                                                <i class="ri-time-line align-middle me-1"></i> 
                                                <?php if ($daysUntilStart == 0): ?>
                                                    Opens Today
                                                <?php elseif ($daysUntilStart == 1): ?>
                                                    Opens Tomorrow
                                                <?php else: ?>
                                                    Opens in <?= $daysUntilStart ?> day<?= $daysUntilStart > 1 ? 's' : '' ?>
                                                <?php endif; ?>
                                            </button>
                                        <?php elseif ($paymentHasEnded): ?>
                                            <button type="button" class="btn btn-lg btn-secondary" disabled>
                                                <i class="ri-time-line align-middle me-1"></i> Payment Expired
                                            </button>
                                        <?php elseif (!$canMakePayment): ?>
                                            <button type="button" class="btn btn-lg btn-secondary" disabled>
                                                <i class="ri-lock-line align-middle me-1"></i> Restricted
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-lg btn-secondary" disabled>
                                                <i class="ri-alert-line align-middle me-1"></i> Unavailable
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($latestPayment['status'] == 1 || $latestPayment['status'] == 'pending'): ?> <!-- Payment Processing UI -->
                <div class="mb-4 text-center">
                    <div class="avatar-md mx-auto mb-3">
                        <div class="avatar-title bg-warning-subtle text-warning rounded-circle display-5">
                            <i class="ri-time-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-16 mb-2">Payment In Progress</h5>
                    <div class="alert alert-warning mb-3">
                        <p class="mb-2">We're waiting for your payment to be confirmed by the gateway.</p>
                        <p class="mb-0 small"><i class="ri-time-line me-1"></i>If your payment isn't confirmed within <strong>24 hours</strong>, please contact support.</p>
                    </div>

                    <!-- Processing animation -->
                    <div class="mb-4">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="d-grid gap-2">
                        <?php if (!empty($latestPayment['payment_url'])): ?>
                            <div class="alert alert-info py-2 mb-2 text-start">
                                <i class="ri-information-line me-1"></i>Closed the payment page? Click below to reopen it.
                            </div>
                            <button type="button" class="btn btn-warning" onclick="openPaymentGateway('<?= esc($latestPayment['payment_url']) ?>');">
                                <i class="ri-bank-card-line align-middle me-1"></i> Continue Payment
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-soft-primary" onclick="window.location.reload();">
                            <i class="ri-refresh-line align-middle me-1"></i> Refresh Status
                        </button>
                        <button type="button" class="btn btn-outline-danger"
                            onclick="confirmCancelPayment(<?= (int)$latestPayment['id'] ?>);">
                            <i class="ri-close-circle-line align-middle me-1"></i> Cancel Payment
                        </button>
                    </div>
                </div><?php elseif ($latestPayment['status'] == 3 || $latestPayment['status'] == 'cancelled'): ?>
                <!-- Payment Cancelled UI with timing and access validation -->
                <div class="mb-4 text-center">
                    <div class="avatar-md mx-auto mb-3">
                        <div class="avatar-title bg-danger-subtle text-danger rounded-circle display-5">
                            <i class="ri-close-circle-line"></i>
                        </div>
                    </div>
                    <h5 class="fs-16 mb-2">Payment Cancelled</h5>
                    <?php if ($paymentIsActive && $canMakePayment): ?>
                        <div class="alert alert-warning mb-3">
                            <p class="mb-0">Your previous payment was cancelled. You can try again — the payment period is still open.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger mb-3">
                            <p class="mb-0">This payment was cancelled. If you believe this is an error, please contact support.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$paymentHasStarted): ?>
                        <!-- Payment hasn't started yet -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-time-line fs-18"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="alert-heading mb-1">Payment Coming Soon</h6>
                                    <p class="mb-0 fs-13"><?= htmlspecialchars($comingSoonMessage); ?></p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info btn-lg w-100" disabled>
                            <i class="ri-time-line align-middle me-1"></i> 
                            <?php if ($daysUntilStart == 0): ?>
                                Opens Today
                            <?php elseif ($daysUntilStart == 1): ?>
                                Opens Tomorrow
                            <?php else: ?>
                                Opens in <?= $daysUntilStart ?> day<?= $daysUntilStart > 1 ? 's' : '' ?>
                            <?php endif; ?>
                        </button>
                    <?php elseif ($paymentHasEnded): ?>
                        <!-- Payment period has ended -->
                        <div class="alert alert-secondary mb-0">
                            <p class="mb-0">This payment is overdue and can no longer be processed online. Please contact support for assistance.</p>
                        </div>
                    <?php elseif (!$canMakePayment): ?>
                        <!-- Payment is active but participant cannot make payments due to category restrictions -->
                        <div class="alert alert-warning mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-lock-line fs-18"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="alert-heading mb-1">Access Restricted</h6>
                                    <p class="mb-0 fs-13"><?= htmlspecialchars($categoryMismatchMessage); ?></p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="ri-lock-line align-middle me-1"></i> Restricted
                        </button>
                    <?php else: ?>
                        <!-- Payment is active and participant can make payments - allow retry -->
                        <div class="d-grid gap-2"> 
                            <button type="button" class="btn btn-sm btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                data-payment-name="<?= esc($programPayment['name'] ?? 'Program Payment'); ?>"
                                data-payment-amount="<?= number_format((float)($programPayment['usd_amount'] ?? 0), 2, '.', ''); ?>"
                                data-payment-category="<?= esc($programPayment['category'] ?? ''); ?>"
                                data-payment-object="<?= esc(json_encode($programPayment)); ?>"
                                title="Try Payment Again">
                                <i class="ri-bank-card-line align-middle me-1"></i> Try Again
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2 mt-3">
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
                        <p class="mb-0">Your payment was rejected. Please try making a payment again and review why it was rejected.</p>
                        <?php if (isset($latestPayment['rejection_reason']) && !empty($latestPayment['rejection_reason'])): ?>
                            <hr>
                            <h6 class="alert-heading mb-1">Reason:</h6>
                            <p class="mb-0"><?= $latestPayment['rejection_reason'] ?></p>
                        <?php endif; ?>
                    </div> 
                    
                    <?php if (!$paymentHasStarted): ?>
                        <!-- Payment hasn't started yet -->
                        <div class="alert alert-info mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-time-line fs-18"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="alert-heading mb-1">Payment Coming Soon</h6>
                                    <p class="mb-0 fs-13"><?= htmlspecialchars($comingSoonMessage); ?></p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info btn-lg w-100" disabled>
                            <i class="ri-time-line align-middle me-1"></i> 
                            <?php if ($daysUntilStart == 0): ?>
                                Opens Today
                            <?php elseif ($daysUntilStart == 1): ?>
                                Opens Tomorrow
                            <?php else: ?>
                                Opens in <?= $daysUntilStart ?> day<?= $daysUntilStart > 1 ? 's' : '' ?>
                            <?php endif; ?>
                        </button>
                    <?php elseif ($paymentHasEnded): ?>
                        <!-- Payment period has ended -->
                        <div class="alert alert-secondary mb-0">
                            <p class="mb-0">This payment is overdue and can no longer be processed online. Please contact support for assistance.</p>
                        </div>
                    <?php elseif (!$canMakePayment): ?>
                        <!-- Payment is active but participant cannot make payments due to category restrictions -->
                        <div class="alert alert-warning mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-lock-line fs-18"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="alert-heading mb-1">Access Restricted</h6>
                                    <p class="mb-0 fs-13"><?= htmlspecialchars($categoryMismatchMessage); ?></p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="ri-lock-line align-middle me-1"></i> Restricted
                        </button>
                    <?php else: ?>
                        <!-- Payment is active and participant has access - allow retry -->
                        <div class="d-grid">
                            <button type="button" class="btn btn-md btn-danger payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                data-payment-id="<?= $programPayment['id']; ?>"
                                data-payment-name="<?= esc($programPayment['name'] ?? 'Program Payment'); ?>"
                                data-payment-amount="<?= number_format((float)($programPayment['usd_amount'] ?? 0), 2, '.', ''); ?>"
                                data-payment-category="<?= esc($programPayment['category'] ?? ''); ?>"
                                data-payment-object="<?= esc(json_encode($programPayment)); ?>"
                                title="Try Payment Again">
                                <i class="ri-refresh-line align-middle me-1"></i> Try Again
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?> 
            <!-- Default Payment Required UI with timing and access validation -->
            <div class="mb-4 text-center">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-warning-subtle text-warning rounded-circle display-5">
                        <i class="ri-bank-card-line"></i>
                    </div>
                </div>
                <h5 class="fs-16 mb-2">Payment Required</h5>
                <p class="text-muted mb-4">This payment requires your attention.</p>

                <?php if (!$paymentHasStarted): ?>
                    <!-- Payment hasn't started yet -->
                    <div class="alert alert-info mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-time-line fs-18"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="alert-heading mb-1">Payment Coming Soon</h6>
                                <p class="mb-0 fs-13"><?= htmlspecialchars($comingSoonMessage); ?></p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-info btn-lg w-100" disabled>
                        <i class="ri-time-line align-middle me-1"></i> 
                        <?php if ($daysUntilStart == 0): ?>
                            Opens Today
                        <?php elseif ($daysUntilStart == 1): ?>
                            Opens Tomorrow
                        <?php else: ?>
                            Opens in <?= $daysUntilStart ?> day<?= $daysUntilStart > 1 ? 's' : '' ?>
                        <?php endif; ?>
                    </button>
                <?php elseif ($paymentHasEnded): ?>
                    <!-- Payment period has ended -->
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
                <?php elseif (!$hasAccess): ?>
                    <!-- Payment is active but participant doesn't have access -->
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="ri-lock-line fs-18"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="alert-heading mb-1">Access Restricted</h6>
                                <p class="mb-0 fs-13"><?= htmlspecialchars($categoryMismatchMessage); ?></p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                        <i class="ri-lock-line align-middle me-1"></i> Restricted
                    </button>
                <?php else: ?>
                    <!-- Payment is active and participant has access -->
                    
                    <!-- Show payment history access notice if applicable -->
                    <?php if (!$canMakePayment && $hasPaymentHistory): ?>
                        <div class="alert alert-info mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-history-line fs-18"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="alert-heading mb-1">Payment History Access</h6>
                                    <p class="mb-0 fs-13">You can view this payment because you have payment history for it, even though it's for <?= ucfirst($paymentType); ?> participants.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$canMakePayment): ?>
                        <!-- Participant has history but can't make new payments due to category mismatch -->
                        <button type="button" class="btn btn-lg btn-secondary w-100" disabled>
                            <i class="ri-lock-line align-middle me-1"></i> Cannot Make New Payments
                        </button>
                        <small class="text-muted d-block mt-2">You can view this payment due to payment history, but cannot make new payments for <?= ucfirst($paymentType); ?> participants.</small>
                    <?php else: ?>
                        <!-- Participant can make payment - check if payment is currently active -->
                        <?php if (!$paymentHasStarted): ?>
                            <!-- Payment hasn't started yet - show coming soon -->
                            <button type="button" class="btn btn-lg btn-info w-100" disabled>
                                <i class="ri-time-line align-middle me-1"></i> 
                                <?php if ($daysUntilStart == 0): ?>
                                    Opens Today
                                <?php elseif ($daysUntilStart == 1): ?>
                                    Opens Tomorrow
                                <?php else: ?>
                                    Opens in <?= $daysUntilStart ?> day<?= $daysUntilStart > 1 ? 's' : '' ?>
                                <?php endif; ?>
                            </button>
                            <small class="text-muted d-block mt-2"><?= htmlspecialchars($comingSoonMessage); ?></small>
                        <?php elseif ($paymentHasEnded): ?>
                            <!-- Payment period has ended - show expired -->
                            <button type="button" class="btn btn-lg btn-secondary w-100" disabled>
                                <i class="ri-time-line align-middle me-1"></i> Payment Expired
                            </button>
                            <small class="text-muted d-block mt-2">Payment period ended on <?= $endDate->format('M d, Y H:i'); ?></small>
                        <?php else: ?>
                            <!-- Payment is active and participant has access - allow payment -->
                            <button type="button" class="btn btn-lg btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                                data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                                data-payment-name="<?= esc($programPayment['name'] ?? 'Program Payment'); ?>"
                                data-payment-amount="<?= number_format((float)($programPayment['usd_amount'] ?? 0), 2, '.', ''); ?>"
                                data-payment-category="<?= esc($programPayment['category'] ?? ''); ?>"
                                data-payment-object="<?= esc(json_encode($programPayment)); ?>"
                                title="Make Payment">
                                <i class="ri-bank-card-line align-middle me-1"></i> Make Payment
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
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

    // Function to open payment gateway in new tab
    function openPaymentGateway(url) {
        if (!url) {
            Swal.fire({
                title: 'Error',
                text: 'Payment URL is not available. Please contact support.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Try to open payment URL in new tab
        const newTab = window.open(url, '_blank', 'noopener');

        if (!newTab || newTab.closed || typeof newTab.closed === 'undefined') {
            // Popup was blocked — show manual link
            Swal.fire({
                title: 'Payment Gateway',
                html: `
                    <div class="text-center">
                        <p>Your browser may have blocked the payment gateway from opening.</p>
                        <p class="mb-3">Please click the button below to open the payment gateway:</p>
                        <a href="${url}" target="_blank" class="btn btn-primary mb-2">Open Payment Gateway</a>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-secondary copy-payment-url" 
                                    onclick="copyToClipboard('${url}')">
                                <i class="ri-clipboard-line"></i> Copy Link
                            </button>
                            <span id="copy-success" class="d-none text-success ms-2">
                                <i class="ri-check-line"></i> Copied!
                            </span>
                        </div>
                        <p class="text-muted small mt-3">For the best experience, we recommend using Google Chrome.</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } else {
            // Tab opened — show "come back and check status" guidance
            Swal.fire({
                title: 'Payment In Progress',
                html: `
                    <div class="text-center">
                        <p>The payment gateway has been opened in a new tab.</p>
                        <p class="mb-0">Complete your payment there, then come back and click <strong>Check Payment Status</strong> to see if it was confirmed.</p>
                        <div class="mt-3">
                            <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ri-external-link-line me-1"></i>Reopen gateway tab
                            </a>
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Check Payment Status',
                cancelButtonText: 'Done — Refresh Page',
                allowOutsideClick: true,
            }).then((result) => {
                if (result.isConfirmed || result.dismiss === Swal.DismissReason.cancel || result.dismiss === Swal.DismissReason.backdrop) {
                    window.location.reload();
                }
            });
        }
    }

    // Cancel a pending payment with SweetAlert confirmation
    function confirmCancelPayment(paymentId) {
        Swal.fire({
            title: 'Cancel Payment?',
            text: 'Are you sure you want to cancel this payment? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, cancel it',
            cancelButtonText: 'Go back',
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Cancelling...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('<?= site_url('payments/cancel') ?>/' + paymentId, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Payment Cancelled',
                        text: 'Your payment has been cancelled successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                    }).then(() => { window.location.reload(); });
                } else {
                    Swal.fire({
                        title: 'Could Not Cancel',
                        text: data.message || 'Failed to cancel payment. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                });
            });
        });
    }

    // Function to copy text to clipboard
    function copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        // Show success message
        const successMsg = document.getElementById('copy-success');
        if (successMsg) {
            successMsg.classList.remove('d-none');
            setTimeout(() => {
                successMsg.classList.add('d-none');
            }, 2000);
        }
    }
</script>