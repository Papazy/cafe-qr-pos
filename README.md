# Warkop QR - Sistem Pemesanan QR Code

Sistem pemesanan makanan dan minuman berbasis QR Code untuk warung kopi/cafe. Pelanggan cukup scan QR di meja, pilih menu, dan pesan langsung dari smartphone.

## Tentang Project

Project ini dibuat menggunakan PHP Native (tanpa framework) dengan konsep simple dan mudah dipahami. Cocok untuk warung kopi, cafe kecil, atau restoran yang ingin digitalisasi sistem pemesanan dengan budget minimal.

**Tech Stack:** PHP 7.4+, MySQL, Tailwind CSS, Chart.js

## Fitur Utama

### Untuk Pelanggan
- **QR Code Ordering** - Scan QR di meja untuk langsung pesan
- **Browse Menu** - Lihat menu lengkap dengan foto dan harga
- **Keranjang Belanja** - Tambah/kurang item sebelum checkout
- **Multiple Payment** - Pilihan pembayaran: Cash, Transfer, QRIS
- **Order Tracking** - Lacak status pesanan real-time
- **Mobile Responsive** - Optimized untuk smartphone

### Untuk Admin
- **Dashboard** - Monitoring orders dan revenue harian
- **Kelola Menu** - CRUD menu dengan upload gambar
- **Kelola Pesanan** - Update status pesanan (Pending → Diproses → Selesai)
- **Kelola Meja** - Generate dan manage QR code per meja
- **Laporan Penjualan** - Revenue, top menu, payment method analytics
- **Filter Periode** - Laporan harian, mingguan, bulanan

## Instalasi

### Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 5.7+
- Composer
- Web server (Apache/Nginx) atau PHP built-in server

### Langkah Instalasi

1. **Clone repository**
```bash
git clone https://github.com/username/warkop-qr.git
cd warkop-qr
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup database**
```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE warkop_qr"

# Import schema
mysql -u root -p warkop_qr < database/schema.sql
```

4. **Konfigurasi environment**
```bash
cp .env.example .env
```

Edit `.env` sesuai konfigurasi database Anda:
```
DB_HOST=localhost
DB_NAME=warkop_qr
DB_USER=root
DB_PASS=your_password
BASE_URL=http://localhost:8000
```

5. **Seed data (opsional)**
```bash
# Seed kategori dan menu sample
php database/seed.php

# Generate 14 meja dengan QR code
php database/seed_tables.php
```

6. **Jalankan server**
```bash
php -S localhost:8000
```

Buka browser: `http://localhost:8000`

## Cara Penggunaan

### Admin Panel
1. Akses: `http://localhost:8000/pages/admin/login.php`
2. Login: `admin` / `admin123`
3. Menu admin:
   - Dashboard: Lihat statistik harian
   - Orders: Kelola status pesanan
   - Menu: Tambah/edit/hapus menu
   - Meja: Generate QR code dan kelola meja
   - Reports: Analisa penjualan

### Customer Flow
1. Scan QR code di meja (atau pilih nomor meja untuk testing)
2. Browse menu dan tambahkan ke keranjang
3. Klik keranjang → Checkout
4. Isi nama dan pilih metode pembayaran
5. Konfirmasi pesanan
6. Lihat status pesanan

## Testing dari HP

Untuk test dari smartphone di jaringan lokal:

1. Jalankan server dengan IP network:
```bash
php -S 0.0.0.0:8000
```

2. Cek IP address Mac/PC:
```bash
# Mac/Linux
ifconfig | grep "inet "

# Windows
ipconfig
```

3. Akses dari HP (pastikan satu WiFi):
```
http://192.168.x.x:8000
```

## Struktur Database

### Tables
- `users` - Admin login credentials
- `kategori` - Kategori menu (Kopi, Non Kopi, Makanan, Snack)
- `menu` - Daftar menu dengan harga dan gambar
- `tables` - Data meja dengan QR token
- `orders` - Pesanan pelanggan
- `order_items` - Detail item per pesanan

### Key Features
- Foreign key relationships untuk data integrity
- Index pada kolom yang sering di-query
- QR token dengan daily rotation untuk security
- Status tracking: Pending → Diproses → Selesai/Dibatalkan

## Teknologi

**Backend:**
- PHP Native (no framework)
- PDO untuk database
- Composer untuk dependency management

**Frontend:**
- Tailwind CSS untuk styling
- Vanilla JavaScript (no jQuery)
- LocalStorage untuk cart management
- Chart.js untuk data visualization

**Libraries:**
- `endroid/qr-code` - QR code generation
- `vlucas/phpdotenv` - Environment variables

## Fitur Keamanan

- Password hashing dengan `password_hash()`
- Prepared statements untuk prevent SQL injection
- Session management untuk admin authentication
- QR token dengan SHA-256 encryption
- CSRF protection ready
- Input validation dan sanitization

## Deployment

### Production Checklist
- [ ] Ubah credentials admin di database
- [ ] Set `BASE_URL` ke domain production
- [ ] Ganti `SECRET_KEY` di .env
- [ ] Disable error display di `php.ini`
- [ ] Setup SSL/HTTPS
- [ ] Backup database secara berkala
- [ ] Set proper file permissions (755 folder, 644 files)

### Recommended Hosting
- Shared hosting dengan PHP 7.4+ dan MySQL
- VPS dengan LAMP/LEMP stack
- Cloud hosting (DigitalOcean, AWS, etc)

## Browser Support

- Chrome (recommended)
- Safari iOS
- Firefox
- Edge
- Opera

## Troubleshooting

**Problem:** QR scan tidak redirect
- Pastikan BASE_URL sudah benar di .env
- Check token masih valid (daily rotation)

**Problem:** Menu tidak muncul
- Jalankan seed: `php database/seed.php`
- Check koneksi database

**Problem:** Upload gambar gagal
- Check permission folder uploads/ (755)
- Pastikan PHP `upload_max_filesize` cukup besar

**Problem:** Chart tidak muncul di dashboard
- Check ada data orders di database
- Clear browser cache

## Roadmap

- [ ] WebSocket untuk real-time order notification
- [ ] Print receipt feature
- [ ] Multi-branch support
- [ ] Loyalty program
- [ ] Rating & review system
- [ ] Inventory management
- [ ] WhatsApp notification integration

## Kontribusi

Kontribusi selalu welcome! Silakan:
1. Fork repository
2. Buat branch fitur (`git checkout -b fitur-baru`)
3. Commit changes (`git commit -m 'Tambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## License

MIT License - bebas digunakan untuk project komersial maupun personal.

## Support

Jika ada pertanyaan atau butuh bantuan:
- Buat issue di GitHub
- Email: your-email@example.com

---

**Made with ☕ for Indonesian Coffee Shops**
