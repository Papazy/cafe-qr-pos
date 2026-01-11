<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/menu.php');
    exit;
}

$menu_id = $_POST['menu_id'] ?? null;
$quantity = max(1, min(99, intval($_POST['quantity'] ?? 1)));

if (!$menu_id) {
    header('Location: ../pages/menu.php');
    exit;
}

// Fetch menu from database
$stmt = $conn->prepare("
    SELECT m.*, k.nama as kategori_nama 
    FROM menu m 
    JOIN kategori k ON m.kategori_id = k.id 
    WHERE m.id = :menu_id
");
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    $_SESSION['toast'] = 'Menu tidak tersedia';
    header('Location: ../pages/menu.php');
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if item already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['menu_id'] == $menu_id) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}

// Add new item if not found
if (!$found) {
    $_SESSION['cart'][] = [
        'menu_id' => $menu['id'],
        'nama_menu' => $menu['nama'],
        'nama_kategori' => $menu['kategori_nama'],
        'harga' => $menu['harga'],
        'quantity' => $quantity
    ];
}

$_SESSION['toast'] = $menu['nama'] . ' (' . $quantity . 'x) ditambahkan!';
header('Location: ../pages/menu.php' . ($_GET['kategori'] ?? ''));
exit;
