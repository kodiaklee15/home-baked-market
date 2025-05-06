
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
  }
  
  // Get categories for dropdown
  $categories_query = "SELECT * FROM categories ORDER BY name";
  $categories = $conn->query($categories_query);
  
  $errors = array();
  
  // Handle form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $name = $_POST['name'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $description = $_POST['description'] ?? '';
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    if (empty($name)) $errors['name'] = 'Name is required';
    if (empty($category_id)) $errors['category_id'] = 'Category is required';
    if (empty($price) || !is_numeric($price)) $errors['price'] = 'Valid price is required';
    if (!isset($_POST['stock']) || !is_numeric($stock)) $errors['stock'] = 'Valid stock quantity is required';
    if (empty($description)) $errors['description'] = 'Description is required';
    
    // Handle image upload
    $image = 'default.jpg'; // Default image
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
      $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
      $file_name = $_FILES['image']['name'];
      $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
      
      if (!in_array($file_ext, $allowed_extensions)) {
        $errors['image'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
      } else {
        // Generate unique filename
        $new_file_name = uniqid() . '.' . $file_ext;
        $upload_path = '../images/products/' . $new_file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
          $image = $new_file_name;
        } else {
          $errors['image'] = 'Error uploading file. Please try again.';
        }
      }
    }
    
    // If no errors, save product to database
    if (empty($errors)) {
      $sql = "INSERT INTO products (name, category_id, price, stock, description, image, featured)
              VALUES ('$name', $category_id, $price, $stock, '$description', '$image', $featured)";
      
      if ($conn->query($sql)) {
        header("Location: products.php?msg=added");
        exit();
      } else {
        $errors['system'] = 'Error adding product: ' . $conn->error;
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Add New Product</h1>
        <a href="products.php" class="btn">Back to Products</a>
      </div>
      
      <?php if (isset($errors['system'])): ?>
        <div class="alert error"><?php echo $errors['system']; ?></div>
      <?php endif; ?>
      
      <div class="form-container">
        <form action="add_product.php" method="post" enctype="multipart/form-data">
          <div class="form-grid">
            <div class="form-group">
              <label for="name">Product Name</label>
              <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
              <?php if (isset($errors['name'])) echo '<span class="error">' . $errors['name'] . '</span>'; ?>
            </div>
            
            <div class="form-group">
              <label for="category_id">Category</label>
              <select id="category_id" name="category_id">
                <option value="">Select Category</option>
                <?php while($category = $categories->fetch_assoc()): ?>
                  <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                    <?php echo $category['name']; ?>
                  </option>
                <?php endwhile; ?>
              </select>
              <?php if (isset($errors['category_id'])) echo '<span class="error">' . $errors['category_id'] . '</span>'; ?>
            </div>
            
            <div class="form-group">
              <label for="price">Price ($)</label>
              <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>">
              <?php if (isset($errors['price'])) echo '<span class="error">' . $errors['price'] . '</span>'; ?>
            </div>
            
            <div class="form-group">
              <label for="stock">Stock Quantity</label>
              <input type="number" id="stock" name="stock" min="0" value="<?php echo isset($_POST['stock']) ? $_POST['stock'] : ''; ?>">
              <?php if (isset($errors['stock'])) echo '<span class="error">' . $errors['stock'] . '</span>'; ?>
            </div>
            
            <div class="form-group full-width">
              <label for="image">Product Image</label>
              <input type="file" id="image" name="image">
              <?php if (isset($errors['image'])) echo '<span class="error">' . $errors['image'] . '</span>'; ?>
              <small>Recommended size: 600 x 600 pixels. Max file size: 2MB.</small>
            </div>
            
            <div class="form-group full-width">
              <label for="description">Description</label>
              <textarea id="description" name="description" rows="5"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
              <?php if (isset($errors['description'])) echo '<span class="error">' . $errors['description'] . '</span>'; ?>
            </div>
            
            <div class="form-group checkbox-group">
              <label class="checkbox-label">
                <input type="checkbox" name="featured" <?php echo (isset($_POST['featured'])) ? 'checked' : ''; ?>>
                Featured Product
              </label>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
