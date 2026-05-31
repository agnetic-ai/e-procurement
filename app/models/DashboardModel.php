<?php
class DashboardModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get summary stat cards
     */
    public function getStats()
    {
        $stats = [];

        // Total Vendors (active)
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM vendors WHERE status_code = 'VEND_ACTIVE'");
        $stats['vendors_active'] = (int)$stmt->fetchColumn();

        // Total Products
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products WHERE status_code = 'PRODUCT_ACTIVE'");
        $stats['products_active'] = (int)$stmt->fetchColumn();

        // Purchase Orders count
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM purchase_orders");
        $stats['po_total'] = (int)$stmt->fetchColumn();

        // PO by status
        $stmt = $this->db->query("SELECT status_code, COUNT(*) as cnt FROM purchase_orders GROUP BY status_code");
        $stats['po_by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Pending Approvals (PO + PR)
        $stmt = $this->db->query("SELECT COUNT(*) FROM purchase_orders WHERE status_code IN ('PO_DRAFT','PO_PARTIAL')");
        $stats['po_pending'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM purchase_requests WHERE status_code = 'PR_DRAFT'");
        $stats['pr_pending'] = (int)$stmt->fetchColumn();

        // Total Spent (from completed POs)
        $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM purchase_orders WHERE status_code IN ('PO_COMPLETED','PO_PARTIAL')");
        $stats['total_spent'] = (float)$stmt->fetchColumn();

        // Invoices
        $stmt = $this->db->query("SELECT COUNT(*) FROM invoices");
        $stats['invoices_total'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM invoices WHERE status_code = 'INV_PAID'");
        $stats['invoices_paid'] = (int)$stmt->fetchColumn();

        // Overdue invoices (due_date < today AND not paid)
        $stmt = $this->db->query("SELECT COUNT(*) FROM invoices WHERE due_date < CURDATE() AND status_code NOT IN ('INV_PAID','INV_CANCELLED')");
        $stats['invoices_overdue'] = (int)$stmt->fetchColumn();

        // Payments total
        $stmt = $this->db->query("SELECT COALESCE(SUM(paid_amount), 0) FROM payments");
        $stats['total_paid'] = (float)$stmt->fetchColumn();

        // Goods Receipts
        $stmt = $this->db->query("SELECT COUNT(*) FROM goods_receipts");
        $stats['gr_total'] = (int)$stmt->fetchColumn();

        // Employees
        $stmt = $this->db->query("SELECT COUNT(*) FROM employees");
        $stats['employees_total'] = (int)$stmt->fetchColumn();

        return $stats;
    }

    /**
     * Get recent purchase orders
     */
    public function getRecentPOs($limit = 5)
    {
        $sql = "SELECT po.id, po.po_number, po.po_date AS order_date, po.total_amount,
                       po.status_code, sc.status_name,
                       v.company_name AS vendor_name
                FROM purchase_orders po
                JOIN vendors v ON po.vendor_id = v.id
                LEFT JOIN status_codes sc ON po.status_code = sc.status_code AND sc.module_code = 'PO'
                ORDER BY po.id DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent purchase requests
     */
    public function getRecentPRs($limit = 5)
    {
        $sql = "SELECT pr.id, pr.pr_number, pr.request_date, pr.total_estimated AS estimated_total,
                       pr.status_code, sc.status_name,
                       e.full_name AS requester_name
                FROM purchase_requests pr
                LEFT JOIN employees e ON pr.requested_by = e.id
                LEFT JOIN status_codes sc ON pr.status_code = sc.status_code AND sc.module_code = 'PR'
                ORDER BY pr.id DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get monthly PO spending (last 6 months)
     */
    public function getMonthlySpending()
    {
        $sql = "SELECT DATE_FORMAT(po_date, '%Y-%m') AS month_key,
                       DATE_FORMAT(po_date, '%b %Y') AS month_label,
                       SUM(total_amount) AS total
                FROM purchase_orders
                WHERE po_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY month_key, month_label
                ORDER BY month_key ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get top vendors by PO amount
     */
    public function getTopVendors($limit = 5)
    {
        $sql = "SELECT v.company_name, SUM(po.total_amount) AS total_spent, COUNT(po.id) AS po_count
                FROM purchase_orders po
                JOIN vendors v ON po.vendor_id = v.id
                GROUP BY v.id, v.company_name
                ORDER BY total_spent DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get PO status distribution for chart
     */
    public function getPOStatusDistribution()
    {
        $sql = "SELECT sc.status_name, COUNT(*) as cnt
                FROM purchase_orders po
                JOIN status_codes sc ON po.status_code = sc.status_code AND sc.module_code = 'PO'
                GROUP BY sc.status_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
