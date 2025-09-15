@extends('layouts.app')

@section('title', 'EditQuotation')

@section('page-title', 'EditQuotation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
<li class="breadcrumb-item">
    <a href="{{ route('quotations.show', $quotation) }}">
        {{ $quotation->customer->name ?? 'Customer' }}
    </a>
</li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-outline-secondary">
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
                        <h5 class="card-title mb-0">Edit Quotation</h5>
                        <p class="text-muted mb-0">Update quotation details</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('quotations.update', $quotation) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Customer Dropdown -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->customer_id }}" {{ old('customer_id', $quotation->
                                    customer_id) == $customer->customer_id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Quote Date & Valid Until -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-forms.input type="date" label="Quote Date" name="quote_date"
                                :value="old('quote_date', $quotation->quote_date)" required icon="calendar"
                                :error="$errors->first('quote_date')" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <x-forms.input type="date" label="Valid Until" name="valid_until"
                                :value="old('valid_until', $quotation->valid_until)" required icon="calendar"
                                :error="$errors->first('valid_until')" />
                        </div>
                    </div>

                    <!-- Total Estimate -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <x-forms.input type="number" step="0.01" label="Total Estimate" name="total_estimate"
                                :value="old('total_estimate', $quotation->total_estimate)" required icon="dollar-sign"
                                :error="$errors->first('total_estimate')" />
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <x-forms.select label="Status" name="status"
                                :options="['Pending'=>'Pending','Approved'=>'Approved','Rejected'=>'Rejected']"
                                :selected="old('status', $quotation->status)" required icon="check-circle"
                                :error="$errors->first('status')" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="save" class="me-2"></i>
                            Update Quotation
                        </button>
                        <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-outline-secondary">
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