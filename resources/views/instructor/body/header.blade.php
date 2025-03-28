<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <!-- Notifications -->
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" 
                           role="button" data-bs-toggle="dropdown" data-bs-target="#notificationDropdown" aria-expanded="false">
                            <i class="bx bx-bell fs-5"></i>
                            @php
                                $instructor = Auth::guard('instructor')->user();
                                $unreadNotifications = $instructor ? $instructor->unreadNotifications()->latest()->get() : collect();
                                $unreadCount = $unreadNotifications->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger p-1" style="font-size: 0.65rem; min-width: 18px; height: 18px; line-height: 1;">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    <span class="visually-hidden">notifications non lues</span>
                                </span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm" id="notificationDropdown" style="min-width: 350px; max-width: 400px;">
                            <!-- En-tête du Dropdown -->
                            <div class="dropdown-header bg-light p-2 border-bottom">
                                <h6 class="mb-0 fw-bold">Notifications ({{ $unreadCount }})</h6>
                            </div>
                            <!-- Contenu des Notifications -->
                            <div class="dropdown-body" style="max-height: 300px; overflow-y: auto; overflow-x: hidden;">
                                @if ($unreadCount > 0)
                                    @foreach ($unreadNotifications as $notification)
                                        <a class="dropdown-item py-2 px-3 border-bottom" 
                                           href="{{ route('instructor.orders.show', $notification->data['order_id']) }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bx bx-book text-primary fs-5"></i>
                                                <div class="flex-grow-1" style="overflow: hidden;">
                                                    <p class="mb-0 text-dark" style="word-break: break-word; white-space: normal; overflow-wrap: break-word;">
                                                        {{ $notification->data['message'] }}
                                                    </p>
                                                    <small class="text-muted" style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="dropdown-item text-center py-3 text-muted">
                                        <i class="bx bx-info-circle me-1"></i> Aucune notification pour le moment
                                    </div>
                                @endif
                            </div>
                            <!-- Pied du Dropdown -->
                            @if ($unreadCount > 0)
                                <div class="dropdown-footer p-2 border-top">
                                    <form action="{{ route('instructor.notifications.markAllAsRead') }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-primary w-100 text-decoration-none">
                                            <i class="bx bx-check-circle me-1"></i> Marquer tout comme lu
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Profil Instructeur -->
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" 
                   role="button" data-bs-toggle="dropdown" data-bs-target="#userDropdown" aria-expanded="false">
                    <img src="{{ $instructor && $instructor->photo ? Storage::url('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
                         class="user-img rounded-circle" alt="Profil" style="width: 40px; height: 40px;">
                    <div class="user-info">
                        <p class="user-name mb-0 fw-semibold">{{ $instructor->name ?? 'Instructor' }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('instructor.profile.edit') }}">
                            <i class="bx bx-user fs-5 text-primary"></i>
                            <span>Profil</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#">
                            <i class="bx bx-dollar-circle fs-5 text-success"></i>
                            <span>Gains</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider mb-0"></li>
                    <li>
                        <form method="POST" action="{{ route('instructor.logout') }}" class="d-inline w-100">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bx bx-log-out-circle fs-5 text-danger"></i>
                                <span>Déconnexion</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>