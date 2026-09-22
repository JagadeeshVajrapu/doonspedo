@extends('layouts.app')

@section('title', 'Create Support Ticket - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page">
    <header class="rider-topbar">
        <a href="{{ route('rider.support.index') }}" class="rider-back-btn" aria-label="Back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>Support Center</h1>
    </header>

    <main class="rider-content">
        <div class="text-center mb-4">
            <div class="ds-stat-icon mx-auto mb-3" style="width:64px;height:64px;font-size:1.75rem;">
                <i class="bi bi-headset"></i>
            </div>
            <h2 class="h4 fw-bold">How can we help?</h2>
            <p class="text-muted small mb-0">Start a conversation and we'll get back to you shortly.</p>
        </div>

        <form action="{{ route('rider.support.store') }}" method="POST" class="rider-card p-4">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase ls-1" for="support-subject">Subject</label>
                <input type="text" id="support-subject" name="subject" class="form-control clean-input" placeholder="e.g., Payment Issue, Late Driver..." required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase ls-1">Priority</label>
                <div class="d-flex gap-2">
                    @foreach(['low', 'medium', 'high'] as $p)
                        <label class="flex-grow-1 mb-0">
                            <input type="radio" name="priority" value="{{ $p }}" {{ $p == 'medium' ? 'checked' : '' }} class="d-none">
                            <div class="priority-card p-3 border rounded-4 text-center cursor-pointer">
                                <span class="small fw-bold text-capitalize">{{ $p }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase ls-1" for="support-message">Message Detail</label>
                <textarea id="support-message" name="message" class="form-control clean-input" rows="6" placeholder="Please describe your issue in detail..." required></textarea>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-3 rounded-4 fw-bold shadow">
                <i class="bi bi-send-fill me-2"></i>Submit Support Request
            </button>
        </form>
    </main>
</div>

<style>
.priority-card { transition: all 0.2s ease; background: #fff; }
input[type="radio"]:checked + .priority-card {
    background-color: var(--ds-brand, #cddc29);
    border-color: var(--ds-brand, #cddc29) !important;
}
input[type="radio"]:checked + .priority-card span { color: #111 !important; }
</style>
@endsection
