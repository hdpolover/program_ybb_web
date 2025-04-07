<?php
/**
 * Payment Information Widget
 * Displays the basic payment details in a modern card layout with enhanced visual presentation
 */

// Include helper functions
require_once(__DIR__ . '/../helpers/payment_helpers.php');

// Get payment status
$statusInfo = isset($programPayment['status']) ? getPaymentStatusInfo($programPayment['status']) : [];
$statusClass = isset($statusInfo['iconClass']) ? $statusInfo['iconClass'] : 'primary';
?>

<div class="card border shadow-none mb-4">
    <div class="card-header d-flex align-items-center bg-light">
        <h5 class="card-title mb-0 flex-grow-1">
            <i class="ri-bank-card-line me-1 align-middle"></i> Payment Information
        </h5>
        <?php if (isset($programPayment['status'])): ?>
            <div class="flex-shrink-0">
                <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> px-3 py-2">
                    <i class="<?= $statusInfo['icon'] ?? 'ri-information-line' ?> me-1"></i>
                    <?= $statusInfo['statusText'] ?? 'Unknown' ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Payment Info -->
            <div class="col-md-8">
                <div class="p-3 border rounded bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <i class="ri-price-tag-3-line text-muted fs-16 me-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-0 text-uppercase fs-12">Payment Name</h6>
                                    <p class="mb-0 fs-15"><?= isset($programPayment['name']) ? $programPayment['name'] : 'Program Payment' ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <i class="ri-folders-line text-muted fs-16 me-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-0 text-uppercase fs-12">Category</h6>
                                    <p class="mb-0 fs-15"><?= isset($programPayment['category']) ? $programPayment['category'] : 'Category' ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <i class="ri-money-dollar-circle-line text-muted fs-16 me-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-0 text-uppercase fs-12">Amount</h6>
                                    <p class="mb-0 fs-15 fw-medium">
                                        <?= isset($programPayment['usd_amount']) ? formatCurrency($programPayment['usd_amount'], $programPayment['currency'] ?? 'USD') : '$0.00' ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <i class="ri-money-pound-circle-line text-muted fs-16 me-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-0 text-uppercase fs-12">Currency</h6>
                                    <p class="mb-0 fs-15"><?= isset($programPayment['currency']) ? strtoupper($programPayment['currency']) : 'USD' ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <i class="ri-calendar-check-line text-muted fs-16 me-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-0 text-uppercase fs-12">Due Date</h6>
                                    <p class="mb-0 fs-15">
                                        <?php if (isset($programPayment['end_date'])): ?>
                                            <?= date('F d, Y', strtotime($programPayment['end_date'])) ?>
                                            <?php 
                                            $today = new DateTime();
                                            $dueDate = new DateTime($programPayment['end_date']);
                                            $interval = $today->diff($dueDate);
                                            if ($dueDate < $today): ?>
                                                <span class="badge bg-danger-subtle text-danger ms-1">Overdue</span>
                                            <?php elseif ($interval->days <= 7): ?>
                                                <span class="badge bg-warning-subtle text-warning ms-1">Due soon</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <i class="ri-calendar-event-line text-muted fs-16 me-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-0 text-uppercase fs-12">Created Date</h6>
                                    <p class="mb-0 fs-15">
                                        <?= isset($programPayment['created_at']) ? date('F d, Y', strtotime($programPayment['created_at'])) : date('F d, Y') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php if(isset($programPayment['description']) && !empty($programPayment['description'])): ?>
                        <div class="col-12 mt-2">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-file-text-line text-muted fs-16 me-1 mt-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-1 text-uppercase fs-12">Description</h6>
                                    <p class="mb-0 fs-14"><?= $programPayment['description'] ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Payment Summary Card -->
            <div class="col-md-4">
                <div class="card bg-light border-0 mb-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Payment Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span class="fw-medium"><?= isset($programPayment['usd_amount']) ? formatCurrency($programPayment['usd_amount'], $programPayment['currency'] ?? 'USD') : '$0.00' ?></span>
                            </div>
                            
                            <?php 
                            // Display fee if available
                            if (isset($programPayment['fee']) && $programPayment['fee'] > 0): 
                                $feeAmount = $programPayment['fee'];
                            ?>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Fee:</span>
                                <span class="fw-medium"><?= formatCurrency($feeAmount, $programPayment['currency'] ?? 'USD') ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php 
                            // Display discount if available
                            if (isset($programPayment['discount']) && $programPayment['discount'] > 0): 
                                $discountAmount = $programPayment['discount'];
                            ?>
                            <div class="d-flex justify-content-between text-success mb-3">
                                <span>Discount:</span>
                                <span class="fw-medium">-<?= formatCurrency($discountAmount, $programPayment['currency'] ?? 'USD') ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between border-top pt-3">
                                <span class="fw-medium">Total:</span>
                                <?php
                                $totalAmount = isset($programPayment['usd_amount']) ? (float)$programPayment['usd_amount'] : 0;
                                $totalAmount = $totalAmount + (isset($feeAmount) ? (float)$feeAmount : 0) - (isset($discountAmount) ? (float)$discountAmount : 0);
                                ?>
                                <span class="fw-bold fs-18"><?= formatCurrency($totalAmount, $programPayment['currency'] ?? 'USD') ?></span>
                            </div>
                        </div>
                        
                        <?php
                        // Calculate payment progress
                        $paidAmount = 0;
                        $paymentComplete = false;
                        
                        if (isset($payments) && !empty($payments)) {
                            foreach ($payments as $payment) {
                                if (isset($payment['status']) && $payment['status'] == 2 && isset($payment['amount'])) {
                                    $paidAmount += (float)$payment['amount'];
                                    if ($paidAmount >= $totalAmount) {
                                        $paymentComplete = true;
                                    }
                                }
                            }
                        }
                        
                        $remainingAmount = max(0, $totalAmount - $paidAmount);
                        $progressPercent = $totalAmount > 0 ? min(100, round(($paidAmount / $totalAmount) * 100)) : 0;
                        $progressClass = $paymentComplete ? 'bg-success' : ($progressPercent > 50 ? 'bg-info' : 'bg-warning');
                        ?>
                        
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h6 class="fs-14 mb-0">Payment Progress</h6>
                                <span class="badge bg-<?= $progressClass ?>-subtle text-<?= $progressClass ?>"><?= $progressPercent ?>%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar <?= $progressClass ?>" role="progressbar" style="width: <?= $progressPercent ?>%" 
                                     aria-valuenow="<?= $progressPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            
                            <?php if (!$paymentComplete): ?>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-muted small">Remaining:</span>
                                <span class="fw-medium"><?= formatCurrency($remainingAmount, $programPayment['currency'] ?? 'USD') ?></span>
                            </div>
                            <?php else: ?>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-success small">
                                    <i class="ri-checkbox-circle-line align-bottom"></i> Fully Paid
                                </span>
                                <span class="text-success fw-medium"><?= formatCurrency($paidAmount, $programPayment['currency'] ?? 'USD') ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>