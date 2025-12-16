<?php
class ApprovalWorkflowController extends Controller
{
    private $approval;
    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->approval = new ApprovalWorkflowModel();
    }

    public function GetApprovalWorkflow()
    {
        header('Content-Type: application/json');
        $payload = json_decode(file_get_contents('php://input'), true);

        try {
            $response = $this->approval->GetApprovalWorkflows($payload);

            ResponseHelper::success($response, 'Success');
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
