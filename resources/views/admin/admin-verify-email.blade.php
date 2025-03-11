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

  <title>Verify Admin Email | Easy Learning</title>
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
                <img src="{{ asset('backend/assets/images/login-images/login-cover.svg') }}" 
                     class="img-fluid auth-img-cover-login" 
                     width="650" 
                     alt="Verify Email Cover" />
              </div>
            </div>
          </div>

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

                      <form method="POST" action="{{ route('admin.verification.send') }}">
                        @csrf
                        <div class="d-grid mb-3">
                          <button type="submit" class="btn btn-primary">
                            Resend Verification Email
                          </button>
                        </div>
                      </form>

                      <p class="mb-0">Already verified? 
                        <a href="{{ route('admin.login') }}">Login here</a>
                      </p>
                    </div>
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
</body>

</html>