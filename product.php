
<?php
  session_start();
  include 'includes/db_connect.php';
  include 'includes/functions.php';
  
  // Get product details
  if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
  }
  
  $product_id = $_GET['id'];
  $product_query = "SELECT p.*, c.name as category_name FROM products p 
                    JOIN categories c ON p.category_id = c.id 
                    WHERE p.id = $product_id";
  $product_result = $conn->query($product_query);
  
  if ($product_result->num_rows == 0) {
    header("Location: products.php");
    exit();
  }
  
  $product = $product_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $product['name']; ?> - ShopEasy</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>
  
  <main>
    <div class="container">
      <div class="breadcrumb">
        <a href="index.php">Home</a> &gt; 
        <a href="products.php?category=<?php echo $product['category_id']; ?>">
          <?php echo $product['category_name']; ?>
        </a> &gt; 
        <?php echo $product['name']; ?>
      </div>
      
      <div class="product-details">
        <div class="product-image">
          <img src="images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
        </div>
        
        <div class="product-info">
          <h1><?php echo $product['name']; ?></h1>
          <p class="product-price">$<?php echo $product['price']; ?></p>
          
          <?php if($product['stock'] > 0): ?>
            <p class="stock in-stock">In Stock (<?php echo $product['stock']; ?> available)</p>
          <?php else: ?>
            <p class="stock out-of-stock">Out of Stock</p>
          <?php endif; ?>
          
          <div class="product-description">
            <h3>Description</h3>
            <p><?php echo $product['description']; ?></p>
          </div>
          
          <form action="includes/cart_actions.php" method="post" class="cart-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <div class="quantity">
              <label for="quantity">Quantity:</label>
              <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
            </div>
            
            <button type="submit" class="btn btn-large btn-primary" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
              Add to Cart
            </button>
          </form>
        </div>
      </div>
      
      <section class="related-products">
        <h2>Related Products</h2>
        <div class="product-grid">
          <?php
            $related_query = "SELECT * FROM products 
                            WHERE category_id = " . $product['category_id'] . "
                            AND id != $product_id
                            LIMIT 4";
            $related_products = $conn->query($related_query);
            
            if ($related_products->num_rows > 0) {
              while($related = $related_products->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '<img src="images/products/' . $related['image'] . '" alt="' . $related['name'] . '">';
                echo '<h3>' . $related['name'] . '</h3>';
                echo '<p class="price">$' . $related['price'] . '</p>';
                echo '<a href="product.php?id=' . $related['id'] . '" class="btn">View Details</a>';
                echo '</div>';
              }
            } else {
              echo '<p>No related products found.</p>';
            }
          ?>
        </div>
      </section>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
