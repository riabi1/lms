@extends('User.layout.User_layout')

@section('title', 'Chat')

@section('userdashboard')
    <div class="chat-wrapper compact">
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex align-items-center">
                    <div class="chat-user-online">
                        <img src="{{ Auth::user()?->photo && \Storage::disk('public')->exists('upload/user_images/' . Auth::user()->photo) ? asset('storage/upload/user_images/' . Auth::user()->photo) : asset('upload/no_image.jpg') }}" 
                             width="100" height="100" class="rounded-circle user-avatar" alt="{{ Auth::user()?->name ?? 'User' }}" 
                             style="object-fit: cover;" loading="lazy" />
                        <span class="online-status"></span>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0 user-name" style="font-size: 14px; font-weight: 600;">{{ Auth::user()?->name ?? 'User' }}</p>
                    </div>
                </div>
                <div class="mb-2"></div>
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
                        <div class="chat-list">
                            <div class="list-group list-group-flush">
                                @forelse($conversations as $conversation)
                                    <a href="{{ route('messages.show', $conversation->id) }}" class="list-group-item conversation-item {{ $conversation->id == $selectedConversation?->id ? 'active' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="chat-user-online">
                                                <img src="{{ $conversation->instructor?->photo && file_exists(public_path('upload/instructor_images/' . $conversation->instructor->photo)) ? asset('upload/instructor_images/' . $conversation->instructor->photo) : asset('upload/no_image.jpg') }}" 
                                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ $conversation->instructor?->name ?? 'Instructeur inconnu' }}" 
                                                     style="object-fit: cover;" loading="lazy" />
                                                <span class="online-status {{ $conversation->instructor?->is_online ? 'online' : 'offline' }}"></span>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0 chat-title" style="font-size: 13px;">{{ $conversation->instructor?->name ?? 'Instructeur inconnu' }}</h6>
                                                <p class="mb-0 chat-msg" style="font-size: 11px;">{{ Str::limit($conversation->messages->last()->message ?? 'Aucun message', 20) }}</p>
                                            </div>
                                            <div class="chat-time" style="font-size: 10px;">{{ $conversation->last_message_at ? \Carbon\Carbon::parse($conversation->last_message_at)->diffForHumans() : '' }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <p class="p-2 no-conversations" style="font-size: 12px; color: #6B7280;">No conversations yet.</p>
                                @endforelse
                                <div>
                                    <button class="scroll-to-bottom sidebar-scroll three-d" style="display: none;" aria-label="Scroll to bottom of conversations"><i class='bx bx-down-arrow-alt'></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($selectedConversation)
            <div class="chat-header d-flex align-items-center">
                <div class="chat-toggle-btn"><i class='bx bx-menu-alt-left'></i></div>
                <div class="d-flex align-items-center">
                    <img src="{{ $selectedConversation->instructor?->photo && file_exists(public_path('upload/instructor_images/' . $conversation->instructor->photo)) ? asset('upload/instructor_images/' . $conversation->instructor->photo) : asset('upload/no_image.jpg') }}" 
                         width="40" height="40" class="rounded-circle user-avatar" alt="{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}" 
                         style="object-fit: cover;" loading="lazy" />
                    <div class="ms-2">
                        <h4 class="mb-0 chat-user-name" style="font-size: 15px;">{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}</h4>
                        <small class="chat-status" style="font-size: 11px;">{{ $selectedConversation->instructor?->is_online ? 'Active Now' : 'Offline' }}</small>
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
                                    <p class="mb-0 chat-time text-end" style="font-size: 10px;">{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
                                    <p class="chat-right-msg three-d" data-message-id="{{ $message->id }}">{{ $message->message }}</p>
                                </div>
                                <img src="{{ Auth::user()?->photo && \Storage::disk('public')->exists('upload/user_images/' . Auth::user()->photo) ? asset('storage/upload/user_images/' . Auth::user()->photo) : asset('upload/no_image.jpg') }}" 
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ Auth::user()?->name ?? 'User' }}" 
                                     style="object-fit: cover;" loading="lazy" />
                            </div>
                        </div>
                    @else
                        <div class="chat-content-leftside">
                            <div class="d-flex">
                                <img src="{{ $selectedConversation->instructor?->photo && file_exists(public_path('upload/instructor_images/' . $selectedConversation->instructor->photo)) ? asset('upload/instructor_images/' . $selectedConversation->instructor->photo) : asset('upload/no_image.jpg') }}" 
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}" 
                                     style="object-fit: cover;" loading="lazy" />
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 chat-time" style="font-size: 10px;">{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}, {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
                                    <p class="chat-left-msg three-d" data-message-id="{{ $message->id }}">{{ $message->message }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="p-2 text-center no-messages" style="font-size: 12px; color: #6B7280;">No messages in this conversation.</p>
                @endforelse
                <button class="scroll-to-bottom three-d" style="display: none;" aria-label="Scroll to bottom"><i class='bx bx-down-arrow-alt'></i></button>
            </div>
            <div class="chat-footer d-flex align-items-center">
                <div class="flex-grow-1 pe-2">
                    <form id="message-form" action="{{ route('messages.send', $selectedConversation->id) }}" method="POST" aria-label="Send message">
                        @csrf
                        <div class="input-group input-group-sm three-d">
                            <span class="input-group-text"><i class='bx bx-smile'></i></span>
                            <input type="text" name="message" class="form-control message-input" placeholder="Type a message..." style="font-size: 13px;" required aria-label="Message input">
                            <button type="submit" class="input-group-text send-btn three-d" aria-label="Send message"><i class='bx bx-send'></i></button>
                        </div>
                        <span class="text-danger error-message" style="font-size: 11px; display: none;"></span>
                    </form>
                </div>
            </div>
        @else
            <div class="chat-content">
                <p class="p-2 text-center no-messages" style="font-size: 14px; color: #6B7280;">Select a conversation to start chatting.</p>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .chat-wrapper {
            display: flex;
            height: calc(100vh - 120px);
            overflow: hidden;
        }
        .chat-sidebar {
            width: 300px;
            border-right: 1px solid #e9ecef;
        }
        .chat-sidebar-content {
            overflow-y: auto;
        }
        .chat-content {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            max-height: calc(100vh - 240px);
        }
        .scroll-to-bottom {
            position: sticky;
            bottom: 10px;
            right: 10px;
            float: right;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .chat-right-msg {
            background: #6992f0;
            color: #000000;
            padding: 10px 14px;
            border-radius: 12px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .chat-left-msg {
            background: #f06969;
            color: #000000;
            padding: 10px 14px;
            border-radius: 12px;
            max-width: 70%;
            word-wrap: break-word;
        }
        .typing-indicator {
            padding: 10px;
            font-size: 12px;
            color: #6B7280;
        }
        .typing-indicator .dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            margin: 0 2px;
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
        @keyframes dot-flashing {
            0% { opacity: 0.2; }
            100% { opacity: 1; }
        }
    </style>
@endpush

@push('scripts')
    @if($selectedConversation)
        <script src="{{ asset('js/echo.js') }}"></script>
        <script>
            console.log('Initializing Echo for conversation {{ $selectedConversation->id }}');
            document.addEventListener('DOMContentLoaded', () => {
                const chatContent = document.querySelector('.chat-content');
                const scrollToBottomBtn = document.querySelector('.scroll-to-bottom');
                const messageForm = document.querySelector('#message-form');
                const messageInput = document.querySelector('.message-input');
                const errorMessage = document.querySelector('.error-message');
                let lastMessageId = {{ $selectedConversation->messages->max('id') ?? 0 }};

                chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });

                chatContent.addEventListener('scroll', () => {
                    const isNearBottom = chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < 100;
                    scrollToBottomBtn.style.display = isNearBottom ? 'none' : 'flex';
                });

                scrollToBottomBtn.addEventListener('click', () => {
                    chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });
                });

                messageForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const message = messageInput.value.trim();
                    if (!message) return;

                    fetch('{{ route("messages.send", $selectedConversation->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ message }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            messageInput.value = '';
                            errorMessage.style.display = 'none';
                            errorMessage.textContent = '';
                        } else {
                            errorMessage.textContent = data.error || 'Failed to send message';
                            errorMessage.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                        errorMessage.textContent = 'An error occurred';
                        errorMessage.style.display = 'block';
                    });
                });

                let typingTimer;
                let isTyping = false;
                messageInput.addEventListener('input', () => {
                    if (!isTyping) {
                        isTyping = true;
                        fetch('/messages/{{ $selectedConversation->id }}/typing', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            },
                        }).catch(error => console.error('Typing request failed:', error));
                    }
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        isTyping = false;
                    }, 2000);
                });

                Echo.private(`conversation.{{ $selectedConversation->id }}`)
                    .listen('.MessageSent', (e) => {
                        console.log('MessageSent event received:', e);
                        if (e.message_id <= lastMessageId) return;
                        lastMessageId = e.message_id;

                        const isCurrentUser = e.sender_id === {{ Auth::id() }} && e.sender_type === 'App\\Models\\User';
                        const messageDiv = document.createElement('div');
                        messageDiv.className = isCurrentUser ? 'chat-content-rightside' : 'chat-content-leftside';
                        messageDiv.style.opacity = '0';
                        messageDiv.innerHTML = isCurrentUser ?
                            `<div class="d-flex">
                                <div class="flex-grow-1 me-2">
                                    <p class="mb-0 chat-time text-end" style="font-size: 10px;">Just now</p>
                                    <p class="chat-right-msg three-d" data-message-id="${e.message_id}">${e.message}</p>
                                </div>
                                <img src="${e.sender_photo}" 
                                     width="36" height="36" class="rounded-circle user-avatar" alt="${e.sender_name}" 
                                     style="object-fit: cover;" loading="lazy" />
                            </div>` :
                            `<div class="d-flex">
                                <img src="${e.sender_photo}" 
                                     width="36" height="36" class="rounded-circle user-avatar" alt="${e.sender_name}" 
                                     style="object-fit: cover;" loading="lazy" />
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 chat-time" style="font-size: 10px;">${e.sender_name}, Just now</p>
                                    <p class="chat-left-msg three-d" data-message-id="${e.message_id}">${e.message}</p>
                                </div>
                            </div>`;
                        chatContent.appendChild(messageDiv);
                        setTimeout(() => {
                            messageDiv.style.transition = 'opacity 0.3s ease';
                            messageDiv.style.opacity = '1';
                        }, 10);

                        const isNearBottom = chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < 100;
                        if (isNearBottom) {
                            chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });
                        }
                    })
                    .listen('.Typing', (e) => {
                        console.log('Typing event received:', e);
                        if (e.user.id === {{ Auth::id() }}) return;
                        const typingIndicator = document.querySelector('.typing-indicator');
                        typingIndicator.style.display = 'flex';
                        typingIndicator.innerHTML = `<span class="dot"></span><span class="dot"></span><span class="dot"></span> ${e.user.name} is typing...`;
                        setTimeout(() => {
                            typingIndicator.style.display = 'none';
                        }, 3000);
                    });
            });
        </script>
    @endif
@endpush