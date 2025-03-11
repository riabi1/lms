<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <!-- Mode Sombre / Clair -->
                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="#"><i class="bx bx-moon"></i></a>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="bx bx-bell"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Récupération des données administrateur -->
            @php
                $admin = Auth::guard('admin')->user();
            @endphp

            <!-- Profil administrateur -->
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown">
                    <img src="{{ $admin && $admin->photo ? Storage::url('upload/admin_images/' . $admin->photo) : asset('upload/no_image.jpg') }}"
                         class="user-img" alt="Profil">
                    <div class="user-info">
                        <p class="user-name mb-0">{{ $admin->name ?? 'Admin' }}</p>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile.edit') }}">
                            <i class="bx bx-user fs-5"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bx bx-dollar-circle fs-5"></i>
                            <span>Earnings</span>
                        </a>
                    </li>
                    <li><div class="dropdown-divider mb-0"></div></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center" href="#"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bx bx-log-out-circle"></i>
                                <span>Logout</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>