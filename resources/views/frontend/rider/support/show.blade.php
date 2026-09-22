@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_id . ' - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page" style="height: 100vh; max-height: 100vh;">
    <header class="rider-topbar">
        <a href="{{ route('rider.support.index') }}" class="rider-back-btn" aria-label="Back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>Ticket #{{ $ticket->ticket_id }}</h1>
    </header>

    <main class="flex-grow-1 p-3 overflow-auto" id="support-box" style="background: var(--ds-page-light);">
        <div class="rider-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 mb-0 fw-bold">{{ $ticket->subject }}</h2>
                <span class="rider-status rider-status-{{ $ticket->status }}">{{ strtoupper($ticket->status) }}</span>
            </div>
            <p class="x-small text-muted mb-0">Priority: <span class="text-brand text-capitalize fw-bold">{{ $ticket->priority }}</span></p>
        </div>

        @foreach($ticket->messages as $msg)
            <div class="d-flex {{ $msg->sender_type == 'user' ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                <div class="message-bubble {{ $msg->sender_type == 'user' ? 'bg-brand text-dark' : 'bg-white border' }} p-3 rounded-4 shadow-sm" style="max-width: 85%;">
                    <p class="mb-1 small fw-medium mb-0">{{ $msg->message }}</p>
                    <div class="d-flex justify-content-end mt-1">
                        <span class="x-small opacity-50" style="font-size: 10px;">{{ $msg->created_at->format('d M, h:i A') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </main>

    @if($ticket->status !== 'closed')
    <footer class="p-3 bg-white border-top sticky-bottom">
        <form action="{{ route('rider.support.reply', $ticket->id) }}" method="POST" class="d-flex gap-2 align-items-center">
            @csrf
            <div class="input-group bg-light border rounded-pill p-1 shadow-sm overflow-hidden flex-grow-1">
                <input type="text" name="message" class="form-control bg-transparent border-0 shadow-none px-3" placeholder="Type your reply..." autocomplete="off" required>
                <button type="submit" class="btn btn-brand rounded-circle p-0 d-flex align-items-center justify-content-center me-1" style="width: 40px; height: 40px;" aria-label="Send reply">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>
    </footer>
    @endif
</div>

<style>
#support-box::-webkit-scrollbar { width: 0; }
.message-bubble { border-radius: 18px !important; }
</style>

<script>
const supportBox = document.getElementById('support-box');
supportBox.scrollTop = supportBox.scrollHeight;
</script>
@endsection
