@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8 relative z-10">
        <!-- Total Daily Sales -->
        <div class="card p-6 bg-gradient-to-br from-pink-500/5 via-purple-500/5 to-pink-500/5 border border-pink-400/20 backdrop-blur-md shadow-xl shadow-pink-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-pink-500/15">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white" id="daily-sales-amount">$0.00</div>
                    <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Total Daily Sales</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Daily Target</span>
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium" id="daily-sales-progress">(0%)</span>
                </div>
                <div class="w-full bg-gradient-to-r from-slate-200/50 via-slate-300/50 to-slate-200/50 dark:from-slate-700/50 dark:via-slate-600/50 dark:to-slate-700/50 rounded-full h-3 backdrop-blur-sm border border-slate-300/30 dark:border-slate-600/30">
                    <div class="bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-600 h-3 rounded-full transition-all duration-500 ease-out shadow-sm shadow-emerald-500/20" id="daily-sales-bar" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Total Inventory Value -->
        <div class="card p-6 bg-gradient-to-br from-blue-500/5 via-cyan-500/5 to-blue-500/5 border border-blue-400/20 backdrop-blur-md shadow-xl shadow-blue-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/15">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="package" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white" id="inventory-value">$0.00</div>
                    <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Inventory Value</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Stock Health</span>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium" id="inventory-turnover">(Good)</span>
                </div>
                <div class="w-full bg-gradient-to-r from-slate-200/50 via-slate-300/50 to-slate-200/50 dark:from-slate-700/50 dark:via-slate-600/50 dark:to-slate-700/50 rounded-full h-3 backdrop-blur-sm border border-slate-300/30 dark:border-slate-600/30">
                    <div class="bg-gradient-to-r from-blue-400 via-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500 ease-out shadow-sm shadow-blue-500/20" id="inventory-turnover-bar" style="width: 75%"></div>
                </div>
            </div>
        </div>

        <!-- Quotations Count -->
        <div class="card p-6 bg-gradient-to-br from-amber-500/5 via-yellow-500/5 to-amber-500/5 border border-amber-400/20 backdrop-blur-md shadow-xl shadow-amber-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-amber-500/15">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white" id="quotations-count">0</div>
                    <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Active Quotations</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Conversion Rate</span>
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium" id="quotations-conversion">(0%)</span>
                </div>
                <div class="w-full bg-gradient-to-r from-slate-200/50 via-slate-300/50 to-slate-200/50 dark:from-slate-700/50 dark:via-slate-600/50 dark:to-slate-700/50 rounded-full h-3 backdrop-blur-sm border border-slate-300/30 dark:border-slate-600/30">
                    <div class="bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 h-3 rounded-full transition-all duration-500 ease-out shadow-sm shadow-amber-500/20" id="quotations-conversion-bar" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Daily Profit -->
        <div class="card p-6 bg-gradient-to-br from-purple-500/5 via-violet-500/5 to-purple-500/5 border border-purple-400/20 backdrop-blur-md shadow-xl shadow-purple-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/15">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white" id="daily-profit-amount">$0.00</div>
                    <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Daily Profit</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Profit Margin</span>
                    <span class="text-xs text-purple-600 dark:text-purple-400 font-medium" id="profit-target-progress">(0%)</span>
                </div>
                <div class="w-full bg-gradient-to-r from-slate-200/50 via-slate-300/50 to-slate-200/50 dark:from-slate-700/50 dark:via-slate-600/50 dark:to-slate-700/50 rounded-full h-3 backdrop-blur-sm border border-slate-300/30 dark:border-slate-600/30">
                    <div class="bg-gradient-to-r from-purple-400 via-purple-500 to-purple-600 h-3 rounded-full transition-all duration-500 ease-out shadow-sm shadow-purple-500/20" id="profit-target-bar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 relative z-10">
        <!-- PERK Enterprises Image -->
        <div class="xl:col-span-2">
            <div class="bg-gradient-to-br from-indigo-500/8 via-purple-500/8 to-pink-500/8 border border-indigo-400/25 backdrop-blur-md shadow-xl shadow-indigo-500/15 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/20 hover:border-purple-400/30">
                <img src="{{ asset('images/bg.jpeg') }}"
                     alt="PERK Enterprises Vehicle"
                     class="w-full h-auto object-cover">
            </div>
        </div>

        <!-- Daily Sales -->
        <div class="card overflow-hidden bg-gradient-to-br from-emerald-500/5 via-green-500/5 to-emerald-500/5 border border-emerald-400/20 backdrop-blur-md shadow-xl shadow-emerald-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-emerald-500/15">
            <div class="bg-gradient-to-br from-emerald-600/90 via-emerald-500/90 to-emerald-600/90 backdrop-blur-sm text-white p-4 border-b border-emerald-400/30">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="text-2xl font-bold text-white" id="sidebar-daily-sales-total">$0.00</h4>
                        <p class="text-emerald-100 text-sm">Today's Sales</p>
                    </div>
                    <div class="flex items-center gap-1 text-xs font-medium bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full border border-white/30">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        <span id="sidebar-last-updated">--:--</span>
                    </div>
                </div>
                <div class="h-12 flex items-center justify-center bg-gradient-to-r from-emerald-700/30 via-emerald-600/30 to-emerald-700/30 backdrop-blur-sm rounded-lg border border-emerald-500/30">
                    <div class="flex items-center gap-2 text-emerald-100">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Live Sales Data</span>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-3" id="recent-sales-list">
                    <!-- Sales will be populated here by JavaScript -->
                    <div class="text-center text-slate-500 dark:text-slate-400 py-6">
                        <i data-lucide="shopping-cart" class="w-6 h-6 mx-auto mb-2 opacity-50"></i>
                        <p class="text-sm">Loading recent sales...</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('sales.index') }}" class="block p-3 bg-gradient-to-r from-slate-50/80 via-slate-100/80 to-slate-50/80 dark:from-slate-800/80 dark:via-slate-700/80 dark:to-slate-800/80 backdrop-blur-sm text-center text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider hover:from-slate-100/90 hover:via-slate-200/90 hover:to-slate-100/90 dark:hover:from-slate-700/90 dark:hover:via-slate-600/90 dark:hover:to-slate-700/90 transition-all duration-300 border-t border-slate-200/50 dark:border-slate-700/50">
                View All Sales
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Load dashboard data
            loadDashboardData();

            // Auto-refresh every 30 seconds
            setInterval(loadDashboardData, 30000);

            function loadDashboardData() {
                fetch('{{ route("dashboard.data") }}')
                    .then(response => response.json())
                    .then(data => {
                        updateDashboardCards(data);
                    })
                    .catch(error => {
                        console.error('Error loading dashboard data:', error);
                    });
            }

            function updateDashboardCards(data) {
                // Update Daily Sales
                const dailySales = data.daily_sales;
                const dailySalesAmount = document.getElementById('daily-sales-amount');
                if (dailySalesAmount) {
                    dailySalesAmount.textContent = '$' + dailySales.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }

                const salesChange = document.getElementById('daily-sales-change');
                if (salesChange) {
                    const changeSpan = salesChange.querySelector('span');
                    if (changeSpan) {
                        changeSpan.textContent = Math.abs(dailySales.change_percentage) + '%';
                    }

                    // Update change color and icon
                    if (dailySales.change_percentage >= 0) {
                        salesChange.className = 'flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400';
                        const icon = salesChange.querySelector('i');
                        if (icon) icon.setAttribute('data-lucide', 'trending-up');
                    } else {
                        salesChange.className = 'flex items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400';
                        const icon = salesChange.querySelector('i');
                        if (icon) icon.setAttribute('data-lucide', 'trending-down');
                    }
                }

                const dailySalesProgress = document.getElementById('daily-sales-progress');
                if (dailySalesProgress) {
                    dailySalesProgress.textContent = '(' + dailySales.target_percentage.toFixed(1) + '%)';
                }

                const dailySalesBar = document.getElementById('daily-sales-bar');
                if (dailySalesBar) {
                    dailySalesBar.style.width = dailySales.target_percentage + '%';
                }

                // Update Inventory Value
                const inventory = data.inventory_value;
                const inventoryValue = document.getElementById('inventory-value');
                if (inventoryValue) {
                    inventoryValue.textContent = '$' + inventory.total_value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }

                const inventoryItemsCount = document.getElementById('inventory-items-count');
                if (inventoryItemsCount) {
                    inventoryItemsCount.textContent = inventory.total_items + ' items';
                }

                const inventoryTurnover = document.getElementById('inventory-turnover');
                if (inventoryTurnover) {
                    inventoryTurnover.textContent = '(' + inventory.turnover_percentage + '%)';
                }

                const inventoryTurnoverBar = document.getElementById('inventory-turnover-bar');
                if (inventoryTurnoverBar) {
                    inventoryTurnoverBar.style.width = inventory.turnover_percentage + '%';
                }

                // Update Quotations
                const quotations = data.quotations;
                const quotationsCount = document.getElementById('quotations-count');
                if (quotationsCount) {
                    quotationsCount.textContent = quotations.active_count;
                }

                const quotationsTotal = document.getElementById('quotations-total');
                if (quotationsTotal) {
                    quotationsTotal.textContent = quotations.total_count + ' total';
                }

                const quotationsConversion = document.getElementById('quotations-conversion');
                if (quotationsConversion) {
                    quotationsConversion.textContent = '(' + quotations.conversion_rate + '%)';
                }

                const quotationsConversionBar = document.getElementById('quotations-conversion-bar');
                if (quotationsConversionBar) {
                    quotationsConversionBar.style.width = quotations.conversion_rate + '%';
                }

                // Update Daily Profit
                const profit = data.daily_profit;
                const dailyProfitAmount = document.getElementById('daily-profit-amount');
                if (dailyProfitAmount) {
                    dailyProfitAmount.textContent = '$' + profit.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }

                const profitMarginDisplay = document.getElementById('profit-margin-display');
                const profitMarginText = document.getElementById('profit-margin-text');
                if (profitMarginText) {
                    profitMarginText.textContent = profit.margin_percentage.toFixed(1) + '%';
                }

                // Update profit margin color
                if (profitMarginDisplay) {
                    if (profit.margin_percentage >= 20) {
                        profitMarginDisplay.className = 'flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400';
                    } else if (profit.margin_percentage >= 10) {
                        profitMarginDisplay.className = 'flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400';
                    } else {
                        profitMarginDisplay.className = 'flex items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400';
                    }
                }

                const profitTargetProgress = document.getElementById('profit-target-progress');
                if (profitTargetProgress) {
                    profitTargetProgress.textContent = '(' + profit.target_percentage.toFixed(1) + '%)';
                }

                const profitTargetBar = document.getElementById('profit-target-bar');
                if (profitTargetBar) {
                    profitTargetBar.style.width = profit.target_percentage + '%';
                }

                // Update Sidebar Daily Sales
                const sidebarDailySalesTotal = document.getElementById('sidebar-daily-sales-total');
                if (sidebarDailySalesTotal) {
                    sidebarDailySalesTotal.textContent = '$' + dailySales.amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }

                const sidebarLastUpdated = document.getElementById('sidebar-last-updated');
                if (sidebarLastUpdated) {
                    sidebarLastUpdated.textContent = data.updated_at;
                }

                // Update Recent Sales List
                const recentSalesList = document.getElementById('recent-sales-list');
                if (recentSalesList && data.recent_sales) {
                    if (data.recent_sales.length > 0) {
                        let salesHtml = '';
                        data.recent_sales.forEach((sale, index) => {
                            const colors = ['bg-emerald-500', 'bg-blue-500', 'bg-purple-500', 'bg-amber-500', 'bg-rose-500'];
                            const colorClass = colors[index % colors.length];

                            salesHtml += `
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg p-2">
                                            <div class="w-full h-full ${colorClass} rounded flex items-center justify-center">
                                                <i data-lucide="user" class="w-4 h-4 text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900 dark:text-white">${sale.customer_name}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">${sale.payment_method} • ${sale.created_at}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-slate-900 dark:text-white">$${sale.total_amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                                        <div class="text-xs text-slate-500">#${sale.sale_id}</div>
                                    </div>
                                </div>`;

                            if (index < data.recent_sales.length - 1) {
                                salesHtml += '<hr class="border-dashed border-slate-200 dark:border-slate-700">';
                            }
                        });
                        recentSalesList.innerHTML = salesHtml;
                    } else {
                        recentSalesList.innerHTML = `
                            <div class="text-center text-slate-500 dark:text-slate-400 py-8">
                                <i data-lucide="shopping-cart" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-sm">No sales today yet</p>
                            </div>`;
                    }
                }

                // Re-initialize Lucide icons for updated elements
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            // Format numbers for better readability
            function formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            }
        });

        // Alpine.js interactions for dashboard
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({
                // Dashboard functionality can be added here
                refreshData() {
                    loadDashboardData();
                }
            }));
        });
    </script>
@endpush