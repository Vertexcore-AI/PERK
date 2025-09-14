@extends('layouts.app')

@section('title', 'Stores')

@section('page-title', 'Stores Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Stores</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('stores.create') }}" class="btn btn-primary" style="margin-right: 10px;">
            <i data-lucide="plus" class="me-2"></i>
            Add New Stores
        </a>
        <a href="{{ route('stores.export') }}" class="btn btn-outline-secondary">
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

<!-- Stores Table -->
<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">Stores</h5>
        <div class="dropdown">
            <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                data-bs-offset="25, 25">
                <div data-bs-toggle="tooltip" title="Options">
                    <i data-lucide="more-vertical"></i>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="{{ route('stores.create') }}" class="dropdown-item"><i data-lucide="plus"></i>New Store</a>
                <a href="javascript:void(0);" class="dropdown-item"><i data-lucide="download"></i>Export</a>
                <a href="javascript:void(0);" class="dropdown-item"><i data-lucide="filter"></i>Filter</a>
            </div>
        </div>
    </div>

    <div class="card-body custom-card-action p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="border-b">
                        <th scope="row">Store ID</th>
                        <th>Store Name</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $store)
                    <tr>
                        <td>{{ $store->store_id }}</td>
                        <td>{{ $store->store_name }}</td>
                        <td>{{ $store->store_location ?? 'No location' }}</td>
                        <td>{{ $store->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('stores.edit', $store) }}" data-bs-toggle="tooltip"
                                    title="Edit Store">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                </a>
                                <form action="{{ route('stores.destroy', $store) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-bs-toggle="tooltip" title="Delete Store"
                                        onclick="return confirm('Are you sure you want to delete this store?')">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    </button>
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
                                        <i data-lucide="home" class="avatar-icon text-muted"></i>
                                    </div>
                                </div>
                                <span class="d-block mb-2">No stores found</span>
                                <span class="fs-12 d-block fw-normal text-muted mb-3">Get started by adding your first
                                    store</span>
                                <a href="{{ route('stores.create') }}" class="btn btn-primary">
                                    <i data-lucide="plus" class="me-2"></i>
                                    Add New Store
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($stores->hasPages())
    <div class="card-footer">
        <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
            @if ($stores->onFirstPage())
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-left"></i></a></li>
            @else
            <li><a href="{{ $stores->previousPageUrl() }}"><i data-lucide="arrow-left"></i></a></li>
            @endif

            @foreach ($stores->getUrlRange(1, $stores->lastPage()) as $page => $url)
            @if ($page == $stores->currentPage())
            <li><a href="javascript:void(0);" class="active">{{ $page }}</a></li>
            @else
            <li><a href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach

            @if ($stores->hasMorePages())
            <li><a href="{{ $stores->nextPageUrl() }}"><i data-lucide="arrow-right"></i></a></li>
            @else
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-right"></i></a></li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection