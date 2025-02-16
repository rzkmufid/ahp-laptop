<?php
// index.php
require_once 'config.php';
requireLogin();

// Get counts for dashboard
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

include 'includes/header.php';
?>



    <div class="container mt-4">
        <h2>Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-3 mb-4">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text display-6"><?php echo $productCount; ?></p>
                        <a href="products.php" class="text-white">Manage Products →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Orders</h5>
                        <p class="card-text display-6"><?php echo $orderCount; ?></p>
                        <a href="orders.php" class="text-white">View Orders →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text display-6"><?php echo $userCount; ?></p>
                        <a href="users.php" class="text-white">Manage Users →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pending Orders</h5>
                        <p class="card-text display-6"><?php echo $pendingOrders; ?></p>
                        <a href="orders.php?status=pending" class="text-white">View Pending →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include 'includes/footer.php';
?>