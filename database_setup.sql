
-- Database setup script for ShopEasy e-commerce website

-- Create the database
CREATE DATABASE IF NOT EXISTS shopeasy;
USE shopeasy;

-- Create tables
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  image VARCHAR(255) DEFAULT 'default.jpg',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10, 2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) DEFAULT 'default.jpg',
  category_id INT,
  featured TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100) NOT NULL,
  zip VARCHAR(20) NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  subtotal DECIMAL(10, 2) NOT NULL,
  tax DECIMAL(10, 2) NOT NULL,
  shipping DECIMAL(10, 2) NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  quantity INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
-- Categories
INSERT INTO categories (name, description, image) VALUES
('Electronics', 'Electronic devices and accessories', 'electronics.jpg'),
('Clothing', 'Mens and womens clothing', 'clothing.jpg'),
('Home & Kitchen', 'Products for your home', 'home.jpg'),
('Books', 'Fiction and non-fiction books', 'books.jpg');

-- Products
INSERT INTO products (name, description, price, stock, image, category_id, featured) VALUES
('Smartphone X', 'Latest flagship smartphone with high-end features', 799.99, 25, 'smartphone.jpg', 1, 1),
('Laptop Pro', '15-inch laptop with powerful CPU and long battery life', 1299.99, 15, 'laptop.jpg', 1, 1),
('Wireless Headphones', 'Noise cancelling wireless headphones', 199.99, 50, 'headphones.jpg', 1, 0),
('Smart Watch', 'Track your fitness and receive notifications', 249.99, 30, 'smartwatch.jpg', 1, 1),
('Men\'s T-shirt', 'Comfortable cotton t-shirt in various colors', 24.99, 100, 'tshirt.jpg', 2, 0),
('Women\'s Jeans', 'Classic fit jeans with stretch fabric', 49.99, 75, 'jeans.jpg', 2, 1),
('Hoodie', 'Warm hoodie perfect for cold days', 39.99, 60, 'hoodie.jpg', 2, 0),
('Coffee Maker', 'Programmable coffee maker for your kitchen', 79.99, 20, 'coffeemaker.jpg', 3, 1),
('Blender', 'High-speed blender for smoothies and more', 69.99, 25, 'blender.jpg', 3, 0),
('Toaster', '2-slice toaster with multiple settings', 34.99, 40, 'toaster.jpg', 3, 0),
('Bestseller Novel', 'The latest bestselling fiction novel', 19.99, 200, 'novel.jpg', 4, 0),
('Cookbook', 'Recipes for every occasion', 29.99, 30, 'cookbook.jpg', 4, 0),
('Self-Help Book', 'Improve your life with this guide', 24.99, 50, 'selfhelp.jpg', 4, 1);

-- Admin user
INSERT INTO admins (username, password, email) VALUES
('admin', 'admin123', 'admin@shopeasy.com');
