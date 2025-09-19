<nav x-data="{
    expanded: localStorage.getItem('sidebar-expanded') !== 'false',
    activeMenu: null,
    toggleSidebar() {
        this.expanded = !this.expanded;
        localStorage.setItem('sidebar-expanded', this.expanded);
    }
}" :class="expanded ? 'w-64' : 'w-20'"
    class="bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-300 ease-in-out flex-shrink-0 relative z-10">

    <!-- Logo Section -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ url('/dashboard') }}" class="flex items-center space-x-3 p-2">
            <!-- Logo -->
            <img src="{{ asset('assets/images/Perk Enterprises.png') }}" alt="Perk Enterprises Logo"
                class="w-20 h-auto object-contain flex-shrink-0">
            <!-- Company Name -->
            <span class="font-display font-bold text-base text-slate-900 dark:text-white 
                 leading-tight overflow-visible">
                Perk Enterprises Pvt Ltd
            </span>
        </a>

        <button @click="toggleSidebar()"
            class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="menu" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
        </button>
    </div>

    <!-- Navigation Items -->
    <div class="flex-1 overflow-y-auto scrollbar-thin py-4">
        <div class="px-3 space-y-1">
            <!-- Dashboard -->
            <a href="{{ url('/dashboard') }}" class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                <span x-show="expanded" x-transition class="flex-1">Dashboard</span>
                <span x-show="expanded && request()->is('dashboard')"
                    class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
            </a>

            <!-- Section Label -->
            <div x-show="expanded" class="pt-4 pb-2">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-3">
                    Auto Parts Management
                </span>
            </div>

            <!-- Inventory Management -->
            <div
                x-data="{ open: {{ request()->is('grns*') || request()->is('inventory*') || request()->is('items*') || request()->is('batches*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('grns*') || request()->is('inventory*') || request()->is('items*') || request()->is('batches*') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                    <i data-lucide="package" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Inventory</span>
                    <i x-show="expanded" data-lucide="chevron-down" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/grns') }}"
                        class="sidebar-item pl-12 {{ request()->is('grns*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="truck" class="w-4 h-4 mr-2"></i>
                        GRN Entry
                    </a>
                    <a href="{{ route('inventory.mappings.index') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory/mappings*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="link" class="w-4 h-4 mr-2"></i>
                        Vendor Mapping
                    </a>
                    <a href="{{ url('/inventory') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory') || request()->is('inventory/stock-*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="warehouse" class="w-4 h-4 mr-2"></i>
                        Stock Overview
                    </a>
                    <a href="{{ route('inventory.stock-by-item') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory/stock-by-item') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="package-search" class="w-4 h-4 mr-2"></i>
                        Stock by Item
                    </a>
                    <a href="{{ route('inventory.low-stock') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory/low-stock') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                        Low Stock Alert
                    </a>
                    <a href="{{ url('/batches') }}"
                        class="sidebar-item pl-12 {{ request()->is('batches*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="layers" class="w-4 h-4 mr-2"></i>
                        Batch Management
                    </a>
                    <a href="{{ url('/items') }}"
                        class="sidebar-item pl-12 {{ request()->is('items*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="database" class="w-4 h-4 mr-2"></i>
                        Items Registry
                    </a>
                </div>
            </div>

            <!-- Sales & POS -->
            <div
                x-data="{ open: {{ request()->is('pos*') || request()->is('sales*') || request()->is('returns*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('pos*') || request()->is('sales*') || request()->is('returns*') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                    <i data-lucide="shopping-cart" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Sales & POS</span>
                    <i x-show="expanded" data-lucide="chevron-down" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/pos') }}"
                        class="sidebar-item pl-12 {{ request()->is('pos*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                        Point of Sale
                    </a>
                    <a href="{{ url('/sales') }}"
                        class="sidebar-item pl-12 {{ request()->is('sales*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="receipt" class="w-4 h-4 mr-2"></i>
                        Sales History
                    </a>
                    <a href="{{ url('/returns') }}"
                        class="sidebar-item pl-12 {{ request()->is('returns*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Returns
                    </a>
                </div>
            </div>

            <!-- Customers -->
            <div x-data="{ open: {{ request()->is('customers*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('customers*') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                    <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Customers</span>
                    <i x-show="expanded" data-lucide="chevron-down" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/customers') }}"
                        class="sidebar-item pl-12 {{ request()->is('customers') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Customer List
                    </a>
                    <a href="{{ url('/customers/create') }}"
                        class="sidebar-item pl-12 {{ request()->is('customers/create') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Add Customer
                    </a>
                </div>
            </div>

            <!-- Quotations -->
            <div x-data="{ open: {{ request()->is('quotations*') || request()->is('invoices*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('quotations*') || request()->is('invoices*') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                    <i data-lucide="file-text" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Quotations</span>
                    <i x-show="expanded" data-lucide="chevron-down" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/quotations/create') }}"
                        class="sidebar-item pl-12 {{ request()->is('quotations/create') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Create Quote
                    </a>
                    <a href="{{ url('/quotations') }}"
                        class="sidebar-item pl-12 {{ request()->is('quotations') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Manage Quotes
                    </a>
                    <a href="{{ url('/invoices') }}"
                        class="sidebar-item pl-12 {{ request()->is('invoices*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Invoices
                    </a>
                </div>
            </div>
           
            <!-- Settings Section -->
            <div x-show="expanded" class="pt-6 pb-2">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-3">
                    Settings
                </span>
            </div>

            <!-- Configuration -->
            <div
                x-data="{ open: {{ request()->is('vendors*') || request()->is('categories*') || request()->is('stores*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('vendors*') || request()->is('categories*') || request()->is('stores*') ? 'text-primary-600 dark:text-primary-400' : '' }}">
                    <i data-lucide="settings" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Configuration</span>
                    <i x-show="expanded" data-lucide="chevron-down" :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/vendors') }}"
                        class="sidebar-item pl-12 {{ request()->is('vendors*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Vendors
                    </a>
                    <a href="{{ url('/categories') }}"
                        class="sidebar-item pl-12 {{ request()->is('categories*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Categories
                    </a>
                    <a href="{{ url('/stores') }}"
                        class="sidebar-item pl-12 {{ request()->is('stores*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Stores & Bins
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Toggle Button (Desktop) -->
    <div class="absolute -right-3 top-20 hidden lg:block">
        <button @click="toggleSidebar()"
            class="w-6 h-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full shadow-md flex items-center justify-center hover:scale-110 transition-transform">
            <i data-lucide="chevron-left" :class="!expanded && 'rotate-180'"
                class="w-3 h-3 text-slate-600 dark:text-slate-400 transition-transform duration-200"></i>
        </button>
    </div>
</nav>

<!-- Alpine.js Collapse Plugin -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.directive('collapse', (el) => {
            el._x_isShown = false;

            if (!el._x_doShow) {
                el._x_doShow = () => {
                    el.style.height = `${el.scrollHeight}px`;
                    el._x_isShown = true;
                }
            }

            if (!el._x_doHide) {
                el._x_doHide = () => {
                    el.style.height = '0px';
                    el._x_isShown = false;
                }
            }

            let show = () => {
                el.style.display = 'block';
                el.style.overflow = 'hidden';
                el.style.height = '0px';
                el.style.transition = 'height 0.3s ease-out';
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        el._x_doShow();
                    });
                });
            }

            let hide = () => {
                el.style.overflow = 'hidden';
                el.style.transition = 'height 0.3s ease-out';
                el._x_doHide();
                setTimeout(() => {
                    el.style.display = 'none';
                }, 300);
            }

            el._x_toggleCollapse = () => {
                el._x_isShown ? hide() : show();
            }
        });
    });
</script>