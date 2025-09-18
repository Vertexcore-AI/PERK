@extends('layouts.app')

@section('title', 'Quotation #' . str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Quotation #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Created on {{ $quotation->created_at->format('M d, Y') }}
            </p>
        </div>
        <div class="flex gap-3">
            @if($quotation->status === 'Pending')
                <a href="{{ route('quotations.edit', $quotation->quote_id) }}" class="btn btn-primary">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Edit Quotation
                </a>
            @endif

            <a href="{{ route('quotations.print', $quotation->quote_id) }}" target="_blank" class="btn btn-secondary">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                Print
            </a>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="btn btn-secondary">
                    <i data-lucide="more-horizontal" class="w-4 h-4 mr-2"></i>
                    More
                </button>
                <div x-show="open" @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                    <div class="py-1">
                        <form method="POST" action="{{ route('quotations.duplicate', $quotation->quote_id) }}" class="inline">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i data-lucide="copy" class="w-4 h-4 inline mr-2"></i>
                                Duplicate
                            </button>
                        </form>

                        @if($quotation->status !== 'Converted')
                            <form method="POST" action="{{ route('quotations.destroy', $quotation->quote_id) }}"
                                  class="inline" onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quotation Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->customer->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Type:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->customer->type }}</span>
                            </div>
                            @if($quotation->customer->contact)
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Contact:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->customer->contact }}</span>
                                </div>
                            @endif
                            @if($quotation->customer->address)
                                <div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Address:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->customer->address }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quotation Details</h3>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Quote Date:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->quote_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Valid Until:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->valid_until->format('M d, Y') }}</span>
                                @if($quotation->isExpired())
                                    <span class="text-red-500 text-xs ml-1">(Expired)</span>
                                @elseif($quotation->valid_until->diffInDays(now()) <= 3)
                                    <span class="text-yellow-500 text-xs ml-1">(Expiring Soon)</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                @if($quotation->status === 'Pending')
                                    <span class="badge badge-warning ml-2">{{ $quotation->status }}</span>
                                @elseif($quotation->status === 'Converted')
                                    <span class="badge badge-success ml-2">{{ $quotation->status }}</span>
                                @else
                                    <span class="badge badge-secondary ml-2">{{ $quotation->status }}</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Items:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->quoteItems->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quotation Items</h3>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Batch</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Discount %</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">VAT %</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($quotation->quoteItems as $item)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->item->description }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->item->item_no }}</div>
                                        @if($item->item->manufacturer_name)
                                            <div class="text-xs text-gray-500">{{ $item->item->manufacturer_name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $item->batch->batch_number }}</div>
                                        <div class="text-xs text-gray-500">Available: {{ $item->batch->remaining_qty }}</div>
                                        @if($item->batch->expiry_date)
                                            <div class="text-xs text-gray-500">Exp: {{ $item->batch->expiry_date->format('M d, Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">LKR {{ number_format($item->unit_price, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $item->discount }}%</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $item->vat }}%</span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">LKR {{ number_format($item->total, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($item->isStockAvailable())
                                            <span class="badge badge-success">Available</span>
                                        @else
                                            <span class="badge badge-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Summary</h3>

                @php
                    $subtotal = $quotation->quoteItems->sum(function($item) {
                        return $item->quantity * $item->unit_price;
                    });
                    $totalDiscount = $quotation->quoteItems->sum(function($item) {
                        $itemTotal = $item->quantity * $item->unit_price;
                        return $itemTotal * ($item->discount / 100);
                    });
                    $totalVat = $quotation->quoteItems->sum(function($item) {
                        $itemTotal = $item->quantity * $item->unit_price;
                        $afterDiscount = $itemTotal - ($itemTotal * ($item->discount / 100));
                        return $afterDiscount * ($item->vat / 100);
                    });
                @endphp

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Items:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quotation->quoteItems->count() }}</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-medium text-gray-900 dark:text-white">LKR {{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total Discount:</span>
                        <span class="font-medium text-gray-900 dark:text-white">LKR {{ number_format($totalDiscount, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total VAT:</span>
                        <span class="font-medium text-gray-900 dark:text-white">LKR {{ number_format($totalVat, 2) }}</span>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <div class="flex justify-between text-lg font-bold">
                        <span class="text-gray-900 dark:text-white">Total Estimate:</span>
                        <span class="text-gray-900 dark:text-white">LKR {{ number_format($quotation->total_estimate, 2) }}</span>
                    </div>

                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <div>Valid until: {{ $quotation->valid_until->format('M d, Y') }}</div>
                        <div>{{ $quotation->valid_until->diffInDays(now(), false) }} days {{ $quotation->valid_until->isPast() ? 'ago' : 'remaining' }}</div>
                    </div>
                </div>

                @if($quotation->canConvert())
                    <div class="mt-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i data-lucide="info" class="w-5 h-5 text-blue-500 mr-2 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Ready to Convert</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-300">This quotation can be converted to a sale in the POS system.</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('pos.index') }}?load_quote={{ $quotation->quote_id }}" class="w-full btn btn-primary">
                            <i data-lucide="shopping-cart" class="w-4 h-4 mr-2"></i>
                            Convert to Sale
                        </a>
                    </div>
                @elseif($quotation->status === 'Expired')
                    <div class="mt-6">
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-2 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-red-800 dark:text-red-200">Quotation Expired</p>
                                    <p class="text-xs text-red-600 dark:text-red-300">This quotation has expired and cannot be converted to a sale.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($quotation->status === 'Converted')
                    <div class="mt-6">
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-green-800 dark:text-green-200">Converted to Sale</p>
                                    <p class="text-xs text-green-600 dark:text-green-300">This quotation has been successfully converted to a sale.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection