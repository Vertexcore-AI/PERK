@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Batch Details
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                    Batch #{{ $batch->batch_no }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('batches.index') }}"
                   class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Batches
                </a>
                <a href="{{ route('batches.edit', $batch) }}"
                   class="btn-primary">
                   
                    Edit Batch
                </a>
            </div>
        </div>

        <!-- Batch Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Basic Info -->
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                        Batch Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Batch Number</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">{{ $batch->batch_no }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Item</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">{{ $batch->item->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $batch->item->item_no }}</p>
                            @if($batch->item->category)
                                <span class="inline-block mt-1 px-2 py-1 text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded">
                                    {{ $batch->item->category->name }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Vendor</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">{{ $batch->vendor->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $batch->vendor->contact }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Unit Cost</label>
                            <p class="text-slate-900 dark:text-white font-bold text-lg mt-1">
                                ${{ number_format($batch->unit_cost, 2) }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Selling Price</label>
                            @if($batch->selling_price > 0)
                                <p class="text-green-600 dark:text-green-400 font-bold text-lg mt-1">
                                    ${{ number_format($batch->selling_price, 2) }}
                                </p>
                                @php
                                    $margin = (($batch->selling_price - $batch->unit_cost) / $batch->unit_cost) * 100;
                                @endphp
                                <p class="text-sm {{ $margin > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ number_format($margin, 1) }}% margin
                                </p>
                            @else
                                <p class="text-slate-400 dark:text-slate-500 font-medium text-lg mt-1">
                                    Not set
                                </p>
                            @endif
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Received Date</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">
                                {{ \Carbon\Carbon::parse($batch->received_date)->format('d/m/Y') }}
                            </p>
                        </div>
                        @if($batch->expiry_date)
                            <div>
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Expiry Date</label>
                                <p class="text-slate-900 dark:text-white font-medium mt-1">
                                    {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }}
                                </p>
                                @if(\Carbon\Carbon::parse($batch->expiry_date)->isPast())
                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded">
                                        Expired
                                    </span>
                                @elseif(\Carbon\Carbon::parse($batch->expiry_date)->diffInDays() <= 30)
                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 rounded">
                                        Expires Soon
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    @if($batch->notes)
                        <div class="mt-4">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Notes</label>
                            <p class="text-slate-900 dark:text-white mt-1">{{ $batch->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                        Stock Statistics
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Total Received</label>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalReceived }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Current Stock</label>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $currentStock }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Total Sold</label>
                            <p class="text-2xl font-bold text-slate-600 dark:text-slate-400">{{ $totalSold }}</p>
                        </div>
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Stock Value (Cost)</label>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                ${{ number_format($stockValue, 2) }}
                            </p>
                        </div>
                        @if($batch->selling_price > 0)
                            <div>
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Potential Selling Value</label>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    ${{ number_format($currentStock * $batch->selling_price, 2) }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Locations -->
        @if($batch->inventoryStock->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                        Stock Locations
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Store</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Bin</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Quantity</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Value</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Last Updated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($batch->inventoryStock as $stock)
                                    <tr>
                                        <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">
                                            {{ $stock->store->store_name }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-700 dark:text-slate-300">
                                            {{ $stock->bin ? $stock->bin->bin_name : 'No Bin' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                                {{ $stock->quantity }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-medium text-slate-900 dark:text-white">
                                            ${{ number_format($stock->quantity * $batch->unit_cost, 2) }}
                                        </td>
                                        <td class="py-3 px-4 text-center text-sm text-slate-600 dark:text-slate-400">
                                            {{ \Carbon\Carbon::parse($stock->last_updated)->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- GRN Information -->
        @if($batch->grnItems->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                        GRN Information
                    </h2>
                    @foreach($batch->grnItems as $grnItem)
                        <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 mb-4 last:mb-0">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">GRN #</label>
                                    <p class="text-slate-900 dark:text-white font-medium">{{ $grnItem->grn->grn_id }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Invoice #</label>
                                    <p class="text-slate-900 dark:text-white font-medium">{{ $grnItem->grn->inv_no }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Received Qty</label>
                                    <p class="text-slate-900 dark:text-white font-medium">{{ $grnItem->received_qty }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Unit Cost</label>
                                    <p class="text-slate-900 dark:text-white font-medium">${{ number_format($grnItem->unit_cost, 2) }}</p>
                                </div>
                                @if($grnItem->selling_price > 0)
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Selling Price</label>
                                        <p class="text-green-600 dark:text-green-400 font-medium">${{ number_format($grnItem->selling_price, 2) }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Serial Numbers -->
        @if($batch->item->is_serialized && $batch->serialItems->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                            Serial Numbers ({{ $batch->serialItems->count() }})
                        </h2>
                        <button onclick="generateSerials()" class="btn-primary">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Generate Serials
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($batch->serialItems as $serial)
                            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                                <p class="font-mono text-sm text-slate-900 dark:text-white">{{ $serial->serial_no }}</p>
                                <div class="mt-1">
                                    @if($serial->status == 0)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded">
                                            Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded">
                                            Sold
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Generate Serials Modal -->
<div id="generateSerialsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Generate Serial Numbers</h3>
        <form id="generateSerialsForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Quantity</label>
                <input type="number" name="quantity" min="1" max="1000" required
                       class="input w-full" placeholder="Enter quantity">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Prefix (Optional)</label>
                <input type="text" name="prefix" maxlength="10"
                       class="input w-full" placeholder="e.g., SN">
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeGenerateSerialsModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Generate</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();

    function generateSerials() {
        document.getElementById('generateSerialsModal').classList.remove('hidden');
    }

    function closeGenerateSerialsModal() {
        document.getElementById('generateSerialsModal').classList.add('hidden');
        document.getElementById('generateSerialsForm').reset();
    }

    document.getElementById('generateSerialsForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const quantity = formData.get('quantity');
        const prefix = formData.get('prefix');

        try {
            const response = await fetch(`{{ route('batches.generate-serials', $batch) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ quantity, prefix })
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('An error occurred while generating serial numbers.');
        }

        closeGenerateSerialsModal();
    });
</script>
@endpush