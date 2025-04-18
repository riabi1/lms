@extends('User.layout.User_layout')

@section('title', 'Chat')

@section('userdashboard')
    <div class="chat-wrapper compact">
        <!-- Sidebar: Conversation List -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex align-items-center">
                    <div class="chat-user-online">
                        <img src="{{ Auth::user()->photo && \Storage::disk('public')->exists('upload/user_images/' . Auth::user()->photo) ? asset('storage/upload/user_images/' . Auth::user()->photo) : asset('assets/images/avatars/avatar-1.png') }}" width="40" height="40" class="rounded-circle user-avatar" alt="{{ Auth::user()->name }}" loading="lazy" />
                        <span class="online-status online"></span> <!-- Default to online -->
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0 user-name" style="font-size: 14px; font-weight: 600;">{{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div class="chat-tab-menu mt-2">
                    <ul class="nav nav-pills nav-justified">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="pill" href="javascript:;">
                                <div class="font-20"><i class='bx bx-conversation'></i></div>
                                <div><small>Chats</small></div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="chat-sidebar-content">
                <div class="tab-content">
                    <div class="tab-pane fade show active">
                        <div class="p-2">
                            <!-- Optional: Add search or filter here if needed -->
                        </div>
                        <div class="chat-list">
                            <div class="list-group list-group-flush">
                                @forelse($conversations as $conversation)
                                    <a href="{{ route('messages.show', $conversation->id) }}" class="list-group-item conversation-item {{ $conversation->id == $selectedConversation?->id ? 'active' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="chat-user-online">
                                                <img src="{{ $conversation->instructor->photo && \Storage::disk('public')->exists('upload/instructor_images/' . $conversation->instructor->photo) ? asset('storage/upload/instructor_images/' . $conversation->instructor->photo) : asset('assets/images/avatars/avatar-2.png') }}" width="36" height="36" class="rounded-circle user-avatar" alt="{{ $conversation->instructor->name ?? 'Instructeur inconnu' }}" loading="lazy" />
                                                <span class="online-status {{ $conversation->instructor->is_online === false ? 'offline' : 'online' }}"></span> <!-- Default to online -->
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0 chat-title" style="font-size: 13px;">{{ $conversation->instructor->name ?? 'Instructeur inconnu' }}</h6>
                                                <p class="mb-0 chat-msg" style="font-size: 11px;">{{ Str::limit($conversation->messages->last()->message ?? 'Aucun message', 20) }}</p>
                                            </div>
                                            <div class="chat-time" style="font-size: 10px;">{{ $conversation->last_message_at ? \Carbon\Carbon::parse($conversation->last_message_at)->diffForHumans() : '' }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <p class="p-2 no-conversations" style="font-size: 12px; color: #6B7280;">No conversations yet.</p>
                                @endforelse
                            </div>
                            <button class="scroll-to-bottom sidebar-scroll three-d" style="display: none;" aria-label="Scroll to bottom of conversations"><i class='bx bx-down-arrow-alt'></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        @if($selectedConversation)
            <div class="chat-main">
                <div class="chat-header d-flex align-items-center">
                    <div class="chat-toggle-btn d-lg-none"><i class='bx bx-menu-alt-left'></i></div>
                    <div class="d-flex align-items-center">
                        <img src="{{ $selectedConversation->instructor->photo && \Storage::disk('public')->exists('upload/instructor_images/' . $selectedConversation->instructor->photo) ? asset('storage/upload/instructor_images/' . $selectedConversation->instructor->photo) : asset('assets/images/avatars/avatar-3.png') }}" width="40" height="40" class="rounded-circle user-avatar" alt="{{ $selectedConversation->instructor->name ?? 'Instructeur inconnu' }}" loading="lazy" />
                        <div class="ms-2">
                            <h4 class="mb-0 chat-user-name" style="font-size: 15px;">{{ $selectedConversation->instructor->name ?? 'Instructeur inconnu' }}</h4>
                            <small class="chat-status" style="font-size: 11px;">{{ $selectedConversation->instructor->is_online === false ? 'Offline' : 'Active Now' }}</small>
                        </div>
                    </div>
                </div>
                <div class="chat-content">
                    <div class="typing-indicator" style="display: none;">
                        <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                    </div>
                    @forelse($selectedConversation->messages as $message)
                        @if($message->sender_id == Auth::id() && $message->sender_type == 'App\\Models\\User')
                            <div class="chat-content-rightside">
                                <div class="d-flex">
                                    <div class="flex-grow-1 me-2">
                                        <p class="mb-0 chat-time text-end" style="font-size: 0.625rem;">{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
                                        <p class="chat-right-msg three-d {{ $loop->last ? 'last-message' : '' }}" data-message-id="{{ $message->id }}">{{ $message->message }}</p>
                                    </div>
                                    <img src="{{ Auth::user()->photo && \Storage::disk('public')->exists('upload/user_images/' . Auth::user()->photo) ? asset('storage/upload/user_images/' . Auth::user()->photo) : asset('assets/images/avatars/avatar-1.png') }}" width="36" height="36" class="rounded-circle user-avatar" alt="{{ Auth::user()->name }}" loading="lazy" />
                                </div>
                            </div>
                        @else
                            <div class="chat-content-leftside">
                                <div class="d-flex">
                                    <img src="{{ $selectedConversation->instructor->photo && \Storage::disk('public')->exists('upload/instructor_images/' . $selectedConversation->instructor->photo) ? asset('storage/upload/instructor_images/' . $selectedConversation->instructor->photo) : asset('assets/images/avatars/avatar-3.png') }}" width="36" height="36" class="rounded-circle user-avatar" alt="{{ $selectedConversation->instructor->name ?? 'Instructeur inconnu' }}" loading="lazy" />
                                    <div class="flex-grow-1 ms-2">
                                        <p class="mb-0 chat-time" style="font-size: 0.625rem;">{{ $selectedConversation->instructor->name }}, {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
                                        <p class="chat-left-msg three-d {{ $loop->last ? 'last-message' : '' }}" data-message-id="{{ $message->id }}">{{ $message->message }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="p-2 text-center no-messages" style="font-size: 0.75rem; color: #6B7280;">Start a conversation!</p>
                    @endforelse
                    <div class="chat-content-bottom-spacer"></div>
                    <button class="scroll-to-bottom content-scroll three-d" style="display: none;" aria-label="Scroll to bottom of messages">
                        <i class='bx bx-down-arrow-alt'></i>
                    </button>
                </div>
                <div class="chat-footer d-flex align-items-center">
                    <div class="flex-grow-1 pe-2">
                        <form id="message-form" action="{{ route('messages.send', $selectedConversation->id) }}" method="POST" aria-label="Send message">
                            @csrf
                            <div class="input-group input-group-sm three-d">
                                <span class="input-group-text"><i class='bx bx-smile'></i></span>
                                <input type="text" name="message" class="form-control message-input" placeholder="Type a message..." style="font-size: 0.8125rem;" required aria-label="Message input">
                                <button type="submit" class="input-group-text send-btn three-d" aria-label="Send message"><i class='bx bx-send'></i></button>
                            </div>
                            @error('message')
                                <span class="text-danger error-message" style="font-size: 0.6875rem;">{{ $message }}</span>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .chat-wrapper.compact {
            display: flex;
            height: calc(100vh - 8.75rem);
            flex-direction: row;
            margin: 0;
            background: #fff;
        }

        .chat-sidebar {
            flex: 0 0 18.75rem;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e5e7eb;
        }

        .chat-sidebar-header {
            padding: 0.625rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .chat-sidebar-content {
            flex: 1;
            overflow: hidden;
        }

        .chat-list .list-group {
            overflow-y: auto;
            height: calc(100vh - 12.5rem);
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 0.625rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .chat-content {
            flex: 1;
            overflow-y: auto;
            padding: 0.9375rem;
            position: relative;
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
            background: #f9fafb;
        }

        .chat-content::-webkit-scrollbar {
            width: 0.375rem;
        }

        .chat-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 0.1875rem;
        }

        .chat-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chat-content-bottom-spacer {
            height: 3.75rem;
        }

        .chat-footer {
            padding: 0.625rem;
            border-top: 1px solid #e5e7eb;
        }

        .scroll-to-bottom {
            position: fixed;
            bottom: 5.625rem;
            right: 1.5625rem;
            z-index: 100;
            background: #dc2626;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 3.75rem;
            height: 3.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.3);
            transition: transform 0.2s ease, opacity 0.2s ease;
            animation: bounce 1.5s infinite;
        }

        .scroll-to-bottom:hover {
            transform: scale(1.15);
        }

        .scroll-to-bottom i {
            font-size: 1.75rem;
        }

        .sidebar-scroll {
            position: absolute;
            bottom: 0.625rem;
            right: 1.25rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            width: 2.5rem;
            height: 2.5rem;
        }

        .chat-content-rightside, .chat-content-leftside {
            max-width: 100%;
            word-wrap: break-word;
            margin-bottom: 0.9375rem;
        }

        .chat-right-msg, .chat-left-msg {
            display: inline-block;
            max-width: 75%;
            padding: 0.625rem 0.875rem;
            border-radius: 0.75rem;
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .chat-right-msg {
            background: #2563eb;
            color: #000000;
            box-shadow: 0 0.0625rem 0.1875rem rgba(0, 0, 0, 0.1);
        }

        .chat-left-msg {
            background: #ffffff;
            color: #000000;
            border: 1px solid #e5e7eb;
            box-shadow: 0 0.0625rem 0.1875rem rgba(0, 0, 0, 0.1);
        }

        .last-message {
            font-size: 0.9375rem;
            font-weight: 500;
            padding: 0.75rem 1rem;
            background: #fef3c7 !important;
            border: 2px solid #f59e0b !important;
            color: #1f2937 !important;
        }

        .chat-time {
            color: #6b7280;
            font-size: 0.625rem;
            margin-bottom: 0.25rem;
        }

        .typing-indicator {
            padding: 0.3125rem 0.625rem;
            font-size: 0.75rem;
            color: #6B7280;
        }

        .typing-indicator .dot {
            display: inline-block;
            width: 0.375rem;
            height: 0.375rem;
            margin: 0 0.125rem;
            background: #6B7280;
            border-radius: 50%;
            animation: dot-flashing 1s infinite alternate;
        }

        .typing-indicator .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Online/Offline Status */
        .online-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0.625rem;
            height: 0.625rem;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .online-status.online {
            background: #22c55e; /* Green for online */
        }

        .online-status.offline {
            background: #6b7280; /* Gray for offline */
        }

        @keyframes dot-flashing {
            0% { opacity: 0.2; }
            100% { opacity: 1; }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-0.3125rem); }
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .chat-sidebar {
                flex: 0 0 15.625rem;
            }
        }

        @media (max-width: 767px) {
            .chat-wrapper.compact {
                flex-direction: column;
            }

            .chat-sidebar {
                flex: 0 0 auto;
                max-height: 40vh;
            }

            .chat-main {
                flex: 1;
                display: none;
            }

            .chat-main.active {
                display: flex;
            }

            .chat-sidebar.active ~ .chat-main {
                display: none;
            }

            .chat-list .list-group {
                height: calc(40vh - 6.25rem);
            }

            .chat-content {
                height: calc(60vh - 7.5rem);
            }

            .scroll-to-bottom {
                bottom: 5rem;
                right: 0.9375rem;
                width: 3.125rem;
                height: 3.125rem;
            }

            .scroll-to-bottom i {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    @if($selectedConversation)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Chat content scrolling
                const chatContent = document.querySelector('.chat-content');
                const contentScrollBtn = document.querySelector('.content-scroll');

                // Scroll to ensure last message is fully visible
                const scrollToBottom = () => {
                    const lastMessage = document.querySelector('.last-message');
                    if (lastMessage) {
                        lastMessage.scrollIntoView({ behavior: 'smooth', block: 'end' });
                    } else {
                        chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });
                    }
                };
                scrollToBottom();

                // Show/hide scroll-to-bottom button
                chatContent.addEventListener('scroll', () => {
                    const isAtBottom = chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < 0.625rem;
                    contentScrollBtn.style.display = isAtBottom ? 'none' : 'flex';
                });

                // Scroll to bottom on button click
                contentScrollBtn.addEventListener('click', scrollToBottom);

                // Sidebar scrolling
                const chatList = document.querySelector('.chat-list .list-group');
                const sidebarScrollBtn = document.querySelector('.sidebar-scroll');

                chatList.scrollTo({ top: chatList.scrollHeight, behavior: 'smooth' });

                chatList.addEventListener('scroll', () => {
                    const isNearBottom = chatList.scrollHeight - chatList.scrollTop - chatList.clientHeight < 0.625rem;
                    sidebarScrollBtn.style.display = isNearBottom ? 'none' : 'flex';
                });

                sidebarScrollBtn.addEventListener('click', () => {
                    chatList.scrollTo({ top: chatList.scrollHeight, behavior: 'smooth' });
                });

                // Mobile toggle
                const toggleBtn = document.querySelector('.chat-toggle-btn');
                const chatSidebar = document.querySelector('.chat-sidebar');
                const chatMain = document.querySelector('.chat-main');

                if (toggleBtn) {
                    toggleBtn.addEventListener('click', () => {
                        chatSidebar.classList.toggle('active');
                        chatMain.classList.toggle('active');
                    });
                }

                // Typing indicator
                const messageInput = document.querySelector('.message-input');
                let typingTimer;
                messageInput.addEventListener('input', () => {
                    clearTimeout(typingTimer);
                    fetch('/messages/{{ $selectedConversation->id }}/typing', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    });
                    typingTimer = setTimeout(() => {}, 1000);
                });

                // Form validation
                document.querySelector('#message-form').addEventListener('submit', (e) => {
                    const input = document.querySelector('.message-input');
                    if (!input.value.trim()) {
                        e.preventDefault();
                        toastr.error('Message cannot be empty');
                    }
                });
            });

            Echo.private(`conversation.{{ $selectedConversation->id }}`)
                .listen('MessageSent', (e) => {
                    const chatContent = document.querySelector('.chat-content');
                    const contentScrollBtn = document.querySelector('.content-scroll');
                    const messageDiv = document.createElement('div');
                    const isCurrentUser = e.sender_id === {{ Auth::id() }} && e.sender_type === 'App\\Models\\User';
                    messageDiv.className = isCurrentUser ? 'chat-content-rightside' : 'chat-content-leftside';
                    messageDiv.style.opacity = '0';

                    // Remove last-message class from previous
                    const prevLast = document.querySelector('.last-message');
                    if (prevLast) prevLast.classList.remove('last-message');

                    messageDiv.innerHTML = isCurrentUser ?
                        `<div class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <p class="mb-0 chat-time text-end" style="font-size: 0.625rem;">Just now</p>
                                <p class="chat-right-msg three-d last-message" data-message-id="${e.message_id}">${e.message}</p>
                            </div>
                            <img src="{{ Auth::user()->photo && \Storage::disk('public')->exists('upload/user_images/' . Auth::user()->photo) ? asset('storage/upload/user_images/' . Auth::user()->photo) : asset('assets/images/avatars/avatar-1.png') }}" width="36" height="36" class="rounded-circle user-avatar" alt="{{ Auth::user()->name }}" loading="lazy" />
                        </div>` :
                        `<div class="d-flex">
                            <img src="{{ $selectedConversation->instructor->photo && \Storage::disk('public')->exists('upload/instructor_images/' . $selectedConversation->instructor->photo) ? asset('storage/upload/instructor_images/' . $selectedConversation->instructor->photo) : asset('assets/images/avatars/avatar-3.png') }}" width="36" height="36" class="rounded-circle user-avatar" alt="{{ $selectedConversation->instructor->name ?? 'Instructeur inconnu' }}" loading="lazy" />
                            <div class="flex-grow-1 ms-2">
                                <p class="mb-0 chat-time" style="font-size: 0.625rem;">{{ $selectedConversation->instructor->name }}, Just now</p>
                                <p class="chat-left-msg three-d last-message" data-message-id="${e.message_id}">${e.message}</p>
                            </div>
                        </div>`;
                    chatContent.insertBefore(messageDiv, document.querySelector('.chat-content-bottom-spacer'));
                    setTimeout(() => {
                        messageDiv.style.transition = 'opacity 0.3s ease';
                        messageDiv.style.opacity = '1';
                    }, 10);

                    const isAtBottom = chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < 0.625rem;
                    if (isAtBottom) {
                        document.querySelector('.last-message').scrollIntoView({ behavior: 'smooth', block: 'end' });
                    } else {
                        contentScrollBtn.style.display = 'flex';
                    }
                })
                .listen('Typing', (e) => {
                    const typingIndicator = document.querySelector('.typing-indicator');
                    typingIndicator.style.display = 'flex';
                    typingIndicator.innerHTML = `<span class="dot"></span><span class="dot"></span><span class="dot"></span> ${e.user.name} is typing...`;
                    setTimeout(() => {
                        typingIndicator.style.display = 'none';
                    }, 2000);
                });
        </script>
    @endif
@endpush