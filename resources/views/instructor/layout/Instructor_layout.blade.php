<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}" type="image/png"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/assets/css/pace.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/dark-theme.css') }}"/>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/semi-dark.css') }}"/>
    <link rel="stylesheet" href="{{ asset('backend/assets/css/header-colors.css') }}"/>
    <link href="{{ asset('backend/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

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
    </style>

    @stack('styles')
    <title>@yield('title', 'Instructor Dashboard')</title>
</head>
<body>
    <div class="wrapper">
        @include('instructor.body.sidebar')
        @include('instructor.body.header')
        <div class="page-wrapper">
            <div class="page-content">
                @yield('instructor')
            </div>
        </div>
        <div class="overlay toggle-icon"></div>
        <a href="javascript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        @include('instructor.body.footer')
    </div>

    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/simplebar/js/simplebar.min.js') }}" defer></script>
    <script src="{{ asset('backend/assets/plugins/metismenu/js/metisMenu.min.js') }}" defer></script>
    <script src="{{ asset('backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}" defer></script>
    <script src="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}" defer></script>
    <script src="{{ asset('backend/assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}" defer></script>
    <script src="{{ asset('backend/assets/plugins/chartjs/js/chart.js') }}" defer></script>
    <script src="{{ asset('backend/assets/js/index.js') }}" defer></script>
    <script src="{{ asset('backend/assets/js/app.js') }}" defer></script>
    <script src="{{ asset('backend/assets/js/validate.min.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('backend/assets/js/code.js') }}" defer></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
    <script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
    <script src="https://cdn.tiny.cloud/1/lcgx3yykwntdwuauyavfyoci610jl2hfqsy8ox4a8xv8nysz/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#myeditorinstance',
            plugins: 'powerpaste advcode table lists checklist',
            toolbar: 'undo redo | blocks| bold italic | bullist numlist checklist | code | table'
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#menu').metisMenu({
                toggle: false
            });
            console.log('MetisMenu initialized');
        });
    </script>
    <script>
        if (!window.location.pathname.includes('messages')) {
            new PerfectScrollbar('.page-content');
        }
    </script>
    @stack('scripts')
</body>
</html>