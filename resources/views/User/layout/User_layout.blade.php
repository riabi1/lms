<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('frontend/images/favicon.png') }}" type="image/png" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Plugins -->
    <link href="{{ asset('backend/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />

    <!-- Loader -->
    <link href="{{ asset('backend/assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('backend/assets/js/pace.min.js') }}"></script>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet">

    <!-- Theme Styles -->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/header-colors.css') }}" />

    <!-- Toastr -->
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
        .sidebar-wrapper.sidebar-collapsed .logo-text,
        .sidebar-wrapper.sidebar-collapsed .badge {
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
    </style>

    <!-- Page-specific styles -->
    @stack('styles')

    <title>@yield('title', 'User Dashboard')</title>
</head>

<body>
    <!-- Wrapper -->
    <div class="wrapper">
        <!-- Sidebar Wrapper -->
        @include('User.body.sidebar')
        <!-- End Sidebar Wrapper -->

        <!-- Start Header -->
        @include('User.body.header')
        <!-- End Header -->

        <!-- Start Page Wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                @yield('userdashboard')
            </div>
        </div>
        <!-- End Page Wrapper -->

        <!-- Footer -->
        @include('User.body.footer')

        <!-- Start Overlay -->
        <div class="overlay toggle-icon"></div>
        <!-- End Overlay -->

        <!-- Start Back To Top Button -->
        <a href="javascript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!-- End Back To Top Button -->
    </div>
    <!-- End Wrapper -->

    <!-- Bootstrap JS -->
    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- jQuery (already in head) -->
    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>

    <!-- Plugins -->
    <script src="{{ asset('backend/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>

    <!-- App JS -->
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    <script src="{{ asset('backend/assets/js/validate.min.js') }}"></script>

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        @if(Session::has('message'))
        var type = "{{ Session::get('alert-type','info') }}"
        switch(type){
            case 'info':
                toastr.info(" {{ Session::get('message') }} ");
                break;
            case 'success':
                toastr.success(" {{ Session::get('message') }} ");
                break;
            case 'warning':
                toastr.warning(" {{ Session::get('message') }} ");
                break;
            case 'error':
                toastr.error(" {{ Session::get('message') }} ");
                break; 
        }
        @endif 
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Laravel Echo and Reverb -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.0.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.min.js"></script>
    <script>
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: '{{ env('REVERB_HOST', 'localhost') }}',
            wsPort: '{{ env('REVERB_PORT', 8080) }}',
            wssPort: '{{ env('REVERB_PORT', 8080) }}',
            scheme: '{{ env('REVERB_SCHEME', 'http') }}',
            enabledTransports: ['ws', 'wss'],
            forceTLS: '{{ env('REVERB_SCHEME', 'http') === 'https' }}',
            disableStats: true,
        });
        console.log('Echo initialized for Reverb');
    </script>

    <!-- Sidebar Toggle and MetisMenu -->
    <script>
        $(document).ready(function() {
            // Initialize MetisMenu
            $('#menu').metisMenu({
                toggle: false
            });

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

    <!-- Perfect Scrollbar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.location.pathname.includes('messages')) {
                new PerfectScrollbar('.page-content');
            }
        });
    </script>

    <!-- Debug Bootstrap Dropdowns -->
    <script>
        $(document).ready(function() {
            // Check if Bootstrap dropdown is loaded
            if (typeof $.fn.dropdown === 'undefined') {
                console.error('Bootstrap dropdown plugin is not loaded');
            } else {
                console.log('Bootstrap dropdown plugin is loaded');
            }

            // Log dropdown clicks
            $('.dropdown-toggle').on('click', function() {
                console.log('Dropdown toggle clicked:', $(this).attr('id'));
            });

            // Manually initialize Bootstrap dropdowns
            $('.dropdown-toggle').each(function() {
                new bootstrap.Dropdown(this);
                console.log('Initialized Bootstrap dropdown:', $(this).attr('id'));
            });
        });
    </script>

    <!-- Custom Scripts -->
    @include('frontend.body.script')
    @stack('scripts')
</body>

</html>