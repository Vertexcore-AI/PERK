@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sales History</h1>
            <p class="text-slate-600 dark:text-slate-400 mt-1">Track and manage all sales transactions</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('sales.create') }}"
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                New Sale
            </a>
            <a href="{{ route('pos.index') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                POS
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <i data-lucide="shopping-cart" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Total Sales</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total_sales'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Total Amount</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">LKR {{ number_format($stats['total_amount'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <i data-lucide="trending-up" class="w-6 h-6 text-amber-600 dark:text-amber-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Average Sale</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">LKR {{ number_format($stats['average_sale'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <i data-lucide="credit-card" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">Card Sales</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">LKR {{ number_format($stats['card_sales'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Sale ID or customer name..."
                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Customer</label>
                <select name="customer_id" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->customer_id }}" {{ request('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md">
                    Filter
                </button>
                <a href="{{ route('sales.index') }}" class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-md">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Sales Table -->
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Sale ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Items
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Payment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                #{{ $sale->sale_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $sale->customer->name }}</div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">{{ $sale->customer->type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                                {{ $sale->sale_date->format('M d, Y') }}
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $sale->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                                {{ $sale->saleItems->count() }} items
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $sale->saleItems->sum('quantity') }} total qty
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $sale->payment_method === 'cash' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                    {{ $sale->payment_method === 'card' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                    {{ $sale->payment_method === 'mixed' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : '' }}">
                                    {{ ucfirst($sale->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                LKR {{ number_format($sale->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $sale->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400' : '' }}
                                    {{ $sale->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('sales.show', $sale) }}"
                                       class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('sales.receipt', $sale) }}"
                                       class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300">
                                        <i data-lucide="receipt" class="w-4 h-4"></i>
                                    </a>
                                    <!-- Invoice Dropdown -->
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </button>
                                        <div x-show="open" x-transition
                                             class="absolute right-0 z-10 mt-2 w-48 bg-white dark:bg-slate-800 rounded-md shadow-lg border border-slate-200 dark:border-slate-700">
                                            <a href="{{ route('pos.invoice.normal', $sale) }}" target="_blank"
                                               class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                                                <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i>
                                                Normal Invoice
                                            </a>
                                            <a href="{{ route('pos.invoice.vat', $sale) }}" target="_blank"
                                               class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                                                <i data-lucide="file-plus" class="w-4 h-4 inline mr-2"></i>
                                                VAT Invoice
                                            </a>
                                        </div>
                                    </div>
                                    @if($sale->status === 'completed' && $sale->created_at->diffInHours(now()) <= 24)
                                        <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to cancel this sale?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i data-lucide="shopping-cart" class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4"></i>
                                <p class="text-slate-500 dark:text-slate-400">No sales found.</p>
                                <a href="{{ route('pos.index') }}" class="text-primary-600 hover:text-primary-500 text-sm">
                                    Start making sales in POS →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection