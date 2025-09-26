@extends('layouts.app')

@section('title', 'Vendor Mapping Details')

@section('page-title', 'Vendor Item Mapping Details')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('inventory.index') }}">Inventory</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('inventory.mappings.index') }}">Vendor Item Mappings</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Mapping Details</span>
    </div>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('inventory.mappings.edit', $mapping) }}" class="btn-primary">
            <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
            Edit Mapping
        </a>
        <a href="{{ route('inventory.mappings.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Mappings
        </a>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Mapping Details Card -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                            <i data-lucide="link" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Mapping Details</h5>
                            <p class="text-slate-600 dark:text-slate-400">Vendor item code to internal item mapping</p>
                        </div>
                        @if($mapping->is_preferred)
                            <div class="ml-auto">
                                <span class="badge badge-success">
                                    <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                                    Preferred Vendor
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Vendor</label>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    <i data-lucide="truck" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $mapping->vendor->name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $mapping->vendor->contact_person ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Internal Item</label>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <i data-lucide="package" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $mapping->item->name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $mapping->item->item_no }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Vendor Item Code</label>
                            <p class="font-mono font-medium text-slate-900 dark:text-white">{{ $mapping->vendor_item_code }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                {{ $mapping->item->category->name ?? 'Uncategorized' }}
                            </span>
                        </div>

                        @if($mapping->vendor_item_name)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Vendor Item Name</label>
                            <p class="text-slate-900 dark:text-white">{{ $mapping->vendor_item_name }}</p>
                        </div>
                        @endif

                        @if($mapping->vendor_cost)
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Reference Cost</label>
                            <p class="text-lg font-semibold text-slate-900 dark:text-white">LKR {{ number_format($mapping->vendor_cost, 2) }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Catalog/Reference price</p>
                        </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Created</p>
                                <p class="font-medium text-slate-900 dark:text-white">{{ $mapping->created_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Last Updated</p>
                                <p class="font-medium text-slate-900 dark:text-white">{{ $mapping->updated_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Vendor Mappings for this Item -->
            @if($otherMappings->count() > 0)
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Other Vendors for this Item</h5>
                    <p class="text-slate-600 dark:text-slate-400">Compare prices from different vendors</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th scope="col">Vendor</th>
                                <th scope="col">Vendor Code</th>
                                <th scope="col">Reference Cost</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($otherMappings as $otherMapping)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <i data-lucide="truck" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="text-slate-900 dark:text-white">{{ $otherMapping->vendor->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono text-slate-900 dark:text-white">{{ $otherMapping->vendor_item_code }}</span>
                                </td>
                                <td>
                                    @if($otherMapping->vendor_cost)
                                        <span class="font-medium text-slate-900 dark:text-white">LKR {{ number_format($otherMapping->vendor_cost, 2) }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($otherMapping->is_preferred)
                                        <span class="badge badge-success">
                                            <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                                            Preferred
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">Alternative</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Quick Actions</h5>
                </div>
                <div class="p-6 space-y-3">
                    @if(!$mapping->is_preferred)
                    <button type="button"
                        onclick="setPreferred({{ $mapping->id }})"
                        class="w-full btn-primary btn-sm">
                        <i data-lucide="star" class="w-4 h-4 mr-2"></i>
                        Set as Preferred
                    </button>
                    @endif

                    <a href="{{ route('inventory.mappings.edit', $mapping) }}" class="w-full btn-secondary btn-sm">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                        Edit Mapping
                    </a>

                    <form action="{{ route('inventory.mappings.destroy', $mapping) }}" method="POST" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this mapping?')">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Delete Mapping
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mapping Statistics -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Item Statistics</h5>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Total Vendors</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $otherMappings->count() + 1 }}</span>
                    </div>

                    @if($mapping->vendor_cost && $otherMappings->whereNotNull('vendor_cost')->count() > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Price Range</span>
                        <div class="text-right">
                            @php
                                $costs = $otherMappings->whereNotNull('vendor_cost')->pluck('vendor_cost');
                                if ($mapping->vendor_cost) $costs->push($mapping->vendor_cost);
                                $minCost = $costs->min();
                                $maxCost = $costs->max();
                            @endphp
                            <p class="font-medium text-slate-900 dark:text-white">LKR {{ number_format($minCost, 2) }} - LKR {{ number_format($maxCost, 2) }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Savings: LKR {{ number_format($maxCost - $minCost, 2) }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Current Stock</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $mapping->item->current_stock ?? 0 }} {{ $mapping->item->unit_of_measure ?? 'PCS' }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Tips</h5>
                </div>
                <div class="p-6 space-y-3">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Reference costs help with vendor comparison
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Actual purchase costs are tracked in batches
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Preferred vendors are selected first in GRNs
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Multiple vendors provide price flexibility
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function setPreferred(mappingId) {
        if (confirm('Set this as the preferred vendor for this item?')) {
            fetch(`/inventory/mappings/${mappingId}/set-preferred`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush