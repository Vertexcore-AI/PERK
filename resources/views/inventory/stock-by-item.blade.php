@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Stock by Item
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                    View aggregated stock levels for each item
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('inventory.index') }}"
                   class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Inventory
                </a>
                <a href="{{ route('inventory.low-stock') }}"
                   class="btn-secondary">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    Low Stock Alert
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
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Stock</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalStock) }}</p>
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
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($lowStockCount) }}</p>
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
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">LKR {{ number_format($totalValue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search items..."
                               class="input w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category</label>
                        <select name="category_id" class="select w-full">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Filter</label>
                        <select name="low_stock" class="select w-full">
                            <option value="">All Items</option>
                            <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>Low Stock Only</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Search
                        </button>
                        <a href="{{ route('inventory.stock-by-item') }}" class="btn-secondary">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                        Item Stock Summary ({{ $items->count() }})
                    </h2>
                </div>

                @if($items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Item</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Total Stock</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Reorder Point</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Stock Value</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Store Distribution</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Status</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($items as $itemData)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 {{ $itemData['is_low_stock'] ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                                        <td class="py-4 px-4">
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-white">
                                                    {{ $itemData['item']->name }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $itemData['item']->item_no }}
                                                </p>
                                                @if($itemData['item']->category)
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded">
                                                        {{ $itemData['item']->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                                {{ $itemData['is_low_stock'] ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400' }}">
                                                {{ number_format($itemData['total_stock']) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($itemData['item']->reorder_point > 0)
                                                <span class="text-slate-600 dark:text-slate-400">
                                                    {{ number_format($itemData['item']->reorder_point) }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-slate-900 dark:text-white">
                                            LKR {{ number_format($itemData['stock_value'], 2) }}
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="space-y-1">
                                                @foreach($itemData['stores'] as $storeData)
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="text-slate-600 dark:text-slate-400">
                                                            {{ $storeData['store']->store_name }}
                                                        </span>
                                                        <span class="font-medium text-slate-900 dark:text-white">
                                                            {{ number_format($storeData['quantity']) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($itemData['total_stock'] == 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                                    Out of Stock
                                                </span>
                                            @elseif($itemData['is_low_stock'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                                                    Low Stock
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                                    In Stock
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-center gap-1">
                                                <button onclick="viewItemDetails({{ $itemData['item']->id }})"
                                                        class="btn-icon group/btn"
                                                        data-tooltip="View Details">
                                                    <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                                </button>
                                                <a href="{{ route('items.edit', $itemData['item']) }}"
                                                   class="btn-icon group/btn"
                                                   data-tooltip="Edit Item">
                                                    <i data-lucide="edit-3" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-yellow-600 dark:group-hover/btn:text-yellow-400"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="package-x" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400">No items found matching your criteria.</p>
                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">
                            Try adjusting your filters or add new items.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Item Details Modal -->
<div id="itemDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Item Stock Details</h3>
            <button onclick="closeItemDetailsModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="itemDetailsContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    async function viewItemDetails(itemId) {
        document.getElementById('itemDetailsModal').classList.remove('hidden');
        document.getElementById('itemDetailsContent').innerHTML = '<div class="text-center py-4">Loading...</div>';

        try {
            const response = await fetch(`{{ route('inventory.api.item-stock') }}?item_id=${itemId}`);
            const data = await response.json();

            let content = `
                <div class="mb-4">
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-2">Stock Locations</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-2">Store</th>
                                    <th class="text-left py-2">Bin</th>
                                    <th class="text-left py-2">Batch</th>
                                    <th class="text-center py-2">Quantity</th>
                                    <th class="text-right py-2">Unit Cost</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            `;

            data.stocks.forEach(stock => {
                content += `
                    <tr>
                        <td class="py-2">${stock.store}</td>
                        <td class="py-2">${stock.bin}</td>
                        <td class="py-2"><span class="font-mono text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">${stock.batch}</span></td>
                        <td class="py-2 text-center">${stock.quantity}</td>
                        <td class="py-2 text-right">LKR ${parseFloat(stock.unit_cost).toFixed(2)}</td>
                    </tr>
                `;
            });

            content += `
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-slate-100 dark:bg-slate-700 rounded-lg p-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        <strong>Total Quantity:</strong> ${data.total_quantity}
                    </p>
                </div>
            `;

            document.getElementById('itemDetailsContent').innerHTML = content;
        } catch (error) {
            document.getElementById('itemDetailsContent').innerHTML = '<div class="text-center py-4 text-red-600">Error loading item details</div>';
        }
    }

    function closeItemDetailsModal() {
        document.getElementById('itemDetailsModal').classList.add('hidden');
    }
</script>
@endpush