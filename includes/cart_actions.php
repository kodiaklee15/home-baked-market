
<?php
session_start();
include 'db_connect.php';
include 'functions.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = array();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $action = $_POST['action'] ?? '';
  $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
  
  switch ($action) {
    case 'add':
      // Get product details
      $product_query = "SELECT * FROM products WHERE id = $product_id";
      $product_result = $conn->query($product_query);
      
      if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        
        // Check if the product is already in the cart
        if (isset($_SESSION['cart'][$product_id])) {
          // Update quantity
          $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
          // Add new product to cart
          $_SESSION['cart'][$product_id] = array(
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image'],
            'stock' => $product['stock']
          );
        }
      }
      break;
      
    case 'update':
      $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
      
      if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
      }
      break;
      
    case 'remove':
      if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
      }
      break;
      
    case 'clear':
      $_SESSION['cart'] = array();
      break;
  }
  
  // Redirect back to referring page
  $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../cart.php';
  header("Location: $redirect");
  exit();
} else {
  // Invalid request method
  header("Location: ../index.php");
  exit();
}
?>
