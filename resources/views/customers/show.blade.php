@extends('layouts.app')

@section('title', 'Customer Details - ' . $customer->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('customers.index') }}"
               class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $customer->name }}</h1>
                <p class="text-slate-600 dark:text-slate-400">Customer Details</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                {{ $customer->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' }}">
                {{ $customer->is_active ? 'Active' : 'Inactive' }}
            </span>
            <a href="{{ route('customers.edit', $customer) }}"
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                Edit Customer
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Customer Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Name</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Type</label>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $customer->type == 'Retail' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                            {{ $customer->type == 'Insurance' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400' : '' }}
                            {{ $customer->type == 'Wholesale' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400' : '' }}">
                            {{ $customer->type }}
                        </span>
                    </div>

                    @if($customer->company)
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Company</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->company }}</p>
                    </div>
                    @endif

                    @if($customer->contact)
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Contact</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->contact }}</p>
                    </div>
                    @endif

                    @if($customer->email)
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Email</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->email }}</p>
                    </div>
                    @endif

                    @if($customer->vat_number)
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">VAT Number</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->vat_number }}</p>
                    </div>
                    @endif

                    @if($customer->vehicle_type)
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Vehicle Type</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->vehicle_type }}</p>
                    </div>
                    @endif

                    @if($customer->vehicle_model)
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Vehicle Model</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->vehicle_model }}</p>
                    </div>
                    @endif

                    @if($customer->address)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Address</label>
                        <p class="mt-1 text-slate-900 dark:text-white">{{ $customer->address }}</p>
                    </div>
                    @endif

                    @if($customer->city || $customer->state || $customer->postal_code)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Location</label>
                        <p class="mt-1 text-slate-900 dark:text-white">
                            {{ $customer->city ? $customer->city . ', ' : '' }}
                            {{ $customer->state ? $customer->state . ' ' : '' }}
                            {{ $customer->postal_code }}
                        </p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-700">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Created: {{ $customer->created_at->format('M d, Y H:i') }}
                    </div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Updated: {{ $customer->updated_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Recent Sales -->
            @if($customer->sales && $customer->sales->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 mt-6">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Recent Sales</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Sale ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            @foreach($customer->sales()->latest()->take(5)->get() as $sale)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-primary-600 hover:text-primary-500">
                                        #{{ $sale->sale_id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                    {{ $sale->sale_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                    {{ $sale->saleItems->count() }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">
                                    LKR {{ number_format($sale->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $sale->status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400' : '' }}
                                        {{ $sale->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($customer->sales->count() > 5)
                <div class="px-6 py-3 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700">
                    <a href="{{ route('sales.index', ['customer_id' => $customer->id]) }}"
                       class="text-sm text-primary-600 hover:text-primary-500">
                        View all {{ $customer->sales->count() }} sales →
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Statistics</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Total Purchases</label>
                        <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                            {{ $customer->total_purchases ?? 0 }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Total Spent</label>
                        <p class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                            LKR {{ number_format($customer->total_spent ?? 0, 2) }}
                        </p>
                    </div>

                    @if($customer->sales && $customer->sales->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Average Purchase</label>
                        <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">
                            LKR {{ number_format($customer->total_spent / $customer->total_purchases, 2) }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Last Purchase</label>
                        <p class="mt-1 text-sm text-slate-900 dark:text-white">
                            {{ $customer->sales()->latest()->first()->sale_date->format('M d, Y') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('pos.index') }}?customer_id={{ $customer->id }}"
                       class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-4 h-4 mr-2"></i>
                        New Sale
                    </a>

                    <a href="{{ route('customers.edit', $customer) }}"
                       class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                        Edit Customer
                    </a>

                    @if($customer->sales->count() === 0)
                    <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                          onsubmit="return confirm('Are you sure you want to delete this customer?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Delete Customer
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection