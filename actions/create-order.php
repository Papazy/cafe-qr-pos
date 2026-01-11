<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/qr-functions.php';

// Get JSON input
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
    // Begin transaction
    $conn->beginTransaction();
    
    // Generate order number
    $order_number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Calculate totals
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
    
    // ⚠️ UPDATE TABLE STATUS - Set meja jadi 'ditempati'
    updateTableStatus($tableNumber, 'ditempati', $order_id);
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'order_number' => $order_number,
        'message' => 'Pesanan berhasil dibuat'
    ]);
    
} catch (PDOException $e) {
    // Rollback on error
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
    ]);
}
