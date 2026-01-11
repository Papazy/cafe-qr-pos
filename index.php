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