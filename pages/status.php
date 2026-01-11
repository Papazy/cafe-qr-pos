<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pesanan Berhasil - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Safe area */
        @supports (padding: env(safe-area-inset-top)) {
            body {
                padding-top: env(safe-area-inset-top);
                padding-bottom: env(safe-area-inset-bottom);
            }
        }
        /* Remove tap highlight */
        * { -webkit-tap-highlight-color: transparent; }
        /* Animations */
        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes scale-in {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .checkmark-circle {
            animation: scale-in 0.5s ease-out;
        }
        .checkmark-path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: checkmark 0.5s ease-out 0.3s forwards;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-emerald-50 min-h-screen">
    <!-- Success Animation - Mobile Optimized -->
    <div class="max-w-4xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <div class="text-center mb-4 sm:mb-8">
            <div class="inline-block checkmark-circle">
                <svg class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 mx-auto" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="#10b981" opacity="0.1"/>
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="4"/>
                    <path class="checkmark-path" d="M30 50 L45 65 L70 35" fill="none" stroke="#10b981" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mt-4 sm:mt-6 mb-2 sm:mb-3">Pesanan Berhasil!</h1>
            <p class="text-sm sm:text-base md:text-lg text-gray-600">Terima kasih telah memesan</p>
        </div>

        <!-- Order Info Card - Mobile Optimized -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 mb-4 sm:mb-6">
            <div class="grid grid-cols-1 gap-3 sm:gap-4 md:grid-cols-3 md:gap-6 mb-4 sm:mb-6">
                <div class="text-center md:text-left">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Nomor Pesanan</div>
                    <div class="font-bold text-base sm:text-lg text-gray-900" id="orderNumber">-</div>
                </div>
                <div class="text-center md:text-left">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Nomor Meja</div>
                    <div class="font-bold text-base sm:text-lg text-gray-900">Meja <span id="tableNumber">-</span></div>
                </div>
                <div class="text-center md:text-left">
                    <div class="text-xs sm:text-sm text-gray-500 mb-1">Nama Pelanggan</div>
                    <div class="font-bold text-base sm:text-lg text-gray-900" id="customerName">-</div>
                </div>
            </div>

            <!-- Timeline Status - Compact Mobile -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg sm:rounded-xl p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="font-bold text-gray-900 mb-3 sm:mb-4 text-sm sm:text-base">Status Pesanan</h3>
                <div class="space-y-3 sm:space-y-4">
                    <!-- Step 1: Pesanan Diterima -->
                    <div id="step-pending" class="flex gap-3 sm:gap-4 items-start">
                        <div class="flex-shrink-0">
                            <div class="step-icon w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-500 flex items-center justify-center text-white shadow-md">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm sm:text-base">Pesanan Diterima</div>
                            <div class="text-xs sm:text-sm text-gray-600">Pesanan Anda telah masuk sistem</div>
                        </div>
                    </div>

                    <!-- Step 2: Sedang Diproses -->
                    <div id="step-diproses" class="flex gap-3 sm:gap-4 items-start">
                        <div class="flex-shrink-0">
                            <div class="step-icon w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-300 flex items-center justify-center text-white">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm sm:text-base">Sedang Diproses</div>
                            <div class="text-xs sm:text-sm text-gray-600">Tim dapur sedang menyiapkan pesanan</div>
                        </div>
                    </div>

                    <!-- Step 3: Selesai -->
                    <div id="step-selesai" class="flex gap-3 sm:gap-4 items-start">
                        <div class="flex-shrink-0">
                            <div class="step-icon w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-300 flex items-center justify-center text-white">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm sm:text-base">Selesai</div>
                            <div class="text-xs sm:text-sm text-gray-600">Pesanan telah selesai</div>
                        </div>
                    </div>

                    <!-- Step 4: Dibatalkan (hidden by default) -->
                    <div id="step-dibatalkan" class="flex gap-3 sm:gap-4 items-start hidden">
                        <div class="flex-shrink-0">
                            <div class="step-icon w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white shadow-lg">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm sm:text-base">Dibatalkan</div>
                            <div class="text-xs sm:text-sm text-gray-600">Pesanan telah dibatalkan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items - Scrollable Mobile -->
            <div class="mb-4 sm:mb-6">
                <h3 class="font-bold text-gray-900 mb-3 sm:mb-4 text-sm sm:text-base">Detail Pesanan</h3>
                <div id="orderItems" class="space-y-2 sm:space-y-3 max-h-56 sm:max-h-64 overflow-y-auto">
                    <!-- Items will be inserted here -->
                </div>
            </div>

            <!-- Payment Info - Compact -->
            <div class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-4 mb-4 sm:mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs sm:text-sm text-gray-600">Metode Pembayaran</span>
                    <span class="font-semibold text-gray-900 text-sm sm:text-base" id="paymentMethod">-</span>
                </div>
                <div id="paymentInfo" class="text-xs text-gray-600 mt-2 sm:mt-3 p-2 sm:p-3 bg-amber-50 rounded-lg border border-amber-200 hidden">
                    <div class="flex items-start gap-2">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div id="paymentInstructions" class="text-xs"></div>
                    </div>
                </div>
            </div>

            <!-- Total - Responsive -->
            <div class="border-t border-gray-200 pt-3 sm:pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-base sm:text-xl font-bold text-gray-900">Total</span>
                    <span class="text-xl sm:text-2xl font-bold text-indigo-600" id="total">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons - Full Width Mobile -->
        <div class="grid grid-cols-1 gap-3 sm:gap-4 mb-4 sm:mb-6">
            <a href="menu.php" class="block bg-white border-2 border-indigo-600 text-indigo-600 text-center py-3 sm:py-4 rounded-xl font-bold hover:bg-indigo-50 transition active:scale-95 min-h-[48px] flex items-center justify-center text-sm sm:text-base">
                Pesan Lagi
            </a>
            <a href="landing.php" class="block bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-center py-3 sm:py-4 rounded-xl font-bold hover:shadow-xl transition transform active:scale-95 min-h-[48px] flex items-center justify-center text-sm sm:text-base">
                Kembali ke Beranda
            </a>
        </div>

        <!-- Share Order - Prominent -->
        <div class="text-center">
            <button onclick="shareOrder()" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm flex items-center gap-2 mx-auto min-h-[44px] px-4 py-2 rounded-lg hover:bg-indigo-50 transition active:scale-95">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                </svg>
                Bagikan Pesanan
            </button>
        </div>
    </div>

    <script>
        let order = null;

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const orderId = urlParams.get('order');

            if (!orderId) {
                alert('Nomor pesanan tidak ditemukan!');
                window.location.href = 'landing.php';
                return;
            }

            loadOrder(orderId);
        });

        function loadOrder(orderId) {
            fetch(`../actions/get-order-detail.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        order = data.order;
                        renderOrder();
                        updateStatusTimeline(order.status);
                    } else {
                        alert('Pesanan tidak ditemukan!');
                        window.location.href = 'landing.php';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat pesanan');
                });
        }

        function renderOrder() {
            document.getElementById('orderNumber').textContent = order.order_number;
            document.getElementById('tableNumber').textContent = order.table_number;
            document.getElementById('customerName').textContent = order.customer_name;
            document.getElementById('paymentMethod').textContent = order.payment_method;

            // Show payment instructions
            const paymentInfo = document.getElementById('paymentInfo');
            const paymentInstructions = document.getElementById('paymentInstructions');
            
            if (order.payment_method === 'Transfer') {
                paymentInstructions.innerHTML = `
                    <strong>Instruksi Transfer:</strong><br>
                    Bank BCA: 1234567890<br>
                    a.n. Warkop QR<br>
                    Mohon transfer sesuai nominal total
                `;
                paymentInfo.classList.remove('hidden');
            } else if (order.payment_method === 'QRIS') {
                paymentInstructions.innerHTML = `
                    <strong>Scan QR Code untuk pembayaran:</strong><br>
                    Atau hubungi kasir untuk mendapatkan link pembayaran
                `;
                paymentInfo.classList.remove('hidden');
            } else if (order.payment_method === 'Cash') {
                paymentInstructions.innerHTML = `
                    <strong>Pembayaran Tunai:</strong><br>
                    Silakan bayar ke kasir. Pesanan akan diproses setelah pembayaran dikonfirmasi.
                `;
                paymentInfo.classList.remove('hidden');
            }

            // Render items
            const itemsContainer = document.getElementById('orderItems');
            itemsContainer.innerHTML = order.items.map(item => {
                const itemTotal = item.price * item.quantity;
                return `
                    <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 text-sm sm:text-base">${item.menu_name}</div>
                            <div class="text-xs sm:text-sm text-gray-500">${item.quantity}x Rp ${parseInt(item.price).toLocaleString('id-ID')}</div>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm sm:text-base">Rp ${itemTotal.toLocaleString('id-ID')}</div>
                    </div>
                `;
            }).join('');

            // Update total
            document.getElementById('total').textContent = `Rp ${parseInt(order.total).toLocaleString('id-ID')}`;
        }

        function updateStatusTimeline(status) {
            // Reset all steps to inactive
            const stepPending = document.getElementById('step-pending');
            const stepDiproses = document.getElementById('step-diproses');
            const stepSelesai = document.getElementById('step-selesai');
            const stepDibatalkan = document.getElementById('step-dibatalkan');

            // Reset opacity
            stepPending.classList.remove('opacity-50');
            stepDiproses.classList.remove('opacity-50');
            stepSelesai.classList.remove('opacity-50');
            
            // Get all step icons
            const iconPending = stepPending.querySelector('.step-icon');
            const iconDiproses = stepDiproses.querySelector('.step-icon');
            const iconSelesai = stepSelesai.querySelector('.step-icon');

            // Reset all icons to default state
            iconDiproses.classList.remove('bg-amber-500', 'bg-green-500', 'shadow-md', 'animate-pulse');
            iconDiproses.classList.add('bg-gray-300');
            
            iconSelesai.classList.remove('bg-green-500', 'shadow-md');
            iconSelesai.classList.add('bg-gray-300');

            // Update based on current status
            if (status === 'Pending') {
                // Only "Pesanan Diterima" is active (green)
                stepDiproses.classList.add('opacity-50');
                stepSelesai.classList.add('opacity-50');
            } 
            else if (status === 'Diproses') {
                // "Pesanan Diterima" complete (green), "Sedang Diproses" active (amber with pulse)
                iconDiproses.classList.remove('bg-gray-300');
                iconDiproses.classList.add('bg-amber-500', 'shadow-md', 'animate-pulse');
                stepSelesai.classList.add('opacity-50');
            } 
            else if (status === 'Selesai') {
                // All steps complete (all green)
                iconDiproses.classList.remove('bg-gray-300', 'animate-pulse');
                iconDiproses.classList.add('bg-green-500', 'shadow-md');
                
                iconSelesai.classList.remove('bg-gray-300');
                iconSelesai.classList.add('bg-green-500', 'shadow-md');
            }
            else if (status === 'Dibatalkan') {
                // Show cancelled step, hide others
                stepDiproses.classList.add('hidden');
                stepSelesai.classList.add('hidden');
                stepDibatalkan.classList.remove('hidden');
            }
        }

        function shareOrder() {
            const text = `Pesanan Saya di Warkop QR\n\nNomor Pesanan: ${order.order_number}\nMeja: ${order.table_number}\nTotal: Rp ${parseInt(order.total).toLocaleString('id-ID')}\n\n${order.items.map(i => `${i.quantity}x ${i.menu_name}`).join('\n')}`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'Pesanan Warkop QR',
                    text: text
                }).catch(err => console.log('Error sharing:', err));
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(text).then(() => {
                    alert('Informasi pesanan telah disalin!');
                });
            }
        }
    </script>
</body>
</html>
