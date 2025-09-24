<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Perk Enterprises</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Section - Login Form (30%) -->
        <div class="w-full lg:w-[30%] bg-white dark:bg-slate-900 flex flex-col justify-center px-6 sm:px-12">
            <div class="mx-auto w-full max-w-sm">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('images/logo.jpeg') }}"
                         alt="Perk Enterprises"
                         class="h-20 w-auto object-contain">
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Welcome Back</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">Sign in to your account to continue</p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                            <span class="ml-2 text-sm text-green-600 dark:text-green-400">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                            <span class="ml-2 text-sm text-red-600 dark:text-red-400">
                                {{ $errors->first() }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email/Username -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Email or Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <input
                                id="email"
                                name="email"
                                type="text"
                                required
                                autofocus
                                value="{{ old('email') }}"
                                class="w-full pl-10 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                placeholder="Enter your email or username">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="w-full pl-10 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 bg-white dark:bg-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                placeholder="Enter your password">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="w-4 h-4 text-rose-600 bg-slate-100 border-slate-300 rounded focus:ring-rose-500 dark:focus:ring-rose-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
                            <label for="remember" class="ml-2 text-sm text-slate-600 dark:text-slate-400">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition duration-200">
                        <i data-lucide="log-in" class="w-5 h-5 mr-2"></i>
                        Sign In
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        &copy; {{ date('Y') }} Perk Enterprises. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Section - Image (70%) -->
        <div class="hidden lg:block lg:w-[70%] bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 relative overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center p-12">
                <img src="{{ asset('images/Bolero.png') }}"
                     alt="Bolero Truck"
                     class="max-w-full max-h-full object-contain drop-shadow-2xl">
            </div>
            <!-- Decorative overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-rose-500/10 to-pink-600/10"></div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Focus on email field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>