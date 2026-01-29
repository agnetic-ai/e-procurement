<?php
class InvoiceController extends Controller
{
    private $invoice;
    private $status;
    private $po;
    private $helpers;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->invoice = new InvoiceModel();
        $this->status = new StatusCodeModel();
        $this->po = new PurchaseOrdersModel();
        $this->helpers = new HelpersModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Invoice Management',
            'subtitle' => 'Manage your invoice and detail',
            'invoice_statuses' => $this->status->GetStatusByModuleCode("INV")
        ];
        $this->view('invoice/index', $data);
    }

    public function CreateInvoice()
    {
        $data = [
            'title' => 'Create Invoice Management',
            'subtitle' => 'Manage yout invoice and detail',
            'PoComplete' => $this->po->GetPoReadyForInvoice()
        ];
        $this->view('invoice/CreateInvoice', $data);
    }

    public function GetInvoiceProcurement()
    {
        try {
            $invoices = $this->invoice->GetInvoiceProcurementView();
            ResponseHelper::success($invoices, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function GetDraftCreateInvoice()
    {
        $poNumber = $_GET['poNumber'] ?? null;

        try {
            if (!$poNumber) {
                throw new Exception('PO Number tidak boleh kosong');
            }

            $response = [
                'PoHeader' => $this->po->GetPurchaseOrdersByPoNumber($poNumber),
                'InvoiceItem' => $this->invoice->GetInvoiceItemByPoNumber($poNumber),
                'InvoiceSummary' => $this->invoice->GetInvoiceSummary($poNumber)
            ];
            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            ResponseHelper::badRequest($e->getMessage());
        }
    }

    public function SubmitDraftInvoice()
    {
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $payload["createdBy"] = $this->session->get("user_id");

            $result = $this->invoice->DraftInvoice($payload);

            if ($result['success']) {
                ResponseHelper::created(
                    ['InvoiceNumber' => $result['InvoiceNumber']],
                    $result['message']
                );
            } else {

                $message = $result['message'];

                if (str_contains($message, 'uk_vendor_invoice')) {
                    $message = 'Invoice number already exists for this vendor.';
                }

                ResponseHelper::badRequest($message);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
