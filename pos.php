<?php
require_once 'includes/session.php';
requireLogin();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $product = getProductById($product_id);
    if ($product && $qty > 0 && $qty <= $product['stock_quantity']) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['qty'] += $qty;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'name' => $product['product_name'],
                'price' => $product['unit_price'],
                'qty' => $qty
            ];
        }
    }
    header('Location: pos.php');
    exit;
}

// Remove from cart
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index])) unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header('Location: pos.php');
    exit;
}

// Clear cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header('Location: pos.php');
    exit;
}

// Checkout
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $error = 'Cart is empty.';
    } else {
        $user_id = $_SESSION['user_id'];
        $total = 0;
        $items = [];
        foreach ($_SESSION['cart'] as $item) {
            $subtotal = $item['price'] * $item['qty'];
            $total += $subtotal;
            $items[] = [
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $subtotal
            ];
        }
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO sales (user_id, total_amount) VALUES (?, ?)");
            $stmt->execute([$user_id, $total]);
            $sale_id = $pdo->lastInsertId();

            foreach ($items as $item) {
                $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$sale_id, $item['product_id'], $item['qty'], $item['price'], $item['subtotal']]);
                updateStock($item['product_id'], -$item['qty'], 'sale', $sale_id, $user_id);
            }
            $pdo->commit();
            unset($_SESSION['cart']);
            header("Location: receipt.php?sale_id=$sale_id");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Checkout failed: ' . $e->getMessage();
        }
    }
}

$products = $pdo->query("SELECT product_id, product_name, stock_quantity FROM products")->fetchAll();
$cart = $_SESSION['cart'] ?? [];
$total_cart = array_sum(array_map(function($item){ return $item['price'] * $item['qty']; }, $cart));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Point of Sale - Enca Trading</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">🛒 Point of Sale</h1>
        <p class="page-subtitle">Process customer transactions quickly</p>

        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="pos-grid">
            <!-- Product selection -->
            <div>
                <div class="form-container" style="margin:0; max-width:100%;">
                    <h3>Add Product to Cart</h3>
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
                            <label>Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" required>
                        </div>
                        <button type="submit" name="add_to_cart" class="btn btn-primary" style="width:100%;">➕ Add to Cart</button>
                    </form>
                </div>
            </div>

            <!-- Cart -->
            <div>
                <div class="form-container" style="margin:0; max-width:100%;">
                    <h3>🛍️ Current Cart</h3>
                    <?php if (empty($cart)): ?>
                        <p style="color: #94a3b8; text-align: center; padding: 20px 0;">Cart is empty. Add items to start selling!</p>
                    <?php else: ?>
                        <div class="table-wrapper" style="margin:0;">
                            <table class="data-table">
                                <thead>
                                    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $i => $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['name']) ?></td>
                                            <td><?= $item['qty'] ?></td>
                                            <td>$<?= number_format($item['price'], 2) ?></td>
                                            <td>$<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                            <td><a href="?remove=<?= $i ?>" class="btn-delete" onclick="return confirm('Remove this item?')">✖</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="cart-total">
                            <span>Total</span>
                            <span>$<?= number_format($total_cart, 2) ?></span>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:15px;">
                            <form method="post" style="flex:1;">
                                <button type="submit" name="checkout" class="btn btn-success" style="width:100%;">✅ Checkout</button>
                            </form>
                            <a href="?clear" class="btn btn-danger" style="flex:0;" onclick="return confirm('Clear entire cart?')">🗑️ Clear</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>