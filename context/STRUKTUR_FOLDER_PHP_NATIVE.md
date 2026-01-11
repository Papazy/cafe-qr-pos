# 📁 Struktur Folder PHP Native yang Benar

## ❌ Masalah Struktur Saat Ini

Struktur folder saat ini:
```
app/
  controllers/
  core/
    App.php
    Controller.php
    Model.php
  models/
  views/
config/
  database.php
public/
  index.php
routes/
  web.php
```

**Masalahnya:**
- ❌ Terlalu kompleks untuk PHP Native
- ❌ Mengikuti pola Framework (MVC) padahal tidak pakai framework
- ❌ Banyak folder kosong yang tidak terpakai
- ❌ Overhead yang tidak perlu (core/App.php, routes/web.php)
- ❌ Sulit di-maintain karena over-engineering

---

## ✅ Struktur PHP Native yang Benar & Simple

### Opsi 1: Simple & Flat (Recommended untuk Project Kecil-Menengah)

```
warkop-qr/
├── index.php                    # Entry point utama (landing page)
│
├── includes/                    # File-file yang di-include
│   ├── config.php              # Konfigurasi aplikasi
│   ├── db.php                  # Database connection
│   └── functions.php           # Helper functions
│
├── pages/                       # Halaman-halaman customer
│   ├── menu.php                # Tampilan menu
│   ├── cart.php                # Keranjang belanja
│   ├── checkout.php            # Halaman checkout
│   ├── order-success.php       # Konfirmasi order
│   └── order-status.php        # Cek status order
│
├── actions/                     # Script untuk proses data (POST handler)
│   ├── add-to-cart.php         # Tambah item ke cart
│   ├── remove-from-cart.php    # Hapus item dari cart
│   ├── process-checkout.php    # Proses checkout
│   └── payment-callback.php    # Webhook payment gateway
│
├── admin/                       # Admin panel (protected)
│   ├── index.php               # Dashboard admin
│   ├── login.php               # Login admin
│   ├── logout.php              # Logout
│   ├── orders.php              # Kelola orders
│   ├── menus.php               # Kelola menu (CRUD)
│   ├── tables.php              # Kelola meja
│   ├── reports.php             # Laporan
│   └── includes/               # Helper untuk admin
│       └── auth-check.php      # Cek login
│
├── assets/                      # Static files
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
│       └── logo.png
│
├── uploads/                     # Folder untuk upload file
│   └── menu-images/            # Gambar menu
│
├── database/                    # SQL files
│   ├── schema.sql              # Database structure
│   └── seed.sql                # Sample data
│
└── .htaccess                    # Apache config (optional)
```

**Keuntungan struktur ini:**
- ✅ Sederhana dan mudah dipahami
- ✅ Tidak ada overhead framework
- ✅ File langsung sesuai fungsinya
- ✅ Mudah di-maintain
- ✅ Cocok untuk PHP Native

---

## 🔍 Penjelasan Per Folder

### 1. **index.php** (Root Level)
**Fungsi**: Landing page atau halaman utama
```php
<?php
require_once 'includes/config.php';

// Redirect ke menu jika ada table & token
if (isset($_GET['table']) && isset($_GET['token'])) {
    header('Location: pages/menu.php?table=' . $_GET['table'] . '&token=' . $_GET['token']);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Warkop QR - Welcome</title>
</head>
<body>
    <h1>Selamat Datang di Warkop QR</h1>
    <p>Silakan scan QR Code di meja Anda</p>
</body>
</html>
```

---

### 2. **includes/** (Core Files)
**Fungsi**: File-file yang akan di-include di banyak halaman

**includes/config.php**
```php
<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_URL', 'http://localhost/warkop-qr/');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
?>
```

**includes/db.php**
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'warkop_qr');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>
```

**includes/functions.php**
```php
<?php
function format_rupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// ... helper functions lainnya
?>
```

---

### 3. **pages/** (Customer Pages)
**Fungsi**: Halaman-halaman yang dilihat customer

**Structure:**
```
pages/
├── menu.php           # Tampilan menu + add to cart
├── cart.php           # Lihat keranjang
├── checkout.php       # Form checkout
└── order-success.php  # Konfirmasi setelah order
```

**Setiap halaman include file yang diperlukan:**
```php
<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Logic halaman...
?>
<!DOCTYPE html>
<html>
<!-- HTML content -->
</html>
```

---

### 4. **actions/** (POST Handlers)
**Fungsi**: Script untuk proses data (tidak ada HTML, hanya logic)

**Contoh: actions/add-to-cart.php**
```php
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = (int)$_POST['menu_id'];
    
    // Add to session cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$menu_id] = ($_SESSION['cart'][$menu_id] ?? 0) + 1;
    
    // Redirect back
    header('Location: ../pages/menu.php');
    exit;
}
?>
```

**Pattern untuk actions:**
- ✅ Tidak ada HTML
- ✅ Process data
- ✅ Redirect ke halaman lain
- ✅ Handle POST/GET request

---

### 5. **admin/** (Admin Panel)
**Fungsi**: Area khusus admin/kasir (protected dengan login)

**Structure:**
```
admin/
├── index.php              # Dashboard
├── login.php              # Login page
├── orders.php             # Kelola orders
├── menus.php              # CRUD menu
└── includes/
    └── auth-check.php     # Cek apakah sudah login
```

**admin/includes/auth-check.php**
```php
<?php
require_once '../../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
```

**Setiap admin page:**
```php
<?php
require_once 'includes/auth-check.php'; // Protect page
require_once '../includes/db.php';

// Admin logic...
?>
```

---

### 6. **assets/** (Static Files)
**Fungsi**: CSS, JavaScript, Images yang tidak berubah

```
assets/
├── css/
│   ├── style.css          # Style utama
│   └── admin.css          # Style admin
├── js/
│   ├── script.js          # JavaScript utama
│   └── admin.js           # JavaScript admin
└── images/
    ├── logo.png
    └── qr-sample.png
```

**Load di HTML:**
```html
<link rel="stylesheet" href="/warkop-qr/assets/css/style.css">
<script src="/warkop-qr/assets/js/script.js"></script>
```

---

### 7. **uploads/** (User Generated Content)
**Fungsi**: File yang di-upload user/admin

```
uploads/
└── menu-images/
    ├── kopi-hitam.jpg
    └── nasi-goreng.jpg
```

**Important:** Tambahkan `.htaccess` untuk security:
```apache
# uploads/.htaccess
<FilesMatch "\.(php|phtml|php3|php4|php5|phps)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

---

### 8. **database/** (SQL Files)
**Fungsi**: Simpan struktur database untuk dokumentasi

```
database/
├── schema.sql     # CREATE TABLE statements
└── seed.sql       # INSERT sample data
```

---

## 📊 Perbandingan: Structure Lama vs Baru

| Aspek | Struktur Lama (Framework Style) | Struktur Baru (PHP Native) |
|-------|----------------------------------|----------------------------|
| Kompleksitas | ❌ Tinggi (MVC framework) | ✅ Rendah (flat & simple) |
| File overhead | ❌ Banyak (core, routes, dll) | ✅ Minimal |
| Learning curve | ❌ Butuh pahami MVC pattern | ✅ Langsung bisa pakai |
| Maintenance | ❌ Sulit (banyak abstraksi) | ✅ Mudah (file jelas) |
| Performance | ⚠️ Overhead dari routing | ✅ Direct execution |
| Scalability | ⚠️ Oke untuk big project | ✅ Cukup untuk project ini |

---

## 🎯 Opsi 2: Hybrid (Jika Butuh Sedikit Struktur)

Jika project berkembang besar, bisa pakai struktur hybrid:

```
warkop-qr/
├── public/                  # Publicly accessible
│   ├── index.php
│   ├── assets/
│   └── uploads/
│
├── src/                     # Application code
│   ├── pages/
│   ├── actions/
│   └── admin/
│
├── includes/                # Shared includes
│   ├── config.php
│   ├── db.php
│   └── functions.php
│
└── database/
    └── schema.sql
```

**Keuntungan:**
- Security lebih baik (src/ tidak accessible dari web)
- Lebih terorganisir untuk project besar

**Kekurangan:**
- Butuh config `.htaccess` atau Nginx
- Sedikit lebih kompleks

---

## 🚀 Rekomendasi untuk Project Warkop QR

**Pakai Opsi 1 (Simple & Flat)** karena:
1. ✅ Project size kecil-menengah
2. ✅ Tim kecil (mungkin solo)
3. ✅ Fokus pada functionality, bukan architecture
4. ✅ Maintenance mudah
5. ✅ Development cepat

---

## 📝 Checklist Migrasi dari Struktur Lama

- [ ] 1. Backup struktur lama
- [ ] 2. Buat folder structure baru
- [ ] 3. Pindahkan `config/database.php` → `includes/db.php`
- [ ] 4. Hapus folder `app/core/`, `app/controllers/`, `app/models/`, `routes/`
- [ ] 5. Buat file-file di `includes/`
- [ ] 6. Buat file-file di `pages/`
- [ ] 7. Buat file-file di `actions/`
- [ ] 8. Buat file-file di `admin/`
- [ ] 9. Setup assets folder
- [ ] 10. Test semua functionality

---

## 💡 Best Practices PHP Native

### 1. **Naming Convention**
```
✅ GOOD:
pages/menu.php
actions/add-to-cart.php
includes/functions.php

❌ BAD:
pages/Menu.php          # Capital
actions/addToCart.php   # camelCase
includes/func.php       # Abbreviation
```

### 2. **File Organization**
```php
<?php
// 1. Includes (paling atas)
require_once '../includes/config.php';
require_once '../includes/db.php';

// 2. Logic & Data Processing
$menus = mysqli_query($conn, "SELECT * FROM menus");

// 3. HTML (paling bawah)
?>
<!DOCTYPE html>
<html>
...
</html>
```

### 3. **Separation of Concerns**
```
✅ pages/    → HTML + minimal logic
✅ actions/  → Logic only, no HTML
✅ includes/ → Reusable code
```

### 4. **File Naming Pattern**
```
Pages (customer):  menu.php, cart.php, checkout.php
Admin:            admin/orders.php, admin/menus.php
Actions:          add-to-cart.php, process-checkout.php
Includes:         config.php, db.php, functions.php
```

---

## 🎓 Kesimpulan

**Untuk PHP Native:**
- Jangan pakai struktur Framework MVC yang kompleks
- Pakai struktur flat & simple
- Pisahkan: pages (view) + actions (logic) + includes (shared)
- Fokus pada functionality, bukan over-engineering architecture

**Next Step:**
1. Restructure folder sesuai Opsi 1
2. Buat file-file dasar (config, db, functions)
3. Mulai coding dari halaman menu
4. Test & iterate

---

**Remember**: "Simple is better than complex" - The Zen of PHP Native! 🚀
