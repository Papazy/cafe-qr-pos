<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Warkop QR - Selamat Datang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Safe area untuk notch iPhone */
        @supports (padding: env(safe-area-inset-top)) {
            body {
                padding-top: env(safe-area-inset-top);
                padding-bottom: env(safe-area-inset-bottom);
            }
        }
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        /* Touch highlight removal */
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
    <!-- Header - Mobile Optimized -->
    <header class="bg-white shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 sm:py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Warkop QR</h1>
                </div>
                <a href="admin/login.php" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-700 font-semibold min-h-[44px] flex items-center px-2">
                    Login →
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <div class="max-w-4xl mx-auto space-y-4 sm:space-y-8">
            <!-- Hero Section - Mobile Optimized -->
            <div class="text-center space-y-3 sm:space-y-4 py-4 sm:py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 shadow-2xl mb-3 sm:mb-6 animate-pulse">
                    <svg class="w-9 h-9 sm:w-12 sm:h-12 md:w-14 md:h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 px-4">Selamat Datang!</h2>
                <p class="text-sm sm:text-base md:text-lg text-gray-600 max-w-2xl mx-auto px-4">Pesan makanan dan minuman favorit Anda dengan mudah menggunakan QR Code di meja</p>
            </div>

            <!-- Features Grid - Mobile 2 Columns -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition active:scale-95">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-indigo-100 flex items-center justify-center mb-3 sm:mb-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base">Scan QR</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Scan kode di meja Anda</p>
                </div>

                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition active:scale-95">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-green-100 flex items-center justify-center mb-3 sm:mb-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base">Pilih Menu</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Browse menu lengkap kami</p>
                </div>

                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition active:scale-95">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-purple-100 flex items-center justify-center mb-3 sm:mb-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base">Order</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Tambahkan ke keranjang</p>
                </div>

                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 hover:shadow-xl transition active:scale-95">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-amber-100 flex items-center justify-center mb-3 sm:mb-4">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base">Selesai</h3>
                    <p class="text-xs sm:text-sm text-gray-600">Tunggu pesanan datang</p>
                </div>
            </div>

            <!-- Instructions Card - Mobile Optimized -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 md:p-8">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 text-center">Cara Pesan</h3>
                <div class="grid grid-cols-1 gap-4 sm:gap-6">
                    <div class="flex gap-3 sm:gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg text-sm sm:text-base">1</div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1 text-sm sm:text-base">Scan QR Code</h4>
                            <p class="text-xs sm:text-sm text-gray-600">Gunakan kamera HP untuk scan QR Code yang ada di meja Anda</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">2</div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Pilih Menu</h4>
                            <p class="text-sm text-gray-600">Browse dan pilih menu favorit yang Anda inginkan</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white font-bold shadow-lg">3</div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Tambah ke Keranjang</h4>
                            <p class="text-sm text-gray-600">Klik tombol tambah untuk memasukkan ke keranjang</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white font-bold shadow-lg">4</div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Checkout & Bayar</h4>
                            <p class="text-sm text-gray-600">Konfirmasi pesanan dan pilih metode pembayaran</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Tables Grid - Mobile Optimized -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl sm:rounded-2xl shadow-2xl p-4 sm:p-6 md:p-8 text-white">
                <h3 class="text-xl sm:text-2xl font-bold mb-2 text-center">Mode Testing</h3>
                <p class="text-center text-indigo-100 mb-4 sm:mb-6 text-sm sm:text-base">Pilih nomor meja untuk mulai memesan</p>
                
                <div class="grid grid-cols-3 gap-3 sm:gap-4 max-w-2xl mx-auto">
                    <a href="menu.php?table=1" class="group bg-white hover:bg-indigo-50 rounded-lg sm:rounded-xl p-4 sm:p-6 text-center transition transform active:scale-95 hover:scale-105 shadow-lg min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center">
                        <div class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-1 sm:mb-2">1</div>
                        <div class="text-xs sm:text-sm text-gray-600">Meja 1</div>
                    </a>
                    <a href="menu.php?table=2" class="group bg-white hover:bg-indigo-50 rounded-lg sm:rounded-xl p-4 sm:p-6 text-center transition transform active:scale-95 hover:scale-105 shadow-lg min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center">
                        <div class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-1 sm:mb-2">2</div>
                        <div class="text-xs sm:text-sm text-gray-600">Meja 2</div>
                    </a>
                    <a href="menu.php?table=3" class="group bg-white hover:bg-indigo-50 rounded-lg sm:rounded-xl p-4 sm:p-6 text-center transition transform active:scale-95 hover:scale-105 shadow-lg min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center">
                        <div class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-1 sm:mb-2">3</div>
                        <div class="text-xs sm:text-sm text-gray-600">Meja 3</div>
                    </a>
                    <a href="menu.php?table=4" class="group bg-white hover:bg-indigo-50 rounded-lg sm:rounded-xl p-4 sm:p-6 text-center transition transform active:scale-95 hover:scale-105 shadow-lg min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center">
                        <div class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-1 sm:mb-2">4</div>
                        <div class="text-xs sm:text-sm text-gray-600">Meja 4</div>
                    </a>
                    <a href="menu.php?table=5" class="group bg-white hover:bg-indigo-50 rounded-lg sm:rounded-xl p-4 sm:p-6 text-center transition transform active:scale-95 hover:scale-105 shadow-lg min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center">
                        <div class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-1 sm:mb-2">5</div>
                        <div class="text-xs sm:text-sm text-gray-600">Meja 5</div>
                    </a>
                    <a href="menu.php?table=6" class="group bg-white hover:bg-indigo-50 rounded-lg sm:rounded-xl p-4 sm:p-6 text-center transition transform active:scale-95 hover:scale-105 shadow-lg min-h-[80px] sm:min-h-[100px] flex flex-col items-center justify-center">
                        <div class="text-2xl sm:text-3xl font-bold text-indigo-600 mb-1 sm:mb-2">6</div>
                        <div class="text-xs sm:text-sm text-gray-600">Meja 6</div>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer - Mobile Optimized -->
    <footer class="bg-white border-t border-gray-200 mt-8 sm:mt-16">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-6 text-center text-xs sm:text-sm text-gray-600">
            <p>&copy; 2026 Warkop QR. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
