@extends('layouts.app')

@section('title', 'Add Customer')

@section('page-title', 'Add New Customer')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('customers.index') }}">Customers</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Add New Customer</span>
    </div>
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
        <div x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-danger mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-medium text-rose-900 dark:text-rose-100 mb-2">Please fix the following errors:</h4>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li class="text-rose-700 dark:text-rose-300">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="p-1 hover:bg-rose-200 dark:hover:bg-rose-800 rounded-lg transition-colors flex-shrink-0">
                    <i data-lucide="x" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="max-w-2xl mx-0">
        <!-- Form Card -->
        <div class="card animate-in bg-gradient-to-br from-pink-500/5 via-purple-500/5 to-pink-500/5 border border-pink-400/20 backdrop-blur-md shadow-xl shadow-pink-500/10 transition-all duration-300 hover:shadow-2xl hover:shadow-pink-500/15" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/25">
                        <i data-lucide="users" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Customer Information</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Add a new customer to your system</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('customers.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Customer Name -->
                    <div class="animate-in" style="animation-delay: 0.2s">
                        <x-forms.input
                            label="Customer Name"
                            name="name"
                            :value="old('name')"
                            placeholder="Enter customer name"
                            required
                            icon="user"
                            :error="$errors->first('name')"
                        />
                    </div>

                    <!-- Customer Type -->
                    <div class="animate-in" style="animation-delay: 0.3s">
                        <label for="type" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Customer Type <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i data-lucide="tag" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <select
                                id="type"
                                name="type"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent hover:border-slate-300 dark:hover:border-slate-600 px-4 py-2.5 pl-11"
                                required>
                                <option value="">Select customer type</option>
                                <option value="Retail" {{ old('type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Insurance" {{ old('type') == 'Insurance' ? 'selected' : '' }}>Insurance</option>
                                <option value="Wholesale" {{ old('type') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                            </select>
                        </div>
                        @if($errors->has('type'))
                            <p class="text-sm text-rose-500 dark:text-rose-400 flex items-center gap-1 mt-2">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $errors->first('type') }}
                            </p>
                        @endif
                    </div>

                    <!-- Contact Information -->
                    <div class="animate-in" style="animation-delay: 0.4s">
                        <x-forms.input
                            label="Contact Information"
                            name="contact"
                            :value="old('contact')"
                            placeholder="Phone, email, or contact details"
                            icon="phone"
                            :error="$errors->first('contact')"
                            help="Optional: Phone number, email, or other contact details"
                        />
                    </div>

                    <!-- VAT Number -->
                    <div class="animate-in" style="animation-delay: 0.5s">
                        <x-forms.input
                            label="VAT Number"
                            name="vat_number"
                            :value="old('vat_number')"
                            placeholder="Enter VAT registration number"
                            icon="file-text"
                            :error="$errors->first('vat_number')"
                            help="Optional: VAT registration number for tax invoices"
                        />
                    </div>

                    <!-- Vehicle Type -->
                    <div class="animate-in" style="animation-delay: 0.55s">
                        <x-forms.input
                            label="Vehicle Type"
                            name="vehicle_type"
                            :value="old('vehicle_type')"
                            placeholder="e.g. Car, SUV, Truck"
                            icon="truck"
                            :error="$errors->first('vehicle_type')"
                            help="Optional: Type of vehicle"
                        />
                    </div>

                    <!-- Vehicle Model -->
                    <div class="animate-in" style="animation-delay: 0.58s">
                        <x-forms.input
                            label="Vehicle Model"
                            name="vehicle_model"
                            :value="old('vehicle_model')"
                            placeholder="e.g. Toyota Camry, Honda Civic"
                            icon="car"
                            :error="$errors->first('vehicle_model')"
                            help="Optional: Vehicle make and model"
                        />
                    </div>

                    <!-- Address -->
                    <div class="animate-in" style="animation-delay: 0.6s">
                        <x-forms.textarea
                            label="Address"
                            name="address"
                            :value="old('address')"
                            placeholder="Enter customer address"
                            rows="3"
                            :error="$errors->first('address')"
                            help="Optional: Full address of the customer"
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-4 pt-6 animate-in" style="animation-delay: 0.7s">
                        <button type="submit" class="btn-primary">
                            <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                            Create Customer
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn-ghost">
                            <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection