@extends('layouts.app')

@section('title', 'Quotations')

@section('page-title', 'Quotation Management')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Quotations</li>
@endsection

@section('page-actions')
<div class="page-header-right-items">
    <div class="btn-group">
        <a href="{{ route('quotations.create') }}" class="btn btn-primary" style="margin-right: 10px;">
            <i data-lucide="plus" class="me-2"></i>
            Add New Quotation
        </a>
        <a href="{{ route('quotations.export') }}" class="btn btn-outline-secondary">
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

<!-- Quotations Table -->
<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">Quotations</h5>
        <div class="card-header-action">
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="me-2"></i>
                New Quotation
            </a>
        </div>
    </div>
    <div class="card-body custom-card-action p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="border-b">
                        <th>Customer</th>
                        <th>Quote Date</th>
                        <th>Valid Until</th>
                        <th>Total Estimate</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                    <tr>
                        <!-- Customer Name -->
                        <td>
                            {{ $quotation->customer ? $quotation->customer->name : 'N/A' }}
                        </td>

                        <!-- Quote Date -->
                        <td>{{ \Carbon\Carbon::parse($quotation->quote_date)->format('d/m/Y') }}</td>

                        <!-- Valid Until -->
                        <td>{{ \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') }}</td>

                        <!-- Total Amount -->
                        <td>${{ number_format($quotation->total_estimate, 2) }}</td>

                        <!-- Status -->
                        <td>
                            @if($quotation->status === 'Approved')
                            <span class="badge bg-soft-success text-success">Approved</span>
                            @elseif($quotation->status === 'Rejected')
                            <span class="badge bg-soft-danger text-danger">Rejected</span>
                            @else
                            <span class="badge bg-soft-warning text-warning">Pending</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('quotations.edit', $quotation) }}" data-bs-toggle="tooltip"
                                    title="Edit Quotation">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                </a>
                                <form action="{{ route('quotations.destroy', $quotation) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-bs-toggle="tooltip" title="Delete Quotation"
                                        onclick="return confirm('Are you sure you want to delete this quotation?')">
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
                                        <i data-lucide="file-text" class="avatar-icon text-muted"></i>
                                    </div>
                                </div>
                                <span class="d-block mb-2">No quotations found</span>
                                <span class="fs-12 d-block fw-normal text-muted mb-3">Get started by creating your first
                                    quotation</span>
                                <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                                    <i data-lucide="plus" class="me-2"></i>
                                    Add New Quotation
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($quotations->hasPages())
    <div class="card-footer">
        <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
            @if ($quotations->onFirstPage())
            <li><a href="javascript:void(0);" class="disabled"><i data-lucide="arrow-left"></i></a></li>
            @else
            <li><a href="{{ $quotations->previousPageUrl() }}"><i data-lucide="arrow-left"></i></a></li>
            @endif

            @foreach ($quotations->getUrlRange(1, $quotations->lastPage()) as $page => $url)
            @if ($page == $quotations->currentPage())
            <li><a href="javascript:void(0);" class="active">{{ $page }}</a></li>
            @else
            <li><a href="{{ $url }}">{{ $page }}</a></li>
            @endif
            @endforeach

            @if ($quotations->hasMorePages())
            <li><a href="{{ $quotations->nextPageUrl() }}"><i data-lucide="arrow-right"></i></a></li>
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