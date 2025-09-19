@extends('layouts.app')

@section('title', 'Stores')

@section('page-title', 'Store Management')

@section('breadcrumb')
<li class="flex items-center">
    <a href="{{ url('/dashboard') }}"
        class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
</li>
<li class="text-slate-600 dark:text-slate-300">Stores</li>
@endsection

@section('page-actions')
<div class="flex items-center gap-3">
    <a href="{{ route('stores.create') }}" class="btn-primary">
        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
        Add New Store
    </a>
    <a href="{{ route('binstorecsv.exprot') }}" class="btn-secondary">
        <i data-lucide="download" class="w-5 h-5 mr-2"></i>
        Export
    </a>

</div>
@endsection

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="alert alert-success mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
        </div>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <button @click="show = false"
        class="p-1 hover:bg-emerald-200 dark:hover:bg-emerald-800 rounded-lg transition-colors">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>
@endif

<!-- Stores Table Card -->
<div class="card animate-in" style="animation-delay: 0.1s">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Stores & Locations</h3>

            <!-- Search -->
            <div class="relative">
                <i data-lucide="search"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                <input type="text" id="store-search" placeholder="Search stores..."
                    class="pl-10 pr-4 py-2 w-64 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th scope="col">Store</th>
                    <th scope="col">Location</th>
                    <th scope="col">Created</th>
                    <th scope="col" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($stores as $store)
                <tr class="group">
                    <td>
                        <div class="flex items-center gap-3">

                            <div>
                                <span class="font-medium text-slate-900 dark:text-white">{{ $store->store_name }}</span>
                                edit.blade
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <span class="text-slate-900 dark:text-slate-100">{{ $store->store_location ?? 'No location
                                specified' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="text-slate-500 dark:text-slate-400">
                            {{ $store->created_at->format('M d, Y') }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-1">

                            <a href="{{ route('stores.edit', $store) }}" class="btn-icon group/btn"
                                data-tooltip="Edit Store">
                                <i data-lucide="edit"
                                    class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                            </a>
                            <a href="{{ route('bins.index', ['store' => $store->id]) }}" class="btn-icon group/btn"
                                data-tooltip="Manage Bins">
                                <i data-lucide="grid-3x3"
                                    class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-orange-600 dark:group-hover/btn:text-orange-400"></i>
                            </a>
                            <form action="{{ route('stores.destroy', $store) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon group/btn" data-tooltip="Delete Store"
                                    onclick="return confirm('Are you sure you want to delete this store?')">
                                    <i data-lucide="trash-2"
                                        class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-rose-600 dark:group-hover/btn:text-rose-400"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center">
                            <div
                                class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                <i data-lucide="warehouse" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No stores found</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Get started by adding your first
                                store location</p>
                            <a href="{{ route('stores.create') }}" class="btn-primary">
                                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                                Add New Store
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stores->hasPages())
    <div class="p-6 border-t border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600 dark:text-slate-400">
                Showing {{ $stores->firstItem() }} to {{ $stores->lastItem() }} of {{ $stores->total() }} results
            </span>
            <div class="flex items-center gap-2">
                {{ $stores->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Store Statistics -->
<!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="warehouse" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <span class="badge badge-info">Total</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stores->total() ?? $stores->count() }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Total Stores</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="badge badge-success">Recent</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stores->where('created_at', '>=', now()->subDays(30))->count() }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Added This Month</p>
        </div>
    </div> -->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple search functionality
        const searchInput = document.getElementById('store-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const tbody = document.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush