<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <!-- Notifications -->
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" 
                           data-bs-toggle="dropdown" data-bs-target="#notificationDropdown" aria-expanded="false">
                            <i class="bx bx-bell"></i>
                        </a>
                        <ul class="dropdown-menu" id="notificationDropdown">
                            <li><a class="dropdown-item" href="#">No notifications yet</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            @php
                $instructor = Auth::guard('instructor')->user();
            @endphp
            <!-- Profil instructeur -->
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" 
                   role="button" data-bs-toggle="dropdown" data-bs-target="#userDropdown" aria-expanded="false">
                    <img src="{{ $instructor && $instructor->photo ? Storage::url('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
                         class="user-img" alt="Profil">
                    <div class="user-info">
                        <p class="user-name mb-0">{{ $instructor->name ?? 'Instructor' }}</p>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end" id="userDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('instructor.profile.edit') }}">
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
                        <form method="POST" action="{{ route('instructor.logout') }}" class="d-inline">
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