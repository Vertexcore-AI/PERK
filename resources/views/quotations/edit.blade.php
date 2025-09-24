@extends('layouts.app')

@section('title', 'Edit Quotation')

@section('content')
<div class="container-fluid px-2" x-data="quotationBuilder({{ json_encode($quotation) }})">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit Quotation #{{ str_pad($quotation->quote_id, 4, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Update quotation details</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('quotations.show', $quotation->quote_id) }}" class="btn btn-secondary">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('quotations.update', $quotation->quote_id) }}" method="POST" @submit="validateForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-3">
            <!-- Main Form -->
            <div class="lg:col-span-3 space-y-3">
                <!-- Customer Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Customer Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Customer <span class="text-red-500">*</span>
                            </label>
                            <select id="customer_id" name="customer_id" x-model="selectedCustomer" required class="form-select">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $customer->id == $quotation->customer_id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="manual_customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Custom Name
                            </label>
                            <input type="text" id="manual_customer_name" name="manual_customer_name"
                                   value="{{ old('manual_customer_name', $quotation->manual_customer_name) }}"
                                   class="form-input"
                                   placeholder="Custom name">
                        </div>

                        <div>
                            <label for="validity_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Valid Days
                            </label>
                            <input
                                type="number"
                                id="validity_days"
                                name="validity_days"
                                x-model="validityDays"
                                min="1"
                                max="365"
                                value="{{ \Carbon\Carbon::parse($quotation->valid_until)->diffInDays(\Carbon\Carbon::parse($quotation->quote_date)) }}"
                                class="form-input"
                            >
                            @error('validity_days')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="manual_customer_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Custom Address
                            </label>
                            <textarea id="manual_customer_address" name="manual_customer_address"
                                      rows="2"
                                      class="form-textarea"
                                      placeholder="Custom address">{{ old('manual_customer_address', $quotation->manual_customer_address) }}</textarea>
                        </div>

                        <div>
                            <label for="car_model" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Car Model
                            </label>
                            <input type="text" id="car_model" name="car_model"
                                   value="{{ old('car_model', $quotation->car_model) }}"
                                   class="form-input"
                                   placeholder="Honda Civic">
                        </div>

                        <div>
                            <label for="car_registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Registration
                            </label>
                            <input type="text" id="car_registration_number" name="car_registration_number"
                                   value="{{ old('car_registration_number', $quotation->car_registration_number) }}"
                                   class="form-input"
                                   placeholder="ABC-1234">
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white">Quotation Items</h3>
                        <button type="button" @click="showItemModal = true" class="btn btn-primary focus:outline-none">
                            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                            Add Item
                        </button>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">Item</th>
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">Batch</th>
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">Qty</th>
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">Price</th>
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">Disc%</th>
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">VAT%</th>
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">Total</th>
                                    <th class="text-left py-2 px-2 text-xs font-medium text-gray-700 dark:text-gray-300">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="items.length === 0">
                                    <tr>
                                        <td colspan="8" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                            No items added yet. Click "Add Item" to start.
                                        </td>
                                    </tr>
                                </template>

                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2 px-2">
                                            <div class="font-medium text-gray-900 dark:text-white text-sm" x-text="item.item_description"></div>
                                            <div class="text-xs text-gray-500" x-text="item.item_code"></div>
                                        </td>
                                        <td class="py-2 px-2">
                                            <div class="text-sm text-gray-900 dark:text-white" x-text="item.batch_number"></div>
                                            <div class="text-xs text-gray-500">Stock: <span x-text="item.available_stock"></span></div>
                                        </td>
                                        <td class="py-2 px-2">
                                            <input
                                                type="number"
                                                x-model.number="item.quantity"
                                                :name="`items[${index}][quantity]`"
                                                min="1"
                                                :max="item.available_stock"
                                                class="w-16 px-1 py-1 border border-gray-300 dark:border-gray-600 rounded text-xs"
                                                @input="updateItemTotal(item)"
                                            >
                                            <input type="hidden" :name="`items[${index}][item_id]`" :value="item.item_id">
                                            <input type="hidden" :name="`items[${index}][batch_id]`" :value="item.batch_id">
                                        </td>
                                        <td class="py-2 px-2">
                                            <input
                                                type="number"
                                                x-model.number="item.unit_price"
                                                :name="`items[${index}][unit_price]`"
                                                min="0"
                                                step="0.01"
                                                class="w-20 px-1 py-1 border border-gray-300 dark:border-gray-600 rounded text-xs"
                                                @input="updateItemTotal(item)"
                                            >
                                        </td>
                                        <td class="py-2 px-2">
                                            <input
                                                type="number"
                                                x-model.number="item.discount"
                                                :name="`items[${index}][discount]`"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                class="w-16 px-1 py-1 border border-gray-300 dark:border-gray-600 rounded text-xs"
                                                @input="updateItemTotal(item)"
                                            >
                                        </td>
                                        <td class="py-2 px-2">
                                            <input
                                                type="number"
                                                x-model.number="item.vat"
                                                :name="`items[${index}][vat]`"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                class="w-16 px-1 py-1 border border-gray-300 dark:border-gray-600 rounded text-xs"
                                                @input="updateItemTotal(item)"
                                            >
                                        </td>
                                        <td class="py-2 px-2 font-medium text-sm" x-text="`${item.total.toFixed(2)}`"></td>
                                        <td class="py-2 px-2">
                                            <button type="button" @click="removeItem(index)" class="inline-flex items-center justify-center px-1 py-1 text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors duration-200" title="Remove item">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-3 sticky top-4">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-3">Summary</h3>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Items:</span>
                            <span class="font-medium" x-text="items.length"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                            <span class="font-medium" x-text="`${subtotal.toFixed(2)}`"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                            <span class="font-medium" x-text="`${totalDiscount.toFixed(2)}`"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">VAT:</span>
                            <span class="font-medium" x-text="`${totalVat.toFixed(2)}`"></span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-base font-medium">
                            <span class="text-gray-900 dark:text-white">Total:</span>
                            <span class="text-blue-600 dark:text-blue-400" x-text="`${totalEstimate.toFixed(2)}`"></span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Valid: <span x-text="validUntilDate"></span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 mt-4">
                        <button
                            type="submit"
                            :disabled="items.length === 0 || !selectedCustomer"
                            :class="(items.length === 0 || !selectedCustomer) ? 'btn btn-primary opacity-50 cursor-not-allowed' : 'btn btn-primary'"
                        >
                            <i data-lucide="save" class="w-4 h-4 mr-1"></i>
                            Update
                        </button>
                        <a href="{{ route('quotations.show', $quotation->quote_id) }}" class="btn btn-secondary">
                            <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Item Selection Modal -->
    <div x-show="showItemModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="showItemModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[85vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select Item & Batch</h3>
                <button @click="showItemModal = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[70vh]" x-show="!selectedItem">
                <!-- Search -->
                <div class="mb-4">
                    <input
                        type="text"
                        x-model="itemSearch"
                        placeholder="Search items..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg"
                    >
                </div>

                <!-- Items List -->
                <div class="space-y-2">
                    <template x-for="item in filteredItems" :key="item.id">
                        <div @click="selectItem(item)" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <div class="font-medium text-gray-900 dark:text-white" x-text="item.description"></div>
                            <div class="text-sm text-gray-500" x-text="item.item_code"></div>
                            <div class="text-xs text-blue-600 mt-1" x-text="`${item.batches_count} batches available`"></div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Batch Selection -->
            <div class="p-6 overflow-y-auto max-h-[70vh]" x-show="selectedItem && !selectedBatch">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4" x-text="`Select Batch for ${selectedItem?.description}`"></h4>

                <div class="space-y-3">
                    <template x-for="batch in selectedItem?.batches || []" :key="batch.id">
                        <div @click="selectBatch(batch)" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white" x-text="batch.batch_no"></div>
                                    <div class="text-sm text-gray-500">Available: <span x-text="batch.remaining_qty"></span></div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-blue-600" x-text="`LKR ${parseFloat(batch.selling_price).toFixed(2)}`"></div>
                                    <div class="text-xs text-gray-500">per unit</div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function quotationBuilder(existingQuotation = null) {
    return {
        showItemModal: false,
        selectedCustomer: existingQuotation?.customer_id || '',
        validityDays: existingQuotation ? Math.round((new Date(existingQuotation.valid_until) - new Date(existingQuotation.quote_date)) / (1000 * 60 * 60 * 24)) : 30,
        items: existingQuotation ? existingQuotation.quote_items.map(item => ({
            item_id: item.item_id,
            batch_id: item.batch_id,
            item_description: item.item.description,
            item_code: item.item.item_code,
            batch_number: item.batch ? item.batch.batch_no : 'No Stock',
            available_stock: item.batch ? item.batch.remaining_qty : 0,
            quantity: item.quantity,
            unit_price: parseFloat(item.unit_price),
            discount: parseFloat(item.discount || 0),
            vat: parseFloat(item.vat || 0),
            total: parseFloat(item.total)
        })) : [],
        itemSearch: '',
        selectedItem: null,
        selectedBatch: null,
        availableItems: @json($items),

        get filteredItems() {
            if (!this.itemSearch) return this.availableItems;
            return this.availableItems.filter(item =>
                item.description.toLowerCase().includes(this.itemSearch.toLowerCase()) ||
                item.item_code.toLowerCase().includes(this.itemSearch.toLowerCase())
            );
        },

        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        },

        get totalDiscount() {
            return this.items.reduce((sum, item) => {
                const itemSubtotal = item.quantity * item.unit_price;
                return sum + (itemSubtotal * (item.discount / 100));
            }, 0);
        },

        get totalVat() {
            return this.items.reduce((sum, item) => {
                const itemSubtotal = item.quantity * item.unit_price;
                const afterDiscount = itemSubtotal - (itemSubtotal * (item.discount / 100));
                return sum + (afterDiscount * (item.vat / 100));
            }, 0);
        },

        get totalEstimate() {
            return this.items.reduce((sum, item) => sum + item.total, 0);
        },

        get validUntilDate() {
            const date = new Date();
            date.setDate(date.getDate() + parseInt(this.validityDays));
            return date.toLocaleDateString();
        },

        selectItem(item) {
            this.selectedItem = item;
            this.selectedBatch = null;
        },

        selectBatch(batch) {
            this.selectedBatch = batch;
            this.addItemToQuotation();
        },

        addItemToQuotation() {
            if (!this.selectedItem || !this.selectedBatch) return;

            const newItem = {
                item_id: this.selectedItem.id,
                batch_id: this.selectedBatch.id,
                item_description: this.selectedItem.description,
                item_code: this.selectedItem.item_code,
                batch_number: this.selectedBatch.batch_no,
                available_stock: this.selectedBatch.remaining_qty,
                quantity: 1,
                unit_price: parseFloat(this.selectedBatch.selling_price),
                discount: 0,
                vat: 0,
                total: parseFloat(this.selectedBatch.selling_price)
            };

            this.items.push(newItem);
            this.resetModal();
        },

        updateItemTotal(item) {
            const subtotal = item.quantity * item.unit_price;
            const afterDiscount = subtotal - (subtotal * (item.discount / 100));
            const withVat = afterDiscount + (afterDiscount * (item.vat / 100));
            item.total = withVat;
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        resetModal() {
            this.showItemModal = false;
            this.selectedItem = null;
            this.selectedBatch = null;
            this.itemSearch = '';
        },

        validateForm(event) {
            if (this.items.length === 0) {
                event.preventDefault();
                alert('Please add at least one item to the quotation.');
                return false;
            }
            if (!this.selectedCustomer) {
                event.preventDefault();
                alert('Please select a customer.');
                return false;
            }
            return true;
        }
    }
}
</script>
@endsection