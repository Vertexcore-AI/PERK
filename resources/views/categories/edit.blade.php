@extends('layouts.app')

@section('title', 'Edit Category')

@section('page-title', 'Edit Category')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('categories.index') }}">Categories</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Edit</span>
    </div>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('categories.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Categories
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
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mr-4">
                    <i data-lucide="tag" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Edit Category Information</h5>
                    <p class="text-slate-600 dark:text-slate-400">Update category details</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="animate-in" style="animation-delay: 0.2s">
                    <x-forms.input
                        label="Category Name"
                        name="name"
                        :value="old('name', $category->name)"
                        placeholder="e.g., Engine Parts"
                        required
                        icon="tag"
                        :error="$errors->first('name')"
                    />
                </div>

                <div class="animate-in" style="animation-delay: 0.3s">
                    <x-forms.textarea
                        label="Description"
                        name="description"
                        :value="old('description', $category->description)"
                        placeholder="Enter category description"
                        rows="3"
                        :error="$errors->first('description')"
                        help="Optional: Brief description of what this category contains"
                    />
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Category
                    </button>
                    <a href="{{ route('categories.show', $category) }}" class="btn-secondary">
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