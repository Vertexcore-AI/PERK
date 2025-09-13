<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="@yield('meta_description', '')" />
    <meta name="keywords" content="@yield('meta_keywords', '')" />
    <meta name="author" content="WRAPCODERS" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - Duralux</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    @stack('vendor-styles')
    
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />
    @stack('styles')

    <!-- Sticky Footer CSS -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .nxl-container {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
        }

        .nxl-content {
            flex: 1 0 auto;
            padding-bottom: 2rem;
        }

        /* Ensure footer always sticks to bottom */
        .nxl-container > .nxl-footer,
        .nxl-container > footer,
        [class*="footer"] {
            margin-top: auto;
            flex-shrink: 0;
        }
    </style>
    
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <!-- Navigation Menu -->
    @include('components.navbar')
    
    <!-- Header -->
    @include('components.header')
    
    <!-- Main Content -->
    <main class="nxl-container">
        <div class="nxl-content">
            @hasSection('page-header')
                @yield('page-header')
            @else
                <!-- Default page header -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10">@yield('page-title', 'Dashboard')</h5>
                        </div>
                        @hasSection('breadcrumb')
                            <ul class="breadcrumb">
                                @yield('breadcrumb')
                            </ul>
                        @endif
                    </div>
                    @hasSection('page-actions')
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">
                                @yield('page-actions')
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- Page Content -->
            @yield('content')
        </div>
        
        <!-- Footer -->
        @include('components.footer')
    </main>
    
    <!-- Theme Customizer (if needed) -->
    @yield('customizer')
    
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    @stack('vendor-scripts')
    
    <!-- Common Init -->
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    
    <!-- Page Scripts -->
    @stack('scripts')
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    <!-- Theme Customizer -->
    @hasSection('customizer')
        <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    @endif
</body>

</html>