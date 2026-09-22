@extends('layouts.driver')

@section('title', 'Notifications - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Notifications</h1>
        <p class="text-muted mb-0 small">Stay updated with ride requests, payments, and system alerts.</p>
    </div>
    @if($notifications->where('is_read', false)->count() > 0)
    <form action="{{ route('driver.notifications.markAllRead') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-light border rounded-pill px-4 fw-bold">
            <i class="bi bi-check-all me-1"></i> Mark all as read
        </button>
    </form>
    @endif
</div>

<div class="drv-card overflow-hidden">
    <div class="list-group list-group-flush">
        @if(isset($notifications) && count($notifications) > 0)
            @foreach($notifications as $notification)
            <a href="{{ route('driver.notifications.read', $notification->id) }}"
               class="list-group-item list-group-item-action p-3 p-md-4 border-0 {{ !$notification->is_read ? 'bg-light bg-opacity-50 border-start border-4 border-brand' : '' }}">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        @php
                            $iconMap = [
                                'ride' => 'bi-car-front-fill text-primary',
                                'chat' => 'bi-chat-dots-fill text-success',
                                'payment' => 'bi-cash-stack text-brand-dark',
                                'system' => 'bi-info-circle-fill text-info',
                                'general' => 'bi-bell-fill text-muted'
                            ];
                            $icon = $iconMap[$notification->type] ?? $iconMap['general'];
                            $typeLabel = ucfirst($notification->type ?? 'general');
                        @endphp
                        <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi {{ $icon }} fs-5" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                            <h2 class="h6 fw-bold mb-0 {{ !$notification->is_read ? 'text-dark' : 'text-muted' }}">{{ $notification->title }}</h2>
                            <time class="small text-muted" datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
                        </div>
                        <div class="extra-small text-muted text-uppercase fw-bold mb-1">{{ $typeLabel }} · {{ $notification->is_read ? 'Read' : 'Unread' }}</div>
                        <p class="mb-0 {{ !$notification->is_read ? 'text-dark' : 'text-muted' }} small">{{ $notification->message }}</p>
                    </div>
                    @if(!$notification->is_read)
                    <div class="ms-2">
                        <span class="badge bg-brand rounded-circle p-1" style="width: 10px; height: 10px; display: inline-block;" aria-label="Unread"></span>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        @else
            <div class="p-4">
                @include('partials.ui.empty-state', [
                    'title' => 'No notifications yet',
                    'message' => "We'll notify you when something important happens.",
                    'icon' => 'bi-bell-slash',
                ])
            </div>
        @endif
    </div>
    @if($notifications->hasPages())
    <div class="p-4 border-top">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

<style>
.text-brand-dark { color: #82a800 !important; }
.border-brand { border-color: #cddc29 !important; }
.bg-brand { background-color: #cddc29 !important; }
.list-group-item-action:hover { background-color: #f8f9fa !important; }
.extra-small { font-size: 0.65rem; }
</style>
@endsection
