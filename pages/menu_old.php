<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 pb-24">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="landing.php" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Menu</h1>
                        <p class="text-xs text-gray-600">Meja <span id="tableNumber">-</span></p>
                    </div>
                </div>
                <button onclick="window.location.href='cart.php'" class="relative p-2 text-indigo-600 hover:bg-indigo-50 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span id="cartBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold hidden">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Category Filter - Sticky -->
    <div class="bg-white border-b border-gray-200 sticky top-[72px] z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 overflow-x-auto">
            <div class="flex gap-2 min-w-max">
                <button onclick="filterByCategory('Semua')" class="category-btn px-4 py-2 rounded-full font-semibold text-sm whitespace-nowrap transition bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md">Semua</button>
                <button onclick="filterByCategory('Kopi')" class="category-btn px-4 py-2 rounded-full font-semibold text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 whitespace-nowrap transition">Kopi</button>
                <button onclick="filterByCategory('Non Kopi')" class="category-btn px-4 py-2 rounded-full font-semibold text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 whitespace-nowrap transition">Non Kopi</button>
                <button onclick="filterByCategory('Makanan')" class="category-btn px-4 py-2 rounded-full font-semibold text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 whitespace-nowrap transition">Makanan</button>
                <button onclick="filterByCategory('Snack')" class="category-btn px-4 py-2 rounded-full font-semibold text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 whitespace-nowrap transition">Snack</button>
            </div>
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div id="menuContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <!-- Menu items will be inserted here -->
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-full shadow-2xl hidden z-50 transition-all">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span id="toastMessage">Ditambahkan ke keranjang!</span>
        </div>
    </div>

    <!-- Floating Cart Button (Mobile) -->
    <div class="fixed bottom-6 right-6 md:hidden z-10">
        <button onclick="window.location.href='cart.php'" class="relative bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 rounded-full shadow-2xl hover:shadow-3xl transition transform hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span id="floatingCartBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold hidden">0</span>
        </button>
    </div>

    <script>
        let menus = [];
        let currentCategory = 'Semua';

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Get table number from URL
            const urlParams = new URLSearchParams(window.location.search);
            const tableNumber = urlParams.get('table') || '1';
            document.getElementById('tableNumber').textContent = tableNumber;
            localStorage.setItem('tableNumber', tableNumber);

            // Load sample menus
            if (!localStorage.getItem('menus')) {
                localStorage.setItem('menus', JSON.stringify([
                    { id: 1, name: 'Kopi Hitam', category: 'Kopi', price: 8000, available: true },
                    { id: 2, name: 'Kopi Susu', category: 'Kopi', price: 12000, available: true },
                    { id: 3, name: 'Cappuccino', category: 'Kopi', price: 15000, available: true },
                    { id: 4, name: 'Latte', category: 'Kopi', price: 15000, available: true },
                    { id: 5, name: 'Americano', category: 'Kopi', price: 13000, available: true },
                    { id: 6, name: 'Teh Manis', category: 'Non Kopi', price: 5000, available: true },
                    { id: 7, name: 'Teh Tarik', category: 'Non Kopi', price: 8000, available: true },
                    { id: 8, name: 'Jus Jeruk', category: 'Non Kopi', price: 12000, available: true },
                    { id: 9, name: 'Milkshake', category: 'Non Kopi', price: 18000, available: true },
                    { id: 10, name: 'Nasi Goreng', category: 'Makanan', price: 20000, available: true },
                    { id: 11, name: 'Mie Goreng', category: 'Makanan', price: 18000, available: true },
                    { id: 12, name: 'Pisang Goreng', category: 'Snack', price: 10000, available: true },
                    { id: 13, name: 'Kentang Goreng', category: 'Snack', price: 15000, available: true },
                ]));
            }

            loadMenus();
            updateCartBadge();
        });

        function loadMenus() {
            menus = JSON.parse(localStorage.getItem('menus')) || [];
            renderMenus();
        }

        function renderMenus() {
            const container = document.getElementById('menuContainer');
            let filteredMenus = currentCategory === 'Semua' 
                ? menus 
                : menus.filter(m => m.category === currentCategory);

            filteredMenus = filteredMenus.filter(m => m.available);

            if (filteredMenus.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <p class="text-gray-500">Tidak ada menu tersedia</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filteredMenus.map(menu => {
                const gradient = getCategoryGradient(menu.category);
                return `
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden group">
                        <div class="aspect-square bg-gradient-to-br ${gradient} flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${getCategoryIcon(menu.category)}
                            </svg>
                        </div>
                        <div class="p-3">
                            <div class="mb-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase">${menu.category}</span>
                                <h3 class="font-bold text-gray-900 text-sm line-clamp-2">${menu.name}</h3>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-indigo-600 font-bold text-sm">Rp ${menu.price.toLocaleString()}</span>
                                <button onclick="addToCart(${menu.id})" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-2 rounded-lg hover:shadow-lg transition transform hover:scale-110">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
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

        function getCategoryIcon(category) {
            const icons = {
                'Kopi': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
                'Non Kopi': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>',
                'Makanan': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                'Snack': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            };
            return icons[category] || icons['Kopi'];
        }

        function filterByCategory(category) {
            currentCategory = category;
            
            // Update button styles
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('bg-gradient-to-r', 'from-indigo-600', 'to-purple-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            });
            
            event.target.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            event.target.classList.add('bg-gradient-to-r', 'from-indigo-600', 'to-purple-600', 'text-white', 'shadow-md');
            
            renderMenus();
        }

        function addToCart(menuId) {
            const menu = menus.find(m => m.id === menuId);
            if (!menu) return;

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const existingItem = cart.find(item => item.id === menuId);

            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: menu.id,
                    name: menu.name,
                    category: menu.category,
                    price: menu.price,
                    quantity: 1
                });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartBadge();
            showToast(`${menu.name} ditambahkan!`);
        }

        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            const badge = document.getElementById('cartBadge');
            const floatingBadge = document.getElementById('floatingCartBadge');
            
            if (totalItems > 0) {
                badge.textContent = totalItems;
                badge.classList.remove('hidden');
                floatingBadge.textContent = totalItems;
                floatingBadge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
                floatingBadge.classList.add('hidden');
            }
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.textContent = message;
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 2000);
        }
    </script>
</body>
</html>
