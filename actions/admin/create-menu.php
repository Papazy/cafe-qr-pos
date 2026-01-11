<?php
    session_start();
    
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../../pages/admin/menus.php');
        exit;
    }
    
    require_once '../../includes/database.php';

    // data
    $nama = $_POST['nama'] ?? '';
    $kategori_id = $_POST['kategori_id'] ?? '';
    $harga = $_POST['harga'] ?? 0;
    $gambar = $_FILES['gambar'] ?? null;

    // validasi jangan ada menu yang sama
    $checkStmt = $conn->prepare("SELECT * FROM menu WHERE nama = ? AND kategori_id = ?");
    $checkStmt->execute([$nama, $kategori_id]);

    if($checkStmt->fetch()) {
        $_SESSION["error"] = "Menu dengan nama yang sama sudah ada di kategori ini";
        header("Location: ../../pages/admin/menus.php");
        exit;
    }
    
    // upload file
    $gambarPath = null;
    
    if(!isset($gambar) || !$gambar['error'] === UPLOAD_ERR_OK) {
        // Handle file upload error
        header('Location: ../../pages/admin/menus.php');
        exit;
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if(!in_array($gambar['type'], $allowedTypes)) {
        // Handle invalid file type
        header('Location: ../../pages/admin/menus.php');
        exit;
    }
    
    $filename = $gambar['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $newFilename = uniqid() . '_' . time() . '.' . $ext;
    $uploadPath = __DIR__ . '/../../uploads/' . $newFilename;
    
    if(move_uploaded_file($gambar['tmp_name'], $uploadPath)) {
        $gambar = $newFilename;
    }else{
        $_SESSION["error"] = "Gagal mengupload gambar";
        header("Location: ../../pages/admin/menus.php");
    }
    
    // insert ke database
    try{
        $stmt = $conn->prepare("INSERT INTO menu (nama, kategori_id, harga, gambar) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $kategori_id, $harga, $gambar]);
        
        $_SESSION["success"] = "Menu berhasil ditambahkan";
    }catch(PDOException $e){
        $_SESSION["error"] = "Gagal menambahkan menu";
    }

    header("Location: ../../pages/admin/menus.php");
    exit;
