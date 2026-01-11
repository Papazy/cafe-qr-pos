<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/database.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Order ID tidak ditemukan']);
    exit;
}

try {
    // Get order
    $stmt = $conn->prepare("
        SELECT * FROM orders WHERE id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan']);
        exit;
    }
    
    // Get order items with menu details
    $stmt_items = $conn->prepare("
        SELECT 
            oi.*, 
            m.nama as menu_name
        FROM order_items oi
        JOIN menu m ON oi.menu_id = m.id
        WHERE oi.order_id = ?
    ");
    $stmt_items->execute([$order_id]);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    
    // Add items to order
    $order['items'] = $items;
    
    echo json_encode([
        'success' => true,
        'order' => $order
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
