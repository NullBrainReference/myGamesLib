@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm" style="height: 75vh;">
                <div class="row g-0 h-100">

                    {{-- Left Sidebar: Conversations --}}
                    <div class="col-md-4 border-end h-100 d-flex flex-column">
                        <div class="p-3 border-bottom bg-light">
                            <h5 class="mb-0">Messages</h5>
                        </div>
                        <div class="overflow-auto flex-grow-1">
                            @forelse($conversations as $convUser)
                                <a href="{{ route('messages.index', $convUser->id) }}"
                                   class="d-flex align-items-center p-3 text-decoration-none border-bottom {{ isset($user) && $user->id === $convUser->id ? 'bg-light fw-bold' : 'text-dark' }}">
                                    <img src="{{ $convUser->profile && $convUser->profile->avatar ? asset('storage/' . $convUser->profile->avatar) : asset('images/default-avatar.png') }}"
                                         class="rounded-circle me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                    <div class="text-truncate">
                                        <div>{{ $convUser->name }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center text-muted p-4">No conversations yet. Add friends to start chatting!</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Right Pane: Chat History & Input --}}
                    <div class="col-md-8 h-100 d-flex flex-column">
                        @if($user)
                            {{-- Header --}}
                            <div class="p-3 border-bottom d-flex align-items-center bg-light">
                                <img src="{{ $user->profile && $user->profile->avatar ? asset('storage/' . $user->profile->avatar) : asset('images/default-avatar.png') }}"
                                     class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <a href="{{ route('profile.view', $user->id) }}" class="small text-muted text-decoration-none">View Profile</a>
                                </div>
                            </div>

                            {{-- Chat Body --}}
                            <div class="p-4 overflow-auto flex-grow-1 bg-white" id="chat-box">
                                @forelse($messages as $msg)
                                    <div class="d-flex mb-3 {{ $msg->sender_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                        <div class="p-3 rounded-3 {{ $msg->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light text-dark border' }}" style="max-width: 70%;">
                                            <p class="mb-1" style="white-space: pre-wrap; word-break: break-word;">{{ $msg->body }}</p>
                                            <small class="d-block text-end opacity-75" style="font-size: 0.7rem;">
                                                {{ $msg->created_at->format('H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted my-auto">
                                        This is the beginning of your direct message history with {{ $user->name }}.
                                    </div>
                                @endforelse
                            </div>

                            {{-- Input Form --}}
                            <div class="p-3 border-top bg-light">
                                <form action="{{ route('messages.store', $user->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="text" name="body" class="form-control" placeholder="Type a message..." required autocomplete="off">
                                    <button class="btn btn-primary" type="submit">Send</button>
                                </form>
                            </div>
                        @else
                            <div class="d-flex h-100 align-items-center justify-content-center text-muted">
                                Select a conversation to start chatting.
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    document.addEventListener('DOMContentLoaded', () => {
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        @if(auth()->check() && isset($user))
            const authUserId = {{ auth()->id() }};
            const activeUserId = {{ $user->id }};

            // Listen for real-time messages sent from Mobile/Web on private channel
            if (window.Echo) {
                window.Echo.private(`chat.${authUserId}`)
                    .listen('.message.sent', (e) => {
                        // Only render if the incoming message is from the currently active contact
                        if (e.message.sender_id === activeUserId) {
                            appendIncomingMessage(e.message);
                        }
                    });
            }
        @endif
    });

    function appendIncomingMessage(message) {
        const chatBox = document.getElementById('chat-box');
        if (!chatBox) return;

        // Remove empty state placeholder if this is the first message
        const emptyState = chatBox.querySelector('.text-muted.my-auto');
        if (emptyState) {
            emptyState.remove();
        }

        // Format timestamp (HH:MM)
        const date = message.created_at ? new Date(message.created_at) : new Date();
        const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

        // Build message bubble
        const msgContainer = document.createElement('div');
        msgContainer.className = 'd-flex mb-3 justify-content-start';

        const bubble = document.createElement('div');
        bubble.className = 'p-3 rounded-3 bg-light text-dark border';
        bubble.style.maxWidth = '70%';

        const bodyParagraph = document.createElement('p');
        bodyParagraph.className = 'mb-1';
        bodyParagraph.style.whiteSpace = 'pre-wrap';
        bodyParagraph.style.wordBreak = 'break-word';
        bodyParagraph.textContent = message.body; // Safe from XSS injection

        const timeSmall = document.createElement('small');
        timeSmall.className = 'd-block text-end opacity-75';
        timeSmall.style.fontSize = '0.7rem';
        timeSmall.textContent = formattedTime;

        bubble.appendChild(bodyParagraph);
        bubble.appendChild(timeSmall);
        msgContainer.appendChild(bubble);

        // Append to chat and auto-scroll
        chatBox.appendChild(msgContainer);
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
@endsection
