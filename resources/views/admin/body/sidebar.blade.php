<!-- resources/views/admin/body/sidebar.blade.php -->
<div class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
    <div>
      <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset('frontend/images/logo2.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px; filter: brightness(110%);">
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

    <li class="menu-label">Management Tools</li>

    <li>
      <a href="javascript:;" class="has-arrow">
        <div class="parent-icon"><i class='bx bx-folder'></i></div>
        <div class="menu-title">Manage Category & Subcategory</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.categories.index') }}"><i class='bx bx-radio-circle'></i>All Category</a></li>
        <li><a href="{{ route('admin.subcategories.index') }}"><i class='bx bx-radio-circle'></i>All SubCategory</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-user'></i></div>
        <div class="menu-title">All Users</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.instructors.index') }}"><i class='bx bx-radio-circle'></i>All Instructor</a></li>
        <li><a href="{{ route('admin.users.index') }}"><i class='bx bx-radio-circle'></i>All Students</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-book'></i></div>
        <div class="menu-title">Approve Courses</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.courses.index') }}"><i class='bx bx-radio-circle'></i>All Courses</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-purchase-tag-alt'></i></div>
        <div class="menu-title">View Coupon</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.coupon.index') }}"><i class='bx bx-radio-circle'></i>All Coupon</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-cart'></i></div>
        <div class="menu-title">Orders</div>
      </a>
      <ul class="mm-collapse">
        <li>
          <a href="{{ route('admin.orders.index') }}">
            <i class='bx bx-radio-circle'></i>All Orders
          </a>
        </li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-star'></i></div>
        <div class="menu-title">Approve Reviews</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.pending.review') }}"><i class='bx bx-radio-circle'></i>Pending Review</a></li>
        <li><a href="{{ route('admin.active.review') }}"><i class='bx bx-radio-circle'></i>Active Review</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;" aria-expanded="false">
        <div class="parent-icon"><i class='bx bx-wrench'></i></div>
        <div class="menu-title">Settings</div>
      </a>
      <ul class="mm-collapse">
        <li>
          <a href="{{ route('admin.site.settings') }}">
            <i class='bx bx-radio-circle'></i>Site Settings
          </a>
        </li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-bar-chart-square'></i></div>
        <div class="menu-title">Reports</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.report-categories.index') }}"><i class='bx bx-radio-circle'></i>Report Categories</a></li>
      </ul>
    </li>

    <li>
      <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
        <div class="parent-icon"><i class='bx bx-user-check'></i></div>
        <div class="menu-title">User Reports</div>
      </a>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-news'></i></div>
        <div class="menu-title">Blogs</div>
      </a>
      <ul class="mm-collapse">
        <li class="nav-item">
          <a href="{{ route('admin.blog-categories.index') }}" class="nav-link {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">
            <i class='bx bx-radio-circle'></i> Blog Categories
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.blog-posts.index') }}" class="nav-link {{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}">
            <i class='bx bx-radio-circle'></i> Blog Posts
          </a>
        </li>
        <li><a href="{{ route('admin.comments.index') }}"><i class='bx bx-radio-circle'></i>Blog Comments</a></li>
      </ul>
    </li>

    <li class="menu-label">Role & Permission</li>
    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-shield'></i></div>
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
        <div class="parent-icon"><i class='bx bx-user-plus'></i></div>
        <div class="menu-title">Manage Admin</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Admin</a></li>
      </ul>
    </li>
  </ul>
  <!--end navigation-->
</div>