<?php
namespace App\Controllers;

use App\Config\Database;
use App\Middleware\AuthMiddleware;
use App\Models\Debtor;

class DashboardController {
    public function index() {
        AuthMiddleware::requireLogin();
        $pdo = Database::getInstance();

        // 1. Sales Debt
        $stmt = $pdo->query("SELECT COUNT(*) as invoice_count, SUM(total_amount - paid_amount) as sales_debt FROM sales WHERE payment_status IN ('unpaid', 'partial') AND (voided = 0 OR voided IS NULL)");
        $salesDebtRow = $stmt->fetch();
        $salesDebt = (float)($salesDebtRow['sales_debt'] ?? 0);
        $unpaidInvoiceCount = (int)($salesDebtRow['invoice_count'] ?? 0);

        // 2. Standalone Debt
        $debtorModel = new Debtor($pdo);
        $standaloneDebt = (float)$debtorModel->getTotalOutstanding();
        $stmt = $pdo->query("SELECT COUNT(*) FROM standalone_debtors WHERE status != 'cleared'");
        $standaloneCount = (int)$stmt->fetchColumn();

        // 3. Total Combined Outstanding Debt
        $totalDebt = $salesDebt + $standaloneDebt;

        // 4. Debt Collections this month
        $thisMonthStart = date('Y-m-01 00:00:00');
        $thisMonthEnd = date('Y-m-t 23:59:59');

        // Invoice debt payments this month
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE payment_date BETWEEN :start AND :end");
        $stmt->execute(['start' => $thisMonthStart, 'end' => $thisMonthEnd]);
        $invoicePaymentsThisMonth = (float)$stmt->fetchColumn();

        // Standalone debt repayments this month
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM debt_repayments WHERE payment_date BETWEEN :start AND :end");
        $stmt->execute(['start' => date('Y-m-01'), 'end' => date('Y-m-t')]);
        $standalonePaymentsThisMonth = (float)$stmt->fetchColumn();

        $totalCollectedThisMonth = $invoicePaymentsThisMonth + $standalonePaymentsThisMonth;

        // 5. Customer stats
        $totalCustomers = (int)$pdo->query("SELECT COUNT(*) FROM customers WHERE is_deleted = 0")->fetchColumn();
        $owingCustomers = (int)$pdo->query("
            SELECT COUNT(DISTINCT customer_id) 
            FROM sales 
            WHERE payment_status IN ('unpaid', 'partial') AND (voided = 0 OR voided IS NULL) AND customer_id IS NOT NULL
        ")->fetchColumn();

        // 6. Top 8 Debtors
        $topDebtors = $pdo->query("
            SELECT 
                c.id, 
                c.name, 
                c.phone, 
                COUNT(s.id) as invoice_count, 
                SUM(s.total_amount - s.paid_amount) as total_debt,
                MAX(s.created_at) as last_invoice_date
            FROM customers c
            JOIN sales s ON c.id = s.customer_id
            WHERE s.payment_status IN ('unpaid', 'partial') AND (s.voided = 0 OR s.voided IS NULL) AND c.is_deleted = 0
            GROUP BY c.id, c.name, c.phone
            ORDER BY total_debt DESC
            LIMIT 8
        ")->fetchAll();

        // 7. Recent Payments (Latest 10)
        $recentPayments = $pdo->query("
            (
                SELECT 
                    'invoice' as payment_type,
                    p.id,
                    p.amount,
                    p.payment_date,
                    u.username as recorded_by_name,
                    COALESCE(c.name, 'Walk-in Customer') as party_name,
                    c.id as party_id,
                    s.id as reference_id,
                    s.invoice_number
                FROM payments p
                JOIN sales s ON p.sale_id = s.id
                LEFT JOIN customers c ON s.customer_id = c.id
                LEFT JOIN users u ON p.recorded_by = u.id
                ORDER BY p.payment_date DESC
                LIMIT 10
            )
            UNION ALL
            (
                SELECT 
                    'standalone' as payment_type,
                    dr.id,
                    dr.amount,
                    dr.payment_date,
                    u.username as recorded_by_name,
                    sd.name as party_name,
                    sd.id as party_id,
                    sd.id as reference_id,
                    NULL as invoice_number
                FROM debt_repayments dr
                JOIN standalone_debtors sd ON dr.debtor_id = sd.id
                LEFT JOIN users u ON dr.recorded_by = u.id
                ORDER BY dr.payment_date DESC
                LIMIT 10
            )
            ORDER BY payment_date DESC
            LIMIT 10
        ")->fetchAll();

        $settingModel = new \App\Models\Setting($pdo);
        $settings = $settingModel->get();

        require __DIR__ . '/../../views/dashboard/index.php';
    }
}
