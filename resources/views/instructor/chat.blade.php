
@extends('Instructor.layout.Instructor_layout')

@section('title', 'Chat')
@section('instructor')
    <div class="chat-wrapper compact">
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex align-items-center">
                    <div class="chat-user-online">
                        <img src="{{ Auth::guard('instructor')->user()->photo && file_exists(public_path('upload/instructor_images/' . Auth::guard('instructor')->user()->photo)) ? asset('upload/instructor_images/' . Auth::guard('instructor')->user()->photo) : asset('upload/no_image.jpg') }}"
                             width="100" height="100" class="rounded-circle user-avatar" alt="{{ Auth::guard('instructor')->user()->name }}"
                             style="object-fit: cover;" loading="lazy" />
                        <span class="online-status"></span>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0 user-name" style="font-size: 14px; font-weight: 600;">{{ Auth::guard('instructor')->user()->name }}</p>
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
                                    <a href="{{ route('instructor.messages.show', $conversation->id) }}" class="list-group-item conversation-item {{ $conversation->id == $selectedConversation?->id ? 'active' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="chat-user-online">
                                                <img src="{{ $conversation->user->photo && \Storage::disk('public')->exists('upload/user_images/' . $conversation->user->photo) ? asset('storage/upload/user_images/' . $conversation->user->photo) : asset('upload/no_image.jpg') }}"
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
                                <div> <button class="scroll-to-bottom sidebar-scroll three-d" style="display: none;" aria-label="Scroll to bottom of conversations"><i class='bx bx-down-arrow-alt'></i></button></div>
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
                    <img src="{{ $selectedConversation->user->photo && \Storage::disk('public')->exists('upload/user_images/' . $selectedConversation->user->photo) ? asset('storage/upload/user_images/' . $selectedConversation->user->photo) : asset('upload/no_image.jpg') }}"
                         width="40" height="40" class="rounded-circle user-avatar" alt="{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}"
                         style="object-fit: cover;" loading="lazy" />
                    <div class="ms-2">
                        <h4 class="mb-0 chat-user-name" style="font-size: 15px;">{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}</h4>
                        <small class="chat-status" style="font-size: 11px;">{{ $selectedConversation->user->is_online ? 'Active Now' : 'Offline' }}</small>
                    </div>
                </div>
            </div>
            <div class="chat-content">
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
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ Auth::guard('instructor')->user()->name }}"
                                     style="object-fit: cover;" loading="lazy" />
                            </div>
                        </div>
                    @else
                        <div class="chat-content-leftside">
                            <div class="d-flex">
                                <img src="{{ $selectedConversation->user->photo && \Storage::disk('public')->exists('upload/user_images/' . $selectedConversation->user->photo) ? asset('storage/upload/user_images/' . $selectedConversation->user->photo) : asset('upload/no_image.jpg') }}"
                                     width="36" height="36" class="rounded-circle user-avatar" alt="{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}"
                                     style="object-fit: cover;" loading="lazy" />
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 chat-time" style="font-size: 10px;">{{ $selectedConversation->user->name }}, {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</p>
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
                    <form id="message-form" action="{{ route('instructor.messages.send', $selectedConversation->id) }}" method="POST" aria-label="Send message">
                        @csrf
                        <div class="input-group input-group-sm three-d">
                            <span class="input-group-text"><i class='bx bx-smile'></i></span>
                            <input type="text" name="message" class="form-control message-input" placeholder="Type a message..." style="font-size: 13px;" required aria-label="Message input">
                            <button type="submit" class="input-group-text send-btn three-d" aria-label="Send message"><i class='bx bx-send'></i></button>
                        </div>
                        @error('message')
                            <span class="text-danger error-message" style="font-size: 11px;">{{ $message }}</span>
                        @enderror
                    </form>
                </div>
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
            max-height: calc(100vh - 240px); /* Adjust based on header and footer height */
        }
        .scroll-to-bottom {
            position: sticky;
            bottom: 10px;
            right: 10px;
            float: right;
        }
        .chat-right-msg {
            background: #6992f0; /* Soft blue for sender */
            color: #000000; /* White text for contrast */
            padding: 10px 14px;
            border-radius: 12px;
        }
        .chat-left-msg {
            background: #f06969; /* Vibrant coral for receiver */
            color: #000000; /* White text for contrast */
            padding: 10px 14px;
            border-radius: 12px;
            border: none; /* Remove border for cleaner look */
        }
    </style>
@endpush

@push('scripts')
    @if($selectedConversation)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const chatContent = document.querySelector('.chat-content');
                const scrollToBottomBtn = document.querySelector('.scroll-to-bottom');

                // Initial scroll to bottom
                chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });

                // Show/hide scroll-to-bottom button
                chatContent.addEventListener('scroll', () => {
                    const isNearBottom = chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < 100;
                    scrollToBottomBtn.style.display = isNearBottom ? 'none' : 'flex';
                });

                // Scroll to bottom on button click
                scrollToBottomBtn.addEventListener('click', () => {
                    chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });
                });

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
            });

            Echo.private(`conversation.{{ $selectedConversation->id }}`)
                .listen('MessageSent', (e) => {
                    const chatContent = document.querySelector('.chat-content');
                    const scrollToBottomBtn = document.querySelector('.scroll-to-bottom');
                    const messageDiv = document.createElement('div');
                    const isCurrentUser = e.sender_id === {{ Auth::guard('instructor')->id() }} && e.sender_type === 'App\\Models\\Instructor';
                    messageDiv.className = isCurrentUser ? 'chat-content-rightside' : 'chat-content-leftside';
                    messageDiv.style.opacity = '0';
                    messageDiv.innerHTML = isCurrentUser ?
                        `<div class="d-flex">
                             <div class="flex-grow-1 me-2">
                                 <p class="mb-0 chat-time text-end" style="font-size: 10px;">Just now</p>
                                 <p class="chat-right-msg three-d" data-message-id="${e.message_id}">${e.message}</p>
                             </div>
                             <img src="{{ Auth::guard('instructor')->user()->photo && file_exists(public_path('upload/instructor_images/' . Auth::guard('instructor')->user()->photo)) ? asset('upload/instructor_images/' . Auth::guard('instructor')->user()->photo) : asset('upload/no_image.jpg') }}"
                                  width="36" height="36" class="rounded-circle user-avatar" alt="{{ Auth::guard('instructor')->user()->name }}"
                                  style="object-fit: cover;" loading="lazy" />
                         </div>` :
                        `<div class="d-flex">
                             <img src="{{ $selectedConversation->user->photo && \Storage::disk('public')->exists('upload/user_images/' . $selectedConversation->user->photo) ? asset('storage/upload/user_images/' . $selectedConversation->user->photo) : asset('upload/no_image.jpg') }}"
                                  width="36" height="36" class="rounded-circle user-avatar" alt="{{ $selectedConversation->user->name ?? 'Utilisateur inconnu' }}"
                                  style="object-fit: cover;" loading="lazy" />
                             <div class="flex-grow-1 ms-2">
                                 <p class="mb-0 chat-time" style="font-size: 10px;">{{ $selectedConversation->user->name }}, Just now</p>
                                 <p class="chat-left-msg three-d" data-message-id="${e.message_id}">${e.message}</p>
                             </div>
                         </div>`;
                    chatContent.appendChild(messageDiv);
                    setTimeout(() => {
                        messageDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        messageDiv.style.opacity = '1';
                        messageDiv.style.transform = 'translateY(0)';
                    }, 10);

                    // Auto-scroll only if near bottom
                    const isNearBottom = chatContent.scrollHeight - chatContent.scrollTop - chatContent.clientHeight < 100;
                    if (isNearBottom) {
                        chatContent.scrollTo({ top: chatContent.scrollHeight, behavior: 'smooth' });
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
