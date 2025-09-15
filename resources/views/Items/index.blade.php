@extends('layouts.app')

@section('title', 'Items')
@section('page-title', 'Item Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Items</li>
@endsection

{{-- Page Actions: Add, Export, Import --}}
@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('items.create') }}" class="btn btn-primary me-2">
            <i data-lucide="plus" class="me-2"></i> Add New Item
        </a>
        <a href="{{ route('items.export') }}" class="btn btn-outline-secondary me-2">
            <i data-lucide="download" class="me-2"></i> Export
        </a>
        <button id="openImportBtn" class="btn btn-outline-success">
            <i data-lucide="upload" class="me-2"></i> Import
        </button>
    </div>
</div>

{{-- Import Popup Card --}}
<div id="importPopup" class="custom-popup">
    <div class="custom-popup-content">
        <h5>Import Items from CSV</h5>
        <form id="importForm" action="{{ route('items.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="csv_file" class="form-label">Choose CSV File</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" id="closeImportBtn">Cancel</button>
                <button type="submit" class="btn btn-success">Import</button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- Main Content --}}
@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i data-lucide="check-circle" class="me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">Items</h5>
        <!-- <div class="card-header-action">
                <div class="card-header-btn">
                    <div data-bs-toggle="tooltip" title="Delete">
                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove"> </a>
                    </div>
                    <div data-bs-toggle="tooltip" title="Refresh">
                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                    </div>
                    <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                        <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                        <div data-bs-toggle="tooltip" title="Options">
                            <i data-lucide="more-vertical"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="{{ route('items.create') }}" class="dropdown-item"><i data-lucide="plus"></i>New Vendor</a>
                        <a href="javascript:void(0);" class="dropdown-item"><i data-lucide="download"></i>Export</a>
                        <a href="javascript:void(0);" class="dropdown-item"><i data-lucide="filter"></i>Filter</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item"><i data-lucide="settings"></i>Settings</a>
                    </div>
                </div>
            </div> -->
    </div>
    <div class="card-body custom-card-action p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="border-b">
                        <th>Item No</th>
                        <th>Description</th>
                        <th>VAT (%)</th>
                        <th>Manufacturer</th>
                        <th>Category</th>
                        <th>UOM</th>
                        <th>Min Stock</th>
                        <th>Max Stock</th>
                        <th>Serialized</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>{{ $item->item_no }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->vat }}</td>
                        <td>{{ $item->manufacturer_name ?? '-' }}</td>
                        <td>{{ $item->category?->name ?? '-' }}</td>
                        <td>{{ $item->unit_of_measure }}</td>
                        <td>{{ $item->min_stock }}</td>
                        <td>{{ $item->max_stock ?? '-' }}</td>
                        <td>{{ $item->is_serialized ? 'Yes' : 'No' }}</td>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('items.edit', $item->item_id) }}" data-bs-toggle="tooltip" title="Edit Item">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item->item_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-bs-toggle="tooltip" title="Delete Item" onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="empty-state">
                                <div class="d-flex align-items-center gap-3 justify-content-center mb-3">
                                    <div class="avatar avatar-xl bg-light rounded">
                                        <i data-lucide="box" class="avatar-icon text-muted"></i>
                                    </div>
                                </div>
                                <span class="d-block mb-2">No items found</span>
                                <span class="fs-12 d-block fw-normal text-muted mb-3">Add your first item to get started</span>
                                <a href="{{ route('items.create') }}" class="btn btn-primary">
                                    <i data-lucide="plus" class="me-2"></i>Add New Item
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($items->hasPages())
    <div class="card-footer">
        <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
            @if ($items->onFirstPage())
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-left"></i></a></li>
            @else
            <li><a href="{{ $items->previousPageUrl() }}"><i data-lucide="arrow-left"></i></a></li>
            @endif

            @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                <li><a href="{{ $url }}" class="{{ $page == $items->currentPage() ? 'active' : '' }}">{{ $page }}</a></li>
            @endforeach

            @if ($items->hasMorePages())
            <li><a href="{{ $items->nextPageUrl() }}"><i data-lucide="arrow-right"></i></a></li>
            @else
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-right"></i></a></li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection

{{-- Scripts --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    const openBtn = document.getElementById('openImportBtn');
    const closeBtn = document.getElementById('closeImportBtn');
    const popup = document.getElementById('importPopup');

    openBtn.addEventListener('click', function() {
        popup.style.display = 'flex';
    });

    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    popup.addEventListener('click', function(e) {
        if (e.target === popup) popup.style.display = 'none';
    });
});
</script>
@endpush
