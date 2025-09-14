@extends('layouts.app')

@section('title', 'Vendors')

@section('page-title', 'Vendor Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Vendors</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('vendors.create') }}" class="btn btn-primary" style="margin-right: 10px;">
            <i data-lucide="plus" class="me-2"></i>
            Add New Vendor
        </a>
        <a href="{{ route('vendors.export') }}" class="btn btn-outline-secondary">
            <i data-lucide="download" class="me-2"></i>
            Export
        </a>
    </div>
</div>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i data-lucide="check-circle" class="me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif


<!-- Vendors Table -->
<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">Vendors</h5>
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
                        <a href="{{ route('vendors.create') }}" class="dropdown-item"><i data-lucide="plus"></i>New Vendor</a>
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
                        <th scope="row">Vendor</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">

                                <div>
                                    <span class="d-block">{{ $vendor->name }}</span>

                                </div>
                            </div>
                        </td>
                        <td>
                            @if($vendor->contact)
                            <span class="d-block">{{ $vendor->contact }}</span>
                            @if($vendor->address)
                            <span class="fs-12 d-block fw-normal text-muted">{{ Str::limit($vendor->address, 30)
                                }}</span>
                            @endif
                            @else
                            <span class="text-muted">No contact info</span>
                            @endif
                        </td>
                        <td>{{ $vendor->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge bg-soft-success text-success">Active</span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('vendors.edit', $vendor) }}" data-bs-toggle="tooltip"
                                    title="Edit Vendor"><i data-lucide="edit"
                                        style="width: 14px; height: 14px;"></i></a>
                                <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-bs-toggle="tooltip" title="Delete Vendor"
                                        onclick="return confirm('Are you sure you want to delete this vendor?')"><i
                                            data-lucide="trash-2" style="width: 14px; height: 14px;"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="empty-state">
                                <div class="d-flex align-items-center gap-3 justify-content-center mb-3">
                                    <div class="avatar avatar-xl bg-light rounded">
                                        <i data-lucide="building" class="avatar-icon text-muted"></i>
                                    </div>
                                </div>
                                <span class="d-block mb-2">No vendors found</span>
                                <span class="fs-12 d-block fw-normal text-muted mb-3">Get started by adding your first
                                    vendor</span>
                                <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                                    <i data-lucide="plus" class="me-2"></i>
                                    Add New Vendor
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($vendors->hasPages())
    <div class="card-footer">
        <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
            @if ($vendors->onFirstPage())
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-left"></i></a></li>
            @else
            <li><a href="{{ $vendors->previousPageUrl() }}"><i data-lucide="arrow-left"></i></a></li>
            @endif

            @foreach ($vendors->getUrlRange(1, $vendors->lastPage()) as $page => $url)
            @if ($page == $vendors->currentPage())
            <li><a href="javascript:void(0);" class="active">{{ $page }}</a></li>
            @else
            <li><a href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach

            @if ($vendors->hasMorePages())
            <li><a href="{{ $vendors->nextPageUrl() }}"><i data-lucide="arrow-right"></i></a></li>
            @else
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-right"></i></a></li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Simple search functionality
        const searchInput = document.getElementById('vendor-search');
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