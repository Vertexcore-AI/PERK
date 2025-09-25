@extends('layouts.app')

@section('title', 'Add Category')

@section('page-title', 'Add New Category')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('categories.index') }}">Categories</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Add New Category</span>
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
        <div x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-danger mb-6">
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
                <button @click="show = false" class="p-1 hover:bg-rose-200 dark:hover:bg-rose-800 rounded-lg transition-colors flex-shrink-0">
                    <i data-lucide="x" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="max-w-2xl mx-0">
        <!-- Form Card -->
        <div class="card animate-in" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/25">
                        <i data-lucide="tag" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Category Information</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Create a new category for organizing auto parts</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Category Name -->
                    <div class="animate-in" style="animation-delay: 0.2s">
                        <x-forms.input
                            label="Category Name"
                            name="name"
                            :value="old('name')"
                            placeholder="e.g., Engine Parts"
                            required
                           
                            :error="$errors->first('name')"
                        />
                    </div>

                    <!-- Description -->
                    <div class="animate-in" style="animation-delay: 0.3s">
                        <x-forms.textarea
                            label="Description"
                            name="description"
                            :value="old('description')"
                            placeholder="Enter category description"
                            rows="3"
                            :error="$errors->first('description')"
                          
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-4 pt-6 animate-in" style="animation-delay: 0.4s">
                        <button type="submit" class="btn-primary">

                            Create Category
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn-ghost">
                            <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection