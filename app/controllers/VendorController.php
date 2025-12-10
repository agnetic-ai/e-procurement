<?php
class VendorController extends Controller
{
    private $vendor;
    private $cities;
    private $business;
    public function __construct()
    {
        parent::__construct();
        $this->vendor = new VendorModel();
        $this->cities = new CitiesModel();
        $this->business = new BusinessTypeModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Vendor Management',
        ];

        $this->view('vendor/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Vendor Management',
            'cities' => $this->cities->GetCities(),
            'business' => $this->business->GetBusiness()
        ];

        $this->view('vendor/Create', $data);
    }

    public function GetVendorList()
    {
        header('Content-Type: application/json');

        try {
            $vendors = $this->vendor->GetVendorList();
            ResponseHelper::success($vendors, 'Vendors retrieved successfully');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function SubmitNewVendor()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->vendor->createVendor($payload);
            if ($result['success']) {
                ResponseHelper::created(
                    [
                        'vendorId' => $result['vendorId'],
                        'vendorCode' => $result['vendorCode']
                    ],
                    $result['message']
                );
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            // Log error untuk debugging
            error_log("Error in SubmitNewVendor: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            ResponseHelper::serverError('Terjadi kesalahan saat memproses data vendor');
        }
    }
}
