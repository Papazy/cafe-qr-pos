# 📦 Planning Fitur Pesanan (Orders Management)

**Project:** Warkop QR - Sistem Pemesanan  
**Module:** Orders Management (Admin Side)  
**Created:** 5 Januari 2026  
**Status:** Planning Phase  

---

## 🎯 Tujuan Fitur

**Customer Side:**
- Customer bisa membuat pesanan dari menu
- Customer bisa melihat status pesanan realtime
- Customer bisa memberikan catatan pesanan

**Admin Side:**
- Admin bisa melihat semua pesanan yang masuk
- Admin bisa update status pesanan (Pending → Diproses → Selesai)
- Admin bisa filter pesanan by status, tanggal, meja
- Admin bisa melihat detail pesanan lengkap
- Admin bisa print/export pesanan

---

## 📊 Database Schema

### 1. Table: `orders`

```sql
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    table_number INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    notes TEXT,
    payment_method ENUM('Cash', 'QRIS', 'Transfer') DEFAULT 'Cash',
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Diproses', 'Selesai', 'Dibatalkan') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Kolom Penjelasan:**
- `order_number`: Format WRK-YYYYMMDD-XXXX (WRK-20260105-0001)
- `table_number`: Nomor meja (1-20)
- `customer_name`: Nama customer
- `notes`: Catatan khusus (pedas, tanpa gula, dll)
- `payment_method`: Metode pembayaran
- `subtotal`: Total sebelum pajak
- `tax`: Pajak (10% dari subtotal) - OPTIONAL
- `total`: Total akhir yang dibayar
- `status`: Status pesanan saat ini

### 2. Table: `order_items`

```sql
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE RESTRICT
);
```

**Kolom Penjelasan:**
- `order_id`: ID order (relasi ke table orders)
- `menu_id`: ID menu yang dipesan (relasi ke table menu)
- `quantity`: Jumlah item
- `price`: Harga satuan saat transaksi (snapshot harga)
- `subtotal`: quantity × price
- `notes`: Catatan khusus per item (contoh: "Tanpa es")

---

## 🗂️ Struktur File

```
actions/
├── admin/
│   ├── get-orders.php         # Fungsi ambil data orders
│   ├── update-order-status.php # Update status pesanan
│   ├── delete-order.php        # Hapus pesanan (soft delete optional)
│   └── get-order-detail.php    # Detail 1 pesanan
│
└── customer/
    ├── create-order.php        # Proses checkout customer
    └── get-order-status.php    # Cek status pesanan customer

pages/
├── admin/
│   └── orders.php              # Halaman kelola pesanan admin
│
└── customer/
    ├── order-success.php       # Konfirmasi pesanan customer
    └── order-status.php        # Tracking pesanan customer

includes/
└── order-functions.php         # Helper functions untuk orders
```

---

## 🎨 UI/UX Design - Admin Orders Page

### Header Section
```
┌─────────────────────────────────────────────────────────────────┐
│  📦 Kelola Pesanan                    [Pending] [Diproses] [Selesai]
│  Manajemen pesanan customer                    [Dibatalkan]
└─────────────────────────────────────────────────────────────────┘
```

### Filter Section
```
┌─────────────────────────────────────────────────────────────────┐
│  Status: [Semua ▼]  Tanggal: [Hari Ini ▼]  Meja: [Semua ▼]     │
│                                               [🔍 Cari order...] │
└─────────────────────────────────────────────────────────────────┘
```

### Orders Table/Cards
```
┌─────────────────────────────────────────────────────────────────┐
│  WRK-20260105-0001          Meja 5        [●●●] Pending         │
│  John Doe                   12:45                                │
│  • Kopi Hitam (2x)          Rp 24.000                           │
│  • Nasi Goreng (1x)         Rp 20.000                           │
│  Total: Rp 44.000                                               │
│                                                                  │
│  [📋 Detail]  [✓ Proses]  [✗ Batalkan]                         │
└─────────────────────────────────────────────────────────────────┘
```

### Status Badge Colors
- **Pending**: `bg-yellow-100 text-yellow-700` (Kuning)
- **Diproses**: `bg-blue-100 text-blue-700` (Biru)
- **Selesai**: `bg-green-100 text-green-700` (Hijau)
- **Dibatalkan**: `bg-red-100 text-red-700` (Merah)

---

## 🔄 Flow Pesanan

### Flow Customer → Admin

```
Customer Side:
1. Browse menu → Add to cart
2. Checkout → Isi form (nama, catatan)
3. Submit → Insert ke database (status: Pending)
4. Redirect ke success page → Tampil order_number

Admin Side:
1. Lihat pesanan baru masuk (status: Pending)
2. Klik "Proses" → Status berubah ke "Diproses"
3. Setelah selesai → Klik "Selesai" → Status "Selesai"
4. Jika cancel → Klik "Batalkan" → Status "Dibatalkan"
```

### Status Workflow

```
┌─────────┐     ┌──────────┐     ┌─────────┐
│ Pending │ --> │ Diproses │ --> │ Selesai │
└─────────┘     └──────────┘     └─────────┘
     │               │
     └───────────────┴─────────────┐
                                   ▼
                            ┌──────────────┐
                            │ Dibatalkan   │
                            └──────────────┘
```

---

## 📋 Fitur Detail

### 1. **Get Orders List** (READ)

**File**: `actions/admin/get-orders.php`

**Function**: `getOrders($conn, $status, $date, $tableNumber)`

```php
function getOrders($conn, $status = 'semua', $date = 'hari-ini', $tableNumber = null) {
    $sql = "SELECT 
                o.id,
                o.order_number,
                o.table_number,
                o.customer_name,
                o.notes,
                o.payment_method,
                o.total,
                o.status,
                o.created_at,
                COUNT(oi.id) as total_items
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE 1=1";
    
    // Filter by status
    if ($status !== 'semua') {
        $sql .= " AND o.status = :status";
    }
    
    // Filter by date
    if ($date === 'hari-ini') {
        $sql .= " AND DATE(o.created_at) = CURDATE()";
    } elseif ($date === 'minggu-ini') {
        $sql .= " AND WEEK(o.created_at) = WEEK(NOW())";
    }
    
    // Filter by table
    if ($tableNumber) {
        $sql .= " AND o.table_number = :table";
    }
    
    $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($status !== 'semua') {
        $stmt->bindParam(':status', $status);
    }
    
    if ($tableNumber) {
        $stmt->bindParam(':table', $tableNumber);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

**Output:**
```php
[
    [
        'id' => 1,
        'order_number' => 'WRK-20260105-0001',
        'table_number' => 5,
        'customer_name' => 'John Doe',
        'notes' => 'Pedas sedang',
        'payment_method' => 'Cash',
        'total' => 44000,
        'status' => 'Pending',
        'created_at' => '2026-01-05 12:45:30',
        'total_items' => 3
    ],
    // ... more orders
]
```

---

### 2. **Get Order Detail** (READ)

**File**: `actions/admin/get-order-detail.php`

**Function**: `getOrderDetail($conn, $orderId)`

```php
function getOrderDetail($conn, $orderId) {
    // Get order header
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        return null;
    }
    
    // Get order items with menu details
    $sql = "SELECT 
                oi.id,
                oi.quantity,
                oi.price,
                oi.subtotal,
                oi.notes,
                m.nama as menu_name,
                m.gambar as menu_image,
                k.nama as kategori_name
            FROM order_items oi
            JOIN menu m ON oi.menu_id = m.id
            JOIN kategori k ON m.kategori_id = k.id
            WHERE oi.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$orderId]);
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $order;
}
```

**Output:**
```php
[
    'id' => 1,
    'order_number' => 'WRK-20260105-0001',
    'table_number' => 5,
    'customer_name' => 'John Doe',
    'notes' => 'Pedas sedang',
    'payment_method' => 'Cash',
    'subtotal' => 40000,
    'tax' => 4000,
    'total' => 44000,
    'status' => 'Pending',
    'created_at' => '2026-01-05 12:45:30',
    'items' => [
        [
            'id' => 1,
            'quantity' => 2,
            'price' => 12000,
            'subtotal' => 24000,
            'notes' => null,
            'menu_name' => 'Kopi Hitam',
            'menu_image' => 'menu_123.jpg',
            'kategori_name' => 'Minuman'
        ],
        [
            'id' => 2,
            'quantity' => 1,
            'price' => 20000,
            'subtotal' => 20000,
            'notes' => 'Tanpa sambel',
            'menu_name' => 'Nasi Goreng',
            'menu_image' => 'menu_456.jpg',
            'kategori_name' => 'Makanan'
        ]
    ]
]
```

---

### 3. **Update Order Status** (UPDATE)

**File**: `actions/admin/update-order-status.php`

**Form Data:**
- `order_id`: ID pesanan
- `new_status`: Status baru (Pending/Diproses/Selesai/Dibatalkan)

**Process:**
```php
session_start();
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/admin/orders.php');
    exit;
}

$orderId = $_POST['order_id'] ?? '';
$newStatus = $_POST['new_status'] ?? '';

// Validasi input
$validStatuses = ['Pending', 'Diproses', 'Selesai', 'Dibatalkan'];
if (!in_array($newStatus, $validStatuses)) {
    $_SESSION['error'] = 'Status tidak valid!';
    header('Location: ../../pages/admin/orders.php');
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([$newStatus, $orderId]);
    
    $_SESSION['success'] = "Status pesanan berhasil diubah ke: $newStatus";
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal mengubah status: ' . $e->getMessage();
}

header('Location: ../../pages/admin/orders.php');
exit;
```

---

### 4. **Delete Order** (DELETE)

**File**: `actions/admin/delete-order.php`

**Form Data:**
- `order_id`: ID pesanan yang akan dihapus

**Process:**
```php
session_start();
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/admin/orders.php');
    exit;
}

$orderId = $_POST['order_id'] ?? '';

if (empty($orderId)) {
    $_SESSION['error'] = 'ID pesanan tidak valid!';
    header('Location: ../../pages/admin/orders.php');
    exit;
}

try {
    // Cek apakah order exists
    $stmt = $conn->prepare("SELECT id, order_number FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        $_SESSION['error'] = 'Pesanan tidak ditemukan!';
        header('Location: ../../pages/admin/orders.php');
        exit;
    }
    
    // Delete order (CASCADE akan hapus order_items juga)
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    
    $_SESSION['success'] = "Pesanan {$order['order_number']} berhasil dihapus!";
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal menghapus pesanan: ' . $e->getMessage();
}

header('Location: ../../pages/admin/orders.php');
exit;
```

**Note:** Hapus order hanya untuk data testing. Di production, lebih baik gunakan **soft delete** (tambah kolom `deleted_at`).

---

### 5. **Create Order (Customer Side)**

**File**: `actions/customer/create-order.php`

**Form Data:**
- `table_number`: Nomor meja
- `customer_name`: Nama customer
- `notes`: Catatan pesanan
- `payment_method`: Metode pembayaran
- `cart`: Array item pesanan (dari SESSION)

**Process:**
```php
session_start();
require_once '../../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/customer/checkout.php');
    exit;
}

// Validasi cart
if (empty($_SESSION['cart'])) {
    $_SESSION['error'] = 'Keranjang kosong!';
    header('Location: ../../pages/customer/menu.php');
    exit;
}

$tableNumber = $_POST['table_number'] ?? '';
$customerName = trim($_POST['customer_name'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$paymentMethod = $_POST['payment_method'] ?? 'Cash';

// Validasi input
if (empty($customerName)) {
    $_SESSION['error'] = 'Nama wajib diisi!';
    header('Location: ../../pages/customer/checkout.php');
    exit;
}

try {
    // Begin transaction
    $conn->beginTransaction();
    
    // Generate order number: WRK-YYYYMMDD-XXXX
    $date = date('Ymd');
    $stmt = $conn->prepare("
        SELECT COUNT(*) + 1 as next_number 
        FROM orders 
        WHERE DATE(created_at) = CURDATE()
    ");
    $stmt->execute();
    $nextNumber = str_pad($stmt->fetch()['next_number'], 4, '0', STR_PAD_LEFT);
    $orderNumber = "WRK-{$date}-{$nextNumber}";
    
    // Calculate totals from cart
    $subtotal = 0;
    $cart = $_SESSION['cart'];
    
    foreach ($cart as $menuId => $quantity) {
        $stmt = $conn->prepare("SELECT harga FROM menu WHERE id = ?");
        $stmt->execute([$menuId]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($menu) {
            $subtotal += $menu['harga'] * $quantity;
        }
    }
    
    $tax = 0; // Optional: $subtotal * 0.1 (10%)
    $total = $subtotal + $tax;
    
    // Insert order header
    $stmt = $conn->prepare("
        INSERT INTO orders 
        (order_number, table_number, customer_name, notes, payment_method, 
         subtotal, tax, total, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())
    ");
    
    $stmt->execute([
        $orderNumber, $tableNumber, $customerName, $notes, 
        $paymentMethod, $subtotal, $tax, $total
    ]);
    
    $orderId = $conn->lastInsertId();
    
    // Insert order items
    foreach ($cart as $menuId => $quantity) {
        $stmt = $conn->prepare("SELECT harga FROM menu WHERE id = ?");
        $stmt->execute([$menuId]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($menu) {
            $price = $menu['harga'];
            $itemSubtotal = $price * $quantity;
            
            $stmt = $conn->prepare("
                INSERT INTO order_items 
                (order_id, menu_id, quantity, price, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$orderId, $menuId, $quantity, $price, $itemSubtotal]);
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Save order number for success page
    $_SESSION['last_order_number'] = $orderNumber;
    
    // Redirect to success page
    header('Location: ../../pages/customer/order-success.php');
    exit;
    
} catch (PDOException $e) {
    // Rollback on error
    $conn->rollBack();
    
    $_SESSION['error'] = 'Gagal membuat pesanan: ' . $e->getMessage();
    header('Location: ../../pages/customer/checkout.php');
    exit;
}
```

---

## 🎯 Fitur Tambahan (Optional/Future)

### 1. **Real-time Order Updates**
- Gunakan AJAX polling setiap 5 detik
- Customer auto-refresh status pesanan
- Admin auto-refresh list pesanan baru

### 2. **Print Order**
- Print receipt untuk customer
- Print kitchen order untuk chef
- Format thermal printer 58mm/80mm

### 3. **Order History**
- Customer bisa lihat history pesanan
- Filter by date range
- Export ke PDF/Excel

### 4. **Statistics**
- Total pesanan hari ini
- Total revenue
- Menu terlaris
- Peak hours

### 5. **Notifications**
- Email confirmation ke customer
- WhatsApp notification (via API)
- Admin notification sound saat order masuk

---

## 📊 Query Examples

### Get Today's Orders Count
```sql
SELECT COUNT(*) as total_orders
FROM orders
WHERE DATE(created_at) = CURDATE();
```

### Get Revenue by Status
```sql
SELECT 
    status,
    COUNT(*) as total_orders,
    SUM(total) as total_revenue
FROM orders
WHERE DATE(created_at) = CURDATE()
GROUP BY status;
```

### Get Top Selling Menu
```sql
SELECT 
    m.nama,
    SUM(oi.quantity) as total_sold,
    SUM(oi.subtotal) as total_revenue
FROM order_items oi
JOIN menu m ON oi.menu_id = m.id
JOIN orders o ON oi.order_id = o.id
WHERE DATE(o.created_at) = CURDATE()
GROUP BY m.id
ORDER BY total_sold DESC
LIMIT 5;
```

### Get Orders by Time Slot
```sql
SELECT 
    HOUR(created_at) as hour,
    COUNT(*) as total_orders
FROM orders
WHERE DATE(created_at) = CURDATE()
GROUP BY HOUR(created_at)
ORDER BY hour;
```

---

## ✅ Checklist Implementation

### Phase 1: Database & Backend Functions
- [ ] Create table `orders` & `order_items`
- [ ] Create function `getOrders()` with filters
- [ ] Create function `getOrderDetail()`
- [ ] Test queries with sample data

### Phase 2: Customer Order Creation
- [ ] Create `create-order.php` with transaction
- [ ] Create `order-success.php` page
- [ ] Create `order-status.php` page (optional)
- [ ] Test end-to-end flow dari cart → checkout → success

### Phase 3: Admin Orders Management
- [ ] Create `pages/admin/orders.php` UI
- [ ] Implement filter (status, date, table)
- [ ] Create `update-order-status.php`
- [ ] Create `delete-order.php`
- [ ] Create detail order modal

### Phase 4: Testing & Polish
- [ ] Test all CRUD operations
- [ ] Test status workflow
- [ ] Test with multiple simultaneous orders
- [ ] Add loading states & error handling
- [ ] Add confirmation modals

### Phase 5: Advanced Features (Optional)
- [ ] Real-time updates dengan AJAX
- [ ] Print receipt feature
- [ ] Export to PDF/Excel
- [ ] Order history customer
- [ ] WhatsApp notification

---

## 🚨 Important Notes

1. **Transaction Safety**: Gunakan `BEGIN TRANSACTION` saat create order untuk ensure atomicity
2. **Price Snapshot**: Simpan harga di `order_items` (jangan ambil dari `menu.harga`) karena harga bisa berubah
3. **Cascade Delete**: Set `ON DELETE CASCADE` untuk `order_items` agar otomatis terhapus saat order dihapus
4. **Order Number**: Pastikan unique dengan kombinasi date + auto-increment
5. **Status Validation**: Cek status transitions yang valid (jangan langsung Pending → Selesai)
6. **Security**: Validasi semua input, gunakan prepared statements
7. **Session Management**: Clear cart setelah order berhasil dibuat

---

## 📚 Learning Path

Urutan belajar untuk implementasi:

1. **Pahami Database Schema** → Relasi orders ↔ order_items
2. **Buat Function Read** → `getOrders()` dan `getOrderDetail()`
3. **Test dengan Sample Data** → Insert manual ke database
4. **Buat UI Admin** → Layout cards/table orders
5. **Implement Filter** → Status, tanggal, meja
6. **Create Order Flow** → Customer checkout → database
7. **Update Status** → Button actions untuk ubah status
8. **Polish & Test** → Error handling, loading states

**Estimasi Waktu:** 3-4 hari untuk fitur lengkap

---

**Last Updated:** 5 Januari 2026  
**Next Step:** Buat migration file dan sample data untuk testing

