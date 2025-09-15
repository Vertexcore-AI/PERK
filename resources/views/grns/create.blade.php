@extends('layouts.app')

@section('title', 'Create GRN')

@section('page-title')
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
            <i data-lucide="file-plus" class="w-6 h-6 text-white"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">New Goods Received Note</h1>
         
    </div>
@endsection

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('grns.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">GRNs</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">Create</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('grns.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to GRNs
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

    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-4">
                <!-- Step 1 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-600 text-white font-medium" id="step-1-indicator">
                        <span class="step-number">1</span>
                        <i data-lucide="check" class="w-5 h-5 hidden step-check"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">GRN Information</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Basic details</p>
                    </div>
                </div>

                <!-- Connector -->
                <div class="w-16 h-0.5 bg-slate-200 dark:bg-slate-700" id="progress-connector"></div>

                <!-- Step 2 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 font-medium" id="step-2-indicator">
                        <span class="step-number">2</span>
                        <i data-lucide="check" class="w-5 h-5 hidden step-check"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400" id="step-2-title">Enter Items</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add received items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wizard Form -->
    <form action="{{ route('grns.store') }}" method="POST" id="grn-wizard-form">
        @csrf

        <!-- Step 1: GRN Information -->
        <div class="wizard-step" id="step-1">
            <div class="max-w-4x2 mx-0">
                <div class="card">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                                <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-1">Step 1: GRN Information</h3>
                                <p class="text-slate-600 dark:text-slate-400">Enter basic goods received note details</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label for="vendor_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Vendor <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="truck" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <select name="vendor_id" id="vendor_id"
                                        class="pl-10 w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('vendor_id') border-rose-500 @enderror"
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
                                <label for="inv_no" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Invoice Number <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="hash" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <input type="text"
                                        name="inv_no"
                                        id="inv_no"
                                        value="{{ old('inv_no') }}"
                                        class="pl-10 w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('inv_no') border-rose-500 @enderror"
                                        placeholder="e.g., INV-2025-001"
                                        required>
                                </div>
                                @error('inv_no')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="billing_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Billing Date <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="calendar" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <input type="date"
                                        name="billing_date"
                                        id="billing_date"
                                        value="{{ old('billing_date', date('Y-m-d')) }}"
                                        class="pl-10 w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('billing_date') border-rose-500 @enderror"
                                        required>
                                </div>
                                @error('billing_date')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="default_store_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Default Store <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="building" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <select name="default_store_id" id="default_store_id"
                                        class="pl-10 w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="1">Main Store</option>
                                        @foreach($stores ?? [] as $store)
                                            <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                    Notes (Optional)
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 pointer-events-none">
                                        <i data-lucide="file-text" class="w-5 h-5 text-slate-400"></i>
                                    </div>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="pl-10 w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="Additional notes about this GRN...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-8 border-t border-slate-200 dark:border-slate-700 mt-8">
                            <button type="button" id="next-step-1" class="btn-primary">
                                Next: Enter Items
                                <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Enter Items -->
        <div class="wizard-step hidden" id="step-2">
            <div class="max-w-7x2 mx-0">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Main Items Section -->
                    <div class="lg:col-span-3">
                        <div class="card">
                            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">
                                            <i data-lucide="package" class="w-6 h-6 text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-1">Step 2: Enter Items</h3>
                                            <p class="text-slate-600 dark:text-slate-400">Add items received from vendor</p>
                                        </div>
                                    </div>
                                    <button type="button" id="add-item" class="btn-primary">
                                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                        Add Item
                                    </button>
                                </div>
                            </div>
                            <div class="p-6">
                                <div id="items-container">
                                    <!-- Items will be added here dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between pt-6">
                            <button type="button" id="prev-step-2" class="btn-secondary">
                                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                                Back to GRN Info
                            </button>
                            <div class="flex gap-3">
                                <button type="submit" class="btn-primary">
                                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                    Create GRN
                                </button>
                                <a href="{{ route('grns.index') }}" class="btn-secondary">
                                    <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Sidebar -->
                    <div class="space-y-6">
                        <!-- GRN Summary -->
                        <div class="card">
                            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                                <h5 class="text-lg font-semibold text-slate-900 dark:text-white">GRN Summary</h5>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Vendor</p>
                                    <p class="font-medium text-slate-900 dark:text-white" id="summary-vendor">Not selected</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Invoice No.</p>
                                    <p class="font-medium text-slate-900 dark:text-white" id="summary-invoice">-</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Billing Date</p>
                                    <p class="font-medium text-slate-900 dark:text-white" id="summary-date">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- Items Summary -->
                        <div class="card">
                            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                                <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Items Summary</h5>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Total Items:</span>
                                    <span class="font-medium text-slate-900 dark:text-white" id="total-items">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Total Quantity:</span>
                                    <span class="font-medium text-slate-900 dark:text-white" id="total-quantity">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">Subtotal:</span>
                                    <span class="font-medium text-slate-900 dark:text-white">$<span id="subtotal">0.00</span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600 dark:text-slate-400">VAT:</span>
                                    <span class="font-medium text-slate-900 dark:text-white">$<span id="total-vat">0.00</span></span>
                                </div>
                                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-semibold text-slate-900 dark:text-white">Total:</span>
                                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">$<span id="grand-total">0.00</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="card">
                            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                                <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Tips</h5>
                            </div>
                            <div class="p-6 space-y-3">
                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                    • Use exact vendor item codes from invoice
                                </div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                    • System will auto-map to internal items
                                </div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                    • Each item creates a batch for tracking
                                </div>
                                <div class="text-sm text-slate-600 dark:text-slate-400">
                                    • Stored qty can be less than received
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Item Template -->
    <template id="item-template">
        <div class="grn-item border border-slate-200 dark:border-slate-700 rounded-xl p-6 mb-6 bg-white dark:bg-slate-800">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="package" class="w-4 h-4 text-white"></i>
                    </div>
                    <h6 class="font-semibold text-slate-900 dark:text-white">Item #<span class="item-number"></span></h6>
                </div>
                <button type="button" class="remove-item p-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Vendor Item Code <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="hash" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text"
                            name="items[INDEX][vendor_item_code]"
                            class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent vendor-item-code"
                            placeholder="e.g., BP-001"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Received Qty <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="package" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="number"
                            name="items[INDEX][received_qty]"
                            class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent received-qty"
                            min="1"
                            placeholder="0"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Unit Price <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-400">$</span>
                        </div>
                        <input type="number"
                            name="items[INDEX][unit_price]"
                            class="pl-8 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent unit-price"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Discount (%)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="percent" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="number"
                            name="items[INDEX][discount]"
                            class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent discount"
                            step="0.01"
                            min="0"
                            max="100"
                            placeholder="0"
                            value="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        VAT (%)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="percent" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="number"
                            name="items[INDEX][vat]"
                            class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent vat"
                            step="0.01"
                            min="0"
                            max="100"
                            placeholder="0"
                            value="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Stored Qty
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="warehouse" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="number"
                            name="items[INDEX][stored_qty]"
                            class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent stored-qty"
                            min="0"
                            placeholder="Same as received">
                    </div>
                </div>

                <div class="md:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Store
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="building" class="w-4 h-4 text-slate-400"></i>
                                </div>
                                <select name="items[INDEX][store_id]" class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent store-id">
                                    <option value="1">Main Store</option>
                                    @foreach($stores ?? [] as $store)
                                        <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Bin (Optional)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="box" class="w-4 h-4 text-slate-400"></i>
                                </div>
                                <select name="items[INDEX][bin_id]" class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent bin-id">
                                    <option value="">No specific bin</option>
                                    @foreach($bins ?? [] as $bin)
                                        <option value="{{ $bin->id }}">{{ $bin->bin_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Notes (Optional)
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <i data-lucide="file-text" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <textarea name="items[INDEX][notes]"
                            class="pl-10 w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            rows="2"
                            placeholder="Optional notes about this item..."></textarea>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl p-4 border border-primary-200 dark:border-primary-800">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-primary-700 dark:text-primary-300">Item Total:</span>
                            <span class="text-lg font-bold text-primary-900 dark:text-primary-100">$<span class="item-total">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        let currentStep = 1;
        let itemIndex = 0;
        const itemsContainer = document.getElementById('items-container');
        const itemTemplate = document.getElementById('item-template');

        // Wizard Navigation
        const nextStepBtn = document.getElementById('next-step-1');
        const prevStepBtn = document.getElementById('prev-step-2');
        const addItemBtn = document.getElementById('add-item');

        // Navigation Handlers
        nextStepBtn.addEventListener('click', function() {
            if (validateStep1()) {
                goToStep(2);
                updateSummary();
                if (document.querySelectorAll('.grn-item').length === 0) {
                    addItem(); // Add first item automatically
                }
            }
        });

        prevStepBtn.addEventListener('click', function() {
            goToStep(1);
        });

        addItemBtn.addEventListener('click', addItem);

        function validateStep1() {
            const vendorId = document.getElementById('vendor_id');
            const invNo = document.getElementById('inv_no');
            const billingDate = document.getElementById('billing_date');

            let isValid = true;
            let errorMessage = '';

            if (!vendorId.value) {
                isValid = false;
                errorMessage += '• Please select a vendor\n';
                vendorId.classList.add('border-rose-500');
            } else {
                vendorId.classList.remove('border-rose-500');
            }

            if (!invNo.value) {
                isValid = false;
                errorMessage += '• Please enter invoice number\n';
                invNo.classList.add('border-rose-500');
            } else {
                invNo.classList.remove('border-rose-500');
            }

            if (!billingDate.value) {
                isValid = false;
                errorMessage += '• Please select billing date\n';
                billingDate.classList.add('border-rose-500');
            } else {
                billingDate.classList.remove('border-rose-500');
            }

            if (!isValid) {
                alert('Please complete the following fields:\n\n' + errorMessage);
            }

            return isValid;
        }

        function goToStep(step) {
            currentStep = step;

            // Hide all steps
            document.querySelectorAll('.wizard-step').forEach(stepDiv => {
                stepDiv.classList.add('hidden');
            });

            // Show current step
            document.getElementById(`step-${step}`).classList.remove('hidden');

            // Update progress indicators
            updateProgressIndicators();
        }

        function updateProgressIndicators() {
            for (let i = 1; i <= 2; i++) {
                const indicator = document.getElementById(`step-${i}-indicator`);
                const title = document.getElementById(`step-${i}-title`);
                const stepNumber = indicator.querySelector('.step-number');
                const stepCheck = indicator.querySelector('.step-check');

                if (i < currentStep) {
                    // Completed step
                    indicator.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-400', 'bg-primary-600');
                    indicator.classList.add('bg-emerald-500', 'text-white');
                    stepNumber.classList.add('hidden');
                    stepCheck.classList.remove('hidden');
                    if (title) {
                        title.classList.remove('text-slate-500', 'dark:text-slate-400');
                        title.classList.add('text-emerald-600', 'dark:text-emerald-400');
                    }
                } else if (i === currentStep) {
                    // Current step
                    indicator.classList.remove('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-400', 'bg-emerald-500');
                    indicator.classList.add('bg-primary-600', 'text-white');
                    stepNumber.classList.remove('hidden');
                    stepCheck.classList.add('hidden');
                    if (title) {
                        title.classList.remove('text-slate-500', 'dark:text-slate-400', 'text-emerald-600', 'dark:text-emerald-400');
                        title.classList.add('text-slate-900', 'dark:text-white');
                    }
                } else {
                    // Future step
                    indicator.classList.remove('bg-primary-600', 'bg-emerald-500', 'text-white');
                    indicator.classList.add('bg-slate-200', 'dark:bg-slate-700', 'text-slate-600', 'dark:text-slate-400');
                    stepNumber.classList.remove('hidden');
                    stepCheck.classList.add('hidden');
                    if (title) {
                        title.classList.remove('text-slate-900', 'dark:text-white', 'text-emerald-600', 'dark:text-emerald-400');
                        title.classList.add('text-slate-500', 'dark:text-slate-400');
                    }
                }
            }

            // Update progress connector
            const connector = document.getElementById('progress-connector');
            if (currentStep > 1) {
                connector.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                connector.classList.add('bg-emerald-500');
            } else {
                connector.classList.remove('bg-emerald-500');
                connector.classList.add('bg-slate-200', 'dark:bg-slate-700');
            }
        }

        function updateSummary() {
            const vendorSelect = document.getElementById('vendor_id');
            const invNo = document.getElementById('inv_no');
            const billingDate = document.getElementById('billing_date');

            // Update summary fields
            document.getElementById('summary-vendor').textContent = vendorSelect.options[vendorSelect.selectedIndex]?.text || 'Not selected';
            document.getElementById('summary-invoice').textContent = invNo.value || '-';
            document.getElementById('summary-date').textContent = billingDate.value ? new Date(billingDate.value).toLocaleDateString() : '-';
        }

        function addItem() {
            const template = itemTemplate.content.cloneNode(true);
            const itemDiv = template.querySelector('.grn-item');

            // Replace INDEX with actual index
            itemDiv.innerHTML = itemDiv.innerHTML.replace(/INDEX/g, itemIndex);
            itemDiv.querySelector('.item-number').textContent = itemIndex + 1;

            // Add event listeners
            itemDiv.querySelector('.remove-item').addEventListener('click', function() {
                if (document.querySelectorAll('.grn-item').length > 1) {
                    itemDiv.remove();
                    updateTotals();
                    updateItemNumbers();
                } else {
                    alert('At least one item is required');
                }
            });

            // Add calculation listeners
            const inputs = itemDiv.querySelectorAll('.received-qty, .unit-price, .discount, .vat');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    calculateItemTotal(itemDiv);
                    updateTotals();
                });
            });

            // Copy received qty to stored qty by default
            itemDiv.querySelector('.received-qty').addEventListener('input', function() {
                const storedQty = itemDiv.querySelector('.stored-qty');
                if (!storedQty.value) {
                    storedQty.value = this.value;
                }
            });

            // Vendor item code lookup (if needed)
            itemDiv.querySelector('.vendor-item-code').addEventListener('blur', function() {
                // Could add auto-lookup functionality here
                console.log('Looking up vendor item code:', this.value);
            });

            itemsContainer.appendChild(itemDiv);
            itemIndex++;
            updateTotals();

            // Re-initialize Lucide icons for new elements
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function calculateItemTotal(itemDiv) {
            const qty = parseFloat(itemDiv.querySelector('.received-qty').value) || 0;
            const price = parseFloat(itemDiv.querySelector('.unit-price').value) || 0;
            const discount = parseFloat(itemDiv.querySelector('.discount').value) || 0;
            const vat = parseFloat(itemDiv.querySelector('.vat').value) || 0;

            const subtotal = qty * price;
            const discountAmount = subtotal * (discount / 100);
            const afterDiscount = subtotal - discountAmount;
            const vatAmount = afterDiscount * (vat / 100);
            const total = afterDiscount + vatAmount;

            itemDiv.querySelector('.item-total').textContent = total.toFixed(2);
        }

        function updateTotals() {
            const items = document.querySelectorAll('.grn-item');
            let totalItems = items.length;
            let totalQty = 0;
            let subtotal = 0;
            let totalVat = 0;

            items.forEach(item => {
                const qty = parseFloat(item.querySelector('.received-qty').value) || 0;
                const price = parseFloat(item.querySelector('.unit-price').value) || 0;
                const discount = parseFloat(item.querySelector('.discount').value) || 0;
                const vat = parseFloat(item.querySelector('.vat').value) || 0;

                totalQty += qty;

                const itemSubtotal = qty * price;
                const discountAmount = itemSubtotal * (discount / 100);
                const afterDiscount = itemSubtotal - discountAmount;
                const vatAmount = afterDiscount * (vat / 100);

                subtotal += afterDiscount;
                totalVat += vatAmount;
            });

            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('total-quantity').textContent = totalQty;
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('total-vat').textContent = totalVat.toFixed(2);
            document.getElementById('grand-total').textContent = (subtotal + totalVat).toFixed(2);
        }

        function updateItemNumbers() {
            const items = document.querySelectorAll('.grn-item');
            items.forEach((item, index) => {
                item.querySelector('.item-number').textContent = index + 1;
            });
        }

        // Form submission validation
        document.getElementById('grn-wizard-form').addEventListener('submit', function(e) {
            if (document.querySelectorAll('.grn-item').length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the GRN');
                return false;
            }

            // Prevent double submission
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }

            // Disable submit button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Processing...';

            // Re-initialize lucide icons for the new loader icon
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Additional validation can be added here
            return true;
        });

        // Initialize progress indicators
        updateProgressIndicators();
    });
</script>
@endpush