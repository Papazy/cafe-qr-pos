<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Checkout - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Safe area */
        @supports (padding: env(safe-area-inset-top)) {
            .header-safe { padding-top: env(safe-area-inset-top); }
            .bottom-safe { padding-bottom: calc(env(safe-area-inset-bottom) + 5rem); }
        }
        /* Remove tap highlight */
        * { -webkit-tap-highlight-color: transparent; }
        /* Collapsible transition */
        .collapsible {
            transition: max-height 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 bottom-safe">
    <!-- Header - Mobile Optimized -->
    <header class="bg-white shadow-md sticky top-0 z-10 header-safe">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 sm:py-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-900 min-w-[44px] min-h-[44px] flex items-center justify-center -ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-gray-900">Checkout</h1>
                    <p class="text-[10px] sm:text-xs text-gray-600">Meja <span id="tableNumber">-</span></p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content - Mobile First -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6">
        <div class="grid grid-cols-1 gap-4 sm:gap-6">
            <!-- Order Form -->
            <div class="space-y-3 sm:space-y-4">
                <!-- Customer Info Card - Touch Friendly -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Pelanggan
                    </h2>
                    <div class="space-y-3 sm:space-y-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Nama</label>
                            <input type="text" id="customerName" class="w-full px-3 sm:px-4 py-3 text-base rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-transparent" placeholder="Masukkan nama Anda" required>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">Catatan (Opsional)</label>
                            <textarea id="notes" rows="3" class="w-full px-3 sm:px-4 py-3 text-base rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-600 focus:border-transparent" placeholder="Tambahan catatan untuk pesanan Anda"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Card - Mobile Optimized -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Metode Pembayaran
                    </h2>
                    <div class="grid grid-cols-1 gap-2 sm:gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment" value="Cash" class="peer sr-only" checked>
                            <div class="bg-gray-50 border-2 border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4 flex items-center gap-3 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-indigo-300 transition active:scale-98 min-h-[56px]">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 text-sm sm:text-base">Cash</div>
                                    <div class="text-xs text-gray-500">Tunai</div>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment" value="Transfer" class="peer sr-only">
                            <div class="bg-gray-50 border-2 border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4 flex items-center gap-3 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-indigo-300 transition active:scale-98 min-h-[56px]">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 text-sm sm:text-base">Transfer</div>
                                    <div class="text-xs text-gray-500">Bank Transfer</div>
                                </div>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment" value="E-Wallet" class="peer sr-only">
                            <div class="bg-gray-50 border-2 border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4 flex items-center gap-3 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 hover:border-indigo-300 transition active:scale-98 min-h-[56px]">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-600 peer-checked:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 text-sm sm:text-base">E-Wallet</div>
                                    <div class="text-xs text-gray-500">OVO/GoPay/Dana</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Order Summary - Mobile Optimized -->
            <div>
                <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-4 sm:p-6">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4">Ringkasan Pesanan</h2>
                    
                    <!-- Order Items -->
                    <div id="orderItems" class="space-y-2 sm:space-y-3 mb-3 sm:mb-4 max-h-48 sm:max-h-64 overflow-y-auto">
                        <!-- Items will be inserted here -->
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 my-3 sm:my-4"></div>

                    <!-- Summary -->
                    <div class="space-y-2 text-xs sm:text-sm">
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-base sm:text-lg font-bold text-gray-900">Total</span>
                            <span class="text-xl sm:text-2xl font-bold text-indigo-600" id="total">Rp 0</span>
                        </div>
                    </div>

                    <!-- Submit Button - Mobile Sticky -->
                    <button onclick="submitOrder()" class="w-full mt-4 sm:mt-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 sm:py-4 rounded-xl font-bold hover:shadow-xl transition transform active:scale-95 min-h-[48px] text-sm sm:text-base">
                        Konfirmasi Pesanan
                    </button>

                    <!-- Info -->
                    <div class="mt-3 sm:mt-4 p-2 sm:p-3 bg-blue-50 rounded-lg">
                        <p class="text-[10px] sm:text-xs text-blue-800 flex items-start gap-2">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Pesanan akan diteruskan ke dapur setelah konfirmasi</span>
                        </p>
                    </div>
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
            renderOrderSummary();
        });

        function loadCart() {
            cart = JSON.parse(localStorage.getItem('warkop_cart')) || [];
            if (cart.length === 0) {
                alert('Keranjang kosong!');
                window.location.href = 'menu.php';
            }
        }

        function renderOrderSummary() {
            const container = document.getElementById('orderItems');
            
            container.innerHTML = cart.map(item => {
                const itemTotal = item.harga * item.quantity;
                return `
                    <div class="flex justify-between items-start text-xs sm:text-sm">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">${item.nama_menu}</div>
                            <div class="text-gray-500">${item.quantity}x Rp ${item.harga.toLocaleString('id-ID')}</div>
                        </div>
                        <div class="font-semibold text-gray-900">Rp ${itemTotal.toLocaleString('id-ID')}</div>
                    </div>
                `;
            }).join('');

            updateSummary();
        }

        function updateSummary() {
            const total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);

            document.getElementById('total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        }

        function submitOrder() {
            const customerName = document.getElementById('customerName').value.trim();
            const notes = document.getElementById('notes').value.trim();
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const tableNumber = localStorage.getItem('tableNumber') || '1';

            if (!customerName) {
                alert('Mohon isi nama pelanggan!');
                document.getElementById('customerName').focus();
                return;
            }

            // Calculate total
            const total = cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);

            // Create order data for API
            const orderData = {
                table_number: tableNumber,
                customer_name: customerName,
                notes: notes,
                payment_method: paymentMethod,
                total_amount: total,
                items: cart.map(item => ({
                    menu_id: item.menu_id,
                    quantity: item.quantity,
                    price: item.harga
                }))
            };

            // Send to backend
            fetch('../actions/create-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear cart
                    localStorage.removeItem('warkop_cart');
                    // Redirect to status page
                    window.location.href = `status.php?order=${data.order_id}`;
                } else {
                    alert('Gagal membuat pesanan: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat membuat pesanan');
            });
        }
    </script>
</body>
</html>
