<?php
/**
 * Test Database Connection
 * Run: php database/test-connection.php
 */

require_once __DIR__ . '/../config/database.php';

echo "====================================\n";
echo "WARKOP QR - DATABASE CONNECTION TEST\n";
echo "====================================\n\n";

try {
    echo "Connecting to database...\n";
    $db = new Database();
    $conn = $db->connect();
    
    if ($conn) {
        echo "  Connection successful!\n\n";
        
        // Test 1: Check tables
        echo "1. Checking tables...\n";
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "   Found " . count($tables) . " tables:\n";
        foreach ($tables as $table) {
            echo "   - $table\n";
        }
        echo "\n";
        
        // Test 2: Count menus
        echo "2. Checking menus...\n";
        $stmt = $conn->query("SELECT category, COUNT(*) as total FROM menus GROUP BY category");
        $menus = $stmt->fetchAll();
        foreach ($menus as $menu) {
            echo "   - {$menu['category']}: {$menu['total']} items\n";
        }
        echo "\n";
        
        // Test 3: Count orders
        echo "3. Checking orders...\n";
        $stmt = $conn->query("SELECT status, COUNT(*) as total FROM orders GROUP BY status");
        $orders = $stmt->fetchAll();
        foreach ($orders as $order) {
            echo "   - {$order['status']}: {$order['total']} orders\n";
        }
        echo "\n";
        
        // Test 4: Check users
        echo "4. Checking users...\n";
        $stmt = $conn->query("SELECT username, role FROM users");
        $users = $stmt->fetchAll();
        foreach ($users as $user) {
            echo "   - {$user['username']} ({$user['role']})\n";
        }
        echo "\n";
        
        // Test 5: Check views
        echo "5. Testing views...\n";
        $stmt = $conn->query("SELECT COUNT(*) as total FROM v_orders_detail");
        $result = $stmt->fetch();
        echo "   - v_orders_detail: {$result['total']} rows\n";
        
        $stmt = $conn->query("SELECT COUNT(*) as total FROM v_menu_sales");
        $result = $stmt->fetch();
        echo "   - v_menu_sales: {$result['total']} rows\n";
        echo "\n";
        
        echo "====================================\n";
        echo "  All tests passed!\n";
        echo "Database is ready to use.\n";
        echo "====================================\n";
        
    } else {
        echo "  Connection failed!\n";
    }
    
} catch (Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Check if MySQL is running\n";
    echo "2. Verify credentials in config/database.php\n";
    echo "3. Make sure database 'warkop_qr' exists\n";
    echo "4. Run migration.sql first\n";
}
