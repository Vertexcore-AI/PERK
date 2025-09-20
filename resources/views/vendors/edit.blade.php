@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('page-title', 'Edit Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vendors.show', $vendor) }}">{{ $vendor->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('vendors.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Vendors
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
                <div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Edit Vendor Information</h5>
                    <p class="text-slate-600 dark:text-slate-400">Update vendor details and contact information</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('vendors.update', $vendor) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-forms.input
                        label="Vendor Name"
                        name="name"
                        :value="old('name', $vendor->name)"
                        placeholder="Enter vendor name"
                        required
                        icon="building"
                        :error="$errors->first('name')"
                    />
                </div>

                <div>
                    <x-forms.input
                        label="Contact Information"
                        name="contact"
                        :value="old('contact', $vendor->contact)"
                        placeholder="Phone, email, or contact person"
                        icon="phone"
                        :error="$errors->first('contact')"
                    />
                </div>

                <div>
                    <x-forms.textarea
                        label="Address"
                        name="address"
                        :value="old('address', $vendor->address)"
                        placeholder="Enter vendor address"
                        rows="3"
                        :error="$errors->first('address')"
                    />
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Vendor
                    </button>
                    <a href="{{ route('vendors.show', $vendor) }}" class="btn-secondary">
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