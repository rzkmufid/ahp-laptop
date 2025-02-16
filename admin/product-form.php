<?php
require_once 'config.php';
requireLogin();

$product = [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'stock' => '',
    'image' => ''
];

$specs = [
    'processor' => '',
    'processor_detail' => '',
    'ram' => '',
    'storage' => '',
    'gpu' => '',
    'display' => '',
    'battery' => ''
];

$scores = [
    'processor_score' => '',
    'ram_score' => '',
    'storage_score' => '',
    'gpu_score' => '',
    'display_score' => '',
    'harga_score' => ''  // Added missing harga_score
];

if (isset($_GET['id'])) {
    // Fetch product with specifications and scores
    $sql = "SELECT p.*, 
                   ls.processor, ls.processor_detail, ls.ram, ls.storage, 
                   ls.gpu, ls.display, ls.battery,
                   aps.processor_score, aps.ram_score, aps.storage_score,
                   aps.gpu_score, aps.display_score, aps.harga_score
            FROM products p
            LEFT JOIN laptop_specifications ls ON p.id = ls.product_id
            LEFT JOIN ahp_scores aps ON p.id = aps.product_id
            WHERE p.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        $product = array_intersect_key($data, array_flip(['id', 'name', 'description', 'price', 'stock', 'image']));
        $specs = array_intersect_key($data, array_flip(['processor', 'processor_detail', 'ram', 'storage', 'gpu', 'display', 'battery']));
        $scores = array_intersect_key($data, array_flip(['processor_score', 'ram_score', 'storage_score', 'gpu_score', 'display_score', 'harga_score']));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    
    try {
        $isEdit = isset($_POST['id']) && !empty($_POST['id']);
        $image = $product['image'] ?? null;
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../img/laptops/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                // Delete old image if it exists
                if ($isEdit && !empty($product['image'])) {
                    $oldImagePath = $uploadDir . $product['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $image = $newFileName;
            }
        }
        
        // Insert or update product
        if ($isEdit) {
            $sql = "UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $image, $_POST['id']]);
            $productId = $_POST['id'];
        } else {
            $sql = "INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_POST['name'], $_POST['description'], $_POST['price'], $_POST['stock'], $image]);
            $productId = $pdo->lastInsertId();
        }
        
        // Handle specifications
        if ($isEdit) {
            $sql = "UPDATE laptop_specifications SET 
                    processor=?, processor_detail=?, ram=?, storage=?, gpu=?, display=?, battery=? 
                    WHERE product_id=?";
        } else {
            $sql = "INSERT INTO laptop_specifications 
                    (product_id, processor, processor_detail, ram, storage, gpu, display, battery) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        }
        $stmt = $pdo->prepare($sql);
        $params = [
            $_POST['processor'], $_POST['processor_detail'], $_POST['ram'], 
            $_POST['storage'], $_POST['gpu'], $_POST['display'], 
            $_POST['battery']
        ];
        if ($isEdit) {
            $params[] = $productId;
        } else {
            array_unshift($params, $productId);
        }
        $stmt->execute($params);
        
        // Handle AHP scores
        if ($isEdit) {
            $sql = "UPDATE ahp_scores SET 
                    processor_score=?, ram_score=?, storage_score=?, 
                    gpu_score=?, display_score=?, harga_score=?
                    WHERE product_id=?";
        } else {
            $sql = "INSERT INTO ahp_scores 
                    (product_id, processor_score, ram_score, storage_score, 
                     gpu_score, display_score, harga_score) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        }
        $stmt = $pdo->prepare($sql);
        $params = [
            $_POST['processor_score'], $_POST['ram_score'], 
            $_POST['storage_score'], $_POST['gpu_score'], 
            $_POST['display_score'], $_POST['harga_score']
        ];
        if ($isEdit) {
            $params[] = $productId;
        } else {
            array_unshift($params, $productId);
        }
        $stmt->execute($params);
        
        $pdo->commit();
        header("Location: products.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}

include 'includes/header.php';

?>

    <div class="container mt-4">
        <h2><?= isset($_GET['id']) ? 'Edit' : 'Add' ?> Product</h2>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="mb-5">
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">

            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name"
                            value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"
                            required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (Rp)</label>
                            <input type="number" class="form-control" name="price" value="<?= $product['price'] ?>"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" value="<?= $product['stock'] ?>"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <?php if ($product['image']): ?>
                        <div class="mb-2">
                            <img src="../img/laptops/<?= htmlspecialchars($product['image']) ?>"
                                style="max-width: 200px;">
                        </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="image"
                            <?= !isset($_GET['id']) ? 'required' : '' ?>>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Specifications</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Processor</label>
                            <input type="text" class="form-control" name="processor"
                                value="<?= htmlspecialchars($specs['processor']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Processor Detail</label>
                            <input type="text" class="form-control" name="processor_detail"
                                value="<?= htmlspecialchars($specs['processor_detail']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RAM</label>
                            <input type="text" class="form-control" name="ram"
                                value="<?= htmlspecialchars($specs['ram']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Storage</label>
                            <input type="text" class="form-control" name="storage"
                                value="<?= htmlspecialchars($specs['storage']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GPU</label>
                            <input type="text" class="form-control" name="gpu"
                                value="<?= htmlspecialchars($specs['gpu']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display</label>
                            <input type="text" class="form-control" name="display"
                                value="<?= htmlspecialchars($specs['display']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Battery</label>
                            <input type="text" class="form-control" name="battery"
                                value="<?= htmlspecialchars($specs['battery']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AHP Scores -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">AHP Scores</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price Score</label>
                            <select class="form-select" name="harga_score" required>
                                <option value="1" <?= $scores['harga_score'] == 1 ? 'selected' : '' ?>>>15 juta</option>
                                <option value="0.75" <?= $scores['harga_score'] == 0.75 ? 'selected' : '' ?>>13 juta -
                                    15 juta</option>
                                <option value="0.5" <?= $scores['harga_score'] == 0.5 ? 'selected' : '' ?>>11 juta - 13
                                    juta</option>
                                <option value="0.25" <?= $scores['harga_score'] == 0.25 ? 'selected' : '' ?>>9 juta - 11
                                    juta</option>
                                <option value="0.1" <?= $scores['harga_score'] == 0.1 ? 'selected' : '' ?>>
                                    <9 juta</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Processor Score</label>
                            <select class="form-select" name="processor_score" required>
                                <option value="1" <?= $scores['processor_score'] == 1 ? 'selected' : '' ?>>Intel Core
                                    i9/AMD Ryzen 9</option>
                                <option value="0.75" <?= $scores['processor_score'] == 0.75 ? 'selected' : '' ?>>Intel
                                    Core i7/AMD Ryzen 7</option>
                                <option value="0.5" <?= $scores['processor_score'] == 0.5 ? 'selected' : '' ?>>Intel
                                    Core i5/AMD Ryzen 5</option>
                                <option value="0.25" <?= $scores['processor_score'] == 0.25 ? 'selected' : '' ?>>Intel
                                    Core i3/AMD Ryzen 3</option>
                                <option value="0.1" <?= $scores['processor_score'] == 0.1 ? 'selected' : '' ?>>Intel
                                    Celeron/AMD Athlon</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RAM Score</label>
                            <select class="form-select" name="ram_score" required>
                                <option value="1" <?= $scores['ram_score'] == 1 ? 'selected' : '' ?>>64 GB</option>
                                <option value="0.75" <?= $scores['ram_score'] == 0.75 ? 'selected' : '' ?>>32 GB
                                </option>
                                <option value="0.5" <?= $scores['ram_score'] == 0.5 ? 'selected' : '' ?>>16 GB</option>
                                <option value="0.25" <?= $scores['ram_score'] == 0.25 ? 'selected' : '' ?>>12 GB
                                </option>
                                <option value="0.1" <?= $scores['ram_score'] == 0.1 ? 'selected' : '' ?>>8 GB</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Storage Score</label>
                            <select class="form-select" name="storage_score" required>
                                <option value="1" <?= $scores['storage_score'] == 1 ? 'selected' : '' ?>>>1 TB</option>
                                <option value="0.75" <?= $scores['storage_score'] == 0.75 ? 'selected' : '' ?>>1 TB
                                </option>
                                <option value="0.5" <?= $scores['storage_score'] == 0.5 ? 'selected' : '' ?>>512 GB
                                </option>
                                <option value="0.25" <?= $scores['storage_score'] == 0.25 ? 'selected' : '' ?>>256 GB
                                </option>
                                <option value="0.1" <?= $scores['storage_score'] == 0.1 ? 'selected' : '' ?>>128 GB
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GPU Score</label>
                            <select class="form-select" name="gpu_score" required>
                                <option value="1" <?= $scores['gpu_score'] == 1 ? 'selected' : '' ?>>NVIDIA RTX 4000
                                    Series</option>
                                <option value="0.75" <?= $scores['gpu_score'] == 0.75 ? 'selected' : '' ?>>NVIDIA RTX
                                    3000 Series</option>
                                <option value="0.5" <?= $scores['gpu_score'] == 0.5 ? 'selected' : '' ?>>NVIDIA RTX 2000
                                    Series</option>
                                <option value="0.25" <?= $scores['gpu_score'] == 0.25 ? 'selected' : '' ?>>Intel Iris
                                    Xe/AMD Radeon</option>
                                <option value="0.1" <?= $scores['gpu_score'] == 0.1 ? 'selected' : '' ?>>Intel UHD
                                    Graphics</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Score</label>
                            <select class="form-select" name="display_score" required>
                                <option value="1" <?= $scores['display_score'] == 1 ? 'selected' : '' ?>>OLED
                                </option>
                                <option value="0.75" <?= $scores['display_score'] == 0.75 ? 'selected' : '' ?>>FHD IPS Touchscreen
                                </option>
                                <option value="0.5" <?= $scores['display_score'] == 0.5 ? 'selected' : '' ?>>FHD IPS
                                </option>
                                <option value="0.25" <?= $scores['display_score'] == 0.25 ? 'selected' : '' ?>>FHD TN
                                </option>
                                <option value="0.1" <?= $scores['display_score'] == 0.1 ? 'selected' : '' ?>>HD</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-dark">Save Product</button>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

<?php include 'includes/footer.php'; ?>