@extends('layouts.admin')

@section('title', 'Email Configuration')
@section('page_title', 'SMTP Email Setup')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-envelope-fill text-brand me-2"></i> Mail Engine Settings</h5>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="row g-4">
    <div class="col-md-8 mx-auto">
        <div class="admin-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-mailbox2 text-muted mb-3" style="font-size: 3rem;"></i>
                    <h4 class="fw-bold text-dark">Platform Mail Configuration</h4>
                    <p class="text-muted small px-3">Adjust SMTP relay settings used for transactional emails (Invoices, OTPs, Registrations).</p>
                </div>
                
                <form action="{{ route('admin.notifications.email.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold text-muted">Mail Driver/Mailer</label>
                            <select name="mail_mailer" class="form-select">
                                <option value="smtp">SMTP Configuration</option>
                                <option value="mailgun">Mailgun Gateway</option>
                                <option value="postmark">Postmark</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">SMTP Host</label>
                            <input type="text" class="form-control" name="mail_host" value="smtp.mailtrap.io" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">SMTP Port</label>
                            <input type="number" class="form-control" name="mail_port" value="2525" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">SMTP Username</label>
                            <input type="text" class="form-control" name="mail_username" value="**************" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">SMTP Password</label>
                            <input type="password" class="form-control" name="mail_password" value="**************" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Encryption Protocol</label>
                            <select name="mail_encryption" class="form-select">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">"From" Email Address</label>
                            <input type="email" class="form-control" name="mail_from_address" value="noreply@doonspedo.com" required>
                        </div>
                    </div>

                    <div class="text-end pt-4 border-top border-secondary border-opacity-10">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Email Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
