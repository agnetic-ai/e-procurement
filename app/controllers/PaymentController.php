<?php
class PaymentController extends Controller
{
    private $pay;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->pay = new PaymentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Invoices Ready for Payment',
            "subtitle" => "Manage Payment Invoice"
        ];
        $this->view('payment/index', $data);
    }

    public function GetInvoicePayment()
    {
        try {
            $response = $this->pay->GetListPaymentInvoice();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function PaymentForm()
    {
        $invNo = $_GET['invoiceNo'] ?? null;
        $data = [
            'title' => 'Invoices Ready for Payment',
            'subtitle' => "Manage Payment Invoice",
            'invoiceSummary' => $this->pay->GetPaymentInvoiceSummary($invNo),
            'vendorBank' => $this->pay->GetPaymentVendorBankAccount($invNo)
        ];

        $this->view('payment/PaymentForm', $data);
    }

    public function SubmitPayment()
    {
        header('Content-Type: application/json');

        try {
            $payload = $_POST;
            $payload['createdBy'] = $this->session->get('user_id');

            if (!isset($_FILES['payment_proof'])) {
                ResponseHelper::badRequest('Payment proof is required');
                return;
            }

            $payload['payment_proof'] = $_FILES['payment_proof'];

            $result = $this->pay->SubmitPayment($payload);

            if ($result['success']) {
                ResponseHelper::created($result['message']);
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Throwable $e) {
            ResponseHelper::serverError($e->getMessage());
        }
    }
}
