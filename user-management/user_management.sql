-- Buat database
CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    activation_token VARCHAR(255),
    reset_token VARCHAR(255),
    is_active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample admin user (password: password123)
INSERT INTO users (email, password, full_name, is_active) 
VALUES ('admin@gudang.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Gudang', 1);

-- Insert sample products
INSERT INTO products (name, description, price, stock, created_by) VALUES
('Laptop ASUS ROG', 'Laptop gaming dengan processor Intel i7', 15000000.00, 5, 1),
('Mouse Wireless Logitech', 'Mouse wireless dengan sensor tinggi', 350000.00, 20, 1),
('Keyboard Mechanical', 'Keyboard mechanical RGB', 800000.00, 15, 1),
('Monitor 24 inch', 'Monitor LED 24 inch Full HD', 1200000.00, 8, 1);