<?php
// products.php
require_once 'config.php';
requireLogin();

// Handle product deletion
if (isset($_POST['delete_id'])) {
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Delete from all related tables
        $stmt = $pdo->prepare("DELETE FROM ahp_scores WHERE product_id = ?");
        $stmt->execute([$_POST['delete_id']]);
        
        $stmt = $pdo->prepare("DELETE FROM laptop_specifications WHERE product_id = ?");
        $stmt->execute([$_POST['delete_id']]);
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        
        $pdo->commit();
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

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Products</h2>
            <a href="product-form.php" class="btn btn-dark">Add New Product</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
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
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="../img/laptops/<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     style="width: 50px;">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>Rp<?php echo number_format($product['price'], 2); ?></td>
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
                                CPU: <?php echo $product['processor_score']; ?><br>
                                RAM: <?php echo $product['ram_score']; ?><br>
                                Storage: <?php echo $product['storage_score']; ?><br>
                                GPU: <?php echo $product['gpu_score']; ?><br>
                                Display: <?php echo $product['display_score']; ?><br>
                                Harga: <?php echo $product['harga_score']; ?>
                            </small>
                        </td>
                        <td>
                            <div class="gap">
                                <a href="product-form.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-sm btn-dark">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                    <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Delete
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

<?php include 'includes/footer.php'; ?>