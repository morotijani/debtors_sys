<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Load Environment Variables
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Set Application Timezone
$timezone = $_ENV['APP_TIMEZONE'] ?? 'Africa/Accra';
date_default_timezone_set($timezone);

require_once __DIR__ . '/../src/Config/Router.php';
require_once __DIR__ . '/../src/Config/Security.php';
require_once __DIR__ . '/../src/Config/Helpers.php';

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\CustomerController;
use App\Controllers\SaleController;
use App\Controllers\DebtorController;
use App\Controllers\UserController;
use App\Controllers\ProfileController;
use App\Controllers\SettingController;
use App\Middleware\AuthMiddleware;
use App\Config\Database;

// Start session
session_start();

// Initialize Router
$router = new Router();

// Define Base URL for subfolder support
$scriptName = $_SERVER['SCRIPT_NAME'];
$baseUrl = str_replace('\\', '/', dirname($scriptName));
if ($baseUrl === '/' || $baseUrl === '\\' || $baseUrl === '.') {
    $baseUrl = '';
}
$baseUrl = rtrim($baseUrl, '/');

if (substr($baseUrl, -7) === '/public') {
    $baseUrl = substr($baseUrl, 0, -7);
}

define('BASE_URL', $baseUrl);

// Default Route
$router->get('/', function () {
    if (AuthMiddleware::isAuthenticated()) {
        header('Location: ' . BASE_URL . '/dashboard');
    } else {
        header('Location: ' . BASE_URL . '/login');
    }
    exit;
});

// PWA Manifest
$router->get('/manifest.json', function () {
    $pdo = \App\Config\Database::getInstance();
    $settingModel = new \App\Models\Setting($pdo);
    $settings = $settingModel->get();

    $manifest = [
        "name" => ($settings['company_name'] ?? "Debtors System") . " - Debtors",
        "short_name" => "Debtors",
        "start_url" => ".",
        "display" => "standalone",
        "background_color" => "#f8f9fa",
        "theme_color" => "#c5221f",
        "description" => "Dedicated Debtors and Credit Recovery System",
        "icons" => []
    ];

    if (!empty($settings['company_logo'])) {
        $manifest['icons'][] = [
            "src" => $settings['company_logo'],
            "sizes" => "192x192 512x512",
            "type" => "image/png",
            "purpose" => "any maskable"
        ];
    } else {
        $manifest['icons'][] = [
            "src" => "assets/icon.svg",
            "sizes" => "192x192 512x512",
            "type" => "image/svg+xml",
            "purpose" => "any maskable"
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($manifest, JSON_UNESCAPED_SLASHES);
    exit;
});

// Auth
$router->get('/login', [new AuthController(), 'login']);
$router->post('/login', [new AuthController(), 'login']);
$router->get('/logout', [new AuthController(), 'logout']);

// Dashboard
$router->get('/dashboard', [new DashboardController(), 'index']);

// Customers & Debtors
$customerController = new CustomerController();
$router->get('/customers', [$customerController, 'index']);
$router->post('/customers/create', [$customerController, 'create']);
$router->post('/customers/edit', [$customerController, 'edit']);
$router->get('/customers/view', [$customerController, 'view']);
$router->get('/customers/api-search', [$customerController, 'apiSearch']);
$router->post('/customers/delete', [$customerController, 'delete']);
$router->post('/customers/restore', [$customerController, 'restore']);
$router->post('/customers/repay-bulk', [$customerController, 'repayBulk']);

// Sales / Debt Invoices
$saleController = new SaleController();
$router->get('/sales', [$saleController, 'index']);
$router->get('/sales/create', [$saleController, 'create']);
$router->get('/sales/view', [$saleController, 'view']);
$router->post('/sales/pay', [$saleController, 'pay']);
$router->post('/sales/request-delete', [$saleController, 'requestDelete']);
$router->post('/sales/process-delete', [$saleController, 'processDeleteRequest']);
$router->post('/sales/return', [$saleController, 'returns']);

// Standalone Debtors
$debtorController = new DebtorController();
$router->get('/debtors', [$debtorController, 'index']);
$router->get('/debtors/create', [$debtorController, 'create']);
$router->post('/debtors/create', [$debtorController, 'create']);
$router->get('/debtors/payment', [$debtorController, 'recordPayment']);
$router->post('/debtors/payment', [$debtorController, 'recordPayment']);
$router->get('/debtors/history', [$debtorController, 'history']);
$router->get('/debtors/increase', [$debtorController, 'increaseDebt']);
$router->post('/debtors/increase', [$debtorController, 'increaseDebt']);
$router->post('/debtors/delete', [$debtorController, 'delete']);

// Users (Admin Only)
$userController = new UserController();
$router->get('/users', [$userController, 'index']);
$router->get('/users/create', [$userController, 'create']);
$router->post('/users/create', [$userController, 'create']);
$router->get('/users/edit', [$userController, 'edit']);
$router->post('/users/update', [$userController, 'update']);
$router->post('/users/toggle-status', [$userController, 'toggleStatus']);
$router->post('/users/update-role', [$userController, 'updateRole']);
$router->post('/users/delete', [$userController, 'delete']);

// Company Settings
$settingController = new SettingController();
$router->get('/settings', [$settingController, 'index']);
$router->post('/settings/update', [$settingController, 'update']);

// Profile
$profileController = new ProfileController();
$router->get('/profile', [$profileController, 'index']);
$router->post('/profile/update', [$profileController, 'update']);

// Status API
$router->get('/api/status', [new \App\Controllers\StatusController(), 'check']);

// Graceful fallbacks for main POS routes
$redirectDashboard = function() {
    header('Location: ' . BASE_URL . '/dashboard');
    exit;
};
$router->get('/items', $redirectDashboard);
$router->get('/cashier', $redirectDashboard);
$router->get('/expenses', $redirectDashboard);
$router->get('/expenditures', $redirectDashboard);
$router->get('/proformas', $redirectDashboard);
$router->get('/admin/finance', $redirectDashboard);
$router->get('/reports', $redirectDashboard);

// Dispatch
$router->dispatch();
