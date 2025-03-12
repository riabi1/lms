<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon -->
  <link rel="icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}" type="image/png" />
  <!-- Plugins -->
  <link href="{{ asset('backend/assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
  <link href="{{ asset('backend/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
  <link href="{{ asset('backend/assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
  <!-- Loader -->
  <link href="{{ asset('backend/assets/css/pace.min.css') }}" rel="stylesheet" />
  <script src="{{ asset('backend/assets/js/pace.min.js') }}"></script>
  <!-- Bootstrap CSS -->
  <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link href="{{ asset('backend/assets/css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet">
  <!-- Toastr CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
  <title>Instructor Register | Easy Learning</title>
</head>

<body class="">
  <!-- Wrapper -->
  <div class="wrapper">
    <div class="section-authentication-cover">
      <div class="">
        <div class="row g-0">
          <!-- Left Section (Image) -->
          <div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">
            <div class="card shadow-none bg-transparent rounded-0 mb-0">
              <div class="card-body">
                <img src="{{ asset('backend/assets/images/login-images/login-cover.svg') }}" class="img-fluid auth-img-cover-login" width="650" alt="Instructor Register Cover" />
              </div>
            </div>
          </div>

          <!-- Right Section (Register Form) -->
          <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
            <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
              <div class="card-body p-sm-5">
                <div class="">
                  <div class="mb-3 text-center">
                    <img src="{{ asset('backend/assets/images/logo-icon.png') }}" width="60" alt="Logo">
                  </div>
                  <div class="text-center mb-4">
                    <h5>Instructor Register</h5>
                    <p class="mb-0">Create an instructor account and start teaching!</p>
                  </div>
                  <div class="form-body">
                    <form class="row g-3" method="POST" action="{{ route('instructor.register') }}">
                      @csrf

                      <!-- Name Field -->
                      <div class="col-12">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Instructor name" value="{{ old('name') }}" required>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>

                      <!-- Email Field -->
                      <div class="col-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Instructor email" value="{{ old('email') }}" required>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                      </div>

                      <!-- Password Field -->
                      <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group" id="show_hide_password">
                          <input type="password" id="password" name="password" class="form-control border-end-0 @error('password') is-invalid @enderror" placeholder="Password" required>
                          <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
                          @error('password')
                          <span class="text-danger">{{ $message }}</span>
                          @enderror
                        </div>
                      </div>

                      <!-- Confirm Password Field -->
                      <div class="col-12">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="input-group" id="show_hide_confirm_password">
                          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control border-end-0" placeholder="Confirm Password" required>
                          <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
                        </div>
                      </div>

                      <!-- Checkboxes -->
                      <div class="col-12">
                        <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="receiveCheckbox" name="receive_emails" required>
                          <label class="form-check-label" for="receiveCheckbox">Yes! I want to get the most out of LearnFlow by receiving emails with exclusive deals, personal recommendations and learning tips!</label>
                        </div>
                        <div class="form-check mb-4">
                          <input class="form-check-input" type="checkbox" id="agreeCheckbox" name="agree_terms" required>
                          <label class="form-check-label" for="agreeCheckbox">By signing up, I agree to the
                            <a href="{{ url('terms-and-conditions') }}" class="text-primary">terms and conditions</a> and
                            <a href="{{ url('privacy-policy') }}" class="text-primary">privacy policy</a>
                          </label>
                        </div>
                      </div>

                      <!-- Submit Button -->
                      <div class="col-12">
                        <div class="d-grid">
                          <button type="submit" class="btn btn-primary">Register Instructor Account</button>
                        </div>
                      </div>

                      <!-- Login Link -->
                      <div class="col-12">
                        <div class="text-center">
                          <p class="mb-0">Already have an instructor account? <a href="{{ route('instructor.login') }}">Log in</a></p>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End Row -->
      </div>
    </div>
  </div>
  <!-- End Wrapper -->

  <!-- Bootstrap JS -->
  <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
  <!-- Plugins -->
  <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('backend/assets/plugins/simplebar/js/simplebar.min.js') }}" defer></script>
  <script src="{{ asset('backend/assets/plugins/metismenu/js/metisMenu.min.js') }}" defer></script>
  <script src="{{ asset('backend/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}" defer></script>
  <!-- Password Show/Hide JS -->
  <script>
    $(document).ready(function() {
      function togglePassword(inputId, iconId) {
        $(`#${iconId} a`).on('click', function(event) {
          event.preventDefault();
          var input = $(`#${inputId} input`);
          var icon = $(`#${iconId} i`);
          if (input.attr("type") === "text") {
            input.attr('type', 'password');
            icon.addClass("bx-hide").removeClass("bx-show");
          } else if (input.attr("type") === "password") {
            input.attr('type', 'text');
            icon.removeClass("bx-hide").addClass("bx-show");
          }
        });
      }
      togglePassword('show_hide_password', 'show_hide_password');
      togglePassword('show_hide_confirm_password', 'show_hide_confirm_password');
    });
  </script>
  <!-- App JS -->
  <script src="{{ asset('backend/assets/js/app.js') }}" defer></script>
  <!-- Toastr JS -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>
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
</body>

</html>