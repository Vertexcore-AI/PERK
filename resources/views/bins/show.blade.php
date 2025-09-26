@extends('layouts.app')

@section('title', 'Bin Details')

@section('page-title', 'Storage Bin Details')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('bins.index') }}">Bins</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">{{ $bin->name }}</span>
    </div>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('bins.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Bins
        </a>
        <a href="{{ route('bins.edit', $bin) }}" class="btn-primary">
            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
            Edit Bin
        </a>
        <form action="{{ route('bins.destroy', $bin) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this bin?')">
                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                Delete
            </button>
        </form>
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
        <!-- Bin Information Card -->
        <div class="card animate-in" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/25">
                        <i data-lucide="grid-3x3" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $bin->name }}</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Storage Bin Information</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Bin Code -->
                <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Bin Code</span>
                    <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $bin->code }}</span>
                </div>

                <!-- Bin Name -->
                <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Bin Name</span>
                    <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $bin->name }}</span>
                </div>

                <!-- Store -->
                <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Store</span>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-slate-900 dark:text-white font-medium">{{ $bin->store->store_name }}</span>
                    </div>
                </div>

                <!-- Store Location -->
                <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Store Location</span>
                    <span class="text-sm text-slate-900 dark:text-white">{{ $bin->store->store_location ?? 'No location specified' }}</span>
                </div>

                <!-- Description -->
                <div class="py-3">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400 block mb-2">Description</span>
                    @if($bin->description)
                        <p class="text-sm text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                            {{ $bin->description }}
                        </p>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400 italic">No description provided</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bin Statistics & Timeline Card -->
        <div class="card animate-in" style="animation-delay: 0.2s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Bin Statistics</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Usage and timeline information</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="package" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wide">Items</span>
                        </div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalItems ?? 0 }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Total items stored</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="dollar-sign" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wide">Value</span>
                        </div>
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">LKR {{ number_format($totalValue ?? 0, 2) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Total inventory value</p>
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
                                <p class="text-sm font-medium text-slate-900 dark:text-white">Bin Created</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $bin->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        @if($bin->updated_at != $bin->created_at)
                            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <i data-lucide="edit" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">Last Updated</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $bin->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Items (Future Phase) -->
    <div class="card mt-6 animate-in" style="animation-delay: 0.3s">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Inventory Items</h3>
                <span class="badge badge-info">Coming in Phase 2</span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Items currently stored in this bin</p>
        </div>

        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="package" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
            </div>
            <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">No Items Yet</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto">
                Inventory item tracking will be available in Phase 2. Items will automatically be assigned to bins during the GRN (Goods Received Note) process.
            </p>
        </div>
    </div>
@endsection