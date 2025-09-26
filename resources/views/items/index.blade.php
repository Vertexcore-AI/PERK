@extends('layouts.app')

@section('title', 'Items')

@section('page-title', 'Item Management')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Item Management</span>
    </div>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('items.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add New Item
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

    <!-- Items Table Card -->
    <div class="card animate-in" style="animation-delay: 0.1s">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Auto Parts Inventory</h3>

                <!-- Search and Filter -->
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <select id="category-filter" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="all">All Categories</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                        <input type="text"
                            id="item-search"
                            placeholder="Search items..."
                            class="pl-10 pr-4 py-2 w-64 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-modern w-full">
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Category</th>
                        <th scope="col">Batches</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Status</th>
                        <th scope="col">Last Updated</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($items as $item)
                        <tr class="group" data-category="{{ $item->category_id }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    
                                    <div>
                                        <span class="font-medium text-slate-900 dark:text-white">{{ $item->name }}</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">
                                            {{ $item->item_no }}
                                            @if($item->barcode)
                                                • {{ $item->barcode }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $item->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $batchCount = $item->batches->count();
                                    $avgSellingPrice = $item->batches->where('selling_price', '>', 0)->avg('selling_price');
                                    $avgCost = $item->batches->where('unit_cost', '>', 0)->avg('unit_cost');
                                @endphp
                                <div>
                                    @if($batchCount > 0)
                                        <span class="font-medium text-slate-900 dark:text-white">{{ $batchCount }} batch{{ $batchCount > 1 ? 'es' : '' }}</span>
                                        @if($avgSellingPrice > 0)
                                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                Avg. Price: LKR {{ number_format($avgSellingPrice, 2) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 text-xs">No batches</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $currentStock = $item->getCurrentStock();
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-900 dark:text-slate-100 font-medium">{{ $currentStock }}</span>
                                    <span class="text-slate-500 dark:text-slate-400">{{ $item->unit_of_measure }}</span>
                                    @if($currentStock <= $item->reorder_point)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                                            Low
                                        </span>
                                    @elseif($currentStock == 0)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300">
                                            Out of Stock
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-slate-600 dark:text-slate-400">{{ $item->updated_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('items.show', $item) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="View Item">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <a href="{{ route('items.edit', $item) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="Edit Item">
                                        <i data-lucide="edit" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <button type="button"
                                        class="btn-icon group/btn"
                                        data-tooltip="Adjust Stock"
                                        onclick="adjustStock({{ $item->id }})">
                                        <i data-lucide="package-plus" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-blue-600 dark:group-hover/btn:text-blue-400"></i>
                                    </button>
                                    @if($item->is_serialized)
                                        <button type="button"
                                            class="btn-icon group/btn"
                                            data-tooltip="Serial Numbers"
                                            onclick="manageSerials({{ $item->id }})">
                                            <i data-lucide="scan-line" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-green-600 dark:group-hover/btn:text-green-400"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn-icon group/btn"
                                            data-tooltip="Delete Item"
                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-rose-600 dark:group-hover/btn:text-rose-400"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                        <i data-lucide="package" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No items found</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Get started by adding your first auto part</p>
                                    <a href="{{ route('items.create') }}" class="btn-primary">
                                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                                        Add New Item
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600 dark:text-slate-400">
                        Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} results
                    </span>
                    <div class="flex items-center gap-2">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

  
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search and filter functionality
        const searchInput = document.getElementById('item-search');
        const categoryFilter = document.getElementById('category-filter');
        const tbody = document.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const category = row.dataset.category;

                const matchesSearch = text.includes(searchTerm);
                const matchesCategory = selectedCategory === 'all' || category === selectedCategory;

                row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('keyup', filterTable);
        }

        if (categoryFilter) {
            categoryFilter.addEventListener('change', filterTable);
        }
    });

    // Placeholder functions for future features
    function adjustStock(itemId) {
        alert('Stock adjustment feature will be implemented in Phase 2');
    }

    function manageSerials(itemId) {
        alert('Serial number management feature will be implemented in Phase 2');
    }
</script>
@endpush