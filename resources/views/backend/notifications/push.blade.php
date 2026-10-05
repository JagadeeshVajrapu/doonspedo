@extends('layouts.admin')

@section('title', 'Push Notifications')
@section('page_title', 'Push Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bell-fill text-brand me-2"></i> Broadcast Push Notification</h5>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-4">
    <div class="col-md-8 mx-auto">
        <div class="admin-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-broadcast text-muted mb-3" style="font-size: 3rem;"></i>
                    <h4 class="fw-bold text-dark">Send Broadcast Message</h4>
                    <p class="text-muted small px-4">Send a push notification directly to users' devices (Customers, Drivers, or All).</p>
                </div>
                
                <form action="{{ route('admin.notifications.push.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Target Audience</label>
                        <select name="audience" class="form-select">
                            <option value="all">All Users & Drivers</option>
                            <option value="riders">Only Registered Customers</option>
                            <option value="drivers">Only Active Drivers</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Notification Title</label>
                        <input type="text" class="form-control" name="title" placeholder="e.g. 50% Off Weekend Rides!" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Notification Body / Message</label>
                        <textarea class="form-control" name="message" rows="4" placeholder="Type the message to display on the lock screen..." required></textarea>
                        <div class="text-end mt-1"><small class="text-muted" id="charCount">0/150 characters</small></div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Target URL Link (Optional)</label>
                        <input type="url" class="form-control" name="link" placeholder="App link or external URL...">
                    </div>

                    <div class="text-end pt-3 border-top border-secondary border-opacity-10">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-send-fill me-1"></i> Send Push Notification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
