@extends('layouts.app')

@section('title', 'Edit Category')

@section('page-title', 'Edit Category')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorys</a></li>
<li class="breadcrumb-item"><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-secondary">
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
                        <h5 class="card-title mb-0">Edit Category</h5>
                        <p class="text-muted mb-0">Update category details below</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Category Name -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.input label="Category Name" name="name" :value="old('name', $category->name)"
                                placeholder="Enter category name" required icon="layers"
                                :error="$errors->first('name')" />
                        </div>
                    </div>

                    <!-- Category Description -->
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.textarea label="Description" name="description"
                                :value="old('description', $category->description)"
                                placeholder="Enter category description" rows="3" icon="file-text"
                                :error="$errors->first('description')" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 pt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="save" class="me-2"></i>
                            Update Category
                        </button>
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-secondary">
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