<?php
/**
 * Payment Helper Functions
 * Centralized utility functions for payment-related operations
 */

/**
 * Get payment status information based on status code
 * 
 * @param mixed $status The payment status code or string
 * @return array Status information with icon, class, and text
 */
function getPaymentStatusInfo($status) {
    $iconClass = 'primary';
    $icon = 'ri-notification-2-line';
    $statusBadge = 'info';
    $statusText = 'Unknown';
    $actionText = 'Payment Created';

    switch ($status) {
        case 0:
        case 'created':
        case 'unpaid':
            $iconClass = 'info';
            $icon = 'ri-bank-card-2-line';
            $statusText = 'Created';
            $statusBadge = 'info';
            $actionText = 'Payment Created';
            break;
        case 1:
        case 'pending':
            $iconClass = 'warning';
            $icon = 'ri-calendar-todo-line';
            $statusText = 'Pending';
            $statusBadge = 'warning';
            $actionText = 'Payment Pending';
            break;
        case 2:
        case 'paid':
        case 'success':
            $iconClass = 'success';
            $icon = 'ri-checkbox-circle-fill';
            $statusText = 'Paid';
            $statusBadge = 'success';
            $actionText = 'Payment Completed';
            break;
        case 3:
        case 'cancelled':
            $iconClass = 'danger';
            $icon = 'ri-close-circle-line';
            $statusText = 'Cancelled';
            $statusBadge = 'danger';
            $actionText = 'Payment Cancelled';
            break;
        case 4:
        case 'rejected':
            $iconClass = 'danger';
            $icon = 'ri-close-circle-line';
            $statusText = 'Rejected';
            $statusBadge = 'danger';
            $actionText = 'Payment Rejected';
            break;
        default:
            $iconClass = 'secondary';
            $icon = 'ri-notification-2-line';
            $statusText = 'Unknown';
            $statusBadge = 'secondary';
            $actionText = 'Payment Created';
    }

    return [
        'iconClass' => $iconClass,
        'icon' => $icon,
        'statusText' => $statusText,
        'statusBadge' => $statusBadge,
        'actionText' => $actionText
    ];
}

/**
 * Get payment method name from ID
 * 
 * @param mixed $payment_method_id Payment method ID
 * @param array $paymentMethods Available payment methods
 * @return string Payment method name or fallback
 */
function getPaymentMethodName($payment_method_id, $paymentMethods) {
    $methodName = '';
    if (isset($paymentMethods) && !empty($payment_method_id)) {
        foreach ($paymentMethods as $method) {
            if (isset($method['id']) && $method['id'] == $payment_method_id) {
                $methodName = $method['name'];
                break;
            }
        }
    }
    
    return $methodName ?: (isset($payment_method_id) ? 'Method #' . $payment_method_id : 'N/A');
}

/**
 * Determine if payment is completed
 * 
 * @param array $payments Array of payment objects
 * @return array Payment status information
 */
function getPaymentStatus($payments) {
    $paymentCompleted = false;
    $latestPayment = null;

    // Check if payments exist and get status
    if (isset($payments) && !empty($payments)) {
        // Sort payments by created_at in descending order to get latest first
        usort($payments, function ($a, $b) {
            return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
        });

        // Check if any payment has status 2 (paid/completed)
        foreach ($payments as $payment) {
            if (isset($payment['status']) && $payment['status'] == 2) {
                $paymentCompleted = true;
                break;
            }
        }

        // Get the latest payment
        $latestPayment = $payments[0];
    }

    return [
        'completed' => $paymentCompleted,
        'latestPayment' => $latestPayment
    ];
}

/**
 * Format currency amount
 * 
 * @param mixed $amount Amount to format
 * @param string $currency Currency code
 * @return string Formatted amount with currency symbol
 */
function formatCurrency($amount, $currency = 'USD') {
    $amount = (float)$amount;
    switch (strtoupper($currency)) {
        case 'USD':
            return '$' . number_format($amount, 2);
        case 'EUR':
            return '€' . number_format($amount, 2);
        case 'GBP':
            return '£' . number_format($amount, 2);
        default:
            return number_format($amount, 2) . ' ' . strtoupper($currency);
    }
}