<header class="header-menu-area bg-white">
  <div class="header-top pr-150px pl-150px border-bottom border-bottom-gray py-1">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <div class="header-widget">
            <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14">
              <li class="d-flex align-items-center pr-3 mr-3 border-right border-right-gray"><i class="la la-phone mr-1"></i><a href="tel:00123456789"> (00) 123 456 789</a></li>
              <li class="d-flex align-items-center"><i class="la la-envelope-o mr-1"></i><a href="mailto:contact@aduca.com"> contact@aduca.com</a></li>
            </ul>
          </div><!-- end header-widget -->
        </div><!-- end col-lg-6 -->
        <div class="col-lg-6">
          <div class="header-widget d-flex flex-wrap align-items-center justify-content-end">
            <div class="theme-picker d-flex align-items-center">
              <button class="theme-picker-btn dark-mode-btn" title="Dark mode">
                <svg id="moon" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
              </button>
              <button class="theme-picker-btn light-mode-btn" title="Light mode">
                <svg id="sun" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="5"></circle>
                  <line x1="12" y1="1" x2="12" y2="3"></line>
                  <line x1="12" y1="21" x2="12" y2="23"></line>
                  <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                  <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                  <line x1="1" y1="12" x2="3" y2="12"></line>
                  <line x1="21" y1="12" x2="23" y2="12"></line>
                  <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                  <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
              </button>
            </div>
            <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14 border-left border-left-gray pl-3 ml-3">
              <li class="d-flex align-items-center pr-3 mr-3 border-right border-right-gray">
                <i class="la la-sign-in mr-1"></i>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Login</a>
                <ul class="dropdown-menu">
                  <li><a href="{{ route('login') }}">User Login</a></li>
                  <li><a href="{{ route('instructor.login') }}">Instructor Login</a></li>
                </ul>
              </li>
              <li class="d-flex align-items-center">
                <i class="la la-user mr-1"></i>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Register</a>
                <ul class="dropdown-menu">
                  <li><a href="{{ route('register') }}">User Register</a></li>
                  <li><a href="{{ route('instructor.register') }}">Instructor Register</a></li>
                </ul>
              </li>
            </ul>
          </div><!-- end header-widget -->
        </div><!-- end col-lg-6 -->
      </div><!-- end row -->
    </div><!-- end container-fluid -->
  </div><!-- end header-top -->
  <!-- Rest of your header code remains unchanged -->
</header><!-- end header-menu-area -->