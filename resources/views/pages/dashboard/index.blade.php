@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    <div class="d-flex d-md-none">
        <a href="javascript:void(0)" class="page-header-right-close-toggle">
            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15,18 9,12 15,6"></polyline></svg>
            <span>Back</span>
        </a>
    </div>
    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
        <div id="reportrange" class="reportrange-picker d-flex align-items-center">
            <span class="reportrange-picker-field"></span>
        </div>
        <div class="dropdown filter-dropdown">
            <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3"></polygon></svg>
                <span>Filter</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <div class="dropdown-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="Role" checked="checked" />
                        <label class="custom-control-label c-pointer" for="Role">Role</label>
                    </div>
                </div>
                <div class="dropdown-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="Team" checked="checked" />
                        <label class="custom-control-label c-pointer" for="Team">Team</label>
                    </div>
                </div>
                <div class="dropdown-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="Email" checked="checked" />
                        <label class="custom-control-label c-pointer" for="Email">Email</label>
                    </div>
                </div>
                <div class="dropdown-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="Member" checked="checked" />
                        <label class="custom-control-label c-pointer" for="Member">Member</label>
                    </div>
                </div>
                <div class="dropdown-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="Recommendation" checked="checked" />
                        <label class="custom-control-label c-pointer" for="Recommendation">Recommendation</label>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="javascript:void(0);" class="dropdown-item">
                    <svg class="me-3" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Create New</span>
                </a>
                <a href="javascript:void(0);" class="dropdown-item">
                    <svg class="me-3" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3"></polygon></svg>
                    <span>Manage Filter</span>
                </a>
            </div>
        </div>
    </div>
    <div class="d-md-none d-flex align-items-center">
        <a href="javascript:void(0)" class="page-header-right-open-toggle">
            <svg class="fs-20" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="10" x2="7" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="7" y2="18"></line></svg>
        </a>
    </div>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <!-- [Invoices Awaiting Payment] start -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-gray-200">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark"><span class="counter">45</span>/<span class="counter">76</span></div>
                                    <h3 class="fs-13 fw-semibold text-truncate-1-line">Invoices Awaiting Payment</h3>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </a>
                        </div>
                        <div class="pt-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0);" class="fs-12 fw-medium text-muted text-truncate-1-line">Invoices Awaiting </a>
                                <div class="w-100 text-end">
                                    <span class="fs-12 text-dark">$5,569</span>
                                    <span class="fs-11 text-muted">(56%)</span>
                                </div>
                            </div>
                            <div class="progress mt-2 ht-3">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 56%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [Invoices Awaiting Payment] end -->
            
            <!-- [Converted Leads] start -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-gray-200">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 16.1A5 5 0 0 1 5.9 20M2 12.05A9 9 0 0 1 9.95 20M2 8V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-6"></path><line x1="2" y1="20" x2="2.01" y2="20"></line></svg>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark"><span class="counter">48</span>/<span class="counter">86</span></div>
                                    <h3 class="fs-13 fw-semibold text-truncate-1-line">Converted Leads</h3>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                            </a>
                        </div>
                        <div class="pt-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0);" class="fs-12 fw-medium text-muted text-truncate-1-line">Converted Leads </a>
                                <div class="w-100 text-end">
                                    <span class="fs-12 text-dark">52 Completed</span>
                                    <span class="fs-11 text-muted">(63%)</span>
                                </div>
                            </div>
                            <div class="progress mt-2 ht-3">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 63%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [Converted Leads] end -->
            
            <!-- [Projects In Progress] start -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-gray-200">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark"><span class="counter">16</span>/<span class="counter">20</span></div>
                                    <h3 class="fs-13 fw-semibold text-truncate-1-line">Projects In Progress</h3>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="">
                                <i class="feather-more-vertical"></i>
                            </a>
                        </div>
                        <div class="pt-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0);" class="fs-12 fw-medium text-muted text-truncate-1-line">Projects In Progress </a>
                                <div class="w-100 text-end">
                                    <span class="fs-12 text-dark">16 Completed</span>
                                    <span class="fs-11 text-muted">(78%)</span>
                                </div>
                            </div>
                            <div class="progress mt-2 ht-3">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 78%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [Projects In Progress] end -->
            
            <!-- [Conversion Rate] start -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-gray-200">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"></polyline></svg>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark"><span class="counter">46.59</span>%</div>
                                    <h3 class="fs-13 fw-semibold text-truncate-1-line">Conversion Rate</h3>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="">
                                <i class="feather-more-vertical"></i>
                            </a>
                        </div>
                        <div class="pt-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0);" class="fs-12 fw-medium text-muted text-truncate-1-line"> Conversion Rate </a>
                                <div class="w-100 text-end">
                                    <span class="fs-12 text-dark">$2,254</span>
                                    <span class="fs-11 text-muted">(46%)</span>
                                </div>
                            </div>
                            <div class="progress mt-2 ht-3">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 46%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [Conversion Rate] end -->
            
            <!-- [Payment Records] start -->
            <div class="col-xxl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Payment Record</h5>
                        <div class="card-header-action">
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
                                        <i class="feather-more-vertical"></i>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:void(0);" class="dropdown-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="m12 2 3 10h7l-5 4 2 8-7-6-7 6 2-8-5-4h7l3-10z"></path></svg>New</a>
                                    <a href="javascript:void(0);" class="dropdown-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>Event</a>
                                    <a href="javascript:void(0);" class="dropdown-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="m13.73 21a2 2 0 0 1-3.46 0"></path></svg>Snoozed</a>
                                    <a href="javascript:void(0);" class="dropdown-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c0 1 1 2 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>Deleted</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);" class="dropdown-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>Settings</a>
                                    <a href="javascript:void(0);" class="dropdown-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle><line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line><line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line><line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line><line x1="14.83" y1="9.17" x2="18.36" y2="5.64"></line><line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line></svg>Tips & Tricks</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body custom-card-action p-0">
                        <div id="payment-records-chart"></div>
                    </div>
                    <div class="card-footer">
                        <div class="row g-4">
                            <div class="col-lg-3">
                                <div class="p-3 border border-dashed rounded">
                                    <div class="fs-12 text-muted mb-1">Awaiting</div>
                                    <h6 class="fw-bold text-dark">$5,486</h6>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 81%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="p-3 border border-dashed rounded">
                                    <div class="fs-12 text-muted mb-1">Completed</div>
                                    <h6 class="fw-bold text-dark">$9,275</h6>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 82%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="p-3 border border-dashed rounded">
                                    <div class="fs-12 text-muted mb-1">Rejected</div>
                                    <h6 class="fw-bold text-dark">$3,868</h6>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 68%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="p-3 border border-dashed rounded">
                                    <div class="fs-12 text-muted mb-1">Revenue</div>
                                    <h6 class="fw-bold text-dark">$50,668</h6>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-dark" role="progressbar" style="width: 75%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [Payment Records] end -->
            
            <!-- [Total Sales] start -->
            <div class="col-xxl-4">
                <div class="card stretch stretch-full overflow-hidden">
                    <div class="bg-primary text-white">
                        <div class="p-4">
                            <span class="badge bg-light text-primary text-dark float-end">12%</span>
                            <div class="text-start">
                                <h4 class="text-reset">30,569</h4>
                                <p class="text-reset m-0">Total Sales</p>
                            </div>
                        </div>
                        <div id="total-sales-color-graph"></div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="hstack gap-3">
                                <div class="avatar-image avatar-lg p-2 rounded">
                                    <img class="img-fluid" src="{{ asset('assets/images/brand/shopify.png') }}" alt="" />
                                </div>
                                <div>
                                    <a href="javascript:void(0);" class="d-block">Shopify eCommerce Store</a>
                                    <span class="fs-12 text-muted">Development</span>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">$1200</div>
                                <div class="fs-12 text-end">6 Projects</div>
                            </div>
                        </div>
                        <hr class="border-dashed my-3" />
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="hstack gap-3">
                                <div class="avatar-image avatar-lg p-2 rounded">
                                    <img class="img-fluid" src="{{ asset('assets/images/brand/app-store.png') }}" alt="" />
                                </div>
                                <div>
                                    <a href="javascript:void(0);" class="d-block">iOS Apps Development</a>
                                    <span class="fs-12 text-muted">Development</span>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">$1450</div>
                                <div class="fs-12 text-end">3 Projects</div>
                            </div>
                        </div>
                        <hr class="border-dashed my-3" />
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="hstack gap-3">
                                <div class="avatar-image avatar-lg p-2 rounded">
                                    <img class="img-fluid" src="{{ asset('assets/images/brand/figma.png') }}" alt="" />
                                </div>
                                <div>
                                    <a href="javascript:void(0);" class="d-block">Figma Dashboard Design</a>
                                    <span class="fs-12 text-muted">UI/UX Design</span>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">$1250</div>
                                <div class="fs-12 text-end">5 Projects</div>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:void(0);" class="card-footer fs-11 fw-bold text-uppercase text-center py-4">Full Details</a>
                </div>
            </div>
            <!-- [Total Sales] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('vendor-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
@endpush

@push('vendor-scripts')
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
@endpush