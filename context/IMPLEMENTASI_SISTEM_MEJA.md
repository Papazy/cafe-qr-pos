# Implementasi Sistem Manajemen Meja

## 🎯 Tujuan
Membuat sistem meja yang:
1. **Track status meja** secara real-time (tersedia/ditempati)
2. **Satu meja = Satu order aktif** - Jika scan QR saat ada order aktif → redirect ke halaman status
3. **Auto-update status** - Status meja berubah otomatis berdasarkan status order
4. **QR Code validation** - Validasi token QR untuk keamanan

---

## 📊 Database Schema

### Tabel: `tables` (Sudah ada, perlu penyesuaian)
```sql
CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(10) NOT NULL UNIQUE,
    qr_token VARCHAR(64) NOT NULL UNIQUE,
    status ENUM('tersedia', 'ditempati') DEFAULT 'tersedia',
    current_order_id INT NULL,  -- ⚠️ TAMBAH: Link ke order yang sedang aktif
    last_scanned_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_qr_token (qr_token),
    INDEX idx_table_number (table_number),
    INDEX idx_status (status),
    FOREIGN KEY (current_order_id) REFERENCES orders(id) ON DELETE SET NULL
);
```

### Tabel: `orders` (Perlu update)
```sql
ALTER TABLE orders 
MODIFY COLUMN table_number VARCHAR(10) NOT NULL;  -- Ubah dari INT ke VARCHAR

-- Tambah index untuk query cepat
ALTER TABLE orders 
ADD INDEX idx_table_status (table_number, status);
```

---

## 🔄 Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    CUSTOMER SCAN QR CODE                     │
│                 ?table=5&token=abc123...                     │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
           ┌──────────────────────┐
           │  Validasi QR Token   │
           │  (index.php)         │
           └──────────┬───────────┘
                      │
                ┌─────┴─────┐
                │           │
         ❌ Invalid    ✅ Valid
                │           │
                ▼           ▼
         ┌──────────┐  ┌──────────────────┐
         │ Error    │  │ Cek Status Meja  │
         │ Landing  │  │ di Database      │
         └──────────┘  └──────┬───────────┘
                              │
                        ┌─────┴──────┐
                        │            │
                 Status=tersedia  Status=ditempati
                        │            │
                        ▼            ▼
              ┌─────────────┐  ┌────────────────────┐
              │ Set Session │  │ Get current_order_id│
              │ Redirect    │  │ Redirect ke         │
              │ → menu.php  │  │ → status.php?order=X│
              └─────────────┘  └────────────────────┘
```

---

## 🛠️ Implementasi Step-by-Step

### Phase 1: Update Database Schema

**File:** `/database/migration_add_current_order.sql`
```sql
-- Add current_order_id to tables
ALTER TABLE tables 
ADD COLUMN current_order_id INT NULL AFTER status,
ADD CONSTRAINT fk_current_order 
    FOREIGN KEY (current_order_id) 
    REFERENCES orders(id) 
    ON DELETE SET NULL;

-- Change table_number type in orders
ALTER TABLE orders 
MODIFY COLUMN table_number VARCHAR(10) NOT NULL;

-- Add indexes
ALTER TABLE orders 
ADD INDEX idx_table_status (table_number, status);

-- Add index for active orders query
CREATE INDEX idx_active_orders ON orders(table_number, status);
```

**Jalankan:**
```bash
mysql -u root warkop_qr < database/migration_add_current_order.sql
# atau
php -r "require 'includes/database.php'; $conn->exec(file_get_contents('database/migration_add_current_order.sql')); echo 'Migration done\n';"
```

---

### Phase 2: QR Functions Enhancement

**File:** `/includes/qr-functions.php`
```php
<?php
/**
 * Generate QR Token untuk meja
 * Token berubah setiap hari untuk security
 */
function generateQRToken($tableNumber) {
    $secretKey = $_ENV['QR_SECRET_KEY'] ?? 'warkop_secret_2025_change_this';
    $data = $tableNumber . $secretKey . date('Y-m-d');
    return hash('sha256', $data);
}

/**
 * Validasi QR Token
 */
function validateQRToken($tableNumber, $token) {
    $expectedToken = generateQRToken($tableNumber);
    return hash_equals($expectedToken, $token);
}

/**
 * Get Table Info dengan Current Order
 */
function getTableInfo($tableNumber) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT 
            t.*,
            o.id as order_id,
            o.order_number,
            o.status as order_status,
            o.customer_name
        FROM tables t
        LEFT JOIN orders o ON t.current_order_id = o.id
        WHERE t.table_number = ?
    ");
    $stmt->execute([$tableNumber]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Cek apakah meja punya order aktif (Pending/Diproses)
 */
function hasActiveOrder($tableNumber) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count
        FROM orders 
        WHERE table_number = ? 
        AND status IN ('Pending', 'Diproses')
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$tableNumber]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'] > 0;
}

/**
 * Get Active Order ID untuk meja
 */
function getActiveOrderId($tableNumber) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT id, order_number, status
        FROM orders 
        WHERE table_number = ? 
        AND status IN ('Pending', 'Diproses')
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$tableNumber]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Update table status dan current_order_id
 */
function updateTableStatus($tableNumber, $status, $orderId = null) {
    global $conn;
    
    $stmt = $conn->prepare("
        UPDATE tables 
        SET status = ?, 
            current_order_id = ?,
            last_scanned_at = NOW()
        WHERE table_number = ?
    ");
    
    return $stmt->execute([$status, $orderId, $tableNumber]);
}

/**
 * Clear table (set status = tersedia, clear order)
 */
function clearTable($tableNumber) {
    return updateTableStatus($tableNumber, 'tersedia', null);
}
?>
```

---

### Phase 3: Update Entry Point (index.php)

**File:** `/index.php` (Complete rewrite)
```php
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/qr-functions.php';

// ============================================
// QR SCAN FLOW
// ============================================
if (isset($_GET['table']) && isset($_GET['token'])) {
    $tableNumber = $_GET['table'];
    $token = $_GET['token'];
    
    // Step 1: Validasi QR Token
    if (!validateQRToken($tableNumber, $token)) {
        $_SESSION['error'] = 'QR Code tidak valid atau sudah expired';
        header('Location: ' . BASE_URL . '/pages/landing.php?error=invalid_qr');
        exit;
    }
    
    // Step 2: Get Table Info
    $tableInfo = getTableInfo($tableNumber);
    
    if (!$tableInfo) {
        $_SESSION['error'] = 'Meja tidak ditemukan';
        header('Location: ' . BASE_URL . '/pages/landing.php?error=table_not_found');
        exit;
    }
    
    // Step 3: Cek apakah ada order aktif
    $activeOrder = getActiveOrderId($tableNumber);
    
    if ($activeOrder) {
        // Meja sedang ditempati - redirect ke status order
        $_SESSION['table_number'] = $tableNumber;
        $_SESSION['qr_validated'] = true;
        $_SESSION['message'] = 'Meja ini sedang memiliki pesanan aktif';
        
        header('Location: ' . BASE_URL . '/pages/status.php?order=' . $activeOrder['id']);
        exit;
    }
    
    // Step 4: Meja tersedia - set session dan redirect ke menu
    $_SESSION['table_number'] = $tableNumber;
    $_SESSION['qr_validated'] = true;
    $_SESSION['qr_scanned_at'] = time();
    
    // Update last scanned time
    updateTableStatus($tableNumber, 'tersedia', null);
    
    header('Location: ' . BASE_URL . '/pages/menu.php');
    exit;
}

// ============================================
// MANUAL TABLE SELECTION (untuk testing)
// ============================================
if (isset($_GET['table']) && !isset($_GET['token'])) {
    $tableNumber = $_GET['table'];
    
    // Cek active order
    $activeOrder = getActiveOrderId($tableNumber);
    
    if ($activeOrder) {
        $_SESSION['table_number'] = $tableNumber;
        header('Location: ' . BASE_URL . '/pages/status.php?order=' . $activeOrder['id']);
        exit;
    }
    
    $_SESSION['table_number'] = $tableNumber;
    header('Location: ' . BASE_URL . '/pages/menu.php');
    exit;
}

// Default: redirect ke landing
header('Location: ' . BASE_URL . '/pages/landing.php');
exit;
?>
```

---

### Phase 4: Update Create Order Action

**File:** `/actions/create-order.php` (Enhancement)
```php
<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/qr-functions.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!$data || !isset($data['table_number']) || !isset($data['customer_name']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$tableNumber = $data['table_number'];

// ⚠️ VALIDASI: Cek apakah meja sudah punya order aktif
$activeOrder = getActiveOrderId($tableNumber);
if ($activeOrder) {
    echo json_encode([
        'success' => false, 
        'message' => 'Meja ini sudah memiliki pesanan aktif',
        'existing_order_id' => $activeOrder['id']
    ]);
    exit;
}

try {
    $conn->beginTransaction();
    
    // Generate order number
    $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $total = $data['total_amount'];
    
    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders (order_number, table_number, customer_name, notes, payment_method, subtotal, tax, total, status) 
        VALUES (?, ?, ?, ?, ?, ?, 0, ?, 'Pending')
    ");
    
    $stmt->execute([
        $order_number,
        $tableNumber,
        $data['customer_name'],
        $data['notes'] ?? null,
        $data['payment_method'],
        $total,
        $total
    ]);
    
    $order_id = $conn->lastInsertId();
    
    // Insert order items
    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, menu_id, quantity, price, subtotal) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach ($data['items'] as $item) {
        $item_subtotal = $item['price'] * $item['quantity'];
        $stmt_item->execute([
            $order_id,
            $item['menu_id'],
            $item['quantity'],
            $item['price'],
            $item_subtotal
        ]);
    }
    
    // ⚠️ UPDATE TABLE STATUS
    updateTableStatus($tableNumber, 'ditempati', $order_id);
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'order_number' => $order_number,
        'message' => 'Pesanan berhasil dibuat'
    ]);
    
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
    ]);
}
?>
```

---

### Phase 5: Update Order Status Handler

**File:** `/actions/admin/update-order-status.php`
```php
<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/qr-functions.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['order_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    // Get order info (untuk ambil table_number)
    $stmt = $conn->prepare("SELECT table_number, status FROM orders WHERE id = ?");
    $stmt->execute([$data['order_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan']);
        exit;
    }
    
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$data['status'], $data['order_id']]);
    
    // ⚠️ LOGIC: Jika status = 'Selesai' atau 'Dibatalkan', clear table
    if (in_array($data['status'], ['Selesai', 'Dibatalkan'])) {
        clearTable($order['table_number']);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Status pesanan berhasil diupdate'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal update status: ' . $e->getMessage()
    ]);
}
?>
```

---

### Phase 6: Update Status Page

**File:** `/pages/status.php` (Add table info)
```php
<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/qr-functions.php';

// Get order ID from URL
$order_id = $_GET['order'] ?? null;

if (!$order_id) {
    header('Location: ' . BASE_URL . '/pages/landing.php');
    exit;
}

// Fetch order with table info
$stmt = $conn->prepare("
    SELECT 
        o.*,
        t.status as table_status
    FROM orders o
    LEFT JOIN tables t ON o.table_number = t.table_number
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: ' . BASE_URL . '/pages/landing.php');
    exit;
}

// Set session table number for consistency
$_SESSION['table_number'] = $order['table_number'];
?>
<!-- Rest of status.php HTML... -->
```

---

## 🎯 User Flow Examples

### Scenario 1: First-time Scan (Meja Kosong)
```
1. Customer scan QR → ?table=5&token=abc123
2. Validasi token ✅
3. Cek order aktif → Tidak ada
4. Set session: table_number = 5
5. Redirect → menu.php
6. Customer order → Create order → Table status = 'ditempati'
```

### Scenario 2: Scan Saat Ada Order Aktif
```
1. Customer scan QR → ?table=5&token=abc123
2. Validasi token ✅
3. Cek order aktif → Ada (Order #123, Status: Pending)
4. Set session: table_number = 5
5. Redirect → status.php?order=123
6. Show: "Meja ini sedang memiliki pesanan aktif"
```

### Scenario 3: Order Selesai (Admin Update Status)
```
1. Admin: Update Order #123 → Status = 'Selesai'
2. Backend: clearTable(table_number=5)
3. Table status = 'tersedia', current_order_id = NULL
4. Customer baru scan QR → Bisa order lagi
```

---

## 📋 Testing Checklist

### Test Cases
- [ ] **Test 1**: Scan QR meja kosong → Masuk menu ✅
- [ ] **Test 2**: Scan QR meja dengan order Pending → Redirect status ✅
- [ ] **Test 3**: Scan QR meja dengan order Diproses → Redirect status ✅
- [ ] **Test 4**: Scan QR meja dengan order Selesai → Masuk menu (fresh order) ✅
- [ ] **Test 5**: Create order → Table status update ke 'ditempati' ✅
- [ ] **Test 6**: Admin update status Selesai → Table status ke 'tersedia' ✅
- [ ] **Test 7**: Scan QR invalid token → Error message ✅
- [ ] **Test 8**: Multiple tables concurrent orders → Isolated per table ✅

### Edge Cases
- [ ] Scan QR yang sama 2x bersamaan (race condition)
- [ ] Order dibatalkan → Table harus auto-clear
- [ ] Session expired → Re-scan QR harus work
- [ ] Table tidak ada di database → Graceful error

---

## 🚀 Deployment Steps

```bash
# 1. Backup database
mysqldump -u root warkop_qr > backup_before_table_system.sql

# 2. Run migration
mysql -u root warkop_qr < database/migration_add_current_order.sql

# 3. Update files
# - index.php
# - includes/qr-functions.php
# - actions/create-order.php
# - actions/admin/update-order-status.php

# 4. Generate QR tokens untuk existing tables
php database/seed_tables.php

# 5. Test flow
# - Scan QR
# - Create order
# - Update status
# - Re-scan QR

# 6. Monitor logs
tail -f /var/log/apache2/error.log  # atau PHP built-in server output
```

---

## 💡 Future Enhancements

### Phase 2 Features
1. **Real-time Table Status Board** (Admin Dashboard)
   - Live view semua meja: tersedia/ditempati
   - Color coding: hijau=tersedia, merah=ditempati
   - Click meja → Lihat order detail

2. **Auto-Clear Table Timer**
   - Jika order Selesai > 30 menit → Auto clear table
   - Reminder notification ke staff

3. **Table Reservation**
   - Customer bisa reserve meja via app
   - Status: 'tersedia' | 'ditempati' | 'reserved'

4. **Multi-Order per Table** (Advanced)
   - Untuk group besar yang split bill
   - Parent-child order relationship

---

## 📞 Troubleshooting

### Issue: Table tidak update status
**Solution:**
- Check foreign key constraint
- Verify `current_order_id` link
- Check transaction commit

### Issue: Scan QR loop redirect
**Solution:**
- Clear session: `session_destroy()`
- Verify `getActiveOrderId()` query
- Check order status enum values

### Issue: Race condition (2 order bersamaan)
**Solution:**
- Add database lock: `SELECT ... FOR UPDATE`
- Use transaction isolation level
- Implement optimistic locking

---

**Last Updated**: January 11, 2026  
**Version**: 2.0  
**Author**: Warkop QR Development Team
