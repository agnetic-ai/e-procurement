<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/PurchaseModel.php';

class OrdersRequestController extends Controller
{
    private $purchase;

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->purchase = new PurchaseModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Order Request Management',
            'subtitle' => 'Manage your order request and detail',
            'session' => $this->session
        ];
        $this->view('ordersRequest/index', $data);
    }

    public function GetRequestList()
    {
        try {
            $response = $this->purchase->GetRequestList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function GetRequestDetail()
    {
        $prNumber = $_GET['prNumber'] ?? null;
        if (!$prNumber) {
            $this->redirect('ordersrequest');
            return;
        }

        require_once __DIR__ . '/../models/ApprovalWorkflowModel.php';
        $workflow = new ApprovalWorkflowModel();

        $data = [
            'title' => 'Order Request Detail',
            'subtitle' => 'Detail Purchase Request ' . $prNumber,
            'purchase' => $this->purchase->GetPurchaseRequest($prNumber),
            'purchaseDetail' => $this->purchase->GetPurchaseDetail($prNumber),
            'workflow' => $workflow->GetApprovalPurchaseRequest($prNumber),
            'session' => $this->session
        ];

        $this->view('ordersRequest/RequestDetail', $data);
    }
}
