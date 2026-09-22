@extends('layouts.driver')

@section('title', 'Ride Chat - Doonspedo')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1">Chat: {{ $ride->user->name }}</h3>
        <p class="text-muted mb-0 small">Ride #{{ $ride->id }} Details & Communication.</p>
    </div>
    <a href="{{ route('driver.rides.details', $ride->id) }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> RIDE DETAILS
    </a>
</div>

<div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
    <div class="card-body p-0 d-flex flex-column" style="height: 600px;">
        <!-- Chat Area -->
        <div id="chat-messages" class="flex-grow-1 p-4 overflow-auto bg-light bg-opacity-50" style="scroll-behavior: smooth;">
            @if(count($messages) == 0)
                <div class="text-center my-5 py-5 opacity-50">
                    <i class="bi bi-chat-heart display-1 text-brand"></i>
                    <h5 class="mt-3">Start the conversation!</h5>
                    <p class="small">Send a message to {{ $ride->user->name }} about their trip.</p>
                </div>
            @endif
            
            @foreach($messages as $msg)
                <div class="d-flex {{ $msg->sender_type == 'driver' ? 'justify-content-end' : 'justify-content-start' }} mb-3 animate__animated animate__fadeInUp">
                    <div class="max-60">
                        <div class="p-3 rounded-4 shadow-sm {{ $msg->sender_type == 'driver' ? 'bg-dark text-white rounded-bottom-end-0' : 'bg-white text-dark rounded-bottom-start-0' }}">
                            {{ $msg->message }}
                        </div>
                        <div class="small text-muted mt-1 px-2 {{ $msg->sender_type == 'driver' ? 'text-end' : 'text-start' }}">
                            {{ $msg->created_at->format('h:i A') }}
                            @if($msg->sender_type == 'driver')
                                <i class="bi bi-check2{{ $msg->is_read ? '-all text-brand' : '' }} ms-1"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Area -->
        <div class="p-3 border-top bg-white">
            <div class="row gx-2">
                <div class="col-8 col-md-4 mb-2 d-none d-md-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-msg" data-msg="I have arrived!">Arrived</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-msg" data-msg="Coming in 5 mins!">5 Mins</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-msg" data-msg="Where are you?">Where?</button>
                </div>
            </div>
            <form id="chat-form" class="row g-2">
                @csrf
                <div class="col">
                    <input type="text" id="message-input" name="message" class="form-control rounded-pill border-0 bg-light py-3 px-4 shadow-sm" placeholder="Type your message here..." autocomplete="off" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-brand rounded-circle p-0 d-flex align-items-center justify-content-center shadow" style="width: 55px; height: 55px;">
                        <i class="bi bi-send-fill fs-4"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.max-60 { max-width: 80%; }
@media (min-width: 768px) {
    .max-60 { max-width: 60%; }
}
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
}
.text-brand { color: #cddc29 !important; }
.rounded-bottom-end-0 { border-bottom-right-radius: 0 !important; }
.rounded-bottom-start-0 { border-bottom-left-radius: 0 !important; }
</style>

@section('scripts')
<script>
    const chatContainer = document.getElementById('chat-messages');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const msgText = messageInput.value.trim();
        if(!msgText) return;

        messageInput.value = '';
        
        fetch("{{ route('driver.chat.send', $ride->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: msgText })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                appendMessage(data.message, data.time, 'driver');
            }
        });
    });

    document.querySelectorAll('.quick-msg').forEach(btn => {
        btn.addEventListener('click', () => {
            messageInput.value = btn.dataset.msg;
            chatForm.dispatchEvent(new Event('submit'));
        });
    });

    function appendMessage(text, time, type) {
        const div = document.createElement('div');
        div.className = `d-flex ${type == 'driver' ? 'justify-content-end' : 'justify-content-start'} mb-3`;
        div.innerHTML = `
            <div class="max-60">
                <div class="p-3 rounded-4 shadow-sm ${type == 'driver' ? 'bg-dark text-white rounded-bottom-end-0' : 'bg-white text-dark rounded-bottom-start-0'}">
                    ${text}
                </div>
                <div class="small text-muted mt-1 px-2 ${type == 'driver' ? 'text-end' : 'text-start'}">
                    ${time}
                    ${type == 'driver' ? '<i class="bi bi-check2 ms-1"></i>' : ''}
                </div>
            </div>
        `;
        chatContainer.appendChild(div);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Polling for new messages (In production, use WebSockets/Pusher)
    setInterval(() => {
        fetch("{{ route('driver.chat.get', $ride->id) }}")
        .then(response => response.json())
        .then(data => {
            if(data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    const timeStr = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    appendMessage(msg.message, timeStr, 'user');
                });
            }
        });
    }, 5000);
</script>
@endsection
@endsection
