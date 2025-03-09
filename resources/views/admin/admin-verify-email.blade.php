@extends('frontend.master')
@section('home')
@section('title')
Verify Admin Email | Easy Learning
@endsection

<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area section-padding img-bg-2">
  <div class="overlay"></div>
  <div class="container">
    <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
      <div class="section-heading">
        <h2 class="section__title text-white">Verify Your Admin Email</h2>
      </div>
      <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li>Admin Pages</li>
        <li>Verify Email</li>
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
            <h3 class="card-title text-center fs-24 lh-35 pb-4">Verify Your Admin Email Address</h3>
            <div class="section-block"></div>

            <div class="text-center pt-4">
              <p class="fs-16 pb-3">We've sent a verification link to your email address. Please check your inbox (and spam/junk folder) to verify your admin account.</p>

              @if (session('status') === 'verification-link-sent')
              <div class="alert alert-success mb-3">
                A new verification link has been sent to your email address.
              </div>
              @endif

              <form method="POST" action="{{ route('admin.verification.send') }}" class="pt-2">
                @csrf
                <button class="btn theme-btn" type="submit">
                  Resend Verification Email <i class="la la-arrow-right icon ml-1"></i>
                </button>
              </form>

              <p class="fs-14 pt-4">Already verified? <a href="{{ route('admin.login') }}" class="text-color hover-underline">Login here</a></p>
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