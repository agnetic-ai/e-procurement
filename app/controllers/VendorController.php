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
}
