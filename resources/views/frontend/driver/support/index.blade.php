@extends('layouts.driver')

@section('title', 'Support Tickets - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Support</h1>
        <p class="text-muted mb-0 small">Need help? Raise a ticket and we'll get back to you.</p>
    </div>
    <a href="{{ route('driver.support.create') }}" class="btn btn-brand rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> New ticket
    </a>
</div>

<div class="drv-card overflow-hidden">
            <div class="d-none d-lg-block table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small text-muted">Ticket ID</th>
                                <th class="py-3 text-uppercase small text-muted">Subject</th>
                                <th class="py-3 text-uppercase small text-muted">Category</th>
                                <th class="py-3 text-uppercase small text-muted text-center">Priority</th>
                                <th class="py-3 text-uppercase small text-muted text-center">Status</th>
                                <th class="py-3 text-uppercase small text-muted">Last update</th>
                                <th class="pe-4 py-3 text-end text-uppercase small text-muted">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($tickets) && count($tickets) > 0)
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark">{{ $ticket->ticket_number }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $ticket->subject }}</div>
                                        <div class="small text-muted">{{ str($ticket->description)->limit(50) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ ucfirst($ticket->category) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $prioMap = [
                                                'low' => 'bg-info bg-opacity-10 text-info',
                                                'medium' => 'bg-warning bg-opacity-10 text-dark',
                                                'high' => 'bg-danger bg-opacity-10 text-danger',
                                                'urgent' => 'bg-danger text-white'
                                            ];
                                        @endphp
                                        <span class="badge {{ $prioMap[$ticket->priority] }} rounded-pill px-3">{{ ucfirst($ticket->priority) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusMap = [
                                                'open' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'resolved' => 'bg-info text-white',
                                                'closed' => 'bg-secondary'
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusMap[$ticket->status] }} rounded-pill px-3">{{ ucfirst($ticket->status) }}</span>
                                    </td>
                                    <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('driver.support.show', $ticket->id) }}" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="py-4">
                                        @include('partials.ui.empty-state', [
                                            'title' => 'No support tickets',
                                            'message' => 'Create a ticket when you need help with rides, payments, or your account.',
                                            'icon' => 'bi-headset',
                                            'actionLabel' => 'New ticket',
                                            'actionUrl' => route('driver.support.create'),
                                        ])
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
            </div>

            <div class="d-lg-none p-3">
                @if(isset($tickets) && count($tickets) > 0)
                    @foreach($tickets as $ticket)
                    <a href="{{ route('driver.support.show', $ticket->id) }}" class="text-decoration-none text-dark d-block border rounded-4 p-3 mb-2">
                        <div class="d-flex justify-content-between gap-2 mb-2">
                            <div class="fw-bold">{{ $ticket->ticket_number }}</div>
                            <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                        </div>
                        <div class="fw-semibold mb-1">{{ $ticket->subject }}</div>
                        <div class="small text-muted">{{ ucfirst($ticket->category) }} · {{ $ticket->updated_at->diffForHumans() }}</div>
                    </a>
                    @endforeach
                @else
                    @include('partials.ui.empty-state', [
                        'title' => 'No support tickets',
                        'message' => 'Create a ticket when you need help with rides, payments, or your account.',
                        'icon' => 'bi-headset',
                        'actionLabel' => 'New ticket',
                        'actionUrl' => route('driver.support.create'),
                    ])
                @endif
            </div>
</div>

<style>
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
    color: #000;
}
</style>
@endsection
