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
            <div class="col-md-12">
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
                                    <p class="mb-0 fs-15">
                                        <?php
                                        $category = isset($programPayment['category']) ? $programPayment['category'] : '';
                                        $displayCategory = strtolower($category) === 'registration' ? 'Registration Fee' : 'Program Fee';
                                        echo '<span class="badge bg-info-subtle text-info p-2">' . $displayCategory . '</span>';
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($programPayment['description']) && !empty($programPayment['description'])): ?>
                            <div class="col-md-6 mt-2">
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
                                    <i class="ri-calendar-check-line text-muted fs-16 me-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="text-muted fw-semibold mb-0 text-uppercase fs-12">Due Date</h6>
                                    <p class="mb-0 fs-15">
                                        <?php if (isset($programPayment['end_date'])): ?>
                                            <?= date('F d, Y', strtotime($programPayment['end_date'])) ?>
                                            <?php
                                            $today = new DateTime();
                                            $today->setTime(0, 0, 0); // Set to midnight for full day comparison
                                            $dueDate = new DateTime($programPayment['end_date']);
                                            $dueDate->setTime(23, 59, 59); // Set to end of day
                                            $interval = $today->diff($dueDate);
                                            $daysRemaining = $interval->days;
                                            
                                            if ($dueDate < $today): ?>
                                                <span class="badge bg-danger-subtle text-danger ms-1">Overdue</span>
                                            <?php elseif ($interval->days <= 7): ?>
                                                <span class="badge bg-warning-subtle text-warning ms-1">Due soon (<?= $daysRemaining + 1 ?> days)</span>
                                            <?php else: ?>
                                                <span class="badge bg-info-subtle text-info ms-1"><?= $daysRemaining + 1 ?> days remaining</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Include the receipt handler script -->
<script src="/assets/js/pages/receipt-handler.js"></script>