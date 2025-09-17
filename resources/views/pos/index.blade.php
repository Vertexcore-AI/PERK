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
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg px-4 py-2">
                    <div class="text-sm text-emerald-600 dark:text-emerald-400">Transactions</div>
                    <div class="font-bold text-emerald-700 dark:text-emerald-300" x-text="todayStats.sales_count"></div>
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
                                <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">VAT %</label>
                                <input type="number" x-model="billSettings.vat" @change="updateCartSummary()"
                                       min="0" max="100" step="0.01" placeholder="0.00"
                                       class="w-full text-sm py-2 px-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
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
                        <div class="flex justify-between text-sm" x-show="billSettings.vat > 0">
                            <span class="text-slate-600 dark:text-slate-400">VAT (<span x-text="billSettings.vat"></span>%):</span>
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
                    alert('Failed to create customer: ' + result.error);
                }
            } catch (error) {
                console.error('Customer creation failed:', error);
                alert('Failed to create customer');
            }
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
                alert('No batch information available for this item');
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

            // Calculate subtotal from all cart items
            const subtotal = this.cartItems.reduce((sum, item) => {
                return sum + (item.unit_price * item.quantity);
            }, 0);

            // Calculate discount amount
            const discountAmount = subtotal * (this.billSettings.discount / 100);
            const afterDiscount = subtotal - discountAmount;

            // Calculate VAT amount
            const vatAmount = afterDiscount * (this.billSettings.vat / 100);

            // Calculate final total
            const total = afterDiscount + vatAmount;

            this.cartSummary = {
                subtotal: subtotal,
                total_discount: discountAmount,
                total_vat: vatAmount,
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
                    // Success - show receipt and reset
                    alert(`Sale completed successfully! Sale ID: ${result.sale_id}`);

                    // Reset cart and forms
                    this.cartItems = [];
                    this.selectedCustomer = null;
                    this.customerSearch = '';
                    this.payment = { method: 'cash', cash_amount: 0, card_amount: 0, notes: '' };
                    this.showPaymentModal = false;
                    this.updateCartSummary();
                    this.loadDashboardData();

                    // Optionally open receipt in new window
                    if (confirm('Sale completed! Do you want to print the receipt?')) {
                        window.open(`/pos/print/${result.sale_id}`, '_blank');
                    }
                } else {
                    alert('Sale failed: ' + result.error);
                }
            } catch (error) {
                console.error('Sale processing failed:', error);
                alert('Sale processing failed');
            } finally {
                this.processing = false;
            }
        },

        // Utility
        formatCurrency(amount) {
            return 'LKR ' + parseFloat(amount || 0).toFixed(2);
        }
    }
}
</script>
@endsection