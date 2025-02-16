<?php
require_once 'config/database.php';

// Start transaction
$pdo->beginTransaction();

try {
    // 1. Backup existing products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products_backup LIKE products");
    $pdo->exec("INSERT INTO products_backup SELECT * FROM products");
    
    // 2. Create new tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS laptop_specifications (
            id int(11) NOT NULL AUTO_INCREMENT,
            product_id int(11) NOT NULL,
            processor varchar(100) NOT NULL,
            processor_detail varchar(255) NOT NULL,
            ram varchar(50) NOT NULL,
            storage varchar(100) NOT NULL,
            gpu varchar(100) NOT NULL,
            display varchar(255) NOT NULL,
            battery varchar(100) NOT NULL,
            dark KEY (id),
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ahp_scores (
            id int(11) NOT NULL AUTO_INCREMENT,
            product_id int(11) NOT NULL,
            processor_score float NOT NULL,
            ram_score float NOT NULL,
            storage_score float NOT NULL,
            gpu_score float NOT NULL,
            display_score float NOT NULL,
            harga_score float NOT NULL,
            dark KEY (id),
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");

    // 3. Migrate existing data
    // First, get all products
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare insert statements
    $specStmt = $pdo->prepare("
        INSERT INTO laptop_specifications 
        (product_id, processor, processor_detail, ram, storage, gpu, display, battery)
        VALUES 
        (:product_id, :processor, :processor_detail, :ram, :storage, :gpu, :display, :battery)
    ");

    $scoreStmt = $pdo->prepare("
        INSERT INTO ahp_scores 
        (product_id, processor_score, ram_score, storage_score, gpu_score, display_score, harga_score)
        VALUES 
        (:product_id, :processor_score, :ram_score, :storage_score, :gpu_score, :display_score, :harga_score)
    ");

    // Process each product
    foreach ($products as $product) {
        // Insert default specifications (you may want to modify these defaults)
        $specStmt->execute([
            'product_id' => $product['id'],
            'processor' => 'Intel Core i5', // default value
            'processor_detail' => '11th Gen', // default value
            'ram' => '8GB', // default value
            'storage' => '256GB SSD', // default value
            'gpu' => 'Intel Iris Xe', // default value
            'display' => '14" FHD', // default value
            'battery' => '45WHr' // default value
        ]);

        // Migrate existing AHP scores
        $scoreStmt->execute([
            'product_id' => $product['id'],
            'processor_score' => $product['processor_score'] ?? 0,
            'ram_score' => $product['ram_score'] ?? 0,
            'storage_score' => $product['storage_score'] ?? 0,
            'gpu_score' => $product['gpu_score'] ?? 0,
            'display_score' => $product['display_score'] ?? 0,
            'harga_score' => $product['harga_score'] ?? 0
        ]);
    }

    // 4. Remove old score columns from products table
    $pdo->exec("
        ALTER TABLE products 
        DROP COLUMN processor_score,
        DROP COLUMN ram_score,
        DROP COLUMN storage_score,
        DROP COLUMN gpu_score,
        DROP COLUMN display_score,
        DROP COLUMN harga_score
    ");

    // If everything went well, commit the transaction
    $pdo->commit();
    echo "Migration completed successfully!\n";
    echo "Your data has been backed up to 'products_backup' table.\n";
    
} catch (Exception $e) {
    // If anything goes wrong, roll back the transaction
    $pdo->rollBack();
    echo "Migration failed: " . $e->getMessage() . "\n";
    echo "All changes have been rolled back.\n";
}
?>