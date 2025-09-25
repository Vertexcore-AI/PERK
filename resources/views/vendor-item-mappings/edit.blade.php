@extends('layouts.app')

@section('title', 'Edit Vendor Mapping')

@section('page-title', 'Edit Vendor Item Mapping')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('inventory.index') }}">Inventory</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('inventory.mappings.index') }}">Vendor Item Mappings</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('inventory.mappings.show', $mapping) }}">Mapping Details</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Edit</span>
    </div>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('inventory.mappings.show', $mapping) }}" class="btn-secondary">
            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
            View Details
        </a>
        <a href="{{ route('inventory.mappings.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Mappings
        </a>
    </div>
@endsection

@section('content')
    @if($errors->any())
        <div class="alert-danger mb-6">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <p class="font-medium">Please fix the following errors:</p>
                    <ul class="mt-2 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                            <i data-lucide="edit" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Edit Mapping Information</h5>
                            <p class="text-slate-600 dark:text-slate-400">Update vendor item code to internal item mapping</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('inventory.mappings.update', $mapping) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="vendor_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Vendor <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="truck" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <select name="vendor_id" id="vendor_id"
                                        class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('vendor_id') border-rose-500 @enderror"
                                        required>
                                        <option value="">Select vendor</option>
                                        @foreach($vendors ?? [] as $vendor)
                                            <option value="{{ $vendor->id }}" {{ (old('vendor_id', $mapping->vendor_id) == $vendor->id) ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('vendor_id')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vendor_item_code" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Vendor Item Code <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="hash" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <input type="text"
                                        name="vendor_item_code"
                                        id="vendor_item_code"
                                        value="{{ old('vendor_item_code', $mapping->vendor_item_code) }}"
                                        class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('vendor_item_code') border-rose-500 @enderror"
                                        placeholder="e.g., BP-001, BRAKE-PAD-FRONT"
                                        required>
                                </div>
                                @error('vendor_item_code')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vendor_item_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Vendor Item Name
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="tag" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <input type="text"
                                        name="vendor_item_name"
                                        id="vendor_item_name"
                                        value="{{ old('vendor_item_name', $mapping->vendor_item_name) }}"
                                        class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('vendor_item_name') border-rose-500 @enderror"
                                        placeholder="Vendor's product name">
                                </div>
                                @error('vendor_item_name')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vendor_cost" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Reference Cost
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-400">$</span>
                                    </div>
                                    <input type="number"
                                        name="vendor_cost"
                                        id="vendor_cost"
                                        value="{{ old('vendor_cost', $mapping->vendor_cost) }}"
                                        step="0.01"
                                        min="0"
                                        class="pl-8 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('vendor_cost') border-rose-500 @enderror"
                                        placeholder="0.00">
                                </div>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Catalog/reference price for comparison</p>
                                @error('vendor_cost')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="item_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Internal Item <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="package" class="w-5 h-5 text-slate-400"></i>
                                </div>
                                <select name="item_id" id="item_id"
                                    class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('item_id') border-rose-500 @enderror"
                                    required>
                                    <option value="">Select internal item</option>
                                    @foreach($items ?? [] as $item)
                                        <option value="{{ $item->id }}" {{ (old('item_id', $mapping->item_id) == $item->id) ? 'selected' : '' }}
                                            data-category="{{ $item->category->name ?? 'Uncategorized' }}">
                                            {{ $item->name }} ({{ $item->item_no }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('item_id')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox"
                                    name="is_preferred"
                                    value="1"
                                    {{ old('is_preferred', $mapping->is_preferred) ? 'checked' : '' }}
                                    class="w-4 h-4 text-primary-600 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                                <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Set as preferred vendor</span>
                            </label>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button type="submit" class="btn-primary">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Update Mapping
                            </button>
                            <button type="reset" class="btn-secondary">
                                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i>
                                Reset Changes
                            </button>
                            <a href="{{ route('inventory.mappings.show', $mapping) }}" class="btn-secondary">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Current Mapping Info -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Current Mapping</h5>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Vendor</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $mapping->vendor->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Vendor Code</p>
                        <p class="font-mono font-medium text-slate-900 dark:text-white">{{ $mapping->vendor_item_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Internal Item</p>
                        <p class="font-medium text-slate-900 dark:text-white">{{ $mapping->item->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $mapping->item->item_no }}</p>
                    </div>
                    @if($mapping->vendor_cost)
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Reference Cost</p>
                        <p class="font-medium text-slate-900 dark:text-white">${{ number_format($mapping->vendor_cost, 2) }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Status</p>
                        @if($mapping->is_preferred)
                            <span class="badge badge-success">
                                <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                                Preferred
                            </span>
                        @else
                            <span class="badge badge-secondary">Alternative</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Update Tips -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Update Tips</h5>
                </div>
                <div class="p-6 space-y-3">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Changing the vendor will affect GRN auto-resolution
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Vendor item codes must be unique per vendor
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Setting as preferred will update other mappings for this item
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Reference cost is for comparison only
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Quick Actions</h5>
                </div>
                <div class="p-6 space-y-3">
                    <button type="button" onclick="updateFromRecentCosts()" class="w-full btn-secondary btn-sm">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                        Update from Recent Costs
                    </button>

                    <a href="{{ route('inventory.mappings.show', $mapping) }}" class="w-full btn-secondary btn-sm">
                        <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                        View Full Details
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function updateFromRecentCosts() {
        if (confirm('Update reference cost from recent actual purchase costs?')) {
            fetch(`/inventory/mappings/{{ $mapping->id }}/update-cost`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('vendor_cost').value = data.updated_cost;
                    alert('Reference cost updated to $' + data.updated_cost);
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
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Item selection feedback
        const itemSelect = document.getElementById('item_id');
        if (itemSelect) {
            itemSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const category = selectedOption.getAttribute('data-category');
                    console.log('Selected item category:', category);
                }
            });
        }
    });
</script>
@endpush