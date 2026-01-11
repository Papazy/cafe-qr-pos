<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Keranjang - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Safe area */
        @supports (padding: env(safe-area-inset-top)) {
            .header-safe { padding-top: env(safe-area-inset-top); }
            .bottom-safe { padding-bottom: calc(env(safe-area-inset-bottom) + 8rem); }
        }
        /* Remove tap highlight */
        * { -webkit-tap-highlight-color: transparent; }
        /* Swipe gesture */
        .swipe-item {
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 bottom-safe">
    <!-- Header - Mobile Optimized -->
    <header class="bg-white shadow-md sticky top-0 z-10 header-safe">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 sm:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 sm:gap-3">
                    <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-900 min-w-[44px] min-h-[44px] flex items-center justify-center -ml-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-gray-900">Keranjang</h1>
                        <p class="text-[10px] sm:text-xs text-gray-600">Meja <span id="tableNumber">-</span></p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Cart Content - Mobile Optimized -->
    <div class="max-w-4xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
        <div id="emptyState" class="hidden text-center py-12 sm:py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gray-100 mb-4 sm:mb-6">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Keranjang Kosong</h3>
            <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">Belum ada menu yang ditambahkan</p>
            <a href="menu.php" class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transition active:scale-95 min-h-[48px] flex items-center justify-center">
                Lihat Menu
            </a>
        </div>

        <div id="cartItems" class="space-y-2 sm:space-y-3">
            <!-- Cart items will be inserted here -->
        </div>
    </div>

    <!-- Sticky Bottom Summary - Mobile Optimized -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl z-10" style="padding-bottom: env(safe-area-inset-bottom)">
        <div class="max-w-4xl mx-auto px-3 sm:px-4 py-3 sm:py-4">
            <div class="space-y-2 sm:space-y-3">
                <!-- Summary Rows -->
                <div class="flex justify-between items-center gap-3 pt-2 border-t border-gray-200">
                    <div>
                        <div class="text-xs sm:text-sm text-gray-600">Total</div>
                        <div class="text-xl sm:text-2xl font-bold text-gray-900" id="total">Rp 0</div>
                    </div>
                    <button onclick="checkout()" id="checkoutBtn" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-bold hover:shadow-xl transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] text-sm sm:text-base">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        document.addEventListener('DOMContentLoaded', function() {
            const tableNumber = localStorage.getItem('tableNumber') || '1';
            document.getElementById('tableNumber').textContent = tableNumber;
            loadCart();
        });

        function loadCart() {
            cart = JSON.parse(localStorage.getItem('warkop_cart')) || [];
            renderCart();
            updateSummary();
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const emptyState = document.getElementById('emptyState');
            const checkoutBtn = document.getElementById('checkoutBtn');

            if (cart.length === 0) {
                container.classList.add('hidden');
                emptyState.classList.remove('hidden');
                checkoutBtn.disabled = true;
                return;
            }

            container.classList.remove('hidden');
            emptyState.classList.add('hidden');
            checkoutBtn.disabled = false;

            container.innerHTML = cart.map(item => {
                const itemTotal = item.harga * item.quantity;
                const gradient = getCategoryGradient(item.nama_kategori);
                const imageUrl = item.gambar ? `/uploads/${item.gambar}` : null;
                
                return `
                    <div class="bg-white rounded-lg sm:rounded-xl shadow-md hover:shadow-lg transition overflow-hidden swipe-item">
                        <div class="flex gap-3 sm:gap-4 p-3 sm:p-4">
                            <!-- Image - Smaller on Mobile -->
                            <div class="flex-shrink-0">
                                ${imageUrl ? `
                                    <img src="${imageUrl}" alt="${item.nama_menu}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover">
                                ` : `
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg bg-gradient-to-br ${gradient} flex items-center justify-center">
                                        <span class="text-2xl">☕</span>
                                    </div>
                                `}
                            </div>

                            <!-- Info - Compact -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <span class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase">${item.nama_kategori}</span>
                                        <h3 class="font-bold text-gray-900 text-sm sm:text-base truncate">${item.nama_menu}</h3>
                                        <p class="text-xs sm:text-sm text-indigo-600 font-semibold">Rp ${item.harga.toLocaleString('id-ID')}</p>
                                    </div>
                                    <button onclick="removeFromCart(${item.menu_id})" class="text-red-500 hover:text-red-700 p-1 transition min-w-[32px] min-h-[32px] flex items-center justify-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Quantity Controls - Touch Friendly (44px) -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 bg-gray-100 rounded-full px-1 py-1">
                                        <button onclick="updateQuantity(${item.menu_id}, -1)" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-gray-700 hover:bg-indigo-100 hover:text-indigo-600 flex items-center justify-center font-bold transition shadow-sm active:scale-90">
                                            -
                                        </button>
                                        <span class="font-bold text-gray-900 w-8 text-center text-sm sm:text-base">${item.quantity}</span>
                                        <button onclick="updateQuantity(${item.menu_id}, 1)" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-gray-700 hover:bg-indigo-100 hover:text-indigo-600 flex items-center justify-center font-bold transition shadow-sm active:scale-90">
                                            +
                                        </button>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] sm:text-xs text-gray-500">Subtotal</div>
                                        <div class="font-bold text-gray-900 text-sm sm:text-base">Rp ${itemTotal.toLocaleString('id-ID')}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function getCategoryGradient(category) {
            const gradients = {
                'Kopi': 'from-amber-500 to-orange-600',
                'Non Kopi': 'from-green-500 to-emerald-600',
                'Makanan': 'from-red-500 to-pink-600',
                'Snack': 'from-purple-500 to-indigo-600'
            };
            return gradients[category] || 'from-gray-500 to-gray-600';
        }

        function updateQuantity(menuId, change) {
            const item = cart.find(i => i.menu_id == menuId);
            if (!item) return;

            // Check if quantity will become 0 or less
            const newQuantity = item.quantity + change;
            
            if (newQuantity <= 0) {
                // Show confirmation before removing
                if (confirm('Hapus item dari keranjang?')) {
                    cart = cart.filter(i => i.menu_id != menuId);
                    localStorage.setItem('warkop_cart', JSON.stringify(cart));
                    renderCart();
                    updateSummary();
                }
                // If cancelled, do nothing - quantity stays the same
                return;
            }

            // Update quantity if > 0
            item.quantity = newQuantity;
            localStorage.setItem('warkop_cart', JSON.stringify(cart));
            renderCart();
            updateSummary();
        }

        function removeFromCart(menuId) {
            if (confirm('Hapus item dari keranjang?')) {
                cart = cart.filter(item => item.menu_id != menuId);
                localStorage.setItem('warkop_cart', JSON.stringify(cart));
                renderCart();
                updateSummary();
            }
        }

        function updateSummary() {
            const total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);

            document.getElementById('total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        }

        function checkout() {
            if (cart.length === 0) {
                alert('Keranjang masih kosong!');
                return;
            }
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html>
