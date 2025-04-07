<?php
/**
 * Related Payments Widget
 * Displays a list of related payments with improved UI and grouping
 */

// Include helper functions
require_once(__DIR__ . '/../helpers/payment_helpers.php');

// Skip if no related payments
if (!isset($related_payments) || empty($related_payments)) {
    return;
}

// Group related payments by status
$groupedPayments = [
    'pending' => [],  // Pending and unpaid
    'paid' => [],     // Paid/completed
    'other' => []     // Other statuses
];

foreach ($related_payments as $rel_payment) {
    $status = $rel_payment['status'] ?? 0;
    
    if ($status == 2) {
        $groupedPayments['paid'][] = $rel_payment;
    } elseif ($status == 0 || $status == 1) {
        $groupedPayments['pending'][] = $rel_payment;
    } else {
        $groupedPayments['other'][] = $rel_payment;
    }
}

// Sort by date (if available)
$sortPaymentsByDate = function($a, $b) {
    if (!isset($a['due_date']) || !isset($b['due_date'])) {
        return 0;
    }
    return strtotime($a['due_date']) - strtotime($b['due_date']);
};

if (!empty($groupedPayments['pending'])) {
    usort($groupedPayments['pending'], $sortPaymentsByDate);
}
if (!empty($groupedPayments['paid'])) {
    usort($groupedPayments['paid'], $sortPaymentsByDate);
}
if (!empty($groupedPayments['other'])) {
    usort($groupedPayments['other'], $sortPaymentsByDate);
}

// Check if we have any related payments after grouping
$hasRelatedPayments = !empty($groupedPayments['pending']) || !empty($groupedPayments['paid']) || !empty($groupedPayments['other']);
?>

<?php if ($hasRelatedPayments): ?>
<div class="card border shadow-none mb-3">
    <div class="card-header bg-soft-info d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="ri-links-line me-1 align-middle"></i> Related Payments
            <span class="badge rounded-pill bg-soft-dark text-dark ms-1"><?= count($related_payments) ?></span>
        </h5>
        
        <!-- Dropdown for filtering related payments -->
        <div class="dropdown">
            <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" id="relatedFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ri-filter-3-line align-bottom me-1"></i> Filter
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="relatedFilterDropdown">
                <li><button class="dropdown-item filter-related active" data-filter="all">All Payments</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item filter-related" data-filter="pending">Pending</button></li>
                <li><button class="dropdown-item filter-related" data-filter="paid">Completed</button></li>
                <li><button class="dropdown-item filter-related" data-filter="other">Other</button></li>
            </ul>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Pending/Upcoming Payments Section -->
        <?php if (!empty($groupedPayments['pending'])): ?>
        <div class="related-payment-group pending-group mb-3">
            <h6 class="fs-14 mb-3 text-uppercase d-flex align-items-center">
                <span class="avatar-xs me-2">
                    <span class="avatar-title rounded-circle bg-warning-subtle text-warning">
                        <i class="ri-time-line"></i>
                    </span>
                </span>
                Pending Payments
            </h6>
            <div class="list-group">
                <?php foreach ($groupedPayments['pending'] as $payment): ?>
                    <?php 
                    $status = getPaymentStatusInfo($payment['status'] ?? 0);
                    $statusClass = $status['iconClass'];
                    $statusText = $status['statusText'];
                    $dueDate = isset($payment['end_date']) ? new DateTime($payment['end_date']) : null;
                    $isPastDue = $dueDate && $dueDate < new DateTime();
                    ?>
                    <a href="<?= site_url('participant/programPayment/details/' . $payment['id']) ?>" 
                       class="list-group-item list-group-item-action related-payment-item border-0 rounded mb-2 <?= $isPastDue ? 'bg-danger-subtle' : 'bg-light' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> rounded">
                                        <i class="<?= $status['icon'] ?>"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-reset fs-14">
                                        <?= htmlspecialchars($payment['name']) ?>
                                        <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> ms-1"><?= $statusText ?></span>
                                        <?php if ($isPastDue): ?>
                                            <span class="badge bg-danger-subtle text-danger ms-1">Overdue</span>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="mb-0 text-muted fs-13">
                                        <?php if ($dueDate): ?>
                                            <i class="ri-calendar-line align-middle me-1"></i> Due: <?= $dueDate->format('M d, Y') ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fs-15 fw-medium"><?= formatCurrency($payment['usd_amount'], $payment['currency'] ?? 'USD') ?></h6>
                                <span class="text-muted fs-13"><?= ucfirst($payment['category'] ?? 'Payment') ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Completed Payments Section -->
        <?php if (!empty($groupedPayments['paid'])): ?>
        <div class="related-payment-group paid-group mb-3">
            <h6 class="fs-14 mb-3 text-uppercase d-flex align-items-center">
                <span class="avatar-xs me-2">
                    <span class="avatar-title rounded-circle bg-success-subtle text-success">
                        <i class="ri-checkbox-circle-line"></i>
                    </span>
                </span>
                Completed Payments
            </h6>
            <div class="list-group">
                <?php foreach ($groupedPayments['paid'] as $payment): ?>
                    <?php 
                    $status = getPaymentStatusInfo($payment['status'] ?? 0);
                    $statusClass = $status['iconClass'];
                    $statusText = $status['statusText'];
                    ?>
                    <a href="<?= site_url('participant/programPayment/details/' . $payment['id']) ?>" 
                       class="list-group-item list-group-item-action related-payment-item border-0 rounded mb-2 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> rounded">
                                        <i class="<?= $status['icon'] ?>"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-reset fs-14">
                                        <?= htmlspecialchars($payment['name']) ?>
                                        <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> ms-1"><?= $statusText ?></span>
                                    </h6>
                                    <p class="mb-0 text-muted fs-13">
                                        <i class="ri-check-double-line align-middle me-1"></i> Paid on: 
                                        <?= isset($payment['paid_date']) ? date('M d, Y', strtotime($payment['paid_date'])) : 'Unknown' ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fs-15 fw-medium"><?= formatCurrency($payment['usd_amount'], $payment['currency'] ?? 'USD') ?></h6>
                                <span class="text-muted fs-13"><?= ucfirst($payment['category'] ?? 'Payment') ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Other Payments Section -->
        <?php if (!empty($groupedPayments['other'])): ?>
        <div class="related-payment-group other-group mb-3">
            <h6 class="fs-14 mb-3 text-uppercase d-flex align-items-center">
                <span class="avatar-xs me-2">
                    <span class="avatar-title rounded-circle bg-secondary-subtle text-secondary">
                        <i class="ri-file-list-3-line"></i>
                    </span>
                </span>
                Other Related Payments
            </h6>
            <div class="list-group">
                <?php foreach ($groupedPayments['other'] as $payment): ?>
                    <?php 
                    $status = getPaymentStatusInfo($payment['status'] ?? 0);
                    $statusClass = $status['iconClass'];
                    $statusText = $status['statusText'];
                    ?>
                    <a href="<?= site_url('participant/programPayment/details/' . $payment['id']) ?>" 
                       class="list-group-item list-group-item-action related-payment-item border-0 rounded mb-2 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> rounded">
                                        <i class="<?= $status['icon'] ?>"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-reset fs-14">
                                        <?= htmlspecialchars($payment['name']) ?>
                                        <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?> ms-1"><?= $statusText ?></span>
                                    </h6>
                                    <p class="mb-0 text-muted fs-13">
                                        <?php if (isset($payment['created_at'])): ?>
                                            <i class="ri-calendar-line align-middle me-1"></i> Created: <?= date('M d, Y', strtotime($payment['created_at'])) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fs-15 fw-medium"><?= formatCurrency($payment['usd_amount'], $payment['currency'] ?? 'USD') ?></h6>
                                <span class="text-muted fs-13"><?= ucfirst($payment['category'] ?? 'Payment') ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Empty State for Filtered Results -->
        <div id="empty-related-state" class="text-center py-5 d-none">
            <div class="avatar-md mx-auto mb-3">
                <div class="avatar-title bg-soft-warning text-warning rounded-circle fs-24">
                    <i class="ri-filter-off-line"></i>
                </div>
            </div>
            <h5>No matching related payments</h5>
            <p class="text-muted mb-0">Try changing your filter criteria</p>
            <button id="reset-related-filter" class="btn btn-sm btn-soft-primary mt-3">
                <i class="ri-refresh-line me-1"></i> Show All Payments
            </button>
        </div>
        
        <!-- Total Related Payments Summary -->
        <div class="border-top pt-3 mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fs-14 mb-0">Total Related Payments</h6>
                    <p class="text-muted mb-0 fs-13"><?= count($related_payments) ?> payment(s)</p>
                </div>
                <?php
                // Calculate total of all related payments
                $totalAmount = 0;
                foreach ($related_payments as $payment) {
                    if (isset($payment['usd_amount'])) {
                        $totalAmount += (float)$payment['usd_amount'];
                    }
                }
                ?>
                <div class="text-end">
                    <h5 class="fs-16 fw-medium mb-0"><?= formatCurrency($totalAmount, $payment['currency'] ?? 'USD') ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript for filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-related');
    const relatedGroups = document.querySelectorAll('.related-payment-group');
    const emptyState = document.getElementById('empty-related-state');
    const resetFilter = document.getElementById('reset-related-filter');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            let visibleCount = 0;
            
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            relatedGroups.forEach(group => {
                if (filter === 'all' || group.classList.contains(filter + '-group')) {
                    group.style.display = 'block';
                    visibleCount++;
                } else {
                    group.style.display = 'none';
                }
            });
            
            // Show/hide empty state
            if (visibleCount === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        });
    });
    
    // Reset filter button
    if (resetFilter) {
        resetFilter.addEventListener('click', function() {
            relatedGroups.forEach(group => group.style.display = 'block');
            emptyState.classList.add('d-none');
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
<?php endif; ?>