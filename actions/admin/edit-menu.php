<?php
session_start();
require_once '../../includes/database.php';

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/admin/menus.php');
    exit;
}

// Ambil data dari form
$menuId = $_POST['menuId'] ?? '';
$nama = trim($_POST['nama'] ?? '');
$kategoriId = $_POST['kategori_id'] ?? '';
$harga = $_POST['harga'] ?? '';

// Validasi input
if (empty($menuId) || empty($nama) || empty($kategoriId) || empty($harga)) {
    $_SESSION['error'] = 'Semua field wajib diisi!';
    header('Location: ../../pages/admin/menus.php');
    exit;
}

// Validasi harga
if (!is_numeric($harga) || $harga < 0) {
    $_SESSION['error'] = 'Harga tidak valid!';
    header('Location: ../../pages/admin/menus.php');
    exit;
}

try {
    // Ambil data menu lama untuk gambar
    $stmt = $conn->prepare("SELECT gambar FROM menu WHERE id = ?");
    $stmt->execute([$menuId]);
    $oldMenu = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$oldMenu) {
        $_SESSION['error'] = 'Menu tidak ditemukan!';
        header('Location: ../../pages/admin/menus.php');
        exit;
    }
    
    $oldGambar = $oldMenu['gambar'];
    $newGambar = $oldGambar; // Default pakai gambar lama
    
    // Handle upload gambar baru (jika ada)
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['gambar'];
        
        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Format file tidak didukung! Gunakan JPEG, JPG, atau PNG.';
            header('Location: ../../pages/admin/menus.php');
            exit;
        }
        
        // Validasi ukuran file (max 2MB)
        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'Ukuran file terlalu besar! Maksimal 2MB.';
            header('Location: ../../pages/admin/menus.php');
            exit;
        }
        
        // Generate nama file unik
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'menu_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = __DIR__ . '/../../uploads/' . $fileName;
        
        // Upload file baru
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Hapus gambar lama jika ada
            if (!empty($oldGambar)) {
                $oldFilePath = __DIR__ . '/../../uploads/' . $oldGambar;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            
            $newGambar = $fileName;
        } else {
            $_SESSION['error'] = 'Gagal mengupload gambar!';
            header('Location: ../../pages/admin/menus.php');
            exit;
        }
    }
    
    // Update database
    $stmt = $conn->prepare("
        UPDATE menu 
        SET nama = ?, kategori_id = ?, harga = ?, gambar = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([$nama, $kategoriId, $harga, $newGambar, $menuId]);
    
    $_SESSION['success'] = 'Menu berhasil diupdate!';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Gagal mengupdate menu: ' . $e->getMessage();
}

header('Location: ../../pages/admin/menus.php');
exit;
