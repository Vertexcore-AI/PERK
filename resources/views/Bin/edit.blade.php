@extends('layouts.app')

@section('title', 'Edit Bin')

@section('page-title', 'Edit Bin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('bins.index') }}">Bins</a></li>
<li class="breadcrumb-item"><a href="{{ route('bins.show', $bin) }}">{{ $bin->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('bins.index', $bin) }}" class="btn btn-outline-secondary">
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
                        <h5 class="card-title mb-0">Edit Bin</h5>
                        <p class="text-muted mb-0">Update bin details below</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('bins.update', $bin->bin_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Store Dropdown -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="store_id" class="form-label">Store <span class="text-danger">*</span></label>
                            <select name="store_id" class="form-select" required>
                                <option value="">-- Select Store --</option>
                                @foreach($stores as $store)
                                <option value="{{ $store->store_id }}" {{ $bin->store_id == $store->store_id ?
                                    'selected' : '' }}>
                                    {{ $store->store_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('store_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Bin Name -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-forms.input label="Bin Name" name="bin_name" :value="old('bin_name', $bin->bin_name)"
                                placeholder="Enter bin name" required icon="layers"
                                :error="$errors->first('bin_name')" />
                        </div>
                    </div>

                    <!-- Bin Description -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-forms.textarea label="Description" name="description"
                                :value="old('description', $bin->description)" placeholder="Enter bin description"
                                rows="3" icon="file-text" :error="$errors->first('description')" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="save" class="me-2"></i>
                            Update Bin
                        </button>
                        <a href="{{ route('bins.index') }}" class="btn btn-outline-secondary">
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