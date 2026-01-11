<?php

require_once __DIR__ . '/../includes/database.php';

echo "================= Seeding Menu ===================\n";

try {
    $stmt = $conn->query("SELECT id, nama FROM kategori");
    $kategoriMap = [];
    
    while ($row = $stmt->fetch()) {
        $kategoriMap[$row['nama']] = $row['id'];
    }
    
    if (empty($kategoriMap)) {
        echo " Kategori belum ada! Jalankan seed_kategori.php dulu.\n";
        exit(1);
    }
    
    echo " Kategori ditemukan: " . implode(', ', array_keys($kategoriMap)) . "\n\n";
    
    $menus = [
        // MINUMAN
        ['nama' => 'Kopi Hitam', 'kategori' => 'Minuman', 'harga' => 8000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Kopi Susu', 'kategori' => 'Minuman', 'harga' => 12000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Kopi Susu Gula Aren', 'kategori' => 'Minuman', 'harga' => 15000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Es Kopi Susu', 'kategori' => 'Minuman', 'harga' => 13000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Cappuccino', 'kategori' => 'Minuman', 'harga' => 18000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Cafe Latte', 'kategori' => 'Minuman', 'harga' => 18000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Americano', 'kategori' => 'Minuman', 'harga' => 15000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Mochaccino', 'kategori' => 'Minuman', 'harga' => 20000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Kopi Tubruk', 'kategori' => 'Minuman', 'harga' => 7000, 'gambar' => 'minuman.jpeg'],
        
        ['nama' => 'Teh Tarik', 'kategori' => 'Minuman', 'harga' => 10000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Teh Manis', 'kategori' => 'Minuman', 'harga' => 5000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Es Teh Manis', 'kategori' => 'Minuman', 'harga' => 5000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Teh Poci', 'kategori' => 'Minuman', 'harga' => 8000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Lemon Tea', 'kategori' => 'Minuman', 'harga' => 12000, 'gambar' => 'minuman.jpeg'],
        
        ['nama' => 'Coklat Panas', 'kategori' => 'Minuman', 'harga' => 15000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Es Coklat', 'kategori' => 'Minuman', 'harga' => 15000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Susu Coklat', 'kategori' => 'Minuman', 'harga' => 12000, 'gambar' => 'minuman.jpeg'],
        
        ['nama' => 'Jeruk Peras', 'kategori' => 'Minuman', 'harga' => 10000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Es Jeruk', 'kategori' => 'Minuman', 'harga' => 8000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Jus Alpukat', 'kategori' => 'Minuman', 'harga' => 15000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Jus Mangga', 'kategori' => 'Minuman', 'harga' => 15000, 'gambar' => 'minuman.jpeg'],
        ['nama' => 'Jus Strawberry', 'kategori' => 'Minuman', 'harga' => 18000, 'gambar' => 'minuman.jpeg'],
        
        ['nama' => 'Air Mineral', 'kategori' => 'Minuman', 'harga' => 3000, 'gambar' => 'minuman.jpeg'],
        
        // MAKANAN
        ['nama' => 'Nasi Goreng Spesial', 'kategori' => 'Makanan', 'harga' => 18000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Nasi Goreng Biasa', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Nasi Goreng Seafood', 'kategori' => 'Makanan', 'harga' => 22000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Nasi Goreng Ayam', 'kategori' => 'Makanan', 'harga' => 18000, 'gambar' => 'makanan.jpg'],
        
        ['nama' => 'Mie Goreng', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Mie Rebus', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Mie Ayam', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Indomie Rebus', 'kategori' => 'Makanan', 'harga' => 8000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Indomie Goreng', 'kategori' => 'Makanan', 'harga' => 8000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Indomie Telur', 'kategori' => 'Makanan', 'harga' => 12000, 'gambar' => 'makanan.jpg'],
        
        ['nama' => 'Pisang Goreng', 'kategori' => 'Makanan', 'harga' => 10000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Pisang Goreng Keju', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Pisang Goreng Coklat', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        
        ['nama' => 'Roti Bakar Coklat', 'kategori' => 'Makanan', 'harga' => 12000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Roti Bakar Keju', 'kategori' => 'Makanan', 'harga' => 12000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Roti Bakar Coklat Keju', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Roti Bakar Selai', 'kategori' => 'Makanan', 'harga' => 10000, 'gambar' => 'makanan.jpg'],
        
        ['nama' => 'French Fries', 'kategori' => 'Makanan', 'harga' => 15000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Kentang Goreng Keju', 'kategori' => 'Makanan', 'harga' => 18000, 'gambar' => 'makanan.jpg'],
        
        ['nama' => 'Sosis Bakar', 'kategori' => 'Makanan', 'harga' => 12000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Sosis Goreng', 'kategori' => 'Makanan', 'harga' => 12000, 'gambar' => 'makanan.jpg'],
        
        ['nama' => 'Tahu Goreng', 'kategori' => 'Makanan', 'harga' => 8000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Tempe Goreng', 'kategori' => 'Makanan', 'harga' => 8000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Cireng', 'kategori' => 'Makanan', 'harga' => 10000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Bakwan Jagung', 'kategori' => 'Makanan', 'harga' => 10000, 'gambar' => 'makanan.jpg'],
        
        ['nama' => 'Singkong Goreng', 'kategori' => 'Makanan', 'harga' => 8000, 'gambar' => 'makanan.jpg'],
        ['nama' => 'Singkong Keju', 'kategori' => 'Makanan', 'harga' => 12000, 'gambar' => 'makanan.jpg'],
    ];
    
    // Prepare statements
    $checkStmt = $conn->prepare("SELECT id FROM menu WHERE nama = ?");
    $insertStmt = $conn->prepare(
        "INSERT INTO menu (nama, kategori_id, harga, gambar) VALUES (?, ?, ?, ?)"
    );
    
    $insertedCount = 0;
    $skippedCount = 0;
    
    foreach ($menus as $menu) {
        // Cek kategori ada
        if (!isset($kategoriMap[$menu['kategori']])) {
            echo " Kategori '{$menu['kategori']}' tidak ditemukan untuk menu '{$menu['nama']}'. Dilewati.\n";
            $skippedCount++;
            continue;
        }
        
        $kategoriId = $kategoriMap[$menu['kategori']];
        
        // Cek apakah menu sudah ada
        $checkStmt->execute([$menu['nama']]);
        
        if ($checkStmt->fetch()) {
            echo "⏭  '{$menu['nama']}' sudah ada. Melewati...\n";
            $skippedCount++;
        } else {
            $insertStmt->execute([
                $menu['nama'],
                $kategoriId,
                $menu['harga'],
                $menu['gambar']
            ]);
            echo " '{$menu['nama']}' - Rp " . number_format($menu['harga'], 0, ',', '.') . "\n";
            $insertedCount++;
        }
    }
    
    echo "\n Summary:\n";
    echo "   - Inserted: {$insertedCount}\n";
    echo "   - Skipped: {$skippedCount}\n";
    echo "   - Total: " . count($menus) . "\n";
    
} catch (PDOException $e) {
    echo " Error: " . $e->getMessage() . "\n";
    exit(1);
}