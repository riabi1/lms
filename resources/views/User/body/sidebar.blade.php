<div class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
    <div>
     <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset('frontend/images/logo2.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px; filter: brightness(110%);"> <!-- Taille augmentée et clarté améliorée -->
    </a>
    </div>
    <div>
      <h4 class="logo-text">Student</h4>
    </div>
    <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
  </div>
  <!--navigation-->
  <ul class="metismenu" id="menu">
    <li>
      <a href="{{ route('dashboard') }}">
        <div class="parent-icon"><i class='bx bx-home-alt'></i></div>
        <div class="menu-title">Dashboard</div>
      </a>
    </li>

    <li class="menu-label">UI Elements</li>

     <li>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">My Profile</div>
            </a>
        </li>
    
  <li>
    <a href="{{ route('user.my.courses') }}">
        <div class="parent-icon"><i class='bx bx-book'></i></div>
        <div class="menu-title">My Courses</div>
    </a>
</li>

      <li>
            <a href="#">
                <div class="parent-icon"><i class='bx bx-bolt-circle'></i></div>
                <div class="menu-title">Quiz Attempts</div>
            </a>
        </li>
        <li>
            <a href="#">
                <div class="parent-icon"><i class='bx bx-bookmark'></i></div>
                <div class="menu-title">Wishlist</div>
                <span class="badge badge-info p-1 ml-2" id="wishQty">2</span>
            </a>
        </li>

     <li>
            <a href="#">
                <div class="parent-icon"><i class='bx bx-message-square-dots'></i></div>
                <div class="menu-title">Live Chat</div>
                <span class="badge badge-info p-1 ml-2">2</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.reviews.index') }}" class="{{ request()->routeIs('user.reviews.index') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-star'></i></div>
                <div class="menu-title">Reviews</div>
            </a>
        </li>

       <li>
            <a href="#">
                <div class="parent-icon"><i class='bx bx-lock-alt'></i></div>
                <div class="menu-title">Change Password</div>
            </a>
        </li>


 

 
      <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                    <div class="parent-icon"><i class='bx bx-log-out'></i></div>
                    <div class="menu-title">Logout</div>
                </a>
            </form>
        </li>
  </ul>
  <!--end navigation-->
</div>