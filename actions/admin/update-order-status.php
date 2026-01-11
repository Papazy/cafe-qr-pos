<?php
require_once '../../includes/auth.php';
require_once '../../includes/database.php';
require_once '../../includes/qr-functions.php';

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/admin/orders.php');
    exit;
}

$orderId = $_POST['order_id'] ?? '';
$newStatus = $_POST['new_status'] ?? '';

// Validasi input
if (empty($orderId) || empty($newStatus)) {
    $_SESSION['error'] = 'Data tidak lengkap!';
    header('Location: ../../pages/admin/orders.php');
    exit;
}

$validStatuses = ['Pending', 'Diproses', 'Selesai', 'Dibatalkan'];
if (!in_array($newStatus, $validStatuses)) {
    $_SESSION['error'] = 'Status tidak valid!';
    header('Location: ../../pages/admin/orders.php');
    exit;
}

try {
    // Get order info (untuk ambil table_number)
    $stmt = $conn->prepare("SELECT order_number, table_number, status FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        $_SESSION['error'] = 'Pesanan tidak ditemukan!';
        header('Location: ../../pages/admin/orders.php');
        exit;
    }
    
    // Update status
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([$newStatus, $orderId]);
    
    // ⚠️ LOGIC: Jika status = 'Selesai' atau 'Dibatalkan', clear table
    if (in_array($newStatus, ['Selesai', 'Dibatalkan'])) {
        clearTable($order['table_number']);
        $_SESSION['success'] = "Status pesanan {$order['order_number']} berhasil diubah ke: $newStatus. Meja {$order['table_number']} sekarang tersedia.";
    } else {
        $_SESSION['success'] = "Status pesanan {$order['order_number']} berhasil diubah ke: $newStatus";
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal mengubah status: ' . $e->getMessage();
}

header('Location: ../../pages/admin/orders.php');
exit;
