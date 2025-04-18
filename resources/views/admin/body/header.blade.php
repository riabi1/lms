<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="top-menu ms-auto">
                <!-- You can add other nav items here if needed -->
            </div>
            @php
                $admin = Auth::guard('admin')->user();
            @endphp
            <!-- Profil administrateur -->
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" 
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $admin && $admin->photo ? Storage::url('upload/admin_images/' . $admin->photo) : asset('upload/no_image.jpg') }}"
                         class="user-img rounded-circle" alt="Profil" style="width: 40px; height: 40px;">
                    <div class="user-info">
                        <p class="user-name mb-0 fw-semibold">{{ $admin->name ?? 'Admin' }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" id="userDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.profile.edit') }}">
                            <i class="bx bx-user fs-5 text-primary"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.earnings') }}">
                            <i class="bx bx-dollar-circle fs-5 text-success"></i>
                            <span>Earnings</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider mb-0"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline w-100">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bx bx-log-out-circle fs-5 text-danger"></i>
                                <span>Logout</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>