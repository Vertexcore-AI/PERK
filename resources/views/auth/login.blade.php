<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Perk Enterprises</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Left Section - Login Form (30%) -->
        <div class="w-full lg:w-[30%] login-abstract-bg flex flex-col justify-center px-6 sm:px-12">
            <!-- Abstract Background Elements -->
            <div class="login-geometric-shapes">
                <div class="login-shape-1"></div>
                <div class="login-shape-2"></div>
                <div class="login-shape-3"></div>
                <div class="login-mesh-gradient"></div>
            </div>

            <div class="mx-auto w-full max-w-sm relative z-10">
                <!-- Logo & Brand -->
                <div class="text-center mb-10">
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('images/logo.jpeg') }}"
                             alt="Perk Enterprises"
                             class="h-20 w-auto object-contain">
                    </div>
                   
                </div>

                <!-- Welcome Text -->
                <div class="text-center mb-8">
                    <h2 class="login-title mb-3">Welcome Back</h2>
                    <p class="login-subtitle">Sign in to your account </p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50/80 backdrop-blur-sm border border-emerald-200/50 rounded-xl shadow-sm">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                            <span class="ml-3 text-sm font-medium text-emerald-700">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50/80 backdrop-blur-sm border border-rose-200/50 rounded-xl shadow-sm">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                            <span class="ml-3 text-sm font-medium text-rose-700">
                                {{ $errors->first() }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email/Username -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-3 tracking-wide">
                            Email or Username
                        </label>
                        <div class="relative">
                            <input
                                id="email"
                                name="email"
                                type="text"
                                required
                                autofocus
                                value="{{ old('email') }}"
                                class="login-glass-input @error('email') border-red-500 @enderror"
                                placeholder="Enter your email or username">
                           
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-3 tracking-wide">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="login-glass-input @error('password') border-red-500 @enderror"
                                placeholder="Enter your password">
                           
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-8">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="login-glass-checkbox">
                        <label for="remember" class="ml-3 text-sm font-medium text-slate-600 tracking-wide">
                            Remember me for 30 days
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="login-glass-button">
                        <i data-lucide="log-in" class="w-5 h-5 mr-2"></i>
                        Sign Into Dashboard
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-10 text-center">
                    <p class="text-xs text-slate-500 font-medium tracking-wide">
                        &copy; {{ date('Y') }} Perk Enterprises • All rights reserved
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