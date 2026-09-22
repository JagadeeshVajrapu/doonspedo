@extends('layouts.app')

@section('title', 'Chat with Driver - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="app-container d-flex flex-column vh-100 bg-light">
    <!-- Header -->
    <header class="rider-topbar">
        <a href="{{ route('rider.app') }}" class="rider-back-btn" aria-label="Back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="d-flex align-items-center flex-grow-1">
            <div class="position-relative">
                <img src="{{ $ride->driver->profile_image ? asset('uploads/profiles/' . $ride->driver->profile_image) : 'https://i.pravatar.cc/100?u=' . $ride->driver->id }}" alt="{{ $ride->driver->name }}" class="rounded-circle border" style="width: 40px; height: 40px; object-fit:cover;">
                <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 10px; height: 10px;"></span>
            </div>
            <div class="ms-3">
                <h1 class="h6 mb-0 fw-bold">{{ $ride->driver->name }}</h1>
                <p class="mb-0 x-small text-brand">Driver chat</p>
            </div>
        </div>
        <a href="tel:{{ $ride->driver->mobile }}" class="btn btn-light border rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" aria-label="Call driver">
            <i class="bi bi-telephone-fill text-brand"></i>
        </a>
    </header>

    <!-- Chat Messages -->
    <main class="flex-grow-1 p-3 overflow-auto" id="chat-box" style="background: var(--ds-page-light, #f3f5f7);">
        <div class="text-center mb-4">
            <span class="badge bg-light text-muted border rounded-pill px-3 py-1 x-small text-uppercase">Ride #{{ $ride->id }}</span>
        </div>

        @foreach($messages as $msg)
            <div class="d-flex {{ $msg->sender_type == 'user' ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                <div class="message-bubble {{ $msg->sender_type == 'user' ? 'bg-brand text-dark' : 'bg-white text-dark border' }} p-3 rounded-4 shadow-sm" style="max-width: 80%;">
                    <p class="mb-1 small fw-medium">{{ $msg->message }}</p>
                    <div class="d-flex justify-content-end">
                        <span class="x-small opacity-50" style="font-size: 10px;">{{ $msg->created_at->format('h:i A') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </main>

    <!-- Message Input -->
    <footer class="p-3 bg-white border-top sticky-bottom">
        <form id="chat-form" class="d-flex gap-2 align-items-center">
            @csrf
            <div class="input-group bg-light border rounded-pill p-1 shadow-sm overflow-hidden flex-grow-1">
                <button type="button" class="btn btn-link text-secondary border-0" aria-hidden="true"><i class="bi bi-plus-circle fs-5"></i></button>
                <input type="text" id="message-input" class="form-control bg-transparent border-0 shadow-none px-2" placeholder="Write a message..." autocomplete="off">
                <button type="submit" class="btn btn-brand rounded-circle p-0 d-flex align-items-center justify-content-center me-1" style="width: 40px; height: 40px;" aria-label="Send">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>
    </footer>
</div>

<style>
:root { --primary-color: #cddc29; }
.bg-dark-custom { background-color: #1a1a1a; }
.text-brand { color: var(--primary-color) !important; }
.btn-brand { background-color: var(--primary-color); color: #000; }
.x-small { font-size: 0.75rem; }
#chat-box::-webkit-scrollbar { width: 0; }
.message-bubble { position: relative; }
.message-bubble::after {
    content: '';
    position: absolute;
    width: 0; height: 0;
    bottom: 5px;
}
.justify-content-end .message-bubble::after {
    right: -8px;
    border-left: 10px solid var(--primary-color);
    border-top: 10px solid transparent;
}
.justify-content-start .message-bubble::after {
    left: -8px;
    border-right: 10px solid #1a1a1a;
    border-top: 10px solid transparent;
}
</style>

<script>
const chatBox = document.getElementById('chat-box');
chatBox.scrollTop = chatBox.scrollHeight;

document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('message-input');
    const msg = input.value.trim();
    if (!msg) return;

    input.value = '';
    
    fetch('{{ route("chat.send", $ride->id) }}', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ message: msg })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            appendMessage(data.message, data.time, 'user');
        }
    });
});

function appendMessage(msg, time, type) {
    const div = document.createElement('div');
    div.className = `d-flex ${type == 'user' ? 'justify-content-end' : 'justify-content-start'} mb-3`;
    div.innerHTML = `
        <div class="message-bubble ${type == 'user' ? 'bg-brand text-dark' : 'bg-white text-dark border'} p-3 rounded-4 shadow-sm" style="max-width: 80%;">
            <p class="mb-1 small fw-medium">${msg}</p>
            <div class="d-flex justify-content-end">
                <span class="x-small opacity-50" style="font-size: 10px;">${time}</span>
            </div>
        </div>
    `;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Poll for new messages
setInterval(() => {
    fetch('{{ route("chat.messages", $ride->id) }}')
    .then(response => response.json())
    .then(data => {
        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(m => {
                appendMessage(m.message, new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}), 'driver');
            });
        }
    });
}, 3000);
</script>
@endsection
