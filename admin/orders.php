<?php
// orders.php
require_once 'config.php';
requireLogin();

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    header("Location: orders.php");
    exit();
}

// Get all orders
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT orders.id, users.username, orders.total_amount, orders.status FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    if (!$order) {
        header("Location: orders.php");
        exit();
    }
} else {
    $orders = $pdo->query("SELECT orders.id, users.username, orders.total_amount, orders.status FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.id DESC")->fetchAll();
}

include 'includes/header.php';

?>


    <div class="container mt-4">
        <h2>Manage Orders</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <td>Rp<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $order['status'] === 'Pending' ? 'warning' : ($order['status'] === 'Processed' ? 'success' : 'danger'); ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-select d-inline w-auto">
                                    <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Processed" <?php if ($order['status'] == 'Processed') echo 'selected'; ?>>Processed</option>
                                    <option value="Rejected" <?php if ($order['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-dark">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No orders found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>

