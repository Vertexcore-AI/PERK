<nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="index.html" class="b-brand">
                    <!-- ========   change your logo hear   ============ -->
                    <img src="assets/images/logo/Perk Enterprises.png" alt="" class="logo logo-lg" style="margin: 8px; max-width: 140px; height: auto;" />
                    <img src="assets/images/logo-abbr.png" alt="" class="logo logo-sm" style="margin: 8px;" />
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Auto Parts Management</label>
                    </li>

                    <!-- Dashboard -->
                    <li class="nxl-item">
                        <a href="{{ url('/dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i data-lucide="layout-dashboard"></i></span>
                            <span class="nxl-mtext">Dashboard</span>
                        </a>
                    </li>

                    <!-- Inventory Management -->
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i data-lucide="package"></i></span>
                            <span class="nxl-mtext">Inventory</span>
                            <span class="nxl-arrow"><i data-lucide="chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/grns') }}">GRN Entry</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/inventory') }}">Stock Overview</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/items') }}">Items Registry</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/batches') }}">Batch Tracking</a></li>
                        </ul>
                    </li>

                    <!-- Sales & POS -->
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i data-lucide="shopping-cart"></i></span>
                            <span class="nxl-mtext">Sales & POS</span>
                            <span class="nxl-arrow"><i data-lucide="chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/pos') }}">Point of Sale</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/sales') }}">Sales History</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/returns') }}">Returns</a></li>
                        </ul>
                    </li>

                    <!-- Customers -->
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i data-lucide="users"></i></span>
                            <span class="nxl-mtext">Customers</span>
                            <span class="nxl-arrow"><i data-lucide="chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/customers') }}">Customer List</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/customers/create') }}">Add Customer</a></li>
                        </ul>
                    </li>

                    <!-- Quotations -->
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i data-lucide="file-text"></i></span>
                            <span class="nxl-mtext">Quotations</span>
                            <span class="nxl-arrow"><i data-lucide="chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/quotations/create') }}">Create Quote</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/quotations') }}">Manage Quotes</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/invoices') }}">Invoices</a></li>
                        </ul>
                    </li>

                    <!-- Reports -->
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i data-lucide="bar-chart-3"></i></span>
                            <span class="nxl-mtext">Reports</span>
                            <span class="nxl-arrow"><i data-lucide="chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/reports/stock') }}">Stock Reports</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/reports/sales') }}">Sales Analytics</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/reports/financial') }}">Financial Reports</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/reports/vendors') }}">Vendor Reports</a></li>
                        </ul>
                    </li>

                    <!-- Settings -->
                    <li class="nxl-item nxl-caption">
                        <label>Settings</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i data-lucide="settings"></i></span>
                            <span class="nxl-mtext">Configuration</span>
                            <span class="nxl-arrow"><i data-lucide="chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/vendors') }}">Vendors</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/categories') }}">Categories</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ url('/stores') }}">Stores & Bins</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>