<?php

class OrdersApprovalController extends Controller
{
    private $ordersApproval;
    private $purchase;
    private $workflow;
    private $status;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->ordersApproval = new OrdersApprovalModel();
        $this->purchase = new PurchaseModel();
        $this->workflow = new ApprovalWorkflowModel();
        $this->status = new StatusCodeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Order Approval Management',
            'subtitle' => 'Manage your order approval and detail'
        ];
        $this->view('ordersApproval/index', $data);
    }

    public function GetApprovalList()
    {
        try {
            $response = $this->ordersApproval->GetApprovalList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function GetApprovalDetail()
    {
        $prNumber = $_GET['prNumber'] ?? null;
        $data = [
            'title' => 'Order Approval Detail',
            'subtitle' => 'Manage yout order approval detail',
            'purchase' => $this->purchase->GetPurchaseRequest($prNumber),
            'purchaseDetail' => $this->purchase->GetPurchaseDetail($prNumber),
            'workflow' => $this->workflow->GetApprovalPurchaseRequest($prNumber),
            'status' => $this->status->GetStatusByModuleCode("APR"),
            'level' => $this->session->get("level")
        ];

        $this->view('ordersApproval/ApprovalDetail', $data);
    }

    public function SubmitApproval()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->ordersApproval->SubmitApprovalWorkflow($payload);
            if ($result['success']) {
                ResponseHelper::created(
                    $result['message']
                );
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            ResponseHelper::serverError('Terjadi kesalahan saat memproses data request' . $e->getMessage());
        }
    }
}
