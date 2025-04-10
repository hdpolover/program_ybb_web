<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
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

        h1,
        h2,
        h3 {
            color: #1a1a1a;
            margin: 0;
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

        .info-table,
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 8px 0;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
            vertical-align: top;
        }

        .summary-table {
            margin: 20px 0;
            border: 1px solid #ddd;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .summary-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            color: #000;
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

        .paid-stamp {
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

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #777;
            position: relative;
            z-index: 1;
        }

        .footer-note {
            font-size: 10px;
            color: #555;
            margin-top: 10px;
        }

        .compact-info {
            text-align: left; 
            margin-bottom: 10px; 
            font-size: 10px; 
            display: flex; 
            justify-content: space-between;
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
                <?php if (isset($webSettings['logo_url']) && !empty($webSettings['logo_url'])): ?>
                    <img src="<?= $webSettings['logo_url'] ?>" alt="<?= $program['name'] ?? 'Program Logo' ?>" width="100">
                <?php else: ?>
                    <div style="font-size: 16px; font-weight: bold; color: #000;">YBB</div>
                <?php endif; ?>
                <div class="receipt-number">Receipt #: <?= $payment['transaction_code'] ?? 'YBB-' . ($payment['id'] ?? '000') ?></div>
            </div>
            <div class="header-title">
                <div class="receipt-title">PAYMENT RECEIPT</div>
                <div class="receipt-subtitle">Official Payment Confirmation</div>
            </div>
        </div>        <!-- Receipt Content -->
        <div class="receipt-content">
            <!-- Combined Transaction and Billing Information Section -->
            <div class="receipt-section">
                <div class="section-title">Payment Information</div>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 8px 12px; width: 25%; font-weight: bold; border: 1px solid #ddd;">Transaction Date/Time:</td>
                        <td style="padding: 8px 12px; width: 25%; border: 1px solid #ddd;"><?= date('F j, Y - g:i a', strtotime($payment['created_at'] ?? date('Y-m-d H:i:s'))) ?></td>
                        <td style="padding: 8px 12px; width: 25%; font-weight: bold; border: 1px solid #ddd;">Account ID:</td>
                        <td style="padding: 8px 12px; width: 25%; border: 1px solid #ddd;"><?= $participant['account_id'] ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 12px; font-weight: bold; border: 1px solid #ddd;">Payment Method:</td>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;"><?= ucfirst(str_replace('_', ' ', $paymentMethod['name'] ?? '-')) ?></td>
                        <td style="padding: 8px 12px; font-weight: bold; border: 1px solid #ddd;">Participant Name:</td>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;"><?= esc($participant['full_name']) ?></td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 8px 12px; font-weight: bold; border: 1px solid #ddd;">Payment Status:</td>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;">
                            <span class="payment-status status-success">Paid</span>
                        </td>
                        <td style="padding: 8px 12px; font-weight: bold; border: 1px solid #ddd;">Program:</td>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;"><?= esc($program['name']) ?></td>
                    </tr>
                    <?php if (!empty($payment['source_name'])): ?>
                    <tr>
                        <td style="padding: 8px 12px; font-weight: bold; border: 1px solid #ddd;">Source Name:</td>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;"><?= esc($payment['source_name']) ?></td>
                        <?php if (!empty($payment['account_name'])): ?>
                        <td style="padding: 8px 12px; font-weight: bold; border: 1px solid #ddd;">Account Name:</td>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;"><?= esc($payment['account_name']) ?></td>
                        <?php else: ?>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;" colspan="2"></td>
                        <?php endif; ?>
                    </tr>
                    <?php elseif (!empty($payment['account_name'])): ?>
                    <tr>
                        <td style="padding: 8px 12px; font-weight: bold; border: 1px solid #ddd;">Account Name:</td>
                        <td style="padding: 8px 12px; border: 1px solid #ddd;" colspan="3"><?= esc($payment['account_name']) ?></td>
                    </tr>
                    <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Payment Details Section -->
            <div class="receipt-section">
                <div class="section-title">Payment Details</div>                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Payment Type</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $programPayment['name'] ?></td>
                            <td><?= $programPayment['type'] ?></td>
                            <td class="text-right">$ <?= number_format($payment['usd_amount'] ?? 0, 2) ?></td>
                        </tr>
                        <?php if (!empty($payment['usd_processing_fee']) && $payment['usd_processing_fee'] > 0): ?>
                        <tr>
                            <td>Processing Fee</td>
                            <td>Service Fee</td>
                            <td class="text-right">$ <?= number_format($payment['usd_processing_fee'] ?? 0, 2) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="total-section">
                    Total: <span class="total-amount">$ <?= number_format(($payment['usd_amount'] ?? 0) + ($payment['usd_processing_fee'] ?? 0), 2) ?></span>
                </div>
            </div>

            <?php if (!empty($payment['notes'])): ?>
                <div class="receipt-notes">
                    <strong>Notes:</strong> <?= $payment['notes'] ?>
                </div>
            <?php endif; ?>
            
            <div class="paid-stamp">
                <div class="stamp">PAID</div>
            </div>
        </div>        <!-- Receipt Footer -->
        <div class="footer">
            <?php if (isset($webSettings) && !empty($webSettings)): ?>
            <div class="compact-info" style="text-align: center; margin-bottom: 10px; font-size: 10px;">
                <?php 
                $infoItems = [];
                if (isset($webSettings['contact']) && !empty($webSettings['contact'])) {
                    $infoItems[] = "<strong>Contact:</strong> {$webSettings['contact']}";
                }
                if (isset($webSettings['location']) && !empty($webSettings['location'])) {
                    $infoItems[] = "<strong>Location:</strong> {$webSettings['location']}";
                }
                if (isset($webSettings['email']) && !empty($webSettings['email'])) {
                    $infoItems[] = "<strong>Email:</strong> {$webSettings['email']}";
                }
                if (isset($webSettings['web_url']) && !empty($webSettings['web_url'])) {
                    $infoItems[] = "<strong>Website:</strong> {$webSettings['web_url']}";
                }
                echo implode(' | ', $infoItems);
                ?>
            </div>
            <?php endif; ?>

            <div class="footer-note">
                <p style="margin: 5px 0;">This is an official receipt for your payment. Please keep it for your records.</p>
                
                <?php if (isset($program['email'])): ?>
                    <p style="margin: 5px 0;">For questions: <?= $program['email'] ?></p>
                <?php endif; ?>
                
                <p style="margin: 5px 0; font-size: 9px; color: #777;">Generated: <?= date('Y-m-d H:i') ?> | &copy; <?= date('Y') ?> <?= $program['name'] ?? 'Youth Break the Boundaries' ?></p>
            </div>
        </div>
    </div>

</body>

</html>