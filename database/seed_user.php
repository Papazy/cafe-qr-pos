<?php

require '../includes/database.php';

// User collection
$users = [
    'nama' => 'Admin',
    'email' => 'admin@gmail.com',
    'password' => password_hash('admin123', PASSWORD_DEFAULT)
];

echo "================= Seeding Admin ===================\n";

// Check
$stmt = $conn->prepare("SELECT * FROM users WHERE nama = ?");
$stmt->execute([$users['nama']]);

if ($stmt->rowCount() > 0) {
    echo "Admin sudah ada. Proses seeding dilewati.\n";
} else{

    
    // Insert
    $stmt = $conn->prepare(
        "INSERT INTO users (nama, email, password) VALUES (? , ?, ?)"
    );
    
    $stmt->execute([
        $users['nama'],
        $users['email'],
        $users['password']
    ]);
    
    echo "Admin berhasil ditambahkan.\n";
}