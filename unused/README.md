# 📊 Database Setup - Warkop QR

## 📁 Files

- **migration.sql** - Schema database (tables, views, triggers, indexes)
- **seeder.sql** - Data sample untuk testing
- **README.md** - Dokumentasi ini

---

## 🚀 Quick Start

### 1. Install & Jalankan MySQL/MariaDB

Pastikan MySQL atau MariaDB sudah terinstall dan running:

```bash
# Check MySQL status
mysql --version

# Start MySQL (macOS dengan Homebrew)
brew services start mysql

# Start MySQL (Linux)
sudo systemctl start mysql

# Start MySQL (Windows - XAMPP)
# Buka XAMPP Control Panel → Start MySQL
```

### 2. Jalankan Migration

Import schema database:

```bash
# Via command line
mysql -u root -p < database/migration.sql

# Atau via phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Import → Choose File → migration.sql
# 3. Click "Go"
```

### 3. Jalankan Seeder

Import data sample:

```bash
# Via command line
mysql -u root -p < database/seeder.sql

# Atau via phpMyAdmin
# 1. Buka phpMyAdmin
# 2. Pilih database "warkop_qr"
# 3. Import → Choose File → seeder.sql
# 4. Click "Go"
```

### 4. Verify Installation

Test koneksi database:

```bash
# Login ke MySQL
mysql -u root -p

# Select database
USE warkop_qr;

# Check tables
SHOW TABLES;

# Check data
SELECT * FROM menus LIMIT 5;
SELECT * FROM orders;
SELECT * FROM users;
```

---

## 🗃️ Database Schema

### Tables

| Table | Deskripsi | Rows (Sample) |
|-------|-----------|---------------|
| `tables` | Meja dengan QR token | 10 |
| `menus` | Menu items | 31 |
| `users` | Admin & Kasir | 3 |
| `orders` | Pesanan | 5 |
| `order_items` | Detail item pesanan | 15 |
| `order_status_logs` | Log perubahan status | 6 |

### Views

- **v_orders_detail** - Order dengan informasi lengkap + total items
- **v_menu_sales** - Menu dengan statistik penjualan

### Triggers

- **before_order_insert** - Auto generate order number
- **after_order_status_update** - Log setiap perubahan status order

---

## 👤 User Credentials

### Admin

```
Username: admin
Password: admin123
Role: admin
```

### Kasir

```
Username: kasir
Password: kasir123
Role: kasir

Username: kasir2
Password: kasir123
Role: kasir
```

**Note:** Password di-hash dengan `password_hash()` PHP (bcrypt)

---

## 🔐 QR Tokens

Sample QR tokens untuk testing (6 meja pertama):

| Meja | Token |
|------|-------|
| 1 | `TBL1-QR-ABC123-2026` |
| 2 | `TBL2-QR-DEF456-2026` |
| 3 | `TBL3-QR-GHI789-2026` |
| 4 | `TBL4-QR-JKL012-2026` |
| 5 | `TBL5-QR-MNO345-2026` |
| 6 | `TBL6-QR-PQR678-2026` |

Test URL format:
```
http://localhost:8000/order?table=1&token=TBL1-QR-ABC123-2026
```

---

## 📊 Sample Data

### Menus (31 items)

- **Kopi** (8 items): Kopi Hitam, Kopi Susu, Cappuccino, Latte, dll
- **Non Kopi** (8 items): Teh Manis, Jus Jeruk, Milkshake, dll
- **Makanan** (7 items): Nasi Goreng, Mie Goreng, Soto Ayam, dll
- **Snack** (8 items): Pisang Goreng, Kentang Goreng, Nugget, dll

### Orders (5 orders)

- **Pending**: 2 orders (Meja 1, 7)
- **Paid**: 1 order (Meja 3)
- **Processing**: 1 order (Meja 5)
- **Completed**: 1 order (Meja 2)

---

## 🔍 Useful Queries

### Revenue Hari Ini

```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_orders,
    SUM(total) as total_revenue
FROM orders
WHERE DATE(created_at) = CURDATE()
GROUP BY DATE(created_at);
```

### Top 5 Menu Terlaris

```sql
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
```

### Orders Per Status

```sql
SELECT 
    status,
    COUNT(*) as total,
    SUM(total) as revenue
FROM orders
GROUP BY status;
```

### Menu Per Kategori

```sql
SELECT 
    category,
    COUNT(*) as total_items,
    AVG(price) as avg_price
FROM menus
WHERE available = TRUE
GROUP BY category;
```

---

## 🔧 Troubleshooting

### Error: Access denied for user 'root'

Ubah credentials di `config/database.php`:

```php
private $username = 'your_username';
private $password = 'your_password';
```

### Error: Unknown database 'warkop_qr'

Database belum dibuat. Jalankan migration.sql.

### Error: Table doesn't exist

Seeder dijalankan sebelum migration. Jalankan migration dulu.

### Ingin reset database

```sql
DROP DATABASE warkop_qr;
```

Lalu jalankan migration.sql dan seeder.sql lagi.

---

## 📝 Notes

1. **Password Hashing**: Gunakan `password_hash()` dan `password_verify()` di PHP
2. **QR Token**: Generate dengan format `TBL{number}-QR-{random}-{year}`
3. **Order Number**: Auto-generated via trigger: `ORD-YYYYMMDD-XXXX`
4. **Status Flow**: Pending → Paid → Processing → Completed
5. **Tax**: Default 10% dari subtotal
6. **Foreign Keys**: CASCADE delete untuk order_items, RESTRICT untuk orders

---

## 🎯 Next Steps

1. ✅ Database schema ready
2. ✅ Sample data loaded
3. ⏳ Build Model classes (Menu, Order, Table, User)
4. ⏳ Build Controller untuk CRUD operations
5. ⏳ Connect frontend HTML to backend PHP
6. ⏳ Implement authentication & session
7. ⏳ Add validation & error handling

---

**Created:** 2026-01-04  
**Database:** MySQL/MariaDB  
**Charset:** UTF8MB4  
**Collation:** utf8mb4_unicode_ci
