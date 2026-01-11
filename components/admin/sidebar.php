<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex flex-col">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-gray-800 border-b border-gray-700">
        <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
            Dashboard
        </h1>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $current_page == 'dashboard.php' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="orders.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $current_page == 'orders.php' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Kelola Pesanan</span>
        </a>

        <a href="menus.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $current_page == 'menus.php' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span>Kelola Menu</span>
        </a>

        <a href="tables.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $current_page == 'tables.php' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span>Kelola Meja</span>
        </a>

        <a href="reports.php" class="flex items-center gap-3 px-4 py-3 rounded-lg <?php echo $current_page == 'reports.php' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 font-semibold' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span>Laporan</span>
        </a>
    </nav>

    <!-- User Info -->
    <div class="p-4 border-t border-gray-700">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center font-semibold">
                <span id="userInitial">A</span>
            </div>
            <div class="flex-1">
                <p id="userName" class="text-sm font-semibold">Administrator</p>
                <p class="text-xs text-gray-400">Admin</p>
            </div>
        </div>
        <form action="../../actions/admin/logout-action.php" method="POST">
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>