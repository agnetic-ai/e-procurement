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
            'InvHeader' => $this->verify->GetInvoiceVerifyHeader($invNumber),
            'InvItem' => $this->verify->GetInvoiceVerifyItem($invNumber)
        ];

        $this->view('invoiceVerification/invoiceVerify', $data);
    }

    public function VerifyInvoice()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $payload["verifiedBy"] = $this->session->get("user_id");

            $result = $this->verify->SubmitVerifyInvoice($payload);

            if ($result['success']) {
                ResponseHelper::created(
                    ['InvoiceNumber' => $result['invoiceNumber']],
                    $result['message']
                );
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function RejectInvoice()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $payload["userId"] = $this->session->get("user_id");

            $result = $this->verify->SubmitRejectInvoice($payload);

            if ($result['success']) {
                ResponseHelper::created(
                    ['InvoiceNumber' => $result['invoiceNumber']],
                    $result['message']
                );
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function verify()
    {
        $items = [
            [
                'product_name'    => 'Laptop Dell XPS 13',
                'vendor_name'     => 'PT Supplier Jaya Abadi',
                'quantity'        => 2,
                'unit'            => 'Unit',
                'estimated_price' => 18500000,
                'subtotal'        => 37000000
            ],
            [
                'product_name'    => 'Printer HP LaserJet',
                'vendor_name'     => 'CV Mandiri Sejahtera',
                'quantity'        => 1,
                'unit'            => 'Unit',
                'estimated_price' => 3850000,
                'subtotal'        => 3850000
            ]
        ];

        $body = $this->renderView('email/pr_submitted', [
            'approverName'   => "ESTRI WULANDARI",
            'prNumber'       => "pr->pr_number",
            'prTitle'        => "title",
            'department'     => "department",
            'requesterName'  => "requesterName",
            'items' => $items,
            'totalEstimated' => 10000000,
            'approvalUrl'    => BASE_URL . 'ordersapproval/detail?pr=' . "pr->pr_number"
        ]);

        $sent = EmailHelper::send(
            "lsqbangkit@gmail.com",
            'Approval Required - PR ' . "pr->pr_number",
            $body,
            [
                'cc' => ['estriwulandari13@gmail.com']
            ]
        );

        // $filePath = ROOT_PATH . '/uploads/payments/2026/01/pay_69724200c7cbd5.29117769.pdf';

        // $sent = EmailHelper::send(
        //     "lsqbangkit@gmail.com", //to
        //     'Invoice Verified - NUMBRE_INVOICE', //sub
        //     $body, // content
        //     [
        //         'cc' => ['oraruhya@company.com'], //cc 
        //         'attachments' => [ //attach
        //             [
        //                 'path' => $filePath,
        //                 'name' => "invoiceNumber" . '.pdf'
        //             ]
        //         ]
        //     ]
        // );

        echo json_encode([
            'status'  => $sent ? 200 : 500,
            'message' => $sent ? 'Email berhasil dikirim' : 'Email gagal dikirim'
        ]);
    }
}
