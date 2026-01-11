<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

// Get table number from URL or session
$table_number = $_GET['table'] ?? $_SESSION['table_number'] ?? null;
if ($table_number) {
    $_SESSION['table_number'] = $table_number;
}

// Get filter category
$filter_kategori = $_GET['kategori'] ?? 'Semua';

// Cart will be handled by localStorage in frontend

// Fetch categories
$stmt_kategori = $conn->query("SELECT * FROM kategori ORDER BY nama");
$categories = $stmt_kategori->fetchAll(PDO::FETCH_ASSOC);

// Fetch menus
if ($filter_kategori === 'Semua') {
    $stmt_menu = $conn->prepare("
        SELECT m.*, k.nama as kategori_nama 
        FROM menu m 
        JOIN kategori k ON m.kategori_id = k.id 
        ORDER BY k.nama, m.nama
    ");
    $stmt_menu->execute();
} else {
    $stmt_menu = $conn->prepare("
        SELECT m.*, k.nama as kategori_nama 
        FROM menu m 
        JOIN kategori k ON m.kategori_id = k.id 
        WHERE k.nama = :kategori
        ORDER BY m.nama
    ");
    $stmt_menu->execute(['kategori' => $filter_kategori]);
}
$menus = $stmt_menu->fetchAll(PDO::FETCH_ASSOC);

// Category gradients mapping
function getCategoryGradient($kategori) {
    $gradients = [
        'Kopi' => 'from-amber-500 to-orange-600',
        'Non Kopi' => 'from-green-500 to-emerald-600',
        'Makanan' => 'from-red-500 to-pink-600',
        'Snack' => 'from-purple-500 to-indigo-600'
    ];
    return $gradients[$kategori] ?? 'from-gray-500 to-gray-600';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Menu - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Safe area untuk notch */
        @supports (padding: env(safe-area-inset-top)) {
            .header-safe { padding-top: env(safe-area-inset-top); }
            .bottom-safe { padding-bottom: env(safe-area-inset-bottom); }
        }
        /* Smooth scroll horizontal */
        .category-scroll {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .category-scroll::-webkit-scrollbar {
            display: none;
        }
        /* Remove tap highlight */
        * { -webkit-tap-highlight-color: transparent; }
        /* Loading skeleton */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .skeleton {
            animation: shimmer 2s infinite;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-20 bottom-safe">
    <!-- Header Sticky - Mobile Optimized -->
    <div class="sticky top-0 z-10 bg-white shadow-sm header-safe">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 sm:py-4">
            <div class="flex items-center justify-between">
                <a href="landing.php" class="text-gray-600 hover:text-gray-900 min-w-[44px] min-h-[44px] flex items-center justify-center -ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="text-center">
                    <h1 class="text-base sm:text-xl font-bold text-gray-900">Menu Warkop</h1>
                    <?php if ($table_number): ?>
                        <p class="text-xs sm:text-sm text-gray-500">Meja <?= htmlspecialchars($table_number) ?></p>
                    <?php endif; ?>
                </div>
                <a href="cart.php" class="relative text-gray-600 hover:text-gray-900 min-w-[44px] min-h-[44px] flex items-center justify-center -mr-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span id="header-cart-count" class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 bg-red-500 text-white text-[10px] sm:text-xs rounded-full w-4 h-4 sm:w-5 sm:h-5 items-center justify-center font-bold hidden">
                        0
                    </span>
                </a>
            </div>
        </div>

        <!-- Category Filter - Smooth Scroll -->
        <div class="px-3 sm:px-4 pb-2 sm:pb-3 overflow-x-auto category-scroll">
            <div class="flex gap-2 min-w-max">
                <a href="menu.php?kategori=Semua<?= $table_number ? "&table=$table_number" : '' ?>" 
                   class="px-3 sm:px-4 py-2 rounded-full font-medium text-xs sm:text-sm transition whitespace-nowrap min-h-[36px] flex items-center active:scale-95 <?= $filter_kategori === 'Semua' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Semua
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="menu.php?kategori=<?= urlencode($cat['nama']) ?><?= $table_number ? "&table=$table_number" : '' ?>" 
                       class="px-3 sm:px-4 py-2 rounded-full font-medium text-xs sm:text-sm transition whitespace-nowrap min-h-[36px] flex items-center active:scale-95 <?= $filter_kategori === $cat['nama'] ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        <?= htmlspecialchars($cat['nama']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Menu Grid - Mobile 2 Columns -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
        <?php if (empty($menus)): ?>
            <div class="text-center py-12 sm:py-16">
                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p class="text-sm sm:text-base text-gray-500">Tidak ada menu tersedia</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
                <?php foreach ($menus as $menu): 
                    $gradient = getCategoryGradient($menu['kategori_nama']);
                    $image_url = $menu['gambar'] ? '/uploads/' . $menu['gambar'] : null;
                ?>
                    <div class="bg-white rounded-lg sm:rounded-xl shadow-md hover:shadow-xl transition overflow-hidden active:scale-95">
                        <?php if ($image_url && file_exists(__DIR__ . '/..' . $image_url)): ?>
                            <div class="aspect-square overflow-hidden bg-gray-100">
                                <img src="<?= htmlspecialchars($image_url) ?>" 
                                     alt="<?= htmlspecialchars($menu['nama']) ?>"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            </div>
                        <?php else: ?>
                            <div class="aspect-square bg-gradient-to-br <?= $gradient ?> flex items-center justify-center">
                                <span class="text-4xl sm:text-5xl">☕</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-2 sm:p-3">
                            <div class="mb-2">
                                <span class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase">
                                    <?= htmlspecialchars($menu['kategori_nama']) ?>
                                </span>
                                <h3 class="font-bold text-gray-900 text-xs sm:text-sm line-clamp-2">
                                    <?= htmlspecialchars($menu['nama']) ?>
                                </h3>
                            </div>
                            <div class="space-y-2">
                                <span class="text-indigo-600 font-bold text-xs sm:text-sm block">
                                    Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                                </span>
                                
                                <!-- Stepper - Initially just + button -->
                                <div id="stepper-<?= $menu['id'] ?>" class="stepper-container" data-menu-id="<?= $menu['id'] ?>" data-menu-nama="<?= htmlspecialchars($menu['nama']) ?>" data-menu-harga="<?= $menu['harga'] ?>" data-menu-kategori="<?= htmlspecialchars($menu['kategori_nama']) ?>" data-menu-gambar="<?= htmlspecialchars($menu['gambar'] ?? '') ?>">
                                    <!-- Default: Only + button -->
                                    <button type="button" 
                                            onclick="addToCart(<?= $menu['id'] ?>)"
                                            class="add-btn w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2 px-3 rounded-lg hover:shadow-lg transition transform active:scale-95 min-h-[36px] sm:min-h-[40px] flex items-center justify-center gap-1 text-xs sm:text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah
                                    </button>
                                    
                                    <!-- After added: - qty + stepper -->
                                    <div class="stepper-controls hidden flex items-center gap-1.5 bg-gray-50 rounded-lg p-1">
                                        <button type="button" 
                                                onclick="decrementCart(<?= $menu['id'] ?>)"
                                                class="bg-white text-gray-700 rounded-md min-w-[36px] min-h-[36px] sm:min-w-[40px] sm:min-h-[40px] flex items-center justify-center hover:bg-gray-100 active:scale-90 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <div class="flex-1 text-center font-bold text-sm sm:text-base qty-display">0</div>
                                        <button type="button"
                                                onclick="incrementCart(<?= $menu['id'] ?>)"
                                                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md min-w-[36px] min-h-[36px] sm:min-w-[40px] sm:min-h-[40px] flex items-center justify-center hover:shadow-lg active:scale-90 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Toast Notification - Mobile Optimized -->
    <?php if (isset($_SESSION['toast'])): ?>
        <div id="toast" class="fixed top-16 sm:top-20 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full shadow-2xl z-50 text-xs sm:text-sm">
            <?= htmlspecialchars($_SESSION['toast']) ?>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('toast').remove();
            }, 2000);
        </script>
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>

    <!-- Floating Cart Button (Mobile) - Optimized -->
    <div id="floating-cart" class="fixed bottom-4 right-4 z-10 hidden" style="bottom: max(1rem, env(safe-area-inset-bottom))">
        <a href="cart.php" class="relative bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-3 sm:p-4 rounded-full shadow-2xl hover:shadow-3xl transition transform active:scale-90 block min-w-[56px] min-h-[56px] flex items-center justify-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span id="cart-count" class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 bg-red-500 text-white text-[10px] sm:text-xs rounded-full w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center font-bold">
                0
            </span>
        </a>
    </div>

    <script>
        // LocalStorage Cart Management
        function getCart() {
            const cart = localStorage.getItem('warkop_cart');
            return cart ? JSON.parse(cart) : [];
        }

        function saveCart(cart) {
            localStorage.setItem('warkop_cart', JSON.stringify(cart));
            updateCartUI();
        }

        function addToCart(menuId) {
            const container = document.querySelector(`#stepper-${menuId}`);
            const menuData = {
                menu_id: menuId,
                nama_menu: container.dataset.menuNama,
                harga: parseInt(container.dataset.menuHarga),
                nama_kategori: container.dataset.menuKategori,
                gambar: container.dataset.menuGambar,
                quantity: 1
            };

            let cart = getCart();
            const existingIndex = cart.findIndex(item => item.menu_id == menuId);
            
            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push(menuData);
            }

            saveCart(cart);
            showToast(`${menuData.nama_menu} ditambahkan!`);
        }

        function incrementCart(menuId) {
            let cart = getCart();
            const item = cart.find(item => item.menu_id == menuId);
            if (item && item.quantity < 99) {
                item.quantity += 1;
                saveCart(cart);
            }
        }

        function decrementCart(menuId) {
            let cart = getCart();
            const itemIndex = cart.findIndex(item => item.menu_id == menuId);
            
            if (itemIndex > -1) {
                cart[itemIndex].quantity -= 1;
                if (cart[itemIndex].quantity <= 0) {
                    cart.splice(itemIndex, 1);
                }
                saveCart(cart);
            }
        }

        function updateCartUI() {
            const cart = getCart();
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            // Update header cart badge
            const headerCartCount = document.getElementById('header-cart-count');
            if (totalItems > 0) {
                headerCartCount.classList.remove('hidden');
                headerCartCount.classList.add('flex');
                headerCartCount.textContent = totalItems;
            } else {
                headerCartCount.classList.add('hidden');
                headerCartCount.classList.remove('flex');
            }

            // Update floating cart badge
            const floatingCart = document.getElementById('floating-cart');
            const cartCount = document.getElementById('cart-count');
            if (totalItems > 0) {
                floatingCart.classList.remove('hidden');
                cartCount.textContent = totalItems;
            } else {
                floatingCart.classList.add('hidden');
            }

            // Update all stepper displays
            document.querySelectorAll('.stepper-container').forEach(container => {
                const menuId = container.dataset.menuId;
                const item = cart.find(item => item.menu_id == menuId);
                const addBtn = container.querySelector('.add-btn');
                const stepperControls = container.querySelector('.stepper-controls');
                const qtyDisplay = container.querySelector('.qty-display');
                
                if (item && item.quantity > 0) {
                    addBtn.classList.add('hidden');
                    stepperControls.classList.remove('hidden');
                    stepperControls.classList.add('flex');
                    qtyDisplay.textContent = item.quantity;
                } else {
                    addBtn.classList.remove('hidden');
                    stepperControls.classList.add('hidden');
                    stepperControls.classList.remove('flex');
                }
            });
        }

        function showToast(message) {
            const existingToast = document.getElementById('toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'fixed bottom-16 sm:bottom-20 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full shadow-2xl z-50 text-xs sm:text-sm';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 2000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Get and store table number from URL
            const urlParams = new URLSearchParams(window.location.search);
            const tableNumber = urlParams.get('table') || localStorage.getItem('tableNumber') || '1';
            
            // Store in localStorage for use in cart and checkout
            localStorage.setItem('tableNumber', tableNumber);
            
            // Update UI
            updateCartUI();
        });
    </script>
</body>
</html>
