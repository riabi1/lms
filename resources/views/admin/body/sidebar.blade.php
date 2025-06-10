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

    @canany(['admin.categories.index', 'admin.subcategories.index'])
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class='bx bx-folder'></i></div>
          <div class="menu-title">Manage Category & Subcategory</div>
        </a>
        <ul class="mm-collapse">
          @can('admin.categories.index')
            <li><a href="{{ route('admin.categories.index') }}"><i class='bx bx-radio-circle'></i>All Category</a></li>
          @endcan
          @can('admin.subcategories.index')
            <li><a href="{{ route('admin.subcategories.index') }}"><i class='bx bx-radio-circle'></i>All SubCategory</a></li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['admin.instructors.index', 'admin.users.index'])
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-user'></i></div>
          <div class="menu-title">All Users</div>
        </a>
        <ul class="mm-collapse">
          @can('admin.instructors.index')
            <li><a href="{{ route('admin.instructors.index') }}"><i class='bx bx-radio-circle'></i>All Instructor</a></li>
          @endcan
          @can('admin.users.index')
            <li><a href="{{ route('admin.users.index') }}"><i class='bx bx-radio-circle'></i>All Students</a></li>
          @endcan
        </ul>
      </li>
    @endcanany

    @can('admin.roles.index')
      <li>
        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'mm-active' : '' }}">
          <div class="parent-icon"><i class='bx bx-shield'></i></div>
          <div class="menu-title">Manage Admin Roles</div>
        </a>
      </li>
    @endcan

    @canany(['admin.courses.index', 'admin.courses.updateStatus'])
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-book'></i></div>
          <div class="menu-title">Approve Courses</div>
        </a>
        <ul class="mm-collapse">
          @can('admin.courses.index')
            <li><a href="{{ route('admin.courses.index') }}"><i class='bx bx-radio-circle'></i>All Courses</a></li>
          @endcan
        </ul>
      </li>
    @endcanany

    @can('admin.coupon.index')
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-purchase-tag-alt'></i></div>
          <div class="menu-title">View Coupon</div>
        </a>
        <ul class="mm-collapse">
          <li><a href="{{ route('admin.coupon.index') }}"><i class='bx bx-radio-circle'></i>All Coupon</a></li>
        </ul>
      </li>
    @endcan

    @can('admin.orders.index')
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-cart'></i></div>
          <div class="menu-title">Orders</div>
        </a>
        <ul class="mm-collapse">
          <li><a href="{{ route('admin.orders.index') }}"><i class='bx bx-radio-circle'></i>All Orders</a></li>
        </ul>
      </li>
    @endcan

    @canany(['admin.pending.review', 'admin.active.review'])
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-star'></i></div>
          <div class="menu-title">Approve Reviews</div>
        </a>
        <ul class="mm-collapse">
          @can('admin.pending.review')
            <li><a href="{{ route('admin.pending.review') }}"><i class='bx bx-radio-circle'></i>Pending Review</a></li>
          @endcan
          @can('admin.active.review')
            <li><a href="{{ route('admin.active.review') }}"><i class='bx bx-radio-circle'></i>Active Review</a></li>
          @endcan
        </ul>
      </li>
    @endcanany

    @can('admin.site.settings')
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-wrench'></i></div>
          <div class="menu-title">Settings</div>
        </a>
        <ul class="mm-collapse">
          <li><a href="{{ route('admin.site.settings') }}"><i class='bx bx-radio-circle'></i>Site Settings</a></li>
        </ul>
      </li>
    @endcan

    @canany(['admin.report-categories.index', 'admin.reports.index'])
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-bar-chart-square'></i></div>
          <div class="menu-title">Reports</div>
        </a>
        <ul class="mm-collapse">
          @can('admin.report-categories.index')
            <li><a href="{{ route('admin.report-categories.index') }}"><i class='bx bx-radio-circle'></i>Report Categories</a></li>
          @endcan
          @can('admin.reports.index')
            <li><a href="{{ route('admin.reports.index') }}"><i class='bx bx-radio-circle'></i>User Reports</a></li>
          @endcan
        </ul>
      </li>
    @endcanany

    @canany(['admin.blog-categories.index', 'admin.blog-posts.index', 'admin.comments.index'])
      <li>
        <a class="has-arrow" href="javascript:;">
          <div class="parent-icon"><i class='bx bx-news'></i></div>
          <div class="menu-title">Blogs</div>
        </a>
        <ul class="mm-collapse">
          @can('admin.blog-categories.index')
            <li><a href="{{ route('admin.blog-categories.index') }}"><i class='bx bx-radio-circle'></i>Blog Categories</a></li>
          @endcan
          @can('admin.blog-posts.index')
            <li><a href="{{ route('admin.blog-posts.index') }}"><i class='bx bx-radio-circle'></i>Blog Posts</a></li>
          @endcan
          @can('admin.comments.index')
            <li><a href="{{ route('admin.comments.index') }}"><i class='bx bx-radio-circle'></i>Blog Comments</a></li>
          @endcan
        </ul>
      </li>
    @endcanany
  </ul>
  <!--end navigation-->
</div>