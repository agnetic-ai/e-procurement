<?php
class PurchaseOrdersController extends Controller
{
    private $po;
    private $terms;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->po = new PurchaseOrdersModel();
        $this->terms = new PaymentTermsModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Purchase Orders',
            'subtitle' => 'Manage your purchase orders here',
        ];

        $this->view('purchaseOrder/index', $data);
    }

    public function GetPurchaseOrders()
    {
        try {
            $response = $this->po->GetPurchaseOrdersList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function GetPurchseOrderByPoNumber()
    {
        try {
            $poNumber = $_GET['poNumber'] ?? null;

            if (!$poNumber) {
                throw new Exception('PO Number tidak boleh kosong');
            }

            $response = $this->po->GetPurchaseOrdersByPoNumber($poNumber);
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            ResponseHelper::badRequest($e->getMessage());
        }
    }


    public function GetPurchaseOrderDetail()
    {
        $poNumber = $_GET['poNumber'] ?? null;
        $data = [
            'title' => 'Purchase Order Detail',
            'subtitle' => 'Details of Purchase Order #' . $poNumber,
            'PoHeader' => $this->po->GetPurchaseOrdersByPoNumber($poNumber),
            'PoDetails' => $this->po->GetPurchaseOrderDetailsByPoNumber($poNumber),
            'terms' => $this->terms->GetPaymentTerms()
        ];

        $this->view('purchaseOrder/PurchaseOrderDetail', $data);
    }

    public function SubmitPurchaseOrder()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        try {
            $payload["receiveBy"] = $this->session->get("user_id");
            $response = $this->po->SubmitedPurchaseOrder($payload);
            ResponseHelper::success($response, 'Purchase Order submitted successfully');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
