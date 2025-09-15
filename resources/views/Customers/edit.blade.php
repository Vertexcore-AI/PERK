@extends('layouts.app')

@section('title', 'Edit Customer')

@section('page-title', 'Edit Customer')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
<li class="breadcrumb-item"><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
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
                        <h5 class="card-title mb-0">Edit Customer Information</h5>
                        <p class="text-muted mb-0">Update customer details and contact information</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Customer Name -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.input label="Customer Name" name="name" :value="old('name', $customer->name)"
                                placeholder="Enter customer name" required icon="users"
                                :error="$errors->first('name')" />
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.input label="Contact Information" name="contact"
                                :value="old('contact', $customer->contact)"
                                placeholder="Phone, email, or contact person" icon="phone"
                                :error="$errors->first('contact')" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.textarea label="Address" name="address" :value="old('address', $customer->address)"
                                placeholder="Enter customer address" rows="3" :error="$errors->first('address')" />
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.select label="Customer Type" name="type"
                                :options="['Retail' => 'Retail', 'Insurance' => 'Insurance']"
                                :selected="old('type', $customer->type)" required icon="briefcase"
                                :error="$errors->first('type')" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="save" class="me-2"></i>
                            Update Customer
                        </button>
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary">
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
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush