<?php
    session_start();
    require_once '../../includes/database.php';

    if($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE'){
        header('Location: ../../pages/admin/menus.php');
        exit;
    }

    $menu_id = $_POST['menu_id'] ?? null;

    if(!$menu_id){
        $_SESSION["error"] = "Tidak ada menu yang mau dihapus";
        header('Location: ../../pages/admin/menus.php');
        exit;
    }

    try{

        $query = "SELECT * FROM menu WHERE id = ?";
        $checkStmt = $conn->prepare($query);
        $checkStmt->execute([$menu_id]);
        $menu = $checkStmt->fetch();
        
        if(!$menu){
            $_SESSION['error'] = "Data menu tidak ada";
            header('Location: ../../pages/admin/menus.php');
            exit;
        }
        
        // hapus gambar
        if(!empty($menu['gambar'])) {
            $imagePath = __DIR__ . '/../../uploads/' . $menu['gambar'];
            if(file_exists($imagePath)){
                unlink($imagePath);
            }
        }
        
        // hapus data
        $query = "DELETE FROM menu WHERE id = ?";
        $deleteStmt = $conn->prepare($query);
        $deleteStmt->execute([$menu_id]);
        
        $_SESSION['success'] = "Berhasil menghapus menu " . $menu["nama"];
    } catch(PDOException $e){
        $_SESSION['error'] = 'Gagal menghapus menu: ' . $e->getMessage();
    }

    header('Location: ../../pages/admin/menus.php');
    exit;
