# 🎓 Belajar Konsep PHP Native - Dari NOL sampai BISA

## 🎯 Tujuan Pembelajaran

Setelah belajar ini, Anda harus bisa:
- ✅ Memahami bagaimana web application bekerja
- ✅ Berpikir secara logic dalam memecahkan masalah
- ✅ Membangun fitur sendiri tanpa copy-paste
- ✅ Debug error sendiri
- ✅ Membaca dokumentasi dan mencari solusi

**INGAT**: AI adalah alat bantu, tapi OTAK Anda yang harus paham!

---

## 📚 PART 1: Fundamental Web Application

### 1.1 Bagaimana Web Bekerja?

```
┌─────────┐                    ┌─────────┐
│ Browser │ ──── Request ────→ │ Server  │
│ (User)  │                    │ (PHP)   │
│         │ ←─── Response ──── │         │
└─────────┘                    └─────────┘
```

**Konsep Dasar:**
1. User buka URL di browser → Browser kirim **REQUEST**
2. Server terima request → PHP **PROCESS**
3. PHP ambil data dari database (kalau perlu)
4. PHP generate HTML
5. Server kirim **RESPONSE** (HTML) ke browser
6. Browser tampilkan HTML ke user

**Contoh Real Life:**
```
User ketik: http://localhost/warkop-qr/pages/menu.php

Yang terjadi:
1. Browser: "Halo server, saya mau file menu.php"
2. Server: "Oke, tunggu... *jalankan PHP*"
3. PHP: *query database → ambil menu → buat HTML*
4. Server: "Ini HTML-nya" → kirim ke browser
5. Browser: *tampilkan halaman menu*
```

---

### 1.2 Request Methods: GET vs POST

#### **GET** - Ambil/Lihat Data
```
URL: menu.php?table=5&category=kopi
       ↑       ↑      ↑
     file   param1  param2
```

**Karakteristik GET:**
- ✅ Data tampil di URL
- ✅ Bisa di-bookmark
- ✅ Bisa di-share
- ❌ Tidak aman untuk data sensitif (password, dll)
- ❌ Ada limit panjang URL

**Kapan pakai GET:**
- Filter/search
- Pagination
- Navigasi antar halaman
- Data yang boleh dilihat orang lain

**Cara ambil data GET di PHP:**
```php
<?php
// URL: menu.php?table=5&category=kopi

$table = $_GET['table'];      // "5"
$category = $_GET['category']; // "kopi"

echo "Meja: $table";           // Output: Meja: 5
echo "Kategori: $category";    // Output: Kategori: kopi
?>
```

#### **POST** - Kirim/Simpan Data
```
Form HTML → Submit → Data dikirim via POST (tidak tampil di URL)
```

**Karakteristik POST:**
- ✅ Data tidak tampil di URL (lebih aman)
- ✅ Tidak ada limit size
- ✅ Cocok untuk data sensitif
- ❌ Tidak bisa di-bookmark
- ❌ Tidak bisa di-share

**Kapan pakai POST:**
- Login
- Tambah data
- Update data
- Hapus data
- Upload file

**Cara ambil data POST di PHP:**
```php
<?php
// Form di HTML
// <form method="POST">
//   <input name="username">
//   <input name="password">
// </form>

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "Username: $username";
}
?>
```

---

### 1.3 Session - Menyimpan Data Sementara

**Masalah:** 
HTTP itu **stateless** = Server tidak ingat user sebelumnya.

**Contoh:**
```
Request 1: User login → Server: "Oke, login berhasil"
Request 2: User buka menu → Server: "Siapa kamu?" ← Lupa!
```

**Solusi: SESSION**
Session = Wadah penyimpanan data di server untuk 1 user.

```php
<?php
// START SESSION (harus di awal file!)
session_start();

// SIMPAN DATA ke session
$_SESSION['user_id'] = 123;
$_SESSION['username'] = 'john';
$_SESSION['cart'] = ['item1', 'item2'];

// AMBIL DATA dari session
echo $_SESSION['username'];  // "john"

// CEK apakah ada data
if (isset($_SESSION['user_id'])) {
    echo "User sudah login";
}

// HAPUS DATA tertentu
unset($_SESSION['cart']);

// HAPUS SEMUA (logout)
session_destroy();
?>
```

**Cara Kerja Session:**
1. User pertama kali akses → Server buat session ID
2. Session ID disimpan di cookie browser
3. Setiap request, browser kirim session ID
4. Server pakai session ID untuk ambil data user tersebut

**Kapan pakai Session:**
- Shopping cart (keranjang belanja)
- Login system
- Data temporary (form multi-step)
- Menyimpan preference user

---

## 📚 PART 2: Database - Menyimpan Data Permanen

### 2.1 Konsep Database Relational

**Database** = Lemari arsip digital
**Table** = Laci dalam lemari
**Row** = 1 dokumen
**Column** = Field dalam dokumen

```
Database: warkop_qr
├── Table: menus
│   ├── Row 1: id=1, name="Kopi Hitam", price=12000
│   ├── Row 2: id=2, name="Kopi Susu", price=15000
│   └── Row 3: id=3, name="Teh Manis", price=8000
│
└── Table: orders
    ├── Row 1: id=1, table_id=5, total=27000
    └── Row 2: id=2, table_id=3, total=50000
```

### 2.2 Relasi Antar Table

**Contoh Real Life:**
```
Order #1 pesen:
- 1x Kopi Hitam (12000)
- 1x Kopi Susu (15000)
Total: 27000

Gimana simpan di database?
```

**SALAH** ❌
```
Table: orders
id | menu_name   | price | total
1  | Kopi Hitam  | 12000 | 27000  ← Gimana kalau pesen 2 item?
```

**BENAR** ✅
```
Table: orders
id | table_id | total
1  | 5        | 27000

Table: order_items (detail order)
id | order_id | menu_id | quantity | price
1  | 1        | 1       | 1        | 12000
2  | 1        | 2       | 1        | 15000
```

**Konsep Relasi:**
- 1 Order bisa punya BANYAK items → **One to Many**
- Pakai `order_id` untuk hubungkan

### 2.3 SQL Query - Bahasa Database

#### **SELECT** - Ambil Data
```sql
-- Ambil semua menu
SELECT * FROM menus;

-- Ambil kolom tertentu
SELECT name, price FROM menus;

-- Ambil dengan kondisi
SELECT * FROM menus WHERE price < 15000;

-- Ambil dengan urutan
SELECT * FROM menus ORDER BY price ASC;

-- Ambil dengan limit
SELECT * FROM menus LIMIT 10;

-- Gabung 2 table
SELECT m.name, c.name as category
FROM menus m
JOIN categories c ON m.category_id = c.id;
```

#### **INSERT** - Tambah Data
```sql
-- Insert 1 row
INSERT INTO menus (name, price, category_id)
VALUES ('Kopi Latte', 18000, 1);

-- Insert multiple rows
INSERT INTO menus (name, price) VALUES
('Item 1', 10000),
('Item 2', 15000);
```

#### **UPDATE** - Ubah Data
```sql
-- Update 1 row
UPDATE menus 
SET price = 13000 
WHERE id = 1;

-- Update dengan kondisi
UPDATE orders 
SET status = 'Paid' 
WHERE id = 5 AND status = 'Pending';
```

#### **DELETE** - Hapus Data
```sql
-- Delete dengan kondisi
DELETE FROM menus WHERE id = 5;

-- DANGER! Hapus semua
DELETE FROM menus;  -- Hati-hati!
```

### 2.4 MySQLi di PHP

#### **Koneksi Database**
```php
<?php
$conn = mysqli_connect('localhost', 'root', '', 'warkop_qr');

// Cek koneksi
if (!$conn) {
    die("Gagal: " . mysqli_connect_error());
}
?>
```

#### **Query Database**
```php
<?php
// 1. Buat query string
$query = "SELECT * FROM menus WHERE is_available = 1";

// 2. Execute query
$result = mysqli_query($conn, $query);

// 3. Ambil data
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'];     // Akses kolom 'name'
    echo $row['price'];    // Akses kolom 'price'
}
?>
```

#### **Insert Data**
```php
<?php
$name = "Kopi Latte";
$price = 18000;

$query = "INSERT INTO menus (name, price) VALUES ('$name', $price)";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "Berhasil tambah data!";
    $new_id = mysqli_insert_id($conn); // Get ID yang baru dibuat
}
?>
```

#### **Update Data**
```php
<?php
$id = 5;
$new_price = 20000;

$query = "UPDATE menus SET price = $new_price WHERE id = $id";
$result = mysqli_query($conn, $query);

if ($result) {
    $affected = mysqli_affected_rows($conn);
    echo "Berhasil update $affected rows";
}
?>
```

---

## 📚 PART 3: Cara Berpikir Membangun Fitur

### 3.1 Methodology: Problem → Solution

**Langkah-langkah:**
1. **Pahami masalah** - Apa yang mau dicapai?
2. **Pecah jadi langkah kecil** - Break down
3. **Pikirkan data** - Data apa yang perlu disimpan?
4. **Pikirkan flow** - Urutan prosesnya gimana?
5. **Coding** - Implement langkah per langkah
6. **Testing** - Apakah sudah sesuai?

### 3.2 Case Study: Fitur "Tambah ke Keranjang"

#### **Step 1: Pahami Masalah**
```
User Story:
"Sebagai customer, saya ingin bisa menambah menu ke keranjang
 agar saya bisa checkout nanti"
```

#### **Step 2: Pecah Jadi Langkah Kecil**
```
1. User klik button "Tambah ke Keranjang"
2. System simpan item ke keranjang
3. System redirect/refresh halaman
4. Keranjang ter-update
```

#### **Step 3: Pikirkan Data**
```
Data yang perlu disimpan:
- menu_id (ID menu yang dipilih)
- quantity (berapa banyak)

Dimana simpan?
→ Session! (karena sementara, sebelum checkout)

Struktur data:
$_SESSION['cart'] = [
    1 => 2,    // menu_id => quantity
    3 => 1,
    5 => 3
];
```

#### **Step 4: Pikirkan Flow**
```
[Halaman Menu] 
    ↓
[Form POST: menu_id=1]
    ↓
[actions/add-to-cart.php]
    ↓
1. Ambil menu_id dari POST
2. Cek apakah cart sudah ada di session
3. Jika belum, buat array kosong
4. Jika menu sudah ada di cart, tambah quantity
5. Jika belum, tambah menu baru
6. Redirect kembali ke menu
    ↓
[Halaman Menu (dengan cart ter-update)]
```

#### **Step 5: Coding**

**File: pages/menu.php (Bagian form)**
```php
<form method="POST" action="../actions/add-to-cart.php">
    <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
    <button type="submit">Tambah ke Keranjang</button>
</form>
```

**File: actions/add-to-cart.php**
```php
<?php
session_start();

// 1. Ambil menu_id
$menu_id = (int)$_POST['menu_id'];

// 2. Cek cart di session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];  // Buat array kosong
}

// 3. Tambah atau update quantity
if (isset($_SESSION['cart'][$menu_id])) {
    $_SESSION['cart'][$menu_id]++;  // Tambah 1
} else {
    $_SESSION['cart'][$menu_id] = 1;  // Buat baru
}

// 4. Redirect
header('Location: ../pages/menu.php');
exit;
?>
```

#### **Step 6: Testing**
```
Test Case:
✓ Klik "Tambah" → Apakah masuk cart?
✓ Klik 2x pada item sama → Apakah quantity bertambah?
✓ Refresh halaman → Apakah cart masih ada?
✓ Close browser → Open lagi → Apakah cart hilang? (harusnya hilang karena session)
```

---

## 📚 PART 4: Debugging - Cara Mencari Error

### 4.1 Types of Errors

#### **1. Syntax Error** (Error ketik)
```php
<?php
echo "Hello World"  // ← Missing semicolon

// ERROR: Parse error: syntax error, unexpected end of file
```

**Cara fix:**
- Baca pesan error dengan teliti
- Lihat line number yang disebutkan
- Cek typo, missing semicolon, bracket tidak match

#### **2. Logic Error** (Code jalan tapi hasil salah)
```php
<?php
$price = 10000;
$quantity = 2;
$total = $price + $quantity;  // ← Harusnya kali (*)

echo $total;  // Output: 10002 (SALAH!)
?>
```

**Cara fix:**
- Review logic step by step
- Print/echo variable untuk cek value
- Pikirkan: "Apakah hasilnya masuk akal?"

#### **3. Runtime Error** (Error saat jalan)
```php
<?php
$result = mysqli_query($conn, $query);
// ERROR: Undefined variable $conn
```

**Cara fix:**
- Cek apakah variable sudah di-define
- Cek apakah file sudah di-include
- Cek apakah function tersedia

### 4.2 Debugging Tools

#### **Tool 1: var_dump() / print_r()**
```php
<?php
$cart = $_SESSION['cart'];

// Lihat isi variable
var_dump($cart);
// Output: array(2) { [1]=> int(2) [3]=> int(1) }

print_r($cart);
// Output: Array ( [1] => 2 [3] => 1 )
?>
```

#### **Tool 2: die() / exit()**
```php
<?php
echo "Step 1";
echo "Step 2";
die("STOP HERE!");  // ← Berhenti disini
echo "Step 3";      // ← Tidak jalan
?>
```

#### **Tool 3: Error Reporting**
```php
<?php
// Tampilkan semua error (development mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

#### **Tool 4: Browser Developer Tools**
```
F12 → Console → Lihat JavaScript error
F12 → Network → Lihat request/response
F12 → Application → Lihat cookies/session
```

### 4.3 Debugging Strategy

```
1. REPRODUCE ERROR
   → Bisa recreate error-nya?
   
2. READ ERROR MESSAGE
   → Apa kata error message-nya?
   → Line berapa?
   
3. ISOLATE PROBLEM
   → Comment code satu-satu
   → Mana yang bikin error?
   
4. CHECK BASICS
   → File sudah di-include?
   → Variable sudah di-define?
   → Syntax sudah benar?
   
5. TRACE EXECUTION
   → Echo/var_dump di setiap step
   → Apakah code sampai sini?
   → Value variable apa?
   
6. GOOGLE IT
   → Copy error message
   → Search di Google/Stack Overflow
   
7. FIX & TEST
   → Apply solution
   → Test lagi
```

---

## 📚 PART 5: Security Basics

### 5.1 SQL Injection

**Masalah:**
```php
<?php
$id = $_GET['id'];
$query = "SELECT * FROM menus WHERE id = $id";
// URL: menu.php?id=1 OR 1=1  ← DANGER!
// Query jadi: SELECT * FROM menus WHERE id = 1 OR 1=1
// Return SEMUA data!
?>
```

**Solusi: Escape Input**
```php
<?php
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM menus WHERE id = '$id'";
?>
```

**Solusi Lebih Baik: Prepared Statement**
```php
<?php
$stmt = mysqli_prepare($conn, "SELECT * FROM menus WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
```

### 5.2 XSS (Cross-Site Scripting)

**Masalah:**
```php
<?php
$name = $_POST['name'];
echo "Hello, $name";
// Input: <script>alert('HACKED!')</script>
// Browser execute script!
?>
```

**Solusi: Sanitize Output**
```php
<?php
$name = htmlspecialchars($_POST['name']);
echo "Hello, $name";
// Output: Hello, &lt;script&gt;alert('HACKED!')&lt;/script&gt;
// Browser tampilkan sebagai text, bukan execute
?>
```

### 5.3 Password Security

**SALAH** ❌
```php
<?php
// JANGAN simpan password plain text!
$query = "INSERT INTO users (password) VALUES ('$password')";
?>
```

**BENAR** ✅
```php
<?php
// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);
$query = "INSERT INTO users (password) VALUES ('$hashed')";

// Verify password saat login
if (password_verify($input_password, $hashed_from_db)) {
    echo "Login berhasil!";
}
?>
```

---

## 📚 PART 6: Best Practices

### 6.1 Code Organization

**Prinsip:** DRY (Don't Repeat Yourself)

**BURUK** ❌
```php
<?php
// File 1
$conn = mysqli_connect('localhost', 'root', '', 'warkop_qr');

// File 2
$conn = mysqli_connect('localhost', 'root', '', 'warkop_qr');

// File 3
$conn = mysqli_connect('localhost', 'root', '', 'warkop_qr');
// Copy-paste sama! Kalau password ganti, edit semua file!
?>
```

**BAGUS** ✅
```php
<?php
// includes/db.php
$conn = mysqli_connect('localhost', 'root', '', 'warkop_qr');

// File lain
require_once 'includes/db.php';
// Cukup include! Kalau ganti, edit 1 file aja.
?>
```

### 6.2 Naming Convention

**Konsisten & Descriptive**

```php
<?php
// ❌ BAD
$x = 10000;
$y = 2;
$z = $x * $y;

// ✅ GOOD
$price = 10000;
$quantity = 2;
$total = $price * $quantity;
?>
```

**Untuk File/Folder:**
```
✅ GOOD: add-to-cart.php, process-order.php
❌ BAD:  addToCart.php, processOrder.php (mixed style)
```

### 6.3 Comments

**Kapan harus comment:**
```php
<?php
// ✅ GOOD: Explain WHY, not WHAT
// Generate unique token untuk security
$token = bin2hex(random_bytes(32));

// ❌ BAD: Obvious comment
// Add 1 to i
$i = $i + 1;
?>
```

---

## 🎯 PART 7: Learning Path

### Level 1: Basics ⭐
```
□ Pahami HTTP Request/Response
□ Bisa pakai GET & POST
□ Bisa connect & query database
□ Bisa pakai session
□ Bisa include/require file
```

### Level 2: Features ⭐⭐
```
□ Buat halaman menu (display data)
□ Buat add to cart (session management)
□ Buat checkout (insert database)
□ Buat login admin (authentication)
```

### Level 3: Advanced ⭐⭐⭐
```
□ CRUD lengkap (Create, Read, Update, Delete)
□ File upload
□ Pagination
□ Search & filter
□ Reporting
```

### Level 4: Production Ready ⭐⭐⭐⭐
```
□ Input validation
□ Error handling
□ Security (SQL injection, XSS)
□ Performance optimization
□ User experience (AJAX, loading state)
```

---

## 📝 Exercise untuk Latihan

### Exercise 1: Display Menu
```
Task:
Tampilkan semua menu dari database dalam bentuk card/grid

Skills: SELECT query, loop, HTML

Hints:
1. Buat query SELECT * FROM menus
2. mysqli_query()
3. while loop dengan mysqli_fetch_assoc()
4. Echo HTML di dalam loop
```

### Exercise 2: Filter by Category
```
Task:
Tambah filter kategori di halaman menu

Skills: GET parameter, conditional query

Hints:
1. Buat link/button untuk setiap kategori
2. Pass category_id via GET
3. Modify query WHERE category_id = ?
4. Jangan lupa handle "Semua Kategori"
```

### Exercise 3: Shopping Cart Counter
```
Task:
Tampilkan jumlah item di cart di semua halaman

Skills: Session, function, count

Hints:
1. Buat function count_cart_items()
2. Loop $_SESSION['cart']
3. Sum semua quantity
4. Return total
```

---

## 🚀 Cara Belajar Efektif

### 1. **Jangan Langsung Copy-Paste!**
```
❌ Copy → Paste → Run → Jadi
✅ Baca → Pahami → Ketik ulang → Run → Debug → Pahami error
```

### 2. **Build Small, Test Often**
```
Jangan:
Build semua fitur → Test → Error banyak → Bingung

Lakukan:
Build 1 fitur → Test → Works → Next fitur
```

### 3. **Read Error Messages**
```
Error message adalah CLUE, bukan musuh!

"Undefined variable $conn in line 10"
↓
Artinya: Variable $conn belum di-define sebelum line 10
↓
Solution: Include db.php atau define $conn
```

### 4. **Use Documentation**
```
Lupa syntax mysqli_query?
→ Google: "php mysqli_query"
→ Baca php.net/mysqli-query
→ Lihat contoh
→ Adapt untuk project Anda
```

### 5. **Experiment!**
```
"Kalau saya ganti ini jadi begini, apa yang terjadi?"
→ Coba!
→ Error? Pelajari kenapa
→ Works? Pelajari kenapa
```

---

## 💡 Tips Anti Stuck

### Kalau Stuck, Lakukan Ini:

1. **Take a Break** (5-10 menit)
   → Otak perlu refresh

2. **Rubber Duck Debugging**
   → Explain masalah ke diri sendiri/teman
   → Sering kali solusi muncul sendiri

3. **Simplify**
   → Comment semua code
   → Uncomment 1-1
   → Mana yang error?

4. **Google Smart**
   ```
   ❌ "PHP error"
   ✅ "PHP undefined variable"
   ✅ "PHP mysqli_query returns false"
   ```

5. **Ask Community**
   → Stack Overflow
   → Reddit r/PHP
   → PHP Indonesia Group

---

## 🎓 Kesimpulan

**Kunci Sukses Belajar PHP Native:**

1. **Pahami Konsep** - Jangan hafal syntax
2. **Practice** - Coding setiap hari
3. **Debug** - Jangan takut error
4. **Read Code** - Baca code orang lain
5. **Build Projects** - Buat project real

**Remember:**
- AI adalah ALAT, bukan PENGGANTI OTAK
- Error adalah PEMBELAJARAN, bukan KEGAGALAN
- Progress > Perfection
- Consistency > Intensity

---

**Sekarang Anda siap! Let's build! 🚀**

**Next Step:**
1. Baca dokumen ini pelan-pelan
2. Pahami setiap konsep
3. Buat database
4. Mulai coding dari fitur paling simple
5. Test, debug, improve, repeat!

Good luck! 💪
