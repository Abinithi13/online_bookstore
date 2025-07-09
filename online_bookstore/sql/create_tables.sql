-- USERS TABLE
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    address TEXT,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIES TABLE
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- SUBCATEGORIES TABLE
CREATE TABLE subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- BOOKS TABLE
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    subcategory_id INT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id)
);

-- CART TABLE
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    quantity INT,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- WISHLIST TABLE
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    book_id INT,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- ORDERS TABLE
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10,2),
    payment_method ENUM('UPI', 'Cash on Delivery', 'NetBanking', 'Card'),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    delivery_address TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ORDER ITEMS TABLE
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    book_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- PAYMENTS TABLE
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    user_id INT,
    amount DECIMAL(10,2),
    method ENUM('UPI', 'Cash on Delivery', 'NetBanking', 'Card'),
    status ENUM('Success', 'Failed', 'Pending') DEFAULT 'Pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- EBOOKS TABLE
CREATE TABLE ebooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    file_path VARCHAR(255),
    is_rentable BOOLEAN DEFAULT TRUE,
    rent_duration INT, -- in days
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- RENTED EBOOKS TABLE
CREATE TABLE rented_ebooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ebook_id INT,
    rent_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rent_end TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (ebook_id) REFERENCES ebooks(id)
);

ALTER TABLE books 
ADD COLUMN rent_duration INT DEFAULT 0,  -- Rent duration in days
ADD COLUMN rental_expiry DATE;           -- Expiry date of rented books

ALTER TABLE books
ADD COLUMN ebook_file VARCHAR(255) AFTER description;



ALTER TABLE books ADD COLUMN available BOOLEAN DEFAULT TRUE;
ALTER TABLE orders
ADD COLUMN shipping_charge DECIMAL(10,2) DEFAULT 0.00;

ALTER TABLE orders 
MODIFY COLUMN payment_status ENUM('Pending','Completed','Failed') DEFAULT 'Pending';
DELETE FROM order_items WHERE order_id = 26; --for deleteting any order
UPDATE books SET stock = 10 WHERE id = 1;  -- Change 10 and 1 as needed
UPDATE books SET stock = 10;



-- === SAMPLE DATA STARTS HERE ===

-- Insert categories
INSERT INTO categories (name) VALUES 
('Education'), 
('Fiction'), 
('Science'), 
('Technology'), 
('History');

-- Insert subcategories
INSERT INTO subcategories (category_id, name) VALUES 
(1, 'Engineering'),
(1, 'Law'),
(1, 'Medical'),
(2, 'Mystery'),
(2, 'Romance'),
(3, 'Physics'),
(3, 'Biology'),
(4, 'Programming'),
(4, 'AI & Machine Learning'),
(5, 'World History');

-- Insert admin user
INSERT INTO users (username, password, email, full_name, address, role) VALUES 
('admin', 'admin123', 'admin@bookstore.com', 'Admin User', 'Admin Street, HQ', 'admin');

INSERT INTO books (title, author, price, type, description, ebook_file, cover_image)
VALUES 
('Bhagavad_Gita', 'Author A', 100.00, 'ebook', 'A sample eBook for testing.', 'uploads/ebooks/Bhagavad_Gita.pdf', 'uploads/covers/Bhagavad_Gita.jpg'),
('The_AI_revolution', 'Author B', 80.00, 'ebook', 'Another sample eBook for testing.', 'uploads/ebooks/The_AI_Revolution.pdf', 'uploads/covers/The_AI_Revolution.jpg'),
('Learn_Web_Development', 'Author C', 150.00, 'ebook', 'Yet another sample eBook for testing.', 'uploads/ebooks/Learn_Web_Development.pdf', 'uploads/covers/web.jpg');
