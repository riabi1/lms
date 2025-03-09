@extends('frontend.master')
@section('home')
@section('title')
Instructor Forgot Password | Easy Learning
@endsection

<!-- ================================
    START BREADCRUMB AREA
================================= -->
<section class="breadcrumb-area section-padding img-bg-2">
  <div class="overlay"></div>
  <div class="container">
    <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between">
      <div class="section-heading">
        <h2 class="section__title text-white">Instructor Forgot Password</h2>
      </div>
      <ul class="generic-list-item generic-list-item-white generic-list-item-arrow d-flex flex-wrap align-items-center">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li>Instructor Pages</li>
        <li>Forgot Password</li>
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
            <h3 class="card-title text-center fs-24 lh-35 pb-4">Reset Your Instructor Password</h3>
            <div class="section-block"></div>

            <div class="pt-4">
              @if (session('status'))
              <div class="alert alert-success mb-3">
                {{ session('status') }}
              </div>
              @endif

              <p class="fs-16 pb-3 text-center">Enter your instructor email address below, and we’ll send you a link to reset your password.</p>

              <form method="POST" action="{{ route('instructor.password.email') }}">
                @csrf

                <div class="input-box">
                  <label class="label-text">Email</label>
                  <div class="form-group">
                    <input class="form-control form--control" id="email" type="email" name="email" placeholder="Your instructor email" value="{{ old('email') }}" required>
                    <span class="la la-envelope input-icon"></span>
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div><!-- end input-box -->

                <div class="btn-box">
                  <button class="btn theme-btn" type="submit">Send Reset Link <i class="la la-arrow-right icon ml-1"></i></button>
                  <p class="fs-14 pt-2">Remember your password? <a href="{{ route('instructor.login') }}" class="text-color hover-underline">Login here</a></p>
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