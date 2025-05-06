
<?php
  session_start();
  include '../includes/db_connect.php';
  include '../includes/functions.php';
  
  // Check if admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
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
    $search_condition = " WHERE customer_name LIKE '%$search%' OR email LIKE '%$search%'";
  }
  
  // Get total orders count
  $count_sql = "SELECT COUNT(*) AS total FROM orders" . $search_condition;
  $count_result = $conn->query($count_sql);
  $count_data = $count_result->fetch_assoc();
  $total_orders = $count_data['total'];
  $total_pages = ceil($total_orders / $items_per_page);
  
  // Get orders
  $orders_sql = "SELECT * FROM orders
                $search_condition
                ORDER BY created_at DESC
                LIMIT $offset, $items_per_page";
  $orders = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Orders - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Orders</h1>
        <div class="header-actions">
          <form action="orders.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Search orders..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" class="btn">Search</button>
          </form>
        </div>
      </div>
      
      <div class="table-container">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Email</th>
              <th>Date</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($orders->num_rows > 0): ?>
              <?php while($order = $orders->fetch_assoc()): ?>
                <tr>
                  <td>#<?php echo $order['id']; ?></td>
                  <td><?php echo $order['customer_name']; ?></td>
                  <td><?php echo $order['email']; ?></td>
                  <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                  <td><?php echo format_price($order['total']); ?></td>
                  <td class="actions">
                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-small">View Details</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No orders found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php if ($page > 1): ?>
            <a href="orders.php?page=<?php echo $page-1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link">&laquo; Previous</a>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="orders.php?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link <?php echo $page == $i ? 'active' : ''; ?>">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>
          
          <?php if ($page < $total_pages): ?>
            <a href="orders.php?page=<?php echo $page+1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link">Next &raquo;</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
