# Implementasi Sistem QR Code untuk Warkop

## 🎯 Ringkasan Sistem
Sistem QR Code memungkinkan pelanggan untuk:
1. **Scan QR** di meja → Akses menu langsung dengan nomor meja otomatis terisi
2. **Validasi Token** → Keamanan akses per meja
3. **Order Tracking** → Pesanan terasosiasi dengan meja spesifik

---

## 📊 Database Schema
 
### Tabel: `tables`
```sql
CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) NOT NULL UNIQUE,
    qr_token VARCHAR(64) NOT NULL UNIQUE,
    status ENUM('available', 'occupied', 'reserved') DEFAULT 'available',
    capacity INT DEFAULT 4,
    zone VARCHAR(50) DEFAULT 'indoor',
    last_scanned_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_qr_token (qr_token),
    INDEX idx_table_number (table_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Kolom Penting:**
- `qr_token`: Token unik SHA-256 untuk validasi
- `status`: Status meja (tersedia/terisi/dipesan)
- `last_scanned_at`: Track kapan terakhir QR di-scan
- `zone`: Area meja (indoor/outdoor/vip)

---

## 🔐 Struktur URL QR Code

### Format
```
https://warkop-qr.local/?table=1&token=abc123def456...
```

### Parameter
- `table`: Nomor meja (1-50)
- `token`: SHA-256 hash untuk validasi keamanan

### Contoh
```
https://warkop-qr.local/?table=5&token=8f4e3d2c1b0a9f8e7d6c5b4a3f2e1d0c
```

---

## 🛠️ Implementasi

### 1. Generate QR Token (PHP Function)

**File:** `/includes/qr-functions.php`

```php
<?php
function generateQRToken($tableNumber) {
    // Secret key (simpan di config)
    $secretKey = 'warkop_secret_key_2025'; // Ganti dengan random string
    
    // Generate token menggunakan table_number + secret + timestamp
    $data = $tableNumber . $secretKey . date('Y-m-d');
    return hash('sha256', $data);
}

function validateQRToken($tableNumber, $token) {
    $expectedToken = generateQRToken($tableNumber);
    return hash_equals($expectedToken, $token);
}

function getTableByToken($token) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT id, table_number, status, capacity, zone 
        FROM tables 
        WHERE qr_token = ? AND status = 'available'
    ");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateLastScanned($tableNumber) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE tables 
        SET last_scanned_at = NOW() 
        WHERE table_number = ?
    ");
    return $stmt->execute([$tableNumber]);
}
?>
```

---

### 2. Install QR Code Library

**Via Composer:**
```bash
cd /Users/fajryariansyah/Projects/warkop-qr
composer require endroid/qr-code
```

**Alternatif Manual (Jika tanpa Composer):**
Gunakan library JavaScript seperti `qrcode.js` atau service API seperti `api.qrserver.com`

---

### 3. Generate QR Code (Admin Panel)

**File:** `/pages/admin/generate-qr.php`

```php
<?php
require_once '../../includes/config.php';
require_once '../../includes/database.php';
require_once '../../includes/auth.php';
require_once '../../includes/qr-functions.php';

// Check admin auth
requireAdmin();

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableNumber = $_POST['table_number'];
    
    // Generate token
    $token = generateQRToken($tableNumber);
    
    // Update database
    $stmt = $conn->prepare("UPDATE tables SET qr_token = ? WHERE table_number = ?");
    $stmt->execute([$token, $tableNumber]);
    
    // Generate QR Code
    $baseUrl = 'https://warkop-qr.local';
    $qrUrl = "$baseUrl/?table=$tableNumber&token=$token";
    
    $qrCode = QrCode::create($qrUrl)
        ->setSize(300)
        ->setMargin(10);
    
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    // Save to file
    $filename = "table_$tableNumber.png";
    $result->saveToFile(__DIR__ . "/../../uploads/qr/$filename");
    
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'url' => $qrUrl
    ]);
    exit;
}
?>
```

---

### 4. Validasi QR di Entry Point

**File:** `/index.php` (Update)

```php
<?php
session_start();
require_once 'includes/database.php';
require_once 'includes/qr-functions.php';

// Check if QR scan parameters exist
if (isset($_GET['table']) && isset($_GET['token'])) {
    $tableNumber = $_GET['table'];
    $token = $_GET['token'];
    
    // Validate token
    if (validateQRToken($tableNumber, $token)) {
        // Get table info
        $table = getTableByToken($token);
        
        if ($table) {
            // Store table info in session
            $_SESSION['table_number'] = $tableNumber;
            $_SESSION['table_id'] = $table['id'];
            $_SESSION['qr_validated'] = true;
            $_SESSION['qr_scanned_at'] = time();
            
            // Update last scanned time
            updateLastScanned($tableNumber);
            
            // Redirect to menu
            header('Location: pages/menu.php');
            exit;
        } else {
            // Table not available or doesn't exist
            $_SESSION['error'] = 'Meja tidak tersedia atau sudah terisi';
            header('Location: pages/landing.php?error=table_unavailable');
            exit;
        }
    } else {
        // Invalid token
        $_SESSION['error'] = 'QR Code tidak valid atau sudah expired';
        header('Location: pages/landing.php?error=invalid_qr');
        exit;
    }
}

// Normal access without QR
header('Location: pages/landing.php');
exit;
?>
```

---

### 5. Seeder Script untuk Tables

**File:** `/database/seed_tables.php`

```php
<?php
require_once '../includes/database.php';
require_once '../includes/qr-functions.php';

echo "🚀 Seeding tables...\n\n";

// Define tables layout
$tables = [
    // Indoor tables
    ['number' => '1', 'capacity' => 2, 'zone' => 'indoor'],
    ['number' => '2', 'capacity' => 2, 'zone' => 'indoor'],
    ['number' => '3', 'capacity' => 4, 'zone' => 'indoor'],
    ['number' => '4', 'capacity' => 4, 'zone' => 'indoor'],
    ['number' => '5', 'capacity' => 4, 'zone' => 'indoor'],
    ['number' => '6', 'capacity' => 6, 'zone' => 'indoor'],
    ['number' => '7', 'capacity' => 6, 'zone' => 'indoor'],
    ['number' => '8', 'capacity' => 4, 'zone' => 'indoor'],
    
    // Outdoor tables
    ['number' => '9', 'capacity' => 4, 'zone' => 'outdoor'],
    ['number' => '10', 'capacity' => 4, 'zone' => 'outdoor'],
    ['number' => '11', 'capacity' => 6, 'zone' => 'outdoor'],
    ['number' => '12', 'capacity' => 2, 'zone' => 'outdoor'],
    
    // VIP area
    ['number' => 'VIP-1', 'capacity' => 8, 'zone' => 'vip'],
    ['number' => 'VIP-2', 'capacity' => 10, 'zone' => 'vip'],
];

try {
    $conn->beginTransaction();
    
    foreach ($tables as $table) {
        // Generate unique token
        $token = generateQRToken($table['number']);
        
        $stmt = $conn->prepare("
            INSERT INTO tables (table_number, qr_token, capacity, zone, status)
            VALUES (?, ?, ?, ?, 'available')
        ");
        
        $stmt->execute([
            $table['number'],
            $token,
            $table['capacity'],
            $table['zone']
        ]);
        
        echo "✅ Table {$table['number']} created (Capacity: {$table['capacity']}, Zone: {$table['zone']})\n";
        echo "   Token: $token\n\n";
    }
    
    $conn->commit();
    echo "\n✨ All tables seeded successfully!\n";
    
} catch (PDOException $e) {
    $conn->rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
```

**Jalankan:**
```bash
php database/seed_tables.php
```

---

### 6. Template Print QR Code

**File:** `/pages/admin/print-qr.php`

```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print QR Codes - Warkop</title>
    <style>
        @media print {
            .no-print { display: none; }
            .page-break { page-break-after: always; }
        }
        
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px;
        }
        
        .qr-card {
            width: 8cm;
            height: 10cm;
            border: 2px solid #000;
            margin: 1cm;
            padding: 1cm;
            text-align: center;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .qr-header {
            font-size: 24px;
            font-weight: bold;
            color: #4338ca;
            margin-bottom: 0.5cm;
        }
        
        .qr-image {
            width: 5cm;
            height: 5cm;
            margin: 1cm auto;
        }
        
        .qr-footer {
            font-size: 18px;
            margin-top: 0.5cm;
        }
        
        .qr-instructions {
            font-size: 12px;
            color: #666;
            margin-top: 0.3cm;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <h1>Print QR Codes</h1>
        <button onclick="window.print()">🖨️ Print All</button>
        <hr>
    </div>
    
    <?php
    require_once '../../includes/database.php';
    
    $stmt = $conn->query("SELECT * FROM tables ORDER BY table_number");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tables as $index => $table):
        $qrUrl = "https://warkop-qr.local/?table={$table['table_number']}&token={$table['qr_token']}";
    ?>
    
    <div class="qr-card <?= ($index + 1) % 4 === 0 ? 'page-break' : '' ?>">
        <div class="qr-header">
            🍵 WARKOP QR
        </div>
        
        <div class="qr-image">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=<?= urlencode($qrUrl) ?>" 
                 alt="QR Code Meja <?= $table['table_number'] ?>"
                 style="width: 100%; height: 100%;">
        </div>
        
        <div class="qr-footer">
            <strong>MEJA <?= $table['table_number'] ?></strong>
        </div>
        
        <div class="qr-instructions">
            Scan untuk Pesan
        </div>
    </div>
    
    <?php endforeach; ?>
</body>
</html>
```

---

## 🔒 Keamanan

### 1. Token Generation Strategy
- **Daily Rotation**: Token berubah setiap hari
- **SHA-256 Hash**: Tidak bisa di-reverse engineer
- **Secret Key**: Simpan di `config.php`, jangan commit ke Git

### 2. Validasi Flow
```
1. User scan QR → GET request dengan ?table=X&token=ABC
2. Server validasi token dengan fungsi validateQRToken()
3. Jika valid → Set session + redirect ke menu
4. Jika invalid → Redirect ke landing dengan error
```

### 3. Session Management
```php
$_SESSION['table_number'] = '5';
$_SESSION['qr_validated'] = true;
$_SESSION['qr_scanned_at'] = time();
```

### 4. Expiry Logic (Optional)
```php
// Di menu.php atau checkout.php
$scanTime = $_SESSION['qr_scanned_at'] ?? 0;
$currentTime = time();
$hoursSinceScanned = ($currentTime - $scanTime) / 3600;

if ($hoursSinceScanned > 2) {
    // QR expired after 2 hours
    session_destroy();
    header('Location: ../index.php?error=session_expired');
    exit;
}
```

---

## 🎨 UI/UX Considerations

### Landing Page Update
**Tampilkan 2 opsi:**
1. **"Scan QR di Meja"** (Primary CTA)
2. **"Pilih Meja Manual"** (Secondary - untuk walk-in tanpa QR)

### Menu Page Update
**Tampilkan info meja:**
```html
<div class="table-indicator">
    📍 Meja <?= $_SESSION['table_number'] ?? 'Belum dipilih' ?>
</div>
```

### Error Handling
- **Invalid QR**: "QR Code tidak valid. Silakan scan ulang atau hubungi staff."
- **Table Occupied**: "Meja sudah terisi. Silakan pilih meja lain."
- **Session Expired**: "Sesi Anda telah berakhir. Silakan scan QR kembali."

---

## 📋 Checklist Implementasi

### Phase 1: Database & Backend
- [ ] Create `tables` table (run schema.sql)
- [ ] Create `/includes/qr-functions.php`
- [ ] Create `/database/seed_tables.php`
- [ ] Run seeder: `php database/seed_tables.php`

### Phase 2: QR Generation
- [ ] Install `endroid/qr-code` via Composer
- [ ] Create `/pages/admin/generate-qr.php`
- [ ] Create `/actions/admin/regenerate-qr.php` (untuk regenerate token)
- [ ] Test QR generation untuk semua meja

### Phase 3: Validation Flow
- [ ] Update `/index.php` dengan QR validation logic
- [ ] Test valid QR scan → redirect ke menu
- [ ] Test invalid QR → show error message
- [ ] Test occupied table → block access

### Phase 4: Session Management
- [ ] Set session variables saat QR valid
- [ ] Update `/pages/menu.php` untuk show table number
- [ ] Update `/pages/checkout.php` untuk use session table_number
- [ ] Add session expiry logic (optional)

### Phase 5: Admin Panel
- [ ] Create QR management page di admin dashboard
- [ ] Add "Regenerate QR" button per table
- [ ] Create `/pages/admin/print-qr.php` untuk print all QR
- [ ] Test print flow

### Phase 6: Testing
- [ ] Test full flow: Scan QR → Menu → Cart → Checkout → Status
- [ ] Test multiple concurrent sessions (different tables)
- [ ] Test error scenarios (invalid token, occupied table, expired session)
- [ ] Test print template di berbagai ukuran kertas

---

## 🚀 Deployment Notes

### Development
```bash
# Start PHP built-in server
php -S localhost:8000

# Access: http://localhost:8000
```

### Production
1. **Update BASE_URL** di `config.php`:
   ```php
   define('BASE_URL', 'https://your-domain.com');
   ```

2. **Generate Production Tokens**:
   ```bash
   php database/seed_tables.php
   ```

3. **Print QR Codes**:
   - Akses: `/pages/admin/print-qr.php`
   - Print ke kertas sticker (8cm x 10cm)
   - Laminating untuk durability

4. **Update Secret Key**:
   ```php
   // In qr-functions.php
   $secretKey = getenv('QR_SECRET_KEY'); // Use environment variable
   ```

---

## 💡 Enhancement Ideas

### Future Improvements
1. **QR Analytics**: Track scan count, peak hours, popular tables
2. **Table Status Board**: Real-time dashboard untuk status semua meja
3. **Reservation System**: Pre-book table via QR
4. **Multi-Language QR**: QR bisa detect language preference
5. **NFC Integration**: Alternatif untuk QR (tap phone ke meja)

### Performance
- Cache QR images di `/uploads/qr/`
- CDN untuk QR image serving
- Redis untuk session management (high traffic)

---

## 📞 Troubleshooting

### QR Tidak Bisa Di-Scan
- ✅ Check QR image resolution (min 300x300px)
- ✅ Pastikan kontras hitam-putih cukup
- ✅ Test dengan multiple QR scanner apps
- ✅ Print dengan printer high-quality

### Token Invalid
- ✅ Check `qr_token` di database match dengan URL
- ✅ Verify `generateQRToken()` function
- ✅ Clear browser cache

### Session Tidak Persist
- ✅ Check `session_start()` di semua pages
- ✅ Verify `session.save_path` writable
- ✅ Check PHP session configuration

---

**Last Updated**: January 9, 2026  
**Version**: 1.0  
**Author**: Warkop QR Development Team
