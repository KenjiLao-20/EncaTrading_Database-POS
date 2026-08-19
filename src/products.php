<?php
require_once 'includes/session.php';
requireAdmin();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Handle Add
if (isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $cat_id = $_POST['category_id'] ?: null;
    $supp_id = $_POST['supplier_id'] ?: null;
    $price = $_POST['unit_price'];
    $stock = $_POST['stock_quantity'];
    $reorder = $_POST['reorder_level'] ?: 5;
    $desc = $_POST['description'] ?: null;

    $stmt = $pdo->prepare("INSERT INTO products (product_name, category_id, supplier_id, unit_price, stock_quantity, reorder_level, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $cat_id, $supp_id, $price, $stock, $reorder, $desc]);
    if ($stock > 0) {
        updateStock($pdo->lastInsertId(), $stock, 'purchase', null, $_SESSION['user_id']);
    }
    header('Location: products.php?msg=added');
    exit;
}

// Handle Edit
if (isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $name = trim($_POST['product_name']);
    $cat_id = $_POST['category_id'] ?: null;
    $supp_id = $_POST['supplier_id'] ?: null;
    $price = $_POST['unit_price'];
    $stock = $_POST['stock_quantity'];
    $reorder = $_POST['reorder_level'] ?: 5;
    $desc = $_POST['description'] ?: null;

    $stmt = $pdo->prepare("UPDATE products SET product_name=?, category_id=?, supplier_id=?, unit_price=?, stock_quantity=?, reorder_level=?, description=? WHERE product_id=?");
    $stmt->execute([$name, $cat_id, $supp_id, $price, $stock, $reorder, $desc, $id]);
    header('Location: products.php?msg=updated');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    header('Location: products.php?msg=deleted');
    exit;
}

$products = $pdo->query("
    SELECT p.*, c.category_name, s.supplier_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
    ORDER BY p.product_id DESC
")->fetchAll();

$categories = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll();
$suppliers = $pdo->query("SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name")->fetchAll();

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$_GET['edit']]);
    $editProduct = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products - Enca Trading</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="main-content">
        <h1 class="page-title">📦 Products</h1>
        <p class="page-subtitle">Manage your product catalog</p>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <!-- Add / Edit Form -->
        <div class="form-container">
            <h3><?= $editProduct ? '✏️ Edit Product' : '➕ Add New Product' ?></h3>
            <form method="post">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="product_id" value="<?= $editProduct['product_id'] ?>">
                <?php endif; ?>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" required value="<?= $editProduct['product_name'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id">
                            <option value="">None</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['category_id'] ?>" <?= ($editProduct && $editProduct['category_id'] == $c['category_id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id">
                            <option value="">None</option>
                            <?php foreach ($suppliers as $s): ?>
                                <option value="<?= $s['supplier_id'] ?>" <?= ($editProduct && $editProduct['supplier_id'] == $s['supplier_id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['supplier_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Unit Price ($)</label>
                        <input type="number" step="0.01" name="unit_price" required value="<?= $editProduct['unit_price'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock_quantity" required value="<?= $editProduct['stock_quantity'] ?? 0 ?>">
                    </div>
                    <div class="form-group">
                        <label>Reorder Level</label>
                        <input type="number" name="reorder_level" value="<?= $editProduct['reorder_level'] ?? 5 ?>">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Description</label>
                        <input type="text" name="description" value="<?= $editProduct['description'] ?? '' ?>">
                    </div>
                </div>
                <div style="display:flex; gap:10px; margin-top:10px;">
                    <button type="submit" name="<?= $editProduct ? 'edit_product' : 'add_product' ?>" class="btn btn-primary"><?= $editProduct ? 'Update Product' : 'Add Product' ?></button>
                    <?php if ($editProduct): ?>
                        <a href="products.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Product List -->
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Category</th><th>Supplier</th>
                        <th>Price</th><th>Stock</th><th>Reorder</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['product_id'] ?></td>
                            <td><?= htmlspecialchars($p['product_name']) ?></td>
                            <td><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($p['supplier_name'] ?? 'N/A') ?></td>
                            <td>$<?= number_format($p['unit_price'], 2) ?></td>
                            <td><?= $p['stock_quantity'] ?></td>
                            <td><?= $p['reorder_level'] ?></td>
                            <td>
                                <a href="?edit=<?= $p['product_id'] ?>" class="btn-edit">✏️ Edit</a>
                                <a href="?delete=<?= $p['product_id'] ?>" class="btn-delete" onclick="return confirm('Delete this product?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>