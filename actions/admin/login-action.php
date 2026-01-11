<?php
    session_start();
    require_once '../../includes/database.php';

    // Check if form is submitted
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $query = "SELECT * FROM users WHERE nama = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nama]);

        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email'],
            ];

            header('Location: ../../pages/admin/dashboard.php');
            exit;
        }
    }

    header('Location: ../../pages/admin/login.php?error=1');
    exit;
    