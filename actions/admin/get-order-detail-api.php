<?php
/**
 * API endpoint untuk get order detail
 * Dipanggil via AJAX dari orders.php
 */

require_once '../../includes/database.php';
require_once '../../includes/auth.php';
require_once './get-orders.php';

header('Content-Type: application/json');

$orderId = $_GET['id'] ?? '';

if (empty($orderId)) {
    echo json_encode(['success' => false, 'message' => 'Order ID tidak valid']);
    exit;
}

try {
    $order = getOrderDetail($conn, $orderId);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $order
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
