<?php
require_once '../../includes/auth.php';
require_once '../../includes/database.php';
require_once '../../includes/qr-functions.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $tableNumber = $_POST['table_number'] ?? '';
        
        if (empty($tableNumber)) {
            $_SESSION['error'] = 'Nomor meja harus diisi!';
        } else {
            try {
                $token = generateQRToken($tableNumber);
                $stmt = $conn->prepare("INSERT INTO tables (table_number, qr_token, status) VALUES (?, ?, 'tersedia')");
                $stmt->execute([$tableNumber, $token]);
                $_SESSION['success'] = "Meja $tableNumber berhasil ditambahkan!";
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Gagal menambahkan meja: ' . $e->getMessage();
            }
        }
        header('Location: tables.php');
        exit;
    }
    
    if ($action === 'regenerate') {
        $tableId = $_POST['table_id'] ?? '';
        $tableNumber = $_POST['table_number'] ?? '';
        
        try {
            $newToken = generateQRToken($tableNumber);
            $stmt = $conn->prepare("UPDATE tables SET qr_token = ? WHERE id = ?");
            $stmt->execute([$newToken, $tableId]);
            $_SESSION['success'] = "QR Token meja $tableNumber berhasil di-regenerate!";
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal regenerate QR: ' . $e->getMessage();
        }
        header('Location: tables.php');
        exit;
    }
    
    if ($action === 'delete') {
        $tableId = $_POST['table_id'] ?? '';
        
        try {
            // Check if table has active order
            $stmt = $conn->prepare("SELECT table_number, current_order_id FROM tables WHERE id = ?");
            $stmt->execute([$tableId]);
            $table = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($table && $table['current_order_id']) {
                $_SESSION['error'] = "Tidak dapat menghapus meja {$table['table_number']} karena sedang memiliki pesanan aktif!";
            } else {
                $stmt = $conn->prepare("DELETE FROM tables WHERE id = ?");
                $stmt->execute([$tableId]);
                $_SESSION['success'] = "Meja berhasil dihapus!";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Gagal menghapus meja: ' . $e->getMessage();
        }
        header('Location: tables.php');
        exit;
    }
}

// Fetch all tables
$stmt = $conn->query("
    SELECT 
        t.*,
        o.id as order_id,
        o.order_number,
        o.status as order_status,
        o.customer_name,
        o.total as order_total
    FROM tables t
    LEFT JOIN orders o ON t.current_order_id = o.id
    ORDER BY 
        CAST(t.table_number AS UNSIGNED) ASC,
        t.table_number ASC
");
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count stats
$totalTables = count($tables);
$tersedia = count(array_filter($tables, fn($t) => $t['status'] === 'tersedia'));
$ditempati = count(array_filter($tables, fn($t) => $t['status'] === 'ditempati'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Meja - Warkop QR Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <?php include '../../components/admin/sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Kelola Meja</h2>
                    <p class="text-sm text-gray-600">Manajemen meja dan QR Code</p>
                </div>
                <button onclick="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Meja
                </button>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Meja</p>
                                <p class="text-3xl font-bold text-gray-900"><?= $totalTables ?></p>
                            </div>
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Tersedia</p>
                                <p class="text-3xl font-bold text-green-600"><?= $tersedia ?></p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Ditempati</p>
                                <p class="text-3xl font-bold text-red-600"><?= $ditempati ?></p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Tables Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($tables as $table): ?>
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition overflow-hidden">
                            <!-- Header -->
                            <div class="p-4 <?= $table['status'] === 'tersedia' ? 'bg-green-500' : 'bg-red-500' ?> text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-2xl font-bold">Meja <?= htmlspecialchars($table['table_number']) ?></h3>
                                        <p class="text-sm opacity-90">
                                            <?= $table['status'] === 'tersedia' ? '✓ Tersedia' : '● Ditempati' ?>
                                        </p>
                                    </div>
                                    <div class="text-right text-xs opacity-75">
                                        ID: <?= $table['id'] ?>
                                    </div>
                                </div>
                            </div>

                            <!-- QR Code Preview -->
                            <div class="p-4 bg-gray-50 flex justify-center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode('http://localhost:8000/?table=' . $table['table_number'] . '&token=' . $table['qr_token']) ?>" 
                                     alt="QR Meja <?= $table['table_number'] ?>"
                                     class="rounded-lg shadow">
                            </div>

                            <!-- Order Info -->
                            <?php if ($table['order_id']): ?>
                                <div class="p-4 bg-amber-50 border-t border-amber-200">
                                    <p class="text-xs font-semibold text-amber-800 mb-1">Pesanan Aktif:</p>
                                    <p class="text-sm text-gray-900 font-bold"><?= htmlspecialchars($table['order_number']) ?></p>
                                    <p class="text-xs text-gray-600"><?= htmlspecialchars($table['customer_name']) ?></p>
                                    <p class="text-xs text-gray-600">Rp <?= number_format($table['order_total'], 0, ',', '.') ?></p>
                                </div>
                            <?php else: ?>
                                <div class="p-4 border-t border-gray-200">
                                    <p class="text-xs text-gray-500 text-center">Tidak ada pesanan aktif</p>
                                </div>
                            <?php endif; ?>

                            <!-- Actions -->
                            <div class="p-4 bg-gray-50 border-t border-gray-200 flex gap-2">
                                <button onclick="viewQR('<?= $table['table_number'] ?>', '<?= $table['qr_token'] ?>')" 
                                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded text-xs font-semibold">
                                    Lihat QR
                                </button>
                                <button onclick="regenerateQR(<?= $table['id'] ?>, '<?= $table['table_number'] ?>')" 
                                        class="flex-1 bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded text-xs font-semibold">
                                    Regenerate
                                </button>
                                <button onclick="deleteTable(<?= $table['id'] ?>, '<?= $table['table_number'] ?>', <?= $table['order_id'] ? 'true' : 'false' ?>)" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-xs font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Table Modal -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Tambah Meja Baru</h3>
                <form method="POST" action="tables.php">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Nomor Meja</label>
                        <input type="text" name="table_number" required 
                               placeholder="Contoh: 15 atau VIP-3"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        <p class="text-xs text-gray-600 mt-1">Bisa angka (1, 2, 3) atau text (VIP-1, A-1)</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeAddModal()" 
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 py-2 rounded-lg font-semibold">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-semibold">
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Modal -->
    <div id="qrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4">
            <div class="p-6 text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-2">QR Code - Meja <span id="qrTableNumber"></span></h3>
                <p class="text-sm text-gray-600 mb-4">Scan untuk order</p>
                
                <div class="mb-4 bg-gray-50 p-6 rounded-lg inline-block">
                    <img id="qrImage" src="" alt="QR Code" class="w-64 h-64 mx-auto">
                </div>
                
                <div class="mb-4 text-left bg-gray-50 p-3 rounded">
                    <p class="text-xs font-semibold text-gray-700 mb-1">URL:</p>
                    <input id="qrUrl" readonly 
                           class="w-full text-xs bg-white border border-gray-200 rounded px-2 py-1 font-mono">
                </div>
                
                <div class="flex gap-3">
                    <button onclick="closeQRModal()" 
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 py-2 rounded-lg font-semibold">
                        Tutup
                    </button>
                    <button onclick="downloadQR()" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-semibold">
                        Download QR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function viewQR(tableNumber, token) {
            const baseUrl = '<?= BASE_URL ?>';
            const url = `${baseUrl}/?table=${tableNumber}&token=${token}`;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=${encodeURIComponent(url)}`;
            
            document.getElementById('qrTableNumber').textContent = tableNumber;
            document.getElementById('qrImage').src = qrUrl;
            document.getElementById('qrUrl').value = url;
            document.getElementById('qrModal').classList.remove('hidden');
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }

        function downloadQR() {
            const tableNumber = document.getElementById('qrTableNumber').textContent;
            const qrImage = document.getElementById('qrImage').src;
            
            const link = document.createElement('a');
            link.href = qrImage;
            link.download = `QR_Meja_${tableNumber}.png`;
            link.click();
        }

        function regenerateQR(tableId, tableNumber) {
            if (confirm(`Regenerate QR token untuk Meja ${tableNumber}?\n\nQR Code lama tidak akan berfungsi lagi!`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'tables.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'regenerate';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'table_id';
                idInput.value = tableId;
                
                const numberInput = document.createElement('input');
                numberInput.type = 'hidden';
                numberInput.name = 'table_number';
                numberInput.value = tableNumber;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                form.appendChild(numberInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteTable(tableId, tableNumber, hasOrder) {
            if (hasOrder) {
                alert(`Tidak dapat menghapus Meja ${tableNumber} karena sedang memiliki pesanan aktif!`);
                return;
            }
            
            if (confirm(`Hapus Meja ${tableNumber}?\n\nTindakan ini tidak dapat dibatalkan!`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'tables.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'table_id';
                idInput.value = tableId;
                
                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
