<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}" type="image/png" />

    <!-- Plugins CSS -->
    <link href="{{ asset('backend/assets/plugins/input-tags/css/tagsinput.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />

    <!-- Loader CSS -->
    <link href="{{ asset('backend/assets/css/pace.min.css') }}" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="{{ asset('backend/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/header-colors.css') }}" />

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Custom Sidebar Styles -->
    <style>
        .metismenu a {
            text-decoration: none !important;
        }
        .metismenu a:hover,
        .metismenu a:focus,
        .metismenu a.mm-active {
            text-decoration: none !important;
        }

        /* Sidebar default state */
        .sidebar-wrapper {
            width: 250px;
            transition: width 0.3s ease;
            z-index: 1000;
            position: fixed;
            left: 0;
            height: 100%;
        }

        /* Sidebar collapsed state */
        .sidebar-wrapper.sidebar-collapsed {
            width: 70px;
        }

        .sidebar-wrapper.sidebar-collapsed .menu-title,
        .sidebar-wrapper.sidebar-collapsed .menu-label,
        .sidebar-wrapper.sidebar-collapsed .logo-text {
            display: none;
        }

        .sidebar-wrapper.sidebar-collapsed .parent-icon {
            text-align: center;
        }

        .sidebar-wrapper.sidebar-collapsed .metismenu ul.mm-collapse {
            display: none;
        }

        /* Adjust page content */
        .page-wrapper {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .page-wrapper.page-expanded {
            margin-left: 70px;
        }

        /* Overlay for mobile */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
            pointer-events: none;
        }

        .overlay.active {
            display: block;
            pointer-events: auto;
        }

        /* Mobile adjustments */
        @media (max-width: 991px) {
            .sidebar-wrapper {
                width: 250px;
                position: fixed;
                left: 0;
                height: 100%;
                z-index: 1000;
                transition: width 0.3s ease;
            }

            .sidebar-wrapper.sidebar-collapsed {
                width: 70px;
            }

            .page-wrapper {
                margin-left: 250px;
            }

            .page-wrapper.page-expanded {
                margin-left: 70px;
            }
        }

        /* Chart-specific styles */
        .chart-container {
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .chart-container canvas {
            max-height: 350px !important;
            width: 100% !important;
        }
    </style>

    <title>Admin Dashboard</title>
</head>

<body>
    <!-- Wrapper -->
    <div class="wrapper">
        <!-- Sidebar -->
        @include('admin.body.sidebar')
        <!-- Header -->
        @include('admin.body.header')
        <!-- Page Content -->
        <div class="page-wrapper">
            @yield('admin')
        </div>
        <!-- Overlay -->
        <div class="overlay toggle-icon"></div>
        <!-- Back to Top -->
        <a href="javascript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!-- Footer -->
        @include('admin.body.footer')
    </div>
    <!-- End Wrapper -->

    <!-- Core JavaScript -->
    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pace.min.js') }}"></script>

    <!-- Plugins JavaScript -->
    <script src="{{ asset('backend/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/input-tags/js/tagsinput.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- App JavaScript -->
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    <script src="{{ asset('backend/assets/js/validate.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/index.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('backend/assets/js/code.js') }}"></script>

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- TinyMCE (Conditional) -->
    <script src="https://cdn.tiny.cloud/1/lcgx3yykwntdwuauyavfyoci610jl2hfqsy8ox4a8xv8nysz/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        if (document.querySelector('textarea#myeditorinstance')) {
            tinymce.init({
                selector: 'textarea#myeditorinstance',
                plugins: 'powerpaste advcode table lists checklist',
                toolbar: 'undo redo | blocks | bold italic | bullist numlist checklist | code | table',
                setup: function (editor) {
                    editor.on('init', function () {
                        console.log('TinyMCE initialized');
                    });
                }
            });
        }
    </script>

    <!-- DataTables Initialization -->
    <script>
        $(document).ready(function() {
            if ($('#example').length) {
                $('#example').DataTable();
            }
        });
    </script>

    <!-- MetisMenu and Sidebar Toggle -->
    <script>
        $(document).ready(function() {
            // Initialize MetisMenu
            if ($('#menu').length) {
                $('#menu').metisMenu({
                    toggle: false
                });
            }

            // Sidebar toggle functionality
            $('.toggle-icon').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling

                const $sidebar = $('.sidebar-wrapper');
                const $page = $('.page-wrapper');
                const $icon = $('.toggle-icon i');
                const $overlay = $('.overlay');

                if ($sidebar.hasClass('sidebar-collapsed')) {
                    // Expand sidebar
                    $sidebar.removeClass('sidebar-collapsed');
                    $page.removeClass('page-expanded');
                    $icon.removeClass('bx-arrow-forward').addClass('bx-arrow-back');
                    // Show overlay on mobile when expanded
                    if (window.innerWidth <= 991) {
                        $overlay.addClass('active');
                    }
                    console.log('Sidebar expanded');
                } else {
                    // Collapse sidebar
                    $sidebar.addClass('sidebar-collapsed');
                    $page.addClass('page-expanded');
                    $icon.removeClass('bx-arrow-back').addClass('bx-arrow-forward');
                    $overlay.removeClass('active');
                    console.log('Sidebar collapsed');
                }
            });

            // Close sidebar (collapse) when overlay is clicked (mobile only)
            $('.overlay').on('click', function() {
                if (window.innerWidth <= 991) {
                    $('.sidebar-wrapper').addClass('sidebar-collapsed');
                    $('.page-wrapper').addClass('page-expanded');
                    $('.toggle-icon i').removeClass('bx-arrow-back').addClass('bx-arrow-forward');
                    $(this).removeClass('active');
                    console.log('Overlay clicked, sidebar collapsed');
                }
            });

            // Ensure page content is clickable
            $('.page-wrapper').on('click', function(e) {
                if ($('.sidebar-wrapper').hasClass('sidebar-collapsed')) {
                    console.log('Page content clicked while sidebar collapsed');
                    // Allow default behavior, no sidebar toggle
                }
            });

            console.log('MetisMenu and Sidebar Toggle initialized');
        });
    </script>

    <!-- PerfectScrollbar Initialization -->
    <script>
        if (!window.location.pathname.includes('messages')) {
            new PerfectScrollbar('.page-wrapper');
        }
    </script>

    <!-- Toastr Notifications -->
    <script>
        @if(Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            switch (type) {
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif
    </script>

    <!-- Page-specific scripts -->
    @yield('scripts')
</body>

</html>