
<?php
  session_start();
  include 'includes/db_connect.php';
  include 'includes/functions.php';
  
  // Get featured products for homepage
  $featured_query = "SELECT * FROM products WHERE featured = 1 LIMIT 6";
  $featured_products = $conn->query($featured_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopEasy - Online Shopping</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>
  
  <main>
    <section class="hero">
      <div class="container">
        <h1>Welcome to ShopEasy</h1>
        <p>Find the best products at the best prices</p>
        <a href="products.php" class="btn">Shop Now</a>
      </div>
    </section>

    <section class="featured">
      <div class="container">
        <h2>Featured Products</h2>
        <div class="product-grid">
          <?php
            if ($featured_products->num_rows > 0) {
              while($product = $featured_products->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '<img src="images/products/' . $product['image'] . '" alt="' . $product['name'] . '">';
                echo '<h3>' . $product['name'] . '</h3>';
                echo '<p class="price">$' . $product['price'] . '</p>';
                echo '<a href="product.php?id=' . $product['id'] . '" class="btn">View Details</a>';
                echo '<form action="includes/cart_actions.php" method="post">';
                echo '<input type="hidden" name="action" value="add">';
                echo '<input type="hidden" name="product_id" value="' . $product['id'] . '">';
                echo '<button type="submit" class="btn btn-primary">Add to Cart</button>';
                echo '</form>';
                echo '</div>';
              }
            } else {
              echo '<p>No featured products available.</p>';
            }
          ?>
        </div>
      </div>
    </section>

    <section class="categories">
      <div class="container">
        <h2>Shop by Category</h2>
        <div class="category-grid">
          <?php
            $categories_query = "SELECT * FROM categories LIMIT 4";
            $categories = $conn->query($categories_query);
            
            if ($categories->num_rows > 0) {
              while($category = $categories->fetch_assoc()) {
                echo '<a href="products.php?category=' . $category['id'] . '" class="category-card">';
                echo '<img src="images/categories/' . $category['image'] . '" alt="' . $category['name'] . '">';
                echo '<h3>' . $category['name'] . '</h3>';
                echo '</a>';
              }
            } else {
              echo '<p>No categories available.</p>';
            }
          ?>
        </div>
      </div>
    </section>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
