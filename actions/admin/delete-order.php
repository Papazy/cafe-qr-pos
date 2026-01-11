<?php

require_once '../../includes/database.php';
require_once '../../includes/auth.php';

// Validasi request method
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
