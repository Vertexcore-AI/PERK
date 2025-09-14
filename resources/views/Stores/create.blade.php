@extends('layouts.app')

@section('title', 'Add Store')

@section('page-title', 'Add New Store')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('stores.index') }}">Store</a></li>
<li class="breadcrumb-item active">Add Store</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
            <i data-lucide="arrow-left" class="me-2"></i>
            Back to Store
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
            <div class="card-header">
                <h5 class="card-title">Add New Store</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('stores.store') }}" method="POST">
                    @csrf

                    <!-- Store Name -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.input label="Store Name" name="store_name" :value="old('store_name')"
                                placeholder="Enter store name" required icon="building"
                                :error="$errors->first('store_name')" />
                        </div>
                    </div>

                    <!-- Store Location -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.textarea label="Store Location" name="store_location"
                                :value="old('store_location')" placeholder="Enter store location" rows="3"
                                icon="map-pin" :error="$errors->first('store_location')" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="save" class="me-2"></i>
                            Save Store
                        </button>
                        <a href="{{ route('stores.index') }}" class="btn btn-outline-secondary">
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