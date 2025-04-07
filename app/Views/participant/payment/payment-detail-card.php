<h5 class="text-primary mb-3">Transaction Details</h5>

<div class="table-responsive bg-light p-3 rounded">
    <table class="table table-borderless mb-0">
        <tbody>
            <tr>
                <th class="ps-0 text-muted" scope="row">Transaction ID:</th>
                <td class="text-end text-dark">
                    <?= isset($programPayment['transaction_id']) ? $programPayment['transaction_id'] : 'Transaction pending' ?>
                </td>
            </tr>
            <tr>
                <th class="ps-0 text-muted" scope="row">Payment Method:</th>
                <td class="text-end text-dark">
                    <?php
                    $payment_method = 'Not specified';
                    $payment_details = '';

                    if (isset($programPayment['payment_method'])) {
                        switch ($programPayment['payment_method']) {
                            case 'credit_card':
                                $payment_method = 'Credit Card';
                                if (isset($programPayment['card_last4'])) {
                                    $payment_details = ' (ending in ' . $programPayment['card_last4'] . ')';
                                }
                                break;
                            case 'debit_card':
                                $payment_method = 'Debit Card';
                                if (isset($programPayment['card_last4'])) {
                                    $payment_details = ' (ending in ' . $programPayment['card_last4'] . ')';
                                }
                                break;
                            case 'bank_transfer':
                                $payment_method = 'Bank Transfer';
                                if (isset($programPayment['transfer_reference'])) {
                                    $payment_details = ' (Ref: ' . $programPayment['transfer_reference'] . ')';
                                }
                                break;
                            case 'paypal':
                                $payment_method = 'PayPal';
                                break;
                            default:
                                $payment_method = ucfirst($programPayment['payment_method']);
                        }
                    }

                    echo $payment_method . $payment_details;
                    ?>
                </td>
            </tr>
            <tr>
                <th class="ps-0 text-muted" scope="row">Date:</th>
                <td class="text-end text-dark">
                    <?php if (isset($programPayment['payment_date'])): ?>
                        <?= date('F d, Y \a\t h:i A', strtotime($programPayment['payment_date'])) ?>
                    <?php else: ?>
                        Payment not completed
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th class="ps-0 text-muted" scope="row">Subtotal:</th>
                <td class="text-end text-dark">
                    $<?= isset($programPayment['usd_amount']) ? number_format((float)$programPayment['usd_amount'], 2) : '0.00' ?>
                </td>
            </tr>
            <tr>
                <th class="ps-0 text-muted" scope="row">Processing Fee:</th>
                <td class="text-end text-dark">
                    $<?= isset($programPayment['fee_amount']) ? number_format((float)$programPayment['fee_amount'], 2) : '0.00' ?>
                </td>
            </tr>
            <tr>
                <th class="ps-0 text-muted" scope="row">Total Amount:</th>
                <td class="text-end text-dark fw-semibold">
                    $<?php
                        $total = 0;
                        if (isset($programPayment['usd_amount'])) {
                            $total += (float)$programPayment['usd_amount'];
                        }
                        if (isset($programPayment['fee_amount'])) {
                            $total += (float)$programPayment['fee_amount'];
                        }
                        echo number_format($total, 2);
                        ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>