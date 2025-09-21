@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div x-data="posApp()" class="h-full flex flex-col overflow-hidden">
    <!-- POS Header -->
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 p-4 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Point of Sale</h1>
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    <span x-text="currentDate"></span> | <span x-text="currentTime"></span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Today's Sales Stats -->
                <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg px-4 py-2">
                    <div class="text-sm text-primary-600 dark:text-primary-400">Today's Sales</div>
                    <div class="font-bold text-primary-700 dark:text-primary-300" x-text="formatCurrency(todayStats.sales_amount)"></div>
                </div>
                <div class="bg-secondary-50 dark:bg-secondary-900/20 rounded-lg px-4 py-2">
                    <div class="text-sm text-secondary-600 dark:text-secondary-400">Transactions</div>
                    <div class="font-bold text-secondary-700 dark:text-secondary-300" x-text="todayStats.sales_count"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        <!-- Left Panel - Item Search & Selection -->
        <div class="w-2/3 flex flex-col border-r border-slate-200 dark:border-slate-800">
            <!-- Search Bar -->
            <div class="p-4 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                <div class="relative">
                    <input
                        type="text"
                        x-model="searchQuery"
                        @input.debounce.300ms="searchItems()"
                        placeholder="Search items by code, description, or manufacturer..."
                        class="w-full pl-10 pr-4 py-3 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                    <div x-show="searching" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <div class="animate-spin rounded-full h-5 w-5 border-2 border-primary-500 border-t-transparent"></div>
                    </div>
                </div>
            </div>

            <!-- Search Results -->
            <div class="flex-1 overflow-y-auto p-4">
                <div x-show="searchResults.length === 0 && searchQuery.length >= 2 && !searching" class="text-center py-8">
                    <i data-lucide="package-x" class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4"></i>
                    <p class="text-slate-500 dark:text-slate-400">No items found matching your search.</p>
                </div>

                <div x-show="searchQuery.length < 2 && searchResults.length === 0" class="text-center py-8">
                    <i data-lucide="package-search" class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4"></i>
                    <p class="text-slate-500 dark:text-slate-400">Start typing to search for items...</p>
                </div>

                <div class="grid gap-3">
                    <template x-for="(item, index) in searchResults" :key="index">
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors"
                             @click="selectItem(item)">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="font-semibold text-slate-900 dark:text-white" x-text="item.description"></div>
                                    <div class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                        Code: <span x-text="item.item_no"></span>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-500 mt-1">
                                        <span x-show="item.manufacturer_name">Vendor: <span x-text="item.manufacturer_name"></span></span>
                                    </div>
                                    <div class="mt-2">
                                        <div class="text-xs bg-slate-100 dark:bg-slate-700 rounded px-2 py-1 flex justify-between">
                                            <span>Batch: <span x-text="item.batch_number"></span></span>
                                            <span class="font-medium text-emerald-600 dark:text-emerald-400">
                                                LKR <span x-text="parseFloat(item.selling_price).toFixed(2)"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-sm font-medium"
                                         :class="item.available_stock > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                                        Stock: <span x-text="item.available_stock"></span>
                                    </div>
                                    <div x-show="item.is_serialized" class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                        <i data-lucide="hash" class="w-3 h-3 inline"></i> Serialized
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right Panel - Cart & Customer -->
        <div class="w-1/3 flex flex-col bg-slate-50 dark:bg-slate-900/50">
            <!-- Customer Selection -->
            <div class="p-4 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Customer</label>
                    <div class="relative">
                        <input
                            type="text"
                            x-model="customerSearch"
                            @input.debounce.300ms="searchCustomers()"
                            @focus="showCustomerDropdown = true"
                            placeholder="Search or create customer..."
                            class="w-full pl-3 pr-10 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500">
                        <button @click="showCustomerModal = true" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Customer Dropdown -->
                    <div x-show="showCustomerDropdown && customerResults.length > 0"
                         @click.outside="showCustomerDropdown = false"
                         class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md shadow-lg max-h-40 overflow-y-auto">
                        <template x-for="customer in customerResults" :key="customer.customer_id">
                            <div @click="selectCustomer(customer)"
                                 class="px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer">
                                <div class="font-medium text-slate-900 dark:text-white" x-text="customer.name"></div>
                                <div class="text-sm text-slate-500" x-text="customer.contact"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Selected Customer Display -->
                <div x-show="selectedCustomer" class="bg-slate-100 dark:bg-slate-800 rounded-md p-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="font-medium text-slate-900 dark:text-white" x-text="selectedCustomer?.name"></div>
                            <div class="text-sm text-slate-600 dark:text-slate-400" x-text="selectedCustomer?.contact"></div>
                        </div>
                        <button @click="clearCustomer()" class="text-slate-400 hover:text-red-500">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Load Quotation Button -->
                <div class="mt-3">
                    <button @click="showQuotationModal = true; searchQuotations()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2 inline"></i>
                        Load Quotation
                    </button>
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="flex-1 flex flex-col">
                <div class="p-4 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="font-semibold text-slate-900 dark:text-white">Shopping Cart</h3>
                </div>

                <div class="flex-1 overflow-y-auto p-4">
                    <div x-show="cartItems.length === 0" class="text-center py-8">
                        <i data-lucide="shopping-cart" class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400">Cart is empty</p>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in cartItems" :key="index">
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="font-medium text-slate-900 dark:text-white text-sm" x-text="item.item_no"></div>
                                        <div class="text-xs text-slate-600 dark:text-slate-400" x-text="item.description"></div>
                                    </div>
                                    <button @click="removeFromCart(index)" class="text-slate-400 hover:text-red-500 ml-2">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-2 mb-3">
                                    <button @click="decreaseQuantity(index)" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600">
                                        <i data-lucide="minus" class="w-3 h-3"></i>
                                    </button>
                                    <input type="number" x-model="item.quantity" @change="updateCartItem(index)"
                                           class="w-16 text-center py-1 px-2 border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                                    <button @click="increaseQuantity(index)" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                    </button>
                                    <button @click="showBatchSelector(index)" class="ml-2 px-2 py-1 text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded">
                                        Batch
                                    </button>
                                </div>

                                <!-- Pricing Details -->
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-slate-400">Unit Price:</span>
                                        <span x-text="formatCurrency(item.unit_price)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 dark:text-slate-400">Batch:</span>
                                        <span x-text="item.batch_number || 'Auto'"></span>
                                    </div>
                                </div>

                                <!-- Total Line -->
                                <div class="flex justify-between text-sm font-medium mt-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                                    <span class="text-slate-600 dark:text-slate-400">Total:</span>
                                    <span class="text-slate-900 dark:text-white" x-text="formatCurrency(item.unit_price * item.quantity)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Cart Summary & Actions -->
                <div class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4">
                    <!-- Bill-level Discount and VAT Controls -->
                    <div class="space-y-3 mb-4 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Bill Discount %</label>
                                <input type="number" x-model="billSettings.discount" @change="updateCartSummary()"
                                       min="0" max="100" step="0.01" placeholder="0.00"
                                       class="w-full text-sm py-2 px-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                    <span x-text="hasItemVat ? 'Total VAT' : 'VAT %'"></span>
                                </label>
                                <input x-show="!hasItemVat"
                                       type="number"
                                       x-model="billSettings.vat"
                                       @change="updateCartSummary()"
                                       min="0" max="100" step="0.01" placeholder="0.00"
                                       class="w-full text-sm py-2 px-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                                <input x-show="hasItemVat"
                                       type="text"
                                       :value="formatCurrency(cartSummary.total_vat)"
                                       readonly
                                       class="w-full text-sm py-2 px-3 border border-slate-300 dark:border-slate-600 rounded-md bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Bill Summary -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Subtotal:</span>
                            <span class="text-slate-900 dark:text-white" x-text="formatCurrency(cartSummary.subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm" x-show="billSettings.discount > 0">
                            <span class="text-slate-600 dark:text-slate-400">Discount (<span x-text="billSettings.discount"></span>%):</span>
                            <span class="text-red-600 dark:text-red-400">-<span x-text="formatCurrency(cartSummary.total_discount)"></span></span>
                        </div>
                        <div class="flex justify-between text-sm" x-show="cartSummary.total_vat > 0">
                            <span class="text-slate-600 dark:text-slate-400">VAT:</span>
                            <span class="text-slate-900 dark:text-white">+<span x-text="formatCurrency(cartSummary.total_vat)"></span></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-slate-200 dark:border-slate-700 pt-2">
                            <span class="text-slate-900 dark:text-white">Total:</span>
                            <span class="text-slate-900 dark:text-white" x-text="formatCurrency(cartSummary.total)"></span>
                        </div>
                    </div>

                    <button @click="showPaymentModal = true"
                            :disabled="cartItems.length === 0 || !selectedCustomer"
                            class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Process Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Creation Modal -->
    <div x-show="showCustomerModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         @click.self="showCustomerModal = false">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white">Add New Customer</h3>
                </div>

                <form @submit.prevent="createCustomer()" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Name *</label>
                        <input type="text" x-model="newCustomer.name" required
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact</label>
                        <input type="text" x-model="newCustomer.contact"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Type</label>
                        <select x-model="newCustomer.type"
                                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                            <option value="Retail">Retail</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Wholesale">Wholesale</option>
                            <option value="Corporate">Corporate</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="showCustomerModal = false"
                                class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md">
                            Create Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         @click.self="showPaymentModal = false">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="showPaymentModal = false"></div>

            <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-60">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white">Process Payment</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        Total Amount: <span class="font-medium" x-text="formatCurrency(cartSummary.total)"></span>
                    </p>
                </div>

                <form @submit.prevent="processSale()" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Payment Method</label>
                        <select x-model="payment.method" @change="updatePaymentAmounts()"
                                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mixed">Mixed (Cash + Card)</option>
                        </select>
                    </div>

                    <div x-show="payment.method === 'cash' || payment.method === 'mixed'">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Cash Amount</label>
                        <input type="number" step="0.01" x-model="payment.cash_amount" min="0"
                               :max="payment.method === 'mixed' ? cartSummary.total : null"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                    </div>

                    <div x-show="payment.method === 'card' || payment.method === 'mixed'">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Card Amount</label>
                        <input type="number" step="0.01" x-model="payment.card_amount" min="0"
                               :max="payment.method === 'mixed' ? cartSummary.total : null"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                    </div>

                    <div x-show="payment.method === 'cash' && payment.cash_amount > cartSummary.total"
                         class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-md p-3">
                        <div class="text-sm text-emerald-700 dark:text-emerald-300">
                            Change: <span class="font-medium" x-text="formatCurrency(payment.cash_amount - cartSummary.total)"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Notes (Optional)</label>
                        <textarea x-model="payment.notes" rows="2"
                                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white resize-none"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="showPaymentModal = false"
                                class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                            Cancel
                        </button>
                        <button type="submit" :disabled="processing"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white rounded-md flex items-center">
                            <div x-show="processing" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
                            <span x-text="processing ? 'Processing...' : 'Complete Sale'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Selection Modal -->
    <div x-show="showBatchModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         @click.self="showBatchModal = false">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white">Select Batch</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1" x-show="selectedItemForBatch">
                        Item: <span x-text="selectedItemForBatch?.item_no"></span> - <span x-text="selectedItemForBatch?.description"></span>
                    </p>
                </div>

                <div class="p-6">
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <template x-for="batch in availableBatches" :key="batch.batch_id">
                            <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer"
                                 @click="selectBatch(batch)"
                                 :class="selectedBatch?.batch_id === batch.batch_id ? 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-900/20' : ''">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="font-medium text-slate-900 dark:text-white text-sm" x-text="batch.batch_number"></div>
                                        <div class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                                            Received: <span x-text="batch.received_date"></span>
                                            <span x-show="batch.expiry_date"> | Expires: <span x-text="batch.expiry_date"></span></span>
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-white" x-text="formatCurrency(batch.selling_price)"></div>
                                        <div class="text-xs text-emerald-600 dark:text-emerald-400">
                                            Available: <span x-text="batch.available_quantity"></span>
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            Cost: <span x-text="formatCurrency(batch.unit_cost)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="availableBatches.length === 0" class="text-center py-8">
                            <i data-lucide="package-x" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-4"></i>
                            <p class="text-slate-500 dark:text-slate-400">No batches available for this item</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 mt-4 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" @click="showBatchModal = false"
                                class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                            Cancel
                        </button>
                        <button type="button" @click="applySelectedBatch()" :disabled="!selectedBatch"
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 disabled:bg-slate-300 text-white rounded-md">
                            Apply Batch
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Quotation Modal -->
    <div x-show="showQuotationModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 overflow-y-auto"
         @click.self="showQuotationModal = false">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="showQuotationModal = false"></div>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-10">
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white">Load Quotation</h3>
                        <button @click="showQuotationModal = false" class="text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="mb-4">
                        <input
                            type="text"
                            x-model="quotationSearch"
                            @input.debounce.300ms="searchQuotations()"
                            placeholder="Search quotations by customer name or quote number..."
                            class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500"
                        >
                    </div>

                    <!-- Loading -->
                    <div x-show="loadingQuotations" class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent mx-auto"></div>
                        <p class="text-slate-500 dark:text-slate-400 mt-2">Loading quotations...</p>
                    </div>

                    <!-- Quotations List -->
                    <div x-show="!loadingQuotations" class="max-h-96 overflow-y-auto">
                        <div x-show="availableQuotations.length === 0" class="text-center py-8">
                            <i data-lucide="file-text" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-4"></i>
                            <p class="text-slate-500 dark:text-slate-400">No pending quotations found</p>
                        </div>

                        <div class="space-y-3">
                            <template x-for="quotation in availableQuotations" :key="quotation.quote_id">
                                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors"
                                     @click="selectQuotation(quotation)">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-semibold text-slate-900 dark:text-white" x-text="`Quote #${String(quotation.quote_id).padStart(4, '0')}`"></span>
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400" x-text="quotation.status"></span>
                                            </div>
                                            <div class="text-sm text-slate-600 dark:text-slate-400 mb-1">
                                                Customer: <span class="font-medium" x-text="quotation.customer_name"></span>
                                            </div>
                                            <div class="text-sm text-slate-600 dark:text-slate-400 mb-2">
                                                <span x-text="`${quotation.item_count} item(s)`"></span> •
                                                <span x-text="`Valid until: ${new Date(quotation.valid_until).toLocaleDateString()}`"></span>
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-500">
                                                Created: <span x-text="new Date(quotation.quote_date).toLocaleDateString()"></span>
                                            </div>
                                        </div>
                                        <div class="text-right ml-4">
                                            <div class="font-bold text-lg text-blue-600 dark:text-blue-400" x-text="`LKR ${parseFloat(quotation.total_estimate || 0).toFixed(2)}`"></div>
                                            <div class="text-xs text-slate-500 mt-1">Total Estimate</div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showQuotationModal = false"
                            class="w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div x-show="showAlert"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-60 overflow-y-auto"
         @click.self="closeAlert()">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="closeAlert()"></div>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10">
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div :class="{
                            'bg-blue-100 dark:bg-blue-900/30': alertType === 'info',
                            'bg-green-100 dark:bg-green-900/30': alertType === 'success',
                            'bg-yellow-100 dark:bg-yellow-900/30': alertType === 'warning',
                            'bg-red-100 dark:bg-red-900/30': alertType === 'error'
                        }" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <i :data-lucide="alertType === 'success' ? 'check' : alertType === 'warning' ? 'alert-triangle' : alertType === 'error' ? 'x' : 'info'"
                               :class="{
                                   'text-blue-600 dark:text-blue-400': alertType === 'info',
                                   'text-green-600 dark:text-green-400': alertType === 'success',
                                   'text-yellow-600 dark:text-yellow-400': alertType === 'warning',
                                   'text-red-600 dark:text-red-400': alertType === 'error'
                               }" class="w-6 h-6"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" x-text="alertTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-slate-400" x-html="alertMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="alertAction ? alertAction() : closeAlert()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showConfirm"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-60 overflow-y-auto"
         @click.self="closeConfirm()">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="closeConfirm()"></div>

            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-10">
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-lucide="help-circle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-slate-400" x-html="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="confirmAction ? confirmAction() : closeConfirm()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm"
                            x-text="confirmButtonText">
                    </button>
                    <button type="button" @click="confirmCancelAction ? confirmCancelAction() : closeConfirm()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            x-text="confirmCancelButtonText">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function posApp() {
    return {
        // State
        searchQuery: '',
        searching: false,
        searchResults: [],

        customerSearch: '',
        customerResults: [],
        selectedCustomer: null,
        showCustomerDropdown: false,
        showCustomerModal: false,
        newCustomer: {
            name: '',
            contact: '',
            type: 'Retail'
        },

        cartItems: [],
        cartSummary: {
            subtotal: 0,
            total_discount: 0,
            total_vat: 0,
            total: 0
        },

        // Bill-level settings
        billSettings: {
            discount: 0,
            vat: 0
        },

        showPaymentModal: false,
        payment: {
            method: 'cash',
            cash_amount: 0,
            card_amount: 0,
            notes: ''
        },

        // Quotation loading
        showQuotationModal: false,
        quotationSearch: '',
        availableQuotations: [],
        loadingQuotations: false,

        // Alert system
        showAlert: false,
        alertType: 'info', // 'info', 'success', 'warning', 'error'
        alertTitle: '',
        alertMessage: '',
        alertAction: null,

        // Confirmation dialog
        showConfirm: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmAction: null,
        confirmCancelAction: null,
        confirmButtonText: 'Yes',
        confirmCancelButtonText: 'No',

        // Batch Selection
        showBatchModal: false,
        availableBatches: [],
        selectedBatch: null,
        selectedItemForBatch: null,
        currentCartIndex: null,

        processing: false,
        currentDate: '',
        currentTime: '',
        todayStats: {
            sales_amount: 0,
            sales_count: 0
        },

        // Initialize
        init() {
            this.updateDateTime();
            this.loadDashboardData();
            setInterval(() => this.updateDateTime(), 1000);
            setInterval(() => this.loadDashboardData(), 30000); // Update every 30 seconds

            // Check for load_quote URL parameter
            this.checkLoadQuoteParameter();
        },

        // Check URL for load_quote parameter and auto-load quotation
        checkLoadQuoteParameter() {
            const urlParams = new URLSearchParams(window.location.search);
            const loadQuoteId = urlParams.get('load_quote');

            if (loadQuoteId) {
                console.log('Auto-loading quotation:', loadQuoteId);
                this.autoLoadQuotation(loadQuoteId);
            }
        },

        // Auto-load quotation from URL parameter
        async autoLoadQuotation(quoteId) {
            try {
                const response = await fetch('/api/pos/quotations/load', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ quote_id: quoteId })
                });

                const result = await response.json();

                if (result.success) {
                    // Load customer
                    this.selectedCustomer = result.quotation.customer;

                    // Clear existing cart
                    this.cartItems = [];

                    // Add items to cart
                    for (const item of result.items) {
                        const cartItem = {
                            item_id: item.item_id,
                            batch_id: item.status === 'available' ? item.original_batch_id : null,
                            description: item.item.description,
                            item_code: item.item.item_no,
                            batch_number: item.original_batch ? item.original_batch.batch_no : 'N/A',
                            quantity: item.quantity,
                            unit_price: item.unit_price,
                            discount: item.discount || 0,
                            vat: item.vat || 0,
                            unit_cost: item.original_batch ? item.original_batch.unit_cost : 0,
                            total: this.calculateItemTotal({
                                quantity: item.quantity,
                                unit_price: item.unit_price,
                                discount: item.discount || 0,
                                vat: item.vat || 0
                            }),
                            stock_available: item.status === 'available',
                            alternatives: item.alternatives || []
                        };

                        this.cartItems.push(cartItem);
                    }

                    // Update cart summary
                    this.updateCartSummary();

                    console.log('Quotation loaded successfully');
                    this.showAlertDialog('success', 'Quotation Loaded', 'Quotation items have been loaded into the cart. Review and process the sale.');
                } else {
                    console.error('Failed to load quotation:', result.message);
                    this.showAlertDialog('error', 'Load Failed', result.message || 'Failed to load quotation');
                }
            } catch (error) {
                console.error('Error loading quotation:', error);
                this.showAlertDialog('error', 'Load Error', 'An error occurred while loading the quotation');
            }
        },

        // Time & Date
        updateDateTime() {
            const now = new Date();
            this.currentDate = now.toLocaleDateString();
            this.currentTime = now.toLocaleTimeString();
        },

        // Dashboard Data
        async loadDashboardData() {
            try {
                const response = await fetch('/pos/dashboard-data');
                const data = await response.json();
                this.todayStats = data;
            } catch (error) {
                console.error('Failed to load dashboard data:', error);
            }
        },

        // Item Search
        async searchItems() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }

            this.searching = true;
            try {
                const response = await fetch('/api/pos/search-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ search: this.searchQuery })
                });

                this.searchResults = await response.json();
            } catch (error) {
                console.error('Search failed:', error);
                this.searchResults = [];
            } finally {
                this.searching = false;
            }
        },

        // Customer Management
        async searchCustomers() {
            if (this.customerSearch.length < 2) {
                this.customerResults = [];
                return;
            }

            try {
                const response = await fetch('/api/pos/search-customers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ search: this.customerSearch })
                });

                this.customerResults = await response.json();
            } catch (error) {
                console.error('Customer search failed:', error);
                this.customerResults = [];
            }
        },

        selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.customerSearch = customer.name;
            this.showCustomerDropdown = false;
        },

        clearCustomer() {
            this.selectedCustomer = null;
            this.customerSearch = '';
        },

        async createCustomer() {
            try {
                const response = await fetch('/api/pos/quick-create-customer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newCustomer)
                });

                const result = await response.json();
                if (result.success) {
                    this.selectedCustomer = result.customer;
                    this.customerSearch = result.customer.name;
                    this.showCustomerModal = false;
                    this.newCustomer = { name: '', contact: '', type: 'Retail' };
                } else {
                    this.showAlertDialog('error', 'Customer Creation Failed', result.error);
                }
            } catch (error) {
                console.error('Customer creation failed:', error);
                this.showAlertDialog('error', 'Customer Creation Failed', 'An error occurred while creating the customer. Please try again.');
            }
        },

        // Quotation Management
        async searchQuotations() {
            this.loadingQuotations = true;
            try {
                const response = await fetch('/api/pos/quotations/pending', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const quotations = await response.json();
                this.availableQuotations = quotations.filter(q => {
                    if (!this.quotationSearch) return true;
                    const search = this.quotationSearch.toLowerCase();
                    return q.customer.name.toLowerCase().includes(search) ||
                           String(q.quote_id).padStart(4, '0').includes(search);
                });
            } catch (error) {
                console.error('Failed to load quotations:', error);
                this.availableQuotations = [];
            } finally {
                this.loadingQuotations = false;
            }
        },

        async selectQuotation(quotation) {
            try {
                // Check stock for quotation items
                const response = await fetch(`/quotations/${quotation.quote_id}/check-stock`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.error) {
                    this.showAlertDialog('error', 'Error Loading Quotation', data.error);
                    return;
                }

                // Set customer
                this.selectedCustomer = data.quotation.customer;
                this.customerSearch = this.selectedCustomer.name;

                // Clear existing cart
                this.cartItems = [];

                // Load quotation items
                for (const item of data.items) {
                    if (item.status === 'available') {
                        // Add item directly
                        const cartItem = {
                            item_id: item.item_id,
                            batch_id: item.original_batch_id,
                            description: item.item.description,
                            item_code: item.item.item_code,
                            batch_number: item.original_batch.batch_no,
                            quantity: item.quantity,
                            unit_price: parseFloat(item.unit_price),
                            discount: parseFloat(item.discount || 0),
                            vat: parseFloat(item.vat || 0),
                            available_stock: item.original_batch.remaining_qty,
                            selling_price: parseFloat(item.unit_price)
                        };
                        this.cartItems.push(cartItem);
                    } else {
                        // Item is out of stock, show alternatives
                        const message = `Item "${item.item.description}" from batch "${item.original_batch.batch_no}" is out of stock.`;
                        if (item.alternatives && item.alternatives.length > 0) {
                            const altMessage = `Available alternatives:<br>${item.alternatives.map(alt =>
                                `• ${alt.batch_number} (Stock: ${alt.remaining_quantity}, Price: LKR ${alt.selling_price})`
                            ).join('<br>')}`;

                            this.showConfirmDialog(
                                'Item Out of Stock',
                                `${message}<br><br>${altMessage}<br><br>Would you like to use the first available alternative?`,
                                () => {
                                const alt = item.alternatives[0];
                                const cartItem = {
                                    item_id: item.item_id,
                                    batch_id: alt.batch_id,
                                    description: item.item.description,
                                    item_code: item.item.item_code,
                                    batch_number: alt.batch_number,
                                    quantity: item.quantity,
                                    unit_price: parseFloat(alt.selling_price),
                                    discount: parseFloat(item.discount || 0),
                                    vat: parseFloat(item.vat || 0),
                                    available_stock: alt.remaining_quantity,
                                    selling_price: parseFloat(alt.selling_price)
                                };
                                this.cartItems.push(cartItem);
                                this.closeConfirm();
                            });
                        } else {
                            this.showAlertDialog('warning', 'Item Out of Stock', `${message}<br><br>No alternative batches available for this item.`);
                        }
                    }
                }

                // Update cart summary
                this.updateCartSummary();

                // Close modal
                this.showQuotationModal = false;

                this.showAlertDialog('success', 'Quotation Loaded', `Quotation #${String(quotation.quote_id).padStart(4, '0')} loaded successfully!`);

            } catch (error) {
                console.error('Failed to load quotation:', error);
                this.showAlertDialog('error', 'Error', 'Failed to load quotation');
            }
        },

        // Alert System Methods
        showAlertDialog(type, title, message, action = null) {
            this.alertType = type;
            this.alertTitle = title;
            this.alertMessage = message;
            this.alertAction = action;
            this.showAlert = true;
        },

        closeAlert() {
            this.showAlert = false;
            this.alertAction = null;
        },

        showConfirmDialog(title, message, confirmAction = null, cancelAction = null, confirmText = 'Yes', cancelText = 'No') {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmAction = confirmAction;
            this.confirmCancelAction = cancelAction;
            this.confirmButtonText = confirmText;
            this.confirmCancelButtonText = cancelText;
            this.showConfirm = true;
        },

        closeConfirm() {
            this.showConfirm = false;
            this.confirmAction = null;
            this.confirmCancelAction = null;
            this.confirmButtonText = 'Yes';
            this.confirmCancelButtonText = 'No';
        },

        showInvoiceOptions(saleId) {
            this.showConfirmDialog(
                'Sale Completed Successfully!',
                `Sale ID: ${saleId}<br><br>Choose invoice type to print:`,
                () => {
                    // Print normal invoice
                    window.open('/pos/invoice/' + saleId + '/normal', '_blank');
                    this.closeConfirm();
                    this.resetAfterSale();
                },
                () => {
                    // Print VAT invoice
                    window.open('/pos/invoice/' + saleId + '/vat', '_blank');
                    this.closeConfirm();
                    this.resetAfterSale();
                },
                'Normal Invoice',
                'VAT Invoice'
            );
        },


        // Cart Management
        async selectItem(item) {
            // Check if item already in cart with same batch
            const existingIndex = this.cartItems.findIndex(cartItem =>
                cartItem.item_id === item.item_id && cartItem.batch_id === item.batch_id);

            if (existingIndex !== -1) {
                this.increaseQuantity(existingIndex);
                return;
            }

            // Item already has batch information from search results
            if (!item.batch_id) {
                this.showAlertDialog('warning', 'No Batch Information', 'No batch information available for this item. Please select a different item.');
                return;
            }

            this.cartItems.push({
                item_id: item.item_id,
                item_no: item.item_no,
                description: item.description,
                quantity: 1,
                unit_price: item.selling_price,
                unit_cost: item.unit_cost,
                batch_id: item.batch_id,
                batch_number: item.batch_number,
                available_quantity: item.available_stock
            });

            this.updateCartSummary();
        },

        removeFromCart(index) {
            this.cartItems.splice(index, 1);
            this.updateCartSummary();
        },

        increaseQuantity(index) {
            this.cartItems[index].quantity++;
            this.updateCartSummary();
        },

        decreaseQuantity(index) {
            if (this.cartItems[index].quantity > 1) {
                this.cartItems[index].quantity--;
                this.updateCartSummary();
            }
        },

        updateCartItem(index) {
            if (this.cartItems[index].quantity < 1) {
                this.cartItems[index].quantity = 1;
            }
            this.updateCartSummary();
        },

        // Calculate individual item total with discount and VAT
        calculateItemTotal(item) {
            const baseAmount = item.unit_price * item.quantity;
            const discountAmount = (baseAmount * (item.discount || 0)) / 100;
            const amountAfterDiscount = baseAmount - discountAmount;
            const vatAmount = (amountAfterDiscount * (item.vat || 0)) / 100;
            return amountAfterDiscount + vatAmount;
        },

        // Batch Selection Functions
        async showBatchSelector(cartIndex) {
            this.currentCartIndex = cartIndex;
            this.selectedItemForBatch = this.cartItems[cartIndex];
            this.selectedBatch = null;

            try {
                const response = await fetch(`/api/pos/batches/${this.selectedItemForBatch.item_id}`);
                this.availableBatches = await response.json();
                this.showBatchModal = true;
            } catch (error) {
                console.error('Failed to load batches:', error);
                alert('Failed to load batch information');
            }
        },

        selectBatch(batch) {
            this.selectedBatch = batch;
        },

        applySelectedBatch() {
            if (this.selectedBatch && this.currentCartIndex !== null) {
                const item = this.cartItems[this.currentCartIndex];
                item.unit_price = this.selectedBatch.selling_price;
                item.unit_cost = this.selectedBatch.unit_cost;
                item.batch_id = this.selectedBatch.batch_id;
                item.batch_number = this.selectedBatch.batch_number;
                item.available_quantity = this.selectedBatch.available_quantity;

                this.updateCartSummary();
                this.showBatchModal = false;
                this.selectedBatch = null;
                this.currentCartIndex = null;
            }
        },

        async updateCartSummary() {
            if (this.cartItems.length === 0) {
                this.cartSummary = {
                    subtotal: 0,
                    total_discount: 0,
                    total_vat: 0,
                    total: 0
                };
                return;
            }

            // Calculate subtotal and item-level totals
            let subtotal = 0;
            let totalItemDiscount = 0;
            let totalItemVat = 0;

            this.cartItems.forEach(item => {
                const itemSubtotal = item.unit_price * item.quantity;
                const itemDiscountAmount = itemSubtotal * ((item.discount || 0) / 100);
                const afterItemDiscount = itemSubtotal - itemDiscountAmount;
                const itemVatAmount = afterItemDiscount * ((item.vat || 0) / 100);

                subtotal += itemSubtotal;
                totalItemDiscount += itemDiscountAmount;
                totalItemVat += itemVatAmount;
            });

            // Apply bill-level discount on top of item discounts
            const billDiscountAmount = (subtotal - totalItemDiscount) * (this.billSettings.discount / 100);
            const totalDiscount = totalItemDiscount + billDiscountAmount;

            // Apply bill-level VAT on top of item VAT
            const afterAllDiscounts = subtotal - totalDiscount;
            const billVatAmount = afterAllDiscounts * (this.billSettings.vat / 100);
            const totalVat = totalItemVat + billVatAmount;

            // Calculate final total
            const total = afterAllDiscounts + totalVat;

            this.cartSummary = {
                subtotal: subtotal,
                total_discount: totalDiscount,
                total_vat: totalVat,
                total: total
            };
        },

        // Payment Processing
        updatePaymentAmounts() {
            if (this.payment.method === 'cash') {
                this.payment.cash_amount = this.cartSummary.total;
                this.payment.card_amount = 0;
            } else if (this.payment.method === 'card') {
                this.payment.card_amount = this.cartSummary.total;
                this.payment.cash_amount = 0;
            } else {
                // Mixed - let user enter amounts
                this.payment.cash_amount = 0;
                this.payment.card_amount = 0;
            }
        },

        async processSale() {
            this.processing = true;

            try {
                const saleData = {
                    customer_id: this.selectedCustomer.id,
                    payment_method: this.payment.method,
                    cash_amount: this.payment.cash_amount || 0,
                    card_amount: this.payment.card_amount || 0,
                    notes: this.payment.notes,
                    discount_percentage: this.billSettings.discount,
                    vat_percentage: this.billSettings.vat,
                    items: this.cartItems
                };

                const response = await fetch('/api/pos/process-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(saleData)
                });

                const result = await response.json();

                if (result.success) {
                    // Close payment modal first
                    this.showPaymentModal = false;

                    // Then show invoice options after a brief delay
                    setTimeout(() => {
                        this.showInvoiceOptions(result.sale_id);
                    }, 100);
                } else {
                    this.showAlertDialog('error', 'Sale Failed', result.error);
                }
            } catch (error) {
                console.error('Sale processing failed:', error);
                this.showAlertDialog('error', 'Sale Processing Failed', 'An error occurred while processing the sale. Please try again.');
            } finally {
                this.processing = false;
            }
        },

        resetAfterSale() {
            // Reset cart and forms
            this.cartItems = [];
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.payment = { method: 'cash', cash_amount: 0, card_amount: 0, notes: '' };
            this.showPaymentModal = false;
            this.updateCartSummary();
            this.loadDashboardData();
        },

        // Computed properties
        get hasItemVat() {
            return this.cartItems.some(item => item.vat > 0);
        },

        // Utility
        formatCurrency(amount) {
            return 'LKR ' + parseFloat(amount || 0).toFixed(2);
        }
    }
}
</script>
@endsection