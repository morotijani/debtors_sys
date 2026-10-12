<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Sale;
use App\Models\Item;
use App\Models\Customer;
use App\Middleware\AuthMiddleware;
use Exception;

class SaleController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        
        // Store current URL with filters for "Back" button persistence
        $_SESSION['last_sales_url'] = ($_SERVER['REQUEST_URI'] ?? '');

        $pdo = Database::getInstance();
        $saleModel = new Sale($pdo);
        
        // Filters
        $filters = [
            'search' => $_GET['search'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'status' => $_GET['status'] ?? 'all',
            'delete_request' => $_GET['delete_request'] ?? '',
            'show_voided' => $_GET['show_voided'] ?? 'no'
        ];

        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;

        $totalRecords = $saleModel->countAll($filters);
        $totalPages = ceil($totalRecords / $limit);
        
        // Ensure page is valid
        if ($page < 1) $page = 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

        $sales = $saleModel->getAll($filters, $page, $limit);
        
        require __DIR__ . '/../../views/sales/index.php';
    }

    public function create() {
        // Redirection in debtors mini system
        header('Location: ' . BASE_URL . '/sales');
        exit;
    }

    public function view() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }

        $pdo = Database::getInstance();
        $saleModel = new Sale($pdo);
        $sale = $saleModel->getById($id);

        if (!$sale) {
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }

        // Fetch payment history
        $stmt = $pdo->prepare("SELECT p.*, COALESCE(u.username, 'System') as username FROM payments p LEFT JOIN users u ON p.recorded_by = u.id WHERE p.sale_id = :sid ORDER BY p.payment_date DESC");
        $stmt->execute(['sid' => $id]);
        $payments = $stmt->fetchAll();

        // Fetch Return history
        $stmtRet = $pdo->prepare("SELECT sr.*, COALESCE(u.username, 'System') as returner_name FROM sale_returns sr LEFT JOIN users u ON sr.recorded_by = u.id WHERE sr.sale_id = :sid ORDER BY sr.created_at DESC");
        try {
            $stmtRet->execute(['sid' => $id]);
            $returns = $stmtRet->fetchAll();
        } catch (\Exception $e) {
            $returns = [];
        }

        foreach ($returns as &$row) {
            $stmtRI = $pdo->prepare("SELECT sri.*, i.name as item_name FROM sale_return_items sri JOIN items i ON sri.item_id = i.id WHERE sri.return_id = :rid");
            $stmtRI->execute(['rid' => $row['id']]);
            $row['details'] = $stmtRI->fetchAll();
        }

        // Fetch Settings
        $settingModel = new \App\Models\Setting($pdo);
        $settings = $settingModel->get();

        $returnUrl = $_SESSION['last_sales_url'] ?? (BASE_URL . '/sales');

        require __DIR__ . '/../../views/sales/view.php';
    }

    public function pay() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $amount = (float)$_POST['amount'];
            
            if ($amount <= 0) {
                $_SESSION['error'] = "Invalid payment amount.";
                header('Location: ' . (BASE_URL . '/sales/view?id=' . $saleId));
                exit;
            }

            $pdo = Database::getInstance();
            $paymentModel = new \App\Models\Payment($pdo);
            $paymentModel->recordPayment($saleId, $amount, $_SESSION['user_id']);
            $_SESSION['success'] = "Payment of ₵" . number_format($amount, 2) . " successfully recorded for Invoice #$saleId.";
            
            $returnUrl = $_SESSION['last_sales_url'] ?? (BASE_URL . '/sales/view?id=' . $saleId);
            header('Location: ' . $returnUrl);
            exit;
        }
    }

    public function requestDelete() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $pdo = Database::getInstance();
            $saleModel = new Sale($pdo);
            $saleModel->approveDelete($saleId);
            $_SESSION['success'] = "Invoice #$saleId voided.";
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }
    }

    public function processDeleteRequest() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $action = $_POST['action'];
            $pdo = Database::getInstance();
            $saleModel = new Sale($pdo);
            if ($action === 'approve') {
                $saleModel->approveDelete($saleId);
                $_SESSION['success'] = "Invoice #$saleId voided.";
            }
            header('Location: ' . BASE_URL . '/sales');
            exit;
        }
    }

    public function returns() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $saleId = $_POST['sale_id'];
            $returnedItems = $_POST['returns'] ?? [];
            $pdo = Database::getInstance();
            $saleModel = new Sale($pdo);
            try {
                $saleModel->processReturn($saleId, $returnedItems, $_SESSION['user_id']);
                $_SESSION['success'] = "Return processed for Invoice #$saleId.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Return failed: " . $e->getMessage();
            }
            header('Location: ' . BASE_URL . '/sales/view?id=' . $saleId);
            exit;
        }
    }
}
