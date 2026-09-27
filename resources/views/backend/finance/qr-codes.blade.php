@extends('layouts.admin')

@section('title', 'QR Code Management')
@section('page_title', 'QR Code Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Payment QR</h1>
        <p class="text-muted small mb-0">Only one QR can be active. Partners scan this code, then an admin confirms the payment.</p>
    </div>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif
@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card p-4">
            <h2 class="h6 fw-bold mb-3">Upload QR</h2>
            <form action="{{ route('admin.finance.qr.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold" for="qr-title">Title</label>
                    <input id="qr-title" name="title" class="form-control" value="{{ old('title') }}" required maxlength="80">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold" for="qr-upi">UPI ID</label>
                    <input id="qr-upi" name="upi_id" class="form-control" value="{{ old('upi_id') }}" maxlength="100" placeholder="name@upi">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold" for="qr-image">QR image</label>
                    <input id="qr-image" name="image" type="file" class="form-control" accept="image/png,image/jpeg,image/webp" required>
                    <div class="form-text">JPEG, PNG, or WebP. Maximum 2 MB.</div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_test" value="1" id="qr-test">
                    <label class="form-check-label" for="qr-test">Mark as TEST QR</label>
                </div>
                <button class="btn btn-dark rounded-pill px-4" type="submit">Save and activate</button>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-header"><h2 class="admin-card-title">QR codes</h2></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="px-3">Preview</th>
                            <th>Title</th>
                            <th>UPI</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($codes as $code)
                        <tr>
                            <td class="px-3"><img src="{{ $code->imageUrl() }}" alt="{{ $code->title }}" width="64" height="64" style="object-fit:contain"></td>
                            <td>
                                <div class="fw-bold">{{ $code->title }}</div>
                                @if($code->is_test)<div class="small text-warning">TEST QR</div>@endif
                            </td>
                            <td class="small">{{ $code->upi_id ?: '—' }}</td>
                            <td>{{ $code->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="small">{{ $code->created_at?->format('d M Y') }}</td>
                            <td class="text-end pe-3">
                                @if(!$code->is_active)
                                <form action="{{ route('admin.finance.qr.activate', $code->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-dark rounded-pill">Activate</button></form>
                                @else
                                <form action="{{ route('admin.finance.qr.deactivate', $code->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-secondary rounded-pill">Deactivate</button></form>
                                @endif
                                <form action="{{ route('admin.finance.qr.destroy', $code->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger rounded-pill">Delete</button></form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-4 text-muted">No QR code uploaded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
