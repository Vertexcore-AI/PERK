@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-7x2 mx-0">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Inventory Management
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                    Track stock levels across stores and bins
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('inventory.stock-by-item') }}"
                   class="btn-secondary">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    Stock by Item
                </a>
                <a href="{{ route('inventory.low-stock') }}"
                   class="btn-secondary">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    Low Stock
                </a>
                <a href="{{ route('inventory.transfer.show') }}"
                   class="btn-primary">
                    <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
                    Transfer Stock
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <i data-lucide="package" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Items</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalItems) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <i data-lucide="boxes" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Quantity</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalQuantity) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Low Stock Items</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($lowStockItems) }}</p>
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
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search items..."
                               class="input w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Store</label>
                        <select name="store_id" class="select w-full">
                            <option value="">All Stores</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->store_name }}
                                </option>
                            @endforeach
                        </select>
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
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bin</label>
                        <select name="bin_id" class="select w-full">
                            <option value="">All Bins</option>
                            @foreach($bins as $bin)
                                <option value="{{ $bin->id }}" {{ request('bin_id') == $bin->id ? 'selected' : '' }}>
                                    {{ $bin->store->store_name }} - {{ $bin->bin_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Search
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn-secondary">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                        Current Inventory ({{ $inventory->total() }})
                    </h2>
                </div>

                @if($inventory->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Item</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Store</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Bin</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Batch</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Quantity</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Unit Cost</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Total Value</th>
   
                                    <!-- <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Actions</th> -->
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($inventory as $stock)
                                    @php
                                        $stockValue = $stock->quantity * ($stock->batch->unit_cost ?? 0);
                                        $isLowStock = $stock->item->reorder_point > 0 && $stock->quantity <= $stock->item->reorder_point;
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 {{ $isLowStock ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                                        <td class="py-4 px-4">
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-white">
                                                    {{ $stock->item->name }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $stock->item->item_no }}
                                                </p>
                                                @if($stock->item->category)
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded">
                                                        {{ $stock->item->category->name }}
                                                    </span>
                                                @endif
                                                @if($isLowStock)
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 rounded">
                                                        Low Stock
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <p class="text-slate-700 dark:text-slate-300">{{ $stock->store->store_name }}</p>
                                        </td>
                                        <td class="py-4 px-4">
                                            <p class="text-slate-700 dark:text-slate-300">
                                                {{ $stock->bin ? $stock->bin->bin_name : 'No Bin' }}
                                            </p>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <span class="font-mono text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 px-2 py-1 rounded">
                                                    {{ $stock->batch->batch_no }}
                                                </span>
                                                <a href="{{ route('batches.show', $stock->batch) }}" class="ml-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                </a>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                Vendor: {{ $stock->batch->vendor->name }}
                                            </p>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $isLowStock ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' }}">
                                                {{ $stock->quantity }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right font-medium text-slate-900 dark:text-white">
                                            ${{ number_format($stock->batch->unit_cost ?? 0, 2) }}
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-slate-900 dark:text-white">
                                            ${{ number_format($stockValue, 2) }}
                                        </td>
                                 
                                        <!-- <td class="py-4 px-4">
                                            <div class="flex items-center justify-center gap-1">
                                                <button onclick="adjustStock({{ $stock->id }}, {{ $stock->quantity }})"
                                                        class="btn-icon group/btn"
                                                        data-tooltip="Adjust Quantity">
                                                    <i data-lucide="edit-3" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-yellow-600 dark:group-hover/btn:text-yellow-400"></i>
                                                </button>
                                            </div>
                                        </td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $inventory->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="package-x" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400">No inventory records found.</p>
                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">
                            Try adjusting your filters or add new inventory through GRN entry.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustStockModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Adjust Stock Quantity</h3>
        <form id="adjustStockForm">
            <input type="hidden" id="adjustStockId" name="inventory_stock_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Current Quantity</label>
                <input type="number" id="currentQuantity" readonly
                       class="input w-full bg-slate-100 dark:bg-slate-700">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">New Quantity</label>
                <input type="number" name="new_quantity" min="0" required
                       class="input w-full" placeholder="Enter new quantity">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Reason</label>
                <textarea name="reason" required rows="3"
                          class="input w-full" placeholder="Enter reason for adjustment"></textarea>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeAdjustStockModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Adjust Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    function adjustStock(stockId, currentQty) {
        document.getElementById('adjustStockId').value = stockId;
        document.getElementById('currentQuantity').value = currentQty;
        document.getElementById('adjustStockModal').classList.remove('hidden');
    }

    function closeAdjustStockModal() {
        document.getElementById('adjustStockModal').classList.add('hidden');
        document.getElementById('adjustStockForm').reset();
    }

    document.getElementById('adjustStockForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        try {
            const response = await fetch('{{ route('inventory.adjust-stock') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('An error occurred while adjusting stock.');
        }

        closeAdjustStockModal();
    });
</script>
@endpush