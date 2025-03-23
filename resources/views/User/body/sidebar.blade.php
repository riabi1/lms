<div class="sidebar-wrapper" data-simplebar="true">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div>
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset('frontend/images/logo2.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px; filter: brightness(110%);">
            </a>
        </div>
        <div>
            <h4 class="logo-text">User Dashboard</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>

    <!-- Navigation -->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <div class="parent-icon"><i class='bx bx-user'></i></div>
                <div class="menu-title">My Profile</div>
            </a>
        </li>
        <li>
            <a href="#">
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
    <!-- End Navigation -->
</div>

<!-- Styles personnalisés -->
<style>
    .sidebar-wrapper {
        width: 300px; /* Largeur par défaut, ajustable si nécessaire */
        height: 100vh;
        background: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
    }
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
    }
    .logo-text {
        font-size: 1.25rem;
        font-weight: 500;
        color: #333;
        margin: 0;
    }
    .toggle-icon {
        cursor: pointer;
        font-size: 1.5rem;
        color: #666;
    }
    .metismenu {
        list-style: none;
        padding: 0;
    }
    .metismenu li {
        margin-bottom: 5px;
    }
    .metismenu a {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        color: #555;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    .metismenu a:hover {
        background-color: #f8f9fa;
    }
    .metismenu .active {
        background-color: #e0e7ff;
        color: #1e40af;
    }
    .parent-icon {
        width: 30px;
        text-align: center;
    }
    .menu-title {
        flex-grow: 1;
    }
    .badge {
        font-size: 0.75rem;
    }
</style>