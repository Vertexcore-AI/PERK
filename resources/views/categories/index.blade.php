@extends('layouts.app')

@section('title', 'Categories')

@section('page-title', 'Category Management')

@section('breadcrumb')
<li class="flex items-center">
    <a href="{{ url('/dashboard') }}"
        class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
    <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
</li>
<li class="text-slate-600 dark:text-slate-300">Categories</li>
@endsection

@section('page-actions')
<div class="flex items-center gap-3">
    <a href="{{ route('categories.create') }}" class="btn-primary">
        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
        Add New Category
    </a>
    <a href="{{ route('categorycsv.exprot') }}" class="btn-secondary">
        <i data-lucide="download" class="w-5 h-5 mr-2"></i>
        Export
    </a>

</div>
@endsection

@section('content')
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="alert alert-success mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
        </div>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    <button @click="show = false"
        class="p-1 hover:bg-emerald-200 dark:hover:bg-emerald-800 rounded-lg transition-colors">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
</div>
@endif

<!-- Categories Table Card -->
<div class="card animate-in" style="animation-delay: 0.1s">
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Categories</h3>

            <!-- Search -->
            <div class="relative">
                <i data-lucide="search"
                    class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                <input type="text" id="category-search" placeholder="Search categories..."
                    class="pl-10 pr-4 py-2 w-64 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table-modern w-full">
            <thead>
                <tr>
                    <th scope="col">Category</th>
                    <th scope="col">Description</th>
                    <th scope="col">Items Count</th>
                    <th scope="col">Date Added</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($categories as $category)
                <tr class="group">
                    <td>
                        <div class="flex items-center gap-3">

                            <div>
                                <span class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</span>
                                @if($category->parent_category)
                                <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Parent: {{ $category->parent_category }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($category->description)
                        <span class="text-slate-600 dark:text-slate-400">{{ Str::limit($category->description, 50)
                            }}</span>
                        @else
                        <span class="text-slate-400 dark:text-slate-500 italic">No description</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span class="text-slate-900 dark:text-slate-100 font-medium">{{ $category->items_count ?? 0
                                }}</span>
                            <span class="text-slate-500 dark:text-slate-400">items</span>
                        </div>
                    </td>
                    <td>
                        <span class="text-slate-600 dark:text-slate-400">{{ $category->created_at->format('d/m/Y H:i')
                            }}</span>
                    </td>
                    <td>
                        <span class="badge badge-success">Active</span>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-1">

                            <a href="{{ route('categories.edit', $category) }}" class="btn-icon group/btn"
                                data-tooltip="Edit Category">
                                <i data-lucide="edit"
                                    class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon group/btn" data-tooltip="Delete Category"
                                    onclick="return confirm('Are you sure you want to delete this category?')">
                                    <i data-lucide="trash-2"
                                        class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-rose-600 dark:group-hover/btn:text-rose-400"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12">
                        <div class="flex flex-col items-center justify-center">
                            <div
                                class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                <i data-lucide="tag" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No categories found</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Get started by adding your first
                                category</p>
                            <a href="{{ route('categories.create') }}" class="btn-primary">
                                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                                Add New Category
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
    <div class="p-6 border-t border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600 dark:text-slate-400">
                Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }}
                results
            </span>
            <div class="flex items-center gap-2">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple search functionality
        const searchInput = document.getElementById('category-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const tbody = document.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush