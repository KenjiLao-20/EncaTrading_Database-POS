<nav class="nav-menu">
    <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">🏠 Dashboard</a>
    <a href="pos.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'pos.php' ? 'active' : '' ?>">🛒 POS</a>
    <a href="products.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">📦 Products</a>
    <a href="categories.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">📂 Categories</a>
    <a href="suppliers.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'suppliers.php' ? 'active' : '' ?>">🏢 Suppliers</a>
    <a href="inventory.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active' : '' ?>">📊 Inventory</a>
    <a href="alerts.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'alerts.php' ? 'active' : '' ?>">🔔 Alerts</a>
    <a href="analytics.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">📈 Analytics</a>
    <div style="margin-left: auto; display: flex; align-items: center; gap: 10px; padding: 0 10px;">
        <span style="color: #475569; font-size: 14px;">👋 <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></span>
        <a href="logout.php" class="nav-item logout">🚪 Logout</a>
    </div>
</nav>