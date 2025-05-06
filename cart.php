
<?php
  session_start();
  include 'includes/db_connect.php';
  include 'includes/functions.php';
  
  // Initialize cart if it doesn't exist
  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
  }
  
  // Calculate cart totals
  $subtotal = 0;
  $tax_rate = 0.07; // 7% tax
  $shipping = 10; // Flat shipping rate
  
  foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
  }
  
  $tax = $subtotal * $tax_rate;
  $total = $subtotal + $tax + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart - ShopEasy</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>
  
  <main>
    <div class="container">
      <h1>Shopping Cart</h1>
      
      <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
          <p>Your cart is empty.</p>
          <a href="products.php" class="btn">Continue Shopping</a>
        </div>
      <?php else: ?>
        <div class="cart-container">
          <div class="cart-items">
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                  <tr>
                    <td class="product-info">
                      <img src="images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                      <div>
                        <h3><?php echo $item['name']; ?></h3>
                      </div>
                    </td>
                    <td class="price">$<?php echo $item['price']; ?></td>
                    <td>
                      <form action="includes/cart_actions.php" method="post" class="update-form">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                        <button type="submit" class="btn btn-small">Update</button>
                      </form>
                    </td>
                    <td class="item-total">$<?php echo $item['price'] * $item['quantity']; ?></td>
                    <td>
                      <form action="includes/cart_actions.php" method="post">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" class="btn btn-danger">Remove</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <div class="cart-summary">
            <h3>Order Summary</h3>
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
            <div class="cart-actions">
              <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
              <a href="products.php" class="btn btn-secondary btn-block">Continue Shopping</a>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
