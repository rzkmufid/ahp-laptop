<!-- compare.php -->
<?php
session_start();
require_once 'config/database.php';
require_once 'classes/AHP.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize comparison session
if (!isset($_SESSION['compare_laptops'])) {
    $_SESSION['compare_laptops'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_compare'])) {
        $product_id = $_POST['product_id'];
        if (!in_array($product_id, $_SESSION['compare_laptops'])) {
            $_SESSION['compare_laptops'][] = $product_id;
        }
    }
    
    if (isset($_POST['remove_from_compare'])) {
        $product_id = $_POST['product_id'];
        $key = array_search($product_id, $_SESSION['compare_laptops']);
        if ($key !== false) {
            unset($_SESSION['compare_laptops'][$key]);
            $_SESSION['compare_laptops'] = array_values($_SESSION['compare_laptops']); // Re-index array
        }
    }
    
    if (isset($_POST['compare_now']) && count($_SESSION['compare_laptops']) >= 2) {
        try {
            $stmt = $pdo->prepare("SELECT p.id, p.name, aps.processor_score, aps.ram_score, aps.storage_score, aps.gpu_score, aps.display_score, aps.harga_score
                                   FROM products p
                                   JOIN ahp_scores aps ON p.id = aps.product_id
                                   WHERE p.id IN (" . str_repeat('?,', count($_SESSION['compare_laptops']) - 1) . "?)");
            $stmt->execute($_SESSION['compare_laptops']);
            $laptops = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $ahp = new AHP();
            $results = $ahp->compareLaptops($laptops);
            
            $_SESSION['ahp_results'] = $results;
            $_SESSION['ahp_details'] = $ahp->getCalculationDetails(); // Save detailed calculation steps
            
            header("Location: ahp_result.php");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get selected laptops
$selected_laptops = [];
if (!empty($_SESSION['compare_laptops'])) {
    $stmt = $pdo->prepare("SELECT p.*, ls.processor, ls.processor_detail, ls.ram, ls.storage, ls.gpu, ls.display, ls.battery
                           FROM products p
                           LEFT JOIN laptop_specifications ls ON p.id = ls.product_id
                           WHERE p.id IN (" . str_repeat('?,', count($_SESSION['compare_laptops']) - 1) . "?)");
    $stmt->execute($_SESSION['compare_laptops']);
    $selected_laptops = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Compare Laptops</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($selected_laptops)): ?>
        <div class="alert alert-info">
            Select 2-4 laptops from the catalog to compare.
            <a href="catalog.php" class="btn btn-dark ms-3">Go to Catalog</a>
        </div>
    <?php else: ?>
        <div class="row mb-4">
            <?php foreach ($selected_laptops as $laptop): ?>
                <div class="col-md-3">
                    <div class="card">
                        <img src="img/laptops/<?php echo htmlspecialchars($laptop['image']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($laptop['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($laptop['name']); ?></h5>
                            <p class="card-text">Price: Rp<?php echo number_format($laptop['price'], 2); ?></p>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $laptop['id']; ?>">
                                <button type="submit" name="remove_from_compare" class="btn btn-danger">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($selected_laptops) >= 2): ?>
            <form method="POST" class="text-center">
                <button type="submit" name="compare_now" class="btn btn-dark btn-lg">Compare Selected Laptops</button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">Select at least 2 laptops to compare.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
