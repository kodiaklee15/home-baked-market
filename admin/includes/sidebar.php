
<aside class="admin-sidebar">
  <div class="sidebar-header">
    <h2>ShopEasy</h2>
    <p>Admin Panel</p>
  </div>
  
  <nav class="sidebar-nav">
    <ul>
      <li>
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
          <span class="icon">📊</span>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
          <span class="icon">📋</span>
          <span>Orders</span>
        </a>
      </li>
      <li>
        <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' || basename($_SERVER['PHP_SELF']) == 'add_product.php' || basename($_SERVER['PHP_SELF']) == 'edit_product.php' ? 'active' : ''; ?>">
          <span class="icon">📦</span>
          <span>Products</span>
        </a>
      </li>
      <li>
        <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
          <span class="icon">🏷️</span>
          <span>Categories</span>
        </a>
      </li>
      <li>
        <a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
          <span class="icon">👥</span>
          <span>Customers</span>
        </a>
      </li>
    </ul>
  </nav>
  
  <div class="sidebar-footer">
    <a href="../index.php" target="_blank">View Store</a>
    <a href="logout.php">Logout</a>
  </div>
</aside>
