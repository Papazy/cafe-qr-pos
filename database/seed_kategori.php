<?php

require '../includes/database.php';

// User collection
$kategori = [
    ['nama' => 'Minuman'],
    ['nama' => 'Makanan'],
];

echo "================= Seeding Kategori ===================\n";

$checkStmt = $conn->prepare("SELECT * FROM kategori WHERE nama = ?");
$insertStmt = $conn->prepare("INSERT INTO kategori (nama) VALUES (?)" );

$insertedCount = 0;
$skippedCount = 0;

foreach ($kategori as $kat) {
    // check
    $checkStmt->execute([$kat['nama']]);

    if($checkStmt->fetch()) {
        echo "Kategori '{$kat['nama']}' sudah ada. Melewati...\n";
        $skippedCount++;
        continue;
    }else{
        $insertStmt->execute([$kat['nama']]);
        echo "Kategori '{$kat['nama']}' berhasil ditambahkan.\n";
        $insertedCount++;
    }
}


echo "Kategori berhasil ditambahkan.\n";
echo "- Total kategori ditambahkan: $insertedCount\n";
echo "- Total kategori dilewati: $skippedCount\n";