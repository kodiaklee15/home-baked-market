
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if already logged in
  if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
  }
  
  $error = '';
  
  // Handle login form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple authentication (in a real app, you would use password_hash/password_verify)
    $admin_query = "SELECT * FROM admins WHERE username = '$username'";
    $result = $conn->query($admin_query);
    
    if ($result->num_rows == 1) {
      $admin = $result->fetch_assoc();
      // For this example, we're checking plain text password (not secure for production)
      if ($password == $admin['password']) {
        // Success - create session and redirect
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        header("Location: index.php");
        exit();
      } else {
        $error = 'Invalid password';
      }
    } else {
      $error = 'Invalid username';
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - ShopEasy</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="login-page">
  <div class="login-container">
    <div class="login-header">
      <h1>ShopEasy Admin</h1>
    </div>
    
    <form action="login.php" method="post" class="login-form">
      <h2>Login</h2>
      
      <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    
    <div class="login-footer">
      <a href="../index.php">Back to Store</a>
    </div>
  </div>
</body>
</html>
