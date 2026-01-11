<?php
require_once '../../includes/auth.php';
require_once '../../includes/database.php';
require_once '../../actions/admin/get-menus.php';

// data
$filter = $_GET['kategori'] ?? 'semua';
$kategoriList = getKategoriList($conn);
$menus = getMenus($conn, $filter);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-50">
    <?php include '../../components/notification.php'; ?>
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include '../../components/admin/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Kelola Menu</h2>
                    <p class="text-sm text-gray-600">Manajemen menu & kategori</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex gap-2">
                        <a href="menus.php?kategori=semua" class="filter-btn px-4 py-2 rounded-lg <?= $filter === 'semua' ? 'bg-indigo-600 text-white text-sm font-semibold' : 'bg-gray-200 text-gray-900 text-sm font-semibold hover:bg-gray-300' ?>">Semua</a>
                        <?php foreach ($kategoriList as $kategori): ?>
                            <a href="menus.php?kategori=<?= $kategori['nama'] ?>" class="filter-btn px-4 py-2 rounded-lg <?= $filter === $kategori['nama'] ? 'bg-indigo-600 text-white text-sm font-semibold' : 'bg-gray-200 text-gray-900 text-sm font-semibold hover:bg-gray-300' ?>"><?php echo $kategori['nama']; ?></a>
                        <?php endforeach; ?>
                    </div>
                    <button onclick="openAddModal()" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-semibold shadow-lg">
                        + Tambah Menu
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div id="menuList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                    <!-- Card menu -->
                    <?php foreach ($kategoriList as $kategori): ?>
                        <?php if ($filter !== 'semua' && $filter !== $kategori['nama']) continue; ?>
                        <div class="w-full col-span-1 md:col-span-2 lg:col-span-3 xl:col-span-4">
                            <h2 class="text-xl font-bold text-gray-900 mb-2 mt-4"><?= $kategori['nama'] ?></h2>
                        </div>
                        <?php $menusInCategory = array_filter($menus, fn($m) => $m['kategori_nama'] === $kategori['nama']); ?>
                        <?php foreach ($menusInCategory as $menu): ?>
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                                <!-- Gambar -->
                                <div class="h-48 bg-gradient-to-br from-indigo-100 to-purple-100 overflow-hidden">
                                    <img
                                        class="w-full h-48 object-cover"
                                        src="../../uploads/<?= htmlspecialchars($menu['gambar']) ?>"
                                        alt="<?= htmlspecialchars($menu['nama']) ?>">
                                </div>

                                <!-- Info -->
                                <div class="p-4">
                                    <!-- Badge kategori -->
                                    <div class="mb-2">
                                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                                            <?= $menu['kategori_nama'] ?>
                                        </span>
                                    </div>

                                    <!-- Nama menu -->
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        <?= $menu['nama'] ?>
                                    </h3>

                                    <!-- Harga -->
                                    <p class="text-xl font-bold text-indigo-600 mb-4">
                                        Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                                    </p>

                                    <!-- Actions -->
                                    <div class="flex gap-2">
                                        <button
                                            onclick='openEditModal(
                                            <?= $menu["id"] ?>,
                                            " <?= htmlspecialchars($menu["nama"], ENT_QUOTES) ?>",
                                            <?= $kategori["id"] ?>,
                                            <?= $menu["harga"] ?>, 
                                            "<?= htmlspecialchars($menu["gambar"] ?? "", ENT_QUOTES) ?>"
                                            )'
                                            class="flex-1 px-2 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold">
                                            Edit
                                        </button>
                                        <button onclick="openDeleteMenuModal(<?= $menu['id']; ?>, '<?= htmlspecialchars($menu['nama'], ENT_QUOTES) ?>')"
                                            class="px-2 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                </div>

                <div id="emptyState" class="text-center py-20 hidden">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Menu</h3>
                    <p class="text-gray-600 mb-4">Tambahkan menu untuk ditampilkan</p>
                    <button onclick="openAddModal()" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
                        + Tambah Menu Pertama
                    </button>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit Menu Modal -->
    <div id="menuModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Tambah Menu</h3>
                <button onclick="closeModal()" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="menuForm" class="p-6 space-y-4" action="../../actions/admin/create-menu.php" method="POST" enctype="multipart/form-data"">
                <input type="hidden" id="menuId" name="menuId">
                <div>
                    <label for="menuName" class="block text-sm font-semibold text-gray-900 mb-2">
                        Nama Menu <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        required
                        placeholder="Contoh: Kopi Hitam"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                </div>

                <div>
                    <label for="menuCategory" class="block text-sm font-semibold text-gray-900 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="menuCategory"
                        name="kategori_id"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent">

                        <?php foreach ($kategoriList as $kategori): ?>
                            <option value="<?= $kategori['id'] ?>"><?= htmlspecialchars($kategori['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="menuPrice" class="block text-sm font-semibold text-gray-900 mb-2">
                        Harga <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 font-semibold">Rp</span>
                        <input
                            type="number"
                            name="harga"
                            id="menuPrice"
                            required
                            min="0"
                            placeholder="10000"
                            class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label for="menuImage" class="block text-sm font-semibold text-gray-900 mb-2">
                        Gambar Menu
                    </label>
                    <!-- preview -->
                    <div id="imagePreviewContainer" class="mb-3 hidden">
                        <img
                            id="imagePreview"
                            class="w-full h-48 object-cover rounded-lg border-2 border-gray-300"
                            alt="preview">
                        <button type="button"
                            onclick="removeImage()"
                            class="mt-2 text-sm text-red-600 hover:text-red-700">
                            ✕ Hapus gambar
                        </button>
                    </div>
                    <!-- input -->
                    <input
                        type="file"
                        id="menuImage"
                        name="gambar"
                        accept="image/jpeg,image/png,image/jpg"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                        onchange="previewImage(event)">

                    <p class="text-xs text-gray-500 mt-1">
                        Format: JPEG, JPG, PNG (Max 2MB)
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="flex-1 px-4 py-3 border border-gray-300 text-gray-900 rounded-lg font-semibold hover:bg-gray-50">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-semibold">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Hapus Modal -->
    <div id="hapusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <!-- Header -->
            <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Hapus Menu</h3>
                    </div>
                </div>
            </div>

            <!-- body -->
            <div class="p-6">
                <p class="text-gray-700 mb-4">
                    Apakah Anda yakin ingin menghapus menu
                    <span id="deleteMenuName" class="font-bold text-gray-900"></span>?
                </p>
                <p class="text-sm text-gray-600">
                    Data menu akan dihapus secara permanen dari database.
                </p>
            </div>

            <!-- Footer -->
            <div class="px-6 pb-6 flex gap-3">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-900 rounded-lg font-semibold hover:bg-gray-50">
                    Batal
                </button>
                <form id="deleteForm" action="../../actions/admin/delete-menu.php" method="POST" class="flex-1">
                    <input type="hidden" id="deleteMenuId" name="menu_id">
                    <button
                        type="submit"
                        class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>

<script>
    function openAddModal() {
        // Reset form untuk mode tambah
        document.getElementById('menuForm').reset();
        document.getElementById('menuId').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Menu';
        document.getElementById('menuForm').action = '../../actions/admin/create-menu.php';

        // Sembunyikan preview gambar
        document.getElementById('imagePreviewContainer').classList.add('hidden');

        const modal = document.getElementById('menuModal')
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openEditModal(id, nama, kategoriId, harga, gambar) {
        console.log("opening edit modal for id:", id);
        // Isi form dengan data menu yang akan diedit
        document.getElementById('menuId').value = id;
        document.getElementById('nama').value = nama;
        document.getElementById('menuCategory').value = kategoriId;
        document.getElementById('menuPrice').value = harga;

        // Ubah judul & action form
        document.getElementById('modalTitle').textContent = 'Edit Menu';
        document.getElementById('menuForm').action = '../../actions/admin/edit-menu.php';

        // Tampilkan preview gambar lama jika ada
        if (gambar) {
            const imagePreview = document.getElementById('imagePreview');
            const previewContainer = document.getElementById('imagePreviewContainer');

            imagePreview.src = '../../uploads/' + gambar;
            previewContainer.classList.remove('hidden');
        } else {
            document.getElementById('imagePreviewContainer').classList.add('hidden');
        }

        // Buka modal
        const modal = document.getElementById('menuModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('menuModal')
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openDeleteMenuModal(menuId, menuName) {
        const modal = document.getElementById('hapusModal');
        const deleteMenuId = document.getElementById('deleteMenuId');
        const deleteMenuName = document.getElementById('deleteMenuName');

        deleteMenuId.value = menuId;
        deleteMenuName.textContent = menuName;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('hapusModal');
        modal.classList.add("hidden");
        modal.classList.remove('flex')
    }

    function previewImage(event) {
        const file = event.target.files[0];

        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('File yang dipilih bukan gambar.');
            event.target.value = '';
            return;
        }

        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert('Ukuran file terlalu besar. Maksimal 2MB');
            event.target.value = '';
            return;
        }

        // event ketika selesai baca file data
        const reader = new FileReader();

        reader.onload = function(e) {
            const imagePreview = document.getElementById('imagePreview');
            const previewContainer = document.getElementById('imagePreviewContainer');

            imagePreview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }

        // baca file data sebagai URL
        reader.readAsDataURL(file);
    }

    function removeImage() {
        const imagePreview = document.getElementById('imagePreview');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const imageInput = document.getElementById('menuImage');

        // reset
        imagePreview.src = '';
        previewContainer.classList.add('hidden');
        imageInput.value = '';
    }
</script>

</html>