<?php
class PurchaseController extends Controller
{
    private $product;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->product = new ProductModel();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Purchase Request',
            'product' => $this->product->GetProductActive()
        ];

        $this->view('purchase/index', $data);
    }
}
