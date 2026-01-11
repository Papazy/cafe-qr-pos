-- ========================================
-- WARKOP QR - DATA SEEDER
-- Created: 2026-01-04
-- ========================================

USE warkop_qr;

-- ========================================
-- 1. TABLES (Meja) - 10 Meja
-- ========================================
INSERT INTO tables (table_number, qr_token, status) VALUES
(1, 'TBL1-QR-ABC123-2026', 'available'),
(2, 'TBL2-QR-DEF456-2026', 'available'),
(3, 'TBL3-QR-GHI789-2026', 'available'),
(4, 'TBL4-QR-JKL012-2026', 'available'),
(5, 'TBL5-QR-MNO345-2026', 'available'),
(6, 'TBL6-QR-PQR678-2026', 'available'),
(7, 'TBL7-QR-STU901-2026', 'available'),
(8, 'TBL8-QR-VWX234-2026', 'available'),
(9, 'TBL9-QR-YZA567-2026', 'available'),
(10, 'TBL10-QR-BCD890-2026', 'available');

-- ========================================
-- 2. USERS (Admin & Kasir)
-- ========================================
-- Password: admin123 (hashed dengan password_hash PHP)
-- Password: kasir123 (hashed dengan password_hash PHP)
INSERT INTO users (username, password, role, name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator'),
('kasir', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kasir', 'Kasir Utama'),
('kasir2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kasir', 'Kasir 2');

-- ========================================
-- 3. MENUS (Menu Items) - Sesuai dengan HTML
-- ========================================

-- Kategori: Kopi
INSERT INTO menus (name, category, price, description, available) VALUES
('Kopi Hitam', 'Kopi', 8000, 'Kopi hitam original pilihan', TRUE),
('Kopi Susu', 'Kopi', 12000, 'Kopi dengan susu segar', TRUE),
('Cappuccino', 'Kopi', 15000, 'Espresso dengan foam susu', TRUE),
('Latte', 'Kopi', 15000, 'Espresso dengan susu steamed', TRUE),
('Americano', 'Kopi', 13000, 'Espresso dengan air panas', TRUE),
('Espresso', 'Kopi', 10000, 'Shot espresso murni', TRUE),
('Kopi Tubruk', 'Kopi', 7000, 'Kopi tubruk tradisional', TRUE),
('Vietnam Drip', 'Kopi', 14000, 'Kopi Vietnam dengan susu kental', TRUE);

-- Kategori: Non Kopi
INSERT INTO menus (name, category, price, description, available) VALUES
('Teh Manis', 'Non Kopi', 5000, 'Teh manis hangat', TRUE),
('Teh Tarik', 'Non Kopi', 8000, 'Teh susu tarik', TRUE),
('Jus Jeruk', 'Non Kopi', 12000, 'Jus jeruk segar', TRUE),
('Jus Alpukat', 'Non Kopi', 15000, 'Jus alpukat dengan cokelat', TRUE),
('Milkshake', 'Non Kopi', 18000, 'Milkshake berbagai rasa', TRUE),
('Teh Lemon', 'Non Kopi', 10000, 'Teh dengan lemon segar', TRUE),
('Cokelat Panas', 'Non Kopi', 12000, 'Cokelat hangat dengan susu', TRUE),
('Thai Tea', 'Non Kopi', 13000, 'Thai tea original', TRUE);

-- Kategori: Makanan
INSERT INTO menus (name, category, price, description, available) VALUES
('Nasi Goreng', 'Makanan', 20000, 'Nasi goreng spesial', TRUE),
('Mie Goreng', 'Makanan', 18000, 'Mie goreng spesial', TRUE),
('Nasi Goreng Seafood', 'Makanan', 25000, 'Nasi goreng dengan seafood', TRUE),
('Mie Ayam', 'Makanan', 17000, 'Mie ayam bakso', TRUE),
('Nasi Uduk', 'Makanan', 15000, 'Nasi uduk komplit', TRUE),
('Soto Ayam', 'Makanan', 18000, 'Soto ayam kuah kuning', TRUE),
('Nasi Kuning', 'Makanan', 16000, 'Nasi kuning dengan lauk', TRUE);

-- Kategori: Snack
INSERT INTO menus (name, category, price, description, available) VALUES
('Pisang Goreng', 'Snack', 10000, 'Pisang goreng crispy', TRUE),
('Kentang Goreng', 'Snack', 15000, 'French fries dengan saus', TRUE),
('Singkong Goreng', 'Snack', 8000, 'Singkong goreng renyah', TRUE),
('Tahu Isi', 'Snack', 12000, 'Tahu isi sayuran', TRUE),
('Risoles', 'Snack', 10000, 'Risoles isi ragout', TRUE),
('Lumpia', 'Snack', 10000, 'Lumpia sayuran', TRUE),
('Onion Rings', 'Snack', 15000, 'Onion rings crispy', TRUE),
('Chicken Nugget', 'Snack', 18000, 'Chicken nugget dengan saus', TRUE);

-- ========================================
-- 4. SAMPLE ORDERS (Contoh Pesanan)
-- ========================================

-- Order 1: Meja 1 - Pending
INSERT INTO orders (order_number, table_id, customer_name, notes, payment_method, subtotal, tax, total, status, created_at) 
VALUES ('ORD-20260104-0001', 1, 'Budi Santoso', 'Kopinya less sugar', 'Cash', 40000, 4000, 44000, 'Pending', '2026-01-04 08:30:00');

INSERT INTO order_items (order_id, menu_id, menu_name, menu_price, quantity, subtotal) VALUES
(1, 2, 'Kopi Susu', 12000, 2, 24000),
(1, 13, 'Pisang Goreng', 10000, 1, 10000),
(1, 6, 'Teh Tarik', 8000, 1, 8000);

-- Order 2: Meja 3 - Paid
INSERT INTO orders (order_number, table_id, customer_name, notes, payment_method, subtotal, tax, total, status, created_at) 
VALUES ('ORD-20260104-0002', 3, 'Siti Aminah', '', 'Transfer', 53000, 5300, 58300, 'Paid', '2026-01-04 09:15:00');

INSERT INTO order_items (order_id, menu_id, menu_name, menu_price, quantity, subtotal) VALUES
(2, 10, 'Nasi Goreng', 20000, 1, 20000),
(2, 3, 'Cappuccino', 15000, 1, 15000),
(2, 16, 'Kentang Goreng', 15000, 1, 15000);

-- Order 3: Meja 5 - Processing
INSERT INTO orders (order_number, table_id, customer_name, notes, payment_method, subtotal, tax, total, status, created_at) 
VALUES ('ORD-20260104-0003', 5, 'Ahmad Rifai', 'Minta cepat', 'E-Wallet', 65000, 6500, 71500, 'Processing', '2026-01-04 09:45:00');

INSERT INTO order_items (order_id, menu_id, menu_name, menu_price, quantity, subtotal) VALUES
(3, 11, 'Mie Goreng', 18000, 2, 36000),
(3, 8, 'Jus Jeruk', 12000, 1, 12000),
(3, 15, 'Tahu Isi', 12000, 1, 12000);

-- Order 4: Meja 2 - Completed
INSERT INTO orders (order_number, table_id, customer_name, notes, payment_method, subtotal, tax, total, status, created_at) 
VALUES ('ORD-20260104-0004', 2, 'Dewi Lestari', '', 'Cash', 48000, 4800, 52800, 'Completed', '2026-01-04 10:00:00');

INSERT INTO order_items (order_id, menu_id, menu_name, menu_price, quantity, subtotal) VALUES
(4, 4, 'Latte', 15000, 2, 30000),
(4, 13, 'Pisang Goreng', 10000, 1, 10000),
(4, 6, 'Teh Tarik', 8000, 1, 8000);

-- Order 5: Meja 7 - Pending (Recent)
INSERT INTO orders (order_number, table_id, customer_name, notes, payment_method, subtotal, tax, total, status, created_at) 
VALUES ('ORD-20260104-0005', 7, 'Rudi Hermawan', 'Tambah es batu', 'Cash', 35000, 3500, 38500, 'Pending', NOW());

INSERT INTO order_items (order_id, menu_id, menu_name, menu_price, quantity, subtotal) VALUES
(5, 1, 'Kopi Hitam', 8000, 1, 8000),
(5, 5, 'Americano', 13000, 1, 13000),
(5, 16, 'Kentang Goreng', 15000, 1, 15000);

-- ========================================
-- 5. ORDER STATUS LOGS (Manual untuk sample data)
-- ========================================
INSERT INTO order_status_logs (order_id, old_status, new_status, changed_by, created_at) VALUES
(2, 'Pending', 'Paid', 1, '2026-01-04 09:20:00'),
(3, 'Pending', 'Paid', 2, '2026-01-04 09:50:00'),
(3, 'Paid', 'Processing', 2, '2026-01-04 09:52:00'),
(4, 'Pending', 'Paid', 1, '2026-01-04 10:05:00'),
(4, 'Paid', 'Processing', 2, '2026-01-04 10:10:00'),
(4, 'Processing', 'Completed', 2, '2026-01-04 10:25:00');

-- ========================================
-- UPDATE table status (occupied for active orders)
-- ========================================
UPDATE tables SET status = 'occupied' WHERE id IN (1, 3, 5, 7);

-- ========================================
-- VERIFICATION QUERIES
-- ========================================

-- Total meja
SELECT COUNT(*) as total_tables FROM tables;

-- Total menu per kategori
SELECT category, COUNT(*) as total FROM menus GROUP BY category;

-- Total orders per status
SELECT status, COUNT(*) as total FROM orders GROUP BY status;

-- Revenue hari ini
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_orders,
    SUM(total) as total_revenue
FROM orders
WHERE DATE(created_at) = CURDATE()
GROUP BY DATE(created_at);

-- Top 5 menu terlaris
SELECT 
    m.name,
    m.category,
    SUM(oi.quantity) as total_sold,
    SUM(oi.subtotal) as total_revenue
FROM order_items oi
JOIN menus m ON oi.menu_id = m.id
GROUP BY oi.menu_id
ORDER BY total_sold DESC
LIMIT 5;

-- ========================================
-- SELESAI
-- ========================================
