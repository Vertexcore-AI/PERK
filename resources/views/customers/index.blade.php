@extends('layouts.app')

@section('title', 'Customers')

@section('page-title', 'Customer Management')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">Customers</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('customers.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add New Customer
        </a>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="p-1 hover:bg-emerald-200 dark:hover:bg-emerald-800 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    <!-- Customer Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card p-4 bg-gradient-to-br from-pink-500/5 via-purple-500/5 to-pink-500/5 border border-pink-400/20 backdrop-blur-md shadow-xl shadow-pink-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-pink-500/15">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $retailCount ?? 0 }}</h4>
                        <span class="badge badge-success">Retail</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Retail Customers</p>
                </div>
            </div>
        </div>

        <div class="card p-4 bg-gradient-to-br from-pink-500/5 via-purple-500/5 to-pink-500/5 border border-pink-400/20 backdrop-blur-md shadow-xl shadow-pink-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-pink-500/15">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shield" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $insuranceCount ?? 0 }}</h4>
                        <span class="badge badge-info">Insurance</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Insurance Customers</p>
                </div>
            </div>
        </div>

        <div class="card p-4 bg-gradient-to-br from-pink-500/5 via-purple-500/5 to-pink-500/5 border border-pink-400/20 backdrop-blur-md shadow-xl shadow-pink-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-pink-500/15">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="building" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $wholesaleCount ?? 0 }}</h4>
                        <span class="badge badge-warning">Wholesale</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Wholesale Customers</p>
                </div>
            </div>
        </div>

        <div class="card p-4 bg-gradient-to-br from-pink-500/5 via-purple-500/5 to-pink-500/5 border border-pink-400/20 backdrop-blur-md shadow-xl shadow-pink-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-pink-500/15">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($totalRevenue ?? 0, 0) }}</h4>
                        <span class="badge badge-success">Revenue</span>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table Card -->
    <div class="card animate-in" style="animation-delay: 0.1s">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Customers</h3>

                <!-- Search and Filter -->
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="relative">
                        <select id="customer-filter" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="all">All Types</option>
                            <option value="retail">Retail</option>
                            <option value="insurance">Insurance</option>
                            <option value="wholesale">Wholesale</option>
                        </select>
                    </div>
                    <div class="relative">
                        <input type="text"
                            id="vehicle-type-filter"
                            placeholder="Vehicle type..."
                            class="px-3 py-2 w-40 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    <div class="relative">
                        <input type="text"
                            id="vehicle-model-filter"
                            placeholder="Vehicle model..."
                            class="px-3 py-2 w-40 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                        <input type="text"
                            id="customer-search"
                            placeholder="Search customers..."
                            class="pl-10 pr-4 py-2 w-64 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-modern w-full">
                <thead>
                    <tr>
                        <th scope="col">Customer</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Type</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Total Orders</th>
                        <th scope="col">Total Spent</th>
                        <th scope="col">Last Order</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($customers as $customer)
                        <tr class="group" data-type="{{ $customer->type }}" data-vehicle-type="{{ $customer->vehicle_type }}" data-vehicle-model="{{ $customer->vehicle_model }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    
                                    <div>
                                        <span class="font-medium text-slate-900 dark:text-white">{{ $customer->name }}</span>
                                        @if($customer->company)
                                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                {{ $customer->company }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if($customer->contact)
                                    <span class="text-slate-900 dark:text-slate-100">{{ $customer->contact }}</span>
                                    @endif
                                    @if($customer->email)
                                        <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $customer->email }}</span>
                                    @endif
                                    @if(!$customer->contact && !$customer->email)
                                        <span class="text-slate-400 dark:text-slate-500 italic">No contact info</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($customer->type === 'retail')
                                    <span class="badge badge-success">Retail</span>
                                @elseif($customer->type === 'insurance')
                                    <span class="badge badge-info">Insurance</span>
                                @elseif($customer->type === 'wholesale')
                                    <span class="badge badge-warning">Wholesale</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($customer->type) }}</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    @if($customer->vehicle_type || $customer->vehicle_model)
                                        @if($customer->vehicle_type)
                                            <span class="text-slate-900 dark:text-slate-100">{{ $customer->vehicle_type }}</span>
                                        @endif
                                        @if($customer->vehicle_model)
                                            <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $customer->vehicle_model }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 italic">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-900 dark:text-slate-100 font-medium">{{ $customer->orders_count ?? 0 }}</span>
                                    <span class="text-slate-500 dark:text-slate-400">orders</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-slate-900 dark:text-slate-100 font-medium">
                                    ${{ number_format($customer->total_spent ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
                                @if($customer->last_order_date)
                                    <span class="text-slate-600 dark:text-slate-400">{{ $customer->last_order_date->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">No orders</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('customers.show', $customer) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="View Customer">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="Edit Customer">
                                        <i data-lucide="edit" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <button type="button"
                                        class="btn-icon group/btn"
                                        data-tooltip="Create Sale"
                                        onclick="window.location.href='/pos?customer={{ $customer->id }}'">
                                        <i data-lucide="shopping-cart" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-green-600 dark:group-hover/btn:text-green-400"></i>
                                    </button>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn-icon group/btn"
                                            data-tooltip="Delete Customer"
                                            onclick="return confirm('Are you sure you want to delete this customer?')">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-rose-600 dark:group-hover/btn:text-rose-400"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                        <i data-lucide="users" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No customers found</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Get started by adding your first customer</p>
                                    <a href="{{ route('customers.create') }}" class="btn-primary">
                                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                                        Add New Customer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600 dark:text-slate-400">
                        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
                    </span>
                    <div class="flex items-center gap-2">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('customer-search');
        const filterSelect = document.getElementById('customer-filter');
        const vehicleTypeInput = document.getElementById('vehicle-type-filter');
        const vehicleModelInput = document.getElementById('vehicle-model-filter');
        const tbody = document.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const filterType = filterSelect.value;
            const vehicleTypeFilter = vehicleTypeInput.value.toLowerCase();
            const vehicleModelFilter = vehicleModelInput.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const type = row.dataset.type;
                const vehicleType = (row.dataset.vehicleType || '').toLowerCase();
                const vehicleModel = (row.dataset.vehicleModel || '').toLowerCase();

                const matchesSearch = text.includes(searchTerm);
                const matchesFilter = filterType === 'all' || type === filterType;
                const matchesVehicleType = !vehicleTypeFilter || vehicleType.includes(vehicleTypeFilter);
                const matchesVehicleModel = !vehicleModelFilter || vehicleModel.includes(vehicleModelFilter);

                row.style.display = (matchesSearch && matchesFilter && matchesVehicleType && matchesVehicleModel) ? '' : 'none';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('keyup', filterTable);
        }

        if (filterSelect) {
            filterSelect.addEventListener('change', filterTable);
        }

        if (vehicleTypeInput) {
            vehicleTypeInput.addEventListener('keyup', filterTable);
        }

        if (vehicleModelInput) {
            vehicleModelInput.addEventListener('keyup', filterTable);
        }
    });
</script>
@endpush