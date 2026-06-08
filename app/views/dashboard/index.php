<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production OS - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .sidebar {
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            padding-top: 20px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        .card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }
        .stat-content h3 {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .stat-content .number {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-bars"></i> Menu
            </span>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <span class="badge bg-primary">
                        <i class="fas fa-user-circle"></i> <?php echo escape($user['first_name']); ?>
                    </span>
                </div>
                <a href="<?php echo baseUrl('logout'); ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
                <div class="text-center mb-4 pb-4 border-bottom border-light">
                    <h5 class="mb-2">
                        <i class="fas fa-industry"></i> Production OS
                    </h5>
                    <small>MVP Phase 1</small>
                </div>

                <div class="menu-items">
                    <a href="<?php echo baseUrl('dashboard'); ?>" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="<?php echo baseUrl('products'); ?>" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="#" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-flask"></i> Formulas
                    </a>
                    <a href="#" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-vials"></i> Materials
                    </a>
                    <a href="#" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-warehouse"></i> Inventory
                    </a>
                    <a href="#" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-cube"></i> Manufacturing Orders
                    </a>
                    <a href="#" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-cogs"></i> Production
                    </a>
                    <a href="#" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-check-circle"></i> QC
                    </a>
                    <a href="#" class="menu-item text-white text-decoration-none d-block py-3 px-3 rounded">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </h2>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: var(--primary);">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Products</h3>
                                <p class="number"><?php echo $totalProducts; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: var(--success);">
                                <i class="fas fa-vials"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Materials</h3>
                                <p class="number"><?php echo $totalMaterials; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: var(--warning);">
                                <i class="fas fa-hourglass-start"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Pending MO</h3>
                                <p class="number"><?php echo $pendingMOs; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: var(--danger);">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Low Stock</h3>
                                <p class="number"><?php echo $lowStockItems; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card p-4">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-chart-line"></i> Monthly Production
                            </h5>
                            <canvas id="productionChart"></canvas>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card p-4">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-check-circle"></i> QC Statistics
                            </h5>
                            <div class="text-center">
                                <p class="mb-2">Pass Rate: <strong><?php echo ($qcStats['total_inspections'] > 0 ? round(($qcStats['passed'] / $qcStats['total_inspections']) * 100, 2) : 0); ?>%</strong></p>
                                <small class="text-muted">
                                    Total: <?php echo $qcStats['total_inspections']; ?> |
                                    Passed: <?php echo $qcStats['passed']; ?> |
                                    Failed: <?php echo $qcStats['failed']; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Monthly Production Chart
        const ctx = document.getElementById('productionChart').getContext('2d');
        const chartData = <?php echo $monthlyProduction; ?>;

        const labels = chartData.map(d => d.month);
        const data = chartData.map(d => d.count);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Manufacturing Orders',
                    data: data,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
