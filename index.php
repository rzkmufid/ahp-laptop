<?php
session_start();
require_once 'config/database.php';

// Get featured products
$stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 6");
$featured_products = $stmt->fetchAll();

include 'includes/header.php';
?>


<div class="container-fluid p-0">
    <!-- Hero Section -->
    <div class="hero-section d-flex align-items-center text-white mb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Welcome to GAPTECH STORE</h1>
                    <p class="lead mb-4">Find your perfect laptop with our intelligent comparison system</p>
                    <a href="catalog.php" class="btn btn-custom btn-lg">Browse Laptops</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Laptops Section -->
    <div class="container mb-5">
        <h2 class="text-center mb-5">Featured Laptops</h2>
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="img/laptops/<?php echo htmlspecialchars($product['image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="h5 mb-0">Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                <a href="detail.php?id=<?php echo $product['id']; ?>" class="btn btn-custom">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Why Choose Us Section -->
    <div class="bg-custom-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Us?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line text-custom display-4 mb-3"></i>
                            <h5 class="card-title">Intelligent Comparison</h5>
                            <p class="card-text">Our AHP-based comparison system helps you make the best choice.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-laptop text-custom display-4 mb-3"></i>
                            <h5 class="card-title">Quality Products</h5>
                            <p class="card-text">We offer only the best laptops from trusted brands.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt text-custom display-4 mb-3"></i>
                            <h5 class="card-title">Secure Shopping</h5>
                            <p class="card-text">Shop with confidence with our secure checkout system.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
