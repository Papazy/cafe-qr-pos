# 📚 Panduan Lengkap PHP Native untuk Sistem Warkop QR

## 🎯 Pengantar

Anda diminta membuat sistem ini dengan **PHP Native** (tanpa framework). Artinya, Anda akan membangun semuanya dari nol menggunakan PHP murni tanpa bantuan framework seperti Laravel, CodeIgniter, atau Symfony.

**Keuntungan PHP Native:**
- Lebih ringan dan cepat
- Kontrol penuh atas kode
- Tidak ada overhead framework
- Bagus untuk belajar fundamental

**Tantangannya:**
- Harus bikin struktur sendiri
- Security harus handle manual
- Tidak ada helper/library bawaan

---

## 📖 Konsep Dasar yang Harus Dipahami

### 1. Struktur Folder PHP Native

```
warkop-qr/
├── public/              # Folder yang accessible dari browser
│   ├── index.php       # Entry point aplikasi
│   ├── css/           
│   ├── js/            
│   └── images/        
├── includes/           # File-file yang di-include
│   ├── config.php     # Konfigurasi
│   ├── functions.php  # Helper functions
│   └── db.php         # Database connection
├── pages/              # File halaman
│   ├── menu.php
│   ├── checkout.php
│   └── order.php
└── admin/              # Admin panel
    ├── index.php
    ├── orders.php
    └── login.php
```

### 2. Cara Kerja PHP Native

**Request Flow:**
```
Browser Request → index.php → Include files → Process → Output HTML
```

**Contoh:**
```php
// index.php
<?php
// 1. Include konfigurasi
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// 2. Proses request
$page = $_GET['page'] ?? 'home';

// 3. Load halaman yang diminta
switch($page) {
    case 'menu':
        include 'pages/menu.php';
        break;
    case 'cart':
        include 'pages/cart.php';
        break;
    default:
        include 'pages/home.php';
}
?>
```

### 3. Konsep Database Connection (MySQLi)

**File: includes/db.php**
```php
<?php
// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'warkop_qr');

// Buat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");
?>
```

**Cara pakai:**
```php
<?php
require_once 'includes/db.php';

// Query data
$query = "SELECT * FROM menus WHERE is_available = 1";
$result = mysqli_query($conn, $query);

// Loop hasil
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'];
}
?>
```

### 4. Konsep Session (untuk Cart & Login)

**Start session:**
```php
<?php
session_start(); // HARUS di awal file sebelum output apapun
?>
```

**Set session:**
```php
<?php
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['cart'] = [];
?>
```

**Get session:**
```php
<?php
if (isset($_SESSION['user_id'])) {
    echo "User sudah login";
}
?>
```

**Destroy session (logout):**
```php
<?php
session_start();
session_destroy();
header('Location: login.php');
?>
```

### 5. Konsep GET & POST

**GET** - untuk mengambil data
```php
// URL: menu.php?table=5&token=abc123
<?php
$table = $_GET['table'];      // 5
$token = $_GET['token'];      // abc123
?>
```

**POST** - untuk kirim data (form)
```php
<!-- Form HTML -->
<form method="POST" action="process.php">
    <input type="text" name="nama">
    <button type="submit">Submit</button>
</form>

<!-- process.php -->
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    echo "Halo, $nama";
}
?>
```

### 6. Konsep Include & Require

**Include** - masukkan file lain
```php
<?php
include 'header.php';  // kalau error, lanjut
require 'config.php';  // kalau error, stop
?>
```

**Include Once** - cuma include sekali
```php
<?php
require_once 'db.php';  // cuma load sekali meski dipanggil berkali-kali
?>
```

---

## 🛠️ Implementasi Step-by-Step

### STEP 1: Setup Database

**1.1 Buat Database**
```sql
CREATE DATABASE warkop_qr;
USE warkop_qr;
```

**1.2 Buat Tabel**
```sql
-- Tabel Meja
CREATE TABLE tables (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_number INT NOT NULL,
    qr_token VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('available', 'occupied') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kategori Menu
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Tabel Menu
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

-- Tabel Orders
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'digital') DEFAULT 'cash',
    status ENUM('Pending', 'Paid', 'Complete', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES tables(id)
);

-- Tabel Order Items
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    menu_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (menu_id) REFERENCES menus(id)
);

-- Tabel Users (Admin)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO categories (name) VALUES 
('Kopi'), ('Teh'), ('Makanan'), ('Snack');

INSERT INTO tables (table_number, qr_token) VALUES 
(1, 'TOKEN_MEJA_1_ABC123'),
(2, 'TOKEN_MEJA_2_DEF456'),
(3, 'TOKEN_MEJA_3_GHI789');

INSERT INTO menus (category_id, name, description, price) VALUES 
(1, 'Kopi Hitam', 'Kopi hitam original', 12000),
(1, 'Kopi Susu', 'Kopi dengan susu', 15000),
(2, 'Teh Manis', 'Teh manis dingin', 8000),
(3, 'Nasi Goreng', 'Nasi goreng special', 20000);

-- Insert admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

---

### STEP 2: Setup Koneksi Database

**File: includes/db.php**
```php
<?php
// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'warkop_qr');

// Membuat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($conn, "utf8");

// Optional: Set timezone
date_default_timezone_set('Asia/Jakarta');
?>
```

---

### STEP 3: Buat Helper Functions

**File: includes/functions.php**
```php
<?php
/**
 * Helper Functions untuk Warkop QR
 */

// Sanitize input untuk mencegah XSS
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Redirect ke halaman lain
function redirect($url) {
    header("Location: $url");
    exit();
}

// Cek apakah user sudah login
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Cek apakah user adalah admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Generate order number
function generate_order_number() {
    return 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
}

// Format harga ke Rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Validasi token QR
function validate_qr_token($conn, $token) {
    $token = mysqli_real_escape_string($conn, $token);
    $query = "SELECT * FROM tables WHERE qr_token = '$token'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

// Get cart dari session
function get_cart() {
    return $_SESSION['cart'] ?? [];
}

// Add item ke cart
function add_to_cart($menu_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$menu_id])) {
        $_SESSION['cart'][$menu_id] += $quantity;
    } else {
        $_SESSION['cart'][$menu_id] = $quantity;
    }
}

// Remove item dari cart
function remove_from_cart($menu_id) {
    if (isset($_SESSION['cart'][$menu_id])) {
        unset($_SESSION['cart'][$menu_id]);
    }
}

// Clear cart
function clear_cart() {
    unset($_SESSION['cart']);
}

// Hitung total cart
function calculate_cart_total($conn) {
    $cart = get_cart();
    $total = 0;
    
    foreach ($cart as $menu_id => $quantity) {
        $query = "SELECT price FROM menus WHERE id = $menu_id";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $total += $row['price'] * $quantity;
        }
    }
    
    return $total;
}

// Show alert message
function set_flash_message($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}
?>
```

---

### STEP 4: Buat File Config

**File: includes/config.php**
```php
<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Base URL
define('BASE_URL', 'http://localhost/warkop-qr/public/');

// Upload path
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');

// Error reporting (development mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Jakarta');
?>
```

---

### STEP 5: Buat Entry Point

**File: public/index.php**
```php
<?php
// Include semua file yang diperlukan
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Get action dari URL
$page = $_GET['page'] ?? 'home';
$table = $_GET['table'] ?? null;
$token = $_GET['token'] ?? null;

// Validasi token jika ada
if ($table && $token) {
    $table_data = validate_qr_token($conn, $token);
    if (!$table_data) {
        die("Token tidak valid!");
    }
    $_SESSION['table_id'] = $table_data['id'];
    $_SESSION['table_number'] = $table_data['table_number'];
}

// Routing sederhana
switch($page) {
    case 'menu':
        include '../pages/menu.php';
        break;
    
    case 'cart':
        include '../pages/cart.php';
        break;
    
    case 'checkout':
        include '../pages/checkout.php';
        break;
    
    case 'order-success':
        include '../pages/order-success.php';
        break;
    
    default:
        include '../pages/home.php';
        break;
}
?>
```

---

### STEP 6: Buat Halaman Menu

**File: pages/menu.php**
```php
<?php
// Cek apakah ada table_id di session
if (!isset($_SESSION['table_id'])) {
    die("Silakan scan QR Code terlebih dahulu!");
}

$table_number = $_SESSION['table_number'];

// Get kategori
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Get filter kategori
$selected_category = $_GET['category'] ?? 'all';

// Get menu
if ($selected_category == 'all') {
    $menus_query = "SELECT m.*, c.name as category_name 
                    FROM menus m 
                    JOIN categories c ON m.category_id = c.id 
                    WHERE m.is_available = 1 
                    ORDER BY c.name, m.name";
} else {
    $category_id = (int)$selected_category;
    $menus_query = "SELECT m.*, c.name as category_name 
                    FROM menus m 
                    JOIN categories c ON m.category_id = c.id 
                    WHERE m.is_available = 1 AND m.category_id = $category_id
                    ORDER BY m.name";
}
$menus_result = mysqli_query($conn, $menus_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Warkop QR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 15px; text-align: center; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .category-filter { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; }
        .category-btn { padding: 10px 20px; background: white; border: 2px solid #ddd; 
                        border-radius: 20px; cursor: pointer; white-space: nowrap; }
        .category-btn.active { background: #3498db; color: white; border-color: #3498db; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
                     gap: 20px; }
        .menu-card { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .menu-card h3 { color: #2c3e50; margin-bottom: 5px; }
        .menu-card .category { color: #7f8c8d; font-size: 12px; margin-bottom: 10px; }
        .menu-card .price { color: #27ae60; font-weight: bold; font-size: 18px; margin: 10px 0; }
        .menu-card button { width: 100%; padding: 10px; background: #3498db; color: white; 
                           border: none; border-radius: 5px; cursor: pointer; }
        .menu-card button:hover { background: #2980b9; }
        .cart-float { position: fixed; bottom: 20px; right: 20px; background: #e74c3c; 
                     color: white; padding: 15px 25px; border-radius: 50px; 
                     text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Menu Digital</h1>
        <p>Meja <?= $table_number ?></p>
    </div>

    <div class="container">
        <!-- Filter Kategori -->
        <div class="category-filter">
            <a href="?page=menu&category=all" class="category-btn <?= $selected_category == 'all' ? 'active' : '' ?>">
                Semua
            </a>
            <?php while($cat = mysqli_fetch_assoc($categories_result)): ?>
                <a href="?page=menu&category=<?= $cat['id'] ?>" 
                   class="category-btn <?= $selected_category == $cat['id'] ? 'active' : '' ?>">
                    <?= $cat['name'] ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Grid Menu -->
        <div class="menu-grid">
            <?php while($menu = mysqli_fetch_assoc($menus_result)): ?>
                <div class="menu-card">
                    <div class="category"><?= $menu['category_name'] ?></div>
                    <h3><?= $menu['name'] ?></h3>
                    <p><?= $menu['description'] ?></p>
                    <div class="price"><?= format_rupiah($menu['price']) ?></div>
                    <form method="POST" action="actions/add-to-cart.php">
                        <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
                        <button type="submit">+ Tambah ke Keranjang</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Floating Cart Button -->
    <?php $cart_count = count(get_cart()); ?>
    <?php if($cart_count > 0): ?>
        <a href="?page=cart" class="cart-float">
            🛒 Keranjang (<?= $cart_count ?>)
        </a>
    <?php endif; ?>
</body>
</html>
```

---

### STEP 7: Buat Action Add to Cart

**File: public/actions/add-to-cart.php**
```php
<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = (int)$_POST['menu_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Add to cart
    add_to_cart($menu_id, $quantity);
    
    // Set flash message
    set_flash_message('Item berhasil ditambahkan ke keranjang!', 'success');
    
    // Redirect back
    redirect('../index.php?page=menu');
} else {
    redirect('../index.php');
}
?>
```

---

### STEP 8: Buat Halaman Cart

**File: pages/cart.php**
```php
<?php
if (!isset($_SESSION['table_id'])) {
    die("Silakan scan QR Code terlebih dahulu!");
}

$cart = get_cart();
if (empty($cart)) {
    echo "<p>Keranjang kosong. <a href='?page=menu'>Kembali ke menu</a></p>";
    exit;
}

// Get menu details untuk items di cart
$menu_ids = implode(',', array_keys($cart));
$query = "SELECT * FROM menus WHERE id IN ($menu_ids)";
$result = mysqli_query($conn, $query);

$cart_items = [];
$total = 0;

while ($menu = mysqli_fetch_assoc($result)) {
    $quantity = $cart[$menu['id']];
    $subtotal = $menu['price'] * $quantity;
    $total += $subtotal;
    
    $cart_items[] = [
        'menu' => $menu,
        'quantity' => $quantity,
        'subtotal' => $subtotal
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Warkop QR</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 15px; text-align: center; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .cart-item { background: white; padding: 15px; margin-bottom: 10px; 
                     border-radius: 8px; display: flex; justify-content: space-between; 
                     align-items: center; }
        .item-info { flex: 1; }
        .item-actions { display: flex; gap: 10px; align-items: center; }
        .total-section { background: white; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .total-price { font-size: 24px; font-weight: bold; color: #27ae60; }
        .checkout-btn { display: block; width: 100%; padding: 15px; background: #27ae60; 
                       color: white; text-align: center; border-radius: 8px; 
                       text-decoration: none; margin-top: 15px; font-weight: bold; }
        .remove-btn { background: #e74c3c; color: white; padding: 5px 10px; 
                     border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Keranjang Belanja</h1>
        <p>Meja <?= $_SESSION['table_number'] ?></p>
    </div>

    <div class="container">
        <?php foreach($cart_items as $item): ?>
            <div class="cart-item">
                <div class="item-info">
                    <h3><?= $item['menu']['name'] ?></h3>
                    <p><?= format_rupiah($item['menu']['price']) ?> x <?= $item['quantity'] ?></p>
                </div>
                <div class="item-actions">
                    <strong><?= format_rupiah($item['subtotal']) ?></strong>
                    <a href="actions/remove-from-cart.php?menu_id=<?= $item['menu']['id'] ?>" 
                       class="remove-btn">Hapus</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="total-section">
            <h2>Total Pembayaran</h2>
            <div class="total-price"><?= format_rupiah($total) ?></div>
            <a href="?page=checkout" class="checkout-btn">Lanjut ke Pembayaran</a>
            <a href="?page=menu" style="display: block; text-align: center; margin-top: 10px;">
                ← Tambah Item Lagi
            </a>
        </div>
    </div>
</body>
</html>
```

---

## 📝 Konsep-Konsep Penting

### 1. **Prepared Statements** (untuk Security)
```php
// BAD - rentan SQL Injection
$query = "SELECT * FROM users WHERE username = '$username'";

// GOOD - pakai prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
```

### 2. **Password Hashing**
```php
// Saat register
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Saat login
if (password_verify($_POST['password'], $hashed_password_from_db)) {
    // Login berhasil
}
```

### 3. **AJAX untuk Dynamic Content**
```javascript
// Tambah ke cart tanpa reload
function addToCart(menuId) {
    fetch('actions/add-to-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'menu_id=' + menuId
    })
    .then(response => response.json())
    .then(data => {
        alert('Berhasil ditambahkan!');
        updateCartCount();
    });
}
```

---

## 🎓 Latihan & Best Practices

### Latihan 1: Buat Halaman Checkout
- Form pilih metode pembayaran
- Simpan order ke database
- Redirect ke halaman success

### Latihan 2: Buat Admin Dashboard
- Login system
- List semua orders
- Update status order

### Latihan 3: Tambah Fitur Search Menu
- Input search
- Query dengan LIKE
- Tampilkan hasil

### Best Practices:
1. **Selalu sanitize input** - gunakan `mysqli_real_escape_string()` atau prepared statements
2. **Gunakan HTTPS** di production
3. **Validasi di server side** - jangan percaya input dari client
4. **Error handling** yang baik
5. **Pisahkan logic dan view** - jangan campur PHP logic di HTML
6. **Comment code** untuk maintainability

---

## 📚 Resources untuk Belajar

1. **PHP Manual**: php.net/manual
2. **W3Schools PHP**: w3schools.com/php
3. **PHP The Right Way**: phptherightway.com
4. **MDN Web Docs**: developer.mozilla.org

---

## 🚀 Next Steps

1. ✅ Pahami konsep dasar di atas
2. ✅ Setup database
3. ✅ Buat koneksi database
4. ✅ Implementasi helper functions
5. ⬜ Buat halaman menu (sudah ada contoh)
6. ⬜ Buat fitur cart
7. ⬜ Buat checkout & payment
8. ⬜ Buat admin panel
9. ⬜ Testing & debugging
10. ⬜ Deploy

**Ingat**: PHP Native itu learning by doing. Jangan takut error, debug pelan-pelan! 💪

---

**Dibuat pada**: 25 Desember 2025
