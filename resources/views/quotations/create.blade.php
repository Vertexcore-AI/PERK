@extends('layouts.app')

@section('title', 'Create Quotation')

@section('content')
<div class="container-fluid px-2" x-data="quotationWizard()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Create Quotation</h1>
            <p class="text-xs text-gray-600 dark:text-gray-400">Create a new quotation for a customer</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" class="w-3 h-3 mr-1"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="mb-4">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-3">
                <!-- Step 1 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full font-medium text-sm"
                         :class="currentStep >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                        <span x-show="currentStep > 1">✓</span>
                        <span x-show="currentStep <= 1">1</span>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs font-medium" :class="currentStep >= 1 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'">Customer Info</p>
                    </div>
                </div>

                <!-- Connector -->
                <div class="w-12 h-0.5" :class="currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'"></div>

                <!-- Step 2 -->
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full font-medium text-sm"
                         :class="currentStep >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                        <span x-show="currentStep > 2">✓</span>
                        <span x-show="currentStep <= 2">2</span>
                    </div>
                    <div class="ml-2">
                        <p class="text-xs font-medium" :class="currentStep >= 2 ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'">Items & Summary</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('quotations.store') }}" method="POST" @submit="validateForm">
        @csrf

        <!-- Step 1: Customer & Vehicle Information -->
        <div x-show="currentStep === 1" class="wizard-step">
            <div class="max-w-full mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3">
                    <div class="flex items-center mb-3">
                        <!-- <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-2">
                            <i data-lucide="user" class="w-4 h-4 text-white"></i>
                        </div> -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Step 1: Customer Information</h3>

                        </div>
                    </div>

                    <!-- Customer Information Section -->
                    <div class="space-y-4">
                        <div class="border-l-4 border-blue-500 pl-3">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Customer Details</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <!-- Customer Selection with Autocomplete -->
                            <div class="md:col-span-2">
                                <label for="customer_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Customer Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           id="customer_name"
                                           x-model="manualCustomerName"
                                           @input="searchCustomers()"
                                           @focus="showCustomerDropdown = true"
                                           @click.away="showCustomerDropdown = false"
                                           required
                                           class="form-input"
                                           placeholder="Type customer name or select from list">

                                    <!-- Dropdown List -->
                                    <div x-show="showCustomerDropdown && filteredCustomers.length > 0"
                                         class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                        <template x-for="customer in filteredCustomers" :key="customer.id">
                                            <div @click="selectCustomerFromDropdown(customer); showCustomerDropdown = false"
                                                 class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="customer.name"></div>
                                                <div class="text-xs text-gray-500" x-text="customer.type + (customer.contact ? ' • ' + customer.contact : '')"></div>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Hidden inputs for form submission -->
                                    <input type="hidden" name="customer_id" :value="selectedCustomer === 'manual' ? '' : selectedCustomer">
                                    <input type="hidden" name="manual_customer_name" x-model="manualCustomerName">
                                </div>
                                @error('customer_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Customer Address -->
                            <div class="md:col-span-1">
                                <label for="manual_customer_address" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Customer Address
                                </label>
                                <input type="text" id="manual_customer_address" name="manual_customer_address"
                                       x-model="manualCustomerAddress"
                                       class="form-input"
                                       placeholder="Enter customer address">
                            </div>

                            <!-- Validity Days -->
                            <div>
                                <label for="validity_days" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Valid Days
                                </label>
                                <input type="number" id="validity_days" name="validity_days"
                                       x-model="validityDays" min="1" max="365" value="30"
                                       class="form-input">
                                <p class="text-xs text-gray-500 mt-1">Default: 30 days</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information Section -->
                    <div class="space-y-4">
                        <div class="border-l-4 border-green-500 pl-3">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Vehicle Details</h4>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Car Model -->
                            <div>
                                <label for="car_model" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Vehicle Model
                                </label>
                                <input type="text" id="car_model" name="car_model"
                                       x-model="carModel"
                                       class="form-input"
                                       placeholder="e.g., Honda Civic, Toyota Corolla">
                            </div>

                            <!-- Car Registration Number -->
                            <div>
                                <label for="car_registration_number" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Registration Number
                                </label>
                                <input type="text" id="car_registration_number" name="car_registration_number"
                                       x-model="carRegistrationNumber"
                                       class="form-input"
                                       placeholder="e.g., ABC-1234, KL-5678">
                            </div>
                        </div>
                    </div>

                    <!-- Step 1 Navigation -->
                    <div class="flex justify-between mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div></div>
                        <button type="button" @click="nextStep()" class="btn btn-primary btn-sm mt-2">
                            Next: Add Items
                            <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Items & Summary -->
        <div x-show="currentStep === 2" class="wizard-step">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
                <!-- Main Items Section -->
                <div class="lg:col-span-3 space-y-3">
                    <!-- Items Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quotation Items</h3>
                            <button type="button" @click="openItemModal" class="btn btn-primary btn-sm focus:outline-none">
                                <i data-lucide="plus" class="w-3 h-3 mr-1"></i>
                                Add Item
                            </button>
                        </div>

                        <!-- Items Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full table-fixed">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="w-1/4 px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                        <th class="w-1/6 px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>
                                        <th class="w-16 px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                        <th class="w-20 px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                        <th class="w-16 px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Disc%</th>
                                        <th class="w-16 px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">VAT%</th>
                                        <th class="w-20 px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                        <th class="w-12 px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Del</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-2 py-2">
                                                <div class="text-xs font-medium text-gray-900 dark:text-white" x-text="item.item_name"></div>
                                                <div class="text-xs text-gray-500" x-text="item.item_no"></div>
                                                <input type="hidden" :name="`items[${index}][item_id]`" :value="item.item_id">
                                            </td>
                                            <td class="px-2 py-2">
                                                <div x-show="item.batch_id">
                                                    <!-- <div class="text-xs text-gray-900 dark:text-white" x-text="item.batch_number"></div> -->
                                                    <div class="text-xs text-gray-500" x-text="item.available_stock"></div>
                                                </div>
                                                <div x-show="!item.batch_id">
                                                    <span class="inline-flex items-center px-1 py-0.5 text-xs font-large text-orange-800 bg-orange-100 rounded dark:bg-orange-900/50 dark:text-orange-300">
                                                        No Stock
                                                    </span>
                                                </div>
                                                <input type="hidden" :name="`items[${index}][batch_id]`" :value="item.batch_id">
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                                       @input="calculateItemTotal(index)" min="1" :max="item.batch_id ? item.available_stock : 999999"
                                                       class="w-full text-center form-input text-xs">
                                            </td>
                                            <td class="px-2 py-2 text-right">
                                                <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                                       @input="calculateItemTotal(index)" step="0.01" min="0"
                                                       class="w-full text-right form-input text-xs">
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <input type="number" :name="`items[${index}][discount]`" x-model="item.discount"
                                                       @input="calculateItemTotal(index)" step="0.01" min="0" max="100"
                                                       class="w-full text-center form-input text-xs">
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <input type="number" :name="`items[${index}][vat]`" x-model="item.vat"
                                                       @input="calculateItemTotal(index)" step="0.01" min="0" max="100"
                                                       class="w-full text-center form-input text-xs">
                                            </td>
                                            <td class="px-2 py-2 text-right">
                                                <span class="font-medium text-gray-900 dark:text-white text-xs" x-text="formatCurrency(item.total)"></span>
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <button type="button" @click="removeItem(index)" class="inline-flex items-center justify-center p-1 text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors duration-200" title="Remove item">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="items.length === 0">
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No items added yet. Click "Add Item" to start.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Step 2 Navigation -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3">
                        <div class="flex justify-between">
                            <button type="button" @click="prevStep()" class="btn btn-secondary btn-sm">
                                <i data-lucide="arrow-left" class="w-3 h-3 mr-1"></i>
                                Previous
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm" :disabled="items.length === 0">
                                <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                Create Quotation
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3 sticky top-6">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Summary</h3>

                        <!-- Customer Info Preview -->
                        <div class="space-y-2 mb-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Customer:</span>
                                <span class="text-xs font-medium text-gray-900 dark:text-white ml-1" x-text="getDisplayCustomerName()"></span>
                            </div>
                            <div x-show="carModel">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Vehicle:</span>
                                <span class="text-xs font-medium text-gray-900 dark:text-white ml-1" x-text="carModel"></span>
                            </div>
                            <div x-show="carRegistrationNumber">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Reg:</span>
                                <span class="text-xs font-medium text-gray-900 dark:text-white ml-1" x-text="carRegistrationNumber"></span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Valid Until:</span>
                                <span class="text-xs font-medium text-gray-900 dark:text-white ml-1" x-text="validUntilDate"></span>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Subtotal:</span>
                                <span class="text-xs font-medium text-gray-900 dark:text-white" x-text="formatCurrency(subtotal)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Discount:</span>
                                <span class="text-xs font-medium text-red-600" x-text="'-' + formatCurrency(totalDiscount)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-600 dark:text-gray-400">VAT:</span>
                                <span class="text-xs font-medium text-gray-900 dark:text-white" x-text="formatCurrency(totalVat)"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Total:</span>
                                <span class="text-sm font-bold text-blue-600" x-text="formatCurrency(grandTotal)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Item Selection Modal -->
    <div x-show="showItemModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-3xl w-full mx-4 max-h-[80vh] overflow-hidden">
            <div class="flex justify-between items-center p-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-medium text-gray-900 dark:text-white">Select Item & Batch</h3>
                <button @click="closeItemModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-3">
                <!-- Item Search -->
                <div class="mb-4">
                    <input type="text" x-model="itemSearch" @input="searchItems"
                           placeholder="Search items..." class="form-input">
                </div>

                <!-- Items List -->
                <div class="grid grid-cols-1 gap-3 max-h-60 overflow-y-auto">
                    <template x-for="item in filteredItems" :key="item.id">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
                             @click="selectItem(item)">
                            <div class="font-medium text-gray-900 dark:text-white" x-text="item.description"></div>
                            <div class="text-sm text-gray-500" x-text="item.item_no"></div>
                            <div class="text-xs mt-1">
                                <span x-show="item.has_stock" class="text-blue-600" x-text="`${item.available_batches.length} batches available`"></span>
                                <span x-show="!item.has_stock" class="text-orange-600 font-medium">No Stock - Can still add to quotation</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Selection Modal -->
    <div x-show="showBatchModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full mx-4 max-h-[70vh] overflow-hidden">
            <div class="flex justify-between items-center p-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-medium text-gray-900 dark:text-white">Select Batch for <span x-text="selectedItem?.description"></span></h3>
                <button @click="closeBatchModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-3">
                <!-- Show available batches or no-stock option -->
                <template x-if="selectedItem?.has_stock">
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <template x-for="batch in selectedItem?.available_batches" :key="batch.id">
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
                                 @click="selectBatch(batch)">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white" x-text="batch.batch_no"></div>
                                        <div class="text-sm text-gray-500">Available: <span x-text="batch.remaining_qty"></span></div>
                                        <div class="text-sm text-gray-500" x-show="batch.expiry_date">
                                            Expires: <span x-text="batch.expiry_date"></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-gray-900 dark:text-white">LKR <span x-text="batch.selling_price"></span></div>
                                        <div class="text-xs text-gray-500">per unit</div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <!-- No stock option -->
                <template x-if="!selectedItem?.has_stock">
                    <div>
                        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg p-4 mb-4">
                            <p class="text-orange-800 dark:text-orange-300 font-medium">No Stock Available</p>
                            <p class="text-sm text-orange-600 dark:text-orange-400 mt-1">This item will be added without a batch. Alternative batches can be selected when converting to sale.</p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Price (LKR)</label>
                                <input type="number" x-model="noStockPrice" min="0" step="0.01" class="form-input" placeholder="Enter price">
                            </div>
                            <button @click="selectNoStockItem()" class="w-full btn btn-primary">
                                Add Without Stock
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function quotationWizard() {
    return {
        currentStep: 1,
        selectedCustomer: '',
        manualCustomerName: '',
        manualCustomerAddress: '',
        validityDays: 30,
        carModel: '',
        carRegistrationNumber: '',
        items: [],
        showItemModal: false,
        showBatchModal: false,
        itemSearch: '',
        availableItems: @json($items),
        selectedItem: null,
        noStockPrice: 0,
        customers: @json($customers),
        filteredCustomers: [],
        showCustomerDropdown: false,

        // Customer methods
        searchCustomers() {
            const searchTerm = this.manualCustomerName.toLowerCase();
            if (searchTerm.length >= 1) {
                this.filteredCustomers = this.customers.filter(customer =>
                    customer.name.toLowerCase().includes(searchTerm) ||
                    customer.type.toLowerCase().includes(searchTerm) ||
                    (customer.contact && customer.contact.toLowerCase().includes(searchTerm))
                ).slice(0, 10); // Limit to 10 results
            } else {
                this.filteredCustomers = this.customers.slice(0, 10); // Show first 10 customers when empty
            }
        },

        selectCustomerFromDropdown(customer) {
            this.selectedCustomer = customer.id;
            this.manualCustomerName = customer.name;
            this.manualCustomerAddress = customer.address || '';
        },

        onCustomerChange() {
            // This method is no longer needed but kept for compatibility
        },

        getDisplayCustomerName() {
            return this.manualCustomerName || 'Not selected';
        },

        // Navigation methods
        nextStep() {
            if (this.currentStep < 2 && this.validateCurrentStep()) {
                // If manual customer name is entered but no customer selected, set a placeholder
                if (!this.selectedCustomer && this.manualCustomerName && this.manualCustomerName.trim() !== '') {
                    this.selectedCustomer = 'manual';
                }
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        validateCurrentStep() {
            if (this.currentStep === 1) {
                // Allow progression if either a customer is selected OR a manual name is entered
                return this.selectedCustomer !== '' || (this.manualCustomerName && this.manualCustomerName.trim() !== '');
            }
            return true;
        },

        // Date calculations
        get validUntilDate() {
            const date = new Date();
            date.setDate(date.getDate() + parseInt(this.validityDays));
            return date.toLocaleDateString();
        },

        // Financial calculations
        get subtotal() {
            return this.items.reduce((sum, item) => {
                const itemTotal = item.quantity * item.unit_price;
                return sum + itemTotal;
            }, 0);
        },

        get totalDiscount() {
            return this.items.reduce((sum, item) => {
                const itemTotal = item.quantity * item.unit_price;
                const discount = itemTotal * (item.discount / 100);
                return sum + discount;
            }, 0);
        },

        get totalVat() {
            return this.items.reduce((sum, item) => {
                const itemTotal = item.quantity * item.unit_price;
                const afterDiscount = itemTotal - (itemTotal * (item.discount / 100));
                const vat = afterDiscount * (item.vat / 100);
                return sum + vat;
            }, 0);
        },

        get grandTotal() {
            return this.items.reduce((sum, item) => sum + item.total, 0);
        },

        // Item management
        openItemModal() {
            this.showItemModal = true;
        },

        closeItemModal() {
            this.showItemModal = false;
            this.itemSearch = '';
        },

        closeBatchModal() {
            this.showBatchModal = false;
            this.selectedItem = null;
            this.noStockPrice = 0;
        },

        searchItems() {
            // This will be automatically reactive due to computed property
        },

        get filteredItems() {
            if (!this.itemSearch || this.itemSearch.length < 2) {
                return this.availableItems;
            }
            return this.availableItems.filter(item => {
                return item.description.toLowerCase().includes(this.itemSearch.toLowerCase()) ||
                       item.item_no.toLowerCase().includes(this.itemSearch.toLowerCase());
            });
        },

        selectItem(item) {
            this.selectedItem = item;
            this.closeItemModal();
            this.showBatchModal = true;
        },

        selectBatch(batch) {
            console.log('Selected batch:', batch);
            this.items.push({
                item_id: this.selectedItem.id,
                item_name: this.selectedItem.description,
                item_no: this.selectedItem.item_no,
                batch_id: batch.id,
                batch_number: batch.batch_no || batch.batch_number || 'N/A',
                available_stock: batch.remaining_qty,
                quantity: 1,
                unit_price: parseFloat(batch.selling_price),
                discount: 0,
                vat: 0,
                total: parseFloat(batch.selling_price)
            });

            this.closeBatchModal();
        },

        selectNoStockItem() {
            if (!this.noStockPrice || this.noStockPrice <= 0) {
                alert('Please enter a valid price');
                return;
            }

            this.items.push({
                item_id: this.selectedItem.id,
                item_name: this.selectedItem.description,
                item_no: this.selectedItem.item_no,
                batch_id: null,
                batch_number: 'No Stock',
                available_stock: 0,
                quantity: 1,
                unit_price: parseFloat(this.noStockPrice),
                discount: 0,
                vat: 0,
                total: parseFloat(this.noStockPrice)
            });

            this.closeBatchModal();
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        calculateItemTotal(index) {
            const item = this.items[index];
            const subtotal = item.quantity * item.unit_price;
            const afterDiscount = subtotal - (subtotal * (item.discount / 100));
            const withVat = afterDiscount + (afterDiscount * (item.vat / 100));
            item.total = withVat;
        },

        formatCurrency(amount) {
            return '' + parseFloat(amount || 0).toFixed(2);
        },

        validateForm(event) {
            if (this.currentStep !== 2) {
                event.preventDefault();
                return false;
            }
            if (this.items.length === 0) {
                event.preventDefault();
                alert('Please add at least one item to the quotation.');
                return false;
            }
            if (!this.validateCurrentStep()) {
                event.preventDefault();
                return false;
            }
        }
    }
}
</script>
@endsection