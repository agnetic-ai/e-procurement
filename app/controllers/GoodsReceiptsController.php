<?php
class GoodsReceiptsController extends Controller
{

    private $goods;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->goods = new GoodsReciptsModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Goods Receipts',
            "subtitle" => "Manage Goods Receipts"
        ];
        $this->view('goodsReceipts/index', $data);
    }

    public function GetGoodsReciptsList()
    {
        try {
            $response = $this->goods->GetGoodsReciptsList();
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
            'GrHeader' => $this->goods->GetGoodsReciptsByGrNumber($grNumber),
            'GrDetails' => $this->goods->GetGoodsReceiptsDetailByGrNumber($grNumber),
            'AssetUnits' => $this->goods->GetAssetUnitsByGrNumber($grNumber)
        ];
        $this->view('goodsReceipts/GoodsReceiptsDetail', $data);
    }

    public function SubmitGoodsReceipt()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->goods->SubmitGoodsReceipt($payload);
            if ($result['success']) {
                ResponseHelper::created(
                    [
                        'RequestNumber' => $result['RequestNumber']
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
