<?php
class AssetsController extends Controller
{
    private $asset;
    private $emp;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->asset = new AssetsModel();
        $this->emp = new EmployeeModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Assets Management',
            'subtitle' => 'Manage your assets and detail',
            'totalAssets' => $this->asset->GetTotalAssets(),
            'inStock' => $this->asset->GetAssetsInStock(),
            'assigned' => $this->asset->GetAssetsAssigned(),
            'damage' => $this->asset->GetAssetsDamage(),
            'recentAssets' => $this->asset->GetRecentAssets()
        ];
        $this->view('assets/index', $data);
    }

    public function GetListAssetsInstock()
    {
        try {
            $assets = $this->asset->GetListAssetsInStock();
            ResponseHelper::success($assets, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function Assign()
    {
        $assetId = $_GET['assetId'] ?? null;
        $data = [
            'title' => 'Assign Asset',
            'subtitle' => 'Distribute assets to employees',
            'assetsInfo' => $this->asset->GetAssetUnitsDetail($assetId),
            'employee' => $this->emp->GetEmployeeList('EMP_ACTIVE')
        ];
        $this->view('assets/assign', $data);
    }

    public function Stock()
    {
        $data = [
            'title' => 'Asset Stock',
            'subtitle' => 'List of assets available and ready for distribution'

        ];
        $this->view('assets/stock', $data);
    }

    public function SubmitAssignAsset()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $payload["assignedBy"] = $this->session->get("user_id");

            $result = $this->asset->SubmitAssignAsset($payload);

            if ($result['success']) {
                ResponseHelper::created(
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
}
