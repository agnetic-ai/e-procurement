<?php
class GoodsReceiptsController extends Controller
{

    private $gr;
    private $po;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->gr = new GoodsReciptsModel();
        $this->po = new PurchaseOrdersModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Goods Receipts',
            "subtitle" => "Manage Goods Receipts"
        ];
        $this->view('goodsReceipts/index', $data);
    }

    public function CreateGoodsReceipts()
    {
        $data = [
            'title' => 'Create Goods Receipts',
            'subtitle' => "Manage Goods Receipts",
            'PurchaseOrder' => $this->po->GetPurchaseOrderNumberApproved(),
            'GrNumber' => $this->gr->GenerateGrNumber()
        ];
        $this->view('goodsReceipts/CreateGoodsReceipts', $data);
    }

    public function GetGoodsReciptsList()
    {
        try {
            $response = $this->gr->GetGoodsReciptsList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function GetGoodsReciptsDetail()
    {
        $grNumber = $_GET['grNumber'] ?? null;
        $data = [
            'title' => 'Goods Receipt',
            'subtitle' => 'Manage Goods Receipt',
            'GrHeader' => $this->gr->GetGoodsReciptsByGrNumber($grNumber),
            'GrDetails' => $this->gr->GetGoodsReceiptsDetailByGrNumber($grNumber),
            'AssetUnits' => $this->gr->GetAssetUnitsByPoNumber($grNumber)
        ];
        $this->view('goodsReceipts/GoodsReceiptsDetail', $data);
    }

    public function GetDraftCreateGoodsRecipts()
    {
        $poNumber = $_GET['poNumber'] ?? null;

        try {
            if (!$poNumber) {
                throw new Exception('PO Number tidak boleh kosong');
            }

            $response = [
                'PoHeader' => $this->po->GetPurchaseOrdersByPoNumber($poNumber),
                'HistoryGr' => $this->gr->GetHistoryGoodsReceipts($poNumber),
                'GrDetail' => $this->gr->GetGoodsReceiptsDetailByPoNumber($poNumber),
                'AssetUnits' => $this->gr->GetAssetUnitsByPoNumber($poNumber)
            ];
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            ResponseHelper::badRequest($e->getMessage());
        }
    }


    public function SubmitGoodsReceipts()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->gr->SubmitGoodsReceipt($payload);
            if ($result['success']) {
                ResponseHelper::created(
                    [
                        'RequestNumber' => $result['grNumber']
                    ],
                    $result['message']
                );
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            ResponseHelper::serverError('Terjadi kesalahan saat memproses data request');
        }
    }
}
