# Pemahaman Konteks: Sistem QR Code untuk Pemesanan Warung Kopi (Warkop)

## 📋 Ringkasan Proyek

Proyek ini adalah **sistem pemesanan digital berbasis QR Code** untuk warung kopi (Warkop). Pelanggan dapat scan QR code di meja untuk langsung mengakses menu digital, memesan, dan melakukan pembayaran - mengurangi kebutuhan akan menu fisik dan meningkatkan efisiensi pelayanan.

## 🏗️ Arsitektur Teknis

### Framework & Struktur
- **Framework**: ⚠️ **PHP NATIVE (Tanpa Framework)**
- **Pattern**: Struktur folder sederhana dengan include/require
- **Entry Point**: `public/index.php`
- **Catatan**: Project ini menggunakan PHP murni tanpa framework seperti Laravel, CodeIgniter, atau Symfony. Semua fitur dibuat dari nol.

### Folder Structure
```
warkop-qr/
├── app/
│   ├── controllers/    # Business logic controllers
│   ├── core/          # Core MVC framework files
│   ├── models/        # Database models
│   └── views/         # HTML templates
├── config/
│   └── database.php   # Database configuration
├── public/
│   └── index.php      # Application entry point
└── routes/
    └── web.php        # Route definitions
```

## 🔄 Alur Sistem (System Flow)

### 1. Flow: QR Code Scan → Menu Digital
**Tahapan:**
1. Pelanggan scan QR Code di meja
2. QR Code berisi URL: `https://website.com/order?table=5&token=xyz`
   - `table`: Nomor meja
   - `token`: Token keamanan untuk validasi
3. Server memvalidasi token
4. Sistem mengambil data meja dari database
5. Menampilkan halaman menu digital

**Kebutuhan Implementasi:**
- Controller untuk handle request scan QR
- Validasi token (security)
- Model untuk tabel `tables` atau `meja`
- View untuk menampilkan menu digital

---

### 2. Flow: Pemesanan Menu
**Tahapan:**
1. Pelanggan browsing daftar menu
2. Pelanggan memilih item dan masukkan ke keranjang
3. Pelanggan menuju checkout
4. Server memproses data keranjang

**Kebutuhan Implementasi:**
- Model `Menu` untuk daftar menu
- Shopping cart logic (session atau database)
- Controller untuk manage keranjang
- AJAX untuk add/remove items (optional, untuk UX lebih baik)

---

### 3. Flow: Checkout & Pembayaran
**Dua Metode Pembayaran:**

#### A. Bayar di Kasir
1. Sistem membuat pesanan dengan status `Pending`
2. Customer akan bayar langsung ke kasir

#### B. Pembayaran Digital
1. Sistem membuat pesanan dengan status `Pending`
2. Redirect ke Payment Gateway (contoh: Midtrans, Xendit, dll)
3. Payment Gateway kirim callback setelah pembayaran berhasil
4. Status berubah menjadi `Paid`

**Kebutuhan Implementasi:**
- Model `Orders` dengan field status
- Payment gateway integration
- Webhook handler untuk callback
- Status management system

**Status Order:**
- `Pending` - Belum dibayar
- `Paid` - Sudah dibayar
- `Complete` - Sudah dihidangkan

---

### 4. Flow: Manajemen Order (Admin/Kasir)
**Tahapan:**
1. Order baru masuk ke dashboard admin
2. Admin melihat detail pesanan:
   - Nama/nomor meja
   - Waktu pemesanan
   - List menu yang dipesan
   - Total pembayaran
3. Admin mengubah status order:
   - `Pending` → `Paid` (setelah terima pembayaran)
   - `Paid` → `Complete` (setelah dihidangkan)
4. Sistem menyimpan log aktivitas status

**Kebutuhan Implementasi:**
- Dashboard admin (protected route)
- Controller untuk order management
- Real-time update (optional: WebSocket/SSE)
- Model untuk order history/log
- Authentication & Authorization system

---

### 5. Flow: Laporan (Reporting)
**Proses:**
1. Setiap order selesai → data masuk ke tabel transaksi
2. Sistem melakukan agregasi otomatis:
   - Total pendapatan per hari
   - Jumlah pesanan
   - Menu favorit
3. Admin membuka halaman laporan
4. Tampilkan grafik & tabel

**Kebutuhan Implementasi:**
- Model `Transactions` atau `Reports`
- Query agregasi (SQL: SUM, COUNT, GROUP BY)
- Chart library (Chart.js, ApexCharts, etc.)
- Filter: harian, mingguan, bulanan
- Export to Excel/PDF (optional)

---

## 📊 Database Schema (Rancangan)

### Tabel Utama yang Diperlukan:

1. **tables** (meja)
   - id
   - table_number
   - qr_token
   - status (available/occupied)
   - created_at

2. **menus**
   - id
   - name
   - description
   - price
   - category_id
   - image
   - is_available
   - created_at

3. **categories**
   - id
   - name

4. **orders**
   - id
   - table_id
   - order_number
   - total_price
   - payment_method (cash/digital)
   - status (Pending/Paid/Complete)
   - created_at
   - updated_at

5. **order_items**
   - id
   - order_id
   - menu_id
   - quantity
   - price
   - subtotal
   - notes

6. **transactions** (untuk reporting)
   - id
   - order_id
   - amount
   - payment_method
   - transaction_date
   - created_at

7. **users** (admin/kasir)
   - id
   - username
   - password
   - role
   - created_at

8. **order_logs** (untuk tracking perubahan status)
   - id
   - order_id
   - old_status
   - new_status
   - changed_by
   - created_at

---

## 🎯 Fitur Utama yang Perlu Dibangun

### Customer Side:
- [x] QR Code Scanner Integration
- [ ] Menu Digital Display
- [ ] Shopping Cart
- [ ] Checkout System
- [ ] Payment Integration
- [ ] Order Status Tracking

### Admin/Kasir Side:
- [ ] Login System
- [ ] Dashboard Overview
- [ ] Order Management
- [ ] Menu Management (CRUD)
- [ ] Table Management
- [ ] Reporting & Analytics
- [ ] QR Code Generator untuk Meja

---

## 🔐 Pertimbangan Keamanan

1. **Token Validation**: Setiap QR code harus punya unique token untuk mencegah unauthorized access
2. **Admin Authentication**: Sistem login untuk admin/kasir
3. **CSRF Protection**: Untuk form submissions
4. **SQL Injection Prevention**: Prepared statements
5. **Payment Security**: Secure webhook verification dari payment gateway

---

## 🚀 Teknologi Pendukung yang Direkomendasikan

### Front-end:
- HTML5, CSS3, JavaScript
- Bootstrap atau Tailwind CSS (untuk styling)
- jQuery atau Vanilla JS (untuk interaktivity)
- Chart.js (untuk grafik laporan)

### Back-end:
- PHP (custom MVC framework)
- MySQL/MariaDB (database)
- Composer (untuk dependencies, optional)

### Additional Tools:
- Payment Gateway: Midtrans/Xendit/Doku
- QR Code Generator: PHP QR Code library
- PDF Generator: TCPDF/FPDF (untuk invoice)

---

## 📝 Status Proyek Saat Ini

**Yang Sudah Ada:**
- ✅ Struktur folder MVC dasar
- ✅ Entry point (`index.php`)
- ✅ Core files (App.php, Controller.php, Model.php) - belum diisi

**Yang Perlu Dikembangkan:**
- ⚠️ Core MVC framework (routing, controller loading, dll)
- ⚠️ Database configuration
- ⚠️ Models untuk semua tabel
- ⚠️ Controllers untuk semua fitur
- ⚠️ Views/templates
- ⚠️ Routes definition
- ⚠️ Authentication system
- ⚠️ Payment integration

---

## 🎯 Next Steps (Langkah Selanjutnya)

1. **Setup Core Framework**
   - Implement routing system di App.php
   - Base Controller dengan helper methods
   - Base Model dengan database connection

2. **Database Setup**
   - Create database schema
   - Implement migration system (optional)
   - Seed data untuk testing

3. **Build Customer Flow**
   - QR validation endpoint
   - Menu display
   - Cart system
   - Checkout & payment

4. **Build Admin Panel**
   - Authentication
   - Order dashboard
   - Menu CRUD
   - Reporting

5. **Testing & Deployment**
   - Testing semua flow
   - Security audit
   - Deploy ke server

---

## 💡 Catatan Tambahan

- Sistem ini cocok untuk warung kopi dengan multiple meja
- Mengurangi kontak fisik (cocok untuk era post-pandemic)
- Meningkatkan efisiensi pelayanan
- Data analytics untuk business intelligence
- Scalable untuk franchise atau multiple outlet (future development)

---

**Dibuat pada**: 25 Desember 2025
**Version**: 1.0
