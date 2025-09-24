@extends('layouts.app')

@section('title', 'Quotation #' . str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid px-2">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                Quotation #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Created on {{ $quotation->created_at->format('M d, Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            @if($quotation->status === 'Pending')
                <a href="{{ route('quotations.edit', $quotation->quote_id) }}" class="btn btn-primary">
                    <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                    Edit
                </a>
            @endif

            <a href="{{ route('quotations.pdf', $quotation->quote_id) }}" class="btn btn-primary">
                <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                PDF
            </a>

            <!-- <a href="{{ route('quotations.print', $quotation->quote_id) }}" target="_blank" class="btn btn-secondary">
                <i data-lucide="printer" class="w-4 h-4 mr-1"></i>
                Print
            </a> -->

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="btn btn-secondary">
                    <i data-lucide="more-horizontal" class="w-4 h-4 mr-1"></i>
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

            <!-- <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                Back
            </a> -->
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-3">
            <!-- Customer & Vehicle Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Customer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">
                            {{ $quotation->manual_customer_name ?: ($quotation->customer ? $quotation->customer->name : 'Manual Customer') }}
                            @if($quotation->manual_customer_name && $quotation->customer && $quotation->manual_customer_name !== $quotation->customer->name)
                                <span class="text-xs text-blue-600 ml-1">(Custom)</span>
                            @endif
                        </span>
                    </div>
                    <!-- <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Type:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->customer ? $quotation->customer->type : 'Manual' }}</span>
                    </div> -->
                    @if($quotation->customer && $quotation->customer->contact)
                        <div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Contact:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->customer->contact }}</span>
                        </div>
                    @endif
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
                    <div class="md:col-span-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Address:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">
                            {{ $quotation->manual_customer_address ?: ($quotation->customer ? $quotation->customer->address : 'No address provided') }}
                            @if($quotation->manual_customer_address && $quotation->customer && $quotation->manual_customer_address !== $quotation->customer->address)
                                <span class="text-xs text-blue-600 ml-1">(Custom)</span>
                            @endif
                        </span>
                    </div>
                    @if($quotation->car_model || $quotation->car_registration_number)
                        @if($quotation->car_model)
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Car Model:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->car_model }}</span>
                            </div>
                        @endif
                        @if($quotation->car_registration_number)
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Registration:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white ml-2">{{ $quotation->car_registration_number }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Quotation Items</h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Batch</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($quotation->quoteItems as $item)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->item->description }}</div>
                                        <div class="text-xs text-gray-500">Code: {{ $item->item->item_no }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        @php
                                            $totalAvailable = \App\Models\Batch::where('item_id', $item->item_id)
                                                ->where('remaining_qty', '>', 0)
                                                ->sum('remaining_qty');
                                            $availableBatches = \App\Models\Batch::where('item_id', $item->item_id)
                                                ->where('remaining_qty', '>', 0)
                                                ->orderBy('received_date', 'asc')
                                                ->get();
                                        @endphp

                                        @if($item->batch)
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->batch->batch_no }}</div>
                                            <div class="text-xs text-gray-500">Available: {{ $item->batch->remaining_qty }}</div>
                                            @if($item->batch->expiry_date)
                                                <div class="text-xs text-gray-500">Exp: {{ \Carbon\Carbon::parse($item->batch->expiry_date)->format('M Y') }}</div>
                                            @endif
                                        @elseif($totalAvailable > 0)
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Multiple Batches</div>
                                            <div class="text-xs text-gray-500">Total Available: {{ $totalAvailable }}</div>
                                            <div class="text-xs text-gray-400">{{ $availableBatches->count() }} batch(es)</div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-orange-800 bg-orange-100 rounded-md dark:bg-orange-900/50 dark:text-orange-300">
                                                No Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->unit_price, 2) }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($item->total, 2) }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        @if($totalAvailable >= $item->quantity)
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
        <div class="space-y-3">
            <!-- Quote Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Quote Details</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Quote Date:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quotation->quote_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Valid Until:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quotation->valid_until->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total Items:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quotation->quoteItems->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Financial Summary</h3>

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

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($totalDiscount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">VAT:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($totalVat, 2) }}</span>
                    </div>
                    <hr class="border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-900 dark:text-white">Total:</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($quotation->total_estimate, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($quotation->status === 'Pending')
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h3 class="text-base font-medium text-blue-900 dark:text-blue-200 mb-2">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('pos.index') }}" class="w-full btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart w-4 h-4 mr-1"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="m1 1 6 6m0 0L5 6l8.5 8.5a2 2 0 0 1 0 2.83L11.17 19.5a2 2 0 0 1-2.83 0L1 14"></path><path d="M20 5 10 17l-5-5"></path></svg>
                            Process in POS
                        </a>
                    </div>
                </div>
            @elseif($quotation->status === 'Expired')
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mr-2 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">Quotation Expired</p>
                            <p class="text-xs text-red-600 dark:text-red-300">Cannot be converted to sale.</p>
                        </div>
                    </div>
                </div>
            @elseif($quotation->status === 'Converted')
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mr-2 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">Converted to Sale</p>
                            <p class="text-xs text-green-600 dark:text-green-300">Successfully converted.</p>
                        </div>
                    </div>
                </div>
            @endif
      </div>
    </div>
</div>

<!-- Custom Notification Modal -->
<div id="custom-notification" class="fixed inset-0 bg-slate-900 bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div id="notification-icon" class="w-10 h-10 rounded-xl flex items-center justify-center">
                    <i id="notification-icon-symbol" data-lucide="" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 id="notification-title" class="text-lg font-semibold text-slate-900 dark:text-white"></h3>
                </div>
            </div>
            <p id="notification-message" class="text-slate-600 dark:text-slate-400 mb-6"></p>
            <div class="flex justify-end gap-3">
                <button id="notification-cancel" class="btn btn-secondary hidden">Cancel</button>
                <button id="notification-ok" class="btn btn-primary">OK</button>
            </div>
        </div>
    </div>
</div>

@if(session('download_pdf'))
    <script>
        // Automatically download PDF after quotation creation
        window.addEventListener('load', function() {
            window.location.href = "{{ route('quotations.pdf', $quotation->quote_id) }}";
        });
    </script>
@endif

<script>
function showNotification(message, type = 'info', title = '', showCancel = false) {
    return new Promise((resolve) => {
        const notificationModal = document.getElementById('custom-notification');
        const notificationIcon = document.getElementById('notification-icon');
        const notificationIconSymbol = document.getElementById('notification-icon-symbol');
        const notificationTitle = document.getElementById('notification-title');
        const notificationMessage = document.getElementById('notification-message');
        const notificationOk = document.getElementById('notification-ok');
        const notificationCancel = document.getElementById('notification-cancel');

        // Set up based on type
        let iconClass = '';
        let iconBg = '';
        let defaultTitle = '';

        switch (type) {
            case 'success':
                iconClass = 'check-circle';
                iconBg = 'bg-gradient-to-br from-emerald-500 to-emerald-600';
                defaultTitle = 'Success';
                break;
            case 'error':
                iconClass = 'x-circle';
                iconBg = 'bg-gradient-to-br from-red-500 to-red-600';
                defaultTitle = 'Error';
                break;
            case 'warning':
                iconClass = 'alert-triangle';
                iconBg = 'bg-gradient-to-br from-amber-500 to-amber-600';
                defaultTitle = 'Warning';
                break;
            case 'confirm':
                iconClass = 'help-circle';
                iconBg = 'bg-gradient-to-br from-blue-500 to-blue-600';
                defaultTitle = 'Confirm';
                showCancel = true;
                break;
            default:
                iconClass = 'info';
                iconBg = 'bg-gradient-to-br from-slate-500 to-slate-600';
                defaultTitle = 'Information';
        }

        // Set content
        notificationIcon.className = 'w-10 h-10 rounded-xl flex items-center justify-center ' + iconBg;
        notificationIconSymbol.setAttribute('data-lucide', iconClass);
        notificationIconSymbol.className = 'w-5 h-5 text-white';
        notificationTitle.textContent = title || defaultTitle;
        notificationMessage.textContent = message;

        // Handle buttons
        if (showCancel) {
            notificationCancel.classList.remove('hidden');
            notificationOk.textContent = 'Confirm';
        } else {
            notificationCancel.classList.add('hidden');
            notificationOk.textContent = 'OK';
        }

        // Show modal
        notificationModal.style.display = 'flex';
        notificationModal.classList.remove('hidden');

        // Re-initialize Lucide icons for the modal
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Handle button clicks
        const handleOk = () => {
            notificationModal.style.display = 'none';
            notificationModal.classList.add('hidden');
            resolve(true);
        };

        const handleCancel = () => {
            notificationModal.style.display = 'none';
            notificationModal.classList.add('hidden');
            resolve(false);
        };

        notificationOk.onclick = handleOk;
        notificationCancel.onclick = handleCancel;
    });
}

</script>
@endsection