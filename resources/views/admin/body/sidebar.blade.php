<div class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
    <div>
     <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset('frontend/images/logo2.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px; filter: brightness(110%);"> <!-- Taille augmentée et clarté améliorée -->
    </a>
    </div>
    <div>
      <h4 class="logo-text">Admin</h4>
    </div>
    <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
  </div>
  <!--navigation-->
  <ul class="metismenu" id="menu">
    <li>
      <a href="{{ route('admin.dashboard') }}">
        <div class="parent-icon"><i class='bx bx-home-alt'></i></div>
        <div class="menu-title">Dashboard</div>
      </a>
    </li>

    <li class="menu-label">UI Elements</li>

    <li>
      <a href="javascript:;" class="has-arrow">
        <div class="parent-icon"><i class='bx bx-cart'></i></div>
        <div class="menu-title">Manage Category</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.categories.index') }}"><i class='bx bx-radio-circle'></i>All Category</a></li>
        <li><a href="{{ route('admin.subcategories.index') }}"><i class='bx bx-radio-circle'></i>All SubCategory</a></li>
      </ul>
    </li>
    
    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Instructor</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.instructors.index') }}"><i class='bx bx-radio-circle'></i>All Instructor</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Courses</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.courses.index') }}"><i class='bx bx-radio-circle'></i>All Courses</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Coupon</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Coupon</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Setting</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>Manage SMTP</a></li>
        <li><a href="#"><i class='bx bx-radio-circle'></i>Site Setting</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Orders</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Orders</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Report</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>Report View</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Review</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.pending.review') }}"><i class='bx bx-radio-circle'></i>Pending Review</a></li>
        <li><a href="{{ route('admin.active.review') }}"><i class='bx bx-radio-circle'></i>Active Review</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage All User</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>All User</a></li>
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Instructor</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i></div>
        <div class="menu-title">Manage Blog</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>Blog Category</a></li>
        <li><a href="#"><i class='bx bx-radio-circle'></i>Blog Post</a></li>
      </ul>
    </li>

    <li class="menu-label">Role & Permission</li>
    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class="bx bx-line-chart"></i></div>
        <div class="menu-title">Role & Permission</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Permission</a></li>
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Roles</a></li>
        <li><a href="#"><i class='bx bx-radio-circle'></i>Role In Permission</a></li>
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Role In Permission</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class="bx bx-line-chart"></i></div>
        <div class="menu-title">Manage Admin</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Admin</a></li>
      </ul>
    </li>
  </ul>
  <!--end navigation-->
</div>