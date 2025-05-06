
<?php
  session_start();
  include 'includes/db_connect.php';
  include 'includes/functions.php';
  
  // Check if cart is empty
  if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
  }
  
  // Handle form submission
  $errors = array();
  $success = false;
  
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    
    if (empty($name)) $errors['name'] = 'Name is required';
    if (empty($email)) $errors['email'] = 'Email is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required';
    if (empty($address)) $errors['address'] = 'Address is required';
    if (empty($city)) $errors['city'] = 'City is required';
    if (empty($state)) $errors['state'] = 'State is required';
    if (empty($zip)) $errors['zip'] = 'ZIP code is required';
    if (empty($payment_method)) $errors['payment_method'] = 'Payment method is required';
    
    // If no errors, process the order
    if (empty($errors)) {
      // Calculate order total
      $subtotal = 0;
      foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
      }
      
      $tax_rate = 0.07; // 7% tax
      $shipping = 10; // Flat shipping rate
      $tax = $subtotal * $tax_rate;
      $total = $subtotal + $tax + $shipping;
      
      // Insert order into database
      $order_sql = "INSERT INTO orders (customer_name, email, address, city, state, zip, payment_method, subtotal, tax, shipping, total, created_at)
                    VALUES ('$name', '$email', '$address', '$city', '$state', '$zip', '$payment_method', $subtotal, $tax, $shipping, $total, NOW())";
      
      if ($conn->query($order_sql)) {
        $order_id = $conn->insert_id;
        
        // Add order items
        foreach ($_SESSION['cart'] as $product_id => $item) {
          $item_sql = "INSERT INTO order_items (order_id, product_id, name, price, quantity)
                        VALUES ($order_id, $product_id, '{$item['name']}', {$item['price']}, {$item['quantity']})";
          $conn->query($item_sql);
          
          // Update product stock
          $update_stock = "UPDATE products SET stock = stock - {$item['quantity']} WHERE id = $product_id";
          $conn->query($update_stock);
        }
        
        // Clear the cart
        $_SESSION['cart'] = array();
        
        // Set success
        $success = true;
        $_SESSION['order_id'] = $order_id;
      } else {
        $errors['system'] = 'There was a problem processing your order. Please try again.';
      }
    }
  }
  
  // If order was successful, redirect to confirmation
  if ($success) {
    header("Location: confirmation.php");
    exit();
  }
  
  // Calculate totals for display
  $subtotal = 0;
  foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
  }
  
  $tax_rate = 0.07; // 7% tax
  $shipping = 10; // Flat shipping rate
  $tax = $subtotal * $tax_rate;
  $total = $subtotal + $tax + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - ShopEasy</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>
  
  <main>
    <div class="container">
      <h1>Checkout</h1>
      
      <div class="checkout-container">
        <form action="checkout.php" method="post" class="checkout-form">
          <div class="form-section">
            <h2>Shipping Information</h2>
            
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
              <?php if (isset($errors['name'])) echo '<span class="error">' . $errors['name'] . '</span>'; ?>
            </div>
            
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
              <?php if (isset($errors['email'])) echo '<span class="error">' . $errors['email'] . '</span>'; ?>
            </div>
            
            <div class="form-group">
              <label for="address">Address</label>
              <input type="text" id="address" name="address" value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>" required>
              <?php if (isset($errors['address'])) echo '<span class="error">' . $errors['address'] . '</span>'; ?>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?php echo isset($_POST['city']) ? $_POST['city'] : ''; ?>" required>
                <?php if (isset($errors['city'])) echo '<span class="error">' . $errors['city'] . '</span>'; ?>
              </div>
              
              <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" value="<?php echo isset($_POST['state']) ? $_POST['state'] : ''; ?>" required>
                <?php if (isset($errors['state'])) echo '<span class="error">' . $errors['state'] . '</span>'; ?>
              </div>
              
              <div class="form-group">
                <label for="zip">ZIP Code</label>
                <input type="text" id="zip" name="zip" value="<?php echo isset($_POST['zip']) ? $_POST['zip'] : ''; ?>" required>
                <?php if (isset($errors['zip'])) echo '<span class="error">' . $errors['zip'] . '</span>'; ?>
              </div>
            </div>
          </div>
          
          <div class="form-section">
            <h2>Payment Method</h2>
            
            <div class="payment-options">
              <div class="payment-option">
                <input type="radio" id="credit" name="payment_method" value="credit" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit') ? 'checked' : ''; ?>>
                <label for="credit">Credit Card</label>
              </div>
              
              <div class="payment-option">
                <input type="radio" id="paypal" name="payment_method" value="paypal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'paypal') ? 'checked' : ''; ?>>
                <label for="paypal">PayPal</label>
              </div>
              
              <div class="payment-option">
                <input type="radio" id="cod" name="payment_method" value="cod" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cod') ? 'checked' : ''; ?>>
                <label for="cod">Cash on Delivery</label>
              </div>
            </div>
            <?php if (isset($errors['payment_method'])) echo '<span class="error">' . $errors['payment_method'] . '</span>'; ?>
          </div>
          
          <button type="submit" class="btn btn-primary btn-block">Place Order</button>
          <?php if (isset($errors['system'])) echo '<div class="system-error">' . $errors['system'] . '</div>'; ?>
        </form>
        
        <div class="order-summary">
          <h2>Order Summary</h2>
          
          <div class="order-items">
            <?php foreach ($_SESSION['cart'] as $item): ?>
              <div class="summary-item">
                <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          
          <div class="summary-divider"></div>
          
          <div class="summary-item">
            <span>Subtotal</span>
            <span>$<?php echo number_format($subtotal, 2); ?></span>
          </div>
          
          <div class="summary-item">
            <span>Tax</span>
            <span>$<?php echo number_format($tax, 2); ?></span>
          </div>
          
          <div class="summary-item">
            <span>Shipping</span>
            <span>$<?php echo number_format($shipping, 2); ?></span>
          </div>
          
          <div class="summary-total">
            <span>Total</span>
            <span>$<?php echo number_format($total, 2); ?></span>
          </div>
          
          <a href="cart.php" class="btn btn-secondary btn-block">Edit Cart</a>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
