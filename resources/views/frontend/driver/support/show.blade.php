@extends('layouts.driver')

@section('title', 'Ticket Details - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Ticket #{{ $ticket->ticket_number }}</h1>
        <p class="text-muted mb-0 small">{{ $ticket->subject }}</p>
    </div>
    <a href="{{ route('driver.support.index') }}" class="btn btn-light border rounded-pill px-4 fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Back to tickets
    </a>
</div>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="drv-card overflow-hidden">
            <div class="p-4 border-start border-4 border-brand">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="small text-muted fw-bold text-uppercase">Status</div>
                        @php
                            $statusMap = [
                                'open' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'resolved' => 'bg-info text-white',
                                'closed' => 'bg-secondary'
                            ];
                        @endphp
                        <span class="badge {{ $statusMap[$ticket->status] }} rounded-pill px-3">{{ ucfirst($ticket->status) }}</span>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="small text-muted fw-bold text-uppercase">Category</div>
                        <span class="fw-bold">{{ ucfirst($ticket->category) }}</span>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="small text-muted fw-bold text-uppercase">Priority</div>
                        <span class="fw-bold">{{ ucfirst($ticket->priority) }}</span>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="small text-muted fw-bold text-uppercase">Created at</div>
                        <span class="fw-bold">{{ $ticket->created_at->format('d M, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 fw-bold text-uppercase small text-muted">
                Conversation History
            </div>
            <div class="card-body p-4 bg-light bg-opacity-50">
                <!-- Original Description -->
                <div class="d-flex mb-4">
                    <div class="flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($driver->name) }}&background=000&color=fff" class="rounded-circle" style="width: 40px;">
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <div class="bg-white p-3 rounded-4 shadow-sm">
                            <h6 class="fw-bold mb-1">{{ $driver->name }} <span class="badge bg-light text-dark fw-normal ms-1">Author</span></h6>
                            <p class="mb-0">{{ $ticket->description }}</p>
                        </div>
                        <div class="small text-muted mt-1 px-2">{{ $ticket->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                @foreach($ticket->messages as $msg)
                <div class="d-flex mb-4 {{ $msg->sender_type == 'admin' ? 'flex-row-reverse' : '' }}">
                    <div class="flex-shrink-0 {{ $msg->sender_type == 'admin' ? 'ms-3' : 'me-3' }}">
                        @if($msg->sender_type == 'admin')
                            <div class="bg-brand rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                <i class="bi bi-person-workspace text-dark"></i>
                            </div>
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($driver->name) }}&background=000&color=fff" class="rounded-circle" style="width: 40px;">
                        @endif
                    </div>
                    <div class="flex-grow-1 {{ $msg->sender_type == 'admin' ? 'text-end' : '' }}">
                        <div class="bg-white p-3 rounded-4 shadow-sm d-inline-block text-start {{ $msg->sender_type == 'admin' ? 'border-start border-4 border-brand' : '' }}" style="max-width: 85%;">
                            <h6 class="fw-bold mb-1">
                                {{ $msg->sender_type == 'admin' ? 'Support Agent' : $driver->name }}
                                @if($msg->sender_type == 'admin')
                                    <span class="badge bg-brand text-dark fw-normal ms-1">Official</span>
                                @endif
                            </h6>
                            <p class="mb-0">{{ $msg->message }}</p>
                            @if($msg->attachment_path)
                                <div class="mt-2 pt-2 border-top">
                                    <a href="{{ asset('storage/' . $msg->attachment_path) }}" target="_blank" class="small text-decoration-none fw-bold">
                                        <i class="bi bi-paperclip"></i> View Attachment
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="small text-muted mt-1 px-2">{{ $msg->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($ticket->status != 'closed' && $ticket->status != 'resolved')
            <div class="card-footer bg-white border-0 p-4 pt-0">
                <form action="{{ route('driver.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-light p-3 rounded-4">
                        <textarea name="message" class="form-control border-0 bg-transparent" rows="4" placeholder="Type your reply here..." required></textarea>
                        <hr class="opacity-10">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <label for="attachment" class="btn btn-sm btn-light rounded-pill px-3">
                                    <i class="bi bi-paperclip me-1"></i> Attach File
                                </label>
                                <input type="file" name="attachment" id="attachment" class="d-none">
                                <span id="file-name" class="small text-muted ms-2"></span>
                            </div>
                            <button type="submit" class="btn btn-brand rounded-pill px-4 fw-bold shadow-sm">SEND REPLY</button>
                        </div>
                    </div>
                </form>
            </div>
            @else
            <div class="card-footer bg-light border-0 p-4 text-center">
                <p class="mb-0 text-muted small"><i class="bi bi-lock-fill me-1"></i> This ticket is {{ $ticket->status }}. You can no longer send replies.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
}
.text-brand { color: #cddc29 !important; }
.border-brand { border-color: #cddc29 !important; }
.bg-brand { background-color: #cddc29 !important; }
</style>

@section('scripts')
<script>
    document.getElementById('attachment').onchange = function() {
        if(this.files.length > 0) {
            document.getElementById('file-name').textContent = this.files[0].name;
        }
    };
</script>
@endsection
@endsection
