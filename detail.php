<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: catalog.php");
    exit();
}

$laptop_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT p.*, ls.processor, ls.processor_detail, ls.ram, ls.storage, ls.gpu, ls.display, ls.battery, 
                              aps.processor_score, aps.ram_score, aps.storage_score, aps.gpu_score, aps.display_score, aps.harga_score
                       FROM products p
                       LEFT JOIN laptop_specifications ls ON p.id = ls.product_id
                       LEFT JOIN ahp_scores aps ON p.id = aps.product_id
                       WHERE p.id = ?");
$stmt->execute([$laptop_id]);
$laptop = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$laptop) {
    header("Location: catalog.php");
    exit();
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($laptop['name']); ?> - Product Detail</title>
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
        .table {
            border-color: #ddd;
        }
        .table thead th {
            border-bottom: 2px solid #333;
        }
        .bg-custom {
            background-color: #333;
            color: #fff;
        }
        .text-custom {
            color: #333;
        }
        .spec-list {
            list-style-type: none;
            padding-left: 0;
        }
        .spec-list li {
            margin-bottom: 10px;
            padding-left: 20px;
            position: relative;
        }
        .spec-list li:before {
            content: '•';
            position: absolute;
            left: 0;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card p-4">
        <div class="row">
            <div class="col-md-5">
                <img src="img/laptops/<?php echo htmlspecialchars($laptop['image']); ?>" class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($laptop['name']); ?>">
            </div>
            <div class="col-md-7">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($laptop['name']); ?></h1>
                    <a href="catalog.php" class="btn btn-custom mt-4 btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Catalog
                    </a>
                </div>
                <h3 class="text-custom mb-3">Rp<?php echo number_format($laptop['price'], 0, ',', '.'); ?></h3>
                <p class="lead"><strong>Stock:</strong> <?php echo $laptop['stock']; ?> available</p>
                <p class="mb-4"><?php echo nl2br(htmlspecialchars($laptop['description'])); ?></p>

                <h4 class="mt-4 mb-3"><i class="fas fa-laptop"></i> Specifications</h4>
                <ul class="spec-list">
                    <li><strong>Processor:</strong> <?php echo htmlspecialchars($laptop['processor_detail']); ?></li>
                    <li><strong>RAM:</strong> <?php echo htmlspecialchars($laptop['ram']); ?></li>
                    <li><strong>Storage:</strong> <?php echo htmlspecialchars($laptop['storage']); ?></li>
                    <li><strong>GPU:</strong> <?php echo htmlspecialchars($laptop['gpu']); ?></li>
                    <li><strong>Display:</strong> <?php echo htmlspecialchars($laptop['display']); ?></li>
                    <li><strong>Battery:</strong> <?php echo htmlspecialchars($laptop['battery']); ?></li>
                </ul>

                <h4 class="mt-4 mb-3"><i class="fas fa-chart-bar"></i> AHP Scores</h4>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-custom">
                            <tr>
                                <th>Price (C1)</th>
                                <th>Display (C2)</th>
                                <th>CPU (C3)</th>
                                <th>GPU (C4)</th>
                                <th>Storage (C5)</th>
                                <th>RAM (C6)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo round($laptop['harga_score'], 3); ?></td>
                                <td><?php echo round($laptop['display_score'], 3); ?></td>
                                <td><?php echo round($laptop['processor_score'], 3); ?></td>
                                <td><?php echo round($laptop['gpu_score'], 3); ?></td>
                                <td><?php echo round($laptop['storage_score'], 3); ?></td>
                                <td><?php echo round($laptop['ram_score'], 3); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <form method="POST" action="wishlist.php" class="mt-4">
                    <input type="hidden" name="product_id" value="<?php echo $laptop['id']; ?>">
                    <button type="submit" name="add_to_wishlist" class="btn btn-custom btn-sm">
                                        <i class="fas fa-heart"></i> Wishlist
                                    </button>
                    <a href="compare.php?add=<?php echo $laptop['id']; ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-exchange-alt"></i> Compare
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'includes/footer.php'; ?>