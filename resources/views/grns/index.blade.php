@extends('layouts.app')

@section('title', 'Goods Received Notes')

@section('page-title', 'GRN Management')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">GRNs</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('grns.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            New GRN Entry
        </a>
        <button type="button" class="btn-secondary">
            <i data-lucide="download" class="w-5 h-5 mr-2"></i>
            Export
        </button>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="p-1 hover:bg-emerald-200 dark:hover:bg-emerald-800 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }"
            x-show="show"
            class="alert alert-danger mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                </div>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="p-1 hover:bg-rose-200 dark:hover:bg-rose-800 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    <!-- GRN Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <span class="badge badge-info">Total</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalGRNs ?? 0 }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Total GRNs</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-amber-600 dark:text-amber-400"></i>
                </div>
                <span class="badge badge-warning">Pending</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $pendingGRNs ?? 0 }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Pending Storage</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="badge badge-success">Today</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $todayGRNs ?? 0 }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Today's GRNs</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <span class="badge badge-primary">Value</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($totalValue ?? 0, 0) }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Total Value</p>
        </div>
    </div>

    <!-- GRNs Table -->
    <div class="card">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Recent Goods Received Notes</h3>
                
                <!-- Search -->
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                    <input type="text"
                        id="grn-search"
                        placeholder="Search GRNs..."
                        class="pl-10 pr-4 py-2 w-64 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-modern w-full">
                <thead>
                    <tr>
                        <th scope="col">GRN No</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Invoice No</th>
                        <th scope="col">Billing Date</th>
                        <th scope="col">Items</th>
                        <th scope="col">Total Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($grns as $grn)
                        <tr class="group">
                            <td>
                                <span class="font-medium text-slate-900 dark:text-white">
                                    GRN-{{ str_pad($grn->grn_id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <i data-lucide="truck" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <span class="text-slate-900 dark:text-white">{{ $grn->vendor->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-slate-600 dark:text-slate-400">{{ $grn->inv_no }}</span>
                            </td>
                            <td>
                                <span class="text-slate-600 dark:text-slate-400">
                                    {{ $grn->billing_date ? $grn->billing_date->format('d/m/Y') : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $grn->grnItems->count() }} items
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-slate-900 dark:text-white">
                                    ${{ number_format($grn->total_amount, 2) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $hasUnstored = $grn->grnItems->where('stored_qty', '<', function($item) {
                                        return $item->received_qty;
                                    })->count() > 0;
                                @endphp
                                @if($hasUnstored)
                                    <span class="badge badge-warning">Partial</span>
                                @else
                                    <span class="badge badge-success">Complete</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('grns.show', $grn->grn_id) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="View GRN">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <a href="{{ route('grns.edit', $grn->grn_id) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="Edit GRN">
                                        <i data-lucide="edit" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <form action="{{ route('grns.destroy', $grn->grn_id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn-icon group/btn"
                                            data-tooltip="Delete GRN"
                                            onclick="return confirm('Are you sure you want to delete this GRN?')">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-rose-600 dark:group-hover/btn:text-rose-400"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                        <i data-lucide="file-text" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No GRNs found</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Start by creating your first goods received note</p>
                                    <a href="{{ route('grns.create') }}" class="btn-primary">
                                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                                        New GRN Entry
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($grns->hasPages())
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                {{ $grns->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('grn-search');
        const tbody = document.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush