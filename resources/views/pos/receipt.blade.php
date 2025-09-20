@extends('layouts.app')

@section('title', 'Receipt - Sale #' . $sale->sale_id)

@section('content')
<div class="max-w-md mx-auto bg-white dark:bg-slate-800 shadow-lg rounded-lg overflow-hidden">
    <!-- Receipt Header -->
    <div class="bg-primary-600 text-white px-6 py-4 text-center">
        <h1 class="text-xl font-bold">PERK Auto Parts</h1>
        <p class="text-sm opacity-90">Sales Receipt</p>
    </div>

    <!-- Receipt Content -->
    <div class="p-6 space-y-4">
        <!-- Sale Info -->
        <div class="flex justify-between text-sm">
            <span>Receipt #:</span>
            <span class="font-bold">#{{ $sale->sale_id }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span>Date:</span>
            <span>{{ $sale->sale_date->format('M d, Y H:i') }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span>Customer:</span>
            <span>{{ $sale->customer->name }}</span>
        </div>

        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
            <!-- Items -->
            <div class="space-y-2">
                @foreach($sale->saleItems as $item)
                <div class="text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $item->item->item_no }}</span>
                        <span>LKR {{ number_format($item->total, 2) }}</span>
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400 ml-2">
                        {{ $item->item->description }}
                    </div>
                    <div class="text-xs text-slate-600 dark:text-slate-400 ml-2">
                        {{ $item->quantity }} × LKR {{ number_format($item->unit_price, 2) }}
                        @if($item->discount > 0)
                            ({{ $item->discount }}% off)
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Totals -->
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-2">
            @php
                $subtotal = $sale->saleItems->sum(function($item) { return $item->unit_price * $item->quantity; });
                $totalDiscount = $sale->saleItems->sum(function($item) {
                    return ($item->unit_price * $item->quantity) * ($item->discount / 100);
                });
                $totalVat = $sale->saleItems->sum('vat_amount');
            @endphp

            <div class="flex justify-between text-sm">
                <span>Subtotal:</span>
                <span>LKR {{ number_format($subtotal, 2) }}</span>
            </div>

            @if($totalDiscount > 0)
            <div class="flex justify-between text-sm">
                <span>Discount:</span>
                <span>-LKR {{ number_format($totalDiscount, 2) }}</span>
            </div>
            @endif

            @if($totalVat > 0)
            <div class="flex justify-between text-sm">
                <span>VAT:</span>
                <span>LKR {{ number_format($totalVat, 2) }}</span>
            </div>
            @endif

            <div class="flex justify-between font-bold text-lg border-t border-slate-200 dark:border-slate-700 pt-2">
                <span>Total:</span>
                <span>LKR {{ number_format($sale->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-2">
            <div class="flex justify-between text-sm">
                <span>Payment Method:</span>
                <span class="capitalize">{{ $sale->payment_method }}</span>
            </div>

            @if($sale->cash_amount > 0)
            <div class="flex justify-between text-sm">
                <span>Cash Paid:</span>
                <span>LKR {{ number_format($sale->cash_amount, 2) }}</span>
            </div>
            @endif

            @if($sale->card_amount > 0)
            <div class="flex justify-between text-sm">
                <span>Card Paid:</span>
                <span>LKR {{ number_format($sale->card_amount, 2) }}</span>
            </div>
            @endif

            @if($sale->payment_method === 'cash' && $sale->cash_amount > $sale->total_amount)
            <div class="flex justify-between text-sm font-medium text-emerald-600">
                <span>Change:</span>
                <span>LKR {{ number_format($sale->cash_amount - $sale->total_amount, 2) }}</span>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 text-center text-xs text-slate-500">
            <p>Thank you for your business!</p>
            <p class="mt-2">Generated on {{ now()->format('M d, Y H:i') }}</p>
        </div>

        <!-- Actions -->
        <div class="flex space-x-2 pt-4">
            <button onclick="window.print()"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                Print
            </button>
            <a href="{{ route('pos.index') }}"
               class="flex-1 bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to POS
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .max-w-md, .max-w-md * {
        visibility: visible;
    }
    .max-w-md {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
    }
    button, a {
        display: none;
    }
}
</style>
@endsection