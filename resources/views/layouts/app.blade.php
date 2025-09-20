<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="@yield('meta_description', '')" />
    <meta name="keywords" content="@yield('meta_keywords', '')" />
    <meta name="author" content="PERK Enterprises" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - PERK Enterprises</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- Tailwind CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Vendor Styles -->
    @stack('vendor-styles')

    <!-- Additional Styles -->
    @stack('styles')
</head>

<body class="h-full overflow-hidden">
    <div class="flex h-full">
        <!-- Sidebar Navigation -->
        @include('components.navbar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('components.header')

            <!-- Main Content -->
            <main class="flex-1 overflow-auto bg-gradient-to-br from-slate-50 via-white to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
                <div class="w-full px-4 py-2">
                    @hasSection('page-header')
                        @yield('page-header')
                    @endif

                    <!-- Page Content with Animation -->
                    <div class="animate-in" style="animation-delay: 0.1s">
                        @yield('content')
                    </div>
                </div>
            </main>

            <!-- Footer -->
            @include('components.footer')
        </div>
    </div>

    <!-- Theme Customizer (if needed) -->
    @yield('customizer')

    <!-- Vendor Scripts -->
    @stack('vendor-scripts')

    <!-- Alpine.js for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Initialize icons after Alpine is ready
        document.addEventListener('alpine:initialized', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    <!-- Force light mode always -->
    <script>
        // Force light mode - remove any dark class and set theme to light
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');

        // Ensure light mode is maintained even if other scripts try to change it
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    </script>

    <!-- Page Scripts -->
    @stack('scripts')
</body>

</html>