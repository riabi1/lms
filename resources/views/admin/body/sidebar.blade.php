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
        <div class="parent-icon"><i class='bx bx-grid-alt'></i></div>
        <div class="menu-title">Dashboard</div>
      </a>
    </li>

    <li class="menu-label">Management Tools</li>

    <li>
      <a href="javascript:;" class="has-arrow">
        <div class="parent-icon"><i class='bx bx-category'></i></div>
        <div class="menu-title">Manage Category</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.categories.index') }}"><i class='bx bx-radio-circle'></i>All Category</a></li>
        <li><a href="{{ route('admin.subcategories.index') }}"><i class='bx bx-radio-circle'></i>All SubCategory</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-user'></i></div>
        <div class="menu-title">Manage Instructor</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.instructors.index') }}"><i class='bx bx-radio-circle'></i>All Instructor</a></li>
        <li><a href="{{ route('admin.users.index') }}"><i class='bx bx-radio-circle'></i>All Users</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-book-open'></i></div>
        <div class="menu-title">Manage Courses</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.courses.index') }}"><i class='bx bx-radio-circle'></i>All Courses</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-purchase-tag'></i></div>
        <div class="menu-title">Manage Coupon</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.coupon.index') }}"><i class='bx bx-radio-circle'></i>All Coupon</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-cart-alt'></i></div>
        <div class="menu-title">Manage Orders</div>
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
        <div class="menu-title">Manage Review</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.pending.review') }}"><i class='bx bx-radio-circle'></i>Pending Review</a></li>
        <li><a href="{{ route('admin.active.review') }}"><i class='bx bx-radio-circle'></i>Active Review</a></li>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;" aria-expanded="false">
        <div class="parent-icon"><i class='bx bx-cog'></i></div>
        <div class="menu-title">Manage Settings</div>
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
        <div class="parent-icon"><i class='bx bx-category'></i></div>
        <div class="menu-title">Manage Report Categories</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="{{ route('admin.report-categories.index') }}"><i class='bx bx-radio-circle'></i>All Report Categories</a></li>
      </ul>


      <ul>
        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
          <div class="parent-icon"><i class='bx bx-bar-chart-alt'></i></div>
          <div class="menu-title">Reports</div>
        </a>
      </ul>
    </li>

    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-news'></i></div>
        <div class="menu-title">Manage Blog</div>
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
        <li><a href="{{ route('admin.comments.index') }}"><i class='bx bx-radio-circle'></i>Comments</a></li>
      </ul>
    </li>

    <li class="menu-label">Role & Permission</li>
    <li>
      <a class="has-arrow" href="javascript:;">
        <div class="parent-icon"><i class='bx bx-lock'></i></div>
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
        <div class="parent-icon"><i class='bx bx-user-circle'></i></div>
        <div class="menu-title">Manage Admin</div>
      </a>
      <ul class="mm-collapse">
        <li><a href="#"><i class='bx bx-radio-circle'></i>All Admin</a></li>
      </ul>
    </li>
  </ul>
  <!--end navigation-->
</div>