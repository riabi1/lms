<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--favicon-->
  <link rel="icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}" type="image/png" />
  <!--plugins-->
  <link href="{{ asset('backend/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
  <link href="{{ asset('backend/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
  <link href="{{ asset('backend/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
  <!-- loader-->
  <link href="{{ asset('backend/assets/css/pace.min.css') }}" rel="stylesheet" />
  <script src="{{ asset('backend/assets/js/pace.min.js') }}"></script>
  <!-- Bootstrap CSS -->
  <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link href="{{ asset('backend/assets/css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

  <title>Instructor Change Password</title>
</head>

<body class="">
  <!--wrapper-->
  <div class="wrapper">
    <div class="section-authentication-cover">
      <div class="">
        <div class="row g-0">

          <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">
            <div class="card shadow-none bg-transparent rounded-0 mb-0">
              <div class="card-body">
                <img src="{{ asset('backend/assets/images/login-images/login-cover.svg') }}" class="img-fluid auth-img-cover-login" width="650" alt="" />
              </div>
            </div>
          </div>

          <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
            <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
              <div class="card-body p-sm-5">
                <div class="">
                  <div class="mb-3 text-center">
                    <img src="{{ asset('backend/assets/images/logo-icon.png') }}" width="60" alt="">
                  </div>
                  <div class="text-center mb-4">
                    <h5 class="">Instructor Change Password</h5>
                    <p class="mb-0">Set a new password for your account</p>
                  </div>
                  <div class="form-body">
                    <form class="row g-3" method="POST" action="{{ route('instructor.password.update') }}">
                      @csrf
                      <input type="hidden" name="token" value="{{ $token }}">

                      <div class="col-12">
                        <label for="inputEmailAddress" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $email }}" required>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>
                      <div class="col-12">
                        <label for="inputNewPassword" class="form-label">New Password</label>
                        <input type="password" id="inputNewPassword" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter new password" required>
                        @error('password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>
                      <div class="col-12">
                        <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" id="inputConfirmPassword" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                      </div>
                      <div class="col-12">
                        <div class="d-grid">
                          <button type="submit" class="btn btn-primary">Reset Password</button>
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="text-center">
                          <p class="mb-0">Back to <a href="{{ route('instructor.login') }}">Login</a></p>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!--end row-->
      </div>
    </div>
  </div>
  <!--end wrapper-->
  <!-- Bootstrap JS -->
  <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
  <!--plugins-->
  <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('backend/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
  <script src="{{ asset('backend/assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
  <script src="{{ asset('backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
  <!--app JS-->
  <script src="{{ asset('backend/assets/js/app.js') }}"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    @if(Session::has('message'))
    var type = "{{ Session::get('alert-type','info') }}"
    switch (type) {
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
</body>

</html>