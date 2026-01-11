<?php 
    $host = "localhost";
    $db_name = "warkop_qr";
    $username = "root";
    $password = "";

    try {
        $conn = new PDO(
            "mysql:host=$host;dbname=$db_name;charset=utf8",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]

            );
    }catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
