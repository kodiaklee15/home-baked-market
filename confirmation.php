
<?php
  session_start();
  include 'includes/db_connect.php';
  include 'includes/functions.php';
  
  // Check if order_id exists in session
  if (!isset($_SESSION['order_id'])) {
    header("Location: index.php");
    exit();
  }
  
  $order_id = $_SESSION['order_id'];
  
  // Get order details
  $order_query = "SELECT * FROM orders WHERE id = $order_id";
  $order_result = $conn->query($order_query);
  
  if ($order_result->num_rows == 0) {
    header("Location: index.php");
    exit();
  }
  
  $order = $order_result->fetch_assoc();
  
  // Get order items
  $items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
  $items_result = $conn->query($items_query);
  
  // Clear the order_id from session
  unset($_SESSION['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation - ShopEasy</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>
  
  <main>
    <div class="container">
      <div class="confirmation">
        <div class="confirmation-header">
          <h1>Order Confirmed!</h1>
          <p>Your order has been successfully placed.</p>
          <p class="order-number">Order #<?php echo $order_id; ?></p>
        </div>
        
        <div class="confirmation-details">
          <div class="confirmation-section">
            <h2>Order Details</h2>
            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
            <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
          </div>
          
          <div class="confirmation-section">
            <h2>Shipping Address</h2>
            <p><?php echo $order['customer_name']; ?></p>
            <p><?php echo $order['address']; ?></p>
            <p><?php echo $order['city'] . ', ' . $order['state'] . ' ' . $order['zip']; ?></p>
          </div>
        </div>
        
        <div class="confirmation-items">
          <h2>Order Items</h2>
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php while($item = $items_result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $item['name']; ?></td>
                  <td><?php echo $item['quantity']; ?></td>
                  <td>$<?php echo number_format($item['price'], 2); ?></td>
                  <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        
        <div class="confirmation-summary">
          <div class="summary-row">
            <span>Subtotal</span>
            <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
          </div>
          <div class="summary-row">
            <span>Tax</span>
            <span>$<?php echo number_format($order['tax'], 2); ?></span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span>$<?php echo number_format($order['shipping'], 2); ?></span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>$<?php echo number_format($order['total'], 2); ?></span>
          </div>
        </div>
        
        <div class="confirmation-actions">
          <p>A confirmation email has been sent to your email address.</p>
          <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
