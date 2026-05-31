<?php
class ProductController extends Controller
{
    private $product;
    private $categories;
    private $vendors;
    private $status;
    public function __construct()
    {
        parent::__construct();
        $this->product = new ProductModel();
        $this->categories = new CategoriesModel();
        $this->vendors = new VendorModel();
        $this->status = new StatusCodeModel();
        $this->checkLogin();
    }

    public function index()
    {
        $data = [
            'title' => 'Product Management',
        ];
        $this->view('product/index', $data);
    }

    public function GetProductList()
    {
        try {
            $vendors = $this->product->GetProductList();
            ResponseHelper::success($vendors, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function NewProduct()
    {
        $data = [
            'title' => 'Product Management',
            'categories' => $this->categories->GetCategories(),
            'vendor' => $this->vendors->GetVendorActive(),
            'status' => $this->status->GetStatusByModuleCode("PRODUCT")
        ];

        $this->view('product/NewProduct', $data);
    }

    public function  SubmitNewProduct()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->product->AddNewProduct($payload);
            if ($result['success']) {
                ResponseHelper::created(
                    [
                        'productName' => $result['productName']
                    ],
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

    public function UpdateProduct()
    {
        $productId = (int)$_GET['productId'] ?? 0;
        $vendorId = (int)$_GET['vendorId'] ?? 0;
        $data = [
            'title' => 'Product Management',
            'product' => $this->product->GetProductDetail($productId, $vendorId),
            'categories' => $this->categories->GetCategories(),
            'vendor' => $this->vendors->GetVendorActive(),
            'status' => $this->status->GetStatusByModuleCode("PRODUCT")
        ];
        $this->view('product/UpdateProduct', $data);
    }

    public function SubmitUpdateProduct()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->product->UpdateProduct($payload);
            if ($result['success']) {
                ResponseHelper::success(
                    [
                        'productName' => $result['productName']
                    ],
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

    public function RemoveProduct()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $result = $this->product->DeleteProduct($payload);
            if ($result['success']) {
                ResponseHelper::success(
                    null,
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
