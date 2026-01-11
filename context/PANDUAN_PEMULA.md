# 🎯 PANDUAN KODING UNTUK PEMULA - Warkop QR

## 🚨 MASALAH: Struktur Terlalu Ribet!

Struktur sekarang:
```
app/controllers, app/core, app/models, routes/web.php
```

**Ini terlalu kompleks untuk pemula!** Ini pola framework, bukan PHP native.

---

## ✅ SOLUSI: Struktur SIMPLE untuk Pemula

Kita akan pakai struktur **FLAT & SIMPLE**:

```
warkop-qr/
├── index.php              ← Landing page
├── menu.php               ← Halaman menu
├── cart.php               ← Halaman cart
├── checkout.php           ← Halaman checkout
├── order-success.php      ← Halaman sukses
│
├── db.php                 ← Koneksi database (1 file saja!)
├── functions.php          ← Helper functions
│
├── admin/
│   ├── login.php          ← Login admin
│   ├── dashboard.php      ← Dashboard
│   ├── orders.php         ← Kelola orders
│   └── menus.php          ← Kelola menu
│
└── database/
    ├── migration.sql      ← Database schema (sudah ada)
    └── seeder.sql         ← Data sample (sudah ada)
```

**Kenapa simple?**
- ✅ 1 file = 1 halaman (mudah dipahami)
- ✅ Tidak pakai class (pakai function biasa)
- ✅ Tidak pakai routing (langsung akses file)
- ✅ Copy-paste HTML yang sudah ada, tambah PHP

---

## 📖 KONSEP DASAR PHP (Yang Perlu Kamu Tahu)

### 1. Cara Kerja PHP

```
User buka: menu.php
          ↓
    Browser request ke server
          ↓
    PHP jalankan menu.php
          ↓
    PHP query database
          ↓
    PHP generate HTML
          ↓
    Server kirim HTML ke browser
          ↓
    Browser tampilkan halaman
```

### 2. Include/Require File

**Fungsi:**
- `require_once` = import file PHP lain (1x saja)
- Dipakai untuk: koneksi DB, functions, config

**Contoh:**
```php
<?php
require_once 'db.php';        // ← Import koneksi database
require_once 'functions.php'; // ← Import helper functions

// Sekarang bisa pakai koneksi $conn dan function-function
?>
```

**Kenapa butuh include?**
- Biar tidak copy-paste kode yang sama di banyak file
- Koneksi database cukup 1 file, di-include di mana-mana

### 3. Koneksi Database (MySQLi)

**File: db.php**
```php
<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'warkop_qr';

$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
```

**Cara pakai:**
```php
<?php
require_once 'db.php'; // ← Variable $conn sekarang tersedia

// Query ke database
$sql = "SELECT * FROM menus";
$result = mysqli_query($conn, $sql);

// Loop hasil
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'];
}
?>
```

---

## 🎯 CARA KODING: Step by Step

### STEP 1: Convert HTML → PHP

Kamu sudah punya **menuphp**. Sekarang convert jadi **menu.php**:

**BEFORE (menuphp):**
```html
<div id="menuContainer">
    <!-- Menu items will be inserted here -->
</div>

<script>
    // Data dari localStorage
    let menus = JSON.parse(localStorage.getItem('menus'));
</script>
```

**AFTER (menu.php):**
```php
<?php
require_once 'db.php'; // ← Koneksi database

// Query menu dari database
$sql = "SELECT * FROM menus WHERE available = 1";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php while ($menu = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3><?= $menu['name'] ?></h3>
            <p>Rp <?= number_format($menu['price']) ?></p>
            <button>Add to Cart</button>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
```

**Penjelasan:**
- `require_once 'db.php'` → Import koneksi database
- `mysqli_query()` → Query ke database
- `while()` → Loop setiap data
- `<?= ?>` → Shorthand untuk echo (print)

---

### STEP 2: Form Processing (POST)

**Form HTML:**
```html
<form method="POST" action="process-checkout.php">
    <input type="text" name="customer_name" required>
    <select name="payment_method">
        <option value="Cash">Cash</option>
        <option value="Transfer">Transfer</option>
    </select>
    <button type="submit">Submit</button>
</form>
```

**Process di PHP (process-checkout.php):**
```php
<?php
require_once 'db.php';

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form
    $customer_name = $_POST['customer_name'];
    $payment_method = $_POST['payment_method'];
    
    // Insert ke database
    $sql = "INSERT INTO orders (customer_name, payment_method) 
            VALUES ('$customer_name', '$payment_method')";
    
    if (mysqli_query($conn, $sql)) {
        // Sukses → Redirect
        header("Location: order-success.php");
    } else {
        // Gagal → Tampilkan error
        echo "Error: " . mysqli_error($conn);
    }
}
?>
```

**Penjelasan:**
- `$_SERVER['REQUEST_METHOD']` → Cek apakah POST
- `$_POST['name']` → Ambil data dari form input
- `mysqli_query()` → Insert ke database
- `header("Location: ...")` → Redirect ke halaman lain

---

### STEP 3: Session (Login Admin)

**Login form:**
```html
<form method="POST">
    <input type="text" name="username">
    <input type="password" name="password">
    <button type="submit">Login</button>
</form>
```

**Process login:**
```php
<?php
session_start(); // ← WAJIB di awal file!

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Query user
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    
    // Cek password
    if ($user && password_verify($password, $user['password'])) {
        // Login sukses → Simpan session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: admin/dashboard.php");
    } else {
        $error = "Username atau password salah!";
    }
}
?>
```

**Proteksi halaman admin:**
```php
<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Sekarang aman, user sudah login
echo "Halo, " . $_SESSION['username'];
?>
```

---

## 🔧 HELPER FUNCTIONS

**File: functions.php**
```php
<?php

// Format rupiah
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Escape string untuk prevent SQL injection
function clean($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

// Generate order number
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user
function currentUser() {
    global $conn;
    if (!isLoggedIn()) return null;
    
    $sql = "SELECT * FROM users WHERE id = " . $_SESSION['user_id'];
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

?>
```

**Cara pakai:**
```php
<?php
require_once 'functions.php';

echo rupiah(15000);  // Output: Rp 15.000
$orderNum = generateOrderNumber(); // ORD-20260104-5678
?>
```

---

## 🎨 PATTERN KODING YANG KONSISTEN

### Pattern 1: Halaman Tampilan

```php
<?php
require_once 'db.php';
require_once 'functions.php';

// 1. Query data dari database
$sql = "SELECT * FROM menus";
$menus = mysqli_query($conn, $sql);

// 2. HTML dengan loop data
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php while ($menu = mysqli_fetch_assoc($menus)): ?>
        <div><?= $menu['name'] ?></div>
    <?php endwhile; ?>
</body>
</html>
```

### Pattern 2: Halaman Process (POST)

```php
<?php
require_once 'db.php';
require_once 'functions.php';

// 1. Cek apakah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 2. Ambil data dari form
    $name = clean($_POST['name']);
    
    // 3. Validasi
    if (empty($name)) {
        $error = "Nama wajib diisi!";
    } else {
        // 4. Insert/Update database
        $sql = "INSERT INTO orders (customer_name) VALUES ('$name')";
        
        if (mysqli_query($conn, $sql)) {
            // 5. Sukses → Redirect
            header("Location: success.php");
            exit;
        } else {
            // 6. Error → Tampilkan pesan
            $error = "Gagal menyimpan: " . mysqli_error($conn);
        }
    }
}
?>
```

### Pattern 3: Protected Admin Page

```php
<?php
session_start();
require_once '../db.php';
require_once '../functions.php';

// 1. Cek login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// 2. Rest of the code...
?>
<!DOCTYPE html>
<html>
<body>
    <h1>Dashboard Admin</h1>
</body>
</html>
```

---

## 📝 STYLE KODING (Best Practices)

### 1. Naming Convention

```php
// Variables: snake_case
$customer_name = "Budi";
$order_total = 50000;

// Functions: camelCase
function calculateTotal() {}
function getUserById() {}

// Constants: UPPER_CASE
define('TAX_RATE', 0.1);
define('SITE_NAME', 'Warkop QR');

// Database tables: lowercase plural
tables, menus, orders, users
```

### 2. File Organization

```php
<?php
// 1. Include files di paling atas
require_once 'db.php';
require_once 'functions.php';

// 2. Session start (kalau perlu)
session_start();

// 3. Process logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form
}

// 4. Query data
$sql = "SELECT * FROM menus";
$result = mysqli_query($conn, $sql);

// 5. HTML (tutup PHP tag)
?>
<!DOCTYPE html>
<html>
...
```

### 3. Security Basics

```php
// SELALU escape user input
$name = mysqli_real_escape_string($conn, $_POST['name']);

// ATAU pakai prepared statement (lebih aman)
$stmt = mysqli_prepare($conn, "INSERT INTO orders (customer_name) VALUES (?)");
mysqli_stmt_bind_param($stmt, "s", $name);
mysqli_stmt_execute($stmt);

// Password: SELALU hash
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Check password
password_verify($input_password, $hashed_from_db);
```

---

## 🚀 MULAI DARI MANA?

### Prioritas Development:

**Week 1: Basic Pages**
1. ✅ Database sudah ada (migration.sql, seeder.sql)
2. 🔨 Buat `db.php` (koneksi database)
3. 🔨 Buat `functions.php` (helper functions)
4. 🔨 Convert `menuphp` → `menu.php` (tampil menu dari DB)
5. 🔨 Buat cart system (pakai session, bukan localStorage)

**Week 2: Checkout & Orders**
6. 🔨 Buat `checkout.php` (form checkout)
7. 🔨 Buat `process-checkout.php` (simpan order ke DB)
8. 🔨 Buat `order-success.php` (konfirmasi)

**Week 3: Admin Panel**
9. 🔨 Buat `admin/login.php` (authentication)
10. 🔨 Buat `admin/dashboard.php` (stats)
11. 🔨 Buat `admin/orders.php` (kelola orders)
12. 🔨 Buat `admin/menus.php` (CRUD menu)

---

## 💡 TIPS BELAJAR

1. **Jangan langsung pakai class** → Pakai function dulu
2. **Jangan mikirin framework pattern** → Fokus logika dulu
3. **Test sedikit-sedikit** → Jangan langsung semua
4. **Pakai `var_dump()` untuk debug** → Lihat isi variable
5. **Baca error message** → PHP kasih tahu masalahnya
6. **Google adalah teman** → Stack Overflow, W3Schools

---

## 🔗 Resources untuk Belajar

- **PHP Manual**: https://www.php.net/manual/en/
- **W3Schools PHP**: https://www.w3schools.com/php/
- **PHP The Right Way**: https://phptherightway.com/

---

**INTINYA: MULAI SIMPLE, NANTI KOMPLEKS SENDIRI!**

Jangan takut salah, jangan takut error. Semua programmer mulai dari error! 🚀
