<?php
if (!isset($settings)) {
    $pdo = \App\Config\Database::getInstance();
    $settingModel = new \App\Models\Setting($pdo);
    $settings = $settingModel->get();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['company_name'] ?? 'Debtors System') ?> - Debtors & Debt Recovery</title>
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/icon.svg">

    <?php if (!empty($settings['company_logo'])): ?>
        <link rel="icon" type="image/png" href="<?= BASE_URL ?>/<?= htmlspecialchars($settings['company_logo']) ?>">
    <?php endif; ?>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Material Symbols Outlined -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css?v=2.0" rel="stylesheet">
    <style>
    :root {
        --bs-font-sans-serif: 'Google Sans Flex', 'Google Sans', 'Product Sans', 'Roboto', system-ui, -apple-system, sans-serif !important;
        --bs-body-font-family: 'Google Sans Flex', 'Google Sans', 'Product Sans', 'Roboto', system-ui, -apple-system, sans-serif !important;
        --md-font-family: 'Google Sans Flex', 'Google Sans', 'Product Sans', 'Roboto Flex', 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
    }

    body, button, input, select, textarea, .nav-link, .navbar-brand, .table, .card, h1, h2, h3, h4, h5, h6, .btn, .badge {
        font-family: var(--md-font-family) !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f0f4f9;
            color: #1f1f1f;
        }

        @media (min-width: 768px) {
            .nav-side-left,
            .nav-side-right {
                flex: 1 1 0% !important;
            }
            .col-md-9, main {
                margin-left: 256px !important;
                width: calc(100% - 256px) !important;
                padding: 24px !important;
                max-width: 1400px;
            }
        }

        @media (max-width: 767.98px) {
            body {
                padding-top: 110px !important;
            }
            .sidebar {
                top: 110px !important;
            }
        }

        /* Top Google Header */
        .google-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e3e3;
            min-height: 64px;
            padding: 8px 0;
        }

        .google-search-container {
            background-color: #f1f3f4;
            border-radius: 24px;
            height: 48px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            transition: background-color 0.2s, box-shadow 0.2s;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }

        .google-search-container:focus-within {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        .google-search-input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            padding: 8px 16px;
            font-size: 14px;
            color: #1f1f1f;
        }

        .google-search-icon {
            color: #5f6368;
            font-size: 22px;
        }

        /* Google Material 3 Sidebar - Exact match to machine_mvp */
        .sidebar {
            position: fixed;
            top: 64px;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            box-shadow: none;
            background-color: #f0f4f9 !important;
            width: 256px;
            border-right: 1px solid #dadce0;
        }

        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 64px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .sidebar-sticky::-webkit-scrollbar {
            display: none;
        }

        .nav-flex-column {
            padding-right: 12px;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }

        .nav-flex-column .nav-item {
            margin-bottom: 4px;
        }

        .nav-flex-column .nav-link {
            color: #444746 !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            border-radius: 0 24px 24px 0 !important;
            margin-right: 12px !important;
            padding: 10px 12px 10px 24px !important;
            display: flex !important;
            align-items: center !important;
            transition: background-color 0.15s ease, color 0.15s ease !important;
            text-decoration: none !important;
        }

        .nav-flex-column .nav-link:hover {
            background-color: rgba(68, 71, 70, 0.08) !important;
            color: #202124 !important;
        }

        .nav-flex-column .nav-link.active {
            color: #0b57d0 !important;
            background-color: #c2e7ff !important;
            font-weight: 600 !important;
        }

        .nav-flex-column .nav-link .icon {
            margin-right: 16px !important;
            font-size: 20px !important;
            width: 24px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: inherit !important;
        }

        .nav-flex-column .nav-link.text-danger {
            color: #ba1a1a !important;
        }

        .nav-flex-column .nav-link.text-danger:hover {
            background-color: #ffdad6 !important;
            color: #93000a !important;
        }

        .sidebar-heading {
            font-size: 0.72rem !important;
            letter-spacing: 0.5px !important;
            color: #747775 !important;
            font-weight: 600 !important;
            padding: 0.75rem 1.5rem 0.25rem !important;
        }
    </style>
</head>

<body>
    <!-- Top Fixed Navbar -->
    <header class="navbar fixed-top p-0 google-header no-print flex-wrap flex-md-nowrap align-items-center">
        <!-- Top Row on Mobile: Toggler + Brand + Profile -->
        <div class="d-flex nav-side-left w-100 w-md-auto align-items-center justify-content-between px-2 px-md-0 order-1">
            <div class="d-flex align-items-center">
                <!-- Mobile Toggle -->
                <button class="navbar-toggler d-lg-none collapsed border-0 p-1 me-2 ms-2" type="button"
                    data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="material-symbols-outlined" style="font-size: 24px; color: #5f6368;">menu</span>
                </button>

                <a class="navbar-brand me-0 px-md-4 fs-5 fw-bold d-flex align-items-center text-decoration-none"
                    href="<?= BASE_URL ?>/dashboard">
                    <span class="me-2 d-flex align-items-center">
                        <?php if (!empty($settings['company_logo'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['company_logo']) ?>" alt="Company Logo"
                                class="img-fluid" style="max-height: 32px; border-radius: 4px;">
                        <?php else: ?>
                            <span class="material-symbols-outlined icon me-2 text-primary" style="font-size: 28px;">account_balance_wallet</span>
                        <?php endif; ?>
                    </span>
                    <span style="color: #444746; font-size: 22px; font-weight: 400; letter-spacing: -0.5px;">
                        <?= e($settings['company_name'] ?? 'POS') ?>
                    </span>
                    <span class="badge rounded-pill ms-2 px-2 py-1 text-uppercase"
                        style="background-color: #fee2e2; color: #b91c1c; font-size: 10px; font-weight: 600; letter-spacing: 0.5px;">
                        Debtors
                    </span>
                </a>
            </div>

            <!-- Mobile Profile -->
            <div class="d-md-none d-flex align-items-center pe-3">
                <a href="<?= BASE_URL ?>/profile" class="d-block link-dark text-decoration-none" title="My Profile">
                    <?php if (!empty($_SESSION['profile_image'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($_SESSION['profile_image']) ?>"
                            class="rounded-circle border" style="width: 32px; height: 32px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px; font-weight: 500; background-color: #0b57d0; color: white;">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mx-3 mx-md-auto d-flex justify-content-center align-items-center w-100 mt-2 mt-md-0 mb-2 mb-md-0 order-3 order-md-2"
            style="max-width: 600px;">
            <form action="<?= BASE_URL ?>/sales" method="GET" class="w-100">
                <div class="google-search-container w-100">
                    <span class="material-symbols-outlined google-search-icon">search</span>
                    <input class="google-search-input" type="text" name="search" placeholder="Search customer, invoice #, phone..." aria-label="Search"
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </form>

            <div id="statusIndicators" class="d-flex gap-2 ms-2">
                <span id="dbStatus" class="badge rounded-pill bg-success d-flex align-items-center"
                    title="Database: debtors_db" style="padding: 6px 10px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">database</span>
                </span>
            </div>
        </div>

        <!-- Right Side: User Profile (Desktop) -->
        <div class="navbar-nav nav-side-right flex-row align-items-center justify-content-end pe-4 d-none d-md-flex order-2 order-md-3">
            <div class="nav-item text-nowrap d-flex align-items-center gap-2">
                <div class="text-end me-2 d-none d-xl-block">
                    <div class="fw-medium text-dark small"><?= e($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Admin') ?></div>
                    <div class="text-muted" style="font-size: 11px; text-transform: uppercase;"><?= e($_SESSION['role'] ?? 'admin') ?></div>
                </div>
                <a href="<?= BASE_URL ?>/profile" class="d-block link-dark text-decoration-none" title="My Profile">
                    <?php if (!empty($_SESSION['profile_image'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($_SESSION['profile_image']) ?>"
                            class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; font-weight: 500; font-size: 18px; background-color: #0b57d0; color: white;">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <div class="d-flex align-items-stretch w-100">
        <!-- Sidebar Navigation -->
        <nav class="sidebar collapse d-lg-block no-print" id="sidebarMenu">
            <div class="sidebar-sticky">
                <ul class="nav flex-column nav-flex-column mt-3">
                    
                    <!-- 1. Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?= (($_SERVER['REQUEST_URI'] ?? '') === BASE_URL . '/dashboard' || ($_SERVER['REQUEST_URI'] ?? '') === BASE_URL || ($_SERVER['REQUEST_URI'] ?? '') === BASE_URL . '/') ? 'active' : ''; ?>"
                            href="<?= BASE_URL ?>/dashboard">
                            <span class="material-symbols-outlined icon">dashboard</span> Dashboard
                        </a>
                    </li>

                    <!-- 2. Customers -->
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(($_SERVER['REQUEST_URI'] ?? ''), BASE_URL . '/customers') !== false) ? 'active' : ''; ?>"
                            href="<?= BASE_URL ?>/customers">
                            <span class="material-symbols-outlined icon">group</span> Customers
                        </a>
                    </li>

                    <!-- 3. Debt Invoices -->
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(($_SERVER['REQUEST_URI'] ?? ''), BASE_URL . '/sales') !== false) ? 'active' : ''; ?>"
                            href="<?= BASE_URL ?>/sales">
                            <span class="material-symbols-outlined icon">receipt_long</span> Debt Invoices
                        </a>
                    </li>

                    <!-- 4. Standalone Debtors -->
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(($_SERVER['REQUEST_URI'] ?? ''), BASE_URL . '/debtors') !== false) ? 'active' : ''; ?>"
                            href="<?= BASE_URL ?>/debtors">
                            <span class="material-symbols-outlined icon">person_search</span> Debt System
                        </a>
                    </li>

                    <?php if (in_array(($_SESSION['role'] ?? ''), ['admin', 'sales_cashier'])): ?>
                        <div class="my-2 border-top mx-3"></div>
                        
                        <!-- 5. Manage Users -->
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(($_SERVER['REQUEST_URI'] ?? ''), BASE_URL . '/users') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/users">
                                <span class="material-symbols-outlined icon">manage_accounts</span> Manage Users
                            </a>
                        </li>

                        <!-- 6. Company Settings -->
                        <li class="nav-item">
                            <a class="nav-link <?= (strpos(($_SERVER['REQUEST_URI'] ?? ''), BASE_URL . '/settings') !== false) ? 'active' : ''; ?>"
                                href="<?= BASE_URL ?>/settings">
                                <span class="material-symbols-outlined icon">admin_panel_settings</span> Company Settings
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Account Section -->
                    <li class="nav-item mt-3">
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center text-muted text-uppercase">
                            <span>Account</span>
                        </h6>
                        <ul class="nav flex-column nav-flex-column mb-2">
                            <li class="nav-item">
                                <a class="nav-link <?= (strpos(($_SERVER['REQUEST_URI'] ?? ''), '/profile') !== false) ? 'active' : ''; ?>"
                                    href="<?= BASE_URL ?>/profile">
                                    <span class="material-symbols-outlined icon">settings</span>
                                    Settings & Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger"
                                    href="<?= BASE_URL ?>/logout">
                                    <span class="material-symbols-outlined icon">logout</span>
                                    Sign out
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-grow-1 px-md-4">
            <?php echo $content; ?>
        </main>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        window.APP_BASE_URL = <?= json_encode(BASE_URL) ?>;
    </script>
</body>
</html>