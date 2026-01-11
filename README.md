# 🚀 Warkop QR - Setup Guide

Sistem pemesanan menggunakan QR Code untuk warung kopi.

## 📋 Requirements

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Composer (optional, untuk library tambahan)

## 🛠️ Installation Steps

### 1. Clone/Copy Project

```bash
# Jika menggunakan git
git clone <repository-url>
cd warkop-qr

# Atau extract ZIP ke folder project
```

### 2. Setup Database

```bash
# Login ke MySQL
mysql -u root -p

# Jalankan schema (ini akan create database & tables)
mysql -u root -p < database/schema.sql
```

**Atau via phpMyAdmin:**
1. Buka phpMyAdmin
2. Import file `database/schema.sql`

### 3. Konfigurasi Database Connection

Edit file `includes/database.php`:

```php
$host = 'localhost';
$dbname = 'warkop_qr';
$username = 'root';        // Sesuaikan dengan username MySQL Anda
$password = '';            // Sesuaikan dengan password MySQL Anda
```

### 4. Seed Data (Optional tapi Recommended)

```bash
# Seed kategori menu
php database/seed_kategori.php

# Seed menu items
php database/seed_menu.php

# Seed user admin (username: admin, password: admin123)
php database/seed_user.php

# Seed tables dengan QR tokens
php database/seed_tables.php
```

### 5. Setup Permissions

```bash
# Set permission untuk folder uploads (jika di Linux/Mac)
chmod 755 uploads/
chmod 755 uploads/qr/

# Buat folder jika belum ada
mkdir -p uploads/qr
```

### 6. Jalankan Server

**Option A: PHP Built-in Server (Development)**
```bash
php -S localhost:8000
```

Akses: `http://localhost:8000`

**Option B: Apache/Nginx (Production)**
- Copy project ke folder htdocs/www
- Akses via: `http://localhost/warkop-qr`

### 7. Login Admin

```
URL: http://localhost:8000/pages/admin/login.php
Username: admin
Password: admin123
```

---

## 📱 Cara Pakai

### Untuk Customer:

1. **Scan QR Code** di meja
2. **Pilih menu** yang diinginkan
3. **Tambah ke keranjang**
4. **Checkout** → Isi nama & pilih payment
5. **Lihat status** pesanan

### Untuk Admin:

1. **Login** di `/pages/admin/login.php`
2. **Dashboard** - Lihat statistik hari ini
3. **Kelola Pesanan** - Update status order
4. **Kelola Menu** - Tambah/edit/hapus menu
5. **Kelola Meja** - Manage meja & QR codes
6. **Laporan** - Lihat revenue & analytics

---

## 🔐 Security Notes

### Setelah Setup, Wajib Ubah:

1. **Password Admin**
   - Login → Update password di database
   - Hash dengan: `password_hash('password_baru', PASSWORD_DEFAULT)`

2. **QR Secret Key**
   - Edit `.env` atau `includes/qr-functions.php`
   - Ganti `warkop_secret_2025_change_this_in_production`

3. **Database Credentials**
   - Jangan gunakan user `root` di production
   - Buat user khusus dengan permissions terbatas

---

## 📦 Struktur Database

### Tables:
- `users` - Admin accounts
- `kategori` - Menu categories (Kopi, Non Kopi, Makanan, Snack)
- `menu` - Menu items dengan harga & gambar
- `tables` - Meja dengan QR token & status
- `orders` - Order dengan status (Pending → Diproses → Selesai)
- `order_items` - Detail items per order

---

## 🎯 Fitur Utama

### Customer Side:
- ✅ QR Code scanning untuk pilih meja
- ✅ Browse menu by kategori
- ✅ Shopping cart dengan localStorage
- ✅ Multiple payment methods (Cash, QRIS, Transfer)
- ✅ Real-time order status tracking
- ✅ Mobile-responsive design

### Admin Side:
- ✅ Dashboard dengan statistik real-time
- ✅ Order management (update status)
- ✅ Menu management (CRUD)
- ✅ Table management (QR codes)
- ✅ Revenue reports & analytics
- ✅ Print QR codes untuk meja

### Table Management:
- ✅ Auto table status (tersedia/ditempati)
- ✅ One active order per table
- ✅ Scan QR saat ada order aktif → redirect ke status
- ✅ Auto-clear table saat order selesai
- ✅ QR token regeneration untuk security

---

## 🐛 Troubleshooting

### Error: "Cannot connect to database"
```bash
# Check MySQL running
sudo service mysql status

# Check credentials di includes/database.php
```

### Error: "Headers already sent"
```bash
# Check untuk whitespace atau ?> di file PHP
# Hapus closing tag ?> di semua include files
```

### QR Code tidak bisa di-scan
```bash
# Regenerate QR token di admin
# Atau jalankan ulang: php database/seed_tables.php
```

### Meja tidak auto-update status
```bash
# Check foreign key constraint
# Jalankan: mysql -u root warkop_qr < database/migration_add_current_order.sql
```

---

## 📞 Support

Jika ada masalah atau pertanyaan, silakan:
1. Check dokumentasi di folder `/context/`
2. Lihat error log di browser console
3. Check PHP error log

---

## 📄 License

MIT License - Bebas digunakan untuk project komersial/personal.

---

**Happy Coding! ☕️**


pass2026warkopqr