<?php
$title = "Debt Invoices";
ob_start();
?>

<style>
    .google-table-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
        overflow: hidden;
        margin-bottom: 24px;
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
    
    .table-custom tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #e3e3e3;
        color: #1f1f1f;
        vertical-align: middle;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }
    .table-custom th {
        padding: 14px 20px;
        font-size: 12px;
        text-transform: uppercase;
        color: #5f6368;
        font-weight: 600;
        border-bottom: 1px solid #e3e3e3;
        background: #fafafa;
    }
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-3 gap-2">
            <div>
                <h1 class="h3 mb-1 fw-normal" style="color: #1f1f1f;">Debt Invoices</h1>
                <div class="text-muted small">List of all unpaid and partially paid credit invoices &bull; Settle debts directly</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= BASE_URL ?>/customers?owing=1" class="google-btn-secondary gap-1">
                    <span class="material-symbols-outlined" style="font-size: 18px;">group</span> Owing Customers
                </a>
                <a href="<?= BASE_URL ?>/debtors" class="google-btn-secondary gap-1">
                    <span class="material-symbols-outlined" style="font-size: 18px;">person_search</span> Standalone Debtors
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: #e6f4ea; color: #137333;">
                <span class="material-symbols-outlined align-middle me-2">check_circle</span>
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: #fce8e6; color: #c5221f;">
                <span class="material-symbols-outlined align-middle me-2">error</span>
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search & Filter Form -->
        <div class="google-table-card p-3 mb-4" style="border-radius: 16px;">
            <form action="<?= BASE_URL ?>/sales" method="GET" class="row g-3 align-items-center">
                <!-- Group 1: Search & Dates -->
                <div class="col-12 col-xl-6">
                    <div class="d-flex align-items-center bg-light rounded-pill overflow-hidden border px-3">
                        <span class="material-symbols-outlined text-muted" style="font-size: 18px;">search</span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none"
                            placeholder="Invoice # or Customer name" value="<?= e($filters['search']) ?>">
                        
                        <div class="d-flex align-items-center gap-1 border-start ps-3 ms-2">
                            <span class="text-muted" style="font-size: 12px;">From</span>
                            <input type="date" name="start_date" class="form-control border-0 bg-transparent shadow-none px-1"
                                value="<?= e($filters['start_date']) ?>" style="width: 105px; font-size: 13px;">
                            <span class="text-muted" style="font-size: 12px;">To</span>
                            <input type="date" name="end_date" class="form-control border-0 bg-transparent shadow-none px-1"
                                value="<?= e($filters['end_date']) ?>" style="width: 105px; font-size: 13px;">
                        </div>
                    </div>
                </div>

                <!-- Group 2: Status & Display Filters -->
                <div class="col-12 col-xl-6 d-flex flex-wrap flex-md-nowrap gap-3">
                    <div class="d-flex align-items-center flex-grow-1 bg-light rounded-pill border px-3">
                        <span class="fw-medium text-muted me-2" style="font-size: 13px;">Status:</span>
                        <select name="status" class="form-select border-0 bg-transparent shadow-none pe-4" onchange="this.form.submit()" style="font-size: 14px;">
                            <option value="all" <?= $filters['status'] === 'all' ? 'selected' : ''; ?>>All (Unpaid & Partial)</option>
                            <option value="unpaid" <?= $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid Only</option>
                            <option value="partial" <?= $filters['status'] === 'partial' ? 'selected' : ''; ?>>Partial Only</option>
                            <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : ''; ?>>Fully Paid</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 flex-shrink-0">
                        <button type="submit" class="google-btn-primary gap-1">
                            <span class="material-symbols-outlined" style="font-size: 16px;">filter_list</span> Filter
                        </button>
                        <a href="<?= BASE_URL ?>/sales" class="google-btn-secondary gap-1">
                            <span class="material-symbols-outlined" style="font-size: 16px;">restart_alt</span> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Balance Due</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sales)): ?>
                            <?php foreach ($sales as $sale): 
                                $balanceDue = (float)$sale['total_amount'] - (float)$sale['paid_amount'];
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium text-dark"><?= date('M j, Y', strtotime($sale['created_at'])) ?></div>
                                        <div class="text-muted" style="font-size: 11px;"><?= date('H:i', strtotime($sale['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" class="text-decoration-none fw-bold text-dark">
                                            #<?= $sale['id'] ?>
                                            <?php if (!empty($sale['invoice_number'])): ?>
                                                <span class="text-muted small fw-normal d-block"><?= e($sale['invoice_number']) ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if (!empty($sale['customer_id'])): ?>
                                            <a href="<?= BASE_URL ?>/customers/view?id=<?= $sale['customer_id'] ?>" class="text-decoration-none fw-medium text-dark">
                                                <?= e($sale['customer_name']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted italic"><?= e($sale['customer_name'] ?? 'Walk-in') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-medium">
                                        ₵<?= number_format($sale['total_amount'], 2) ?>
                                    </td>
                                    <td class="text-end text-success">
                                        ₵<?= number_format($sale['paid_amount'], 2) ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($balanceDue > 0): ?>
                                            <span class="text-danger fw-bold fs-6">₵<?= number_format($balanceDue, 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-success fw-medium">₵0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($sale['voided']): ?>
                                            <span class="google-badge badge-subtle-danger">Voided</span>
                                        <?php elseif ($sale['payment_status'] === 'paid'): ?>
                                            <span class="google-badge badge-subtle-success">Paid</span>
                                        <?php elseif ($sale['payment_status'] === 'partial'): ?>
                                            <span class="google-badge badge-subtle-warning">Partial</span>
                                        <?php else: ?>
                                            <span class="google-badge badge-subtle-danger">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" 
                                           class="btn btn-sm <?= ($balanceDue > 0) ? 'btn-outline-danger' : 'btn-outline-secondary' ?> rounded-pill px-3 py-1">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 16px;">
                                                <?= ($balanceDue > 0) ? 'payments' : 'visibility' ?>
                                            </span>
                                            <?= ($balanceDue > 0) ? 'Settle Debt' : 'View' ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined fs-2 mb-2 d-block text-secondary">receipt_long</span>
                                    No invoices matching your criteria.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Showing page <?= $page ?> of <?= $totalPages ?> (Total <?= $totalRecords ?> invoices)
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($filters['search']) ?>&status=<?= $filters['status'] ?>&start_date=<?= $filters['start_date'] ?>&end_date=<?= $filters['end_date'] ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
