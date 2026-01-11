# 🗺️ Roadmap Simple - Warkop QR

## 📊 Overview Halaman

### 👤 CUSTOMER (User) - 5 Halaman
1. Landing Page (Scan QR)
2. Menu Page (Lihat & pilih menu)
3. Cart Page (Keranjang belanja)
4. Checkout Page (Form pembayaran)
5. Order Success (Konfirmasi)

### 👨‍💼 ADMIN - 5 Halaman
1. Login Page
2. Dashboard (Overview)
3. Orders Page (Kelola pesanan)
4. Menus Page (Kelola menu)
5. Reports Page (Laporan)

**Total: 10 halaman utama**

---

## 🎯 Phase by Phase Development

## 📱 PART 1: CUSTOMER SIDE (5-7 hari)

### Phase 1.1: Setup & Landing Page (1 hari)

**Tujuan:** Customer bisa scan QR dan masuk ke sistem

**File yang dibuat:**
- `index.php` - Landing page dengan QR validation

**Database:**
```sql
CREATE TABLE tables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_number INT NOT NULL,
    qr_token VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('available', 'occupied') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tables (table_number, qr_token) VALUES
(1, 'TOKEN_MEJA_1_ABC123'),
(2, 'TOKEN_MEJA_2_DEF456'),
(3, 'TOKEN_MEJA_3_GHI789');
```

**Test:**
- Buka: http://localhost:8000/?table=1&token=TOKEN_MEJA_1_ABC123
- Harus redirect ke menu page

**Checklist:**
- [ ] Database table `tables` created
- [ ] Landing page tampil
- [ ] QR token validation works
- [ ] Session table_id & table_number tersimpan

---

### Phase 1.2: Menu Page (2 hari)

**Tujuan:** Customer bisa lihat semua menu dan filter by category

**File yang dibuat:**
- `pages/menu.php` - Tampilan menu dengan filter
- `assets/css/style.css` - Styling

**Database:**
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE menus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO categories (name) VALUES 
('Kopi'), ('Teh'), ('Makanan'), ('Snack');

INSERT INTO menus (category_id, name, description, price) VALUES 
(1, 'Kopi Hitam', 'Kopi hitam original', 12000),
(1, 'Kopi Susu', 'Kopi dengan susu', 15000),
(2, 'Teh Manis', 'Teh manis dingin', 8000),
(3, 'Nasi Goreng', 'Nasi goreng special', 20000),
(4, 'Pisang Goreng', 'Pisang goreng crispy', 10000);
```

**Fitur:**
- List semua menu dari database
- Filter by category (Semua, Kopi, Teh, Makanan, Snack)
- Button "Tambah ke Keranjang" di setiap menu
- Floating cart button (kalau ada isi)

**Test:**
- [ ] Menu tampil dari database
- [ ] Filter kategori berfungsi
- [ ] Harga tampil dengan format Rupiah

**Checklist:**
- [ ] Table `categories` & `menus` created
- [ ] Sample data inserted
- [ ] Menu page tampil dengan styling
- [ ] Filter kategori works

---

### Phase 1.3: Shopping Cart (1 hari)

**Tujuan:** Customer bisa add/remove item dari keranjang

**File yang dibuat:**
- `pages/cart.php` - Halaman keranjang
- `actions/add-to-cart.php` - Proses tambah item
- `actions/remove-from-cart.php` - Proses hapus item

**Fitur:**
- Add item ke cart (simpan di SESSION)
- List item di cart dengan quantity & subtotal
- Remove item dari cart
- Calculate total harga
- Button "Lanjut ke Pembayaran"

**Session Structure:**
```php
$_SESSION['cart'] = [
    1 => 2,  // menu_id => quantity
    3 => 1,
    5 => 3
];
```

**Test:**
- [ ] Klik "Tambah ke Keranjang" → item masuk cart
- [ ] Cart page tampil item dengan benar
- [ ] Remove item works
- [ ] Total calculation correct

**Checklist:**
- [ ] Add to cart berfungsi
- [ ] Cart page tampil
- [ ] Remove item berfungsi
- [ ] Floating cart counter update

---

### Phase 1.4: Checkout & Order (1-2 hari)

**Tujuan:** Customer bisa checkout dan order tersimpan di database

**File yang dibuat:**
- `pages/checkout.php` - Form checkout
- `actions/process-checkout.php` - Proses order
- `pages/order-success.php` - Konfirmasi order

**Database:**
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    table_id INT,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'digital') DEFAULT 'cash',
    status ENUM('Pending', 'Paid', 'Complete') DEFAULT 'Pending',
    customer_name VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES tables(id)
);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    menu_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);
```

**Fitur:**
- Form: Nama customer, Catatan, Metode pembayaran
- Generate order number (ORD-20260103-1234)
- Insert ke table `orders`
- Insert items ke table `order_items`
- Clear cart setelah order berhasil
- Redirect ke success page dengan order number

**Test:**
- [ ] Form checkout tampil
- [ ] Submit order berhasil
- [ ] Data masuk database
- [ ] Cart ter-clear setelah checkout
- [ ] Success page tampil order number

**Checklist:**
- [ ] Tables `orders` & `order_items` created
- [ ] Checkout form works
- [ ] Order tersimpan ke database
- [ ] Order items tersimpan
- [ ] Cart cleared after checkout

---

## 🎯 Milestone 1 Complete! ✅

**Customer Flow Complete:**
- ✅ Scan QR → Lihat Menu → Add to Cart → Checkout → Order Success

---

## 👨‍💼 PART 2: ADMIN SIDE (5-7 hari)

### Phase 2.1: Login System (1 hari)

**Tujuan:** Admin bisa login untuk akses admin panel

**File yang dibuat:**
- `admin/login.php` - Form login
- `admin/logout.php` - Proses logout
- `admin/includes/auth-check.php` - Middleware cek login

**Database:**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password: admin123 (hashed)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

**Fitur:**
- Form login (username & password)
- Validasi credentials
- Password hashing (password_verify)
- Session untuk login state
- Redirect ke dashboard setelah login
- Logout button

**Test:**
- [ ] Login dengan admin/admin123 berhasil
- [ ] Login dengan password salah gagal
- [ ] Setelah login redirect ke dashboard
- [ ] Logout berhasil & redirect ke login

**Checklist:**
- [ ] Table `users` created
- [ ] Login form works
- [ ] Authentication works
- [ ] Session saved
- [ ] Logout works

---

### Phase 2.2: Dashboard (1 hari)

**Tujuan:** Admin lihat overview system

**File yang dibuat:**
- `admin/index.php` - Dashboard

**Fitur:**
- Total orders hari ini
- Total revenue hari ini
- Pending orders count
- Recent orders (5 terakhir)
- Navigation menu ke halaman lain

**Query untuk statistics:**
```sql
-- Total orders today
SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE();

-- Total revenue today
SELECT SUM(total_price) FROM orders 
WHERE DATE(created_at) = CURDATE() AND status != 'Cancelled';

-- Pending orders
SELECT COUNT(*) FROM orders WHERE status = 'Pending';
```

**Test:**
- [ ] Dashboard tampil statistics
- [ ] Recent orders tampil
- [ ] Navigation menu works

**Checklist:**
- [ ] Dashboard page created
- [ ] Statistics displayed
- [ ] Recent orders shown
- [ ] Navigation ready

---

### Phase 2.3: Orders Management (2 hari)

**Tujuan:** Admin bisa kelola semua pesanan

**File yang dibuat:**
- `admin/orders.php` - List & manage orders
- `actions/update-order-status.php` - Update status

**Fitur:**
- List semua orders dengan detail
- Filter by status (All, Pending, Paid, Complete)
- View order details (items, customer, table)
- Update order status
- Delete order (optional)

**Order Flow:**
```
Pending → Paid → Complete
```

**Test:**
- [ ] List orders tampil
- [ ] Filter by status works
- [ ] Order details tampil
- [ ] Update status berhasil

**Checklist:**
- [ ] Orders page created
- [ ] List orders works
- [ ] Filter implemented
- [ ] Update status works

---

### Phase 2.4: Menus Management (2 hari)

**Tujuan:** Admin bisa kelola menu (CRUD)

**File yang dibuat:**
- `admin/menus.php` - List menus
- `admin/menu-add.php` - Form tambah menu
- `admin/menu-edit.php` - Form edit menu
- `actions/menu-create.php` - Proses tambah
- `actions/menu-update.php` - Proses update
- `actions/menu-delete.php` - Proses hapus

**Fitur:**
- **Create:** Form tambah menu baru (nama, desc, price, category)
- **Read:** List semua menu
- **Update:** Edit menu existing
- **Delete:** Hapus menu (soft delete: set is_available = 0)

**Test:**
- [ ] Add menu baru berhasil
- [ ] List menu tampil
- [ ] Edit menu berhasil
- [ ] Delete menu berhasil

**Checklist:**
- [ ] Menus list page created
- [ ] Add form works
- [ ] Edit form works
- [ ] Delete works
- [ ] CRUD complete

---

### Phase 2.5: Reports (1-2 hari)

**Tujuan:** Admin bisa lihat laporan penjualan

**File yang dibuat:**
- `admin/reports.php` - Halaman laporan

**Fitur:**
- Total pendapatan (hari ini, minggu ini, bulan ini)
- Jumlah orders per periode
- Menu terlaris (top 5)
- Grafik penjualan (optional: pakai Chart.js)
- Filter by date range

**Query:**
```sql
-- Revenue by date
SELECT DATE(created_at) as date, SUM(total_price) as revenue
FROM orders 
WHERE status != 'Cancelled'
GROUP BY DATE(created_at);

-- Top selling menu
SELECT m.name, SUM(oi.quantity) as total_sold
FROM order_items oi
JOIN menus m ON oi.menu_id = m.id
GROUP BY m.id
ORDER BY total_sold DESC
LIMIT 5;
```

**Test:**
- [ ] Reports tampil dengan data
- [ ] Filter date works
- [ ] Top menu displayed

**Checklist:**
- [ ] Reports page created
- [ ] Statistics displayed
- [ ] Top menu shown
- [ ] Filter works

---

## 🎯 Milestone 2 Complete! ✅

**Admin Panel Complete:**
- ✅ Login → Dashboard → Manage Orders → Manage Menus → View Reports

---

## 📋 Complete Feature List

### Customer Side ✅
```
1. ✅ Landing Page (QR Validation)
2. ✅ Menu Page (Browse & Filter)
3. ✅ Shopping Cart
4. ✅ Checkout
5. ✅ Order Success
```

### Admin Side ✅
```
6. ✅ Login System
7. ✅ Dashboard
8. ✅ Orders Management
9. ✅ Menus Management (CRUD)
10. ✅ Reports
```

---

## 📊 Development Timeline

### Week 1: Customer Side
```
Day 1: Setup + Landing Page
Day 2-3: Menu Page
Day 4: Shopping Cart
Day 5-6: Checkout & Order
Day 7: Testing & Bug Fix
```

### Week 2: Admin Side
```
Day 8: Login System
Day 9: Dashboard
Day 10-11: Orders Management
Day 12-13: Menus CRUD
Day 14: Reports
```

### Week 3: Polish
```
Day 15-16: UI/UX Improvement
Day 17-18: Testing End-to-End
Day 19: Bug Fixing
Day 20: Documentation
Day 21: Final Review
```

---

## 🎯 Priority Order (Kalau Waktu Terbatas)

### Must Have (Core Features):
1. ✅ Menu Page (Customer lihat menu)
2. ✅ Cart & Checkout (Customer order)
3. ✅ Orders Management (Admin kelola order)

### Should Have:
4. ✅ Login System (Security)
5. ✅ Dashboard (Overview)
6. ✅ Menus CRUD (Admin kelola menu)

### Nice to Have:
7. ⚠️ Reports (Analytics)
8. ⚠️ Advanced filtering
9. ⚠️ Payment gateway integration
10. ⚠️ Real-time notifications

---

## 📝 Quick Start Checklist

### Persiapan:
- [ ] PHP & MySQL installed
- [ ] Project folder created
- [ ] Database `warkop_qr` created
- [ ] Basic files: config.php, db.php, functions.php

### Customer Side (Week 1):
- [ ] Phase 1.1: Landing Page
- [ ] Phase 1.2: Menu Page
- [ ] Phase 1.3: Shopping Cart
- [ ] Phase 1.4: Checkout

### Admin Side (Week 2):
- [ ] Phase 2.1: Login
- [ ] Phase 2.2: Dashboard
- [ ] Phase 2.3: Orders Management
- [ ] Phase 2.4: Menus CRUD
- [ ] Phase 2.5: Reports

### Testing (Week 3):
- [ ] End-to-end testing
- [ ] Bug fixing
- [ ] UI polish
- [ ] Documentation

---

## 💡 Tips Development

### 1. Build Incremental
```
❌ Jangan: Buat semua halaman sekaligus
✅ Lakukan: 1 halaman complete → test → next
```

### 2. Test After Each Phase
```
Setiap selesai 1 phase:
1. Test functionality
2. Fix bugs
3. Commit to git
4. Baru lanjut phase berikutnya
```

### 3. Use Git
```bash
git init
git add .
git commit -m "Phase 1.1: Landing page complete"
```

### 4. Keep It Simple First
```
Week 1-2: Functionality > Beauty
Week 3: Polish UI/UX
```

### 5. Debug Systematically
```
Error? 
→ Read error message
→ Check basics (file, variable, syntax)
→ Google it
→ Fix
→ Test again
```

---

## 🚀 Start Now!

**Your First 3 Steps:**
```bash
# 1. Start server
cd /Users/fajryariansyah/Projects/warkop-qr
php -S localhost:8000

# 2. Create database
mysql -u root -p
CREATE DATABASE warkop_qr;
USE warkop_qr;

# 3. Start with Phase 1.1 (Landing Page)
# Follow the roadmap step by step!
```

**Good luck! 🎉**

---

**Remember:**
- 📖 Baca konsep dulu (BELAJAR_KONSEP_PHP_NATIVE.md)
- 🎯 Focus satu phase sampai selesai
- 🧪 Test setelah setiap phase
- 💪 Jangan menyerah kalau stuck!
