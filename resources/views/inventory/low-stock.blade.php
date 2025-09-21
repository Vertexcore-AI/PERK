@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Low Stock Alert
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                    Items below their reorder point requiring attention
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('inventory.index') }}"
                   class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Inventory
                </a>
                <a href="{{ route('grns.create') }}"
                   class="btn-primary">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Create GRN
                </a>
            </div>
        </div>

        <!-- Alert Summary -->
        @if(count($lowStockItems) > 0)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="font-semibold text-yellow-800 dark:text-yellow-400">Low Stock Alert</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-500 mt-1">
                            {{ count($lowStockItems) }} items are below their reorder point with a total shortage of {{ number_format($totalShortage) }} units.
                            @if($criticalItems > 0)
                                {{ $criticalItems }} items are critically low (shortage > 10 units).
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Low Stock Items</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ count($lowStockItems) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Critical Items</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $criticalItems }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                        <i data-lucide="trending-down" class="w-6 h-6 text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Shortage</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalShortage) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                        Items Requiring Reorder
                    </h2>
                </div>

                @if(count($lowStockItems) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Item</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Current Stock</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Reorder Point</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Shortage</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Priority</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($lowStockItems as $stockItem)
                                    @php
                                        $priority = $stockItem['shortage'] > 10 ? 'critical' : ($stockItem['shortage'] > 5 ? 'high' : 'medium');
                                        $priorityColors = [
                                            'critical' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400',
                                            'high' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400',
                                            'medium' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400'
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                        <td class="py-4 px-4">
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-white">
                                                    {{ $stockItem['item']->name }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $stockItem['item']->item_no }}
                                                </p>
                                                @if($stockItem['item']->category)
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded">
                                                        {{ $stockItem['item']->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                                {{ number_format($stockItem['current_stock']) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="text-slate-600 dark:text-slate-400 font-medium">
                                                {{ number_format($stockItem['reorder_point']) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="font-bold text-red-600 dark:text-red-400">
                                                {{ number_format($stockItem['shortage']) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$priority] }}">
                                                {{ ucfirst($priority) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center justify-center gap-1">
                                                <a href="{{ route('items.show', $stockItem['item']) }}"
                                                   class="btn-icon group/btn"
                                                   data-tooltip="View Item">
                                                    <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                                </a>
                                                <button onclick="createReorderAlert({{ $stockItem['item']->id }}, '{{ $stockItem['item']->name }}', {{ $stockItem['shortage'] }})"
                                                        class="btn-icon group/btn"
                                                        data-tooltip="Create Reorder Alert">
                                                    <i data-lucide="bell" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-yellow-600 dark:group-hover/btn:text-yellow-400"></i>
                                                </button>
                                                <a href="{{ route('grns.create') }}?item_id={{ $stockItem['item']->id }}"
                                                   class="btn-icon group/btn"
                                                   data-tooltip="Create GRN">
                                                    <i data-lucide="plus-circle" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-green-600 dark:group-hover/btn:text-green-400"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center justify-between">
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            Showing {{ count($lowStockItems) }} low stock items
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('grns.create') }}" class="btn-primary">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Create GRN
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="check-circle" class="w-12 h-12 text-green-400 mx-auto mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400 text-lg font-medium">All items are adequately stocked!</p>
                        <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">
                            No items are currently below their reorder point.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('inventory.stock-by-item') }}" class="btn-primary">
                                <i data-lucide="package" class="w-4 h-4"></i>
                                View Stock Summary
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reorder Alert Modal -->
<div id="reorderAlertModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Create Reorder Alert</h3>
        <form id="reorderAlertForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Item</label>
                <input type="text" id="reorderItemName" readonly
                       class="input w-full bg-slate-100 dark:bg-slate-700">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Suggested Quantity</label>
                <input type="number" id="suggestedQuantity" readonly
                       class="input w-full bg-slate-100 dark:bg-slate-700">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                          class="input w-full" placeholder="Add any additional notes..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeReorderAlertModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Alert</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    function createReorderAlert(itemId, itemName, shortage) {
        document.getElementById('reorderItemName').value = itemName;
        document.getElementById('suggestedQuantity').value = shortage * 2; // Suggest 2x the shortage
        document.getElementById('reorderAlertModal').classList.remove('hidden');
    }

    function closeReorderAlertModal() {
        document.getElementById('reorderAlertModal').classList.add('hidden');
        document.getElementById('reorderAlertForm').reset();
    }

    function exportLowStockReport() {
        // Create a simple CSV export
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Item Name,Item No,Current Stock,Reorder Point,Shortage,Priority\n";

        @foreach($lowStockItems as $stockItem)
            @php
                $priority = $stockItem['shortage'] > 10 ? 'Critical' : ($stockItem['shortage'] > 5 ? 'High' : 'Medium');
            @endphp
            csvContent += "{{ $stockItem['item']->name }},{{ $stockItem['item']->item_no }},{{ $stockItem['current_stock'] }},{{ $stockItem['reorder_point'] }},{{ $stockItem['shortage'] }},{{ $priority }}\n";
        @endforeach

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "low_stock_report_" + new Date().toISOString().slice(0,10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    document.getElementById('reorderAlertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Reorder alert created successfully! (This would integrate with your procurement system)');
        closeReorderAlertModal();
    });
</script>
@endpush