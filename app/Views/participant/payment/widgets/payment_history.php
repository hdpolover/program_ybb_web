<?php

/**
 * Payment History Widget
 * Displays a timeline of payment actions/history
 * 
 * Enhanced with improved UI, filters and user-friendly features
 */

// Use centralized helper functions
require_once(__DIR__ . '/../helpers/payment_helpers.php');
?>

<!-- Payment History Section -->
<?php if (isset($payments) && !empty($payments)): ?>
    <div class="card border shadow-none mb-4">
        <div class="card-header bg-light d-flex align-items-center">
            <div class="flex-grow-1">
                <h5 class="card-title mb-0 text-primary">
                    <i class="ri-history-line me-1 align-middle"></i> Payment History
                    <span class="badge rounded-pill bg-soft-dark text-dark ms-1"><?= count($payments) ?></span>
                </h5>
            </div>
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" id="historyFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-filter-3-line align-bottom me-1"></i> Filter
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="historyFilterDropdown">
                    <li><button class="dropdown-item filter-history active" data-filter="all">All Activities</button></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><button class="dropdown-item filter-history" data-filter="created">Created</button></li>
                    <li><button class="dropdown-item filter-history" data-filter="pending">Pending</button></li>
                    <li><button class="dropdown-item filter-history" data-filter="paid">Completed</button></li>
                    <li><button class="dropdown-item filter-history" data-filter="cancelled">Cancelled</button></li>
                    <li><button class="dropdown-item filter-history" data-filter="rejected">Rejected</button></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="acitivity-timeline py-2">
                <?php
                $sortedPayments = $payments;
                // Sort by created_at in descending order to show newest first
                usort($sortedPayments, function ($a, $b) {
                    return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
                });
                foreach ($sortedPayments as $index => $history):
                    $statusInfo = getPaymentStatusInfo($history['status'] ?? 0);
                    $historyId = isset($history['id']) ? $history['id'] : rand(1000, 9999);

                    // Map status values to filter classes
                    $status_value = $history['status'] ?? 0;
                    $status_class = '';

                    switch ($status_value) {
                        case 0:
                            $status_class = 'created';
                            break;
                        case 1:
                            $status_class = 'pending';
                            break;
                        case 2:
                            $status_class = 'paid';
                            break;
                        case 3:
                            $status_class = 'cancelled';
                            break;
                        case 4:
                            $status_class = 'rejected';
                            break;
                        default:
                            $status_class = 'other';
                    }
                ?>

                    <!-- Payment History Item -->
                    <div class="acitivity-item d-flex mb-4 payment-history-item <?= $status_class ?>-item status-<?= $status_value ?>">
                        <!-- Timeline Connector Line -->
                        <?php if ($index < count($sortedPayments) - 1): ?>
                            <div class="timeline-line" style="position: absolute; height: 100%; border-left: 1px dashed #ccc; left: 20px; top: 40px;"></div>
                        <?php endif; ?>

                        <div class="flex-shrink-0">
                            <div class="avatar-sm acitivity-avatar">
                                <div class="avatar-title rounded-circle bg-<?= $statusInfo['iconClass'] ?>-subtle text-<?= $statusInfo['iconClass'] ?> border border-<?= $statusInfo['iconClass'] ?>">
                                    <i class="<?= $statusInfo['icon'] ?> fs-20"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fs-16">
                                <?= $statusInfo['actionText'] ?>
                                <span class="badge bg-<?= $statusInfo['statusBadge'] ?>-subtle text-<?= $statusInfo['statusBadge'] ?> ms-1">
                                    <?= $statusInfo['statusText'] ?>
                                </span>
                            </h6>

                            <div class="d-flex align-items-center mb-2">
                                <?php if (!empty($history['payment_method_id']) && isset($paymentMethods)): ?>
                                    <span class="badge bg-light text-dark me-2">
                                        <i class="ri-bank-card-line me-1"></i>
                                        <?= esc(getPaymentMethodName($history['payment_method_id'], $paymentMethods)) ?>
                                    </span>
                                <?php endif; ?>

                                <span class="badge bg-light text-dark me-2">
                                    <i class="ri-money-dollar-circle-line me-1"></i>
                                    <?= (isset($history['usd_amount'])) ? formatCurrency($history['usd_amount'], 'USD') : formatCurrency($programPayment['usd_amount'], 'USD') ?>
                                </span>

                                <span class="text-muted small">
                                    <i class="ri-time-line me-1"></i>
                                    <?= isset($history['created_at']) && !empty($history['created_at'])
                                        ? date('M d, Y | h:i A', strtotime($history['created_at']))
                                        : date('M d, Y | h:i A') ?>
                                </span>
                            </div>

                            <?php if (!empty($history['notes'])): ?>
                                <div class="alert alert-info-subtle p-2 mb-2">
                                    <i class="ri-information-line me-1"></i> <?= $history['notes'] ?>
                                </div>
                            <?php endif; ?>

                            <!-- View Details Button -->
                            <button class="btn btn-sm btn-outline-primary mt-1" type="button" data-bs-toggle="collapse"
                                data-bs-target="#paymentDetails<?= $historyId ?>"
                                aria-expanded="false" aria-controls="paymentDetails<?= $historyId ?>">
                                <i class="ri-information-line align-middle"></i> View Details
                            </button> <!-- Download Receipt Button (if payment is successful) -->
                            <?php if (($history['status'] ?? 0) == 2): ?>
                                <a target="_blank" href="<?= site_url('payments/receipt/' . $history['id']); ?>" class="btn btn-sm btn-success receipt-button" title="Download Receipt">
                                    <i class="ri-download-2-line align-middle me-1"></i> Receipt
                                </a>
                            <?php endif; ?>

                            <!-- Collapsible Payment Details -->
                            <div class="collapse mt-3" id="paymentDetails<?= $historyId ?>">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0">Transaction Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="border rounded p-3">
                                                    <h6 class="fs-14 mb-2">Transaction Information</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-borderless mb-0">
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row" class="ps-0"><small>Transaction Code:</small></th>
                                                                    <td class="text-muted"><?= isset($history['transaction_code']) ? $history['transaction_code'] : ($historyId) ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row" class="ps-0"><small>Payment Method:</small></th>
                                                                    <td class="text-muted"><?= getPaymentMethodName($history['payment_method_id'] ?? null, $paymentMethods ?? []) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row" class="ps-0"><small>Date:</small></th>
                                                                    <td class="text-muted"><?= isset($history['created_at']) ? date('F d, Y h:i A', strtotime($history['created_at'])) : 'N/A' ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-12">
                                                <div class="border rounded p-3">
                                                    <h6 class="fs-14 mb-2">Payment Information</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-borderless mb-0">
                                                            <tbody>
                                                                <?php if (isset($history['account_name'])): ?>
                                                                    <tr>
                                                                        <th scope="row" class="ps-0"><small>Account Name:</small></th>
                                                                        <td class="text-muted"><?= $history['account_name']  ?></td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                                <tr>
                                                                    <th scope="row" class="ps-0"><small>Amount:</small></th>
                                                                    <td class="text-muted fw-medium">
                                                                        <?= isset($history['usd_amount']) ? formatCurrency($history['usd_amount'], 'USD') : formatCurrency($programPayment['usd_amount'], 'USD') ?>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row" class="ps-0"><small>Source:</small></th>
                                                                    <td class="text-muted"><?= isset($history['source_name']) ? $history['source_name'] : '-' ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (isset($history['proof_url']) && $history['proof_url']): ?>
                                                <div class="col-12">
                                                    <div class="border rounded p-3">
                                                        <h6 class="fs-14 mb-3">Payment Proof</h6>
                                                        <div class="text-center">
                                                            <img src="<?= $history['proof_url'] ?>" class="img-fluid rounded" style="max-height: 250px;" alt="Payment Proof">
                                                            <div class="mt-3">
                                                                <a href="<?= $history['proof_url'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                                                    <i class="ri-eye-line align-middle me-1"></i> View Full Image
                                                                </a>
                                                                <a href="<?= $history['proof_url'] ?>" target="_blank" download class="btn btn-sm btn-info ms-1">
                                                                    <i class="ri-download-line align-middle me-1"></i> Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (isset($history['status']) && $history['status'] == 4 && isset($history['rejection_reason'])): ?>
                                                <div class="col-12">
                                                    <div class="alert alert-danger mb-0">
                                                        <h6 class="alert-heading fs-14 mb-1">Rejection Reason:</h6>
                                                        <p class="mb-0"><?= $history['rejection_reason'] ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State for Filtered Results -->
            <div id="empty-filter-state" class="text-center py-5 d-none">
                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-soft-warning text-warning rounded-circle fs-24">
                        <i class="ri-filter-off-line"></i>
                    </div>
                </div>
                <h5>No matching payment history found</h5>
                <p class="text-muted mb-0">Try changing your filter criteria</p>
                <button id="reset-filter" class="btn btn-sm btn-soft-primary mt-3">
                    <i class="ri-refresh-line me-1"></i> Show All History
                </button>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-history');
            const historyItems = document.querySelectorAll('.payment-history-item');
            const emptyState = document.getElementById('empty-filter-state');
            const resetFilter = document.getElementById('reset-filter');
            const timelineContainer = document.querySelector('.acitivity-timeline');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    let visibleCount = 0;

                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    historyItems.forEach(item => {
                        if (filter === 'all' || item.classList.contains(filter + '-item')) {
                            item.style.display = 'flex';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Show/hide empty state and timeline container
                    if (visibleCount === 0) {
                        emptyState.classList.remove('d-none');
                        timelineContainer.classList.add('d-none');
                    } else {
                        emptyState.classList.add('d-none');
                        timelineContainer.classList.remove('d-none');
                    }
                });
            });

            // Reset filter button
            if (resetFilter) {
                resetFilter.addEventListener('click', function() {
                    historyItems.forEach(item => item.style.display = 'flex');
                    emptyState.classList.add('d-none');
                    timelineContainer.classList.remove('d-none');
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.dataset.filter === 'all') {
                            btn.classList.add('active');
                        }
                    });
                });
            }
        });
    </script>
<?php else: ?>
    <!-- Empty State when no payment history -->
    <div class="card border shadow-none mb-4">
        <div class="card-body text-center p-5">
            <div class="avatar-lg mx-auto mb-4">
                <div class="avatar-title bg-soft-info text-info rounded-circle fs-24">
                    <i class="ri-history-line"></i>
                </div>
            </div>
            <h5>No Payment History Found</h5>
            <p class="text-muted mb-4">There is no payment history available for this payment yet.</p>
            <button type="button" class="btn btn-sm btn-success payment-button" data-bs-toggle="modal" data-bs-target="#makePaymentModal"
                data-payment-id="<?= $programPayment['id'] ?? ''; ?>"
                data-payment-name="<?= esc($programPayment['name'] ?? 'Program Payment'); ?>"
                data-payment-amount="<?= number_format((float)($programPayment['usd_amount'] ?? 0), 2, '.', ''); ?>"
                data-payment-category="<?= esc($programPayment['category'] ?? ''); ?>"
                data-payment-object="<?= esc(json_encode($programPayment)); ?>"
                title="Make First Payment">
                <i class="ri-bank-card-line align-middle me-1"></i> Make First Payment
            </button>
        </div>
    </div>
<?php endif; ?>