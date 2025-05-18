<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-bell fs-5"></i>
                            @php
                                $instructor = Auth::guard('instructor')->user();
                                $unreadNotifications = $instructor ? $instructor->unreadNotifications()->latest()->take(10)->get() : collect();
                                $unreadCount = $unreadNotifications->count();
                            @endphp
                            <span id="notificationCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; min-width: 18px; height: 18px; line-height: 1; {{ $unreadCount > 0 ? '' : 'd-none' }}">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                <span class="visually-hidden">notifications non lues</span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm" id="notificationDropdown" style="min-width: 350px;">
                            <div class="dropdown-header bg-light p-2 border-bottom">
                                <h6 class="mb-0 fw-bold">Notifications (<span id="notificationCountText">{{ $unreadCount }}</span>)</h6>
                            </div>
                            <div class="dropdown-body" id="notificationList" style="max-height: 300px; overflow-y: auto;">
                                @if ($unreadCount > 0)
                                    @foreach ($unreadNotifications as $notification)
                                        <a class="dropdown-item py-2 px-3 border-bottom notification-item" 
                                           href="{{ $notification->data['type'] === 'report_resolution' ? route('instructor.reports.show', $notification->data['report_id']) : route('instructor.notifications.markAsRead', $notification->id) }}"
                                           data-notification-id="{{ $notification->id }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bx bx-book text-primary fs-5"></i>
                                                <div class="flex-grow-1">
                                                    <p class="mb-0 text-dark">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? 'No message', 50) }}</p>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="bx bx-info-circle me-1"></i> Aucune notification
                                    </div>
                                @endif
                            </div>
                            @if ($unreadCount > 0)
                                <div class="dropdown-footer p-2 border-top text-center">
                                    <form action="{{ route('instructor.notifications.markAllAsRead') }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-primary text-decoration-none">
                                            <i class="bx bx-check-circle me-1"></i> Tout marquer comme lu
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" 
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $instructor && $instructor->photo ? asset('upload/instructor_images/' . $instructor->photo) : asset('upload/no_image.jpg') }}"
                         class="user-img rounded-circle" alt="Profil" style="width: 40px; height: 40px;">
                    <div class="user-info">
                        <p class="user-name mb-0 fw-semibold">{{ $instructor->name ?? 'Instructor' }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" id="userDropdown">
                    <li><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('instructor.profile.edit') }}"><i class="bx bx-user fs-5 text-primary"></i><span>Profil</span></a></li>
                    <li><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('instructor.earnings') }}"><i class="bx bx-dollar-circle fs-5 text-success"></i><span>Gains</span></a></li>
                    <li>
                        <form method="POST" action="{{ route('instructor.logout') }}" class="d-inline w-100">
                            @csrf
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bx bx-log-out-circle fs-5 text-danger"></i><span>Déconnexion</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.Echo.private(`App.Models.Instructor.${{ Auth::guard('instructor')->id() }}`)
                    .notification((notification) => {
                        const notificationList = document.getElementById('notificationList');
                        const notificationCount = document.getElementById('notificationCount');
                        const notificationCountText = document.getElementById('notificationCountText');

                        // Create new notification item
                        const newItem = document.createElement('a');
                        newItem.className = 'dropdown-item py-2 px-3 border-bottom notification-item';
                        newItem.href = notification.type === 'report_resolution' 
                            ? `/instructor/reports/${notification.report_id}`
                            : `/instructor/notifications/${notification.id}/mark-as-read`;
                        newItem.setAttribute('data-notification-id', notification.id);
                        newItem.innerHTML = `
                            <div class="d-flex align-items-center gap-2">
                                <i class="bx bx-book text-primary fs-5"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-0 text-dark">${notification.message.substring(0, 50)}${notification.message.length > 50 ? '...' : ''}</p>
                                    <small class="text-muted">Just now</small>
                                </div>
                            </div>
                        `;
                        notificationList.prepend(newItem);

                        // Update notification count
                        let count = parseInt(notificationCountText.textContent) || 0;
                        count++;
                        notificationCountText.textContent = count;
                        notificationCount.classList.remove('d-none');
                        notificationCount.textContent = count > 9 ? '9+' : count;

                        // Show notification dropdown
                        const dropdown = new bootstrap.Dropdown(document.querySelector('.dropdown-toggle'));
                        dropdown.show();
                    });
            });
        </script>
    @endpush
</header>