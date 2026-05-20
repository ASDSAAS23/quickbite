-- =============================================================
-- QuickBite / Seges Foods — PostgreSQL Schema for Supabase
-- =============================================================
-- Run this in the Supabase SQL Editor to set up your database.
-- =============================================================

-- Clean slate (drop in reverse dependency order)
DROP TABLE IF EXISTS payments CASCADE;
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS cart CASCADE;
DROP TABLE IF EXISTS reservations CASCADE;
DROP TABLE IF EXISTS menu_items CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- ── Users ────────────────────────────────────────────────────
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Categories ───────────────────────────────────────────────
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Menu Items ───────────────────────────────────────────────
CREATE TABLE menu_items (
    id SERIAL PRIMARY KEY,
    category_id INT NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    item_name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    availability_status VARCHAR(20) NOT NULL DEFAULT 'Available'
        CHECK (availability_status IN ('Available', 'Unavailable')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Cart ─────────────────────────────────────────────────────
CREATE TABLE cart (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    menu_item_id INT NOT NULL REFERENCES menu_items(id) ON DELETE CASCADE,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Orders ───────────────────────────────────────────────────
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_method VARCHAR(20) NOT NULL DEFAULT 'Delivery'
        CHECK (delivery_method IN ('Delivery', 'Pickup')),
    payment_method VARCHAR(50) NOT NULL,
    order_status VARCHAR(20) NOT NULL DEFAULT 'Pending'
        CHECK (order_status IN ('Pending', 'Confirmed', 'Preparing', 'Delivered', 'Cancelled')),
    delivery_address TEXT,
    special_request TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Order Items ──────────────────────────────────────────────
CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    menu_item_id INT NOT NULL REFERENCES menu_items(id) ON DELETE CASCADE,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Reservations ─────────────────────────────────────────────
CREATE TABLE reservations (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests INT NOT NULL,
    special_request TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending'
        CHECK (status IN ('Pending', 'Approved', 'Rejected')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ── Payments ─────────────────────────────────────────────────
CREATE TABLE payments (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'Pending'
        CHECK (payment_status IN ('Pending', 'Paid', 'Failed')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================================
-- Seed Data
-- =============================================================

INSERT INTO categories (category_name) VALUES
('Burgers'),
('Pizza'),
('Chicken'),
('Shawarma'),
('Drinks'),
('Snacks');

-- Admin user (password: admin123)
INSERT INTO users (full_name, email, phone, password, role) VALUES
('QuickBite Admin', 'admin@quickbite.com', '+2348000000000',
 '$2y$12$Ud.mOQtVYbHzYqNI0FZ5n.rUbz.2dZBx6W9503fWzla91J2QMe3GW', 'admin');

INSERT INTO menu_items (category_id, item_name, description, price, image, availability_status) VALUES
(1, 'Classic Burger', 'Juicy grilled beef burger served with vegetables and house sauce.', 4500.00, 'burger.jpg', 'Available'),
(2, 'Cheese Pizza', 'Oven-baked cheese pizza with rich tomato sauce and fresh toppings.', 6000.00, 'pizza.jpg', 'Available'),
(3, 'Fried Chicken Combo', 'Crispy fried chicken served with fries and dipping sauce.', 5500.00, 'chicken.jpg', 'Available'),
(4, 'Chicken Shawarma', 'Soft wrap filled with chicken strips, vegetables, and creamy dressing.', 3800.00, 'shawarma.jpg', 'Available'),
(5, 'Soft Drink', 'Chilled beverage served cold to complement your meal.', 1000.00, 'drink.jpg', 'Available'),
(6, 'French Fries', 'Crispy golden fries served hot and lightly seasoned.', 2000.00, 'french-fries.jpg', 'Available');
