
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
  }
  
  // Check if order ID is provided
  if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: orders.php");
    exit();
  }
  
  $order_id = $_GET['id'];
  
  // Get order details
  $order_sql = "SELECT * FROM orders WHERE id = $order_id";
  $order_result = $conn->query($order_sql);
  
  if ($order_result->num_rows === 0) {
    header("Location: orders.php");
    exit();
  }
  
  $order = $order_result->fetch_assoc();
  
  // Get order items
  $items_sql = "SELECT * FROM order_items WHERE order_id = $order_id";
  $items = $conn->query($items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order #<?php echo $order_id; ?> - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Order #<?php echo $order_id; ?></h1>
        <div class="header-actions">
          <a href="orders.php" class="btn">&larr; Back to Orders</a>
        </div>
      </div>
      
      <div class="order-details-grid">
        <div class="order-info-card">
          <h2>Order Information</h2>
          <div class="info-group">
            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> <span class="status-badge">Completed</span></p>
            <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
          </div>
        </div>
        
        <div class="order-info-card">
          <h2>Customer Information</h2>
          <div class="info-group">
            <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
            <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
            <p><?php echo $order['city']; ?>, <?php echo $order['state']; ?> <?php echo $order['zip']; ?></p>
          </div>
        </div>
        
        <div class="order-items-card">
          <h2>Order Items</h2>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($items->num_rows > 0): ?>
                <?php while($item = $items->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo format_price($item['price']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4">No items found for this order.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
          
          <div class="order-totals">
            <div class="total-row">
              <span>Subtotal:</span>
              <span><?php echo format_price($order['subtotal']); ?></span>
            </div>
            <div class="total-row">
              <span>Tax:</span>
              <span><?php echo format_price($order['tax']); ?></span>
            </div>
            <div class="total-row">
              <span>Shipping:</span>
              <span><?php echo format_price($order['shipping']); ?></span>
            </div>
            <div class="total-row grand-total">
              <span>Total:</span>
              <span><?php echo format_price($order['total']); ?></span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
