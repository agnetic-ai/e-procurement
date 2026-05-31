<?php
class PaymentModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetListPaymentInvoice()
    {
        $invNumber = $_POST['inv_number'] ?? '';

        $query = "
            SELECT
                inv.id AS invoiceId,
                inv.invoice_number AS invoiceNo,
                v.company_name AS vendorName,
                DATE_FORMAT(inv.due_date, '%d %b %Y') AS dueDate,
                FORMAT(inv.total_amount, 'id-ID') AS totalAmount,
                FORMAT(IFNULL(SUM(ip.paid_amount), 0), 'id-ID') AS paidAmount,
                FORMAT((inv.total_amount - IFNULL(SUM(ip.paid_amount), 0)), 'id-ID') AS remainingAmount,
                sc.status_name AS statusName,
                inv.status_code AS statusCode
            FROM invoices inv
            JOIN vendors v
                ON v.id = inv.vendor_id
            JOIN status_codes sc
                ON sc.status_code = inv.status_code
                AND sc.module_code = 'INV'
            LEFT JOIN payments ip
                ON ip.invoice_id = inv.id
            WHERE inv.status_code = 'INV_VERIFIED'
        ";

        $params = [];

        if (!empty($invNumber)) {
            $query .= " AND inv.invoice_number LIKE ?";
            $params[] = "%{$invNumber}%";
        }

        $query .= "
        GROUP BY
            inv.id,
            inv.invoice_number,
            v.company_name,
            inv.due_date,
            inv.total_amount,
            sc.status_name,
            inv.status_code
        HAVING remainingAmount > 0
        ORDER BY inv.due_date ASC";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error fetching payment invoice list: ' . $e->getMessage());
            return [];
        }
    }

    public function GetPaymentInvoiceSummary($invNumber)
    {
        $query = "
            SELECT
                inv.id AS invoiceId,
                inv.invoice_number AS invoiceNumber,
                v.company_name AS vendorName,
                DATE_FORMAT(inv.due_date, '%d %b %Y') AS dueDate,
                FORMAT(inv.total_amount, 'id-ID')AS totalAmount,
                FORMAT(IFNULL(SUM(ip.paid_amount), 0), 'id-ID') AS paidAmount,
                FORMAT((inv.total_amount - IFNULL(SUM(ip.paid_amount), 0)), 'id-ID') AS remainingAmount,
                inv.status_code AS statusCode
            FROM invoices inv
            JOIN vendors v
                ON v.id = inv.vendor_id
            LEFT JOIN payments ip
                ON ip.invoice_id = inv.id
            WHERE inv.invoice_number = :invoice_number
            GROUP BY
                inv.id,
                inv.invoice_number,
                v.company_name,
                inv.due_date,
                inv.total_amount,
                inv.status_code;";
        $params = [":invoice_number" => $invNumber];
        $stmt = $this->db->prepare($query);

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetPaymentVendorBankAccount($invNumber)
    {
        $query = "
            SELECT
                vba.bank_name AS bankName,
                vba.account_number AS accountNumber,
                vba.account_name AS accountName
            FROM vendors v
            JOIN vendor_bank_accounts vba
                ON vba.vendor_id = v.id
                AND vba.is_primary = 1
            JOIN invoices inv
                ON inv.vendor_id = v.id
            WHERE inv.invoice_number = :invoice_number
            LIMIT 1;";
        $params = [":invoice_number" => $invNumber];
        $stmt = $this->db->prepare($query);

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function SubmitPayment(array $payload)
    {
        try {

            $this->db->beginTransaction();


            $stmt = $this->db->prepare("
                SELECT id, total_amount, status_code
                FROM invoices
                WHERE id = :invoiceId
                FOR UPDATE
            ");

            $stmt->execute([
                ':invoiceId' => $payload['invoice_id']
            ]);

            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$invoice) {
                throw new Exception('Invoice not found.');
            }

            if ($invoice['status_code'] !== 'INV_VERIFIED') {
                throw new Exception('Invoice must be VERIFIED before payment.');
            }

            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM payments
                WHERE invoice_id = :invoiceId
            ");

            $stmt->execute([
                ':invoiceId' => $payload['invoice_id']
            ]);

            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Invoice already paid.');
            }

            if ((float)$payload['paid_amount'] !== (float)$invoice['total_amount']) {
                throw new Exception('Paid amount must be equal to invoice total.');
            }

            if (empty($_FILES['payment_proof'])) {
                throw new Exception('Payment proof is required.');
            }

            $proofPath = FileUploadHelper::upload(
                $_FILES['payment_proof'],
                'uploads/payments/' . date('Y/m')
            );

            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    invoice_id,
                    payment_date,
                    paid_amount,
                    payment_method,
                    reference_number,
                    payment_proof,
                    notes,
                    created_by
                ) VALUES (
                    :invoiceId,
                    :paymentDate,
                    :paidAmount,
                    :method,
                    :reference,
                    :proof,
                    :notes,
                    :userId
                )
            ");

            $stmt->execute([
                ':invoiceId'   => $payload['invoice_id'],
                ':paymentDate' => $payload['payment_date'],
                ':paidAmount'  => $payload['paid_amount'],
                ':method'      => $payload['payment_method'],
                ':reference'   => $payload['reference_number'] ?? null,
                ':proof'       => $proofPath,
                ':notes'       => $payload['notes'] ?? null,
                ':userId'      => $payload['createdBy']
            ]);

            $stmt = $this->db->prepare("
                UPDATE invoices
                SET status_code = 'INV_PAID'
                WHERE id = :invoiceId
            ");

            $stmt->execute([
                ':invoiceId' => $payload['invoice_id']
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Payment completed successfully.'
            ];
        } catch (Throwable $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
