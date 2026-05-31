<?php
class ApprovalPoController extends Controller
{

    private $apo;
    private $po;
    private $workflow;
    private $status;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->apo = new ApprovalPoModel();
        $this->po = new PurchaseOrdersModel();
        $this->workflow = new ApprovalWorkflowModel();
        $this->status = new StatusCodeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Approval Purchase Order',
            "subtitle" => "Manage Purchase Order"
        ];

        $this->view('approvalPo/index', $data);
    }

    public function GetApprovalPurchaseOrder()
    {
        try {
            $response = $this->apo->GetApprovalPurchaseOrderList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function GetApprovalPoDetail()
    {
        $poNumber = $_GET['poNumber'] ?? null;
        $data = [
            'title' => 'Purchase Order Detail',
            'subtitle' => 'Details of Purchase Order #' . $poNumber,
            'PoHeader' => $this->po->GetPurchaseOrdersByPoNumber($poNumber),
            'PoDetails' => $this->po->GetPurchaseOrderDetailsByPoNumber($poNumber),
            'workflow' => $this->workflow->GetApprovalPurchaseOrder($poNumber),
            'status' => $this->status->GetStatusByModuleCode("APR"),
            'level' => $this->session->get("level")
        ];

        $this->view('approvalPo/ApprovalPoDetail', $data);
    }

    public function SubmitApprovalPo()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->apo->SubmitApprovalPurchaseOrder($payload);
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
