<?php
$title = "Invoice #" . str_pad($sale['id'], 6, '0', STR_PAD_LEFT);
ob_start();
?>

<?php
$receiptFormat = $settings['receipt_type'] ?? 'a4';
?>

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        body {
            background-color: #fff !important;
            margin: 0;
            padding: 0;
        }

        <?php if ($receiptFormat === 'thermal'): ?>
            .invoice-card {
                display: none !important;
            }

        <?php else: ?>
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                display: block !important;
            }

        <?php endif; ?>

        .modal-backdrop,
        .modal,
        .navbar,
        .sidebar {
            display: none !important;
        }

        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
    }

    .print-only {
        display: none;
    }

    .google-btn-secondary {
        background: transparent;
        border: 1px solid #dadce0;
        color: #1f1f1f;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .google-btn-secondary:hover {
        background: #f8f9fa;
        color: #1f1f1f;
    }

    .google-btn-primary {
        background: #0b57d0;
        border: none;
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .google-btn-primary:hover {
        background: #0842a0;
        color: #fff;
    }

    .invoice-card {
        background: white;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
        max-width: 800px;
        margin: 0 auto;
    }

    .header-title {
        font-size: 2rem;
        font-weight: bold;
        color: #1f1f1f;
    }

    .cancel-watermark {
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 5rem;
        font-weight: bold;
        border: 8px solid;
        padding: 5px 30px;
        border-radius: 10px;
        pointer-events: none;
        opacity: 0.25;
        z-index: 0;
        font-family: 'Courier New', Courier, monospace;
        text-transform: uppercase;
        letter-spacing: 5px;
    }

    .watermark-red {
        color: #c5221f;
        border-color: #c5221f;
    }

    .watermark-green {
        color: #137333;
        border-color: #137333;
    }

    .watermark-yellow {
        color: #b06000;
        border-color: #b06000;
    }

    .material-modal .modal-content {
        border-radius: 28px;
    }

    .material-modal .modal-title-custom {
        font-size: 24px;
        color: #1f1f1f;
        font-weight: 400;
        margin-bottom: 16px;
    }

    .material-modal .btn-cancel {
        color: #0b57d0;
        font-weight: 500;
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 20px;
    }

    .material-modal .btn-cancel:hover {
        background-color: #f6f8fb;
    }

    .material-modal .btn-ok {
        background-color: #137333;
        color: #fff;
        font-weight: 500;
        border-radius: 20px;
        padding: 10px 24px;
        border: none;
        transition: background-color 0.2s;
    }

    .material-modal .btn-ok:hover {
        background-color: #0e5a27;
        color: #fff;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-10">

        <!-- Toolbar -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-4 gap-2 no-print">
            <h1 class="h3 mb-0 fw-normal" style="color: #1f1f1f;">Invoice #<?= $sale['id'] ?></h1>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= $returnUrl ?>" class="google-btn-secondary gap-1 border-0 shadow-sm bg-white">
                    <span class="material-symbols-outlined align-text-bottom" style="font-size: 18px;">arrow_back</span>
                    Back to Invoices
                </a>
                
                <button onclick="window.print()"
                    class="<?= ($sale['payment_status'] === 'paid') ? 'google-btn-primary' : 'google-btn-secondary bg-white' ?> d-flex align-items-center gap-1 shadow-sm border-0">
                    <span class="material-symbols-outlined" style="font-size: 18px;">print</span>
                    <?= ($sale['payment_status'] === 'paid') ? 'Print Receipt' : 'Print Invoice' ?>
                </button>

                <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" target="_blank" 
                   class="google-btn-secondary bg-white d-flex align-items-center gap-1 shadow-sm border-0" 
                   title="Open in a new browser tab for clean printing">
                    <span class="material-symbols-outlined" style="font-size: 18px;">open_in_new</span>
                    Open in Browser
                </a>

                <?php if ($sale['total_amount'] - $sale['paid_amount'] > 0): ?>
                    <button type="button" class="btn text-white fw-medium d-flex align-items-center gap-1 shadow-sm"
                        style="background: #137333; border-radius: 20px; padding: 8px 20px;" data-bs-toggle="modal" data-bs-target="#payModal">
                        <span class="material-symbols-outlined" style="font-size: 18px;">payments</span> Settle Debt
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="invoice-card position-relative">
            <?php if (!empty($sale['voided'])): ?>
                <div class="cancel-watermark watermark-red">VOIDED</div>
            <?php elseif ($sale['payment_status'] === 'paid'): ?>
                <div class="cancel-watermark watermark-green">PAID</div>
            <?php elseif ($sale['payment_status'] === 'partial'): ?>
                <div class="cancel-watermark watermark-yellow">PARTIAL</div>
            <?php else: ?>
                <div class="cancel-watermark watermark-red">UNPAID</div>
            <?php endif; ?>

            <!-- Header -->
            <div class="row mb-4 border-bottom pb-4 align-items-center">
                <div class="col-8">
                    <div class="d-flex align-items-center mb-2">
                        <?php if (!empty($settings['company_logo'])): ?>
                            <img src="<?= BASE_URL ?>/<?= e($settings['company_logo']) ?>" alt="Logo"
                                style="height: 50px; margin-right: 15px;">
                        <?php endif; ?>
                        <h1 class="header-title m-0"><?= e($settings['company_name'] ?? 'Debtors System') ?></h1>
                    </div>
                    <div class="text-muted" style="font-size: 14px;">
                        <?php if (!empty($settings['company_address'])): ?>
                            <?= nl2br(e($settings['company_address'])) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_phone'])): ?>
                            PH: <?= e($settings['company_phone']) ?><br>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_email'])): ?>
                            Email: <?= e($settings['company_email']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <?php if ($sale['payment_status'] !== 'paid'): ?>
                        <h4 class="fw-bold" style="color: #c5221f;">UNPAID DEBT INVOICE</h4>
                    <?php else: ?>
                        <h4 class="fw-bold" style="color: #0b57d0;">RECEIPT / SETTLED</h4>
                    <?php endif; ?>
                    <div class="fs-5" style="color: #1f1f1f;">
                        #<?= htmlspecialchars($sale['invoice_number'] ?? str_pad($sale['id'], 6, '0', STR_PAD_LEFT)) ?>
                    </div>
                    <div class="text-muted small mb-2">Date: <?= date('M j, Y', strtotime($sale['created_at'])) ?></div>
                    <div class="d-flex justify-content-end">
                        <svg id="invoice-barcode"
                            data-value="<?= htmlspecialchars($sale['invoice_number'] ?? $sale['id']) ?>"
                            style="max-height: 40px; width: 120px;"></svg>
                    </div>
                </div>
            </div>

            <!-- Bill To -->
            <div class="row mb-5">
                <div class="col-6">
                    <p class="mb-1 text-uppercase text-muted small fw-bold">Customer (Debtor)</p>
                    <?php if ($sale['customer_name']): ?>
                        <h5 class="fw-bold" style="color: #1f1f1f;">
                            <a href="<?= BASE_URL ?>/customers/view?id=<?= $sale['customer_id'] ?>" class="text-decoration-none text-dark">
                                <?= e($sale['customer_name']) ?>
                            </a>
                        </h5>
                        <p style="color: #444746; font-size: 14px;">
                            <?= e($sale['customer_address'] ?? '') ?><br>
                            <?= e($sale['customer_phone'] ?? '') ?>
                        </p>
                    <?php else: ?>
                        <h5 class="fw-bold text-muted">Walk-in Customer</h5>
                    <?php endif; ?>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-1 text-uppercase text-muted small fw-bold">Payment Status</p>
                    <?php if ($sale['payment_status'] === 'paid'): ?>
                        <span class="badge"
                            style="background: #e6f4ea; color: #137333; font-size: 16px; padding: 6px 12px; border-radius: 12px; border: 1px solid #ceead6;">PAID</span>
                    <?php elseif ($sale['payment_status'] === 'partial'): ?>
                        <span class="badge"
                            style="background: #fef7e0; color: #b06000; font-size: 16px; padding: 6px 12px; border-radius: 12px; border: 1px solid #fce8b2;">PARTIAL
                            CREDIT</span>
                    <?php else: ?>
                        <span class="badge"
                            style="background: #fce8e6; color: #c5221f; font-size: 16px; padding: 6px 12px; border-radius: 12px; border: 1px solid #fad2cf;">UNPAID</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items -->
            <div class="table-responsive mb-4">
                <table class="table table-borderless">
                    <thead style="border-bottom: 2px solid #e3e3e3;">
                        <tr>
                            <th class="py-3 text-muted text-uppercase" style="font-size: 13px;">Item</th>
                            <th class="py-3 text-center text-muted text-uppercase" style="font-size: 13px;">SKU</th>
                            <th class="py-3 text-center text-muted text-uppercase" style="font-size: 13px;">Qty</th>
                            <th class="py-3 text-end text-muted text-uppercase" style="font-size: 13px;">Unit Price</th>
                            <th class="py-3 text-end text-muted text-uppercase" style="font-size: 13px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sale['items'] as $item): ?>
                            <tr style="border-bottom: 1px solid #f1f3f4;">
                                <td class="fw-medium py-3" style="color: #1f1f1f;">
                                    <?= e($item['item_name'] ?? 'Item #' . $item['item_id']) ?>
                                </td>
                                <td class="text-center small text-muted py-3"><?= e($item['sku'] ?? '-') ?></td>
                                <td class="text-center py-3"><?php echo $item['quantity']; ?></td>
                                <td class="text-end py-3">₵<?php echo number_format($item['price_at_sale'], 2); ?></td>
                                <td class="text-end fw-medium py-3">₵<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot style="background: #f8f9fa;">
                        <tr>
                            <td colspan="4" class="text-end fw-medium py-3 text-uppercase"
                                style="font-size: 13px; color: #5f6368;">Total Amount</td>
                            <td class="text-end fw-bold fs-5 py-3" style="color: #0b57d0;">
                                ₵<?php echo number_format($sale['total_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end py-2 text-uppercase"
                                style="font-size: 13px; color: #5f6368;">Amount Paid</td>
                            <td class="text-end fw-medium py-2" style="color: #137333;">
                                -₵<?php echo number_format($sale['paid_amount'], 2); ?></td>
                        </tr>
                        <?php if ($sale['total_amount'] - $sale['paid_amount'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end fw-bold py-3 text-uppercase"
                                    style="font-size: 13px; color: #c5221f;">Outstanding Balance Due</td>
                                <td class="text-end fw-bold fs-5 py-3" style="color: #c5221f;">
                                    ₵<?php echo number_format($sale['total_amount'] - $sale['paid_amount'], 2); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>

            <!-- Payment History -->
            <?php if (!empty($payments)): ?>
                <div class="mb-4">
                    <h6 class="text-muted text-uppercase small fw-bold">Settlement History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless" style="background: #f1f3f4; border-radius: 12px;">
                            <thead>
                                <tr>
                                    <th class="ps-3 pt-2 text-muted fw-medium" style="font-size: 13px;">Date</th>
                                    <th class="pt-2 text-muted fw-medium" style="font-size: 13px;">Amount Paid</th>
                                    <th class="pe-3 pt-2 text-muted fw-medium" style="font-size: 13px;">Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td class="ps-3 pb-2" style="font-size: 14px;">
                                            <?php echo date('M j, Y H:i', strtotime($payment['payment_date'])); ?>
                                        </td>
                                        <td class="pb-2 fw-medium" style="color: #137333; font-size: 14px;">
                                            ₵<?php echo number_format($payment['amount'], 2); ?></td>
                                        <td class="pe-3 pb-2" style="font-size: 14px;"><?= e($payment['username']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Footer Action Section -->
            <div class="text-center mt-5 text-muted no-print">
                <?php if ($sale['total_amount'] - $sale['paid_amount'] > 0): ?>
                    <button class="btn text-white fw-medium shadow-sm d-inline-flex align-items-center gap-2"
                        style="background: #137333; border-radius: 20px; padding: 12px 28px; font-size: 16px;" data-bs-toggle="modal"
                        data-bs-target="#payModal">
                        <span class="material-symbols-outlined">payments</span> Settle Invoice Debt
                    </button>
                <?php else: ?>
                    <div class="d-inline-flex align-items-center gap-2 text-success fw-bold fs-5 bg-success bg-opacity-10 px-4 py-2 rounded-pill">
                        <span class="material-symbols-outlined">check_circle</span>
                        Invoice Fully Settled
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-center mt-4 text-muted print-only">
                <p>Thank you for settling your account!</p>
                <span style="font-size: 12px;">Printed on: <?php echo date('M j, Y H:i'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Pay Modal -->
<div class="modal fade material-modal" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>/sales/pay" method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="sale_id" value="<?php echo $sale['id']; ?>">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="modal-title-custom mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-success">payments</span> Record Debt Payment
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase">
                            Amount Customer is Paying
                        </label>
                        <div class="position-relative">
                            <span class="position-absolute fw-bold"
                                style="left: 16px; top: 50%; transform: translateY(-50%); color: #1f1f1f; font-size: 20px;">₵</span>
                            <?php
                            $balanceDue = round($sale['total_amount'] - $sale['paid_amount'], 2);
                            ?>
                            <input type="number" name="amount" id="payment-amount" step="0.01" min="0.01"
                                class="form-control"
                                style="background: #f1f3f4; border: none; border-radius: 12px; padding: 14px 16px 14px 38px; font-size: 22px; font-weight: bold;"
                                max="<?= $balanceDue ?>" value="<?= $balanceDue ?>" required autofocus>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="form-text text-danger fw-bold mt-0">Total Due: ₵<?= number_format($balanceDue, 2) ?></span>
                            <button type="button" class="btn btn-link p-0 text-decoration-none small"
                                onclick="document.getElementById('payment-amount').value = '<?= $balanceDue ?>'">Pay Full Balance</button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center gap-2">
                        <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-ok" style="background-color: #137333;">
                            Save Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const barcodeEl = document.getElementById('invoice-barcode');
        if (barcodeEl) {
            JsBarcode("#invoice-barcode", barcodeEl.getAttribute('data-value'), {
                format: "CODE128",
                width: 1.5,
                height: 35,
                displayValue: false,
                margin: 0,
                background: "transparent"
            });
        }
    });
</script>
<?php
require __DIR__ . '/../layouts/main.php';
?>
