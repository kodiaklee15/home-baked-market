
<header class="site-header">
  <div class="container">
    <div class="header-content">
      <div class="logo">
        <a href="index.php">
          <h1>ShopEasy</h1>
        </a>
      </div>
      
      <nav class="main-nav">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="products.php">Products</a></li>
          <?php
            $categories_query = "SELECT * FROM categories LIMIT 5";
            $categories = $conn->query($categories_query);
            
            while($category = $categories->fetch_assoc()) {
              echo '<li><a href="products.php?category=' . $category['id'] . '">' . $category['name'] . '</a></li>';
            }
          ?>
        </ul>
      </nav>
      
      <div class="header-actions">
        <form action="products.php" method="get" class="search-form">
          <input type="text" name="search" placeholder="Search...">
          <button type="submit">
            <span class="search-icon">&#128269;</span>
          </button>
        </form>
        
        <a href="cart.php" class="cart-link">
          <span class="cart-icon">🛒</span>
          <span class="cart-count"><?php echo get_cart_count(); ?></span>
        </a>
      </div>
    </div>
  </div>
</header>
