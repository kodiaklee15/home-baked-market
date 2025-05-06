
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
  }
  
  // Handle product deletion
  if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $delete_sql = "DELETE FROM products WHERE id = $product_id";
    $conn->query($delete_sql);
    
    // Redirect to refresh the page
    header("Location: products.php?msg=deleted");
    exit();
  }
  
  // Setup pagination
  $items_per_page = 10;
  $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
  $offset = ($page - 1) * $items_per_page;
  
  // Handle search
  $search_condition = '';
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $search_condition = " WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
  }
  
  // Get total product count
  $count_sql = "SELECT COUNT(*) AS total FROM products" . $search_condition;
  $count_result = $conn->query($count_sql);
  $count_data = $count_result->fetch_assoc();
  $total_products = $count_data['total'];
  $total_pages = ceil($total_products / $items_per_page);
  
  // Get products
  $products_sql = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id
                  $search_condition
                  ORDER BY p.id DESC
                  LIMIT $offset, $items_per_page";
  $products = $conn->query($products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Products</h1>
        <div class="header-actions">
          <form action="products.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" class="btn">Search</button>
          </form>
          <a href="add_product.php" class="btn btn-primary">Add New Product</a>
        </div>
      </div>
      
      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert success">Product added successfully!</div>
      <?php endif; ?>
      
      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert success">Product updated successfully!</div>
      <?php endif; ?>
      
      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert success">Product deleted successfully!</div>
      <?php endif; ?>
      
      <div class="table-container">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($products->num_rows > 0): ?>
              <?php while($product = $products->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $product['id']; ?></td>
                  <td>
                    <img src="../images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-thumbnail">
                  </td>
                  <td><?php echo $product['name']; ?></td>
                  <td><?php echo $product['category_name']; ?></td>
                  <td>$<?php echo number_format($product['price'], 2); ?></td>
                  <td>
                    <span class="<?php echo $product['stock'] <= 5 ? 'stock-low' : ($product['stock'] <= 20 ? 'stock-medium' : 'stock-ok'); ?>">
                      <?php echo $product['stock']; ?>
                    </span>
                  </td>
                  <td class="actions">
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-small">Edit</a>
                    <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7">No products found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php if ($page > 1): ?>
            <a href="products.php?page=<?php echo $page-1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link">&laquo; Previous</a>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="products.php?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link <?php echo $page == $i ? 'active' : ''; ?>">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>
          
          <?php if ($page < $total_pages): ?>
            <a href="products.php?page=<?php echo $page+1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link">Next &raquo;</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
