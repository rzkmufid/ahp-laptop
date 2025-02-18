<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_wishlist'])) {
        $product_id = $_POST['product_id'];
        
        $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $wishlist_item = $stmt->fetch();
        
        if ($wishlist_item) {
            $stmt = $pdo->prepare("UPDATE wishlist SET quantity = quantity + 1 WHERE id = ?");
            $stmt->execute([$wishlist_item['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
        }
        header("Location: wishlist.php");
        exit();
    }
    
    if (isset($_POST['remove_from_wishlist'])) {
        $wishlist_id = $_POST['wishlist_id'];
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = ? AND user_id = ?");
        $stmt->execute([$wishlist_id, $_SESSION['user_id']]);
        header("Location: wishlist.php");
        exit();
    }
    
    if (isset($_POST['update_quantity'])) {
        $wishlist_id = $_POST['wishlist_id'];
        $quantity = max(1, intval($_POST['quantity']));
        $stmt = $pdo->prepare("UPDATE wishlist SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$quantity, $wishlist_id, $_SESSION['user_id']]);
        header("Location: wishlist.php");
        exit();
    }
}

// Get wishlist items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image 
    FROM wishlist c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$wishlist_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($wishlist_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - GAPTECH STORE</title>
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
        .table {
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
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
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Your Wishlist</h1>
    
    <?php if (empty($wishlist_items)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Your wishlist is empty.
        </div>
        <a href="catalog.php" class="btn btn-custom">
            <i class="fas fa-arrow-left"></i> Back to Catalog
        </a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishlist_items as $item): ?>
                        <tr>
                            <td>
                                <a href="detail.php?id=<?php echo urlencode($item['product_id']); ?>" class="text-dark text-decoration-none">
                                    <img src="img/laptops/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <span class="ms-2"><?php echo htmlspecialchars($item['name']); ?></span>
                                </a>
                            </td>
                            <td>Rp<?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td>
                                <form method="POST" class="d-flex align-items-center">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" class="form-control form-control-sm me-2" style="width: 60px;">
                                    <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-custom">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </td>
                            <td>Rp<?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="wishlist_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="remove_from_wishlist" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-dark">
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td colspan="2"><strong>Rp<?php echo number_format($total, 0, ',', '.'); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-4">
            <a href="catalog.php" class="btn btn-outline-custom">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'includes/footer.php'; ?>