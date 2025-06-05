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
                            <i class="bx bx-bell fs-5"></i>
                            @php
                                $user = Auth::guard('web')->user();
                                $unreadNotifications = $user ? $user->unreadNotifications()->latest()->take(10)->get() : collect();
                                $unreadCount = $unreadNotifications->count();
                            @endphp
                            <span id="notificationCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                  style="font-size: 0.65rem; min-width: 18px; height: 18px; line-height: 1; {{ $unreadCount > 0 ? '' : 'd-none' }}">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                <span class="visually-hidden">notifications non lues</span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 350px;">
                            <div class="dropdown-header bg-light p-2 border-bottom">
                                <h6 class="mb-0 fw-bold">Notifications (<span id="notificationCountText">{{ $unreadCount }}</span>)</h6>
                            </div>
                            <div class="dropdown-body" id="notificationList" style="max-height: 300px; overflow-y: auto;">
                                @if ($unreadCount > 0)
                                    @foreach ($unreadNotifications as $notification)
                                        <a class="dropdown-item py-2 px-3 border-bottom notification-item" 
                                           href="{{ $notification->data['type'] === 'report_resolution' ? route('notifications.read', ['notification' => $notification->id, 'report_id' => $notification->data['report_id']]) : route('notifications.markAsRead', $notification->id) }}"
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
                                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline w-100">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-primary text-decoration-none w-100">
                                            <i class="bx bx-check-circle me-1"></i> Tout marquer comme lu
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Profil utilisateur -->
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" 
                   role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="rounded-circle me-3 shadow-sm"
                         src="{{ $user ? ($user->photo ? asset('upload/user_images/' . $user->photo) : asset('upload/no_image.jpg')) : asset('upload/no_image.jpg') }}"
                         class="user-img rounded-circle" alt="{{ $user ? $user->name : 'User' }}'s Avatar" style="width: 40px; height: 40px;">
                    <div class="user-info">
                        <p class="user-name mb-0">{{ $user->name ?? 'User' }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('purchase.history') }}">
                            <i class="bx bx-history fs-5 me-2 text-success"></i>
                            <span>Purchase History</span>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-log-out fs-5 me-2 text-danger"></i>
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

    @push('styles')
        <style>
            .topbar {
                height: 70px;
                background: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                z-index: 1000;
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
                z-index: 1001;
            }
            .dropdown-item {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
            .dropdown-item:hover {
                background-color: #f8f9fa;
            }
            .body-overlay {
                z-index: 999;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.7.5/socket.io.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const socket = io('{{ env('SOCKET_IO_URL', 'http://localhost:3000') }}', {
                    withCredentials: true
                });

                socket.emit('joinUser', {{ Auth::guard('web')->id() }});
                console.log('User joined:', {{ Auth::guard('web')->id() }});

                socket.on('notification', (notification) => {
                    console.log('Received notification:', notification);
                    const notificationList = document.getElementById('notificationList');
                    const notificationCount = document.getElementById('notificationCount');
                    const notificationCountText = document.getElementById('notificationCountText');

                    if (!notificationList || !notificationCount || !notificationCountText) {
                        console.error('Notification DOM elements not found');
                        return;
                    }

                    // Create new notification item
                    const newItem = document.createElement('a');
                    newItem.className = 'dropdown-item py-2 px-3 border-bottom notification-item';
                    newItem.href = notification.type === 'report_resolution' 
                        ? `/user/notifications/${notification.id}/read?report_id=${notification.report_id}`
                        : `/user/notifications/${notification.id}/mark-as-read`;
                    newItem.setAttribute('data-notification-id', notification.id || 'unknown');
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
                    try {
                        const dropdown = new bootstrap.Dropdown(document.querySelector('#notificationDropdown'));
                        dropdown.show();
                    } catch (e) {
                        console.error('Failed to show dropdown:', e);
                    }
                });

                socket.on('connect_error', (error) => {
                    console.error('Socket.IO connection error:', error);
                });
            });
        </script>
    @endpush
</header>