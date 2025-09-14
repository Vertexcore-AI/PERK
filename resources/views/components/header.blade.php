<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6">
    <!-- Left Section -->
    <div class="flex items-center gap-4">
        <!-- Mobile Menu Toggle -->
        <button @click="$dispatch('toggle-mobile-menu')"
            class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="menu" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
        </button>

        <!-- Search Bar -->
        <div class="hidden md:block relative">
            <div class="relative">
                <i data-lucide="search"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                <input type="text"
                    placeholder="Search..."
                    class="pl-10 pr-4 py-2 w-64 lg:w-80 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
            </div>
        </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-3">
        <!-- Notifications -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="relative p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i data-lucide="bell" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
            </button>

            <!-- Notifications Dropdown -->
            <div x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 z-50">

                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</h3>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    <!-- Notification Item -->
                    <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="package" class="w-4 h-4 text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-slate-900 dark:text-slate-100">New stock arrived</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">5 minutes ago</p>
                            </div>
                        </div>
                    </a>

                    <!-- More notification items... -->
                    <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-slate-900 dark:text-slate-100">Order completed</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">1 hour ago</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="p-3 border-t border-slate-200 dark:border-slate-700">
                    <a href="#" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                        View all notifications
                    </a>
                </div>
            </div>
        </div>

        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode()"
            class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="sun" class="w-5 h-5 text-slate-600 dark:text-slate-400 dark:hidden"></i>
            <i data-lucide="moon" class="w-5 h-5 text-slate-600 dark:text-slate-400 hidden dark:block"></i>
        </button>

        <!-- User Menu -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                    <span class="text-white text-sm font-semibold">AD</span>
                </div>
                <div class="hidden lg:block text-left">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Alexandra Della</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Administrator</p>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 hidden lg:block"></i>
            </button>

            <!-- User Dropdown -->
            <div x-show="open"
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 z-50">

                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-semibold">AD</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900 dark:text-white">Alexandra Della</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">alex.della@perk.com</p>
                        </div>
                    </div>
                </div>

                <div class="p-2">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-sm text-slate-700 dark:text-slate-200">Profile</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-sm text-slate-700 dark:text-slate-200">Settings</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <i data-lucide="help-circle" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-sm text-slate-700 dark:text-slate-200">Help & Support</span>
                    </a>
                </div>

                <div class="p-2 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ url('/logout') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 text-rose-600 dark:text-rose-400 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleDarkMode() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');

        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    }
</script>