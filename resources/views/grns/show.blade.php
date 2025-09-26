@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-7make x2 mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    GRN Details
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                    Good Received Note #{{ $grn->grn_id }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('grns.index') }}"
                   class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to GRNs
                </a>
                <a href="{{ route('grns.edit', $grn) }}"
                   class="btn-primary">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    Edit GRN
                </a>
            </div>
        </div>

        <!-- GRN Information Card -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                    GRN Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Invoice Number</label>
                        <p class="text-slate-900 dark:text-white font-medium mt-1">{{ $grn->inv_no }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Vendor</label>
                        <p class="text-slate-900 dark:text-white font-medium mt-1">{{ $grn->vendor->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $grn->vendor->contact }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Billing Date</label>
                        <p class="text-slate-900 dark:text-white font-medium mt-1">
                            {{ \Carbon\Carbon::parse($grn->billing_date)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Total Amount</label>
                        <p class="text-slate-900 dark:text-white font-bold text-lg mt-1">
                            LKR {{ number_format($grn->total_amount, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Card -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                    Items Received ({{ $grn->grnItems->count() }})
                </h2>

                @if($grn->grnItems->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Item</th>
                                    <th class="text-left py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Vendor Code</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Quantity</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Unit Cost</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Selling Price</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Profit Margin</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Vendor Discount</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">VAT</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Net Cost</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Batch</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Bin Location</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach($grn->grnItems as $grnItem)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                        <td class="py-4 px-4">
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-white">
                                                    {{ $grnItem->item->name }}
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $grnItem->item->item_no }}
                                                </p>
                                                @if($grnItem->item->category)
                                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded">
                                                        {{ $grnItem->item->category->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="font-mono text-sm text-slate-700 dark:text-slate-300">
                                                {{ $grnItem->vendor_item_code }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center font-medium text-slate-900 dark:text-white">
                                            {{ number_format($grnItem->quantity ?? 1) }}
                                        </td>
                                        <td class="py-4 px-4 text-right font-medium text-slate-900 dark:text-white">
                                            LKR {{ number_format($grnItem->unit_cost, 2) }}
                                        </td>
                                        <td class="py-4 px-4 text-right font-medium text-green-600 dark:text-green-400">
                                            @if($grnItem->selling_price > 0)
                                                LKR {{ number_format($grnItem->selling_price, 2) }}
                                            @else
                                                <span class="text-slate-400">Not set</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($grnItem->profit_margin > 0)
                                                <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                                    LKR {{ number_format($grnItem->profit_margin, 2) }}
                                                </span>
                                                @php
                                                    $profitPercent = $grnItem->unit_cost > 0 ? (($grnItem->profit_margin / $grnItem->unit_cost) * 100) : 0;
                                                @endphp
                                                <div class="text-xs text-slate-500">
                                                    ({{ number_format($profitPercent, 1) }}%)
                                                </div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($grnItem->discount > 0)
                                                <span class="text-orange-600 dark:text-orange-400 font-medium">
                                                    {{ $grnItem->discount }}%
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($grnItem->vat > 0)
                                                <span class="text-slate-600 dark:text-slate-400">
                                                    {{ $grnItem->vat }}%
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right font-bold text-slate-900 dark:text-white">
                                            LKR {{ number_format($grnItem->total_cost, 2) }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($grnItem->batch)
                                                <span class="font-mono text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 px-2 py-1 rounded">
                                                    {{ $grnItem->batch->batch_no }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @php
                                                $binLocation = null;
                                                if ($grnItem->batch && $grnItem->batch->item) {
                                                    // Find inventory stock for this item to get bin location
                                                    $inventoryStock = \App\Models\InventoryStock::where('item_id', $grnItem->item_id)
                                                        ->where('batch_id', $grnItem->batch_id)
                                                        ->with('bin')
                                                        ->first();
                                                    $binLocation = $inventoryStock?->bin;
                                                }
                                            @endphp
                                            @if($binLocation)
                                                <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs font-medium">
                                                    {{ $binLocation->code }}
                                                </span>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                    {{ $binLocation->name }}
                                                </div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            
                        </div>

                        @php
                            $totalSellingValue = $grn->grnItems->sum(function($item) {
                                return $item->selling_price * $item->quantity;
                            });
                            $totalProfitMargin = $grn->grnItems->sum(function($item) {
                                return $item->profit_margin * $item->quantity;
                            });
                            $avgDiscountPercent = $grn->grnItems->avg('discount');
                        @endphp

                        @if($totalSellingValue > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                    <div class="text-sm text-green-700 dark:text-green-300">Potential Selling Value</div>
                                    <div class="text-xl font-bold text-green-900 dark:text-green-100">LKR {{ number_format($totalSellingValue, 2) }}</div>
                                </div>
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="text-sm text-blue-700 dark:text-blue-300">Total Profit Margin</div>
                                    <div class="text-xl font-bold text-blue-900 dark:text-blue-100">LKR {{ number_format($totalProfitMargin, 2) }}</div>
                                </div>
                                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                                    <div class="text-sm text-orange-700 dark:text-orange-300">Avg Vendor Discount</div>
                                    <div class="text-xl font-bold text-orange-900 dark:text-orange-100">{{ number_format($avgDiscountPercent ?? 0, 1) }}%</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="package-x" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400">No items found in this GRN.</p>
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