<?php
// index.php
require_once 'config.php';
requireLogin();

// Get counts for dashboard
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Get low stock products (stock below 100)
$lowStockProducts = $pdo->query("
    SELECT * FROM products 
    WHERE stock < 100 
    ORDER BY stock ASC 
    LIMIT 5
")->fetchAll();

// Get recent products
$recentProducts = $pdo->query("
    SELECT * FROM products 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GAPTECH KOMPUTER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c2c2c;
            --secondary-color: #4a4a4a;
            --accent-color: #666666;
            --background-color: #f5f5f5;
            --card-bg: #ffffff;
            --text-primary: #1a1a1a;
            --text-secondary: #4a4a4a;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
        }

        .dashboard-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            min-height: 140px;
            background: var(--primary-color);
            color: white;
        }

        .stat-icon {
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 4rem;
            opacity: 0.1;
        }

        .welcome-section {
            background: linear-gradient(145deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .table {
            color: var(--text-primary);
        }

        .table th {
            font-weight: 600;
            color: var(--text-secondary);
        }

        .badge {
            padding: 0.5em 0.8em;
        }

        .low-stock-alert {
            border-left: 4px solid var(--accent-color);
            background-color: rgba(0, 0, 0, 0.02);
        }

        .btn-mono {
            background-color: var(--primary-color);
            color: white;
            border: none;
            transition: var(--transition);
        }

        .btn-mono:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .product-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .text-mono {
            color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="container py-4">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="display-5 fw-bold">Welcome back, <?php echo $_SESSION['username']; ?>!</h1>
        <p class="lead">Manage your inventory and users efficiently.</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="dashboard-card stat-card">
                <div class="card-body">
                    <h6 class="card-title">Total Products</h6>
                    <h2 class="display-6 fw-bold mb-0"><?php echo number_format($productCount); ?></h2>
                    <div class="mt-3">
                        <a href="products.php" class="text-white text-decoration-none">Manage Products →</a>
                    </div>
                    <i class="bi bi-box stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card stat-card">
                <div class="card-body">
                    <h6 class="card-title">Registered Users</h6>
                    <h2 class="display-6 fw-bold mb-0"><?php echo number_format($userCount); ?></h2>
                    <div class="mt-3">
                        <a href="users.php" class="text-white text-decoration-none">Manage Users →</a>
                    </div>
                    <i class="bi bi-people stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4 ">
        <!-- Recent Products -->
        <div class="col-md-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Products</h5>
                </div>
                <div class="card-body product-list">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentProducts as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <?php if ($product['stock'] < 100): ?>
                                            <span class="badge bg-secondary">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-dark">In Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="product-form.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-mono">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="products.php" class="btn btn-mono">View All Products</a>
                </div>
            </div>
        </div>

        <!-- Side Widget -->
        <div class="col-md-4">
            <!-- Low Stock Alert -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Low Stock Alert</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if (count($lowStockProducts) > 0): ?>
                            <?php foreach ($lowStockProducts as $product): ?>
                            <div class="list-group-item low-stock-alert">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <small class="text-muted">Only <?php echo $product['stock']; ?> units left</small>
                                    </div>
                                    <a href="product-form.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-mono">
                                        Update Stock
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-center align-items-center">
                                    <p class="mb-0">Tidak ada produk dengan stok rendah.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'includes/footer.php'; ?>
