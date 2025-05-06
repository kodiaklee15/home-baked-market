
<footer class="site-footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-column">
        <h3>Shop</h3>
        <ul>
          <li><a href="products.php">All Products</a></li>
          <?php
            $footer_categories_query = "SELECT * FROM categories LIMIT 4";
            $footer_categories = $conn->query($footer_categories_query);
            
            while($category = $footer_categories->fetch_assoc()) {
              echo '<li><a href="products.php?category=' . $category['id'] . '">' . $category['name'] . '</a></li>';
            }
          ?>
        </ul>
      </div>
      
      <div class="footer-column">
        <h3>Customer Service</h3>
        <ul>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">Shipping & Returns</a></li>
          <li><a href="#">FAQs</a></li>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Terms & Conditions</a></li>
        </ul>
      </div>
      
      <div class="footer-column">
        <h3>About Us</h3>
        <p>ShopEasy is your one-stop destination for all your shopping needs. We offer quality products at affordable prices.</p>
      </div>
      
      <div class="footer-column">
        <h3>Newsletter</h3>
        <p>Subscribe to receive updates on new products and special promotions.</p>
        <form action="#" method="post" class="newsletter-form">
          <input type="email" name="email" placeholder="Your email address" required>
          <button type="submit">Subscribe</button>
        </form>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> ShopEasy. All rights reserved.</p>
    </div>
  </div>
</footer>
