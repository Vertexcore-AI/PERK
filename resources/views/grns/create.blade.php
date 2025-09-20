@extends('layouts.app')

@section('title', 'Create GRN')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
@endpush

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
                                        <!-- <option value="1">Main Store</option> -->
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
                                    <div class="flex gap-3">
                                        <button type="button" id="import-excel" class="btn-secondary">
                                            <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
                                            Import Excel
                                        </button>
                                        <button type="button" id="import-pdf" class="btn-secondary">
                                            <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                            Import PDF
                                        </button>
                                        <button type="button" id="add-item" class="btn-primary">
                                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                            Add Item
                                        </button>
                                    </div>
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

    <!-- Excel Import Modal -->
    <div id="import-modal" class="fixed inset-0 bg-slate-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="file-spreadsheet" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Import from Excel</h3>
                            <p class="text-slate-600 dark:text-slate-400">Upload Excel file to import items</p>
                        </div>
                    </div>
                    <button id="close-modal" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                <!-- Step 1: Upload File -->
                <div id="upload-step">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="upload" class="w-10 h-10 text-white"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Upload Excel File</h4>
                        <p class="text-slate-600 dark:text-slate-400">Select an Excel file with your items to import</p>
                    </div>

                    <div class="mb-6">
                        <input type="file" id="excel-file" accept=".xlsx,.xls,.csv" class="hidden">
                        <div id="file-drop-zone" class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center hover:border-primary-500 transition-colors cursor-pointer">
                            <div class="space-y-3">
                                <i data-lucide="file-plus" class="w-12 h-12 text-slate-400 mx-auto"></i>
                                <div>
                                    <p class="text-slate-600 dark:text-slate-400 font-medium">Click to select file or drag and drop</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-500">Excel files (.xlsx, .xls) or CSV files</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Need a template?</span>
                        </div>
                        <a href="{{ route('grns.download-template') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                            Download Template
                        </a>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5"></i>
                            <div>
                                <h5 class="font-medium text-amber-800 dark:text-amber-300 mb-1">Expected Columns:</h5>
                                <ul class="text-sm text-amber-700 dark:text-amber-400 space-y-1">
                                    <li>• Item Code (vendor's item code)</li>
                                    <li>• Description (item name/description)</li>
                                    <li>• Unit Price (purchase price)</li>
                                    <li>• Quantity (received quantity)</li>
                                    <li>• VAT (%) - optional</li>
                                    <li>• Discount (%) - optional</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button id="cancel-import" class="btn-secondary">Cancel</button>
                        <button id="upload-file" class="btn-primary" disabled>
                            <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                            Upload & Process
                        </button>
                    </div>
                </div>

                <!-- Step 2: Processing & Resolution -->
                <div id="processing-step" class="hidden">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="settings" class="w-10 h-10 text-white animate-spin"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Processing Import</h4>
                        <p class="text-slate-600 dark:text-slate-400">Analyzing items and resolving mappings...</p>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Progress</span>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Processing...</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="bg-primary-600 h-2 rounded-full animate-pulse w-1/3"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Results & Resolution -->
                <div id="results-step" class="hidden">
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Import Results</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                                    <div>
                                        <div class="text-2xl font-bold text-green-900 dark:text-green-100" id="resolved-count">0</div>
                                        <div class="text-sm text-green-700 dark:text-green-300">Resolved</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="help-circle" class="w-8 h-8 text-amber-600 dark:text-amber-400"></i>
                                    <div>
                                        <div class="text-2xl font-bold text-amber-900 dark:text-amber-100" id="suggestions-count">0</div>
                                        <div class="text-sm text-amber-700 dark:text-amber-300">Suggestions</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="x-circle" class="w-8 h-8 text-red-600 dark:text-red-400"></i>
                                    <div>
                                        <div class="text-2xl font-bold text-red-900 dark:text-red-100" id="unresolved-count">0</div>
                                        <div class="text-sm text-red-700 dark:text-red-300">Unresolved</div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="file-text" class="w-8 h-8 text-slate-600 dark:text-slate-400"></i>
                                    <div>
                                        <div class="text-2xl font-bold text-slate-900 dark:text-slate-100" id="total-count">0</div>
                                        <div class="text-sm text-slate-600 dark:text-slate-400">Total Items</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="resolution-content">
                        <!-- Resolution content will be populated here -->
                    </div>

                    <div class="flex justify-between gap-3 mt-6">
                        <button id="back-to-upload" class="btn-secondary">
                            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                            Back to Upload
                        </button>
                        <div class="flex gap-3">
                            <button id="finalize-import" class="btn-primary" disabled>
                                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                                Import Items
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div id="custom-alert" class="fixed inset-0 bg-slate-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div id="alert-icon" class="w-10 h-10 rounded-xl flex items-center justify-center">
                        <i id="alert-icon-symbol" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 id="alert-title" class="text-lg font-semibold text-slate-900 dark:text-white"></h3>
                    </div>
                </div>
                <p id="alert-message" class="text-slate-600 dark:text-slate-400 mb-6"></p>
                <div class="flex justify-end gap-3">
                    <button id="alert-cancel" class="btn-secondary hidden">Cancel</button>
                    <button id="alert-ok" class="btn-primary">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Template -->
    <template id="item-template">
        <div class="grn-item bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6 mb-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-medium text-sm">
                        <span class="item-number">1</span>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Item Details</h4>
                </div>
                <button type="button" class="remove-item p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Item Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Vendor Item Code <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                            name="items[INDEX][vendor_item_code]"
                            class="vendor-item-code w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Vendor's item code"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Description <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                            name="items[INDEX][description]"
                            class="description w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Item description"
                            required>
                    </div>
                </div>

                <!-- Quantities & Pricing -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Received Qty <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                name="items[INDEX][received_qty]"
                                class="received-qty w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0"
                                min="0"
                                step="0.01"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Stored Qty
                            </label>
                            <input type="number"
                                name="items[INDEX][stored_qty]"
                                class="stored-qty w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0"
                                min="0"
                                step="0.01">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Unit Price <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500">$</span>
                                <input type="number"
                                    name="items[INDEX][unit_price]"
                                    class="unit-price w-full pl-8 pr-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="0.00"
                                    min="0"
                                    step="0.01"
                                    required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Selling Price
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500">$</span>
                                <input type="number"
                                    name="items[INDEX][selling_price]"
                                    class="selling-price w-full pl-8 pr-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    placeholder="0.00"
                                    min="0"
                                    step="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discounts & Totals -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Discount %
                            </label>
                            <input type="number"
                                name="items[INDEX][discount]"
                                class="discount w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0"
                                min="0"
                                max="100"
                                step="0.01">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                VAT %
                            </label>
                            <input type="number"
                                name="items[INDEX][vat]"
                                class="vat w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="0"
                                min="0"
                                max="100"
                                step="0.01">
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-600 dark:text-slate-400">Unit Cost:</span>
                                <span class="font-medium text-slate-900 dark:text-white">$<span class="unit-cost-display">0.00</span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600 dark:text-slate-400">Selling Price:</span>
                                <span class="font-medium text-slate-900 dark:text-white">$<span class="selling-price-display">0.00</span></span>
                            </div>
                            <div class="flex justify-between col-span-2 pt-2 border-t border-slate-200 dark:border-slate-600">
                                <span class="text-slate-700 dark:text-slate-300 font-medium">Total:</span>
                                <span class="font-bold text-primary-600 dark:text-primary-400">$<span class="item-total">0.00</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for internal mapping -->
            <input type="hidden" name="items[INDEX][internal_item_id]" class="internal-item-id">
            <input type="hidden" name="items[INDEX][store_id]" class="store-id">
            <input type="hidden" name="items[INDEX][bin_id]" class="bin-id">
        </div>
    </template>

    <!-- Excel Import Loading Overlay -->
    <div id="excel-import-overlay" class="pdf-import-overlay hidden">
        <div class="loading-container">
            <div class="loader"></div>
            <div class="loading-message" id="loading-message">Processing Your Excel File</div>
            <div class="loading-submessage" id="loading-submessage">Please wait while we analyze your document...</div>
            <div class="progress-dots">
                <div class="progress-dot progress-dot"></div>
                <div class="progress-dot progress-dot"></div>
                <div class="progress-dot progress-dot"></div>
                <div class="progress-dot progress-dot"></div>
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

        // Clear any existing blur on page load
        const mainContent = document.querySelector('main') || document.body;
        mainContent.classList.remove('content-blurred');

        // Custom Alert Functions
        function showAlert(message, type = 'info', title = '', showCancel = false) {
            return new Promise((resolve) => {
                const alertModal = document.getElementById('custom-alert');
                const alertIcon = document.getElementById('alert-icon');
                const alertIconSymbol = document.getElementById('alert-icon-symbol');
                const alertTitle = document.getElementById('alert-title');
                const alertMessage = document.getElementById('alert-message');
                const alertOk = document.getElementById('alert-ok');
                const alertCancel = document.getElementById('alert-cancel');

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
                        iconBg = 'bg-gradient-to-br from-blue-500 to-blue-600';
                        defaultTitle = 'Information';
                }

                alertIcon.className = `w-10 h-10 rounded-xl flex items-center justify-center text-white ${iconBg}`;
                alertIconSymbol.setAttribute('data-lucide', iconClass);
                alertTitle.textContent = title || defaultTitle;
                alertMessage.textContent = message;

                if (showCancel) {
                    alertCancel.classList.remove('hidden');
                    alertCancel.onclick = () => {
                        alertModal.classList.add('hidden');
                        alertModal.classList.remove('flex');
                        resolve(false);
                    };
                } else {
                    alertCancel.classList.add('hidden');
                }

                alertOk.onclick = () => {
                    alertModal.classList.add('hidden');
                    alertModal.classList.remove('flex');
                    resolve(true);
                };

                alertModal.classList.remove('hidden');
                alertModal.classList.add('flex');

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        }

        let currentStep = 1;
        let itemIndex = 0;
        const itemsContainer = document.getElementById('items-container');

        // Wizard Navigation
        const nextStepBtn = document.getElementById('next-step-1');
        const prevStepBtn = document.getElementById('prev-step-2');
        const addItemBtn = document.getElementById('add-item');
        const importExcelBtn = document.getElementById('import-excel');

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
        importExcelBtn.addEventListener('click', openImportModal);

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
                showAlert('Please complete the following fields:\n\n' + errorMessage, 'warning', 'Validation Error');
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
            const itemTemplate = document.getElementById('item-template');
            if (!itemTemplate) {
                showAlert('Item template not found', 'error');
                return;
            }

            const template = itemTemplate.content.cloneNode(true);
            const itemDiv = template.querySelector('.grn-item');

            // Replace INDEX with actual index
            itemDiv.innerHTML = itemDiv.innerHTML.replace(/INDEX/g, itemIndex);
            itemDiv.querySelector('.item-number').textContent = itemIndex + 1;

            // Set default store from form
            const defaultStoreId = document.getElementById('default_store_id').value;
            if (defaultStoreId) {
                itemDiv.querySelector('.store-id').value = defaultStoreId;
            }

            // Add event listeners
            itemDiv.querySelector('.remove-item').addEventListener('click', function() {
                if (document.querySelectorAll('.grn-item').length > 1) {
                    itemDiv.remove();
                    updateTotals();
                    updateItemNumbers();
                } else {
                    showAlert('At least one item is required', 'warning', 'Item Required');
                }
            });

            // Add calculation listeners
            const inputs = itemDiv.querySelectorAll('.received-qty, .unit-price, .selling-price, .discount, .vat');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    calculateItemTotal(itemDiv);
                    updateTotals();
                });
            });

            // Auto-suggest selling price based on unit price with 30% markup
            itemDiv.querySelector('.unit-price').addEventListener('input', function() {
                const sellingPriceInput = itemDiv.querySelector('.selling-price');
                if (!sellingPriceInput.value || sellingPriceInput.value == 0) {
                    const unitPrice = parseFloat(this.value) || 0;
                    const suggestedPrice = unitPrice * 1.3; // 30% markup
                    sellingPriceInput.value = suggestedPrice.toFixed(2);
                    calculateItemTotal(itemDiv);
                    updateTotals();
                }
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
            const sellingPrice = parseFloat(itemDiv.querySelector('.selling-price').value) || 0;
            const discount = parseFloat(itemDiv.querySelector('.discount').value) || 0;
            const vat = parseFloat(itemDiv.querySelector('.vat').value) || 0;

            // Calculate unit cost after discount
            const unitCost = price - (price * discount / 100);

            const subtotal = qty * price;
            const discountAmount = subtotal * (discount / 100);
            const afterDiscount = subtotal - discountAmount;
            const vatAmount = afterDiscount * (vat / 100);
            const total = afterDiscount + vatAmount;

            // Update display elements
            itemDiv.querySelector('.unit-cost-display').textContent = unitCost.toFixed(2);
            itemDiv.querySelector('.selling-price-display').textContent = sellingPrice.toFixed(2);
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

        // Excel Import Modal Functions
        function openImportModal() {
            if (!validateStep1()) {
                alert('Please complete GRN information first before importing items.');
                return;
            }
            document.getElementById('import-modal').classList.remove('hidden');
            document.getElementById('import-modal').classList.add('flex');
            resetImportModal();
        }

        function closeImportModal() {
            document.getElementById('import-modal').classList.add('hidden');
            document.getElementById('import-modal').classList.remove('flex');
        }

        function resetImportModal() {
            document.getElementById('upload-step').classList.remove('hidden');
            document.getElementById('processing-step').classList.add('hidden');
            document.getElementById('results-step').classList.add('hidden');
            document.getElementById('excel-file').value = '';
            document.getElementById('upload-file').disabled = true;
            updateFileDropZone();
        }

        function updateFileDropZone(file = null) {
            const dropZone = document.getElementById('file-drop-zone');
            if (file) {
                dropZone.innerHTML = `
                    <div class="space-y-3">
                        <i data-lucide="file-check" class="w-12 h-12 text-green-500 mx-auto"></i>
                        <div>
                            <p class="text-slate-600 dark:text-slate-400 font-medium">${file.name}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                        <button type="button" onclick="document.getElementById('excel-file').value = ''; updateFileDropZone(); document.getElementById('upload-file').disabled = true;" class="text-sm text-red-600 hover:text-red-700">Remove file</button>
                    </div>
                `;
                document.getElementById('upload-file').disabled = false;
            } else {
                dropZone.innerHTML = `
                    <div class="space-y-3">
                        <i data-lucide="file-plus" class="w-12 h-12 text-slate-400 mx-auto"></i>
                        <div>
                            <p class="text-slate-600 dark:text-slate-400 font-medium">Click to select file or drag and drop</p>
                            <p class="text-sm text-slate-500 dark:text-slate-500">Excel files (.xlsx, .xls) or CSV files</p>
                        </div>
                    </div>
                `;
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Import Modal Event Listeners
        document.getElementById('close-modal').addEventListener('click', closeImportModal);
        document.getElementById('cancel-import').addEventListener('click', closeImportModal);
        document.getElementById('back-to-upload').addEventListener('click', resetImportModal);

        // File handling
        document.getElementById('file-drop-zone').addEventListener('click', () => {
            document.getElementById('excel-file').click();
        });

        document.getElementById('excel-file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                updateFileDropZone(file);
            }
        });

        // Drag and drop
        const dropZone = document.getElementById('file-drop-zone');
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('excel-file').files = files;
                updateFileDropZone(files[0]);
            }
        });

        // Upload and process file
        document.getElementById('upload-file').addEventListener('click', function() {
            const fileInput = document.getElementById('excel-file');
            const vendorId = document.getElementById('vendor_id').value;

            if (!fileInput.files[0] || !vendorId) {
                alert('Please select both a vendor and a file');
                return;
            }

            // Show loading overlay with blur effect
            showLoadingOverlay();
            blurMainContent();

            // Start progress messages
            startProgressMessages();

            const formData = new FormData();
            formData.append('excel_file', fileInput.files[0]);
            formData.append('vendor_id', vendorId);

            // Show processing step (but hidden behind overlay)
            document.getElementById('upload-step').classList.add('hidden');
            document.getElementById('processing-step').classList.remove('hidden');

            fetch('{{ route("grns.upload-excel") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading overlay
                hideLoadingOverlay();
                unblurMainContent();

                if (data.success) {
                    showImportResults(data);
                } else {
                    alert('Error: ' + data.message);
                    resetImportModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                hideLoadingOverlay();
                unblurMainContent();
                alert('An error occurred while processing the file');
                resetImportModal();
            });
        });

        function showImportResults(data) {
            document.getElementById('processing-step').classList.add('hidden');
            document.getElementById('results-step').classList.remove('hidden');

            // Update counts
            document.getElementById('resolved-count').textContent = data.stats.resolved;
            document.getElementById('suggestions-count').textContent = data.stats.suggestions;
            document.getElementById('unresolved-count').textContent = data.stats.unresolved;
            document.getElementById('total-count').textContent = data.stats.total;

            // Check if we can finalize - allow if we have at least some resolved items
            const canFinalize = data.stats.resolved > 0;
            document.getElementById('finalize-import').disabled = !canFinalize;

            // Update button text to reflect what will happen
            const finalizeBtn = document.getElementById('finalize-import');
            if (data.stats.unresolved > 0 || data.stats.suggestions > 0) {
                finalizeBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Import ' + data.stats.resolved + ' Items';
            } else {
                finalizeBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Import Items';
            }

            // Show resolution content
            const resolutionContent = document.getElementById('resolution-content');
            let contentHtml = '';

            if (data.stats.resolved > 0) {
                contentHtml += '<div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-4">' +
                    '<h5 class="font-medium text-green-800 dark:text-green-300 mb-2">' +
                    '<i data-lucide="check-circle" class="w-4 h-4 inline mr-2"></i>' +
                    data.stats.resolved + ' items ready to import</h5>' +
                    '<p class="text-sm text-green-700 dark:text-green-400">These items have been successfully mapped and are ready for import.</p>' +
                    '</div>';
            }

            // Show info if there are unresolved items that need attention
            if (data.stats.unresolved > 0 || data.stats.suggestions > 0) {
                const pendingCount = data.stats.unresolved + data.stats.suggestions;
                contentHtml += '<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">' +
                    '<h5 class="font-medium text-blue-800 dark:text-blue-300 mb-2">' +
                    '<i data-lucide="info" class="w-4 h-4 inline mr-2"></i>' +
                    pendingCount + ' items need resolution</h5>' +
                    '<p class="text-sm text-blue-700 dark:text-blue-400">Please resolve the items below. All items will be imported - either mapped to existing items or created as new items.</p>' +
                    '</div>';
            }

            // Show suggestions for resolution
            if (data.data.suggestions && data.data.suggestions.length > 0) {
                contentHtml += `
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-4">
                        <h5 class="font-medium text-amber-800 dark:text-amber-300 mb-3">
                            <i data-lucide="help-circle" class="w-4 h-4 inline mr-2"></i>
                            Items with suggestions
                        </h5>
                        <div class="space-y-3">
                `;

                data.data.suggestions.forEach((item, index) => {
                    contentHtml += `
                        <div class="bg-white dark:bg-slate-700 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h6 class="font-medium text-slate-900 dark:text-white">${item.item_code}</h6>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">${item.description}</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                                        <div>
                                            <span class="text-xs text-slate-500">Qty:</span>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 ml-1">${item.quantity}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500">Unit Price:</span>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 ml-1">$${item.unit_price}</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-500">Selling Price:</label>
                                            <input type="number"
                                                id="selling_price_sug_${index}"
                                                value="${item.selling_price || (item.unit_price * 1.3).toFixed(2)}"
                                                step="0.01"
                                                class="ml-1 w-20 px-1 py-0.5 text-sm border border-slate-300 dark:border-slate-600 rounded dark:bg-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500">Markup:</span>
                                            <span id="markup_sug_${index}" class="text-sm font-medium text-green-600 dark:text-green-400 ml-1">
                                                ${((((item.selling_price || item.unit_price * 1.3) - item.unit_price) / item.unit_price) * 100).toFixed(1)}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 mt-3">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Select matching item:</label>
                    `;

                    item.suggestions.forEach((suggestion, sugIndex) => {
                        contentHtml += `
                            <label class="flex items-center p-2 border border-slate-200 dark:border-slate-600 rounded cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-600">
                                <input type="radio" name="suggestion_${index}" value="${suggestion.id}" class="mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-900 dark:text-white">${suggestion.name}</span>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">${suggestion.similarity_score}% match</span>
                                    </div>
                                    <div class="text-sm text-slate-600 dark:text-slate-400">
                                        ${suggestion.item_no} • ${suggestion.category || 'No category'}
                                    </div>
                                </div>
                            </label>
                        `;
                    });

                    contentHtml += `
                                <label class="flex items-center p-2 border border-slate-200 dark:border-slate-600 rounded cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-600">
                                    <input type="radio" name="suggestion_${index}" value="create_new" class="mr-3">
                                    <div class="flex-1">
                                        <span class="font-medium text-blue-600 dark:text-blue-400">Create new item</span>
                                        <div class="text-sm text-slate-600 dark:text-slate-400">Create a new item with this description</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    `;
                });

                contentHtml += `
                        </div>
                    </div>
                `;
            }

            // Show unresolved items
            if (data.data.unresolved && data.data.unresolved.length > 0) {
                contentHtml += `
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-4">
                        <h5 class="font-medium text-red-800 dark:text-red-300 mb-3">
                            <i data-lucide="x-circle" class="w-4 h-4 inline mr-2"></i>
                            Unresolved items
                        </h5>
                        <div class="space-y-3">
                `;

                data.data.unresolved.forEach((item, index) => {
                    contentHtml += `
                        <div class="bg-white dark:bg-slate-700 border border-red-200 dark:border-red-700 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h6 class="font-medium text-slate-900 dark:text-white">${item.item_code}</h6>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">${item.description}</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                                        <div>
                                            <span class="text-xs text-slate-500">Qty:</span>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 ml-1">${item.quantity}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500">Unit Price:</span>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 ml-1">$${item.unit_price}</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-500">Selling Price:</label>
                                            <input type="number"
                                                id="selling_price_unr_${index}"
                                                value="${item.selling_price || (item.unit_price * 1.3).toFixed(2)}"
                                                step="0.01"
                                                class="ml-1 w-20 px-1 py-0.5 text-sm border border-slate-300 dark:border-slate-600 rounded dark:bg-slate-600 dark:text-white">
                                        </div>
                                        <div>
                                            <span class="text-xs text-slate-500">Markup:</span>
                                            <span id="markup_unr_${index}" class="text-sm font-medium text-green-600 dark:text-green-400 ml-1">
                                                ${((((item.selling_price || item.unit_price * 1.3) - item.unit_price) / item.unit_price) * 100).toFixed(1)}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2 mt-3">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Resolution action:</label>
                                <label class="flex items-center p-2 border border-slate-200 dark:border-slate-600 rounded cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-600">
                                    <input type="radio" name="unresolved_${index}" value="create_new" class="mr-3" checked>
                                    <div class="flex-1">
                                        <span class="font-medium text-blue-600 dark:text-blue-400">Create new item</span>
                                        <div class="text-sm text-slate-600 dark:text-slate-400">Create a new item with this description</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    `;
                });

                contentHtml += `
                        </div>
                    </div>
                `;
            }

            // Add resolution actions
            if (data.stats.suggestions > 0 || data.stats.unresolved > 0) {
                contentHtml += `
                    <div class="flex justify-center mt-4">
                        <button id="apply-resolutions" class="btn-primary">
                            <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                            Apply Resolutions
                        </button>
                    </div>
                `;
            }

            resolutionContent.innerHTML = contentHtml;

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Add event listeners for selling price inputs
            if (data.data.suggestions) {
                data.data.suggestions.forEach((item, index) => {
                    const sellingInput = document.getElementById(`selling_price_sug_${index}`);
                    const markupSpan = document.getElementById(`markup_sug_${index}`);

                    if (sellingInput && markupSpan) {
                        sellingInput.addEventListener('input', function() {
                            const sellingPrice = parseFloat(this.value) || 0;
                            const unitPrice = item.unit_price;
                            const markup = unitPrice > 0 ? ((sellingPrice - unitPrice) / unitPrice * 100).toFixed(1) : 0;
                            markupSpan.textContent = `${markup}%`;
                            markupSpan.className = markup >= 0 ?
                                'text-sm font-medium text-green-600 dark:text-green-400 ml-1' :
                                'text-sm font-medium text-red-600 dark:text-red-400 ml-1';
                        });
                    }
                });
            }

            if (data.data.unresolved) {
                data.data.unresolved.forEach((item, index) => {
                    const sellingInput = document.getElementById(`selling_price_unr_${index}`);
                    const markupSpan = document.getElementById(`markup_unr_${index}`);

                    if (sellingInput && markupSpan) {
                        sellingInput.addEventListener('input', function() {
                            const sellingPrice = parseFloat(this.value) || 0;
                            const unitPrice = item.unit_price;
                            const markup = unitPrice > 0 ? ((sellingPrice - unitPrice) / unitPrice * 100).toFixed(1) : 0;
                            markupSpan.textContent = `${markup}%`;
                            markupSpan.className = markup >= 0 ?
                                'text-sm font-medium text-green-600 dark:text-green-400 ml-1' :
                                'text-sm font-medium text-red-600 dark:text-red-400 ml-1';
                        });
                    }
                });
            }

            // Add event listener for apply resolutions button
            const applyBtn = document.getElementById('apply-resolutions');
            if (applyBtn) {
                applyBtn.addEventListener('click', applyResolutions);
            }

            // Store import data globally for resolution processing
            window.importData = data;
        }

        function applyResolutions() {
            const resolutions = [];

            // Collect suggestion resolutions
            const suggestionForms = document.querySelectorAll('[name^="suggestion_"]');
            const suggestionGroups = {};

            suggestionForms.forEach(input => {
                if (input.checked) {
                    const groupName = input.name;
                    const index = groupName.split('_')[1];
                    suggestionGroups[index] = input.value;
                }
            });

            // Collect unresolved resolutions
            const unresolvedForms = document.querySelectorAll('[name^="unresolved_"]');
            const unresolvedGroups = {};

            unresolvedForms.forEach(input => {
                if (input.checked) {
                    const groupName = input.name;
                    const index = groupName.split('_')[1];
                    unresolvedGroups[index] = input.value;
                }
            });

            // Validate that all items have resolutions
            const totalNeedingResolution = (window.importData?.data?.suggestions?.length || 0) +
                                          (window.importData?.data?.unresolved?.length || 0);
            const totalResolutions = Object.keys(suggestionGroups).length + Object.keys(unresolvedGroups).length;

            if (totalResolutions < totalNeedingResolution) {
                alert('Please provide a resolution for all items before proceeding.');
                return;
            }

            // Apply resolutions
            const applyBtn = document.getElementById('apply-resolutions');
            applyBtn.disabled = true;
            applyBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Applying...';

            // Process each resolution sequentially
            let processedCount = 0;
            const totalToProcess = Object.keys(suggestionGroups).length + Object.keys(unresolvedGroups).length;

            // Combine all resolutions to process
            const resolutionsToProcess = [];

            // Add suggestions to queue
            Object.entries(suggestionGroups).forEach(([index, action]) => {
                let mappedAction = action;
                let itemId = null;

                if (action === 'create_new') {
                    mappedAction = action;
                } else {
                    // This is an item ID for mapping to existing item
                    mappedAction = 'map_existing';
                    itemId = action;
                }

                resolutionsToProcess.push({
                    index: index,
                    action: mappedAction,
                    sourceArray: 'suggestions',
                    itemId: itemId
                });
            });

            // Add unresolved to queue
            Object.entries(unresolvedGroups).forEach(([index, action]) => {
                let mappedAction = action;
                let itemId = null;

                if (action === 'create_new') {
                    mappedAction = action;
                } else {
                    // This should not happen for unresolved, but handle it just in case
                    mappedAction = 'map_existing';
                    itemId = action;
                }

                resolutionsToProcess.push({
                    index: index,
                    action: mappedAction,
                    sourceArray: 'unresolved',
                    itemId: itemId
                });
            });

            // Process resolutions sequentially
            async function processAllResolutions() {
                for (const resolution of resolutionsToProcess) {
                    try {
                        await processResolutionAsync(resolution.index, resolution.action, resolution.itemId, resolution.sourceArray);
                        processedCount++;
                    } catch (error) {
                        console.error('Error processing resolution:', error);
                        showAlert('Error processing item: ' + error.message, 'error');
                        resetApplyButton();
                        return;
                    }
                }

                // All resolutions processed
                checkResolutionComplete();
            }

            processAllResolutions();

            async function processResolutionAsync(rowIndex, action, itemId, sourceArray) {
                console.log('Processing resolution:', { rowIndex, action, itemId, sourceArray });

                const formData = new FormData();
                formData.append('row_index', rowIndex);
                formData.append('action', action);
                if (itemId) {
                    formData.append('item_id', itemId);
                }
                if (action === 'create_new') {
                    // Get item data from the current import data state
                    const sourceArrayData = sourceArray === 'suggestions' ?
                        window.importData.data.suggestions :
                        window.importData.data.unresolved;

                    console.log('Available source data:', sourceArrayData);
                    console.log('Looking for rowIndex:', rowIndex, 'in array of length:', sourceArrayData ? sourceArrayData.length : 'undefined');

                    // Find the item by original_index or by current position
                    let sourceData = null;
                    if (sourceArrayData && sourceArrayData.length > 0) {
                        // First try to find by original_index
                        sourceData = sourceArrayData.find(item => item.original_index == rowIndex);

                        // If not found by original_index, try by current array position
                        if (!sourceData && sourceArrayData[rowIndex]) {
                            sourceData = sourceArrayData[rowIndex];
                        }

                        // If still not found, use the first available item if rowIndex is out of bounds
                        if (!sourceData && sourceArrayData.length > 0) {
                            console.warn('Using first available item as fallback');
                            sourceData = sourceArrayData[0];
                        }
                    }

                    console.log('Found source data:', sourceData);

                    if (sourceData) {
                        // Get the selling price from the input field
                        const sellingPriceInput = document.getElementById(
                            sourceArray === 'suggestions' ? `selling_price_sug_${rowIndex}` : `selling_price_unr_${rowIndex}`
                        );
                        const sellingPrice = sellingPriceInput ? parseFloat(sellingPriceInput.value) : sourceData.selling_price;

                        // Update the sourceData with the new selling price
                        sourceData.selling_price = sellingPrice;

                        const itemData = {
                            name: sourceData.description || sourceData.item_code,
                            description: sourceData.description || sourceData.item_code,
                            unit_of_measure: 'pcs'
                        };

                        console.log('Sending item_data:', itemData);
                        formData.append('item_data', JSON.stringify(itemData));

                        // Also send the selling price separately for batch processing
                        formData.append('selling_price', sellingPrice);
                    } else {
                        console.error('No source data found for rowIndex:', rowIndex, 'sourceArray:', sourceArray);
                        console.error('Available arrays:', window.importData.data);
                        throw new Error(`No source data found for item at index ${rowIndex}. Available items: ${sourceArrayData ? sourceArrayData.length : 0}`);
                    }
                }

                const response = await fetch('{{ route("grns.resolve-mapping") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                // Check if response is JSON
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    // If not JSON, likely an error page
                    if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    throw new Error('Server error. Please refresh the page and try again.');
                }

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.success) {
                    console.error('Server validation error:', data);
                    let errorMessage = data.message || 'Validation failed';
                    if (data.errors) {
                        errorMessage += '\n\nDetails:\n' + Object.entries(data.errors)
                            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                            .join('\n');
                    }
                    if (data.debug) {
                        console.log('Debug info:', data.debug);
                    }
                    throw new Error(errorMessage);
                }

                // Update the stored import data
                window.importData = data;
                return data;
            }

            function processResolution(rowIndex, action, itemId, sourceArray) {
                const formData = new FormData();
                formData.append('row_index', rowIndex);
                formData.append('action', action);
                if (itemId) {
                    formData.append('item_id', itemId);
                }
                if (action === 'create_new') {
                    // Get item data from the original import data
                    const sourceData = sourceArray === 'suggestions' ?
                        window.importData.data.suggestions[rowIndex] :
                        window.importData.data.unresolved[rowIndex];

                    if (sourceData) {
                        formData.append('item_data', JSON.stringify({
                            name: sourceData.description || sourceData.item_code,
                            description: sourceData.description || sourceData.item_code,
                            unit_of_measure: 'pcs'
                        }));
                    }
                }

                fetch('{{ route("grns.resolve-mapping") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the stored import data
                        window.importData = data;
                        processedCount++;
                        checkResolutionComplete();
                    } else {
                        alert('Error resolving item: ' + data.message);
                        resetApplyButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while applying resolutions');
                    resetApplyButton();
                });
            }

            function checkResolutionComplete() {
                if (processedCount >= totalToProcess) {
                    // Refresh the results display
                    showImportResults(window.importData);
                }
            }

            function resetApplyButton() {
                applyBtn.disabled = false;
                applyBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Apply Resolutions';
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Finalize import
        document.getElementById('finalize-import').addEventListener('click', function() {
            const vendorId = document.getElementById('vendor_id').value;
            const invNo = document.getElementById('inv_no').value;
            const billingDate = document.getElementById('billing_date').value;

            const formData = new FormData();
            formData.append('vendor_id', vendorId);
            formData.append('inv_no', invNo);
            formData.append('billing_date', billingDate);

            this.disabled = true;
            this.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Processing...';

            fetch('{{ route("grns.process-import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Import completed successfully!');
                    window.location.href = data.redirect;
                } else {
                    alert('Error: ' + data.message);
                    this.disabled = false;
                    this.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Import Items';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during import');
                this.disabled = false;
                this.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Import Items';
            });

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Loading overlay functions
        let progressInterval;
        const progressMessages = [
            { message: "Vertex AI is Reading Your File", submessage: "Analyzing document structure and content..." },
            { message: "Mapping Vendor Codes", submessage: "Matching items with existing inventory..." },
            { message: "Calculating Prices", submessage: "Processing costs and pricing information..." },
            { message: "Finalizing Import", submessage: "Preparing data for your review..." }
        ];

        function showLoadingOverlay() {
            const overlay = document.getElementById('excel-import-overlay');
            if (overlay) {
                overlay.classList.remove('hidden');
            }
        }

        function hideLoadingOverlay() {
            const overlay = document.getElementById('excel-import-overlay');
            if (overlay) {
                overlay.classList.add('hidden');
            }
            if (progressInterval) {
                clearInterval(progressInterval);
                progressInterval = null;
            }
        }

        function blurMainContent() {
            // No blur effect
        }

        function unblurMainContent() {
            // No blur effect
        }

        function startProgressMessages() {
            let currentMessageIndex = 0;
            const loadingMessage = document.getElementById('loading-message');
            const loadingSubmessage = document.getElementById('loading-submessage');
            const progressDots = document.querySelectorAll('.progress-dot');

            // Update progress dots
            function updateProgressDots(activeIndex) {
                progressDots.forEach((dot, index) => {
                    if (index <= activeIndex) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }

            // Show first message immediately
            if (progressMessages[0] && loadingMessage && loadingSubmessage) {
                loadingMessage.textContent = progressMessages[0].message;
                loadingSubmessage.textContent = progressMessages[0].submessage;
                updateProgressDots(0);
            }

            // Cycle through messages
            progressInterval = setInterval(() => {
                currentMessageIndex = (currentMessageIndex + 1) % progressMessages.length;
                const currentMessage = progressMessages[currentMessageIndex];

                if (currentMessage && loadingMessage && loadingSubmessage) {
                    loadingMessage.textContent = currentMessage.message;
                    loadingSubmessage.textContent = currentMessage.submessage;
                    updateProgressDots(currentMessageIndex);
                }
            }, 2000); // Change message every 2 seconds
        }

        // Initialize progress indicators
        updateProgressIndicators();
    });
</script>
@endpush