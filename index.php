<?php
session_start();
require_once 'config/database.php';

// Get featured products
$stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 6");
$featured_products = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid p-0">
    <!-- Hero Section -->
    <div class="bg-dark text-white py-5 mb-5" style="background-image: linear-gradient(to right, rgb(0, 0, 0), rgba(0, 0, 0, 0.53)), url('./img/laptops/fwebp.jpg'); background-size: cover; height: 400px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold">Welcome to GAPTECH STORE</h1>
                    <p class="lead">Find your perfect laptop with our intelligent comparison system</p>
                    <a href="catalog.php" class="btn btn-light btn-lg">Browse Laptops</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Laptops Section -->
    <div class="container mb-5">
        <h2 class="text-center mb-4">Featured Laptops</h2>
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="img/laptops/<?php echo htmlspecialchars($product['image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">Rp<?php echo number_format($product['price'], 2); ?></span>
                                <a href="catalog.php" class="btn btn-outline-dark">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Why Choose Us Section -->
    <div class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Why Choose Us?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up text-dark display-4 mb-3"></i>
                            <h5 class="card-title">Intelligent Comparison</h5>
                            <p class="card-text">Our AHP-based comparison system helps you make the best choice.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-laptop text-dark display-4 mb-3"></i>
                            <h5 class="card-title">Quality Products</h5>
                            <p class="card-text">We offer only the best laptops from trusted brands.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check text-dark display-4 mb-3"></i>
                            <h5 class="card-title">Secure Shopping</h5>
                            <p class="card-text">Shop with confidence with our secure checkout system.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

