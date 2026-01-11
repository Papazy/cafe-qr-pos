# 🗺️ Roadmap: Cara Menjalani Project Warkop QR

## 🎯 Overview

Project ini akan dibangun secara **incremental** - mulai dari yang paling simple, test, baru lanjut ke yang lebih kompleks. Jangan coba build semuanya sekaligus!

**Estimasi waktu total:** 2-3 minggu (1-2 jam per hari)

---

## 📋 Phase 0: Persiapan (1 hari)

### 🎯 Pilih Setup Method:

**Opsi 1: PHP Built-in Server** ⭐ **RECOMMENDED!** (Termudah & Tercepat)
- ✅ Setup cuma 5 menit
- ✅ 1 command langsung jalan: `php -S localhost:8000`
- ✅ No configuration needed
- ✅ Perfect untuk development

**Opsi 2: Apache + PHP** (Traditional)
- ⚠️ Setup lebih kompleks
- ⚠️ Perlu edit config file
- ✅ Production-like environment
- ✅ Support .htaccess

**💡 Saran:** Pakai **Opsi 1** untuk development, nanti bisa deploy ke Apache di production.

---

## Setup Opsi 1: PHP Built-in Server (5 MENIT!) ⭐

### ✅ Checklist:

**1. Cek PHP tersedia:**
```bash
php -v
# Harus muncul: PHP 8.x.x atau PHP 7.x.x
```

**2. Install MySQL:**
```bash
brew install mysql
brew services start mysql
mysql_secure_installation
```

**3. Setup Project:**
```bash
cd /Users/fajryariansyah/Projects/warkop-qr
mkdir -p includes pages actions admin assets/css assets/js assets/images uploads database admin/includes
```

**4. Start Server:**
```bash
php -S localhost:8000
```

**5. Test:**
- Buka: http://localhost:8000
- Done! ✅

**📚 Baca detail:** [SETUP_PHP_BUILTIN_SERVER.md](SETUP_PHP_BUILTIN_SERVER.md)

---

## Setup Opsi 2: Apache + PHP (Traditional)

### ✅ Checklist:

#### A. Setup PHP & Apache (Built-in macOS)

**1. Cek PHP sudah tersedia:**
```bash
php -v
# Harus muncul: PHP 8.x.x atau PHP 7.x.x
```

**2. Start Apache Web Server:**
```bash
sudo apachectl start
```

**3. Test Apache jalan:**
- Buka browser: http://localhost
- Harus muncul: "It works!" atau halaman Apache default

**4. Cari document root:**
```bash
# Document root default macOS ada di:
ls /Library/WebServer/Documents
```

**5. Enable PHP di Apache:**
```bash
# Edit Apache config
sudo nano /etc/apache2/httpd.conf

# Cari line ini (tekan Ctrl+W untuk search):
#LoadModule php_module libexec/apache2/libphp.so

# Hapus tanda # di depannya jadi:
LoadModule php_module libexec/apache2/libphp.so

# Save: Ctrl+O, Enter, Exit: Ctrl+X

# Restart Apache
sudo apachectl restart
```

**6. Test PHP jalan:**
```bash
# Buat file test PHP
echo "<?php phpinfo(); ?>" | sudo tee /Library/WebServer/Documents/test.php

# Buka browser: http://localhost/test.php
# Harus muncul halaman PHP info
```

#### B. Install MySQL

**Opsi 1: Pakai Homebrew (Recommended)**
```bash
# Install Homebrew kalau belum ada
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install MySQL
brew install mysql

# Start MySQL
brew services start mysql

# Secure installation
mysql_secure_installation
# - Set root password (misal: root)
# - Remove anonymous users: Y
# - Disallow root login remotely: Y
# - Remove test database: Y
# - Reload privilege tables: Y
```

**Opsi 2: Download MySQL Community**
- Download dari: https://dev.mysql.com/downloads/mysql/
- Install .dmg file
- Start MySQL dari System Preferences

**7. Test MySQL:**
```bash
mysql -u root -p
# Masukkan password yang tadi dibuat

# Kalau berhasil masuk, keluar dengan:
exit
```

#### C. Install phpMyAdmin (Optional, tapi recommended)

```bash
# Download phpMyAdmin
cd ~/Downloads
curl -O https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-all-languages.zip

# Extract
unzip phpMyAdmin-5.2.1-all-languages.zip

# Pindah ke document root
sudo mv phpMyAdmin-5.2.1-all-languages /Library/WebServer/Documents/phpmyadmin

# Buka browser: http://localhost/phpmyadmin
# Login dengan: root / password_yang_tadi_dibuat
```

#### D. Setup Project Folder

**Opsi 1: Di Document Root (Simple)**
```bash
cd /Library/WebServer/Documents
sudo mkdir warkop-qr
sudo chown $USER warkop-qr
cd warkop-qr
```

**Opsi 2: Di Home Directory (Lebih Flexible)**
```bash
# Buat folder Sites di home
mkdir ~/Sites
cd ~/Sites
mkdir warkop-qr
cd warkop-qr

# Enable user sites di Apache
sudo nano /etc/apache2/httpd.conf
# Uncomment (hapus #) line ini:
# LoadModule userdir_module libexec/apache2/mod_userdir.so

# Uncomment line ini juga:
# Include /private/etc/apache2/extra/httpd-userdir.conf

# Save dan restart Apache
sudo apachectl restart

# Akses di browser: http://localhost/~yourusername/warkop-qr
```

**Checklist Setup:**
- [ ] PHP version check: `php -v`
- [ ] Apache running: `sudo apachectl status`
- [ ] PHP enabled di Apache (test.php works)
- [ ] MySQL installed & running
- [ ] phpMyAdmin accessible (optional)
- [ ] Project folder created

#### E. Install Text Editor
- [ ] Install VS Code (https://code.visualstudio.com)
- [ ] Install extension: PHP Intelephense
- [ ] Install extension: PHP Debug (optional)

#### F. Baca Panduan
- [ ] BELAJAR_KONSEP_PHP_NATIVE.md (pahami konsep)
- [ ] STRUKTUR_FOLDER_PHP_NATIVE.md (pahami struktur)
- [ ] PANDUAN_PHP_NATIVE.md (contoh code)

### 🎯 Goal:
Development environment siap dipakai dengan PHP + Apache + MySQL di macOS.

### 💡 Tips:
- Jangan skip phase ini! Foundation yang kuat penting.
- Kalau belum paham konsep, baca lagi sampai paham.
- Save password MySQL, nanti dipakai untuk config database.
- Kalau error permission, pakai `sudo` untuk command yang butuh admin access.

---

## 📋 Phase 1: Setup Project & Database (1-2 hari)

### Step 1.1: Buat Struktur Folder

```bash
# Di terminal, masuk ke folder project yang sudah dibuat di Phase 0
# Opsi 1: Jika di Document Root
cd /Library/WebServer/Documents/warkop-qr

# Opsi 2: Jika di ~/Sites
cd ~/Sites/warkop-qr

# Buat struktur folder
mkdir includes pages actions admin assets uploads database
mkdir assets/css assets/js assets/images
mkdir admin/includes

# Verify struktur
ls -la
```

**Manual checklist:**
- [ ] Folder `includes/` ada
- [ ] Folder `pages/` ada
- [ ] Folder `actions/` ada
- [ ] Folder `admin/` ada
- [ ] Folder `assets/css/`, `assets/js/`, `assets/images/` ada
- [ ] Folder `uploads/` ada
- [ ] Folder `database/` ada

### Step 1.2: Buat Database
Opsi 1: Pakai phpMyAdmin** (http://localhost/phpmyadmin)
- Login dengan: root / password_anda
- Klik "New" untuk buat database
- Nama database: `warkop_qr`
- Collation: `utf8mb4_general_ci`

**Opsi 2: Pakai Terminal (Command Line)**
```bash
# Login ke MySQL
mysql -u root -p
# Masukkan password

# Buat databaseuat database baru:**
```sql
CREATE DATABASE warkop_qr;
USE warkop_qr;
```

**3. Copy SQL dari PANDUAN_PHP_NATIVE.md (STEP 1):**
- [ ] Buat semua table (tables, categories, menus, orders, order_items, users)
- [ ] Insert sample data
- [ ] Verify data masuk (klik table, lihat data)

**Test:**
```sql
SELECT * FROM menus;  -- Harus ada 4 rows
SELECT * FROM tables;  -- Harus ada 3 rows
```

### Step 1.3: Buat File Konfigurasi

**File: includes/config dengan setup Anda!
// Opsi 1: Jika di Document Root
define('BASE_URL', 'http://localhost/warkop-qr/');

// Opsi 2: Jika di ~/Sites
// define('BASE_URL', 'http://localhost/~yourusername/warkop-qr/');

<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Development mode
error_reporting(E_Aroot');  // GANTI dengan password MySQL Anda!
define('DB_NAME', 'warkop_qr');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
```

**⚠️ PENTING:** Ganti `'root'` di `DB_PASS` dengan password MySQL yang Anda buat saat setup!hp
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Kosong untuk XAMPP default
define('DB_NAME', 'warkop_qr');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
```

**File: includes/functions.php**
```php
<?php
function format_rupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_qr_token($conn, $token) {
    $token = mysqli_real_escape_string($conn, $token);
    $query = "SELECT * FROM tables WHERE qr_token = '$token'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

function get_cart() {
    return $_SESSION['cart'] ?? [];
}

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

function remove_from_cart($menu_id) {
    if (isset($_SESSION['cart'][$menu_id])) {
        unset($_SESSION['cart'][$menu_id]);
    }
}

function clear_cart() {
    unset($_SESSION['cart']);
}

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
?>
```

**Checklist:**
- [ ] 3 file includes sudah dibuat
- [ ] Test koneksi database (lihat Step 1.4)

### Step 1.4: Test Koneksi Database

**Buat file: test-db.php (di root folder)**
```php
<?php
require_once 'includes/db.php';

$query = "SELECT * FROM menus";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "<h1>Database Connected!</h1>";
    echo "<h2>Menu List:</h2><ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>{$row['name']} - Rp {$row['price']}</li>";
    }
    echo "</ul>";
} else {
    echo "Query Error: " . mysqli_error($conn);
}
?>
```

**Test:** 
```bash
# Start server dulu (kalau pakai PHP built-in)
php -S localhost:8000

# Buka browser
open http://localhost:8000/test-db.php
```

- [ ] Halaman muncul tanpa error
- [ ] Ada list menu
- [ ] Kalau sudah OK, delete file test-db.php

### 🎯 Goal Phase 1:
- ✅ Struktur folder sesuai best practice
- ✅ Database siap dengan sample data
- ✅ Koneksi database berhasil
- ✅ Helper functions tersedia

---

## 📋 Phase 2: Customer Flow - View Menu (2-3 hari)

### Step 2.1: Landing Page

**File: index.php** (di root folder)
```php
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Jika ada table & token, redirect ke menu
if (isset($_GET['table']) && isset($_GET['token'])) {
    $table_data = validate_qr_token($conn, $_GET['token']);
    if ($table_data) {
        $_SESSION['table_id'] = $table_data['id'];
        $_SESSION['table_number'] = $table_data['table_number'];
        redirect('pages/menu.php');
    } else {
        $error = "QR Code tidak valid!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warkop QR - Selamat Datang</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 400px;
        }
        h1 { color: #333; margin-bottom: 20px; }
        p { color: #666; margin-bottom: 30px; }
        .qr-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .error { color: #e74c3c; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍵 Warkop QR</h1>
        <p>Sistem Pemesanan Digital</p>
        
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        
        <div class="qr-info">
            <p>Silakan scan QR Code yang tersedia di meja Anda untuk mulai memesan.</p>
            <small style="color: #999;">Atau klik link yang ada di QR Code</small>
        </div>
        
        <!-- Test link (nanti dihapus di production) -->
        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px;">
            <p style="font-size: 12px; color: #856404; margin-bottom: 10px;">Test Links:</p>
            <a href="?table=1&token=TOKEN_MEJA_1_ABC123" style="display: block; margin: 5px 0; color: #667eea;">Meja 1</a>
            <a href="?table=2&token=TOKEN_MEJA_2_DEF456" style="display: block; margin: 5px 0; color: #667eea;">Meja 2</a>
            <a href="?table=3&token=TOKEN_MEJA_3_GHI789" style="display: block; margin: 5px 0; color: #667eea;">Meja 3</a>
        </div>
    </div>
</body>
</html>
```

**Test:** 
```bash
# Start server
php -S localhost:8000

# Buka browser
open http://localhost:8000/
```
- [ ] Landing page muncul
- [ ] Klik test link Meja 1
- [ ] Redirect ke pages/menu.php (masih 404, normal)

### Step 2.2: Halaman Menu

**File: pages/menu.php**

Copy dari PANDUAN_PHP_NATIVE.md STEP 6, tapi saya sederhanakan:

```php
<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Cek apakah sudah scan QR
if (!isset($_SESSION['table_id'])) {
    redirect('../index.php');
}

$table_number = $_SESSION['table_number'];

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_result = mysqli_query($conn, $categories_query);

// Get selected category
$selected_category = $_GET['category'] ?? 'all';

// Get menus
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

// Cart count
$cart_count = count(get_cart());
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Warkop QR</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>🍵 Menu Digital</h1>
        <p>Meja <?= $table_number ?></p>
    </div>

    <div class="container">
        <!-- Filter Kategori -->
        <div class="category-filter">
            <a href="?category=all" class="category-btn <?= $selected_category == 'all' ? 'active' : '' ?>">
                Semua
            </a>
            <?php 
            mysqli_data_seek($categories_result, 0); // Reset pointer
            while($cat = mysqli_fetch_assoc($categories_result)): 
            ?>
                <a href="?category=<?= $cat['id'] ?>" 
                   class="category-btn <?= $selected_category == $cat['id'] ? 'active' : '' ?>">
                    <?= $cat['name'] ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Grid Menu -->
        <div class="menu-grid">
            <?php if (mysqli_num_rows($menus_result) == 0): ?>
                <p>Tidak ada menu tersedia.</p>
            <?php else: ?>
                <?php while($menu = mysqli_fetch_assoc($menus_result)): ?>
                    <div class="menu-card">
                        <div class="category"><?= $menu['category_name'] ?></div>
                        <h3><?= $menu['name'] ?></h3>
                        <p><?= $menu['description'] ?></p>
                        <div class="price"><?= format_rupiah($menu['price']) ?></div>
                        <form method="POST" action="../actions/add-to-cart.php">
                            <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
                            <button type="submit">+ Tambah ke Keranjang</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Cart Button -->
    <?php if($cart_count > 0): ?>
        <a href="cart.php" class="cart-float">
            🛒 Keranjang (<?= $cart_count ?>)
        </a>
    <?php endif; ?>
</body>
</html>
```

### Step 2.3: CSS Styling

**File: assets/css/style.css**
```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f5f5;
}

.header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header h1 {
    margin-bottom: 5px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.category-filter {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.category-btn {
    padding: 10px 20px;
    background: white;
    border: 2px solid #ddd;
    border-radius: 20px;
    text-decoration: none;
    color: #333;
    white-space: nowrap;
    transition: all 0.3s;
}

.category-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.category-btn.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 80px;
}

.menu-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.menu-card .category {
    color: #999;
    font-size: 12px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.menu-card h3 {
    color: #333;
    margin-bottom: 10px;
}

.menu-card p {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
    min-height: 40px;
}

.menu-card .price {
    color: #27ae60;
    font-weight: bold;
    font-size: 20px;
    margin-bottom: 15px;
}

.menu-card button {
    width: 100%;
    padding: 12px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background 0.3s;
}

.menu-card button:hover {
    background: #5568d3;
}

.cart-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #e74c3c;
    color: white;
    padding: 15px 25px;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
    font-weight: bold;
    z-index: 1000;
    transition: transform 0.3s;
}

.cart-float:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .menu-grid {
        grid-template-columns: 1fr;
    }
    
```bash
# Pastikan server jalan
php -S localhost:8000
```
- [ ] Buka http://localhost:8000
        justify-content: flex-start;
    }
}
```

**Test Menu Page:**
- [ ] Buka http://localhost/warkop-qr/ → klik test link
- [ ] Halaman menu muncul dengan styling
- [ ] Filter kategori berfungsi
- [ ] Menu tampil dalam grid

### 🎯 Goal Phase 2:
- ✅ Landing page berfungsi
- ✅ QR validation berfungsi
- ✅ Halaman menu tampil dengan data dari database
- ✅ Filter kategori berfungsi
- ✅ Styling responsive

---

## 📋 Phase 3: Shopping Cart (2 hari)

### Step 3.1: Add to Cart Action

**File: actions/add-to-cart.php**
```php
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
    
    if ($menu_id > 0) {
        add_to_cart($menu_id, 1);
        $_SESSION['success_message'] = "Item berhasil ditambahkan ke keranjang!";
    }
    
    redirect('../pages/menu.php');
} else {
    redirect('../pages/menu.php');
}
?>
```

**Test:**
- [ ] Klik "Tambah ke Keranjang" di menu
- [ ] Counter keranjang muncul (floating button)

### Step 3.2: Cart Page

**File: pages/cart.php**
```php
<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['table_id'])) {
    redirect('../index.php');
}

$cart = get_cart();

if (empty($cart)) {
    // Keranjang kosong
    $cart_items = [];
    $total = 0;
} else {
    // Get menu details
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
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Warkop QR</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>🛒 Keranjang Belanja</h1>
        <p>Meja <?= $_SESSION['table_number'] ?></p>
    </div>

    <div class="container">
        <?php if (empty($cart_items)): ?>
            <div style="text-align: center; padding: 50px;">
                <h2>Keranjang Kosong</h2>
                <p>Belum ada item di keranjang.</p>
                <a href="menu.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;">
                    Lihat Menu
                </a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-info">
                            <h3><?= $item['menu']['name'] ?></h3>
                            <p><?= format_rupiah($item['menu']['price']) ?> x <?= $item['quantity'] ?></p>
                        </div>
                        <div class="item-actions">
                            <strong><?= format_rupiah($item['subtotal']) ?></strong>
                            <a href="../actions/remove-from-cart.php?menu_id=<?= $item['menu']['id'] ?>" 
                               class="remove-btn"
                               onclick="return confirm('Hapus item ini dari keranjang?')">
                                Hapus
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-section">
                <h2>Total Pembayaran</h2>
                <div class="total-price"><?= format_rupiah($total) ?></div>
                <a href="checkout.php" class="checkout-btn">Lanjut ke Pembayaran</a>
                <a href="menu.php" style="display: block; text-align: center; margin-top: 10px; color: #667eea; text-decoration: none;">
                    ← Tambah Item Lagi
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
```

### Step 3.3: Remove from Cart Action

**File: actions/remove-from-cart.php**
```php
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isset($_GET['menu_id'])) {
    $menu_id = (int)$_GET['menu_id'];
    remove_from_cart($menu_id);
    $_SESSION['success_message'] = "Item berhasil dihapus dari keranjang!";
}

redirect('../pages/cart.php');
?>
```

### Step 3.4: Add Cart Styles

**Tambahkan di: assets/css/style.css**
```css
/* Cart Styles */
.cart-items {
    margin-bottom: 20px;
}

.cart-item {
    background: white;
    padding: 20px;
    margin-bottom: 10px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.item-info h3 {
    color: #333;
    margin-bottom: 5px;
}

.item-info p {
    color: #666;
    font-size: 14px;
}

.item-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.item-actions strong {
    color: #27ae60;
    font-size: 18px;
}

.remove-btn {
    background: #e74c3c;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 12px;
    transition: background 0.3s;
}

.remove-btn:hover {
    background: #c0392b;
}

.total-section {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.total-section h2 {
    color: #333;
    margin-bottom: 15px;
}

.total-price {
    font-size: 32px;
    font-weight: bold;
    color: #27ae60;
    margin-bottom: 20px;
}

.checkout-btn {
    display: block;
    width: 100%;
    padding: 15px;
    background: #27ae60;
    color: white;
    text-align: center;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    transition: background 0.3s;
}

.checkout-btn:hover {
    background: #229954;
}
```

**Test Shopping Cart:**
- [ ] Tambah beberapa item dari menu
- [ ] Lihat keranjang (klik floating button)
- [ ] Item tampil dengan benar
- [ ] Total harga benar
- [ ] Hapus item berfungsi

### 🎯 Goal Phase 3:
- ✅ Add to cart berfungsi
- ✅ Shopping cart page complete
- ✅ Remove item berfungsi
- ✅ Total calculation correct

---

## 📋 Phase 4: Checkout & Order (2-3 hari)

### Fokus: Membuat fitur checkout dan simpan order ke database

**File yang akan dibuat:**
- pages/checkout.php
- actions/process-checkout.php
- pages/order-success.php

**Checklist:**
- [ ] Form checkout (pilih metode pembayaran)
- [ ] Validasi checkout
- [ ] Insert order ke database
- [ ] Insert order_items ke database
- [ ] Clear cart setelah checkout
- [ ] Halaman konfirmasi order

---

## 📋 Phase 5: Admin Panel (3-4 hari)

### Fokus: Dashboard admin untuk kelola order

**File yang akan dibuat:**
- admin/login.php
- admin/logout.php
- admin/index.php (dashboard)
- admin/orders.php
- admin/includes/auth-check.php

**Checklist:**
- [ ] Login system
- [ ] Dashboard overview
- [ ] List orders dengan filter status
- [ ] Update order status
- [ ] Logout

---

## 📋 Phase 6: CRUD Menu (Admin) (2-3 hari)

### Fokus: Admin bisa manage menu

**File yang akan dibuat:**
- admin/menus.php
- admin/menu-add.php
- admin/menu-edit.php
- actions/menu-create.php
- actions/menu-update.php
- actions/menu-delete.php

---

## 📋 Phase 7: Reporting (2 hari)

### Fokus: Laporan penjualan

**File yang akan dibuat:**
- admin/reports.php

**Fitur:**
- [ ] Total pendapatan per hari/minggu/bulan
- [ ] Jumlah order
- [ ] Menu favorit
- [ ] Grafik (optional: pakai Chart.js)

---

## 📋 Phase 8: Polish & Testing (2-3 hari)

### Fokus: Improvement & bug fixing

**Checklist:**
- [ ] Test semua fitur end-to-end
- [ ] Fix bugs yang ditemukan
- [ ] Improve UI/UX
- [ ] Add loading states
- [ ] Add error messages yang jelas
- [ ] Mobile responsive check
- [ ] Security audit (SQL injection, XSS)
- [ ] Add validation untuk semua form

---

## 🎯 Tips Menjalani Project

### 1. **Satu Phase Selesai Dulu, Baru Next**
```
❌ Mulai Phase 1,2,3 sekaligus
✅ Selesaikan Phase 1 100%, baru Phase 2
```

### 2. **Test Setelah Setiap Step**
```
Jangan: Code banyak → Test → Banyak error → Bingung
Lakukan: Code sedikit → Test → Works → Next
```

### 3. **Commit Git Setelah Setiap Phase**
```bash
git add .
git commit -m "Phase 2 complete: Menu page with filtering"
```

### 4. **Debug dengan Systematic**
```
Error? → Baca error message
       → Google error message
       → Check basics (file include, variable defined, etc)
       → Isolate problem (comment code)
       → Fix
       → Test again
```

### 5. **Jangan Perfectionist di Awal**
```
Phase pertama: Functionality > Beauty
Phase akhir: Polish UI/UX
```

### 6. **Take Breaks**
```
Stuck 30 menit? → Take 5-10 min break
Fresh mind = Fresh ideas
```

### 7. **Document As You Go**
```
Buat notes:
- Apa yang sudah jalan
- Apa yang masih kurang
- Bug yang ditemukan
- Ide improvement
```

---

## 📊 Progress Tracker

Gunakan checklist ini untuk track progress:

```
Phase 0: Persiapan                    [ ]
Phase 1: Setup Project & Database     [ ]
Phase 2: View Menu                    [ ]
Phase 3: Shopping Cart                [ ]
Phase 4: Checkout & Order             [ ]
Phase 5: Admin Panel                  [ ]
Phase 6: CRUD Menu (Admin)            [ ]
Phase 7: Reporting                    [ ]
Phase 8: Polish & Testing             [ ]
```

---

## 🚀 Mulai Sekarang!

**Langkah Pertama Anda:**
1. Baca ulang BELAJAR_KONSEP_PHP_NATIVE.md (pahami konsep)
2. Mulai Phase 0 (setup environment)
3. Lanjut Phase 1 (setup project)
4. Step by step, don't rush!

**Remember:**
- Progress > Perfection
- Consistency > Intensity
- Learning by doing > Learning by reading

**You got this! 💪**

Kalau stuck di phase manapun, tanya dengan spesifik:
- "Saya stuck di Phase X, Step Y, error message: Z"
- Saya akan bantu debug!

Good luck! 🚀
