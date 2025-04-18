<header>
    <div class="topbar d-flex align-items-center bg-white shadow-sm">
        <nav class="navbar navbar-expand gap-3 container-fluid px-4">
            <!-- Menu de droite -->
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-3">
                    <!-- Notifications -->
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" 
                           role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-bell"></i>
                            <span class="dot-status bg-1"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                            <li class="menu-heading-block px-3 py-2">
                                <h4 class="fs-16 font-weight-semi-bold">Notifications</h4>
                            </li>
                            <li><a class="dropdown-item" href="#">No notifications yet</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Profil utilisateur -->
            <?php $user = Auth::guard('web')->user(); ?>
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" 
                   role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $user && $user->photo ? asset('storage/upload/user_images/' . $user->photo) : asset('upload/no_image.jpg') }}"
                         class="user-img rounded-circle" alt="{{ $user ? $user->name : 'User' }}'s Avatar" style="width: 40px; height: 40px;">
                    <div class="user-info">
                        <p class="user-name mb-0">{{ $user->name ?? 'User' }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="">
                            <i class="bx bx-history fs-5 mr-2 text-success"></i>
                            <span>Purchase History</span>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-log-out fs-5 mr-2 text-danger"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="body-overlay"></div>
</header>

<!-- Styles personnalisés -->
<style>
    .topbar {
        height: 70px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1000; /* Ensure topbar is above other elements */
    }
    .navbar {
        padding: 0;
    }
    .icon-element {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: #fff;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .icon-element:hover {
        background: #f8f9fa;
    }
    .icon-element i {
        font-size: 1.5rem;
        color: #555;
    }
    .user-img {
        object-fit: cover;
    }
    .user-info .user-name {
        font-size: 1rem;
        font-weight: 500;
        color: #333;
    }
    .dropdown-toggle-nocaret::after {
        display: none;
    }
    .dropdown-menu {
        min-width: 200px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1001; /* Ensure dropdown is above topbar */
    }
    .dropdown-item {
        padding: 8px 15px;
        font-size: 0.9rem;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    .dot-status {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
    }
    .theme-picker-btn {
        padding: 8px 15px;
        background: none;
        border: none;
        color: #555;
        font-size: 0.9rem;
    }
    .theme-picker-btn:hover {
        background: #f8f9fa;
    }
    .menu-heading-block h4 {
        margin: 0;
        font-size: 1rem;
    }
    .body-overlay {
        z-index: 999; /* Below dropdowns but above content */
    }
</style>