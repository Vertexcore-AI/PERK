@extends('layouts.app')

@section('title', 'Edit Bin')

@section('page-title', 'Edit Storage Bin')

@section('breadcrumb')
    <div class="breadcrumb-path">
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('bins.index') }}">Bins</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('bins.show', $bin) }}">{{ $bin->name }}</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Edit</span>
    </div>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('bins.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Bins
        </a>
        <a href="{{ route('bins.show', $bin) }}" class="btn-secondary">
            <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
            View Bin
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
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/25">
                        <i data-lucide="grid-3x3" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Edit Storage Bin</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Update storage bin information</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('bins.update', $bin) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Store Selection -->
                    <div class="animate-in" style="animation-delay: 0.2s">
                        <label for="store_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Store <span class="text-rose-500">*</span>
                        </label>
                        <select name="store_id" id="store_id" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent {{ $errors->has('store_id') ? 'border-rose-500 dark:border-rose-400' : '' }}">
                            <option value="">Select a store</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store_id', $bin->store_id) == $store->id ? 'selected' : '' }}>
                                    {{ $store->store_name }} - {{ $store->store_location ?? 'No location' }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('store_id'))
                            <p class="text-sm text-rose-500 dark:text-rose-400 flex items-center gap-1 mt-2">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $errors->first('store_id') }}
                            </p>
                        @endif
                    </div>

                    <!-- Bin Code -->
                    <div class="animate-in" style="animation-delay: 0.3s">
                        <x-forms.input
                            label="Bin Code"
                            name="code"
                            :value="old('code', $bin->code)"
                            placeholder="e.g., A1, B2, ENG-01"
                            required
                            icon="hash"
                            :error="$errors->first('code')"
                            help="Unique code for this storage bin"
                        />
                    </div>

                    <!-- Bin Name -->
                    <div class="animate-in" style="animation-delay: 0.4s">
                        <x-forms.input
                            label="Bin Name"
                            name="name"
                            :value="old('name', $bin->name)"
                            placeholder="e.g., Engine Parts Storage"
                            required
                            icon="grid-3x3"
                            :error="$errors->first('name')"
                            help="Descriptive name for this storage bin"
                        />
                    </div>

                    <!-- Description -->
                    <div class="animate-in" style="animation-delay: 0.5s">
                        <x-forms.textarea
                            label="Description"
                            name="description"
                            :value="old('description', $bin->description)"
                            placeholder="Enter bin description (optional)"
                            rows="3"
                            :error="$errors->first('description')"
                            help="Optional: Brief description of what this bin is used for"
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-4 pt-6 animate-in" style="animation-delay: 0.6s">
                        <button type="submit" class="btn-primary">
                            <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                            Update Bin
                        </button>
                        <a href="{{ route('bins.index') }}" class="btn-ghost">
                            <i data-lucide="x" class="w-5 h-5 mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bin Details Card -->
        <div class="card mt-6 animate-in" style="animation-delay: 0.2s">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h5 class="text-lg font-semibold text-slate-900 dark:text-white">Bin Details</h5>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Current bin information</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Created</span>
                        </div>
                        <p class="text-slate-900 dark:text-slate-100">{{ $bin->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-600 dark:text-slate-400"></i>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Last Updated</span>
                        </div>
                        <p class="text-slate-900 dark:text-slate-100">{{ $bin->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection