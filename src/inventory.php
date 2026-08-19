<?php
require_once 'includes/session.php';
requireLogin();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Manual stock adjustment (admin only)
if (isset($_POST['adjust_stock']) && $_SESSION['role'] === 'admin') {
    $product_id = $_POST['product_id'];
    $qty_change = (int)$_POST['quantity_change'];
    $reason = $_POST['reason'];
    
    // Check if product exists
    $check = $pdo->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
    $check->execute([$product_id]);
    $current = $check->fetch();
    
    if (!$current) {
        $error = "Product not found.";
    } elseif ($current['stock_quantity'] + $qty_change < 0) {
        $error = "Insufficient stock! Current: " . $current['stock_quantity'] . ", Requested: " . abs($qty_change);
    } else {
        if (updateStock($product_id, $qty_change, $reason, null, $_SESSION['user_id'])) {
            header('Location: inventory.php?msg=adjusted');
            exit;
        } else {
            $error = "Adjustment failed. Please try again.";
        }
    }
}

$products = $pdo->query("SELECT product_id, product_name, stock_quantity, reorder_level FROM products ORDER BY product_name")->fetchAll();

$logs = $pdo->query("
    SELECT l.*, p.product_name, u.full_name as user
    FROM inventory_logs l
    JOIN products p ON l.product_id = p.product_id
    JOIN users u ON l.created_by = u.user_id
    ORDER BY l.log_date DESC
    LIMIT 50
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory - Enca Trading</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">📊 Inventory Overview</h1>
        <p class="page-subtitle">Track stock levels and adjust inventory</p>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Stock Adjustment (Admin only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="form-container">
                <h3>📦 Manual Stock Adjustment</h3>
                <form method="post">
                    <div class="form-group">
                        <label>Select Product</label>
                        <select name="product_id" required>
                            <option value="">-- Choose --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['product_id'] ?>">
                                    <?= htmlspecialchars($p['product_name']) ?> (Stock: <?= $p['stock_quantity'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity Change (+ add, - subtract)</label>
                        <input type="number" name="quantity_change" placeholder="e.g. 10 or -5" required>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <select name="reason" required>
                            <option value="adjustment">Adjustment</option>
                            <option value="purchase">Purchase (add)</option>
                            <option value="return">Return (add)</option>
                            <option value="sale">Sale (subtract)</option>
                        </select>
                    </div>
                    <button type="submit" name="adjust_stock" class="btn btn-primary">Apply Adjustment</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Current Stock Table -->
        <h3>Current Stock Levels</h3>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>Product</th><th>Stock</th><th>Reorder Level</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['product_name']) ?></td>
                            <td><?= $p['stock_quantity'] ?></td>
                            <td><?= $p['reorder_level'] ?></td>
                            <td>
                                <?php if ($p['stock_quantity'] == 0): ?>
                                    <span class="badge badge-danger">ZERO STOCK</span>
                                <?php elseif ($p['stock_quantity'] <= $p['reorder_level']): ?>
                                    <span class="badge badge-warning">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge badge-success">OK</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Inventory Logs -->
        <h3>Recent Inventory Logs</h3>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>Date</th><th>Product</th><th>Change</th><th>New Qty</th><th>Reason</th><th>By</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i', strtotime($log['log_date'])) ?></td>
                            <td><?= htmlspecialchars($log['product_name']) ?></td>
                            <td><?= $log['change_quantity'] ?></td>
                            <td><?= $log['new_quantity'] ?></td>
                            <td><?= $log['reason'] ?></td>
                            <td><?= htmlspecialchars($log['user']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>