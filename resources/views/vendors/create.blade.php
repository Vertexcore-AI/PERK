@extends('layouts.app')

@section('title', 'Add Vendor')

@section('page-title', 'Add New Vendor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
    <li class="breadcrumb-item active">Add Vendor</li>
@endsection

@section('page-actions')
    <div class="page-header-right-items">
        <div class="btn-group">
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                <i data-lucide="arrow-left" class="me-2"></i>
                Back to Vendors
            </a>
        </div>
    </div>
@endsection

@section('content')
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i data-lucide="alert-circle" class="me-2"></i>
            Please fix the following errors:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="card">
              
                <div class="card-body">
                    <form action="{{ route('vendors.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <x-forms.input
                                    label="Vendor Name"
                                    name="name"
                                    :value="old('name')"
                                    placeholder="Enter vendor name"
                                    required
                                    icon="building"
                                    :error="$errors->first('name')"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <x-forms.input
                                    label="Contact Information"
                                    name="contact"
                                    :value="old('contact')"
                                    placeholder="Phone, email, or contact person"
                                    icon="phone"
                                    :error="$errors->first('contact')"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <x-forms.textarea
                                    label="Address"
                                    name="address"
                                    :value="old('address')"
                                    placeholder="Enter vendor address"
                                    rows="3"
                                    :error="$errors->first('address')"
                                />
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" class="btn btn-success">
                                <i data-lucide="save" class="me-2"></i>
                                Save Vendor
                            </button>
                            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                                <i data-lucide="x" class="me-2"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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