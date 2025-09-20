@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Batch Management
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                    Track and manage inventory batches with FIFO support
                </p>
            </div>
            <!-- <div class="flex items-center gap-3">
                <a href="{{ route('batches.stock-value') }}"
                   class="btn-secondary">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                    Stock Value
                </a>
                <a href="{{ route('batches.expiring') }}"
                   class="btn-secondary">
                    <i data-lucide="calendar-x" class="w-4 h-4"></i>
                    Expiring Batches
                </a>
            </div> -->
        </div>

        <!-- Statistics Cards -->
        <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <i data-lucide="package" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Batches</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalBatches) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Active Batches</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($activeBatches) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Low Stock</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($lowStockBatches) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Value</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($totalValue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search batch, item, vendor..."
                               class="input w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Item</label>
                        <select name="item_id" class="select w-full">
                            <option value="">All Items</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Vendor</label>
                        <select name="vendor_id" class="select w-full">
                            <option value="">All Vendors</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                        <select name="status" class="select w-full">
                            <option value="">All Batches</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="depleted" {{ request('status') == 'depleted' ? 'selected' : '' }}>Depleted</option>
                            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        </select>
                    </div> -->

                    <div class="flex items-end gap-2 mb-2">
                        <button type="submit" class="btn-primary">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Search
                        </button>
                        <a href="{{ route('batches.index') }}" class="btn-secondary">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Batches Table -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                        Batches ({{ $batches->total() }})
                    </h2>
                </div>

                @if($batches->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Batch</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Item</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Vendor</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Stock</th>

                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Total Value</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Status</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Created</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($batches as $batch)
                                    @php
                                        $currentStock = $batch->inventoryStock->sum('quantity');
                                        $stockValue = $currentStock * $batch->unit_cost;
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                        <td class="py-4 px-4">
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-white">
                                                    {{ $batch->batch_no }}
                                                </p>
                                                @if($batch->expiry_date)
                                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                                        Expires: {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-white">
                                                    {{ $batch->item->name }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $batch->item->item_no }}
                                                </p>
                                                @if($batch->item->category)
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded">
                                                        {{ $batch->item->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <p class="text-slate-700 dark:text-slate-300">{{ $batch->vendor->name }}</p>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($currentStock > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                                    {{ $currentStock }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                                    0
                                                </span>
                                            @endif
                                        </td>
                                       
                                        <td class="py-4 px-4 text-right font-bold text-slate-900 dark:text-white">
                                            ${{ number_format($stockValue, 2) }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($currentStock > 0)
                                                @if($batch->item->reorder_point > 0 && $currentStock <= $batch->item->reorder_point)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                                                        Low Stock
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                                        Available
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                                    Depleted
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center text-sm text-slate-600 dark:text-slate-400">
                                            {{ \Carbon\Carbon::parse($batch->created_at)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('batches.show', $batch) }}"
                                                   class="btn-icon group/btn"
                                                   data-tooltip="View Details">
                                                    <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                                </a>
                                                <a href="{{ route('batches.edit', $batch) }}"
                                                   class="btn-icon group/btn"
                                                   data-tooltip="Edit Batch">
                                                    <i data-lucide="edit-3" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-yellow-600 dark:group-hover/btn:text-yellow-400"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $batches->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="package-x" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400">No batches found matching your criteria.</p>
                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">
                            Try adjusting your filters or create new batches through GRN entry.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush