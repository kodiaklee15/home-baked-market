
# ShopEasy E-commerce Website

## About
ShopEasy is a simple e-commerce website built with HTML, PHP, and CSS for localhost development and testing.

## Features
- Product browsing with categories
- Product search functionality
- Shopping cart system
- Checkout process
- Admin panel for product and order management

## Installation

1. **Set up a local PHP development environment**
   - Install a stack like XAMPP, WAMP, MAMP, or use PHP's built-in server

2. **Clone or download this repository to your web server directory**
   - For XAMPP: `htdocs` folder 
   - For WAMP: `www` folder
   - For MAMP: `htdocs` folder

3. **Create the database**
   - Create a MySQL database named `shopeasy`
   - Import the `database_setup.sql` file using phpMyAdmin or MySQL command line

4. **Configure database connection**
   - Open `includes/db_connect.php` and update the database credentials if needed:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "shopeasy";
     ```

5. **Create image directories**
   - Create the following directories in your project:
     - `images/products`
     - `images/categories`

6. **Access the website**
   - Open your web browser and navigate to: `http://localhost/shopeasy` (or the appropriate path based on your setup)

7. **Admin Access**
   - Navigate to `http://localhost/shopeasy/admin`
   - Login using:
     - Username: `admin`
     - Password: `admin123`

## File Structure
- `index.php` - Homepage
- `products.php` - Product listing page
- `product.php` - Individual product page
- `cart.php` - Shopping cart
- `checkout.php` - Checkout process
- `confirmation.php` - Order confirmation
- `admin/` - Admin panel files
- `includes/` - Shared PHP files
- `css/` - Stylesheets
- `images/` - Image directories

## Security Notes
This application is intended for local development and learning purposes only. 
For production use, additional security measures would be required:

- Proper password hashing
- Input validation and sanitization
- CSRF protection
- SQL injection prevention
- XSS prevention

## License
This project is for educational purposes only.
