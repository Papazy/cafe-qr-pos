<?php
/**
 * Functions untuk Orders Management
 * File ini berisi fungsi-fungsi untuk mengambil data orders
 */

/**
 * Get list orders dengan filter
 * 
 * @param PDO $conn Database connection
 * @param string $status Filter by status (semua/Pending/Diproses/Selesai/Dibatalkan)
 * @param string $date Filter by date (semua/hari-ini/minggu-ini)
 * @param int|null $tableNumber Filter by table number
 * @param string|null $paymentMethod Filter by payment method
 * @return array List of orders
 */
function getOrders($conn, $status = 'semua', $date = 'hari-ini', $tableNumber = null, $paymentMethod = null) {
    $sql = "SELECT 
                o.id,
                o.order_number,
                o.table_number,
                o.customer_name,
                o.notes,
                o.payment_method,
                o.subtotal,
                o.tax,
                o.total,
                o.status,
                o.created_at,
                o.updated_at,
                COUNT(oi.id) as total_items
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE 1=1";
    
    $params = [];
    
    // Filter by status
    if ($status !== 'semua') {
        $sql .= " AND o.status = ?";
        $params[] = $status;
    }
    
    // Filter by date
    if ($date === 'hari-ini') {
        $sql .= " AND DATE(o.created_at) = CURDATE()";
    } elseif ($date === 'minggu-ini') {
        $sql .= " AND YEARWEEK(o.created_at, 1) = YEARWEEK(NOW(), 1)";
    } elseif ($date === 'bulan-ini') {
        $sql .= " AND MONTH(o.created_at) = MONTH(NOW()) AND YEAR(o.created_at) = YEAR(NOW())";
    }
    // 'semua' = no date filter
    
    // Filter by table
    if ($tableNumber !== null && $tableNumber !== '' && $tableNumber !== 'semua') {
        $sql .= " AND o.table_number = ?";
        $params[] = $tableNumber;
    }
    
    // Filter by payment method
    if ($paymentMethod !== null && $paymentMethod !== '' && $paymentMethod !== 'semua') {
        $sql .= " AND o.payment_method = ?";
        $params[] = $paymentMethod;
    }
    
    $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get detail order lengkap dengan items
 * 
 * @param PDO $conn Database connection
 * @param int $orderId Order ID
 * @return array|null Order detail with items or null if not found
 */
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
            LEFT JOIN kategori k ON m.kategori_id = k.id
            WHERE oi.order_id = ?
            ORDER BY oi.id ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$orderId]);
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $order;
}

/**
 * Get available table numbers from orders
 * 
 * @param PDO $conn Database connection
 * @return array List of unique table numbers
 */
function getTableNumbers($conn) {
    $sql = "SELECT DISTINCT table_number 
            FROM orders 
            ORDER BY table_number ASC";
    
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get order statistics for today
 * 
 * @param PDO $conn Database connection
 * @return array Statistics data
 */
function getOrderStats($conn) {
    $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'Pending' AND payment_method = 'Cash' THEN 1 ELSE 0 END) as pending_cash_count,
                SUM(CASE WHEN status = 'Diproses' THEN 1 ELSE 0 END) as diproses_count,
                SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai_count,
                SUM(CASE WHEN status = 'Dibatalkan' THEN 1 ELSE 0 END) as dibatalkan_count,
                SUM(CASE WHEN status IN ('Selesai', 'Diproses') THEN total ELSE 0 END) as total_revenue
            FROM orders
            WHERE DATE(created_at) = CURDATE()";
    
    $stmt = $conn->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
