<?php
require_once '../../includes/auth.php';

 
global $conn;
require_once '../../actions/admin/get-orders.php';

// Get filter parameters
$filterStatus = $_GET['status'] ?? 'semua';
$filterDate = $_GET['date'] ?? 'hari-ini';
$filterTable = $_GET['table'] ?? 'semua';
$filterPayment = $_GET['payment'] ?? 'semua';

// Get data
$orders = getOrders($conn, $filterStatus, $filterDate, $filterTable === 'semua' ? null : $filterTable, $filterPayment === 'semua' ? null : $filterPayment);
$tableNumbers = getTableNumbers($conn);
$stats = getOrderStats($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulse-border {
            0%, 100% { border-color: rgb(249, 115, 22); }
            50% { border-color: rgb(251, 146, 60); }
        }
        .cash-pending-card {
            animation: pulse-border 2s ease-in-out infinite;
            border: 3px solid rgb(249, 115, 22);
        }
        .cash-badge {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            animation: pulse 2s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include '../../components/notification.php'; ?>
    
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include '../../components/admin/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Kelola Pesanan</h2>
                        <p class="text-sm text-gray-600">Manajemen pesanan customer</p>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="flex gap-4">
                        <div class="bg-yellow-50 px-4 py-2 rounded-lg">
                            <p class="text-xs text-yellow-700 font-semibold">Pending</p>
                            <p class="text-xl font-bold text-yellow-700"><?= $stats['pending_count'] ?? 0 ?></p>
                        </div>
                        <div class="bg-blue-50 px-4 py-2 rounded-lg">
                            <p class="text-xs text-blue-700 font-semibold">Diproses</p>
                            <p class="text-xl font-bold text-blue-700"><?= $stats['diproses_count'] ?? 0 ?></p>
                        </div>
                        <div class="bg-green-50 px-4 py-2 rounded-lg">
                            <p class="text-xs text-green-700 font-semibold">Selesai</p>
                            <p class="text-xl font-bold text-green-700"><?= $stats['selesai_count'] ?? 0 ?></p>
                        </div>
                        <div class="bg-indigo-50 px-4 py-2 rounded-lg">
                            <p class="text-xs text-indigo-700 font-semibold">Revenue Hari Ini</p>
                            <p class="text-lg font-bold text-indigo-700">Rp <?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Filters -->
            <div class="bg-white border-b border-gray-200 px-6 py-3">
                <!-- Quick Filters -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-sm font-semibold text-gray-700">Quick Filter:</span>
                    <a href="?status=Pending&payment=Cash&date=hari-ini" 
                       class="px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg text-sm font-semibold flex items-center gap-1">
                        Belum Bayar
                    </a>
                    <a href="?status=Diproses&date=hari-ini" 
                       class="px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg text-sm font-semibold">
                        Sedang Diproses
                    </a>
                    <a href="?status=Selesai&date=hari-ini" 
                       class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-semibold">
                        Selesai
                    </a>
                    <a href="?status=semua&date=hari-ini" 
                       class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">
                        Reset Filter
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Status Filter -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-gray-700">Status:</label>
                        <select onchange="window.location.href='?status='+this.value+'&date=<?= $filterDate ?>&table=<?= $filterTable ?>'"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-600">
                            <option value="semua" <?= $filterStatus === 'semua' ? 'selected' : '' ?>>Semua</option>
                            <option value="Pending" <?= $filterStatus === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Diproses" <?= $filterStatus === 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Selesai" <?= $filterStatus === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="Dibatalkan" <?= $filterStatus === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-gray-700">Tanggal:</label>
                        <select onchange="window.location.href='?status=<?= $filterStatus ?>&date='+this.value+'&table=<?= $filterTable ?>'"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-600">
                            <option value="hari-ini" <?= $filterDate === 'hari-ini' ? 'selected' : '' ?>>Hari Ini</option>
                            <option value="minggu-ini" <?= $filterDate === 'minggu-ini' ? 'selected' : '' ?>>Minggu Ini</option>
                            <option value="bulan-ini" <?= $filterDate === 'bulan-ini' ? 'selected' : '' ?>>Bulan Ini</option>
                            <option value="semua" <?= $filterDate === 'semua' ? 'selected' : '' ?>>Semua</option>
                        </select>
                    </div>

                    <!-- Table Search -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-semibold text-gray-700">Meja:</label>
                        <form method="GET" class="flex gap-2">
                            <input type="hidden" name="status" value="<?= $filterStatus ?>">
                            <input type="hidden" name="date" value="<?= $filterDate ?>">
                            <input 
                                type="number" 
                                name="table" 
                                value="<?= $filterTable !== 'semua' ? $filterTable : '' ?>"
                                placeholder="Cari nomor meja..."
                                min="1"
                                class="w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-600">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">
                                Cari
                            </button>
                            <?php if ($filterTable !== 'semua'): ?>
                                <a href="?status=<?= $filterStatus ?>&date=<?= $filterDate ?>&table=semua"
                                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg text-sm font-semibold">
                                    Reset
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="ml-auto text-sm text-gray-600">
                        Total: <span class="font-bold"><?= count($orders) ?></span> pesanan
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <main class="flex-1 overflow-y-auto p-6">
                <?php if (empty($orders)): ?>
                    <!-- Empty State -->
                    <div class="text-center py-20">
                        <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                        <p class="text-gray-600">Pesanan customer akan muncul di sini</p>
                    </div>
                <?php else: ?>
                    <!-- Orders Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($orders as $order): 
                            // Determine status color
                            $statusColors = [
                                'Pending' => 'bg-yellow-100 text-yellow-700',
                                'Diproses' => 'bg-blue-100 text-blue-700',
                                'Selesai' => 'bg-green-100 text-green-700',
                                'Dibatalkan' => 'bg-red-100 text-red-700'
                            ];
                            $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                            
                            // Check if pending cash payment
                            $isPendingCash = ($order['status'] === 'Pending' && $order['payment_method'] === 'Cash');
                            $cardClass = $isPendingCash ? 'cash-pending-card' : '';
                        ?>
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition <?= $cardClass ?>">
                                <!-- Header -->
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-white text-sm"><?= $order['order_number'] ?></p>
                                            <p class="text-indigo-100 font-bold text-lg">Meja <?= $order['table_number'] ?></p>
                                        </div>
                                        <div class="flex flex-col gap-1 items-end">
                                            <span class="<?= $statusColor ?> px-3 py-1 rounded-full text-xs font-bold">
                                                <?= $order['status'] ?>
                                            </span>
                                            <?php if ($isPendingCash): ?>
                                                <span class="cash-badge text-white px-3 py-1 rounded-full text-xs font-bold">
                                                    Belum Bayar (Cash)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <!-- Customer Info -->
                                    <div class="mb-3">
                                        <p class="text-gray-900 font-bold text-lg"><?= htmlspecialchars($order['customer_name']) ?></p>
                                        <p class="text-gray-600 text-sm">
                                            Pukul <?= date('H:i, d M Y', strtotime($order['created_at'])) ?>
                                        </p>
                                        <?php if (!empty($order['notes'])): ?>
                                            <p class="text-gray-700 text-sm mt-1 bg-gray-50 p-2 rounded">
                                                <span class="font-semibold">Catatan:</span> <?= htmlspecialchars($order['notes']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Order Summary -->
                                    <div class="border-t border-gray-200 pt-3 mb-3">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Total Item:</span>
                                            <span class="font-semibold text-gray-900"><?= $order['total_items'] ?> item</span>
                                        </div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Pembayaran:</span>
                                            <span class="font-semibold text-gray-900"><?= $order['payment_method'] ?></span>
                                        </div>
                                        <div class="flex justify-between text-lg mt-2">
                                            <span class="font-bold text-gray-900">Total:</span>
                                            <span class="font-bold text-indigo-600">Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2">
                                        <button onclick="openDetailModal(<?= $order['id'] ?>)"
                                                class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg text-sm font-semibold">
                                            Detail
                                        </button>
                                        
                                        <?php if ($order['status'] === 'Pending'): ?>
                                            <form action="../../actions/admin/update-order-status.php" method="POST" class="flex-1">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="new_status" value="Diproses">
                                                <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold">
                                                    Proses
                                                </button>
                                            </form>
                                        <?php elseif ($order['status'] === 'Diproses'): ?>
                                            <form action="../../actions/admin/update-order-status.php" method="POST" class="flex-1">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="new_status" value="Selesai">
                                                <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">
                                                    Selesai
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <button onclick="openDeleteModal(<?= $order['id'] ?>, '<?= htmlspecialchars($order['order_number'], ENT_QUOTES) ?>')"
                                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Loading State -->
            <div id="detailLoading" class="p-12 text-center">
                <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-indigo-600 border-r-transparent"></div>
                <p class="mt-4 text-gray-600">Memuat detail pesanan...</p>
            </div>

            <!-- Content -->
            <div id="detailContent" class="hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white" id="detailOrderNumber"></h3>
                            <p class="text-indigo-100 text-sm" id="detailDateTime"></p>
                        </div>
                        <button onclick="closeDetailModal()" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <!-- Customer & Table Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Nama Customer</p>
                            <p class="text-lg font-bold text-gray-900" id="detailCustomerName"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Nomor Meja</p>
                            <p class="text-lg font-bold text-gray-900" id="detailTableNumber"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Metode Pembayaran</p>
                            <p class="text-lg font-bold text-gray-900" id="detailPaymentMethod"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Status</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-bold" id="detailStatus"></span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div id="detailNotesSection" class="mb-6 pb-6 border-b border-gray-200 hidden">
                        <p class="text-sm text-gray-600 mb-2">Catatan Pesanan</p>
                        <p class="text-gray-900 italic" id="detailNotes"></p>
                    </div>

                    <!-- Items List -->
                    <div class="mb-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Detail Pesanan</h4>
                        <div class="space-y-3" id="detailItems"></div>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between text-xl pt-3">
                            <span class="font-bold text-gray-900">Total</span>
                            <span id="detailTotal" class="font-bold text-indigo-600"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button onclick="closeDetailModal()" 
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg font-semibold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Hapus Pesanan</h3>
                </div>
            </div>

            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Apakah Anda yakin ingin menghapus pesanan
                    <span id="deleteOrderNumber" class="font-bold text-gray-900"></span>?
                </p>
                <p class="text-sm text-gray-600">
                    Data pesanan akan dihapus secara permanen.
                </p>
            </div>

            <div class="px-6 pb-6 flex gap-3">
                <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-3 border border-gray-300 text-gray-900 rounded-lg font-semibold hover:bg-gray-50">
                    Batal
                </button>
                <form id="deleteForm" action="../../actions/admin/delete-order.php" method="POST" class="flex-1">
                    <input type="hidden" id="deleteOrderId" name="order_id">
                    <button type="submit"
                            class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Detail Modal
        async function openDetailModal(orderId) {
            const modal = document.getElementById('detailModal');
            const loading = document.getElementById('detailLoading');
            const content = document.getElementById('detailContent');
            
            // Show modal with loading
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            
            try {
                const response = await fetch(`../../actions/admin/get-order-detail-api.php?id=${orderId}`);
                const result = await response.json();
                
                if (!result.success) {
                    alert(result.message);
                    closeDetailModal();
                    return;
                }
                
                const order = result.data;
                
                // Populate data
                document.getElementById('detailOrderNumber').textContent = order.order_number;
                document.getElementById('detailDateTime').textContent = formatDateTime(order.created_at);
                document.getElementById('detailCustomerName').textContent = order.customer_name;
                document.getElementById('detailTableNumber').textContent = 'Meja ' + order.table_number;
                document.getElementById('detailPaymentMethod').textContent = order.payment_method;
                
                // Status badge
                const statusElement = document.getElementById('detailStatus');
                const statusColors = {
                    'Pending': 'bg-yellow-100 text-yellow-700',
                    'Diproses': 'bg-blue-100 text-blue-700',
                    'Selesai': 'bg-green-100 text-green-700',
                    'Dibatalkan': 'bg-red-100 text-red-700'
                };
                statusElement.className = 'inline-block px-3 py-1 rounded-full text-sm font-bold ' + (statusColors[order.status] || 'bg-gray-100 text-gray-700');
                statusElement.textContent = order.status;
                
                // Notes
                if (order.notes) {
                    document.getElementById('detailNotesSection').classList.remove('hidden');
                    document.getElementById('detailNotes').textContent = order.notes;
                } else {
                    document.getElementById('detailNotesSection').classList.add('hidden');
                }
                
                // Items
                const itemsContainer = document.getElementById('detailItems');
                itemsContainer.innerHTML = order.items.map(item => `
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${item.menu_name}</p>
                            <p class="text-sm text-gray-600">${item.kategori_name}</p>
                            ${item.notes ? `<p class="text-sm text-gray-700 italic mt-1">${item.notes}</p>` : ''}
                        </div>
                        <div class="text-right">
                            <p class="text-gray-900 font-semibold">${item.quantity}x</p>
                            <p class="text-sm text-gray-600">@ Rp ${formatNumber(item.price)}</p>
                        </div>
                        <div class="text-right ml-6 min-w-[120px]">
                            <p class="font-bold text-gray-900">Rp ${formatNumber(item.subtotal)}</p>
                        </div>
                    </div>
                `).join('');
                
                // Total (no tax)
                document.getElementById('detailTotal').textContent = 'Rp ' + formatNumber(order.total);
                
                // Show content, hide loading
                loading.classList.add('hidden');
                content.classList.remove('hidden');
                
            } catch (error) {
                alert('Gagal memuat detail pesanan: ' + error.message);
                closeDetailModal();
            }
        }
        
        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Delete Modal
        function openDeleteModal(orderId, orderNumber) {
            const modal = document.getElementById('deleteModal');
            document.getElementById('deleteOrderId').value = orderId;
            document.getElementById('deleteOrderNumber').textContent = orderNumber;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        // Helper functions
        function formatNumber(num) {
            return parseFloat(num).toLocaleString('id-ID');
        }
        
        function formatDateTime(datetime) {
            const date = new Date(datetime);
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('id-ID', options);
        }
    </script>
</body>

</html>
