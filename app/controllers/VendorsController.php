<?php
class VendorsController extends Controller
{
    private $vendor;
    private $cities;
    private $business;
    private $payment;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->vendor = new VendorModel();
        $this->cities = new CitiesModel();
        $this->business = new BusinessTypeModel();
        $this->payment = new PaymentTermsModel();
    }
    public function index()
    {
        $data = [
            'title' => 'Vendor Management',
        ];

        $this->view('vendors/index', $data);
    }

    public function RegisterVendor()
    {
        $data = [
            'title' => 'Vendor Management',
            'cities' => $this->cities->GetCities(),
            'payment' => $this->payment->GetPaymentTerms(),
            'business' => $this->business->GetBusiness()
        ];
        $this->view('vendors/RegisterVendor', $data);
    }

    public function UpdateVendor()
    {
        $vendorCode = $_GET['vendorCode'] ?? null;
        $data = [
            'title' => 'Vendor Management',
            'vendorDetail' => $this->vendor->GetVendorByCode($vendorCode),
            'cities' => $this->cities->GetCities(),
            'payment' => $this->payment->GetPaymentTerms(),
            'business' => $this->business->GetBusiness()
        ];
        $this->view('vendors/UpdateVendor', $data);
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

    public function GetVendorProduct()
    {
        header('Content-Type: application/json');
        $payload = json_decode(file_get_contents('php://input'), true);

        try {
            $vendors = $this->vendor->GetVendorsProduct($payload);

            ResponseHelper::success($vendors, 'Vendors product successfully');
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

            $result = $this->vendor->RegisterVendor($payload);
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
            error_log("Error in SubmitNewVendor: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            ResponseHelper::serverError('Terjadi kesalahan saat memproses data vendor');
        }
    }

    public function SubmitUpdateVendor()
    {
        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->vendor->UpdateVendor($payload);
            if ($result['success']) {
                ResponseHelper::created(
                    [
                        'vendorCode' => $result['vendorCode']
                    ],
                    $result['message']
                );
            } else {
                ResponseHelper::badRequest($result['message']);
            }
        } catch (Exception $e) {
            error_log("Error in SubmitNewVendor: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            ResponseHelper::serverError('Terjadi kesalahan saat memproses data vendor');
        }
    }
}
