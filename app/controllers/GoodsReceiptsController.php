<?php
class GoodsReceiptsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    public function index()
    {
        $data = [
            'title' => 'Goods Receipts',
            "subtitle" => "Manage Goods Receipts"
        ];
        $this->view('goodsReceipts/index', $data);
    }
}
