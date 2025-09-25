@extends('layouts.app')

@section('title', 'Edit Store')

@section('page-title', 'Edit Store')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('stores.index') }}">Stores</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('stores.show', $store) }}">{{ $store->store_name }}</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Edit</span>
    </div>
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
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl p-4 mb-6">
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
            </div>
        </div>
    @endif

    <div class="card">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <i data-lucide="warehouse" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Edit Store Information</h5>
                    <p class="text-slate-600 dark:text-slate-400">Update store details and location information</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('stores.update', $store) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="animate-in" style="animation-delay: 0.2s">
                    <x-forms.input
                        label="Store Name"
                        name="store_name"
                        :value="old('store_name', $store->store_name)"
                        placeholder="e.g., Main Warehouse"
                        required
                        icon="warehouse"
                        :error="$errors->first('store_name')"
                    />
                </div>

                <div class="animate-in" style="animation-delay: 0.3s">
                    <x-forms.textarea
                        label="Store Location"
                        name="store_location"
                        :value="old('store_location', $store->store_location)"
                        placeholder="Enter store location/address"
                        rows="3"
                        :error="$errors->first('store_location')"
                        help="Optional: Location or address of the store"
                    />
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Store
                    </button>
                    <a href="{{ route('stores.show', $store) }}" class="btn-secondary">
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