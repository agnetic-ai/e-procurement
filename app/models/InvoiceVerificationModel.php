<?php
class InvoiceVerificationModel
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
}
