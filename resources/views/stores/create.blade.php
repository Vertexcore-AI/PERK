@extends('layouts.app')

@section('title', 'Add Store')

@section('page-title', 'Add New Store')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('stores.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Stores</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">Add New</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('stores.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Stores
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
        <div class="card animate-in" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                        <i data-lucide="warehouse" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Store Information</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Create a new store location for inventory management</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('stores.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Store Name -->
                    <div class="animate-in" style="animation-delay: 0.2s">
                        <x-forms.input
                            label="Store Name"
                            name="store_name"
                            :value="old('store_name')"
                            placeholder="e.g., Main Warehouse"
                            required
                            icon="warehouse"
                            :error="$errors->first('store_name')"
                        />
                    </div>

                    <!-- Store Location -->
                    <div class="animate-in" style="animation-delay: 0.3s">
                        <x-forms.textarea
                            label="Store Location"
                            name="store_location"
                            :value="old('store_location')"
                            placeholder="Enter store location/address"
                            rows="3"
                            :error="$errors->first('store_location')"
                            help="Optional: Location or address of the store"
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-4 pt-6 animate-in" style="animation-delay: 0.4s">
                        <button type="submit" class="btn-primary">
                            <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                            Create Store
                        </button>
                        <a href="{{ route('stores.index') }}" class="btn-ghost">
                            <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bins Info Card -->
        <div class="card mt-6 animate-in" style="animation-delay: 0.2s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Storage Bins</h5>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">You can add storage bins after creating the store</p>
            </div>
            <div class="p-6">
                <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-slate-600 dark:text-slate-400 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-slate-700 dark:text-slate-300">
                                Storage bins help organize inventory within the store. After creating the store, you can add multiple bins with unique codes for precise inventory location tracking.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection