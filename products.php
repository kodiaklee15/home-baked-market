
<?php
  session_start();
  include 'includes/db_connect.php';
  include 'includes/functions.php';
  
  // Handle pagination
  $page = isset($_GET['page']) ? $_GET['page'] : 1;
  $items_per_page = 12;
  $offset = ($page - 1) * $items_per_page;
  
  // Handle filtering
  $where_clause = "1=1"; // Always true condition to start with
  
  if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_id = $_GET['category'];
    $where_clause .= " AND category_id = $category_id";
  }
  
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $where_clause .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
  }
  
  // Get products
  $products_query = "SELECT * FROM products WHERE $where_clause LIMIT $items_per_page OFFSET $offset";
  $products = $conn->query($products_query);
  
  // Count total for pagination
  $count_query = "SELECT COUNT(*) as total FROM products WHERE $where_clause";
  $count_result = $conn->query($count_query);
  $count_row = $count_result->fetch_assoc();
  $total_items = $count_row['total'];
  $total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - ShopEasy</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>
  
  <main>
    <div class="container">
      <div class="products-header">
        <h1>All Products</h1>
        <form action="products.php" method="get" class="search-form">
          <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
          <button type="submit" class="btn">Search</button>
        </form>
      </div>
      
      <div class="products-container">
        <aside class="filters">
          <h3>Categories</h3>
          <ul>
            <li><a href="products.php">All Categories</a></li>
            <?php
              $categories_query = "SELECT * FROM categories";
              $categories = $conn->query($categories_query);
              
              if ($categories->num_rows > 0) {
                while($category = $categories->fetch_assoc()) {
                  $active = isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'class="active"' : '';
                  echo '<li><a href="products.php?category=' . $category['id'] . '" ' . $active . '>' . $category['name'] . '</a></li>';
                }
              }
            ?>
          </ul>
        </aside>
        
        <section class="product-listings">
          <div class="product-grid">
            <?php
              if ($products->num_rows > 0) {
                while($product = $products->fetch_assoc()) {
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
                echo '<p>No products found.</p>';
              }
            ?>
          </div>
          
          <?php if ($total_pages > 1): ?>
            <div class="pagination">
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="products.php?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" 
                   class="<?php echo $page == $i ? 'active' : ''; ?>">
                  <?php echo $i; ?>
                </a>
              <?php endfor; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
