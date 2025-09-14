@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="btn-secondary">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                Filter
            </button>
            <div x-show="open" @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 z-50">
                <div class="p-2">
                    <label class="flex items-center p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer">
                        <input type="checkbox" class="form-checkbox" checked> <span class="ml-2 text-sm">Role</span>
                    </label>
                    <label class="flex items-center p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer">
                        <input type="checkbox" class="form-checkbox" checked> <span class="ml-2 text-sm">Team</span>
                    </label>
                    <label class="flex items-center p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer">
                        <input type="checkbox" class="form-checkbox" checked> <span class="ml-2 text-sm">Email</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- Invoices Awaiting Payment -->
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-slate-600 dark:text-slate-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">45<span class="text-slate-500">/76</span></div>
                        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Invoices Awaiting Payment</h3>
                    </div>
                </div>
                <button class="btn-icon">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500">Invoices Awaiting</span>
                    <div class="text-right">
                        <span class="text-xs text-slate-900 dark:text-white font-medium">$5,569</span>
                        <span class="text-xs text-slate-400 ml-1">(56%)</span>
                    </div>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full" style="width: 56%"></div>
                </div>
            </div>
        </div>

        <!-- Converted Leads -->
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="radio" class="w-6 h-6 text-slate-600 dark:text-slate-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">48<span class="text-slate-500">/86</span></div>
                        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Converted Leads</h3>
                    </div>
                </div>
                <button class="btn-icon">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500">Converted Leads</span>
                    <div class="text-right">
                        <span class="text-xs text-slate-900 dark:text-white font-medium">52 Completed</span>
                        <span class="text-xs text-slate-400 ml-1">(63%)</span>
                    </div>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="bg-amber-500 h-2 rounded-full" style="width: 63%"></div>
                </div>
            </div>
        </div>

        <!-- Projects In Progress -->
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="briefcase" class="w-6 h-6 text-slate-600 dark:text-slate-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">16<span class="text-slate-500">/20</span></div>
                        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Projects In Progress</h3>
                    </div>
                </div>
                <button class="btn-icon">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500">Projects In Progress</span>
                    <div class="text-right">
                        <span class="text-xs text-slate-900 dark:text-white font-medium">16 Completed</span>
                        <span class="text-xs text-slate-400 ml-1">(78%)</span>
                    </div>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 78%"></div>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="activity" class="w-6 h-6 text-slate-600 dark:text-slate-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">46.59<span class="text-slate-500">%</span></div>
                        <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400">Conversion Rate</h3>
                    </div>
                </div>
                <button class="btn-icon">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-slate-500">Conversion Rate</span>
                    <div class="text-right">
                        <span class="text-xs text-slate-900 dark:text-white font-medium">$2,254</span>
                        <span class="text-xs text-slate-400 ml-1">(46%)</span>
                    </div>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="bg-rose-500 h-2 rounded-full" style="width: 46%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Payment Records Chart -->
        <div class="xl:col-span-2 card">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Payment Record</h5>
                    <div class="flex items-center gap-2">
                        <button class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-900/50 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
                        </button>
                        <button class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mx-auto"></i>
                        </button>
                        <button class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors">
                            <i data-lucide="expand" class="w-4 h-4 mx-auto"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-0">
                <div class="h-80 flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
                    <p class="text-slate-500 dark:text-slate-400">Chart Area</p>
                </div>
            </div>
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg">
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Awaiting</div>
                        <h6 class="text-lg font-bold text-slate-900 dark:text-white">$5,486</h6>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1 mt-2">
                            <div class="bg-primary-600 h-1 rounded-full" style="width: 81%"></div>
                        </div>
                    </div>
                    <div class="p-4 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg">
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Completed</div>
                        <h6 class="text-lg font-bold text-slate-900 dark:text-white">$9,275</h6>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1 mt-2">
                            <div class="bg-emerald-500 h-1 rounded-full" style="width: 82%"></div>
                        </div>
                    </div>
                    <div class="p-4 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg">
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Rejected</div>
                        <h6 class="text-lg font-bold text-slate-900 dark:text-white">$3,868</h6>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1 mt-2">
                            <div class="bg-rose-500 h-1 rounded-full" style="width: 68%"></div>
                        </div>
                    </div>
                    <div class="p-4 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg">
                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Revenue</div>
                        <h6 class="text-lg font-bold text-slate-900 dark:text-white">$50,668</h6>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1 mt-2">
                            <div class="bg-slate-900 dark:bg-slate-100 h-1 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="card overflow-hidden">
            <div class="bg-primary-600 text-white p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="text-2xl font-bold text-white">30,569</h4>
                        <p class="text-primary-100">Total Sales</p>
                    </div>
                    <span class="badge bg-white text-primary-600 px-3 py-1 rounded-full text-sm font-medium">12%</span>
                </div>
                <div class="h-16 flex items-center justify-center bg-primary-700/50 rounded-lg">
                    <p class="text-primary-200 text-sm">Chart Area</p>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-lg p-2">
                                <div class="w-full h-full bg-emerald-500 rounded"></div>
                            </div>
                            <div>
                                <a href="#" class="font-medium text-slate-900 dark:text-white hover:text-primary-600 transition-colors">Shopify eCommerce Store</a>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Development</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-slate-900 dark:text-white">$1200</div>
                            <div class="text-xs text-slate-500">6 Projects</div>
                        </div>
                    </div>
                    <hr class="border-dashed border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-lg p-2">
                                <div class="w-full h-full bg-blue-500 rounded"></div>
                            </div>
                            <div>
                                <a href="#" class="font-medium text-slate-900 dark:text-white hover:text-primary-600 transition-colors">iOS Apps Development</a>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Development</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-slate-900 dark:text-white">$1450</div>
                            <div class="text-xs text-slate-500">3 Projects</div>
                        </div>
                    </div>
                    <hr class="border-dashed border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-lg p-2">
                                <div class="w-full h-full bg-purple-500 rounded"></div>
                            </div>
                            <div>
                                <a href="#" class="font-medium text-slate-900 dark:text-white hover:text-primary-600 transition-colors">Figma Dashboard Design</a>
                                <div class="text-xs text-slate-500 dark:text-slate-400">UI/UX Design</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-slate-900 dark:text-white">$1250</div>
                            <div class="text-xs text-slate-500">5 Projects</div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="#" class="block p-4 bg-slate-50 dark:bg-slate-800 text-center text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                Full Details
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Simple Alpine.js interactions for dashboard
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({
                // Dashboard functionality can be added here
            }));
        });
    </script>
@endpush