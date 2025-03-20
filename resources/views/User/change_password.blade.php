@extends('frontend.master')
@section('home')
@section('title')
Change Password | Easy Learning
@endsection

<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area section-padding img-bg-2">
    <div class="overlay"></div>
    <div class="container">
        <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
            <div class="section-heading">
                <h2 class="section__title text-white">Reset Password</h2>
            </div>
            <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Pages</li>
                <li>Reset Password</li>
            </ul>
        </div><!-- end breadcrumb-content -->
    </div><!-- end container -->
</section><!-- end breadcrumb-area -->
<!-- ================================
    END BREADCRUMB AREA
================================= -->

<!-- ================================
       START CONTACT AREA
================================= -->
<section class="contact-area section--padding position-relative">
    <span class="ring-shape ring-shape-1"></span>
    <span class="ring-shape ring-shape-2"></span>
    <span class="ring-shape ring-shape-3"></span>
    <span class="ring-shape ring-shape-4"></span>
    <span class="ring-shape ring-shape-5"></span>
    <span class="ring-shape ring-shape-6"></span>
    <span class="ring-shape ring-shape-7"></span>
    <div class="container">
        <div class="row">
            <div class="col-lg-7 mx-auto">
                <div class="card card-item">
                    <div class="card-body">
                        <h3 class="card-title text-center fs-24 lh-35 pb-4">Set a New Password</h3>
                        <div class="section-block"></div>
                        
                        <div class="pt-4">
                            @if (session('status'))
                                <div class="alert alert-success mb-3">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="input-box">
                                    <label class="label-text">Email</label>
                                    <div class="form-group">
                                        <input class="form-control form--control" id="email" type="email" name="email" value="{{ $email }}" required>
                                        <span class="la la-envelope input-icon"></span>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!-- end input-box -->
                                <div class="input-box">
                                    <label class="label-text">New Password</label>
                                    <div class="form-group">
                                        <input class="form-control form--control" id="password" type="password" name="password" placeholder="New password" required>
                                        <span class="la la-lock input-icon"></span>
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div><!-- end input-box -->
                                <div class="input-box">
                                    <label class="label-text">Confirm Password</label>
                                    <div class="form-group">
                                        <input class="form-control form--control" id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password" required>
                                        <span class="la la-lock input-icon"></span>
                                    </div>
                                </div><!-- end input-box -->

                                <div class="btn-box">
                                    <button class="btn theme-btn" type="submit">Reset Password <i class="la la-arrow-right icon ml-1"></i></button>
                                    <p class="fs-14 pt-2">Back to <a href="{{ route('login') }}" class="text-color hover-underline">Login</a></p>
                                </div><!-- end btn-box -->
                            </form>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col-lg-7 -->
        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end contact-area -->
<!-- ================================
       END CONTACT AREA
================================= -->

@endsection