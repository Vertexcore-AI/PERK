@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <div class="row">
        <div class="col-12">
            <x-ui.card title="Welcome to Perk Enterprises" subtitle="Auto Parts Inventory & Sales Management System">
                <div class="text-center">
                    <h1 class="display-4 mb-4">Welcome to Perk Enterprises</h1>
                    <p class="lead">Your complete auto parts management solution</p>

                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i data-lucide="package" class="text-primary mb-2" style="width: 48px; height: 48px;"></i>
                                    <h5>Inventory</h5>
                                    <p class="text-muted">Manage stock & GRN</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i data-lucide="shopping-cart" class="text-success mb-2" style="width: 48px; height: 48px;"></i>
                                    <h5>Sales & POS</h5>
                                    <p class="text-muted">Process sales & returns</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i data-lucide="file-text" class="text-warning mb-2" style="width: 48px; height: 48px;"></i>
                                    <h5>Quotations</h5>
                                    <p class="text-muted">Create quotes & invoices</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <i data-lucide="bar-chart-3" class="text-info mb-2" style="width: 48px; height: 48px;"></i>
                                    <h5>Reports</h5>
                                    <p class="text-muted">Analytics & insights</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-ui.button variant="primary" size="lg" icon="arrow-right" class="mt-4">
                        <a href="{{ url('/dashboard') }}" class="text-decoration-none text-white">
                            Go to Dashboard
                        </a>
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection