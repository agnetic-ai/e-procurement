<?php
// app/controllers/DashboardController.php

class DashboardController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    public function index()
    {
        $this->checkLogin(); // Optional, bisa juga di constructor

        $data = [
            'pageTitle' => 'Dashboard'
        ];

        $this->view('dashboard/index', $data);
    }
}
