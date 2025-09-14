@extends('layouts.app')

@section('title', 'Edit Customer')

@section('page-title', 'Edit Customer')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('customers.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Customers</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('customers.show', $customer) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">{{ $customer->name }}</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">Edit</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('customers.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Customers
        </a>
    </div>
@endsection

@section('content')
    @if($errors->any())
        <div class="alert-danger mb-6" x-data="{ show: true }" x-show="show">
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
                <button @click="show = false" class="flex-shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mr-4
                    @if($customer->type === 'retail') bg-gradient-to-br from-green-500 to-green-600
                    @elseif($customer->type === 'insurance') bg-gradient-to-br from-blue-500 to-blue-600
                    @elseif($customer->type === 'wholesale') bg-gradient-to-br from-purple-500 to-purple-600
                    @else bg-gradient-to-br from-gray-500 to-gray-600
                    @endif">
                    <i data-lucide="user" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Edit Customer Information</h5>
                    <p class="text-slate-600 dark:text-slate-400">Update customer details and contact information</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Customer Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <input type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $customer->name) }}"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-rose-500 @enderror"
                                placeholder="Customer name"
                                required>
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Customer Type <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="tag" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <select name="type"
                                id="type"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('type') border-rose-500 @enderror"
                                required>
                                <option value="">Select customer type</option>
                                <option value="retail" {{ old('type', $customer->type) == 'retail' ? 'selected' : '' }}>Retail Customer</option>
                                <option value="insurance" {{ old('type', $customer->type) == 'insurance' ? 'selected' : '' }}>Insurance Company</option>
                                <option value="wholesale" {{ old('type', $customer->type) == 'wholesale' ? 'selected' : '' }}>Wholesale Buyer</option>
                            </select>
                        </div>
                        @error('type')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="company" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Company/Organization
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="building" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <input type="text"
                            name="company"
                            id="company"
                            value="{{ old('company', $customer->company) }}"
                            class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('company') border-rose-500 @enderror"
                            placeholder="Company or organization name">
                    </div>
                    @error('company')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Phone Number
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="phone" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <input type="tel"
                                name="phone"
                                id="phone"
                                value="{{ old('phone', $customer->phone) }}"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('phone') border-rose-500 @enderror"
                                placeholder="Phone number">
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <input type="email"
                                name="email"
                                id="email"
                                value="{{ old('email', $customer->email) }}"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-rose-500 @enderror"
                                placeholder="email@example.com">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Address
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <i data-lucide="map-pin" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        <textarea name="address"
                            id="address"
                            rows="3"
                            class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('address') border-rose-500 @enderror"
                            placeholder="Customer address">{{ old('address', $customer->address) }}</textarea>
                    </div>
                    @error('address')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary-600 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Active Customer</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Customer
                    </button>
                    <a href="{{ route('customers.show', $customer) }}" class="btn-secondary">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Cancel
                    </a>
                </div>
            </form>
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
    });
</script>
@endpush