
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
  }
  
  // Get stats for dashboard
  $orders_query = "SELECT COUNT(*) AS total_orders, SUM(total) AS total_revenue FROM orders";
  $orders_result = $conn->query($orders_query);
  $orders_data = $orders_result->fetch_assoc();
  
  $products_query = "SELECT COUNT(*) AS total_products FROM products";
  $products_result = $conn->query($products_query);
  $products_data = $products_result->fetch_assoc();
  
  $customers_query = "SELECT COUNT(DISTINCT email) AS total_customers FROM orders";
  $customers_result = $conn->query($customers_query);
  $customers_data = $customers_result->fetch_assoc();
  
  // Get recent orders
  $recent_orders_query = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
  $recent_orders = $conn->query($recent_orders_query);
  
  // Get low stock products
  $low_stock_query = "SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5";
  $low_stock = $conn->query($low_stock_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - ShopEasy</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Dashboard</h1>
        <div class="admin-user">
          <span>Welcome, Admin</span>
          <a href="logout.php" class="logout-btn">Logout</a>
        </div>
      </div>
      
      <div class="stats-container">
        <div class="stat-card">
          <div class="stat-icon orders-icon">
            <i class="icon-orders"></i>
          </div>
          <div class="stat-info">
            <h3>Total Orders</h3>
            <p class="stat-number"><?php echo $orders_data['total_orders']; ?></p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon revenue-icon">
            <i class="icon-revenue"></i>
          </div>
          <div class="stat-info">
            <h3>Total Revenue</h3>
            <p class="stat-number">$<?php echo number_format($orders_data['total_revenue'], 2); ?></p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon products-icon">
            <i class="icon-products"></i>
          </div>
          <div class="stat-info">
            <h3>Total Products</h3>
            <p class="stat-number"><?php echo $products_data['total_products']; ?></p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon customers-icon">
            <i class="icon-customers"></i>
          </div>
          <div class="stat-info">
            <h3>Total Customers</h3>
            <p class="stat-number"><?php echo $customers_data['total_customers']; ?></p>
          </div>
        </div>
      </div>
      
      <div class="dashboard-grid">
        <div class="dashboard-card">
          <div class="card-header">
            <h2>Recent Orders</h2>
            <a href="orders.php" class="view-all">View All</a>
          </div>
          <div class="card-content">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Date</th>
                  <th>Total</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($recent_orders->num_rows > 0): ?>
                  <?php while($order = $recent_orders->fetch_assoc()): ?>
                    <tr>
                      <td><a href="view_order.php?id=<?php echo $order['id']; ?>">#<?php echo $order['id']; ?></a></td>
                      <td><?php echo $order['customer_name']; ?></td>
                      <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                      <td>$<?php echo number_format($order['total'], 2); ?></td>
                      <td><span class="status-badge"><?php echo 'Completed'; ?></span></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5">No orders found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        
        <div class="dashboard-card">
          <div class="card-header">
            <h2>Low Stock Products</h2>
            <a href="products.php" class="view-all">View All</a>
          </div>
          <div class="card-content">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Stock</th>
                  <th>Price</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($low_stock->num_rows > 0): ?>
                  <?php while($product = $low_stock->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo $product['name']; ?></td>
                      <td><span class="<?php echo $product['stock'] <= 5 ? 'stock-low' : 'stock-medium'; ?>"><?php echo $product['stock']; ?></span></td>
                      <td>$<?php echo number_format($product['price'], 2); ?></td>
                      <td><a href="edit_product.php?id=<?php echo $product['id']; ?>" class="action-btn">Edit</a></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4">No low stock products.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
