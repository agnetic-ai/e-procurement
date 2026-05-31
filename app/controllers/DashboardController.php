<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/DashboardModel.php';

class DashboardController extends Controller
{
    private $dashboardModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->dashboardModel = new DashboardModel();
    }

    public function index()
    {
        $this->checkLogin();

        $stats = $this->dashboardModel->getStats();
        $recentPOs = $this->dashboardModel->getRecentPOs(5);
        $recentPRs = $this->dashboardModel->getRecentPRs(5);
        $monthlySpending = $this->dashboardModel->getMonthlySpending();
        $topVendors = $this->dashboardModel->getTopVendors(5);
        $poStatusDist = $this->dashboardModel->getPOStatusDistribution();

        $data = [
            'pageTitle' => 'Dashboard',
            'stats' => $stats,
            'recentPOs' => $recentPOs,
            'recentPRs' => $recentPRs,
            'monthlySpending' => $monthlySpending,
            'topVendors' => $topVendors,
            'poStatusDist' => $poStatusDist,
            'session' => $this->session
        ];

        $this->view('dashboard/index', $data);
    }
}
