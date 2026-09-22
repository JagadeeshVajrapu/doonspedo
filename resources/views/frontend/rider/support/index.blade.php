@extends('layouts.app')

@section('title', 'Help & Support - Doonspedo')
@section('body_class', 'rider-shell')

@section('content')
<div class="rider-shell-page">
    <header class="rider-topbar">
        <a href="{{ route('rider.app') }}" class="rider-back-btn" aria-label="Back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1>Help &amp; Support</h1>
    </header>

    <main class="rider-content">
        <a href="{{ route('rider.support.create') }}" class="btn btn-brand w-100 py-3 rounded-4 fw-bold shadow-sm mb-4">
            <i class="bi bi-plus-circle me-2"></i>Create New Ticket
        </a>

        <h2 class="h6 text-muted text-uppercase ls-1 mb-3">Your Recent Tickets</h2>

        @if($tickets->isEmpty())
            @include('partials.ui.empty-state', [
                'icon' => 'bi-headset',
                'title' => 'No support tickets yet',
                'message' => 'Create a ticket and our team will help you shortly.',
                'classExtra' => 'bg-white',
            ])
        @else
            @foreach($tickets as $ticket)
                <a href="{{ route('rider.support.show', $ticket->id) }}" class="text-decoration-none d-block">
                    <article class="rider-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h3 class="h6 mb-0 fw-bold text-dark">{{ $ticket->subject }}</h3>
                                <p class="mb-0 x-small text-muted mt-1">Ticket: #{{ $ticket->ticket_id }}</p>
                            </div>
                            <span class="rider-status rider-status-{{ $ticket->status }}">{{ strtoupper($ticket->status) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="x-small text-muted">{{ $ticket->created_at->format('d M, h:i A') }}</span>
                            <i class="bi bi-chevron-right text-brand" aria-hidden="true"></i>
                        </div>
                    </article>
                </a>
            @endforeach
        @endif

        <div class="mt-4">
            <h2 class="h6 text-muted text-uppercase ls-1 mb-3">Quick Links</h2>
            <div class="row g-3">
                <div class="col-6">
                    <a href="{{ url('/#faq') }}" class="rider-card text-decoration-none text-center d-block mb-0">
                        <i class="bi bi-question-circle text-brand fs-3 mb-2 d-block"></i>
                        <span class="small text-dark fw-semibold">FAQs</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('policy.privacy') }}" class="rider-card text-decoration-none text-center d-block mb-0">
                        <i class="bi bi-shield-check text-brand fs-3 mb-2 d-block"></i>
                        <span class="small text-dark fw-semibold">Privacy Policy</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    @include('partials.ui.rider-bottom-nav', ['active' => 'account'])
</div>
@endsection
