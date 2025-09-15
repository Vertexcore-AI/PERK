@extends('layouts.app')

@section('title', 'Bins')
@section('page-title', 'Bin Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Bins</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('bins.create') }}" class="btn btn-primary" style="margin-right: 10px;">
            <i data-lucide="plus" class="me-2"></i>
            Add New Bin
        </a>
        <a href="{{ route('bins.export') }}" class="btn btn-outline-secondary">
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

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">Bin</h5>
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
                        <a href="{{ route('bins.create') }}" class="dropdown-item"><i data-lucide="plus"></i>New Vendor</a>
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
                        <th>Bin Name</th>
                        <th>Description</th>
                        <th>Store</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bins as $bin)
                    <tr>
                        <td>{{ $bin->bin_name }}</td>
                        <td>{{ $bin->description ?? 'No description' }}</td>
                        <td>{{ $bin->store->store_name ?? 'No Store' }}</td>
                        <td>{{ $bin->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($bin->status == 'active')
                            <span class="badge bg-soft-success text-success">Active</span>
                            @else
                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('bins.edit', $bin->bin_id) }}" data-bs-toggle="tooltip"
                                    title="Edit Bin">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                </a>
                                <form action="{{ route('bins.destroy', $bin->bin_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-bs-toggle="tooltip" title="Delete Bin"
                                        onclick="return confirm('Are you sure you want to delete this bin?')">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <div class="d-flex align-items-center gap-3 justify-content-center mb-3">
                                    <div class="avatar avatar-xl bg-light rounded">
                                        <i data-lucide="layers" class="avatar-icon text-muted"></i>
                                    </div>
                                </div>
                                <span class="d-block mb-2">No bins found</span>
                                <span class="fs-12 d-block fw-normal text-muted mb-3">Add your first bin to get
                                    started</span>
                                <a href="{{ route('bins.create') }}" class="btn btn-primary">
                                    <i data-lucide="plus" class="me-2"></i>Add New Bin
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($bins->hasPages())
    <div class="card-footer">
        <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
            @if ($bins->onFirstPage())
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-left"></i></a></li>
            @else
            <li><a href="{{ $bins->previousPageUrl() }}"><i data-lucide="arrow-left"></i></a></li>
            @endif

            @foreach ($bins->getUrlRange(1, $bins->lastPage()) as $page => $url)
            @if ($page == $bins->currentPage())
            <li><a href="javascript:void(0);" class="active">{{ $page }}</a></li>
            @else
            <li><a href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach

            @if ($bins->hasMorePages())
            <li><a href="{{ $bins->nextPageUrl() }}"><i data-lucide="arrow-right"></i></a></li>
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
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush