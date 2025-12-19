<?php

class OrdersRequestController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    public function index()
    {
        $data = [
            'title' => 'Order Request Management',
            'subtitle' => 'Manage your order request and detail'
        ];
        $this->view('ordersRequest/index', $data);
    }
}
