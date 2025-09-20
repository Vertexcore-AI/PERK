@extends('layouts.app')

@section('title', 'Create Vendor Mapping')

@section('page-title', 'New Vendor Item Mapping')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('inventory.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Inventory</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('inventory.mappings.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Mapping</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">Create</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
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
                            <i data-lucide="link" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Mapping Information</h5>
                            <p class="text-slate-600 dark:text-slate-400">Link vendor item codes to internal items</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('inventory.mappings.store') }}" method="POST" class="space-y-6">
                        @csrf

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
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
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
                                        value="{{ old('vendor_item_code') }}"
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
                                        value="{{ old('vendor_item_name') }}"
                                        class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('vendor_item_name') border-rose-500 @enderror"
                                        placeholder="Vendor's product name">
                                </div>
                                @error('vendor_item_name')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vendor_cost" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Vendor Cost
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-400">$</span>
                                    </div>
                                    <input type="number"
                                        name="vendor_cost"
                                        id="vendor_cost"
                                        value="{{ old('vendor_cost') }}"
                                        step="0.01"
                                        min="0"
                                        class="pl-8 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('vendor_cost') border-rose-500 @enderror"
                                        placeholder="0.00">
                                </div>
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
                                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}
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
                                    {{ old('is_preferred') ? 'checked' : '' }}
                                    class="w-4 h-4 text-primary-600 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                                <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Set as preferred vendor</span>
                            </label>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button type="submit" class="btn-primary">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Create Mapping
                            </button>
                            <button type="reset" class="btn-secondary">
                                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i>
                                Reset
                            </button>
                            <a href="{{ route('inventory.mappings.index') }}" class="btn-secondary">
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
            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">About Vendor Mapping</h5>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mt-0.5">
                            <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h6 class="font-medium text-slate-900 dark:text-white">Multi-Vendor Support</h6>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Map different vendor codes to the same internal item for procurement flexibility</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mt-0.5">
                            <i data-lucide="star" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h6 class="font-medium text-slate-900 dark:text-white">Preferred Vendors</h6>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Mark preferred vendors for automatic selection during GRN processing</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mt-0.5">
                            <i data-lucide="trending-up" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <h6 class="font-medium text-slate-900 dark:text-white">Cost Tracking</h6>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Track vendor-specific costs for better pricing decisions</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Quick Tips</h5>
                </div>
                <div class="p-6 space-y-3">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Vendor item codes should be exactly as they appear on invoices
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • One vendor code can only map to one internal item
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Preferred vendors are selected first during GRN entry
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        • Cost information helps with procurement decisions
                    </div>
                </div>
            </div>
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