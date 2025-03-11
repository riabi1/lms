@extends('backend.layouts.app') <!-- Assuming there's a backend master layout -->
@section('title', 'Verify Admin Email | Easy Learning')

@section('content')
<div class="wrapper">
    <div class="section-authentication-cover">
        <div class="">
            <div class="row g-0">
                <!-- Left Section (Image) -->
                <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">
                    <div class="card shadow-none bg-transparent rounded-0 mb-0">
                        <div class="card-body">
                            <img src="{{ asset('backend/assets/images/login-images/login-cover.svg') }}" 
                                 class="img-fluid auth-img-cover-login" 
                                 width="650" 
                                 alt="Verify Email Cover" />
                        </div>
                    </div>
                </div>

                <!-- Right Section (Verification Content) -->
                <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
                    <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
                        <div class="card-body p-sm-5">
                            <div class="">
                                <div class="mb-3 text-center">
                                    <img src="{{ asset('backend/assets/images/logo-icon.png') }}" 
                                         width="60" 
                                         alt="Logo">
                                </div>
                                <div class="text-center mb-4">
                                    <h5 class="">Verify Your Admin Email</h5>
                                    <p class="mb-0">Please verify your email address</p>
                                </div>

                                <div class="form-body">
                                    <div class="text-center">
                                        <p class="mb-3">We've sent a verification link to your email address. Please check your inbox (and spam/junk folder) to verify your admin account.</p>

                                        @if (session('status') === 'verification-link-sent')
                                        <div class="alert alert-success mb-3">
                                            A new verification link has been sent to your email address.
                                        </div>
                                        @endif

                                        <form class="row g-3" method="POST" action="{{ route('admin.verification.send') }}">
                                            @csrf
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">
                                                        Resend Verification Email
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="text-center">
                                                    <p class="mb-0">Already verified? 
                                                        <a href="{{ route('admin.login') }}">Login here</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('backend/assets/js/app.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    @if(Session::has('message'))
    var type = "{{ Session::get('alert-type','info') }}"
    switch(type){
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
@endsection