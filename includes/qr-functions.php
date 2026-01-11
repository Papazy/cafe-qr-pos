<?php
/**
 * QR Code Functions for Warkop Table Management
 * Handles QR token generation, validation, and table status management
 */

/**
 * Generate QR Token untuk meja
 * Token berubah setiap hari untuk security
 */
function generateQRToken($tableNumber) {
    $secretKey = getenv('QR_SECRET_KEY') ?: 'warkop_secret_2025_change_this_in_production';
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
            o.customer_name,
            o.total as order_total,
            o.created_at as order_created_at
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
        SELECT id, order_number, status, customer_name, total
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

/**
 * Get all tables with their current status
 */
function getAllTablesStatus() {
    global $conn;
    
    $stmt = $conn->query("
        SELECT 
            t.*,
            o.id as order_id,
            o.order_number,
            o.status as order_status,
            o.customer_name,
            o.total as order_total
        FROM tables t
        LEFT JOIN orders o ON t.current_order_id = o.id
        ORDER BY 
            CAST(t.table_number AS UNSIGNED) ASC,
            t.table_number ASC
    ");
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Create new table with QR token
 */
function createTable($tableNumber, $qrToken = null) {
    global $conn;
    
    if ($qrToken === null) {
        $qrToken = generateQRToken($tableNumber);
    }
    
    $stmt = $conn->prepare("
        INSERT INTO tables (table_number, qr_token, status) 
        VALUES (?, ?, 'tersedia')
    ");
    
    return $stmt->execute([$tableNumber, $qrToken]);
}
