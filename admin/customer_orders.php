<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
  }
  
  // Check if email is provided
  if (!isset($_GET['email']) || empty($_GET['email'])) {
    header("Location: customers.php");
    exit();
  }
  
  $email = $_GET['email'];
  
  // Get customer info - FIXED GROUP BY query
  $customer_query = "SELECT ANY_VALUE(customer_name) as customer_name, 
                    email, COUNT(id) as order_count, 
                    SUM(total) as total_spent 
                    FROM orders 
                    WHERE email = '$email'
                    GROUP BY email";
  $customer_result = $conn->query($customer_query);
  
  if ($customer_result->num_rows === 0) {
    header("Location: customers.php");
    exit();
  }
  
  $customer = $customer_result->fetch_assoc();
  
  // Get customer orders
  $orders_sql = "SELECT * FROM orders WHERE email = '$email' ORDER BY created_at DESC";
  $orders = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Orders - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Orders for <?php echo $customer['customer_name']; ?></h1>
        <div class="header-actions">
          <a href="customers.php" class="btn">&larr; Back to Customers</a>
        </div>
      </div>
      
      <div class="customer-stats">
        <div class="stat-card">
          <div class="stat-title">Email</div>
          <div class="stat-value"><?php echo $customer['email']; ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-title">Total Orders</div>
          <div class="stat-value"><?php echo $customer['order_count']; ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-title">Total Spent</div>
          <div class="stat-value"><?php echo format_price($customer['total_spent']); ?></div>
        </div>
      </div>
      
      <div class="table-container">
        <h2>Order History</h2>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Date</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($orders->num_rows > 0): ?>
              <?php while($order = $orders->fetch_assoc()): ?>
                <tr>
                  <td>#<?php echo $order['id']; ?></td>
                  <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                  <td><?php echo format_price($order['total']); ?></td>
                  <td class="actions">
                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-small">View Details</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4">No orders found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
