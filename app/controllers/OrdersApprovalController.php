<?php

class OrdersApprovalController extends Controller
{
    private $orders_approval;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->orders_approval = new OrdersApprovalModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Order Approval Management',
            'subtitle' => 'Manage your order approval and detail'
        ];
        $this->view('ordersApproval/index', $data);
    }

    public function GetApprovalList()
    {
        try {
            $response = $this->orders_approval->GetApprovalList();
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
