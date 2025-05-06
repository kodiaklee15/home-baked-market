
<?php
// Helper functions for the e-commerce site

/**
 * Format price as currency
 */
function format_price($price) {
  return '$' . number_format($price, 2);
}

/**
 * Get cart item count
 */
function get_cart_count() {
  $count = 0;
  if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
      $count += $item['quantity'];
    }
  }
  return $count;
}

/**
 * Generate slug from title
 */
function generate_slug($text) {
  // Replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  // Transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  // Remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);
  // Trim
  $text = trim($text, '-');
  // Remove duplicate -
  $text = preg_replace('~-+~', '-', $text);
  // Lowercase
  $text = strtolower($text);
  
  return $text;
}

/**
 * Truncate text to a given length
 */
function truncate_text($text, $length = 100) {
  if (strlen($text) > $length) {
    return substr($text, 0, $length) . '...';
  }
  return $text;
}

/**
 * Sanitize user input
 */
function sanitize_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
  return isset($_SESSION['customer_id']);
}

/**
 * Get current page URL
 */
function get_current_url() {
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
  $url = $protocol . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  return $url;
}
?>
