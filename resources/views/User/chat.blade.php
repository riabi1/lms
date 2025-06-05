
@extends('User.layout.User_layout')

@section('title', 'Chat')

@section('userdashboard')
    <div class="chat-wrapper compact">
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex align-items-center">
                    <div class="chat-user-online">
                        <img class="rounded-circle me-3 shadow-sm"
                             src="{{ Auth::guard('web')->user() ? (Auth::guard('web')->user()->photo ? asset('upload/user_images/' . Auth::guard('web')->user()->photo) : asset('upload/no_image.jpg')) : asset('upload/no_image.jpg') }}"
                             width="100" height="100" class="rounded-circle user-avatar" alt="{{ Auth::guard('web')->user()?->name ?? 'User' }}"
                             style="object-fit: cover;" loading="lazy" />
                        <span class="online-status"></span>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0 user-name" style="font-size: 14px; font-weight: 600;">{{ Auth::guard('web')->user()?->name ?? 'User' }}</p>
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
                                    <a href="{{ route('messages.show', $conversation->id) }}" class="list-group-item conversation-item {{ $conversation->id == $selectedConversation?->id ? 'active' : '' }}"
                                       data-conversation-id="{{ $conversation->id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="chat-user-online">
                                                <img src="{{ $conversation->instructor?->photo && file_exists(public_path('upload/instructor_images/' . $conversation->instructor->photo)) ? asset('upload/instructor_images/' . $conversation->instructor->photo) : asset('upload/no_image.jpg') }}"
                                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ $conversation->instructor?->name ?? 'Instructeur inconnu' }}"
                                                     style="object-fit: cover;" loading="lazy" />
                                                <span>online</span>
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
                    <img src="{{ $selectedConversation->instructor?->photo && file_exists(public_path('upload/instructor_images/' . $selectedConversation->instructor->photo)) ? asset('upload/instructor_images/' . $selectedConversation->instructor->photo) : asset('upload/no_image.jpg') }}"
                         width="40" height="40" class="rounded-circle user-avatar" alt="{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}"
                         style="object-fit: cover;" loading="lazy" />
                    <div class="ms-2">
                        <h4 class="mb-0 chat-user-name" style="font-size: 15px;">{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}</h4>
                        <small class="chat-status" style="font-size: 11px;">online</small>
                    </div>
                </div>
            </div>
            <div class="chat-content" id="chat-content">
                @forelse($selectedConversation->messages as $message)
                    @if($message->sender_id == Auth::id() && $message->sender_type == 'App\\Models\\User')
                        <div class="chat-content-rightside" data-message-id="{{ $message->id }}">
                            <div class="d-flex">
                                <div class="flex-grow-1 me-2">
                                    <p class="mb-0 chat-time text-end" style="font-size: 10px;">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</p>
                                    <p class="chat-right-msg three-d">{{ $message->message }}</p>
                                </div>
                                <img src="{{ Auth::guard('web')->user()->photo ? asset('upload/user_images/' . Auth::guard('web')->user()->photo) : asset('upload/no_image.jpg') }}"
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ Auth::guard('web')->user()?->name ?? 'User' }}"
                                     style="object-fit: cover;" loading="lazy" />
                            </div>
                        </div>
                    @else
                        <div class="chat-content-leftside" data-message-id="{{ $message->id }}">
                            <div class="d-flex">
                                <img src="{{ $selectedConversation->instructor?->photo && file_exists(public_path('upload/instructor_images/' . $selectedConversation->instructor->photo)) ? asset('upload/instructor_images/' . $selectedConversation->instructor->photo) : asset('upload/no_image.jpg') }}"
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}"
                                     style="object-fit: cover;" loading="lazy" />
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 chat-time" style="font-size: 10px;">{{ $selectedConversation->instructor?->name ?? 'Instructeur inconnu' }}, {{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</p>
                                    <p class="chat-left-msg three-d">{{ $message->message }}</p>
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
                    <form id="message-form" method="POST" aria-label="Send message">
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
    </style>
@endpush

@if($selectedConversation)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.7.5/socket.io.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chatContent = document.querySelector('#chat-content');
            const conversationList = document.querySelector('#conversation-list');
            const scrollToBottomBtn = document.querySelector('.scroll-to-bottom:not(.sidebar-scroll)');
            const messageForm = document.querySelector('#message-form');
            const messageInput = document.querySelector('.message-input');
            const errorMessage = document.querySelector('.error-message');
            const sentMessageIds = new Set();
            const optimisticMessages = new Map(); // Track optimistic messages by tempId

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

            // Initialize Socket.IO
            const socket = io('{{ env('SOCKET_IO_URL', 'http://localhost:3000') }}', {
                withCredentials: true
            });

            // Join conversation
            socket.emit('joinConversation', {{ $selectedConversation->id }}, {{ Auth::id() }}, 'App\\Models\\User');
            console.log('User joined conversation:', {{ $selectedConversation->id }});

            // Send message
            messageForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;

                // Optimistically render the message
                const tempId = `temp-${Date.now()}-${Math.random().toString(36).slice(2)}`;
                const authUser = {
                    name: '{{ Auth::guard('web')->user()->name ?? 'Anonymous' }}',
                    photo: '{{ Auth::guard('web')->user()->photo ? asset('upload/user_images/' . Auth::guard('web')->user()->photo) : asset('upload/no_image.jpg') }}'
                };
                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-content-rightside';
                messageDiv.dataset.tempId = tempId;
                messageDiv.dataset.messageId = ''; // Will be updated on server response
                messageDiv.style.opacity = '0.5'; // Indicate pending state
                messageDiv.innerHTML = `
                    <div class="d-flex">
                        <div class="flex-grow-1 me-2">
                            <p class="mb-0 chat-time text-end" style="font-size: 10px;">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                            <p class="chat-right-msg three-d">${message}</p>
                        </div>
                        <img src="${authUser.photo}" 
                             width="36" height="36" class="rounded-circle user-avatar" alt="${authUser.name}" 
                             style="object-fit: cover;" loading="lazy" />
                    </div>`;
                chatContent.appendChild(messageDiv);
                optimisticMessages.set(tempId, messageDiv);
                messageInput.value = ''; // Clear input immediately
                chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });

                try {
                    const response = await fetch('{{ route("messages.send", $selectedConversation->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ message }),
                    });

                    if (!response.ok) throw new Error('Failed to send message');

                    const data = await response.json();
                    if (data.status === 'success') {
                        errorMessage.style.display = 'none';

                        // Update optimistic message
                        if (optimisticMessages.has(tempId)) {
                            const messageDiv = optimisticMessages.get(tempId);
                            messageDiv.dataset.messageId = data.message.message_id;
                            messageDiv.style.opacity = '1'; // Confirm message
                            messageDiv.removeAttribute('data-tempId');
                            optimisticMessages.delete(tempId);
                            sentMessageIds.add(data.message.message_id);
                            updateConversationList(data.message, data.conversation);
                            console.log('Sender (User) message confirmed:', data.message);
                        }
                    } else {
                        throw new Error(data.error || 'Failed to send message');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    if (optimisticMessages.has(tempId)) {
                        const messageDiv = optimisticMessages.get(tempId);
                        messageDiv.remove(); // Remove optimistic message on failure
                        optimisticMessages.delete(tempId);
                    }
                    errorMessage.textContent = error.message || 'Failed to send message';
                    errorMessage.style.display = 'block';
                }
            });

            // Listen for incoming messages
            socket.on('message', (data) => {
                console.log('Receiver (User) message received:', data);

                if (sentMessageIds.has(data.message_id)) return; // Skip already processed messages
                sentMessageIds.add(data.message_id);

                // Skip sender's own message to avoid duplication
                if (data.sender_id === {{ Auth::id() }} && data.sender_type === 'App\\Models\\User') return;

                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-content-leftside';
                messageDiv.dataset.messageId = data.message_id;
                messageDiv.style.opacity = '0';
                messageDiv.innerHTML = `
                    <div class="d-flex">
                        <img src="${data.sender_photo}" 
                             width="36" height="36" class="rounded-circle user-avatar" alt="${data.sender_name}" 
                             style="object-fit: cover;" loading="lazy" />
                        <div class="flex-grow-1 ms-2">
                            <p class="mb-0 chat-time" style="font-size: 10px;">${data.sender_name}, ${new Date(data.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
                            <p class="chat-left-msg three-d">${data.message}</p>
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
                    message: data.message,
                    message_id: data.message_id,
                    sender_name: data.sender_name,
                    sender_photo: data.sender_photo
                }, {
                    id: {{ $selectedConversation->id }},
                    last_message_at: data.created_at
                });
            });

            function updateConversationList(message, conversation) {
                let conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${message.conversation_id}"]`);
                if (conversationItem) {
                    const chatMsg = conversationItem.querySelector('.chat-msg');
                    const chatTime = conversationItem.querySelector('.chat-time');
                    chatMsg.textContent = message.message.substring(0, 20) + (message.message.length > 20 ? '...' : '');
                    chatTime.textContent = new Date(conversation.last_message_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    conversationItem.classList.add('active');
                    conversationList.prepend(conversationItem);
                }
            }
        });
    </script>
@endif
