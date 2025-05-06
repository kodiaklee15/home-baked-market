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
  
  // Get unique customers (based on email) - FIXED GROUP BY query
  $customers_query = "SELECT ANY_VALUE(customer_name) as customer_name, 
                      email, 
                      COUNT(id) as order_count, 
                      SUM(total) as total_spent, 
                      MAX(created_at) as last_order_date
                      FROM orders
                      $search_condition
                      GROUP BY email
                      ORDER BY last_order_date DESC
                      LIMIT $offset, $items_per_page";
  $customers = $conn->query($customers_query);
  
  // Get total customers count
  $count_sql = "SELECT COUNT(DISTINCT email) AS total FROM orders" . $search_condition;
  $count_result = $conn->query($count_sql);
  $count_data = $count_result->fetch_assoc();
  $total_customers = $count_data['total'];
  $total_pages = ceil($total_customers / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customers - ShopEasy Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-content">
      <div class="admin-header">
        <h1>Customers</h1>
        <div class="header-actions">
          <form action="customers.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Search customers..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" class="btn">Search</button>
          </form>
        </div>
      </div>
      
      <div class="table-container">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Email</th>
              <th>Orders</th>
              <th>Total Spent</th>
              <th>Last Order</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($customers->num_rows > 0): ?>
              <?php while($customer = $customers->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $customer['customer_name']; ?></td>
                  <td><?php echo $customer['email']; ?></td>
                  <td><?php echo $customer['order_count']; ?></td>
                  <td><?php echo format_price($customer['total_spent']); ?></td>
                  <td><?php echo date('M d, Y', strtotime($customer['last_order_date'])); ?></td>
                  <td class="actions">
                    <a href="customer_orders.php?email=<?php echo urlencode($customer['email']); ?>" class="btn btn-small">View Orders</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No customers found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php if ($page > 1): ?>
            <a href="customers.php?page=<?php echo $page-1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link">&laquo; Previous</a>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="customers.php?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link <?php echo $page == $i ? 'active' : ''; ?>">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>
          
          <?php if ($page < $total_pages): ?>
            <a href="customers.php?page=<?php echo $page+1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>" class="pagination-link">Next &raquo;</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
