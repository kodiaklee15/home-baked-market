
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
  }
  
  // Check if category ID is provided
  if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: categories.php");
    exit();
  }
  
  $category_id = $_GET['id'];
  
  // Get category details
  $category_query = "SELECT * FROM categories WHERE id = $category_id";
  $category_result = $conn->query($category_query);
  
  if ($category_result->num_rows === 0) {
    header("Location: categories.php");
    exit();
  }
  
  $category = $category_result->fetch_assoc();
  
  // Handle form submission
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    
    // Handle image upload
    $image = $category['image']; // Default to existing image
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
            
            // Delete old image if it's not the default
            if($category['image'] != 'default.jpg') {
              @unlink('../images/categories/' . $category['image']);
            }
          }
        }
      }
    }
    
    // Update database
    $update_sql = "UPDATE categories SET name = '$name', description = '$description', image = '$image' WHERE id = $category_id";
    $conn->query($update_sql);
    
    // Redirect back to categories
    header("Location: categories.php?msg=updated");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Category - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Edit Category: <?php echo $category['name']; ?></h1>
        <div class="header-actions">
          <a href="categories.php" class="btn">&larr; Back to Categories</a>
        </div>
      </div>
      
      <div class="admin-card">
        <form action="edit_category.php?id=<?php echo $category_id; ?>" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" id="name" name="name" value="<?php echo $category['name']; ?>" required>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?php echo $category['description']; ?></textarea>
          </div>
          <div class="form-group">
            <label>Current Image</label>
            <div class="current-image">
              <img src="../images/categories/<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="image">Update Image (Leave empty to keep current image)</label>
            <input type="file" id="image" name="image" accept="image/*">
          </div>
          <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
