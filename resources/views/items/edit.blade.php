@extends('layouts.app')

@section('title', 'Edit Item')

@section('page-title', 'Edit Item')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('items.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Items</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('items.show', $item) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">{{ $item->name }}</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">Edit</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-4">
        <a href="{{ route('items.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Items
        </a>
    </div>
@endsection

@section('content')
    @if($errors->any())
        <div class="alert-danger mb-6" x-data="{ show: true }" x-show="show">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1">
                    <p class="font-medium">Please fix the following errors:</p>
                    <ul class="mt-2 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="flex-shrink-0">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                    @if($item->is_serialized)
                        <i data-lucide="scan-line" class="w-6 h-6 text-white"></i>
                    @else
                        <i data-lucide="package" class="w-6 h-6 text-white"></i>
                    @endif
                </div>
                <div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">Edit Item Information</h5>
                    <p class="text-slate-600 dark:text-slate-400">Update item details and specifications</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('items.update', $item) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="item_no" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Item Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="hash" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <input type="text"
                                name="item_no"
                                id="item_no"
                                value="{{ old('item_no', $item->item_no) }}"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('item_no') border-rose-500 @enderror"
                                placeholder="e.g., BP-001"
                                required>
                        </div>
                        @error('item_no')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Item Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="package" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <input type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $item->name) }}"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-rose-500 @enderror"
                                placeholder="e.g., Brake Pad Set"
                                required>
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Description
                    </label>
                    <textarea name="description"
                        id="description"
                        rows="3"
                        class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-rose-500 @enderror"
                        placeholder="Item description and specifications">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Category <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="tag" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <select name="category_id"
                                id="category_id"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('category_id') border-rose-500 @enderror"
                                required>
                                <option value="">Select category</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_id')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="unit_of_measure" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Unit of Measure
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="ruler" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            <select name="unit_of_measure"
                                id="unit_of_measure"
                                class="pl-10 w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('unit_of_measure') border-rose-500 @enderror">
                                <option value="piece" {{ old('unit_of_measure', $item->unit_of_measure) == 'piece' ? 'selected' : '' }}>Piece</option>
                                <option value="set" {{ old('unit_of_measure', $item->unit_of_measure) == 'set' ? 'selected' : '' }}>Set</option>
                                <option value="kit" {{ old('unit_of_measure', $item->unit_of_measure) == 'kit' ? 'selected' : '' }}>Kit</option>
                                <option value="liter" {{ old('unit_of_measure', $item->unit_of_measure) == 'liter' ? 'selected' : '' }}>Liter</option>
                                <option value="kg" {{ old('unit_of_measure', $item->unit_of_measure) == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                <option value="meter" {{ old('unit_of_measure', $item->unit_of_measure) == 'meter' ? 'selected' : '' }}>Meter</option>
                            </select>
                        </div>
                        @error('unit_of_measure')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Note: Pricing is now managed at batch level through GRN entries -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0"></i>
                        <div>
                            <h6 class="font-medium text-amber-800 dark:text-amber-200">Batch-Based Pricing</h6>
                            <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                Pricing is now managed at the batch level. Current pricing information can be found
                                in the batch details. New pricing is set when receiving items through GRN.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                            name="is_serialized"
                            value="1"
                            {{ old('is_serialized', $item->is_serialized) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary-600 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Track Serial Numbers</span>
                    </label>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-primary-600 bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Active Item</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Item
                    </button>
                    <a href="{{ route('items.show', $item) }}" class="btn-secondary">
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