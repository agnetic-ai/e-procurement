<?php
class VendorController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Vendor Management',
            'vendors' => $this->getVendors() // method untuk ambil data
        ];

        $this->view('vendor/index', $data);
    }

    public function VendorDetail()
    {
        $this->view('vendor/VendorDetail');
    }

    public function list()
    {
        // alias untuk index
        $this->index();
    }

    public function add()
    {
        $data = [
            'title' => 'Add New Vendor'
        ];

        $this->view('vendor/add', $data);
    }

    private function getVendors()
    {
        // Contoh data dummy
        return [
            ['id' => 1, 'name' => 'PT Supplier Jaya', 'email' => 'jaya@email.com'],
            ['id' => 2, 'name' => 'CV Mandiri Sejahtera', 'email' => 'mandiri@email.com']
        ];
    }
}
