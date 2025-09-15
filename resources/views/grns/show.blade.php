@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
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
                            ${{ number_format($grn->total_amount, 2) }}
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
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Received</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Stored</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Unit Price</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Discount</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">VAT</th>
                                    <th class="text-right py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Total</th>
                                    <th class="text-center py-3 px-4 font-medium text-slate-700 dark:text-slate-300">Batch</th>
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
                                        <td class="py-4 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400">
                                                {{ $grnItem->received_qty }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($grnItem->stored_qty == $grnItem->received_qty)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                                    {{ $grnItem->stored_qty }}
                                                </span>
                                            @elseif($grnItem->stored_qty > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                                                    {{ $grnItem->stored_qty }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                                                    0
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right font-medium text-slate-900 dark:text-white">
                                            ${{ number_format($grnItem->unit_price, 2) }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($grnItem->discount > 0)
                                                <span class="text-green-600 dark:text-green-400 font-medium">
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
                                            ${{ number_format($grnItem->total_cost, 2) }}
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $grn->grnItems->count() }} item(s) •
                                {{ $grn->grnItems->sum('received_qty') }} total units received •
                                {{ $grn->grnItems->sum('stored_qty') }} units stored
                            </div>
                            <div class="text-xl font-bold text-slate-900 dark:text-white">
                                Total: ${{ number_format($grn->total_amount, 2) }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i data-lucide="package-x" class="w-12 h-12 text-slate-400 mx-auto mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400">No items found in this GRN.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Status and Actions -->
        @if($grn->grnItems->where('stored_qty', '<', 'received_qty')->count() > 0)
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex items-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3"></i>
                    <div>
                        <h3 class="font-medium text-yellow-800 dark:text-yellow-400">Pending Storage</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-500 mt-1">
                            Some items have not been fully stored in inventory. Please complete the storage process.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush