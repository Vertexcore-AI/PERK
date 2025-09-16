@extends('layouts.app')

@section('title', 'Item Details')

@section('page-title', 'Item Details')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('items.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Items</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">{{ $item->name }}</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('items.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Items
        </a>
        <a href="{{ route('items.edit', $item) }}" class="btn-primary">
            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
            Edit Item
        </a>
    </div>
@endsection

@section('content')
    <!-- Item Header Card -->
    <div class="card mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        @if($item->is_serialized)
                            <i data-lucide="scan-line" class="w-8 h-8 text-white"></i>
                        @else
                            <i data-lucide="package" class="w-8 h-8 text-white"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $item->name }}</h1>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-slate-600 dark:text-slate-400">{{ $item->item_no }}</span>
                            @if($item->barcode)
                                <span class="text-slate-600 dark:text-slate-400">• {{ $item->barcode }}</span>
                            @endif
                            @if($item->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $item->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @if($item->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-warning">Inactive</span>
                    @endif
                </div>
            </div>

            @if($item->description)
                <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg">
                    <p class="text-slate-700 dark:text-slate-300">{{ $item->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Basic Information -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <div class="lg:col-span-3">
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Basic Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Item Number</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">{{ $item->item_no }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Unit of Measure</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">{{ ucfirst($item->unit_of_measure) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Reorder Point</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">{{ $item->reorder_point ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Serial Tracking</label>
                            <p class="text-slate-900 dark:text-white font-medium mt-1">
                                @if($item->is_serialized)
                                    <span class="text-green-600 dark:text-green-400">Enabled</span>
                                @else
                                    <span class="text-slate-500 dark:text-slate-400">Disabled</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status Sidebar -->
        <div class="space-y-6">
            <!-- Stock Status -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Stock Status</h3>
                </div>
                <div class="p-6">
                    @php
                        $totalStock = $item->batches->sum('remaining_qty');
                        $isLowStock = $item->reorder_point && $totalStock <= $item->reorder_point;
                    @endphp

                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $totalStock }}</div>
                        <div class="text-slate-600 dark:text-slate-400">Units Available</div>
                        @if($isLowStock)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                                    <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                    Low Stock
                                </span>
                            </div>
                        @endif
                    </div>

                    @if($item->reorder_point)
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-400">Reorder Point</span>
                                <span class="font-medium text-slate-900 dark:text-white">{{ $item->reorder_point }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Information - Full Width -->
    <div class="card mb-6">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Batch Information</h3>
                <span class="badge badge-info">{{ $item->batches->count() }} batch{{ $item->batches->count() !== 1 ? 'es' : '' }}</span>
            </div>
        </div>
        <div class="p-6">
            @if($item->batches->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Vendor</th>
                                <th>Received Date</th>
                                <th>Unit Cost</th>
                                <th>Selling Price</th>
                                <th>Margin</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->batches->sortBy('received_date') as $batch)
                                <tr>
                                    <td>
                                        <span class="font-mono text-sm">{{ $batch->batch_no }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $batch->vendor->name ?? 'Unknown' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $batch->received_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="font-medium">${{ number_format($batch->unit_cost, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($batch->selling_price > 0)
                                            <span class="font-medium text-green-600 dark:text-green-400">${{ number_format($batch->selling_price, 2) }}</span>
                                        @else
                                            <span class="text-slate-400">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($batch->selling_price > 0 && $batch->unit_cost > 0)
                                            @php
                                                $margin = (($batch->selling_price - $batch->unit_cost) / $batch->unit_cost) * 100;
                                            @endphp
                                            <span class="text-sm {{ $margin > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ number_format($margin, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-medium">{{ $batch->remaining_qty }}</span>
                                        <span class="text-slate-500 text-sm">/ {{ $batch->received_qty }}</span>
                                    </td>
                                    <td>
                                        @if($batch->remaining_qty > 0)
                                            <span class="badge badge-success">Available</span>
                                        @elseif($batch->remaining_qty == 0)
                                            <span class="badge badge-secondary">Sold Out</span>
                                        @else
                                            <span class="badge badge-warning">Oversold</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Batch Summary -->
                <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4">
                    @php
                        $totalStock = $item->batches->sum('remaining_qty');
                        $avgCost = $item->batches->where('unit_cost', '>', 0)->avg('unit_cost');
                        $avgSellingPrice = $item->batches->where('selling_price', '>', 0)->avg('selling_price');
                    @endphp
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4">
                        <div class="text-sm text-slate-600 dark:text-slate-400">Total Stock</div>
                        <div class="text-xl font-bold text-slate-900 dark:text-white">{{ $totalStock }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4">
                        <div class="text-sm text-slate-600 dark:text-slate-400">Avg. Cost</div>
                        <div class="text-xl font-bold text-slate-900 dark:text-white">
                            @if($avgCost > 0)
                                ${{ number_format($avgCost, 2) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4">
                        <div class="text-sm text-slate-600 dark:text-slate-400">Avg. Selling Price</div>
                        <div class="text-xl font-bold text-slate-900 dark:text-white">
                            @if($avgSellingPrice > 0)
                                ${{ number_format($avgSellingPrice, 2) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="package-x" class="w-8 h-8 text-slate-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">No Batches Found</h4>
                    <p class="text-slate-500 dark:text-slate-400 mb-4">This item doesn't have any batches yet. Batches are created when items are received through GRN.</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Placeholder functions for future features
    function createSale() {
        alert('Sales module will be implemented in the next phase');
    }

    function adjustStock() {
        alert('Stock adjustment feature will be implemented in Phase 2');
    }

    function manageSerials() {
        alert('Serial number management feature will be implemented in Phase 2');
    }
</script>
@endpush