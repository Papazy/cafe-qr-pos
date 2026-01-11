<?php

/**
 * Master Seeder
 * Jalankan file ini untuk seed semua data sekaligus
 * 
 * Usage: php database/seed_all.php
 */

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║         WARKOP QR - MASTER SEEDER                    ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

$startTime = microtime(true);

// Define seeder files in order
$seeders = [
    'seed_user.php'     => 'Seeding Admin User',
    'seed_kategori.php' => 'Seeding Kategori',
    'seed_menu.php'     => 'Seeding Menu',
];

$success = 0;
$failed = 0;

foreach ($seeders as $file => $description) {
    echo "┌─────────────────────────────────────────────────────┐\n";
    echo "│ {$description}\n";
    echo "└─────────────────────────────────────────────────────┘\n";
    
    $filePath = __DIR__ . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "  File tidak ditemukan: {$file}\n\n";
        $failed++;
        continue;
    }
    
    try {
        // Include dan jalankan seeder
        include $filePath;
        echo "\n  {$description} selesai!\n\n";
        $success++;
    } catch (Exception $e) {
        echo "  Error: " . $e->getMessage() . "\n\n";
        $failed++;
    }
}

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

// Summary
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║                    SUMMARY                           ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
echo "║    Success: {$success} seeders                           ║\n";
echo "║    Failed:  {$failed} seeders                           ║\n";
echo "║  ⏱️  Duration: {$duration} seconds                       ║\n";
echo "╚══════════════════════════════════════════════════════╝\n";

if ($failed === 0) {
    echo "\n🎉 Semua seeding berhasil! Database siap digunakan.\n";
} else {
    echo "\n⚠️  Ada seeder yang gagal. Silakan cek error di atas.\n";
    exit(1);
}