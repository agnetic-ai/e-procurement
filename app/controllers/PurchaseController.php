<?php
class PurchaseController extends Controller
{
    private $product;
    private $purchase;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->product = new ProductModel();
        $this->purchase = new PurchaseModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Purchase Request',
            'product' => $this->product->GetProductActive()
        ];

        $this->view('purchase/index', $data);
    }

    public function SubmitPurchaseForm()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);

            $payload["requestedBy"] = $this->session->getUserId();
            $result = $this->purchase->SubmitPurchaseRequest($payload);
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
