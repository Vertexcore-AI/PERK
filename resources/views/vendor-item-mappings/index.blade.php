@extends('layouts.app')

@section('title', 'Vendor Item Mapping')

@section('page-title', 'Vendor Item Mapping')

@section('breadcrumb')
    <li class="flex items-center">
        <a href="{{ url('/dashboard') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Dashboard</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="flex items-center">
        <a href="{{ route('inventory.index') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">Inventory</a>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-slate-400"></i>
    </li>
    <li class="text-slate-600 dark:text-slate-300">Mapping</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('inventory.mappings.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            New Mapping
        </a>
        <button type="button" class="btn-secondary" onclick="openBulkImportModal()">
            <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
            Bulk Import
        </button>
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

    @if(session('error'))
        <div x-data="{ show: true }"
            x-show="show"
            class="alert alert-danger mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                </div>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="p-1 hover:bg-rose-200 dark:hover:bg-rose-800 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="link" class="w-6 h-6 text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <span class="badge badge-info">Total</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalMappings ?? 0 }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Total Mappings</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="star" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="badge badge-success">Preferred</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $preferredMappings ?? 0 }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Preferred Vendors</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="package" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                </div>
                <span class="badge badge-primary">Items</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $uniqueItems ?? 0 }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Mapped Items</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-amber-600 dark:text-amber-400"></i>
                </div>
                <span class="badge badge-warning">Average</span>
            </div>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $averageMappingsPerItem ?? 0 }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Vendors per Item</p>
        </div>
    </div>

    <!-- Mappings Table -->
    <div class="card">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Vendor Item Mappings</h3>
            </div>

            <!-- Filters -->
            <form method="GET" class="flex flex-wrap items-center gap-4">
                <div class="relative min-w-[200px]">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search mappings..."
                        class="pl-10 pr-4 py-2 w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                <select name="vendor_id" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All Vendors</option>
                    @foreach($vendors ?? [] as $vendor)
                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>

                <select name="preferred" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All Mappings</option>
                    <option value="1" {{ request('preferred') === '1' ? 'selected' : '' }}>Preferred Only</option>
                    <option value="0" {{ request('preferred') === '0' ? 'selected' : '' }}>Non-Preferred</option>
                </select>

                <button type="submit" class="btn-primary btn-sm">
                    <i data-lucide="filter" class="w-4 h-4 mr-1"></i>
                    Filter
                </button>

                @if(request()->hasAny(['search', 'vendor_id', 'preferred']))
                    <a href="{{ route('inventory.mappings.index') }}" class="btn-secondary btn-sm">
                        <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-modern w-full">
                <thead>
                    <tr>
                        <th scope="col">Vendor Item Code</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Internal Item</th>
                        <th scope="col">Category</th>
                        <th scope="col">Vendor Cost</th>
                        <th scope="col">Status</th>
                        <th scope="col">Created</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($mappings as $mapping)
                        <tr class="group">
                            <td>
                                <div>
                                    <span class="font-medium text-slate-900 dark:text-white">{{ $mapping->vendor_item_code }}</span>
                                    @if($mapping->vendor_item_name)
                                        <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $mapping->vendor_item_name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <i data-lucide="truck" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <span class="text-slate-900 dark:text-white">{{ $mapping->vendor->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="font-medium text-slate-900 dark:text-white">{{ $mapping->item->name ?? 'N/A' }}</span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $mapping->item->item_no ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $mapping->item->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td>
                                @if($mapping->vendor_cost)
                                    <span class="font-medium text-slate-900 dark:text-white">${{ number_format($mapping->vendor_cost, 2) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td>
                                @if($mapping->is_preferred)
                                    <span class="badge badge-success">
                                        <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                                        Preferred
                                    </span>
                                @else
                                    <button type="button"
                                        onclick="setPreferred({{ $mapping->id }})"
                                        class="badge badge-secondary hover:badge-warning cursor-pointer transition-colors">
                                        Set Preferred
                                    </button>
                                @endif
                            </td>
                            <td>
                                <span class="text-slate-600 dark:text-slate-400">{{ $mapping->created_at ? $mapping->created_at->format('d/m/Y') : 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('inventory.mappings.show', $mapping) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="View Mapping">
                                        <i data-lucide="eye" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <a href="{{ route('inventory.mappings.edit', $mapping) }}"
                                        class="btn-icon group/btn"
                                        data-tooltip="Edit Mapping">
                                        <i data-lucide="edit" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-primary-600 dark:group-hover/btn:text-primary-400"></i>
                                    </a>
                                    <form action="{{ route('inventory.mappings.destroy', $mapping) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn-icon group/btn"
                                            data-tooltip="Delete Mapping"
                                            onclick="return confirm('Are you sure you want to delete this mapping?')">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover/btn:text-rose-600 dark:group-hover/btn:text-rose-400"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
                                        <i data-lucide="link" class="w-10 h-10 text-slate-400 dark:text-slate-500"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No mappings found</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Start by creating your first vendor item mapping</p>
                                    <a href="{{ route('inventory.mappings.create') }}" class="btn-primary">
                                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                                        New Mapping
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mappings->hasPages())
            <div class="p-6 border-t border-slate-200 dark:border-slate-700">
                {{ $mappings->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function setPreferred(mappingId) {
        if (confirm('Set this as the preferred vendor for this item?')) {
            fetch(`/inventory/mappings/${mappingId}/set-preferred`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    }

    function openBulkImportModal() {
        alert('Bulk import feature will be implemented in future version');
    }
</script>
@endpush