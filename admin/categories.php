
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
  }
  
  // Handle category addition
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    
    // Handle image upload
    $image = 'default.jpg';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
      $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
      $filename = $_FILES['image']['name'];
      $filetype = $_FILES['image']['type'];
      $filesize = $_FILES['image']['size'];
      
      // Verify file extension
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      if(array_key_exists($ext, $allowed)) {
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize < $maxsize) {
          // Rename the file to avoid duplicates
          $new_filename = uniqid() . '.' . $ext;
          
          // Move the file to the uploads directory
          if(move_uploaded_file($_FILES['image']['tmp_name'], '../images/categories/' . $new_filename)) {
            $image = $new_filename;
          }
        }
      }
    }
    
    // Insert into database
    $insert_sql = "INSERT INTO categories (name, description, image) VALUES ('$name', '$description', '$image')";
    $conn->query($insert_sql);
    
    // Redirect to refresh page
    header("Location: categories.php?msg=added");
    exit();
  }
  
  // Handle category deletion
  if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $category_id = $_GET['delete'];
    
    // Check if category has products
    $check_query = "SELECT COUNT(*) as product_count FROM products WHERE category_id = $category_id";
    $check_result = $conn->query($check_query);
    $check_data = $check_result->fetch_assoc();
    
    if ($check_data['product_count'] == 0) {
      // Safe to delete
      $delete_sql = "DELETE FROM categories WHERE id = $category_id";
      $conn->query($delete_sql);
      header("Location: categories.php?msg=deleted");
    } else {
      header("Location: categories.php?msg=error&error=has_products");
    }
    exit();
  }
  
  // Get all categories
  $categories_query = "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
                      FROM categories c ORDER BY name ASC";
  $categories = $conn->query($categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Categories</h1>
      </div>
      
      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert success">Category added successfully!</div>
      <?php endif; ?>
      
      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert success">Category deleted successfully!</div>
      <?php endif; ?>
      
      <?php if (isset($_GET['msg']) && $_GET['msg'] == 'error' && isset($_GET['error']) && $_GET['error'] == 'has_products'): ?>
        <div class="alert error">Cannot delete category because it has associated products.</div>
      <?php endif; ?>
      
      <div class="admin-grid">
        <div class="admin-card">
          <h2>Add New Category</h2>
          <form action="categories.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="name">Category Name</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label for="image">Category Image</label>
              <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
          </form>
        </div>
        
        <div class="admin-card full-width">
          <h2>Current Categories</h2>
          <div class="table-container">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Image</th>
                  <th>Name</th>
                  <th>Products</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($categories->num_rows > 0): ?>
                  <?php while($category = $categories->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo $category['id']; ?></td>
                      <td>
                        <img src="../images/categories/<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" class="category-thumbnail">
                      </td>
                      <td><?php echo $category['name']; ?></td>
                      <td><?php echo $category['product_count']; ?></td>
                      <td><?php echo truncate_text($category['description'], 100); ?></td>
                      <td class="actions">
                        <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn btn-small">Edit</a>
                        <a href="categories.php?delete=<?php echo $category['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this category? This cannot be undone.')">Delete</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6">No categories found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
