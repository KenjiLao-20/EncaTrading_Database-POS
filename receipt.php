<?php
require_once 'includes/session.php';
requireLogin();
require_once 'config/database.php';
require_once 'includes/functions.php';

$sale_id = $_GET['sale_id'] ?? 0;
if (!$sale_id) die('No sale specified.');

// Fetch sale details
$stmt = $pdo->prepare("SELECT s.*, u.full_name as cashier FROM sales s JOIN users u ON s.user_id = u.user_id WHERE s.sale_id = ?");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();
if (!$sale) die('Sale not found.');

$stmt = $pdo->prepare("SELECT si.*, p.product_name FROM sale_items si JOIN products p ON si.product_id = p.product_id WHERE si.sale_id = ?");
$stmt->execute([$sale_id]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?= $sale_id ?></title>
    <script src="assets/js/script.js" defer></script>
    <style>
        body { font-family: monospace; max-width: 300px; margin: auto; }
        .receipt { border: 1px solid #000; padding: 10px; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        .total { font-size: 1.2em; }
        button { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <h3>Enca Trading</h3>
            <p>Receipt #<?= $sale_id ?></p>
            <p><?= date('Y-m-d H:i', strtotime($sale['sale_date'])) ?></p>
            <p>Cashier: <?= htmlspecialchars($sale['cashier']) ?></p>
        </div>
        <div class="line"></div>
        <table style="width:100%;">
            <tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['unit_price'], 2) ?></td>
                    <td><?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="line"></div>
        <div class="center total">
            <strong>Total: <?= number_format($sale['total_amount'], 2) ?></strong>
        </div>
        <div class="line"></div>
        <div class="center">
            <p>Thank you for your purchase!</p>
        </div>
    </div>
    <div style="text-align:center; margin-top:10px;">
        <button id="printReceipt" onclick="window.print()">Print Receipt</button>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>