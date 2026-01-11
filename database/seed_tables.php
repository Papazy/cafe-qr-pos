<?php
/**
 * Seeder Script: Generate Tables dengan QR Tokens
 * Membuat data meja dengan QR token untuk sistem ordering
 */

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/qr-functions.php';

echo "🚀 Seeding tables dengan QR tokens...\n\n";

// Define tables layout
$tables = [

    ['number' => '1', 'capacity' => 2, 'zone' => 'indoor'],
    ['number' => '2', 'capacity' => 2, 'zone' => 'indoor'],
    ['number' => '3', 'capacity' => 4, 'zone' => 'indoor'],
    ['number' => '4', 'capacity' => 4, 'zone' => 'indoor'],
    ['number' => '5', 'capacity' => 4, 'zone' => 'indoor'],
    ['number' => '6', 'capacity' => 6, 'zone' => 'indoor'],
    ['number' => '7', 'capacity' => 6, 'zone' => 'indoor'],
    ['number' => '8', 'capacity' => 4, 'zone' => 'indoor'],
    
    ['number' => '9', 'capacity' => 4, 'zone' => 'outdoor'],
    ['number' => '10', 'capacity' => 4, 'zone' => 'outdoor'],
    ['number' => '11', 'capacity' => 6, 'zone' => 'outdoor'],
    ['number' => '12', 'capacity' => 2, 'zone' => 'outdoor'],
    
];

try {
    $conn->beginTransaction();
    
    // Clear existing tables (optional - comment out jika ingin keep existing)
    // $conn->exec("TRUNCATE TABLE tables");
    // echo "   Cleared existing tables\n\n";
    
    $successCount = 0;
    $skippedCount = 0;
    
    foreach ($tables as $table) {
        // Generate unique token
        $token = generateQRToken($table['number']);
        
        // Check if table already exists
        $checkStmt = $conn->prepare("SELECT id FROM tables WHERE table_number = ?");
        $checkStmt->execute([$table['number']]);
        
        if ($checkStmt->fetch()) {
            echo "   Table {$table['number']} already exists, skipping...\n";
            $skippedCount++;
            continue;
        }
        
        // Insert new table
        $stmt = $conn->prepare("
            INSERT INTO tables (table_number, qr_token, status)
            VALUES (?, ?, 'tersedia')
        ");
        
        $stmt->execute([
            $table['number'],
            $token
        ]);
        
        echo "  Table {$table['number']} created (Zone: {$table['zone']}, Capacity: {$table['capacity']})\n";
        echo "     Token: " . substr($token, 0, 16) . "...\n";
        echo "     QR URL: http://localhost:8000/?table={$table['number']}&token={$token}\n\n";
        
        $successCount++;
    }
    
    $conn->commit();
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "  Seeding completed!\n";
    echo "  Summary:\n";
    echo "   - Created: {$successCount} tables\n";
    echo "   - Skipped: {$skippedCount} tables (already exists)\n";
    echo "   - Total: " . count($tables) . " tables\n";
    echo str_repeat("=", 60) . "\n\n";
    
    echo "  Next steps:\n";
    echo "   1. Print QR codes: http://localhost:8000/pages/admin/print-qr.php\n";
    echo "   2. Test QR scan flow\n";
    echo "   3. Place QR codes on physical tables\n\n";
    
} catch (PDOException $e) {
    $conn->rollBack();
    echo "\n  Error: " . $e->getMessage() . "\n";
    echo "  Tip: Make sure the 'tables' table exists in your database.\n";
    echo "    Run schema.sql first if you haven't.\n\n";
    exit(1);
}
?>
