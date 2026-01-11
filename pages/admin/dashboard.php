<?php
require_once '../../includes/auth.php';

 
global $conn;

// Get today's statistics
$today = date('Y-m-d');

// Total orders today
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = ?");
$stmt->execute([$today]);
$totalOrdersToday = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Revenue today
$stmt = $conn->prepare("SELECT COALESCE(SUM(total), 0) as revenue FROM orders WHERE DATE(created_at) = ? AND status != 'Dibatalkan'");
$stmt->execute([$today]);
$revenueToday = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];

// Pending orders
$stmt = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Pending'");
$pendingOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Complete orders today
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = ? AND status = 'Selesai'");
$stmt->execute([$today]);
$completeOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Revenue last 7 days
$stmt = $conn->query("
    SELECT DATE(created_at) as date, COALESCE(SUM(total), 0) as revenue 
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status != 'Dibatalkan'
    GROUP BY DATE(created_at)
    ORDER BY date ASC
");
$revenueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE DATE(created_at) = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$today]);
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <?php include '../../components/admin/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                    <p class="text-sm text-gray-600">Selamat datang kembali, Admin!</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900"><?= date('d F Y') ?></p>
                    <p class="text-xs text-gray-600"><?= date('H:i') ?> WIB</p>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Orders Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900"><?= $totalOrdersToday ?></p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Revenue Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">Rp <?= number_format($revenueToday, 0, ',', '.') ?></p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Pending Orders</p>
                        <p class="text-3xl font-bold text-amber-600"><?= $pendingOrders ?></p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Complete Hari Ini</p>
                        <p class="text-3xl font-bold text-green-600"><?= $completeOrders ?></p>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue Trend (7 Hari Terakhir)</h3>
                    <div style="height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Pesanan Hari Ini</h3>
                        <a href="orders.php" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">
                            Lihat Semua →
                        </a>
                    </div>
                    
                    <?php if (empty($recentOrders)): ?>
                        <div class="text-center py-12 text-gray-500">
                            <p>Belum ada pesanan hari ini</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900">Order</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900">Meja</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900">Pelanggan</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900">Total</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900">Status</th>
                                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): 
                                        $statusColors = [
                                            'Pending' => 'bg-amber-100 text-amber-800',
                                            'Diproses' => 'bg-blue-100 text-blue-800',
                                            'Selesai' => 'bg-green-100 text-green-800',
                                            'Dibatalkan' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusClass = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($order['order_number']) ?></td>
                                            <td class="py-3 px-4 text-sm text-gray-600"><?= htmlspecialchars($order['table_number']) ?></td>
                                            <td class="py-3 px-4 text-sm text-gray-600"><?= htmlspecialchars($order['customer_name']) ?></td>
                                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full <?= $statusClass ?>">
                                                    <?= htmlspecialchars($order['status']) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-600"><?= date('H:i', strtotime($order['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueData = <?= json_encode($revenueData) ?>;
        const dates = revenueData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });
        const revenues = revenueData.map(d => parseFloat(d.revenue));

        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: revenues,
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 3,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
