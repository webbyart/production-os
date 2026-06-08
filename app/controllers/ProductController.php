<?php
/**
 * ProductController
 * Handle product CRUD
 */

class ProductController extends BaseController
{
    private $productModel;
    private $categoryModel;
    private $unitModel;
    private $qrHelper;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::hasPermission('view_products');
        
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->unitModel = new Unit();
        $this->qrHelper = new QRCodeHelper();
    }

    /**
     * List all products
     */
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getAllWithDetails($limit, $offset);
        $totalProducts = $this->productModel->count();
        $totalPages = ceil($totalProducts / $limit);

        $this->render('products.index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ]);
    }

    /**
     * Get products (AJAX)
     */
    public function getProducts()
    {
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        if ($search) {
            $products = $this->productModel->searchProducts($search);
        } else {
            $products = $this->productModel->getAllWithDetails($limit, $offset);
        }

        $this->json(['success' => true, 'data' => $products]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        AuthMiddleware::hasPermission('create_products');

        $categories = $this->categoryModel->getAll();
        $units = $this->unitModel->getAll();

        $this->render('products.create', [
            'categories' => $categories,
            'units' => $units
        ]);
    }

    /**
     * Store product (AJAX)
     */
    public function store()
    {
        AuthMiddleware::hasPermission('create_products');
        CSRFMiddleware::validate();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $data = [
            'product_code' => $_POST['product_code'] ?? '',
            'product_name' => $_POST['product_name'] ?? '',
            'category_id' => $_POST['category_id'] ?? null,
            'unit_id' => $_POST['unit_id'] ?? null,
            'description' => $_POST['description'] ?? '',
            'specification' => $_POST['specification'] ?? '',
            'status' => $_POST['status'] ?? 'active',
            'created_by' => $this->auth->getUserId()
        ];

        // Validate required fields
        if (empty($data['product_code']) || empty($data['product_name'])) {
            $this->json(['success' => false, 'message' => 'Please fill all required fields'], 400);
        }

        // Check if code already exists
        $existing = $this->productModel->getByCode($data['product_code']);
        if ($existing) {
            $this->json(['success' => false, 'message' => 'Product code already exists'], 400);
        }

        $productId = $this->productModel->insert($data);
        if ($productId) {
            logActivity('create', 'Created product: ' . $data['product_name'], 'products', $productId);
            $this->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product_id' => $productId
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to create product'], 500);
        }
    }

    /**
     * Show edit form
     */
    public function edit($productId)
    {
        AuthMiddleware::hasPermission('edit_products');

        $product = $this->productModel->getWithDetails($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $categories = $this->categoryModel->getAll();
        $units = $this->unitModel->getAll();

        $this->render('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'units' => $units
        ]);
    }

    /**
     * Update product (AJAX)
     */
    public function update($productId)
    {
        AuthMiddleware::hasPermission('edit_products');
        CSRFMiddleware::validate();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $product = $this->productModel->getById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $data = [
            'product_name' => $_POST['product_name'] ?? '',
            'category_id' => $_POST['category_id'] ?? null,
            'unit_id' => $_POST['unit_id'] ?? null,
            'description' => $_POST['description'] ?? '',
            'specification' => $_POST['specification'] ?? '',
            'status' => $_POST['status'] ?? 'active'
        ];

        $result = $this->productModel->update($productId, $data);
        if ($result) {
            logActivity('update', 'Updated product: ' . $data['product_name'], 'products', $productId);
            $this->json(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update product'], 500);
        }
    }

    /**
     * Delete product (AJAX)
     */
    public function delete($productId)
    {
        AuthMiddleware::hasPermission('delete_products');
        CSRFMiddleware::validate();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $product = $this->productModel->getById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $result = $this->productModel->delete($productId);
        if ($result) {
            logActivity('delete', 'Deleted product: ' . $product['product_name'], 'products', $productId);
            $this->json(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete product'], 500);
        }
    }

    /**
     * Export products to Excel
     */
    public function exportExcel()
    {
        AuthMiddleware::hasPermission('export_products');

        $products = $this->productModel->getAllWithDetails();

        $headers = ['Code', 'Name', 'Category', 'Unit', 'Status', 'Created Date'];
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                $product['product_code'],
                $product['product_name'],
                $product['category_name'],
                $product['unit_symbol'],
                $product['status'],
                formatDate($product['created_at'])
            ];
        }

        ExportHelper::exportExcel('products_' . date('Ymd_His'), $headers, $data);
    }

    /**
     * Get product QR code
     */
    public function getQRCode($productId)
    {
        $product = $this->productModel->getById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $qrUrl = $this->qrHelper->generateProductQR($product['product_code'], $product['product_name']);
        $this->json(['success' => true, 'qr_url' => $qrUrl]);
    }
}
