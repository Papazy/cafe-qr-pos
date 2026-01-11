# 🚀 Setup PHP Built-in Server (Cara Termudah!)

## 🎯 Kenapa Pakai PHP Built-in Server?

### ✅ Keuntungan:
- **Super simple** - 1 command langsung jalan
- **No configuration** - Tidak perlu edit config Apache
- **No sudo** - Tidak perlu admin permission
- **Fast setup** - Ready dalam 1 menit
- **Perfect for development** - Fokus coding, bukan setup

### ⚠️ Limitations:
- **Development only** - JANGAN pakai di production
- **Single threaded** - Hanya 1 request per time (tapi cukup untuk development)
- **No .htaccess** - Tidak support Apache features

---

## 📋 Setup Guide (5 Menit!)

### Step 1: Cek PHP Tersedia

```bash
php -v
# Harus muncul: PHP 8.x.x atau PHP 7.x.x
```

Kalau muncul error "command not found":
```bash
# Install PHP via Homebrew
brew install php
```

### Step 2: Install MySQL

```bash
# Install via Homebrew
brew install mysql

# Start MySQL
brew services start mysql

# Secure installation (set password)
mysql_secure_installation
```

### Step 3: Setup Project

```bash
# Masuk ke folder project
cd /Users/fajryariansyah/Projects/warkop-qr

# Buat struktur folder (kalau belum)
mkdir -p includes pages actions admin assets/css assets/js assets/images uploads database admin/includes

# Verify
ls -la
```

### Step 4: Start PHP Server

```bash
# Di folder project root
php -S localhost:8000

# Output:
# [Wed Dec 25 10:00:00 2025] PHP 8.2.0 Development Server (http://localhost:8000) started
```

### Step 5: Test Server

Buka browser: **http://localhost:8000**

**Done!** 🎉 Server sudah jalan!

---

## 🎯 Cara Kerja PHP Built-in Server

### Request Flow:
```
Browser → http://localhost:8000/index.php
    ↓
PHP Built-in Server (port 8000)
    ↓
Execute index.php
    ↓
Return HTML ke Browser
```

### File Mapping:
```
http://localhost:8000/                → index.php
http://localhost:8000/pages/menu.php  → pages/menu.php
http://localhost:8000/test.php        → test.php
http://localhost:8000/assets/css/     → assets/css/style.css
```

---

## 💡 Tips & Tricks

### 1. Custom Port
```bash
# Kalau port 8000 sudah dipakai
php -S localhost:3000
php -S localhost:9000
php -S localhost:8888
```

### 2. Custom Host (untuk akses dari device lain)
```bash
# Get IP address
ifconfig | grep "inet "

# Start server di IP address
php -S 192.168.1.100:8000

# Sekarang bisa diakses dari HP/tablet di network yang sama
# http://192.168.1.100:8000
```

### 3. Router Script (Advanced)
```bash
# Buat custom router untuk handle semua request
php -S localhost:8000 router.php
```

**File: router.php**
```php
<?php
// Custom routing logic
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files
if (file_exists(__DIR__ . $uri)) {
    return false;
}

// Route ke index.php untuk dynamic content
require_once __DIR__ . '/index.php';
?>
```

### 4. Lihat Request Log
```bash
# Log otomatis muncul di terminal
[Wed Dec 25 10:00:00 2025] 127.0.0.1:52000 [200]: /index.php
[Wed Dec 25 10:00:01 2025] 127.0.0.1:52001 [200]: /pages/menu.php
[Wed Dec 25 10:00:02 2025] 127.0.0.1:52002 [404]: /notfound.php
```

### 5. Stop Server
```bash
# Tekan: Ctrl + C
^C
[Wed Dec 25 10:05:00 2025] Server stopped
```

---

## 🔧 Configuration untuk Project Warkop QR

### File: includes/config.php
```php
<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Development mode
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Base URL - untuk PHP built-in server
define('BASE_URL', 'http://localhost:8000/');

// Upload path
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
?>
```

### File: includes/db.php
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password_here');  // Password MySQL Anda
define('DB_NAME', 'warkop_qr');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
```

---

## 🚀 Quick Start Commands

### Start Development:
```bash
# Terminal 1: Start PHP Server
cd /Users/fajryariansyah/Projects/warkop-qr
php -S localhost:8000

# Terminal 2: Start MySQL (kalau belum jalan)
brew services start mysql

# Browser: Open http://localhost:8000
```

### Daily Workflow:
```bash
# Morning: Start server
cd ~/Projects/warkop-qr
php -S localhost:8000

# Code, test, debug...

# Evening: Stop server
Ctrl + C
```

---

## 🐛 Troubleshooting

### Error: "Address already in use"
```bash
# Port 8000 sudah dipakai, ganti port
php -S localhost:3000
```

### Error: "Failed to connect to MySQL"
```bash
# Check MySQL running
brew services list

# Start MySQL
brew services start mysql

# Check password di includes/db.php
```

### Error: "Cannot find file"
```bash
# Pastikan jalur file benar
# PHP built-in server tidak support URL rewriting otomatis
# Harus akses dengan path lengkap: /pages/menu.php
```

### Warning: "Session failed"
```bash
# Pastikan session_start() ada di awal file
# Sebelum ada output HTML
```

---

## 📊 Comparison: Apache vs PHP Built-in

| Feature | Apache | PHP Built-in |
|---------|--------|--------------|
| Setup | Complex | 1 command |
| Config | Need edit | No config |
| Performance | Fast | Slower (tapi cukup untuk dev) |
| Features | Full | Basic |
| .htaccess | ✅ Support | ❌ No support |
| Multiple requests | ✅ Concurrent | ⚠️ Sequential |
| Production ready | ✅ Yes | ❌ No |
| Development | ✅ Good | ✅ Perfect |

**Kesimpulan:** Untuk development, **PHP Built-in Server is the way to go!** 🚀

---

## 🎓 Best Practices

### 1. Different Ports for Different Projects
```bash
# Project 1
cd ~/Projects/project1
php -S localhost:8000

# Project 2 (terminal baru)
cd ~/Projects/project2
php -S localhost:8001
```

### 2. Use Terminal Multiplexer
```bash
# Install tmux
brew install tmux

# Start tmux session
tmux

# Split windows: Ctrl+B then "
# Switch: Ctrl+B then arrow keys
```

### 3. Auto-restart on File Changes
```bash
# Install watchexec
brew install watchexec

# Auto-restart server on file changes
watchexec -r -e php -- php -S localhost:8000
```

### 4. Create Alias
```bash
# Add to ~/.zshrc
echo 'alias serve="php -S localhost:8000"' >> ~/.zshrc
source ~/.zshrc

# Now just type:
cd ~/Projects/warkop-qr
serve
```

---

## 🎯 Recommended Setup untuk Warkop QR

### Terminal Setup (iTerm2 + tmux):
```
┌─────────────────────────────────────┐
│ Window 1: PHP Server                │
│ $ php -S localhost:8000             │
│ [Server running...]                 │
├─────────────────────────────────────┤
│ Window 2: MySQL Console             │
│ $ mysql -u root -p                  │
│ mysql>                              │
├─────────────────────────────────────┤
│ Window 3: Git / Commands            │
│ $ git status                        │
│ $                                   │
└─────────────────────────────────────┘
```

### VS Code Setup:
- Install extension: PHP Intelephense
- Install extension: PHP Debug
- Open project: `code /Users/fajryariansyah/Projects/warkop-qr`
- Integrated terminal untuk run server

---

## ✅ Final Checklist

### Persiapan (5 menit):
- [ ] PHP tersedia: `php -v`
- [ ] MySQL installed: `brew services list`
- [ ] Project folder ready
- [ ] Struktur folder created

### Start Development:
```bash
# 1. Start MySQL
brew services start mysql

# 2. Start PHP Server
cd /Users/fajryariansyah/Projects/warkop-qr
php -S localhost:8000

# 3. Open browser
open http://localhost:8000

# 4. Start coding!
code .
```

**You're ready to code! 🚀**

---

## 📝 Quick Reference

### Essential Commands:
```bash
# Start server
php -S localhost:8000

# Stop server
Ctrl + C

# Start MySQL
brew services start mysql

# Stop MySQL
brew services stop mysql

# Check MySQL status
brew services list

# MySQL console
mysql -u root -p
```

### URLs untuk Testing:
```
Main:      http://localhost:8000
Menu:      http://localhost:8000/pages/menu.php
Cart:      http://localhost:8000/pages/cart.php
Admin:     http://localhost:8000/admin/
```

---

**Happy Coding! 🎉**

Dengan PHP Built-in Server, Anda bisa langsung fokus ke coding tanpa ribet setup Apache!
