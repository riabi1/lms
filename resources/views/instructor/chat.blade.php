
@extends('Instructor.layout.Instructor_layout')

@section('title', 'Chat')

@section('instructor')
    <div class="chat-wrapper compact">
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex align-items-center">
                    <div class="chat-user-online">
                        <img src="{{ Auth::guard('instructor')->user()->photo && file_exists(public_path('upload/instructor_images/' . Auth::guard('instructor')->user()->photo)) ? asset('upload/instructor_images/' . Auth::guard('instructor')->user()->photo) : asset('upload/no_image.jpg') }}"
                             width="100" height="100" class="rounded-circle user-avatar" alt="{{ Auth::guard('instructor')->user()->name ?? 'Instructor' }}"
                             style="object-fit: cover;" loading="lazy" />
                        <span class="online-status"></span>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0 user-name" style="font-size: 14px; font-weight: 600;">{{ Auth::guard('instructor')->user()->name ?? 'Instructor' }}</p>
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
                            <div class="list-group list-group-flush" id="conversation-list">
                                @forelse($conversations as $conversation)
                                    <a href="{{ route('instructor.messages.show', $conversation->id) }}" class="list-group-item conversation-item {{ $conversation->id == $selectedConversation?->id ? 'active' : '' }}"
                                       data-conversation-id="{{ $conversation->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="chat-user-online">
                                                <img src="{{ $conversation->user->photo && file_exists(public_path('upload/user_images/' . $conversation->user->photo)) ? asset('upload/user_images/' . $conversation->user->photo) : asset('upload/no_image.jpg') }}"
                                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ $conversation->user->name ?? 'Utilisateur inconnu' }}"
                                                     style="object-fit: cover;" loading="lazy" />
                                                <span class="online-status {{ $conversation->user->is_online ? 'online' : 'offline' }}"></span>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h6 class="mb-0 chat-title" style="font-size: 13px;">{{ $conversation->user->name ?? 'Utilisateur inconnu' }}</h6>
                                                <p class="mb-0 chat-msg" style="font-size: 11px;">{{ Str::limit($conversation->messages->last()->message ?? 'No message', 20) }}</p>
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
                    <img src="{{ $selectedConversation->user->photo && file_exists(public_path('upload/user_images/' . $selectedConversation->user->photo)) ? asset('upload/user_images/' . $selectedConversation->user->photo) : asset('upload/no_image.jpg') }}"
                         width="40" height="40" class="rounded-circle user-avatar" alt="{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}"
                         style="object-fit: cover;" loading="lazy" />
                    <div class="ms-2">
                        <h4 class="mb-0 chat-user-name" style="font-size: 15px;">{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}</h4>
                        <small class="chat-status" style="font-size: 11px;">{{ $selectedConversation->user->is_online ? 'Active Now' : 'Offline' }}</small>
                    </div>
                </div>
            </div>
            <div class="chat-content" id="chat-content">
                <div class="typing-indicator" style="display: none;">
                    <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                </div>
                @forelse($selectedConversation->messages as $message)
                    @if($message->sender_id == Auth::guard('instructor')->id() && $message->sender_type == 'App\\Models\\Instructor')
                        <div class="chat-content-rightside">
                            <div class="d-flex">
                                <div class="flex-grow-1 me-2">
                                    <p class="mb-0 chat-time text-end" style="font-size: 10px;">{{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
                                    <p class="chat-right-msg three-d" data-message-id="{{ $message->id }}">{{ $message->message }}</p>
                                </div>
                                <img src="{{ Auth::guard('instructor')->user()->photo && file_exists(public_path('upload/instructor_images/' . Auth::guard('instructor')->user()->photo)) ? asset('upload/instructor_images/' . Auth::guard('instructor')->user()->photo) : asset('upload/no_image.jpg') }}"
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ Auth::guard('instructor')->user()->name ?? 'Instructor' }}"
                                     style="object-fit: cover;" loading="lazy" />
                            </div>
                        </div>
                    @else
                        <div class="chat-content-leftside">
                            <div class="d-flex">
                                <img src="{{ $selectedConversation->user->photo && file_exists(public_path('upload/user_images/' . $selectedConversation->user->photo)) ? asset('upload/user_images/' . $selectedConversation->user->photo) : asset('upload/no_image.jpg') }}"
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}"
                                     style="object-fit: cover;" loading="lazy" />
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 chat-time" style="font-size: 10px;">{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}, {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
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
                    <form id="message-form" aria-label="Send message">
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
            position: relative;
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
            z-index: 10;
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
            display: flex;
            align-items: center;
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
        <script src="{{ asset('vendor/laravel-reverb/reverb.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                console.log('Initializing chat for conversation {{ $selectedConversation->id }}');

                // Initialize WebSocket connection with Reverb
                try {
                    window.Echo = new Echo({
                        broadcaster: 'reverb',
                        key: '{{ env('REVERB_APP_KEY') }}',
                        wsHost: '{{ env('REVERB_HOST', 'localhost') }}',
                        wsPort: '{{ env('REVERB_PORT', 8080) }}',
                        wssPort: '{{ env('REVERB_PORT', 8080) }}',
                        scheme: '{{ env('REVERB_SCHEME', 'http') }}',
                        authEndpoint: '/broadcasting/auth',
                        disableStats: false,
                        forceTLS: '{{ env('REVERB_SCHEME', 'http') === 'https' }}',
                    });
                    console.log('Echo initialized successfully');
                } catch (error) {
                    console.error('Failed to initialize Echo:', error);
                }

                const chatContent = document.querySelector('#chat-content');
                const conversationList = document.querySelector('#conversation-list');
                const scrollToBottomBtn = document.querySelector('.scroll-to-bottom');
                const messageForm = document.querySelector('#message-form');
                const messageInput = document.querySelector('.message-input');
                const errorMessage = document.querySelector('.error-message');
                const typingIndicator = document.querySelector('.typing-indicator');
                let lastMessageId = {{ $selectedConversation->messages->max('id') ?? 0 }};

                // Scroll to bottom on load
                chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });

                // Show/hide scroll-to-bottom button
                chatContent.addEventListener('scroll', () => {
                    const isNearBottom = chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < 100;
                    scrollToBottomBtn.style.display = isNearBottom ? 'none' : 'flex';
                });

                scrollToBottomBtn.addEventListener('click', () => {
                    chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });
                });

                // Handle message form submission
                messageForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    console.log('Form submission intercepted');
                    const message = messageInput.value.trim();
                    if (!message) return;

                    try {
                        const response = await fetch('{{ route("instructor.messages.send", $selectedConversation->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ message }),
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.status === 'success') {
                            console.log('Message sent successfully:', data);
                            messageInput.value = '';
                            errorMessage.style.display = 'none';
                            errorMessage.textContent = '';

                            // Append the message for the sender
                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'chat-content-rightside';
                            messageDiv.style.opacity = '0';
                            messageDiv.innerHTML = `
                                <div class="d-flex">
                                    <div class="flex-grow-1 me-2">
                                        <p class="mb-0 chat-time text-end" style="font-size: 10px;">Just now</p>
                                        <p class="chat-right-msg three-d" data-message-id="${data.message.message_id}">${data.message.message}</p>
                                    </div>
                                    <img src="${data.message.sender_photo}" 
                                         width="36" height="36" class="rounded-circle user-avatar" alt="${data.message.sender_name}" 
                                         style="object-fit: cover;" loading="lazy" />
                                </div>`;
                            chatContent.appendChild(messageDiv);
                            setTimeout(() => {
                                messageDiv.style.transition = 'opacity 0.3s ease';
                                messageDiv.style.opacity = '1';
                            }, 10);
                            chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });
                            lastMessageId = Math.max(lastMessageId, data.message.message_id);

                            // Update conversation list
                            updateConversationList(data.message, data.conversation);
                        } else {
                            errorMessage.textContent = data.error || 'Failed to send message';
                            errorMessage.style.display = 'block';
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        errorMessage.textContent = 'Failed to send message. Please try again.';
                        errorMessage.style.display = 'block';
                    }
                });

                // Handle typing indicator
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

                // Function to update conversation list
                function updateConversationList(message, conversation) {
                    let conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${message.conversation_id}"]`);
                    if (conversationItem) {
                        const chatMsg = conversationItem.querySelector('.chat-msg');
                        const chatTime = conversationItem.querySelector('.chat-time');
                        chatMsg.textContent = message.message.substring(0, 20) + (message.message.length > 20 ? '...' : '');
                        chatTime.textContent = 'Just now';
                        conversationList.prepend(conversationItem);
                    }
                }

                // Listen for new messages
                if (window.Echo) {
                    Echo.private(`conversation.{{ $selectedConversation->id }}`)
                        .listen('.MessageSent', (e) => {
                            console.log('MessageSent event received:', e);
                            if (e.message_id <= lastMessageId) {
                                console.log('Duplicate message, skipping:', e.message_id);
                                return;
                            }
                            lastMessageId = e.message_id;

                            const isCurrentUser = e.sender_id === {{ Auth::guard('instructor')->id() }} && e.sender_type === 'App\\Models\\Instructor';
                            if (isCurrentUser) {
                                console.log('Message from current user, already appended');
                                return;
                            }

                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'chat-content-leftside';
                            messageDiv.style.opacity = '0';
                            messageDiv.innerHTML = `
                                <div class="d-flex">
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

                            updateConversationList({
                                conversation_id: {{ $selectedConversation->id }},
                                message: e.message,
                                message_id: e.message_id,
                                sender_name: e.sender_name,
                                sender_photo: e.sender_photo
                            }, {
                                id: {{ $selectedConversation->id }},
                                last_message_at: new Date().toISOString()
                            });
                        })
                        .listen('.Typing', (e) => {
                            console.log('Typing event received:', e);
                            if (e.user.id === {{ Auth::guard('instructor')->id() }}) return;
                            typingIndicator.style.display = 'flex';
                            typingIndicator.innerHTML = `<span class="dot"></span><span class="dot"></span><span class="dot"></span> ${e.user.name} is typing...`;
                            setTimeout(() => {
                                typingIndicator.style.display = 'none';
                            }, 3000);
                        });

                    Echo.connector.reverb.on('connect', () => {
                        console.log('WebSocket connected to Reverb');
                        errorMessage.style.display = 'none';
                    });

                    Echo.connector.reverb.on('disconnect', () => {
                        console.warn('WebSocket disconnected from Reverb');
                        errorMessage.textContent = 'Connection lost. Reconnecting...';
                        errorMessage.style.display = 'block';
                    });
                }
            });
        </script>
    @endif
@endpush
