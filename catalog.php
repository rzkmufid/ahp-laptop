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

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptop Catalog - GAPTECH STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .btn-custom {
            background-color: #333;
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #555;
            color: #fff;
        }
        .btn-outline-custom {
            color: #333;
            border-color: #333;
        }
        .btn-outline-custom:hover {
            background-color: #333;
            color: #fff;
        }
        .table {
            border-color: #ddd;
        }
        .table thead th {
            border-bottom: 2px solid #333;
        }
        .bg-custom-light {
            background-color: #f1f3f5;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .sticky-top {
            position: -webkit-sticky;
            position: sticky;
            top: 20px; /* Adjust as needed */
        }
    </style>
</head>
<body>

<div class="container-fluid bg-custom-light py-5">
    <div class="container">
        <h1 class="text-center mb-5">Laptop Catalog</h1>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm sticky-top">
                    <div class="card-header bg-dark text-white">
                        <h4 class="card-title mb-0">Filters</h4>
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
                            <button type="submit" class="btn btn-custom w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="row">
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>No products found matching your criteria. Please try adjusting your filters.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <img src="img/laptops/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text small"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                        
                                        <table class="table table-sm mt-3">
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
                                        </table>
                                        
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="h5 mb-0 text-dark">Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                              
                                                    <div class="btn-group">
                                                        <form method="POST" action="wishlist.php" class="me-1">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                            <button type="submit" name="add_to_wishlist" class="btn btn-outline-custom btn-sm">
                                                                <i class="fas fa-heart"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="compare.php">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                            <button type="submit" name="add_to_compare" class="btn btn-outline-custom btn-sm">
                                                                <i class="fas fa-exchange-alt"></i> Compare
                                                            </button>
                                                        </form>
                                                    </div>
                                               
                                            </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'includes/footer.php'; ?>