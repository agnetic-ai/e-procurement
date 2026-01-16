<?php
class InvoiceVerificationController extends Controller
{
    private $inv;
    private $verify;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->inv = new InvoiceModel();
        $this->verify = new InvoiceVerificationModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Invoice Verification',
            'subtitle' => 'Manage yout invoice and detail'
        ];
        $this->view('invoiceVerification/index', $data);
    }

    public function InvoiceVerify()
    {
        $invNumber = $_GET['invNumber'] ?? null;
        $data = [
            'title' => 'Verification Invoice',
            'subtitle' => 'Manage your invoice verification',
        ];
        $this->view('invoiceVerification/invoiceVerify', $data);
    }
}
