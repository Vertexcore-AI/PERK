@extends('layouts.app')

@section('title', 'Store Details')

@section('page-title', 'Store Details')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('stores.index') }}">Stores</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">{{ $store->store_name }}</span>
    </div>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('stores.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Stores
        </a>
        <a href="{{ route('stores.edit', $store) }}" class="btn-primary">
            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
            Edit Store
        </a>
        <a href="{{ route('bins.index', ['store' => $store->id]) }}" class="btn-secondary">
            <i data-lucide="grid-3x3" class="w-4 h-4 mr-2"></i>
            Manage Bins
        </a>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="p-1 hover:bg-emerald-200 dark:hover:bg-emerald-800 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Store Information Card -->
        <div class="card animate-in" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                        <i data-lucide="warehouse" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $store->store_name }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Store Information</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Store Name -->
                <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Store Name</span>
                    <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $store->store_name }}</span>
                </div>

                <!-- Location -->
                <div class="py-3">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400 block mb-2">Location</span>
                    @if($store->store_location)
                        <p class="text-sm text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                            {{ $store->store_location }}
                        </p>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400 italic">No location specified</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Store Statistics Card -->
        <div class="card animate-in" style="animation-delay: 0.2s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Store Statistics</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Overview and metrics</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="grid-3x3" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wide">Bins</span>
                        </div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $store->bins_count ?? $store->bins->count() }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Storage bins</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="package" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wide">Items</span>
                        </div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">0</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Total items (Phase 2)</p>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="space-y-4">
                    <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300">Timeline</h4>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                                <i data-lucide="plus" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-900 dark:text-white">Store Created</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $store->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        @if($store->updated_at != $store->created_at)
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <i data-lucide="edit" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">Last Updated</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $store->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Bins -->
    <div class="card mt-6 animate-in" style="animation-delay: 0.3s">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Storage Bins</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Bins configured for this store</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('bins.create', ['store_id' => $store->id]) }}" class="btn-primary btn-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                        Add Bin
                    </a>
                    <a href="{{ route('bins.index', ['store' => $store->id]) }}" class="btn-secondary btn-sm">
                        <i data-lucide="external-link" class="w-4 h-4 mr-1"></i>
                        View All
                    </a>
                </div>
            </div>
        </div>

        @if($store->bins->count() > 0)
            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($store->bins->take(5) as $bin)
                    <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                    <i data-lucide="grid-3x3" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">{{ $bin->code }} - {{ $bin->name }}</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $bin->description ?? 'No description' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('bins.show', $bin) }}" class="btn-icon group/btn">
                                    <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-blue-600 dark:group-hover/btn:text-blue-400"></i>
                                </a>
                                <a href="{{ route('bins.edit', $bin) }}" class="btn-icon group/btn">
                                    <i data-lucide="edit" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($store->bins->count() > 5)
                <div class="p-6 border-t border-slate-200 dark:border-slate-700 text-center">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        Showing 5 of {{ $store->bins->count() }} bins
                    </p>
                    <a href="{{ route('bins.index', ['store' => $store->id]) }}" class="btn-secondary btn-sm">
                        <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                        View All {{ $store->bins->count() }} Bins
                    </a>
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="grid-3x3" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">No Storage Bins</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto mb-4">
                    This store doesn't have any storage bins yet. Add bins to organize inventory within this store location.
                </p>
                <a href="{{ route('bins.create', ['store_id' => $store->id]) }}" class="btn-primary">
                    <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                    Add First Bin
                </a>
            </div>
        @endif
    </div>
@endsection