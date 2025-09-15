@extends('layouts.app')

@section('title', 'EditItem')

@section('page-title', 'EditItem')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
<li class="breadcrumb-item">
    <a href="{{ route('items.show', $item) }}">
        {{ $Item->item->name ?? 'item' }}
    </a>
</li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('items.index', $item) }}" class="btn btn-outline-secondary">
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
            <div class="card-body">
                <form action="{{ route('items.update', $item->item_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Item Number -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-forms.input label="Item Number" name="item_no"
                                :value="old('item_no', $item->item_no)"
                                placeholder="Enter item code" required icon="hash"
                                :error="$errors->first('item_no')" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <x-forms.textarea label="Description" name="description"
                                :value="old('description', $item->description)"
                                placeholder="Enter item description" rows="3" required
                                :error="$errors->first('description')" />
                        </div>
                    </div>

                    <!-- VAT and Unit of Measure -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-forms.input type="number" step="0.01" label="VAT (%)" name="vat"
                                :value="old('vat', $item->vat)" placeholder="Enter VAT percentage"
                                icon="percent" :error="$errors->first('vat')" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input label="Unit of Measure" name="unit_of_measure"
                                :value="old('unit_of_measure', $item->unit_of_measure)"
                                placeholder="PCS, KG, etc." icon="box"
                                :error="$errors->first('unit_of_measure')" />
                        </div>
                    </div>

                    <!-- Manufacturer Name and Category -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-forms.input label="Manufacturer Name" name="manufacturer_name"
                                :value="old('manufacturer_name', $item->manufacturer_name)"
                                placeholder="Enter manufacturer" icon="truck"
                                :error="$errors->first('manufacturer_name')" />
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-forms.input type="number" label="Minimum Stock" name="min_stock"
                                :value="old('min_stock', $item->min_stock)"
                                placeholder="Enter minimum stock" icon="minimize"
                                :error="$errors->first('min_stock')" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input type="number" label="Maximum Stock" name="max_stock"
                                :value="old('max_stock', $item->max_stock)"
                                placeholder="Enter maximum stock" icon="maximize"
                                :error="$errors->first('max_stock')" />
                        </div>
                    </div>

                    <!-- Serialized -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_serialized"
                                    name="is_serialized" value="1"
                                    {{ old('is_serialized', $item->is_serialized) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_serialized">Is Serialized?</label>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="save" class="me-2"></i>
                            Update Item
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
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