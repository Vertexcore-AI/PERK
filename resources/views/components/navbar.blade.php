<nav x-data="{
    expanded: localStorage.getItem('sidebar-expanded') !== 'false',
    activeMenu: null,
    toggleSidebar() {
        this.expanded = !this.expanded;
        localStorage.setItem('sidebar-expanded', this.expanded);
    }
}"
    :class="expanded ? 'w-64' : 'w-20'"
    class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-r border-slate-200/50 dark:border-slate-800/50 transition-all duration-300 ease-in-out flex-shrink-0 relative z-10">

    <!-- Logo Section -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-200/30 dark:border-slate-800/30">
        <a href="{{ url('/dashboard') }}" class="flex items-center">
            <!-- Full Logo (when expanded) -->
            <div x-show="expanded" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="w-48 h-12 overflow-hidden">
                <img src="{{ asset('images/logo.jpeg') }}" alt="PERK Enterprises" class="w-full h-full object-contain">
            </div>

            <!-- PERK Text (when minimized) -->
            <div x-show="!expanded" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="w-10 h-10 bg-gradient-to-br from-primary-600/80 to-primary-700/80 rounded-xl flex items-center justify-center shadow-sm backdrop-blur-sm">
                <span class="text-white font-bold text-lg">P</span>
            </div>
        </a>
        <button @click="toggleSidebar()"
            class="lg:hidden p-2 rounded-lg hover:bg-slate-100/80 dark:hover:bg-slate-800/80 transition-colors">
            <i data-lucide="menu" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
        </button>
    </div>

    <!-- Navigation Items -->
    <div class="flex-1 overflow-y-auto scrollbar-thin py-4">
        <div class="px-3 space-y-1">
            <!-- Dashboard -->
            <a href="{{ url('/dashboard') }}"
                class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                <span x-show="expanded" x-transition class="flex-1">Dashboard</span>
                @if(request()->is('dashboard'))
                <span x-show="expanded"
                    class="w-2 h-2 bg-primary-600/70 rounded-full animate-pulse"></span>
                @endif
            </a>

            <!-- Section Label -->
            <div x-show="expanded" class="pt-4 pb-2">
                <span class="text-xs font-semibold text-slate-500/80 dark:text-slate-400/80 uppercase tracking-wider px-3">
                    Auto Parts Management
                </span>
            </div>

            <!-- Vendors -->
            <a href="{{ url('/vendors') }}"
                class="sidebar-item {{ request()->is('vendors*') ? 'active' : '' }}">
                <i data-lucide="truck" class="w-5 h-5 flex-shrink-0"></i>
                <span x-show="expanded" x-transition class="flex-1">Vendors</span>
            </a>

            <!-- Categories -->
            <a href="{{ url('/categories') }}"
                class="sidebar-item {{ request()->is('categories*') ? 'active' : '' }}">
                <i data-lucide="tag" class="w-5 h-5 flex-shrink-0"></i>
                <span x-show="expanded" x-transition class="flex-1">Categories</span>
            </a>

            <!-- Stores & Bins -->
            <a href="{{ url('/stores') }}"
                class="sidebar-item {{ request()->is('stores*') ? 'active' : '' }}">
                <i data-lucide="warehouse" class="w-5 h-5 flex-shrink-0"></i>
                <span x-show="expanded" x-transition class="flex-1">Stores & Bins</span>
            </a>

            <!-- Inventory Management -->
            <div x-data="{ open: {{ request()->is('grns*') || request()->is('inventory*') || request()->is('items*') || request()->is('batches*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('grns*') || request()->is('inventory*') || request()->is('items*') || request()->is('batches*') ? 'text-primary-700/80 dark:text-primary-400/80' : '' }}">
                    <i data-lucide="package" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Inventory</span>
                    <i x-show="expanded" data-lucide="chevron-down"
                        :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/grns') }}"
                        class="sidebar-item pl-12 {{ request()->is('grns*') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Good Recive Notes
                    </a>
                    <a href="{{ route('inventory.mappings.index') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory/mappings*') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Vendor Mapping
                    </a>
                    <a href="{{ url('/inventory') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory') || request()->is('inventory/stock-*') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Stock Management
                    </a>
                    <!-- <a href="{{ route('inventory.stock-by-item') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory/stock-by-item') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="package-search" class="w-4 h-4 mr-2"></i>
                        Stock by Item
                    </a>
                    <a href="{{ route('inventory.low-stock') }}"
                        class="sidebar-item pl-12 {{ request()->is('inventory/low-stock') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i>
                        Low Stock Alert
                    </a> -->
                    <a href="{{ url('/batches') }}"
                        class="sidebar-item pl-12 {{ request()->is('batches*') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Batch Management
                    </a>
                    <a href="{{ url('/items') }}"
                        class="sidebar-item pl-12 {{ request()->is('items*') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Items Registry
                    </a>
                </div>
            </div>

            <!-- Sales & POS -->
            <div x-data="{ open: {{ request()->is('pos*') || request()->is('sales*') || request()->is('returns*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('pos*') || request()->is('sales*') || request()->is('returns*') ? 'text-primary-700/80 dark:text-primary-400/80' : '' }}">
                    <i data-lucide="shopping-cart" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Sales & POS</span>
                    <i x-show="expanded" data-lucide="chevron-down"
                        :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/pos') }}"
                        class="sidebar-item pl-12 {{ request()->is('pos*') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Point of Sale
                    </a>
                    <a href="{{ url('/sales') }}"
                        class="sidebar-item pl-12 {{ request()->is('sales*') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Sales History
                    </a>
                    <!-- <a href="{{ url('/returns') }}"
                        class="sidebar-item pl-12 {{ request()->is('returns*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Returns
                    </a> -->
                </div>
            </div>

    

            <!-- Customers -->
            <div x-data="{ open: {{ request()->is('customers*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('customers*') ? 'text-primary-700/80 dark:text-primary-400/80' : '' }}">
                    <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Customers</span>
                    <i x-show="expanded" data-lucide="chevron-down"
                        :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/customers') }}"
                        class="sidebar-item pl-12 {{ request()->is('customers') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Customer List
                    </a>
                    <a href="{{ url('/customers/create') }}"
                        class="sidebar-item pl-12 {{ request()->is('customers/create') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Add Customer
                    </a>
                </div>
            </div>

            <!-- Quotations -->
            <div x-data="{ open: {{ request()->is('quotations*') || request()->is('invoices*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('quotations*') || request()->is('invoices*') ? 'text-primary-700/80 dark:text-primary-400/80' : '' }}">
                    <i data-lucide="file-text" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Quotations</span>
                    <i x-show="expanded" data-lucide="chevron-down"
                        :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/quotations/create') }}"
                        class="sidebar-item pl-12 {{ request()->is('quotations/create') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Create Quote
                    </a>
                    <a href="{{ url('/quotations') }}"
                        class="sidebar-item pl-12 {{ request()->is('quotations') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Manage Quotes
                    </a>
                    <!-- <a href="{{ url('/invoices') }}"
                        class="sidebar-item pl-12 {{ request()->is('invoices*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                        Invoices
                    </a> -->
                </div>
            </div>

            <!-- Reports -->
            <!-- <div x-data="{ open: {{ request()->is('reports*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="sidebar-item w-full {{ request()->is('reports*') ? 'text-primary-700/80 dark:text-primary-400/80' : '' }}">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="expanded" class="flex-1 text-left">Reports</span>
                    <i x-show="expanded" data-lucide="chevron-down"
                        :class="open ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"></i>
                </button>
                <div x-show="open && expanded" x-collapse class="mt-1 space-y-1">
                    <a href="{{ url('/reports/stock') }}"
                        class="sidebar-item pl-12 {{ request()->is('reports/stock') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Stock Reports
                    </a>
                    <a href="{{ url('/reports/sales') }}"
                        class="sidebar-item pl-12 {{ request()->is('reports/sales') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Sales Analytics
                    </a>
                    <a href="{{ url('/reports/financial') }}"
                        class="sidebar-item pl-12 {{ request()->is('reports/financial') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Financial Reports
                    </a>
                    <a href="{{ url('/reports/vendors') }}"
                        class="sidebar-item pl-12 {{ request()->is('reports/vendors') ? 'bg-primary-50/50 dark:bg-primary-900/10 text-primary-700/80 dark:text-primary-400/80' : '' }}">
                        Vendor Reports
                    </a>
                </div>
            </div> -->

            <!-- Section Label -->
            <div x-show="expanded" class="pt-4 pb-2">
                <span class="text-xs font-semibold text-slate-500/80 dark:text-slate-400/80 uppercase tracking-wider px-3">
                    System Management
                </span>
            </div>

            <!-- Database Backup -->
            <a href="{{ url('/backups') }}"
                class="sidebar-item {{ request()->is('backups*') ? 'active' : '' }}">
                <i data-lucide="database" class="w-5 h-5 flex-shrink-0"></i>
                <span x-show="expanded" x-transition class="flex-1">Database Backup</span>
            </a>

        </div>
    </div>

    <!-- Sidebar Toggle Button (Desktop) -->
    <div class="absolute -right-3 top-20 hidden lg:block">
        <button @click="toggleSidebar()"
            class="w-6 h-6 bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm border border-slate-200/50 dark:border-slate-700/50 rounded-full shadow-sm flex items-center justify-center hover:scale-110 transition-transform hover:border-slate-300/70">
            <i data-lucide="chevron-left"
                :class="!expanded && 'rotate-180'"
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