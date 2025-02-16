<?php
session_start();
require_once 'config/database.php';

// Initialize filter variables
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 100000000;
$brands = isset($_GET['brands']) ? $_GET['brands'] : [];

// Updated SQL query with JOIN
$sql = "SELECT p.*, 
               ls.processor, ls.processor_detail, ls.ram, ls.storage, 
               ls.gpu, ls.display, ls.battery,
               aps.processor_score, aps.ram_score, aps.storage_score,
               aps.gpu_score, aps.display_score, aps.harga_score
        FROM products p
        LEFT JOIN laptop_specifications ls ON p.id = ls.product_id
        LEFT JOIN ahp_scores aps ON p.id = aps.product_id
        WHERE p.price BETWEEN :min_price AND :max_price";
$params = [':min_price' => $min_price, ':max_price' => $max_price];

// Only add name condition if brands (names) are selected
if (!empty($brands)) {
    $placeholders = [];
    foreach ($brands as $key => $brand) {
        $placeholder = ":brand$key";
        $placeholders[] = $placeholder;
        $params[$placeholder] = $brand;
    }
    $sql .= " AND p.name IN (" . implode(',', $placeholders) . ")";
}

$sql .= " ORDER BY p.name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get all unique product names for the filter
$name_stmt = $pdo->query("SELECT DISTINCT name FROM products ORDER BY name");
$all_names = $name_stmt->fetchAll(PDO::FETCH_COLUMN);

// Get the overall min and max prices
$price_stmt = $pdo->query("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products");
$price_range = $price_stmt->fetch();
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid bg-light py-5">
    <div class="container">
        <h1 class="text-center mb-5">Laptop Catalog</h1>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <!-- Filters column remains the same -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title">Price Range</h4>
                    </div>
                    <div class="card-body">
                        <form action="catalog.php" method="get">
                            <div class="mb-3">
                                <label for="min_price" class="form-label">Min Price</label>
                                <input type="number" class="form-control" id="min_price" name="min_price" value="<?php echo $min_price; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="max_price" class="form-label">Max Price</label>
                                <input type="number" class="form-control" id="max_price" name="max_price" value="<?php echo $max_price; ?>">
                            </div>
                            <button type="submit" class="btn btn-dark">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="row">
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                No products found matching your criteria. Please try adjusting your filters.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm">
                                    <img src="img/laptops/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                        
                                        <table class="table table-sm">
                                            <tr>
                                                <td class="fw-bold">Processor:</td>
                                                <td><?php echo htmlspecialchars($product['processor'] . ' ' . $product['processor_detail']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">RAM:</td>
                                                <td><?php echo htmlspecialchars($product['ram']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Storage:</td>
                                                <td><?php echo htmlspecialchars($product['storage']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">GPU:</td>
                                                <td><?php echo htmlspecialchars($product['gpu']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Display:</td>
                                                <td><?php echo htmlspecialchars($product['display']); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Battery:</td>
                                                <td><?php echo htmlspecialchars($product['battery']); ?></td>
                                            </tr>
                                        </table>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="h5 mb-0 text-dark">Rp<?php echo number_format($product['price'], 2); ?></span>
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <div class="btn-group">
                                                    <form method="POST" action="cart.php" class="me-1">
                                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                        <button type="submit" name="add_to_cart" class="btn btn-outline-dark btn-sm">
                                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="compare.php">
                                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                        <button type="submit" name="add_to_compare" class="btn btn-outline-secondary btn-sm">
                                                            <i class="bi bi-list-check"></i> Compare
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <a href="login.php" class="btn btn-dark btn-sm">Login to Purchase</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
