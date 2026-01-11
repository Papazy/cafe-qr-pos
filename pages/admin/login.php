<?php
    require_once '../../includes/config.php';
    require_once '../../includes/database.php';

    session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Warkop QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <header class="bg-white shadow px-4 py-3">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-900">Warkop QR</h1>
                <a href="landing.php" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">
                    ← Beranda
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-md mx-auto p-4 space-y-4">
            <!-- Login Card -->
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <!-- Icon -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Login Admin</h2>
                    <p class="text-gray-600">Masuk ke panel admin</p>
                </div>

                <!-- Error Message -->
                 <?php if (isset($_GET['error'])){ 
                    echo '
                        <div id="errorMessage" class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-3 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="errorText">Username atau password salah</span>
                            </div>
                        </div>
                    ';
                 }
                ?>

                <!-- Login Form -->
                <form method="POST" action="../../actions/admin/login-action.php">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-900 mb-2">
                            Username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            placeholder="Masukkan username"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                placeholder="Masukkan password"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent"
                            >
                            <button 
                                type="button" 
                                id="togglePassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-900"
                            >
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-semibold transition"
                    >
                        Login
                    </button>
                </form>
            </div>

        </main>
    </div>
</body>
</html>
