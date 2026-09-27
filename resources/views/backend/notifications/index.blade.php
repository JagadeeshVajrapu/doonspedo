@extends('layouts.admin')

@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Notifications</h1>
        <p class="text-muted small mb-0">Wallet payments appear here when a partner submits a UTR. Approve the payment before the wallet is credited.</p>
    </div>
    @if($notifications->where('is_read', false)->count())
        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
            @csrf
            <button class="btn btn-outline-dark rounded-pill btn-sm" type="submit">Mark all read</button>
        </form>
    @endif
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="admin-card">
    @forelse($notifications as $notification)
        <a href="{{ route('admin.notifications.open', $notification->id) }}" class="d-flex justify-content-between gap-3 p-3 border-bottom text-decoration-none {{ $notification->is_read ? '' : 'bg-warning bg-opacity-10' }}">
            <div>
                <div class="fw-bold text-dark">{{ $notification->title }}</div>
                <div class="small text-dark">{{ $notification->message }}</div>
                <div class="small text-muted">{{ $notification->created_at?->format('d M Y H:i') }} · {{ $notification->is_read ? 'Read' : 'Unread' }}</div>
            </div>
            <span class="small text-nowrap">{{ $notification->is_read ? 'Open' : 'Review' }}</span>
        </a>
    @empty
        <div class="p-4 text-muted">No notifications yet. A partner payment will show up here after they submit the UTR.</div>
    @endforelse
</div>
<div class="mt-3">{{ $notifications->links() }}</div>
@endsection
