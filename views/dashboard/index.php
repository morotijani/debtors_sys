<?php
$title = "Debtors Dashboard";
ob_start();
?>
<style>
    .dashboard-header {
        margin-bottom: 28px;
    }
    .dashboard-title {
        font-size: 26px;
        font-weight: 500;
        color: #1f1f1f;
        letter-spacing: -0.5px;
    }
    .widget-card {
        border-radius: 20px;
        border: 1px solid #e0e2ec;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #fff;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .widget-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }
    .widget-card-alert {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);
        border: 1px solid #ffc9c9;
    }
    .widget-icon-container {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .widget-title {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #5f6368;
        margin-bottom: 6px;
    }
    .widget-value {
        font-size: 32px;
        font-weight: 600;
        letter-spacing: -0.5px;
        color: #1f1f1f;
        margin-bottom: 6px;
    }
    .widget-subtitle {
        font-size: 13px;
        color: #747775;
    }
    .google-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e0e2ec;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .google-card-header {
        font-size: 18px;
        font-weight: 500;
        color: #1f1f1f;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .google-btn-action {
        border-radius: 20px;
        padding: 9px 18px;
        font-weight: 500;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #dadce0;
        background: #fff;
        color: #0b57d0;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .google-btn-action:hover {
        background: #f8fafd;
        color: #0842a0;
    }
    .google-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        letter-spacing: 0.5px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-subtle-success { background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6; }
    .badge-subtle-danger { background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; }
    .badge-subtle-primary { background-color: #e8f0fe; color: #1967d2; border: 1px solid #d2e3fc; }
    .badge-subtle-warning { background-color: #fef7e0; color: #b06000; border: 1px solid #fce8b2; }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        
        <!-- Header Section -->
        <div class="dashboard-header d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3">
            <div>
                <h1 class="dashboard-title mb-1 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-danger fs-2">account_balance_wallet</span>
                    Debtors & Debt Recovery Dashboard
                </h1>
                <div class="text-muted small">
                    Overview of active unpaid sales, standalone debtors, and debt recovery history &bull; <?= date('l, F j, Y') ?>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= BASE_URL ?>/sales" class="google-btn-action">
                    <span class="material-symbols-outlined" style="font-size: 18px;">receipt_long</span> View Debt Invoices
                </a>
                <a href="<?= BASE_URL ?>/customers?owing=1" class="google-btn-action">
                    <span class="material-symbols-outlined" style="font-size: 18px;">group</span> Owing Customers
                </a>
                <a href="<?= BASE_URL ?>/debtors" class="google-btn-action">
                    <span class="material-symbols-outlined" style="font-size: 18px;">person_search</span> Standalone Debtors
                </a>
            </div>
        </div>

        <!-- Key Metric Widgets -->
        <div class="row g-3 mb-4">
            <!-- 1. Total Outstanding Debt (Hero Card) -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="widget-card widget-card-alert">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="widget-icon-container" style="background: #fce8e6; color: #c5221f;">
                                <span class="material-symbols-outlined">warning</span>
                            </div>
                            <span class="badge bg-danger rounded-pill px-2 py-1 small">Total Debt</span>
                        </div>
                        <div class="widget-title text-danger">Total Outstanding Debt</div>
                        <div class="widget-value text-danger">₵<?= number_format($totalDebt, 2) ?></div>
                    </div>
                    <div class="widget-subtitle text-danger pt-2 border-top border-danger-subtle d-flex justify-content-between">
                        <span>Combined Balance Due</span>
                        <span class="fw-bold">100%</span>
                    </div>
                </div>
            </div>

            <!-- 2. Sales Invoices Debt -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="widget-card">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="widget-icon-container" style="background: #fef7e0; color: #b06000;">
                                <span class="material-symbols-outlined">receipt_long</span>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small"><?= $unpaidInvoiceCount ?> Invoices</span>
                        </div>
                        <div class="widget-title">Sales Invoice Debt</div>
                        <div class="widget-value text-warning-emphasis">₵<?= number_format($salesDebt, 2) ?></div>
                    </div>
                    <div class="widget-subtitle pt-2 border-top d-flex justify-content-between">
                        <span>Across <?= $unpaidInvoiceCount ?> Unpaid/Partial Sales</span>
                        <a href="<?= BASE_URL ?>/sales" class="text-decoration-none small text-primary fw-medium">View</a>
                    </div>
                </div>
            </div>

            <!-- 3. Standalone Debtors -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="widget-card">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="widget-icon-container" style="background: #f3e8fd; color: #7b1fa2;">
                                <span class="material-symbols-outlined">person_search</span>
                            </div>
                            <span class="badge rounded-pill px-2 py-1 small" style="background: #e8d5f9; color: #6a1b9a;"><?= $standaloneCount ?> Debtors</span>
                        </div>
                        <div class="widget-title">Standalone Debt</div>
                        <div class="widget-value" style="color: #6a1b9a;">₵<?= number_format($standaloneDebt, 2) ?></div>
                    </div>
                    <div class="widget-subtitle pt-2 border-top d-flex justify-content-between">
                        <span>Non-invoice credit records</span>
                        <a href="<?= BASE_URL ?>/debtors" class="text-decoration-none small text-primary fw-medium">View</a>
                    </div>
                </div>
            </div>

            <!-- 4. Recovered / Settled This Month -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="widget-card">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="widget-icon-container" style="background: #e6f4ea; color: #137333;">
                                <span class="material-symbols-outlined">check_circle</span>
                            </div>
                            <span class="badge bg-success rounded-pill px-2 py-1 small">This Month</span>
                        </div>
                        <div class="widget-title">Debt Collected (<?= date('M Y') ?>)</div>
                        <div class="widget-value text-success">₵<?= number_format($totalCollectedThisMonth, 2) ?></div>
                    </div>
                    <div class="widget-subtitle pt-2 border-top d-flex justify-content-between">
                        <span>Recoveries this month</span>
                        <span class="text-success fw-medium">&check; Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Information Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="p-3 bg-white rounded-4 border d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 gap-sm-3">
                        <div class="p-2 bg-light rounded-circle text-primary flex-shrink-0">
                            <span class="material-symbols-outlined fs-4">group</span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6"><?= $owingCustomers ?> <span class="fw-normal small text-muted">of <?= $totalCustomers ?> customers</span></div>
                            <div class="text-muted small" style="font-size: 12px; line-height: 1.3;">Currently have outstanding unpaid/partial sales</div>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/customers" class="btn btn-outline-primary btn-sm rounded-pill px-3 flex-shrink-0 text-nowrap align-self-start align-self-sm-center">
                        Customer Ledger
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-3 bg-white rounded-4 border d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2 gap-sm-3">
                        <div class="p-2 bg-light rounded-circle text-success flex-shrink-0">
                            <span class="material-symbols-outlined fs-4">price_check</span>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">Fast Debt Settlement</div>
                            <div class="text-muted small" style="font-size: 12px; line-height: 1.3;">Direct invoice payment & bulk FIFO customer settlement enabled</div>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/sales" class="btn btn-success btn-sm rounded-pill px-3 flex-shrink-0 text-nowrap align-self-start align-self-sm-center">
                        Settle Invoices
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Data Tables Row -->
        <div class="row g-4 mb-4">
            <!-- Top Debtors -->
            <div class="col-12 col-lg-7">
                <div class="google-card h-100 mb-0">
                    <div class="google-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-danger fs-5">crisis_alert</span>
                            <span class="fw-semibold">Top Owing Customers</span>
                        </div>
                        <a href="<?= BASE_URL ?>/customers" class="small text-decoration-none text-nowrap fw-medium">View All &rarr;</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted small text-uppercase">
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th class="text-center">Invoices</th>
                                    <th class="text-end">Total Owed</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topDebtors)): ?>
                                    <?php foreach ($topDebtors as $debtor): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= BASE_URL ?>/customers/view?id=<?= $debtor['id'] ?>" class="text-decoration-none fw-bold text-dark">
                                                    <?= e($debtor['name']) ?>
                                                </a>
                                            </td>
                                            <td class="text-muted small"><?= e($debtor['phone'] ?: 'No Phone') ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border"><?= $debtor['invoice_count'] ?></span>
                                            </td>
                                            <td class="text-end fw-bold text-danger">
                                                ₵<?= number_format($debtor['total_debt'], 2) ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= BASE_URL ?>/customers/view?id=<?= $debtor['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 text-nowrap d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                                    <span class="material-symbols-outlined" style="font-size: 14px;">payments</span> Settle
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No customers currently owing sales debt!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Debt Recovery Payments -->
            <div class="col-12 col-lg-5">
                <div class="google-card h-100 mb-0">
                    <div class="google-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-success">paid</span>
                            <span>Recent Debt Settlements</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted small text-uppercase">
                                <tr>
                                    <th>Date</th>
                                    <th>Party / Invoice</th>
                                    <th class="text-end">Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentPayments)): ?>
                                    <?php foreach ($recentPayments as $pay): ?>
                                        <tr>
                                            <td>
                                                <div class="small fw-medium text-dark"><?= date('M j', strtotime($pay['payment_date'])) ?></div>
                                                <div class="text-muted" style="font-size: 11px;"><?= date('H:i', strtotime($pay['payment_date'])) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-medium text-dark small text-truncate" style="max-width: 160px;">
                                                    <?= e($pay['party_name']) ?>
                                                </div>
                                                <div class="text-muted small" style="font-size: 11px;">
                                                    <?php if ($pay['payment_type'] === 'invoice'): ?>
                                                        <a href="<?= BASE_URL ?>/sales/view?id=<?= $pay['reference_id'] ?>" class="text-decoration-none">
                                                            Inv #<?= $pay['reference_id'] ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-secondary border">Standalone</span>
                                                    <?php endif; ?>
                                                    &bull; by <?= e($pay['recorded_by_name'] ?? 'Admin') ?>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                ₵<?= number_format($pay['amount'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            No recent settlements recorded yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
