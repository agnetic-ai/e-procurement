<?php
class PaymentTermsModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function GetPaymentTerms()
    {
        $query = "SELECT id paymentId,
                    payment_code paymentCode,
                    payment_name paymentName,
                    payment_days paymentDays,
                    payment_description paymentDescription,
                    isActive,
                    createdAt,
                    updatedAt
                FROM payment_terms
                WHERE isActive = 1;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
