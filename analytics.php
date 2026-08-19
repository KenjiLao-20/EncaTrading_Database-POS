<?php
require_once 'includes/session.php';
requireLogin();
require_once 'config/database.php';
require_once 'includes/functions.php';
$top = getTopSellingProducts(10);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Analytics - Enca Trading</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">📈 Top Selling Products</h1>
        <p class="page-subtitle">Identify your best-performing products</p>

        <?php if ($top): ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr><th>Product</th><th>Total Sold</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['product_name']) ?></td>
                                <td><?= $p['total_sold'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert info">No sales data yet. Start selling to see analytics!</div>
        <?php endif; ?>
    </div>
</body>
</html>