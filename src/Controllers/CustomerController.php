<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\Customer;
use App\Middleware\AuthMiddleware;

class CustomerController {
    
    public function index() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();
        $customerModel = new Customer($pdo);
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $sort = $_GET['sort'] ?? 'total_debt';
        $order = $_GET['order'] ?? 'DESC';
        $owingOnly = isset($_GET['owing']) && $_GET['owing'] == '1';
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $_SESSION['last_customers_url'] = ($_SERVER['REQUEST_URI'] ?? '');

        $customers = $customerModel->getAll($limit, $offset, $search, $sort, $order, $owingOnly);
        $totalCustomers = $customerModel->countAll($search, $owingOnly);
        $totalPages = ceil($totalCustomers / $limit);
        
        $totalAllCount = $customerModel->countAll($search, false);
        $totalOwingCount = $customerModel->countAll($search, true);

        require __DIR__ . '/../../views/customers/index.php';
    }

    public function create() {
        AuthMiddleware::requireLogin();
        
        $isJson = false;
        if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
            $isJson = true;
            $input = json_decode(file_get_contents('php://input'), true);
            $name = $input['name'] ?? '';
            $phone = $input['phone'] ?? '';
            $address = $input['address'] ?? '';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
        } else {
            header('Location: ' . BASE_URL . '/customers'); 
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = Database::getInstance();
            $customerModel = new Customer($pdo);
            
            // Validation: Check for unique phone number
            if (!empty($phone)) {
                $existing = $customerModel->findByPhone($phone);
                if ($existing) {
                    if ($isJson) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Phone number already exists for another customer.'
                        ]);
                        exit;
                    } else {
                        header('Location: ' . BASE_URL . '/customers?error=phone_exists');
                        exit;
                    }
                }
            }

            $userId = $_SESSION['user_id'] ?? null;
            $newId = $customerModel->create($name, $phone, $address, $userId);
            
            if ($isJson) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'customer' => [
                        'id' => $newId, 
                        'name' => $name,
                        'phone' => $phone
                    ]
                ]);
                exit;
            }

            header('Location: ' . BASE_URL . '/customers?success=Customer added successfully');
            exit;
        }
    }
    
    public function apiSearch() {
        AuthMiddleware::requireLogin(); 
        $query = $_GET['q'] ?? '';
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT id, name, phone FROM customers WHERE (name LIKE :q OR phone LIKE :q) AND is_deleted = 0 ORDER BY name ASC LIMIT 10");
        $stmt->execute(['q' => "%$query%"]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    public function edit() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';

            $pdo = Database::getInstance();
            $customerModel = new Customer($pdo);
            
            if (!empty($phone)) {
                $existing = $customerModel->findByPhone($phone, $id);
                if ($existing) {
                    header('Location: ' . BASE_URL . '/customers?error=phone_exists');
                    exit;
                }
            }
            
            $customerModel->update($id, $name, $phone, $address);
            header('Location: ' . BASE_URL . '/customers?success=Customer updated successfully');
            exit;
        }
        header('Location: ' . BASE_URL . '/customers');
    }

    public function delete() {
        AuthMiddleware::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $pdo = Database::getInstance();
            $customerModel = new Customer($pdo);
            $customerModel->softDelete($id);
            header('Location: ' . BASE_URL . '/customers?success=Customer soft-deleted');
            exit;
        }
    }

    public function restore() {
        AuthMiddleware::requireAdmin();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/customers?error=Missing ID');
            exit;
        }

        $pdo = Database::getInstance();
        $customerModel = new Customer($pdo);
        $customerModel->restore($id);
        header('Location: ' . BASE_URL . '/customers?success=Customer restored');
        exit;
    }

    public function view() {
        AuthMiddleware::requireLogin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/customers');
            exit;
        }

        $_SESSION['last_sales_url'] = ($_SERVER['REQUEST_URI'] ?? '');

        $pdo = Database::getInstance();
        $customerModel = new Customer($pdo);
        
        $customer = $customerModel->find($id);
        if (!$customer) {
            header('Location: ' . BASE_URL . '/customers');
            exit;
        }

        $history = $customerModel->getHistory($id);
        $debtPayments = $customerModel->getDebtPaymentHistory($id);

        $mergedEvents = [];
        foreach ($history as $sale) {
            $mergedEvents[] = [
                'type' => 'sale',
                'date' => $sale['created_at'],
                'data' => $sale
            ];
        }
        foreach ($debtPayments as $pay) {
            $mergedEvents[] = [
                'type' => 'payment',
                'date' => $pay['payment_date'],
                'data' => $pay
            ];
        }

        usort($mergedEvents, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $groupedHistory = [];
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        foreach ($mergedEvents as $event) {
            $dateKey = date('Y-m-d', strtotime($event['date']));
            $label = match($dateKey) {
                $today => 'Today',
                $yesterday => 'Yesterday',
                default => date('jS F, Y', strtotime($event['date']))
            };
            $groupedHistory[$label][] = $event;
        }

        require __DIR__ . '/../../views/customers/view.php';
    }

    public function repayBulk() {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = $_POST['customer_id'] ?? null;
            $amount = (float)($_POST['amount'] ?? 0);

            if (!$customerId || $amount <= 0) {
                header('Location: ' . BASE_URL . '/customers?error=Invalid payment data');
                exit;
            }

            $pdo = Database::getInstance();
            $saleModel = new \App\Models\Sale($pdo);
            try {
                $result = $saleModel->repayBulkDebt($customerId, $amount, $_SESSION['user_id']);
                if ($result) {
                    $ids = array_column($result['affected_sales'], 'id');
                    $msg = "Bulk debt payment of ₵" . number_format($amount, 2) . " processed successfully! ";
                    $msg .= "Applied to Invoices: #" . implode(', #', $ids);
                    $_SESSION['success'] = $msg;
                } else {
                    $_SESSION['error'] = "Bulk payment could not be processed.";
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = "Payment Error: " . $e->getMessage();
            }
            
            header('Location: ' . BASE_URL . '/customers/view?id=' . $customerId);
            exit;
        }
    }
}
