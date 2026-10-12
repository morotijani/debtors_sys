<?php
$title = "Customers & Debtors";
ob_start();
$isOwingOnly = isset($_GET['owing']) && $_GET['owing'] == '1';
?>
<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: #fce8e6; color: #c5221f;">
                <span class="material-symbols-outlined align-middle me-2">error</span>
                <?= $_GET['error'] === 'phone_exists' ? 'A customer with that phone number already exists.' : e($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background: #e6f4ea; color: #137333;">
                <span class="material-symbols-outlined align-middle me-2">check_circle</span>
                <?= e($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <style>
            .google-search-wrapper {
                background: #fff;
                border-radius: 24px;
                padding: 4px 8px;
                display: flex;
                align-items: center;
                box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
                max-width: 600px;
                width: 100%;
            }
            .google-search-input {
                border: none;
                outline: none;
                padding: 8px 12px;
                flex-grow: 1;
                font-size: 15px;
            }
            .google-search-select {
                border: none;
                outline: none;
                padding: 8px 12px;
                color: #5f6368;
                background: transparent;
                font-size: 14px;
                cursor: pointer;
            }
            .google-divider {
                width: 1px;
                height: 24px;
                background-color: #dadce0;
                margin: 0 8px;
            }
            .google-btn-primary {
                background-color: #0b57d0;
                color: #fff;
                border-radius: 20px;
                padding: 8px 24px;
                font-weight: 500;
                border: none;
                transition: background-color 0.2s;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
            }
            .google-btn-primary:hover {
                background-color: #0842a0;
                color: #fff;
            }
            .google-btn-secondary {
                background-color: transparent;
                color: #444746;
                border-radius: 20px;
                padding: 8px 16px;
                font-weight: 500;
                border: 1px solid #747775;
                transition: background-color 0.2s;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
            }
            .google-btn-secondary:hover {
                background-color: #f1f3f4;
                color: #1f1f1f;
            }
            .google-table-card {
                background: #fff;
                border-radius: 24px;
                overflow: hidden;
                border: none;
                margin-bottom: 30px;
                box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
            }
            .action-btn {
                width: 36px;
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                border: none;
                background: transparent;
                transition: background-color 0.2s;
                text-decoration: none;
            }
            .action-btn:hover {
                background-color: #f1f3f4;
                color: #202124;
            }
            .google-badge {
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 12px;
                letter-spacing: 0.5px;
                font-weight: 600;
                display: inline-block;
            }
            .badge-subtle-success {
                background-color: #e6f4ea;
                color: #137333;
                border: 1px solid #ceead6;
            }
            .filter-tab {
                padding: 8px 18px;
                border-radius: 20px;
                text-decoration: none;
                font-weight: 500;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border: 1px solid transparent;
            }
            .filter-tab.active {
                background-color: #0b57d0;
                color: white !important;
            }
            .filter-tab.inactive {
                background-color: #f1f3f4;
                color: #444746;
            }
            .filter-tab.inactive:hover {
                background-color: #e3e3e3;
            }
        </style>

        <div class="d-flex justify-content-between flex-wrap align-items-center pt-4 pb-3 mb-2 gap-2">
            <div>
                <h1 class="h3 mb-1" style="color: #1f1f1f; font-weight: 500;">Customers & Debtors</h1>
                <div class="text-muted small">Manage customer accounts, inspect debt balances, and record payments</div>
            </div>
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <button type="button" class="google-btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#addCustomerModal">
                    <span class="material-symbols-outlined" style="font-size: 18px;">person_add</span> New Customer
                </button>
            </div>
        </div>

        <!-- Filter Tabs: All vs Owing Only -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/customers<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" 
                   class="filter-tab <?= !$isOwingOnly ? 'active' : 'inactive' ?>">
                    <span class="material-symbols-outlined" style="font-size: 18px;">group</span>
                    All Customers
                    <span class="badge <?= !$isOwingOnly ? 'bg-light text-primary' : 'bg-secondary text-white' ?> rounded-pill ms-1">
                        <?= $totalAllCount ?>
                    </span>
                </a>
                <a href="<?= BASE_URL ?>/customers?owing=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="filter-tab <?= $isOwingOnly ? 'active bg-danger border-danger' : 'inactive' ?>">
                    <span class="material-symbols-outlined" style="font-size: 18px;">error</span>
                    Owing Debtors Only
                    <span class="badge <?= $isOwingOnly ? 'bg-light text-danger' : 'bg-danger text-white' ?> rounded-pill ms-1">
                        <?= $totalOwingCount ?>
                    </span>
                </a>
            </div>

            <!-- Search Bar -->
            <form action="" method="GET" class="d-flex flex-grow-1 flex-md-grow-0" style="min-width: 320px;">
                <?php if ($isOwingOnly): ?>
                    <input type="hidden" name="owing" value="1">
                <?php endif; ?>
                <div class="google-search-wrapper w-100">
                    <span class="material-symbols-outlined text-muted" style="margin-left: 8px;">search</span>
                    <input type="text" name="search" id="customerSearchInput" class="google-search-input" placeholder="Search customer name or phone..."
                        value="<?= e($search ?? '') ?>">
                        
                    <div class="google-divider"></div>
                    <select name="sort" class="google-search-select" onchange="this.form.submit()">
                        <option value="total_debt" <?= ($sort == 'total_debt') ? 'selected' : '' ?>>Debt (High-Low)</option>
                        <option value="name" <?= ($sort == 'name') ? 'selected' : '' ?>>Name</option>
                        <option value="last_purchase" <?= ($sort == 'last_purchase') ? 'selected' : '' ?>>Recent Invoice</option>
                    </select>

                    <button type="submit" class="action-btn text-primary me-1">
                        <span class="material-symbols-outlined" style="font-size: 20px;">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead style="border-bottom: 1px solid #e3e3e3; background: #fafafa;">
                        <tr class="text-muted small text-uppercase">
                            <th style="width: 50px; padding: 16px 20px;">#</th>
                            <th style="padding: 16px 20px;">Customer Name</th>
                            <th style="padding: 16px 20px;">Phone</th>
                            <th style="padding: 16px 20px;">Address</th>
                            <th class="text-end" style="padding: 16px 20px;">Outstanding Debt</th>
                            <th style="padding: 16px 20px;">Last Invoice</th>
                            <th class="text-end" style="padding: 16px 20px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = $offset + 1;
                        foreach ($customers as $customer): ?>
                            <tr style="border-bottom: 1px solid #f1f3f4;">
                                <td class="text-muted fw-bold ps-4"><?= $i++ ?></td>
                                <td>
                                    <strong>
                                        <a href="<?= BASE_URL ?>/customers/view?id=<?= $customer['id'] ?>"
                                           class="text-decoration-none text-dark fs-6">
                                            <?= e($customer['name']) ?>
                                        </a>
                                    </strong>
                                </td>
                                <td><?= e($customer['phone'] ?: '-') ?></td>
                                <td class="text-muted small"><?= e($customer['address'] ?: '-') ?></td>
                                <td class="text-end">
                                    <?php if ($customer['total_debt'] > 0): ?>
                                        <span class="text-danger fw-bold fs-6">₵<?= number_format($customer['total_debt'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="google-badge badge-subtle-success">Fully Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="color: #5f6368; font-size: 13px;">
                                        <?= $customer['last_purchase'] ? date('M j, Y', strtotime($customer['last_purchase'])) : '-'; ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap pe-4">
                                    <?php if ($customer['total_debt'] > 0): ?>
                                        <a href="<?= BASE_URL ?>/customers/view?id=<?= $customer['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 me-1">
                                            <span class="material-symbols-outlined align-middle" style="font-size: 16px;">payments</span> Settle
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/customers/view?id=<?= $customer['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 me-1">
                                            Ledger
                                        </a>
                                    <?php endif; ?>

                                    <button type="button" class="action-btn text-primary" title="Edit Customer"
                                        data-bs-toggle="modal" data-bs-target="#editCustomerModal"
                                        data-id="<?= $customer['id'] ?>" data-name="<?= e($customer['name']) ?>"
                                        data-phone="<?= e($customer['phone'] ?? '') ?>"
                                        data-address="<?= e($customer['address'] ?? '') ?>">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                    </button>

                                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                        <button type="button" class="action-btn text-danger" title="Delete Customer"
                                            data-bs-toggle="modal" data-bs-target="#deleteCustomerModal"
                                            data-id="<?= $customer['id'] ?>" data-name="<?= e($customer['name']) ?>">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined fs-2 mb-2 d-block text-secondary">sentiment_satisfied</span>
                                    <?= $isOwingOnly ? 'No customers currently have outstanding debts matching your search!' : 'No customers found.' ?>
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
                        Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalCustomers) ?> of <?= $totalCustomers ?> customers
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search ?? '') ?>&sort=<?= $sort ?>&order=<?= $order ?><?= $isOwingOnly ? '&owing=1' : '' ?>">
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

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= BASE_URL ?>/customers/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Customer Name *</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Full name or company" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Phone Number</label>
                        <input type="text" name="phone" class="form-control rounded-3" placeholder="e.g. 0244123456">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Address / Location</label>
                        <textarea name="address" class="form-control rounded-3" rows="2" placeholder="Customer address"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= BASE_URL ?>/customers/edit" method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" id="edit_cust_id">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Customer Name *</label>
                        <input type="text" name="name" id="edit_cust_name" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Phone Number</label>
                        <input type="text" name="phone" id="edit_cust_phone" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Address / Location</label>
                        <textarea name="address" id="edit_cust_address" class="form-control rounded-3" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Customer Modal -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?= BASE_URL ?>/customers/delete" method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" id="del_cust_id">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">Delete Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    Are you sure you want to remove <strong id="del_cust_name"></strong>?
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var editModal = document.getElementById('editCustomerModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            var btn = event.relatedTarget;
            document.getElementById('edit_cust_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_cust_name').value = btn.getAttribute('data-name');
            document.getElementById('edit_cust_phone').value = btn.getAttribute('data-phone');
            document.getElementById('edit_cust_address').value = btn.getAttribute('data-address');
        });
    }

    var delModal = document.getElementById('deleteCustomerModal');
    if (delModal) {
        delModal.addEventListener('show.bs.modal', function(event) {
            var btn = event.relatedTarget;
            document.getElementById('del_cust_id').value = btn.getAttribute('data-id');
            document.getElementById('del_cust_name').textContent = btn.getAttribute('data-name');
        });
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
