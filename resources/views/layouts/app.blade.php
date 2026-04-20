{{-- admin app.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - BoardMeet</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo/favicon.png') }}" type="image/x-icon">

    <link id="bootstrap-css" rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/conca.css') }}">

    @stack('styles')
    <!-- app sidebar end -->
    <script>
        /**
         * conca-sidebar.js may collapse all dropdowns on init.
         * This script runs AFTER it and re-opens any dropdown
         * that Blade already marked as .open (active route match).
         */
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.app-sidebar-menu-item.has-dropdown.open').forEach(function(li) {
                var submenu = li.querySelector('.app-sidebar-submenu');
                if (submenu) {
                    // Force visible regardless of what the theme JS did
                    submenu.style.display = 'block';
                }
            });
        });
    </script>
    <style>
        /* ─── Sidebar Active & Hover States ─────────────────────────────────── */

        /* Single menu item — active */
        .app-sidebar-menu-item.active>.menu-link {
            color: var(--bs-primary, #3b82f6);
            background-color: rgba(59, 130, 246, 0.08);
            border-radius: 6px;
        }

        /* Single menu item — active icon color */
        .app-sidebar-menu-item.active>.menu-link .menu-icon svg {
            color: var(--bs-primary, #3b82f6);
            stroke: var(--bs-primary, #3b82f6);
        }

        /* Single menu item — active left border accent */
        .app-sidebar-menu-item.active>.menu-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background-color: var(--bs-primary, #3b82f6);
            border-radius: 0 3px 3px 0;
        }

        /* Make sure menu-link is positioned for the ::before to work */
        .app-sidebar-menu .menu-link {
            position: relative;
        }

        /* ─── Hover State ────────────────────────────────────────────────────── */
        .app-sidebar-menu-item>.menu-link:hover {
            color: var(--bs-primary, #3b82f6);
            background-color: rgba(59, 130, 246, 0.05);
            border-radius: 6px;
        }

        .app-sidebar-menu-item>.menu-link:hover .menu-icon svg {
            color: var(--bs-primary, #3b82f6);
            stroke: var(--bs-primary, #3b82f6);
        }

        /* ─── Dropdown Parent — active + open ───────────────────────────────── */
        .app-sidebar-menu-item.has-dropdown.active>.menu-link {
            color: var(--bs-primary, #3b82f6);
            background-color: rgba(59, 130, 246, 0.08);
            border-radius: 6px;
        }

        .app-sidebar-menu-item.has-dropdown.active>.menu-link .menu-icon svg,
        .app-sidebar-menu-item.has-dropdown.active>.menu-link .menu-arrow svg {
            color: var(--bs-primary, #3b82f6);
            stroke: var(--bs-primary, #3b82f6);
        }

        /* Arrow rotation when dropdown is open */
        .app-sidebar-menu-item.has-dropdown.open>.menu-link .menu-arrow {
            transform: rotate(90deg);
            transition: transform 0.2s ease;
        }

        .app-sidebar-menu-item.has-dropdown>.menu-link .menu-arrow {
            transition: transform 0.2s ease;
        }

        /* ─── Submenu Child — active ─────────────────────────────────────────── */
        .app-sidebar-submenu .app-sidebar-menu-item.active>.menu-link {
            color: var(--bs-primary, #3b82f6);
            font-weight: 600;
        }

        .app-sidebar-submenu .app-sidebar-menu-item.active>.menu-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background-color: var(--bs-primary, #3b82f6);
            border-radius: 0 3px 3px 0;
        }

        /* Submenu child hover */
        .app-sidebar-submenu .app-sidebar-menu-item>.menu-link:hover {
            color: var(--bs-primary, #3b82f6);
            background-color: rgba(59, 130, 246, 0.05);
        }

        /* ─── Dark theme support ─────────────────────────────────────────────── */
        [data-theme="dark"] .app-sidebar-menu-item.active>.menu-link,
        [data-theme="dark"] .app-sidebar-menu-item.has-dropdown.active>.menu-link {
            background-color: rgba(59, 130, 246, 0.15);
        }
    </style>
</head>

<body>
    <div class="app-main">
        <div id="app-wrapper" class="app-wrapper d-flex flex-column align-items-stretch min-vh-100">
            @include('layouts.partials.sidebar')
            @include('layouts.partials.header')

            <div class="app-content-wrapper pt-13 pb-13 px-5">
                <div class="container-fluid">
                    <div class="page-content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- global js scripts for all pages --}}
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.js') }}"></script>

    {{-- app js  --}}
    <script src="{{ asset('assets/js/conca-sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/conca.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
    $(document).ready(function () {

        $('.datatable').DataTable({
            responsive: true,
            searching: true,   // search box
            ordering: true,    // column sorting
            paging: true,      // pagination
            info: true,        // showing info text
            lengthChange: true
        });

    });
    </script>
    @stack('scripts')
</body>

</html>
