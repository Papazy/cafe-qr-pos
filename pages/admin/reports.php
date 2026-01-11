<?php
require_once '../../includes/auth.php';

 
global $conn;

// Get filter period
$period = $_GET['period'] ?? 'today';

 
switch($period) {
    case 'week':
        $dateCondition = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $periodText = "Minggu Ini";
        break;
    case 'month':
        $dateCondition = "MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
        $periodText = "Bulan Ini";
        break;
    case 'all':
        $dateCondition = "1=1";
        $periodText = "Semua Waktu";
        break;
    default:
        $dateCondition = "DATE(created_at) = CURDATE()";
        $periodText = "Hari Ini";
}

// Get revenue and order stats
$stmt = $conn->query("
    SELECT 
        COUNT(*) as total_orders,
        COALESCE(SUM(CASE WHEN status != 'Dibatalkan' THEN total ELSE 0 END), 0) as total_revenue,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'Diproses' THEN 1 ELSE 0 END) as diproses
    FROM orders
    WHERE $dateCondition
");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
$avgOrder = $stats['total_orders'] > 0 ? $stats['total_revenue'] / $stats['total_orders'] : 0;

// Get top selling menu
$stmt = $conn->query("
    SELECT m.nama, SUM(oi.quantity) as total_qty, SUM(oi.subtotal) as total_revenue
    FROM order_items oi
    JOIN menu m ON oi.menu_id = m.id
    JOIN orders o ON oi.order_id = o.id
    WHERE " . str_replace('created_at', 'o.created_at', $dateCondition) . " AND o.status != 'Dibatalkan'
    GROUP BY m.id, m.nama
    ORDER BY total_qty DESC
    LIMIT 5
");
$topMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get revenue by category
$stmt = $conn->query("
    SELECT k.nama as category, SUM(oi.subtotal) as revenue
    FROM order_items oi
    JOIN menu m ON oi.menu_id = m.id
    JOIN kategori k ON m.kategori_id = k.id
    JOIN orders o ON oi.order_id = o.id
    WHERE " . str_replace('created_at', 'o.created_at', $dateCondition) . " AND o.status != 'Dibatalkan'
    GROUP BY k.id, k.nama
    ORDER BY revenue DESC
");
$categoryRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payment method stats
$stmt = $conn->query("
    SELECT payment_method, COUNT(*) as count, SUM(total) as revenue
    FROM orders
    WHERE $dateCondition AND status != 'Dibatalkan'
    GROUP BY payment_method
");
$paymentStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <?php include '../../components/admin/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h2>
                    <p class="text-sm text-gray-600"><?= $periodText ?></p>
                </div>
                <div class="flex gap-2">
                    <a href="?period=today" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $period === 'today' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900 hover:bg-gray-300' ?>">Hari Ini</a>
                    <a href="?period=week" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $period === 'week' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900 hover:bg-gray-300' ?>">Minggu Ini</a>
                    <a href="?period=month" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $period === 'month' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900 hover:bg-gray-300' ?>">Bulan Ini</a>
                    <a href="?period=all" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $period === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900 hover:bg-gray-300' ?>">Semua</a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Revenue Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-gray-900">Rp <?= number_format($stats['total_revenue'], 0, ',', '.') ?></p>
                        <p class="text-sm text-gray-500 mt-1"><?= $stats['total_orders'] ?> pesanan</p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Rata-rata Pesanan</p>
                        <p class="text-3xl font-bold text-gray-900">Rp <?= number_format($avgOrder, 0, ',', '.') ?></p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Selesai</p>
                        <p class="text-3xl font-bold text-green-600"><?= $stats['completed'] ?></p>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <p class="text-sm text-gray-600 mb-1">Pending / Diproses</p>
                        <p class="text-3xl font-bold text-amber-600"><?= $stats['pending'] + $stats['diproses'] ?></p>
                    </div>
                </div>

                <!-- Top Selling Menu -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Top 5 Menu Terlaris</h3>
                    <?php if (empty($topMenus)): ?>
                        <div class="text-center py-12 text-gray-500">
                            <p>Belum ada data penjualan</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php 
                            foreach ($topMenus as $index => $menu): 
                            ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="font-semibold text-gray-900"><?= htmlspecialchars($menu['nama']) ?></p>
                                            <p class="text-sm text-gray-600"><?= $menu['total_qty'] ?> terjual • Rp <?= number_format($menu['total_revenue'], 0, ',', '.') ?></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-indigo-600"><?= $menu['total_qty'] ?></p>
                                        <p class="text-xs text-gray-600">item</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Category Revenue -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Pendapatan per Kategori</h3>
                        <div style="height: 300px;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Metode Pembayaran</h3>
                        <div style="height: 300px;">
                            <canvas id="paymentChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Category Chart
        const categoryData = <?= json_encode($categoryRevenue) ?>;
        const categoryLabels = categoryData.map(d => d.category);
        const categoryValues = categoryData.map(d => parseFloat(d.revenue));

        if (categoryLabels.length > 0) {
            const ctxCategory = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxCategory, {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: categoryValues,
                        backgroundColor: ['#6366f1', '#22c55e', '#f59e0b', '#a855f7']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } },
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
        }

        // Payment Chart
        const paymentData = <?= json_encode($paymentStats) ?>;
        const paymentLabels = paymentData.map(d => d.payment_method);
        const paymentValues = paymentData.map(d => parseFloat(d.revenue));

        if (paymentLabels.length > 0) {
            const ctxPayment = document.getElementById('paymentChart').getContext('2d');
            new Chart(ctxPayment, {
                type: 'doughnut',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        data: paymentValues,
                        backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
