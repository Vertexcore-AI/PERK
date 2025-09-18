@extends('layouts.app')

@section('title', 'Create Quotation')

@section('content')
<div class="container-fluid px-4" x-data="quotationBuilder()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Quotation</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Create a new quotation for a customer</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Back to Quotations
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('quotations.store') }}" method="POST" @submit="validateForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Customer <span class="text-red-500">*</span>
                            </label>
                            <select id="customer_id" name="customer_id" x-model="selectedCustomer" required class="form-select">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->type }})</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="validity_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Valid for (days)
                            </label>
                            <input type="number" id="validity_days" name="validity_days"
                                   x-model="validityDays" min="1" max="365" value="30" class="form-input">
                            <p class="text-xs text-gray-500 mt-1">Default: 30 days</p>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quotation Items</h3>
                        <button type="button" @click="openItemModal" class="btn btn-primary focus:outline-none">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Add Item
                        </button>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Batch</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Discount %</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">VAT %</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.item_name"></div>
                                            <div class="text-xs text-gray-500" x-text="item.item_no"></div>
                                            <input type="hidden" :name="`items[${index}][item_id]`" :value="item.item_id">
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-sm text-gray-900 dark:text-white" x-text="item.batch_number"></div>
                                            <div class="text-xs text-gray-500">Stock: <span x-text="item.available_stock"></span></div>
                                            <input type="hidden" :name="`items[${index}][batch_id]`" :value="item.batch_id">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                                   @input="calculateItemTotal(index)" min="1" :max="item.available_stock"
                                                   class="w-20 text-center form-input text-sm">
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                                   @input="calculateItemTotal(index)" step="0.01" min="0"
                                                   class="w-24 text-right form-input text-sm">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" :name="`items[${index}][discount]`" x-model="item.discount"
                                                   @input="calculateItemTotal(index)" step="0.01" min="0" max="100"
                                                   class="w-16 text-center form-input text-sm">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <input type="number" :name="`items[${index}][vat]`" x-model="item.vat"
                                                   @input="calculateItemTotal(index)" step="0.01" min="0" max="100"
                                                   class="w-16 text-center form-input text-sm">
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(item.total)"></span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="8" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No items added yet. Click "Add Item" to start.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quotation Summary</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Items:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="items.length"></span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(subtotal)"></span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Total Discount:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(totalDiscount)"></span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Total VAT:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(totalVat)"></span>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700">

                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-900 dark:text-white">Total Estimate:</span>
                            <span class="text-gray-900 dark:text-white" x-text="formatCurrency(grandTotal)"></span>
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Valid until: <span x-text="validUntilDate"></span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="submit" :disabled="items.length === 0 || !selectedCustomer"
                                class="w-full btn btn-primary" :class="{ 'opacity-50 cursor-not-allowed': items.length === 0 || !selectedCustomer }">
                            <i data-lucide="file-plus" class="w-4 h-4 mr-2"></i>
                            Create Quotation
                        </button>

                        <a href="{{ route('quotations.index') }}" class="w-full btn btn-secondary">
                            <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Item Selection Modal -->
    <div x-show="showItemModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full mx-4 max-h-96 overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select Item & Batch</h3>
                <button @click="closeItemModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="p-6">
                <!-- Item Search -->
                <div class="mb-4">
                    <input type="text" x-model="itemSearch" @input="searchItems"
                           placeholder="Search items..." class="form-input">
                </div>

                <!-- Items List -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-64 overflow-y-auto">
                    <template x-for="item in filteredItems" :key="item.id">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
                             @click="selectItem(item)">
                            <div class="font-medium text-gray-900 dark:text-white" x-text="item.description"></div>
                            <div class="text-sm text-gray-500" x-text="item.item_no"></div>
                            <div class="text-xs text-blue-600 mt-1" x-text="`${item.batches.length} batches available`"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Selection Modal -->
    <div x-show="showBatchModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-3xl w-full mx-4 max-h-96 overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select Batch for <span x-text="selectedItem?.description"></span></h3>
                <button @click="closeBatchModal" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="p-6">
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    <template x-for="batch in selectedItem?.batches" :key="batch.id">
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
            </div>
        </div>
    </div>
</div>

<script>
function quotationBuilder() {
    return {
        selectedCustomer: '',
        validityDays: 30,
        items: [],
        showItemModal: false,
        showBatchModal: false,
        itemSearch: '',
        availableItems: @json($items),
        selectedItem: null,

        get validUntilDate() {
            const date = new Date();
            date.setDate(date.getDate() + parseInt(this.validityDays));
            return date.toLocaleDateString();
        },

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

        openItemModal() {
            this.showItemModal = true;
        },

        closeItemModal() {
            this.showItemModal = false;
            this.itemSearch = '';
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

        closeBatchModal() {
            this.showBatchModal = false;
            this.selectedItem = null;
        },

        selectBatch(batch) {
            this.items.push({
                item_id: this.selectedItem.id,
                item_name: this.selectedItem.description,
                item_no: this.selectedItem.item_no,
                batch_id: batch.id,
                batch_number: batch.batch_no,
                available_stock: batch.remaining_qty,
                quantity: 1,
                unit_price: parseFloat(batch.selling_price),
                discount: 0,
                vat: 0,
                total: parseFloat(batch.selling_price)
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
            return 'LKR ' + parseFloat(amount || 0).toFixed(2);
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
        }
    }
}
</script>
@endsection