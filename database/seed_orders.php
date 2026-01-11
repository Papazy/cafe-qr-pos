<?php
/**
 * Seed Orders & Order Items
 * Sample data untuk testing fitur pesanan
 */

require_once __DIR__ . '/../includes/database.php';

try {
    echo "🌱 Seeding orders & order_items...\n\n";
    
    // Clear existing data
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $conn->exec("TRUNCATE TABLE order_items");
    $conn->exec("TRUNCATE TABLE orders");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Sample orders data
    $orders = [
        // Order 1 - Pending
        [
            'order_number' => 'WRK-20260105-0001',
            'table_number' => 5,
            'customer_name' => 'Budi Santoso',
            'notes' => 'Kopinya panas ya',
            'payment_method' => 'Cash',
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
            'items' => [
                ['menu_id' => 1, 'quantity' => 2], // 2x Kopi Hitam
                ['menu_id' => 15, 'quantity' => 1] // 1x Pisang Goreng
            ]
        ],
        
        // Order 2 - Diproses
        [
            'order_number' => 'WRK-20260105-0002',
            'table_number' => 3,
            'customer_name' => 'Siti Aminah',
            'notes' => 'Nasi gorengnya pedas sedang',
            'payment_method' => 'QRIS',
            'status' => 'Diproses',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'items' => [
                ['menu_id' => 26, 'quantity' => 1], // 1x Nasi Goreng Seafood
                ['menu_id' => 3, 'quantity' => 2]   // 2x Kopi Susu Gula Aren
            ]
        ],
        
        // Order 3 - Selesai
        [
            'order_number' => 'WRK-20260105-0003',
            'table_number' => 7,
            'customer_name' => 'Ahmad Rizki',
            'notes' => null,
            'payment_method' => 'Transfer',
            'status' => 'Selesai',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'items' => [
                ['menu_id' => 11, 'quantity' => 1], // 1x Teh Manis Hangat
                ['menu_id' => 36, 'quantity' => 2], // 2x Mie Goreng
                ['menu_id' => 19, 'quantity' => 1]  // 1x Risoles Mayo
            ]
        ],
        
        // Order 4 - Pending
        [
            'order_number' => 'WRK-20260105-0004',
            'table_number' => 12,
            'customer_name' => 'Dewi Lestari',
            'notes' => 'Tehnya dingin, es banyak',
            'payment_method' => 'Cash',
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
            'items' => [
                ['menu_id' => 12, 'quantity' => 2], // 2x Es Teh Manis
                ['menu_id' => 13, 'quantity' => 1], // 1x Es Jeruk
                ['menu_id' => 16, 'quantity' => 3]  // 3x Tahu Isi
            ]
        ],
        
        // Order 5 - Diproses
        [
            'order_number' => 'WRK-20260105-0005',
            'table_number' => 8,
            'customer_name' => 'Eko Prasetyo',
            'notes' => 'Roti bakarnya coklat keju',
            'payment_method' => 'QRIS',
            'status' => 'Diproses',
            'created_at' => date('Y-m-d H:i:s', strtotime('-45 minutes')),
            'items' => [
                ['menu_id' => 5, 'quantity' => 1],  // 1x Kopi Vietnam Drip
                ['menu_id' => 44, 'quantity' => 2]  // 2x Roti Bakar Coklat Keju
            ]
        ],
        
        // Order 6 - Selesai (kemarin)
        [
            'order_number' => 'WRK-20260104-0015',
            'table_number' => 2,
            'customer_name' => 'Rina Wijaya',
            'notes' => 'Paket komplit',
            'payment_method' => 'Cash',
            'status' => 'Selesai',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'items' => [
                ['menu_id' => 2, 'quantity' => 1],  // 1x Kopi Susu
                ['menu_id' => 25, 'quantity' => 1], // 1x Nasi Goreng Spesial
                ['menu_id' => 18, 'quantity' => 2]  // 2x Singkong Keju
            ]
        ],
        
        // Order 7 - Dibatalkan
        [
            'order_number' => 'WRK-20260105-0006',
            'table_number' => 10,
            'customer_name' => 'Fajar Ramadhan',
            'notes' => 'Batal karena terlalu lama',
            'payment_method' => 'Cash',
            'status' => 'Dibatalkan',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            'items' => [
                ['menu_id' => 7, 'quantity' => 1], // 1x Cappuccino
                ['menu_id' => 27, 'quantity' => 1] // 1x Nasi Goreng Kampung
            ]
        ],
        
        // Order 8 - Pending (baru masuk)
        [
            'order_number' => 'WRK-20260105-0007',
            'table_number' => 15,
            'customer_name' => 'Linda Sari',
            'notes' => null,
            'payment_method' => 'QRIS',
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 minutes')),
            'items' => [
                ['menu_id' => 9, 'quantity' => 2],  // 2x Matcha Latte
                ['menu_id' => 45, 'quantity' => 1], // 1x Roti Bakar Srikaya
                ['menu_id' => 20, 'quantity' => 2]  // 2x Lemper Ayam
            ]
        ]
    ];
    
    // Insert orders
    foreach ($orders as $orderData) {
        // Get menu prices for calculation
        $subtotal = 0;
        foreach ($orderData['items'] as $item) {
            $stmt = $conn->prepare("SELECT harga FROM menu WHERE id = ?");
            $stmt->execute([$item['menu_id']]);
            $menu = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($menu) {
                $subtotal += $menu['harga'] * $item['quantity'];
            }
        }
        
        $tax = 0; // No tax for now
        $total = $subtotal + $tax;
        
        // Insert order header
        $stmt = $conn->prepare("
            INSERT INTO orders 
            (order_number, table_number, customer_name, notes, payment_method, 
             subtotal, tax, total, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $orderData['order_number'],
            $orderData['table_number'],
            $orderData['customer_name'],
            $orderData['notes'],
            $orderData['payment_method'],
            $subtotal,
            $tax,
            $total,
            $orderData['status'],
            $orderData['created_at'],
            $orderData['created_at']
        ]);
        
        $orderId = $conn->lastInsertId();
        
        // Insert order items
        foreach ($orderData['items'] as $item) {
            $stmt = $conn->prepare("SELECT harga, nama FROM menu WHERE id = ?");
            $stmt->execute([$item['menu_id']]);
            $menu = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($menu) {
                $price = $menu['harga'];
                $itemSubtotal = $price * $item['quantity'];
                
                $stmt = $conn->prepare("
                    INSERT INTO order_items 
                    (order_id, menu_id, quantity, price, subtotal, created_at)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $orderId,
                    $item['menu_id'],
                    $item['quantity'],
                    $price,
                    $itemSubtotal,
                    $orderData['created_at']
                ]);
            }
        }
        
        echo "  Order {$orderData['order_number']} - {$orderData['customer_name']} (Status: {$orderData['status']}, Total: Rp " . number_format($total, 0, ',', '.') . ")\n";
    }
    
    echo "\n🎉 Seeding orders selesai!\n";
    echo "Total: " . count($orders) . " orders\n\n";
    
    // Show summary by status
    echo "  Summary by Status:\n";
    $stmt = $conn->query("
        SELECT status, COUNT(*) as total, SUM(total) as revenue
        FROM orders
        GROUP BY status
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   {$row['status']}: {$row['total']} orders - Rp " . number_format($row['revenue'], 0, ',', '.') . "\n";
    }
    
} catch (PDOException $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
