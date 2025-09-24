@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quotations</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage customer quotations and estimates</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Create Quotation
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('quotations.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Search by customer name or quote ID..."
                           class="form-input">
                </div>

                <div class="w-40">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Expired" {{ request('status') === 'Expired' ? 'selected' : '' }}>Expired</option>
                        <option value="Converted" {{ request('status') === 'Converted' ? 'selected' : '' }}>Converted</option>
                    </select>
                </div>

                <div class="w-48">
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-input">
                </div>

                <div class="w-48">
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="form-input">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Quote ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Quote Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Valid Until
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Total Estimate
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Items
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($quotations as $quotation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $quotation->manual_customer_name ?: ($quotation->customer ? $quotation->customer->name : 'Manual Customer') }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $quotation->customer ? $quotation->customer->type : 'Manual' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $quotation->quote_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $quotation->valid_until->format('M d, Y') }}
                                @if($quotation->isExpired())
                                    <span class="text-red-500 text-xs">(Expired)</span>
                                @elseif($quotation->valid_until->diffInDays(now()) <= 3)
                                    <span class="text-yellow-500 text-xs">(Expiring Soon)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                LKR {{ number_format($quotation->total_estimate, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $quotation->quoteItems->count() }} items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($quotation->status === 'Pending')
                                    <span class="badge badge-warning">{{ $quotation->status }}</span>
                                @elseif($quotation->status === 'Converted')
                                    <span class="badge badge-success">{{ $quotation->status }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $quotation->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('quotations.show', $quotation->quote_id) }}"
                                       class="btn btn-sm btn-secondary">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    @if($quotation->status === 'Pending')
                                        <a href="{{ route('quotations.edit', $quotation->quote_id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('quotations.pdf', $quotation->quote_id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                    </a>

                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="btn btn-sm btn-secondary">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No quotations found</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first quotation.</p>
                                    <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                        Create Quotation
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($quotations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('quotationsIndex', () => ({
        // Add any JavaScript functionality here
    }));
});
</script>
@endsection