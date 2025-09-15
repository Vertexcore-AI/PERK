@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Customers</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('customers.create') }}" class="btn btn-primary me-2">
            <i data-lucide="plus" class="me-2"></i> Add New Customer
        </a>
        <a href="{{ route('customers.export') }}" class="btn btn-outline-secondary">
            <i data-lucide="download" class="me-2"></i> Export
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
    <div class="card-body custom-card-action p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="border-b">
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->contact ?? '-' }}</td>
                        <td>{{ $customer->address ?? '-' }}</td>
                        <td>{{ $customer->type ?? 'Retail' }}</td>
                        <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('customers.edit', $customer->customer_id) }}" data-bs-toggle="tooltip"
                                    title="Edit Customer">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-bs-toggle="tooltip" title="Delete Customer"
                                        onclick="return confirm('Are you sure you want to delete this customer?')">
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
                                        <i data-lucide="users" class="avatar-icon text-muted"></i>
                                    </div>
                                </div>
                                <span class="d-block mb-2">No customers found</span>
                                <span class="fs-12 d-block fw-normal text-muted mb-3">Add your first customer to get
                                    started</span>
                                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                    <i data-lucide="plus" class="me-2"></i>Add New Customer
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($customers->hasPages())
    <div class="card-footer">
        <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
            @if ($customers->onFirstPage())
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-left"></i></a></li>
            @else
            <li><a href="{{ $customers->previousPageUrl() }}"><i data-lucide="arrow-left"></i></a></li>
            @endif

            @foreach ($customers->getUrlRange(1, $customers->lastPage()) as $page => $url)
            @if ($page == $customers->currentPage())
            <li><a href="javascript:void(0);" class="active">{{ $page }}</a></li>
            @else
            <li><a href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach

            @if ($customers->hasMorePages())
            <li><a href="{{ $customers->nextPageUrl() }}"><i data-lucide="arrow-right"></i></a></li>
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