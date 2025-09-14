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
    <div class="page-header-right-items">
        <div class="btn-group">
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                <i data-lucide="arrow-left" class="me-2"></i>
                Back to Details
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
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                       
                        <div>
                            <h5 class="card-title mb-0">Edit Vendor Information</h5>
                            <p class="text-muted mb-0">Update vendor details and contact information</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendors.update', $vendor) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
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
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <x-forms.input
                                    label="Contact Information"
                                    name="contact"
                                    :value="old('contact', $vendor->contact)"
                                    placeholder="Phone, email, or contact person"
                                    icon="phone"
                                    :error="$errors->first('contact')"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <x-forms.input
                                    label="Address"
                                    name="address"
                                    :value="old('address', $vendor->address)"
                                    placeholder="Enter vendor address"
                                    rows="3"
                                    :error="$errors->first('address')"
                                />
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" class="btn btn-success">
                                <i data-lucide="save" class="me-2"></i>
                                Update Vendor
                            </button>
                            <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-outline-secondary">
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