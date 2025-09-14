@extends('layouts.app')

@section('title', 'Add New Bin')
@section('page-title', 'Add New Bin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('bins.index') }}">Bins</a></li>
<li class="breadcrumb-item active">Add New Bin</li>
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
                <h5 class="card-title">Add New Bin</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('bins.store') }}" method="POST">
                    @csrf

                    <!-- Bin Name -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.input label="Bin Name" name="bin_name" :value="old('bin_name')"
                                placeholder="Enter bin name" required icon="layers"
                                :error="$errors->first('bin_name')" />
                        </div>
                    </div>

                    <!-- Bin Description -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.textarea label="Description" name="description" :value="old('description')"
                                placeholder="Enter bin description" rows="3" icon="file-text"
                                :error="$errors->first('description')" />
                        </div>
                    </div>

                    <!-- Store Selection Mode -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Store Option</label><br>
                            <input type="radio" name="store_option" value="existing" checked
                                onchange="toggleStoreFields()"> Existing Store
                            <input type="radio" name="store_option" value="new" class="ms-3"
                                onchange="toggleStoreFields()"> Add New Store
                        </div>
                    </div>

                    <!-- Dropdown for existing stores -->
                    <div class="row mb-3" id="existing_store_field">
                        <div class="col-md-12">
                            <x-forms.select label="Select Store" name="store_id"
                                :options="$stores->pluck('store_name', 'store_id')" :selected="old('store_id')"
                                icon="building" :error="$errors->first('store_id')" />
                        </div>
                    </div>

                    <!-- Input for new store -->
                    <div class="row mb-3" id="new_store_field" style="display:none;">
                        <div class="col-md-12">
                            <x-forms.input label="New Store Name" name="new_store" :value="old('new_store')"
                                placeholder="Enter new store name" icon="building"
                                :error="$errors->first('new_store')" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="save" class="me-2"></i>
                            Save Bin
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
    function toggleStoreFields() {
        let selectedOption = document.querySelector('input[name="store_option"]:checked').value;
        document.getElementById('existing_store_field').style.display = (selectedOption === 'existing') ? 'block' : 'none';
        document.getElementById('new_store_field').style.display = (selectedOption === 'new') ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endpush