<?php
/**
 * DashboardController
 * Handle dashboard display
 */

class DashboardController extends BaseController
{
    private $productModel;
    private $materialModel;
    private $moModel;
    private $qcModel;
    private $activityModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->materialModel = new RawMaterial();
        $this->moModel = new ManufacturingOrder();
        $this->qcModel = new QualityControl();
        $this->activityModel = new ActivityLog();
    }

    /**
     * Show dashboard
     */
    public function index()
    {
        // Get dashboard statistics
        $totalProducts = $this->productModel->count();
        $totalMaterials = $this->materialModel->count();
        $pendingMOs = count($this->moModel->getPending());
        $qcPending = count($this->qcModel->getPending());
        $lowStockItems = count($this->materialModel->getLowStockItems());

        // Get monthly production data
        $monthlyProduction = $this->moModel->getMonthlyProduction();

        // Get QC statistics
        $qcStats = $this->qcModel->getDefectRate();

        // Get recent activities
        $recentActivities = $this->activityModel->getRecent(10);

        $user = $this->getUser();

        $this->render('dashboard.index', [
            'user' => $user,
            'totalProducts' => $totalProducts,
            'totalMaterials' => $totalMaterials,
            'pendingMOs' => $pendingMOs,
            'qcPending' => $qcPending,
            'lowStockItems' => $lowStockItems,
            'monthlyProduction' => json_encode($monthlyProduction),
            'qcStats' => $qcStats,
            'recentActivities' => $recentActivities
        ]);
    }
}
