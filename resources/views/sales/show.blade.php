@extends('layouts.app')

@section('title', 'Sale Details - #' . $sale->sale_id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('sales.index') }}"
               class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sale #{{ $sale->sale_id }}</h1>
                <p class="text-slate-600 dark:text-slate-400">
                    {{ $sale->sale_date->format('F d, Y') }} at {{ $sale->created_at->format('H:i') }}
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                {{ $sale->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400' : '' }}
                {{ $sale->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                {{ ucfirst($sale->status) }}
            </span>
            <a href="{{ route('sales.receipt', $sale) }}"
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i data-lucide="receipt" class="w-4 h-4 mr-2"></i>
                View Receipt
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Sale Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Sale Items -->
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Sale Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">
                                    Item
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">
                                    Batch
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">
                                    Qty
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">
                                    Unit Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">
                                    Discount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">
                                    Profit
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($sale->saleItems as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $item->item->item_no }}</div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">{{ $item->item->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                        {{ $item->batch->batch_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                        LKR {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                        {{ $item->discount }}%
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">
                                        LKR {{ number_format($item->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                        LKR {{ number_format($item->item_profit, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Sale Totals -->
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-400">Subtotal:</span>
                                <span class="text-slate-900 dark:text-white">
                                    LKR {{ number_format($sale->saleItems->sum(function($item) { return $item->unit_price * $item->quantity; }), 2) }}
                                </span>
                            </div>
                            @php
                                $totalDiscount = $sale->saleItems->sum(function($item) {
                                    return ($item->unit_price * $item->quantity) * ($item->discount / 100);
                                });
                                $totalVat = $sale->saleItems->sum('vat_amount');
                            @endphp
                            @if($totalDiscount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-400">Discount:</span>
                                <span class="text-slate-900 dark:text-white">
                                    -LKR {{ number_format($totalDiscount, 2) }}
                                </span>
                            </div>
                            @endif
                            @if($totalVat > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-400">VAT:</span>
                                <span class="text-slate-900 dark:text-white">
                                    LKR {{ number_format($totalVat, 2) }}
                                </span>
                            </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold border-t border-slate-200 dark:border-slate-700 pt-2">
                                <span class="text-slate-900 dark:text-white">Total:</span>
                                <span class="text-slate-900 dark:text-white">
                                    LKR {{ number_format($sale->total_amount, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                <span>Total Profit:</span>
                                <span>LKR {{ number_format($sale->profit, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Customer</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Name</label>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $sale->customer->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Type</label>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $sale->customer->type }}</p>
                    </div>
                    @if($sale->customer->contact)
                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Contact</label>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $sale->customer->contact }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Payment</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Method</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                            {{ $sale->payment_method === 'cash' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                            {{ $sale->payment_method === 'card' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                            {{ $sale->payment_method === 'mixed' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : '' }}">
                            {{ ucfirst($sale->payment_method) }}
                        </span>
                    </div>

                    @if($sale->cash_amount > 0)
                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Cash Amount</label>
                        <p class="font-medium text-slate-900 dark:text-white">LKR {{ number_format($sale->cash_amount, 2) }}</p>
                    </div>
                    @endif

                    @if($sale->card_amount > 0)
                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Card Amount</label>
                        <p class="font-medium text-slate-900 dark:text-white">LKR {{ number_format($sale->card_amount, 2) }}</p>
                    </div>
                    @endif

                    @if($sale->payment_method === 'cash' && $sale->cash_amount > $sale->total_amount)
                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Change</label>
                        <p class="font-medium text-emerald-600 dark:text-emerald-400">
                            LKR {{ number_format($sale->cash_amount - $sale->total_amount, 2) }}
                        </p>
                    </div>
                    @endif

                    <div>
                        <label class="text-sm text-slate-600 dark:text-slate-400">Total Paid</label>
                        <p class="font-bold text-slate-900 dark:text-white">
                            LKR {{ number_format($sale->cash_amount + $sale->card_amount, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customer Stats -->
            @if(isset($customerStats))
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Customer Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Total Purchases:</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $customerStats['total_sales'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Total Spent:</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            LKR {{ number_format($customerStats['total_amount'], 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Average Purchase:</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            LKR {{ number_format($customerStats['average_sale'], 2) }}
                        </span>
                    </div>
                    @if($customerStats['last_purchase'])
                    <div class="flex justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Last Purchase:</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($customerStats['last_purchase'])->format('M d, Y') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($sale->notes)
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Notes</h3>
                <p class="text-slate-600 dark:text-slate-400">{{ $sale->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection