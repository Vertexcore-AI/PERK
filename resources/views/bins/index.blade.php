@extends('layouts.app')

@section('title', 'Bins')

@section('page-title')
    @if($filteredStore)
        {{ $filteredStore->store_name }} - Bins
    @else
        Bin Management
    @endif
@endsection

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    @if($filteredStore)
        <li class="flex items-center">
            <a href="{{ route('stores.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Stores</a>
            <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
        </li>
        <li class="flex items-center">
            <a href="{{ route('bins.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Bins</a>
            <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
        </li>
        <li class="text-slate-600 dark:text-slate-300">{{ $filteredStore->store_name }}</li>
    @else
        <li class="text-slate-600 dark:text-slate-300">Bins</li>
    @endif
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('bins.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add New Bin
        </a>
    </div>
@endsection

@section('content')
    @if(session('success'))
        <div x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="p-1 hover:bg-emerald-200 dark:hover:bg-emerald-800 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    <!-- Bins Table Card -->
    <div class="card animate-in" style="animation-delay: 0.1s">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Storage Bins</h3>

                <!-- Search -->
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                    <input type="text"
                        id="bin-search"
                        placeholder="Search bins..."
                        class="pl-10 pr-4 py-2 w-64 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-modern w-full">
                <thead>
                    <tr>
                        <th scope="col">Bin Code</th>
                        <th scope="col">Store</th>
                        <th scope="col">Description</th>
                        <th scope="col">Created</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($bins as $bin)
                        <tr class="group">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                                        <i data-lucide="grid-3x3" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <div>
                                        <div>
                                            <span class="font-medium text-slate-900 dark:text-white">{{ $bin->code }}</span>
                                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $bin->name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <span class="text-slate-900 dark:text-slate-100">{{ $bin->store->store_name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-slate-600 dark:text-slate-400">
                                    {{ $bin->description ?? 'No description' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-slate-500 dark:text-slate-400">
                                    {{ $bin->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('bins.show', $bin) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="View Bin">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-blue-600 dark:group-hover/btn:text-blue-400"></i>
                                    </a>
                                    <a href="{{ route('bins.edit', $bin) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="Edit Bin">
                                        <i data-lucide="edit" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <form action="{{ route('bins.destroy', $bin) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn-icon group/btn"
                                            data-tooltip="Delete Bin"
                                            onclick="return confirm('Are you sure you want to delete this bin?')">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-rose-600 dark:group-hover/btn:text-rose-400"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                        <i data-lucide="grid-3x3" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No bins found</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Get started by adding your first storage bin</p>
                                    <a href="{{ route('bins.create') }}" class="btn-primary">
                                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                                        Add New Bin
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bins->hasPages())
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600 dark:text-slate-400">
                        Showing {{ $bins->firstItem() }} to {{ $bins->lastItem() }} of {{ $bins->total() }} results
                    </span>
                    <div class="flex items-center gap-2">
                        {{ $bins->links() }}
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
        const searchInput = document.getElementById('bin-search');
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