@extends('layouts.app')

@section('title', 'Category')

@section('page-title', 'Category Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Categories</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('categories.create') }}" class="btn btn-primary" style="margin-right: 10px;">
            <i data-lucide="plus" class="me-2"></i>
            Add New Category
        </a>
        <a href="{{ route('categories.export') }}" class="btn btn-outline-secondary">
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

<!--categories table--->

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">Categories</h5>
        <div class="dropdown">
            <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                data-bs-offset="25, 25">
                <div data-bs-toggle="tooltip" title="Options">
                    <i data-lucide="more-vertical"></i>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="{{ route('categories.create') }}" class="dropdown-item"><i data-lucide="plus"></i>New
                    Category</a>
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
                        <th scope="row">Name</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description ?? 'No description' }}</td>
                        <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge bg-soft-success text-success">Active</span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('categories.edit', $category) }}" data-bs-toggle="tooltip"
                                    title="Edit Category">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-bs-toggle="tooltip" title="Delete Category"
                                        onclick="return confirm('Are you sure you want to delete this category?')">
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
                                        <i data-lucide="layers" class="avatar-icon text-muted"></i>
                                    </div>
                                </div>
                                <span class="d-block mb-2">No categories found</span>
                                <span class="fs-12 d-block fw-normal text-muted mb-3">Get started by adding your first
                                    category</span>
                                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                    <i data-lucide="plus" class="me-2"></i>
                                    Add New Category
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($categories->hasPages())
    <div class="card-footer">
        <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
            @if ($categories->onFirstPage())
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-left"></i></a></li>
            @else
            <li><a href="{{ $categories->previousPageUrl() }}"><i data-lucide="arrow-left"></i></a></li>
            @endif

            @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
            @if ($page == $categories->currentPage())
            <li><a href="javascript:void(0);" class="active">{{ $page }}</a></li>
            @else
            <li><a href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach

            @if ($categories->hasMorePages())
            <li><a href="{{ $categories->nextPageUrl() }}"><i data-lucide="arrow-right"></i></a></li>
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