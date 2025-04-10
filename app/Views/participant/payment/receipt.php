<?php
// Receipt template for program payments
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - <?= $payment['transaction_code'] ?? 'YBB-' . ($payment['id'] ?? '000') ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.3;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 700px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            background-color: #fff;
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            opacity: 0.03;
            transform: rotate(-30deg) translateY(-50%);
            font-size: 80px;
            font-weight: bold;
            z-index: 0;
            color: #000;
            letter-spacing: 5px;
        }

        .receipt-header {
            position: relative;
            z-index: 1;
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-number {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 12px;
            color: #777;
        }

        .header-logo {
            width: 40%;
        }

        .header-title {
            width: 60%;
            text-align: right;
        }

        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .receipt-subtitle {
            font-size: 12px;
            color: #555;
        }

        .receipt-content {
            position: relative;
            z-index: 1;
        }

        .receipt-section {
            margin-bottom: 30px;
        }

        .double-column {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .column {
            width: 48%;
        }

        .section-title {
            font-size: 12px;
            color: #000;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info-table td {
            padding: 8px 0;
        }

        table.info-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
            vertical-align: top;
        }

        .payment-table {
            margin: 20px 0;
            border: 1px solid #ddd;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        .payment-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            color: #000;
            text-align: left;
        }

        .payment-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        .status-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .receipt-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #777;
            position: relative;
            z-index: 1;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            font-size: 11px;
            margin-top: 15px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            text-align: right;
            border: 1px solid #ddd;
        }

        .total-amount {
            font-weight: bold;
            color: #000;
            font-size: 14px;
        }

        .receipt-notes {
            font-style: italic;
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #3b7ddd;
            font-size: 13px;
        }

        .receipt-stamp {
            margin-top: 30px;
            text-align: right;
            margin-right: 20px;
        }

        .stamp {
            display: inline-block;
            color: #0f5132;
            font-size: 18px;
            font-weight: bold;
            border: 3px solid #0f5132;
            padding: 10px 20px;
            border-radius: 10px;
            transform: rotate(-5deg);
        }

        .contact-info {
            margin-top: 20px;
            font-size: 13px;
        }

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }

        /* Reduce complexity for better PDF rendering */
        .box-shadow,
        .shadow {
            box-shadow: none !important;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Watermark -->
        <div class="watermark">OFFICIAL RECEIPT</div>

        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="header-logo">
                <?php if (isset($webSettings['logo_url']) && !empty($webSettings['logo_url'])): ?> <img src="<?= $webSettings['logo_url'] ?>" alt="<?= $program['name'] ?? 'Program Logo' ?>" width="100">
                <?php else: ?>
                    <div style="font-size: 16px; font-weight: bold; color: #000;">YBB</div>
                <?php endif; ?>
                <div class="receipt-number">Receipt #: <?= $payment['transaction_code'] ?? 'YBB-' . ($payment['id'] ?? '000') ?></div>
            </div>
            <div class="header-title">
                <div class="receipt-title">PAYMENT RECEIPT</div>
                <div class="receipt-subtitle">Official Payment Confirmation</div>            </div>
        </div>
        <!-- Receipt Content -->
        <div class="receipt-content">
            <!-- Two-column layout for Transaction Details and Participant Information -->
            <div class="double-column">
                <!-- Transaction Details Section -->
                <div class="column">
                    <div class="section-title">Transaction Details</div>
                    <table class="info-table">
                        <tr>
                            <td>Transaction Date:</td>
                            <td><?= date('F j, Y', strtotime($payment['created_at'] ?? date('Y-m-d H:i:s'))) ?></td>
                        </tr>
                        <tr>
                            <td>Transaction Time:</td>
                            <td><?= date('g:i a', strtotime($payment['created_at'] ?? date('Y-m-d H:i:s'))) ?></td>
                        </tr>
                        <tr>
                            <td>Payment Method:</td>
                            <td><?= ucfirst(str_replace('_', ' ', $payment['payment_method'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <td>Payment Status:</td>
                            <td>
                                <span class="payment-status status-success">
                                    Paid
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Participant Information Section -->
                <div class="column">
                    <div class="section-title">Billing Information</div>
                    <table class="info-table">
                        <tr>
                            <td>Account ID:</td>
                            <td><?= $participant['account_id'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td>Participant Name:</td>
                            <td><?= $participant['full_name']  ?></td>
                        </tr>
                        <tr>
                            <td>Program:</td>
                            <td><?= $program['name'] ?? '-' ?></td>
                        </tr>

                    </table>
                </div>
            </div> <!-- Payment Details Section -->
            <div class="receipt-section">
                <div class="section-title">Payment Details</div>
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Payment Type</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $programPayment['name'] ?? 'Program Payment' ?></td>
                            <td><?= ucfirst($programPayment['category'] ?? 'Fee') ?></td>
                            <td class="text-right"><?= number_format($payment['amount'] ?? 0, 2) ?> USD</td>
                        </tr>
                        <?php if (!empty($payment['processing_fee']) && $payment['processing_fee'] > 0): ?>
                            <tr>
                                <td>Processing Fee</td>
                                <td>Service Fee</td>
                                <td class="text-right"><?= number_format($payment['processing_fee'], 2) ?> USD</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="total-section">
                    Total Amount Paid: <span class="total-amount"><?= number_format(($payment['amount'] ?? 0) + ($payment['processing_fee'] ?? 0), 2) ?> USD</span>
                </div>
            </div>

            <?php if (!empty($payment['notes'])): ?>
                <div class="receipt-notes">
                    <strong>Notes:</strong> <?= $payment['notes'] ?>
                </div> <?php endif; ?> <?php if (($payment['status'] ?? '') == '2'): ?>
                <div class="receipt-stamp">
                    <div class="stamp">PAID</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Receipt Footer with combined Additional Information -->
        <div class="receipt-footer">
            <div class="compact-info" style="text-align: left; margin-bottom: 10px; font-size: 10px; display: flex; justify-content: space-between;">
                <div style="width: 48%;">
                    <?php if (isset($webSettings) && !empty($webSettings)): ?>
                        <?php if (isset($webSettings['contact']) && !empty($webSettings['contact'])): ?>
                            <span><strong>Contact:</strong> <?= $webSettings['contact'] ?></span><br>
                        <?php endif; ?>

                        <?php if (isset($webSettings['location']) && !empty($webSettings['location'])): ?>
                            <span><strong>Location:</strong> <?= $webSettings['location'] ?></span><br>
                        <?php endif; ?>
                </div>
                <div style="width: 48%;">
                    <?php if (isset($webSettings['email']) && !empty($webSettings['email'])): ?>
                        <span><strong>Email:</strong> <?= $webSettings['email'] ?></span><br>
                    <?php endif; ?>

                    <?php if (isset($webSettings['web_url']) && !empty($webSettings['web_url'])): ?>
                        <span><strong>Website:</strong> <?= $webSettings['web_url'] ?></span><br>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            </div>

            <p style="margin: 5px 0; font-size: 10px;">This is an official receipt for your payment. Please keep it for your records.</p>

            <?php if (isset($program['email'])): ?>
                <p style="margin: 5px 0; font-size: 10px;">For questions: <?= $program['email'] ?></p>
            <?php endif; ?>

            <p style="margin: 5px 0; font-size: 9px; color: #777;">Generated: <?= date('Y-m-d H:i') ?> | &copy; <?= date('Y') ?> <?= $program['name'] ?? 'Youth Break the Boundaries' ?></p>
        </div>
    </div>
</body>

</html>