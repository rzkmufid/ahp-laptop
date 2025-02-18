<?php
require_once 'config.php';
requireLogin();

// Handle product deletion
if (isset($_POST['delete_id'])) {
    $pdo->beginTransaction();
    
    try {
        $stmt = $pdo->prepare("DELETE FROM ahp_scores WHERE product_id = ?");
        $stmt->execute([$_POST['delete_id']]);
        
        $stmt = $pdo->prepare("DELETE FROM laptop_specifications WHERE product_id = ?");
        $stmt->execute([$_POST['delete_id']]);
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        
        $pdo->commit();
        $_SESSION['success'] = "Product successfully deleted.";
        header("Location: products.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
    }
}

// Get all products with specifications and scores
$sql = "SELECT p.*, 
               ls.processor, ls.processor_detail, ls.ram, ls.storage, 
               ls.gpu, ls.display, ls.battery,
               aps.processor_score, aps.ram_score, aps.storage_score,
               aps.gpu_score, aps.display_score, aps.harga_score
        FROM products p
        LEFT JOIN laptop_specifications ls ON p.id = ls.product_id
        LEFT JOIN ahp_scores aps ON p.id = aps.product_id
        ORDER BY p.id DESC";
$products = $pdo->query($sql)->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Products</h1>
        <a href="product-form.php" class="btn btn-custom">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <div style="height: 500px; overflow-y: scroll;">
            <table class="table table-hover">
                <thead class="table-dark position-sticky top-0" style="z-index: 1;">
                    <tr >
                        <th>No</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Specifications</th>
                        <th>AHP Scores</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $index => $product): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="../img/laptops/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-image text-muted"></i>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <small>
                                <strong>CPU:</strong> <?php echo htmlspecialchars($product['processor'] . ' ' . $product['processor_detail']); ?><br>
                                <strong>RAM:</strong> <?php echo htmlspecialchars($product['ram']); ?><br>
                                <strong>Storage:</strong> <?php echo htmlspecialchars($product['storage']); ?><br>
                                <strong>GPU:</strong> <?php echo htmlspecialchars($product['gpu']); ?><br>
                                <strong>Display:</strong> <?php echo htmlspecialchars($product['display']); ?><br>
                                <strong>Battery:</strong> <?php echo htmlspecialchars($product['battery']); ?>
                            </small>
                        </td>
                        <td>
                            <small>
                                CPU: <?php echo number_format($product['processor_score'], 2); ?><br>
                                RAM: <?php echo number_format($product['ram_score'], 2); ?><br>
                                Storage: <?php echo number_format($product['storage_score'], 2); ?><br>
                                GPU: <?php echo number_format($product['gpu_score'], 2); ?><br>
                                Display: <?php echo number_format($product['display_score'], 2); ?><br>
                                Price: <?php echo number_format($product['harga_score'], 2); ?>
                            </small>
                        </td>
                        <td style="z-index: -1;">
                            <div class="btn-group" role="group">
                                <a href="product-form.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-sm btn-outline-custom">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                    <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
