@extends('layouts.admin')

@section('title', 'Manage Currencies')
@section('page_title', 'Master Currencies')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack text-brand me-2"></i> Global Currencies</h5>
    <button type="button" class="btn btn-brand rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCurrencyModal">
        <i class="bi bi-plus-lg me-1"></i> Add Currency
    </button>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Currency Name</th>
                        <th class="py-3 text-muted small fw-bold border-0">ISO Code</th>
                        <th class="py-3 text-muted small fw-bold border-0">Symbol</th>
                        <th class="py-3 text-muted small fw-bold border-0">Rate (to USD)</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($currencies) && count($currencies) > 0)
                        @foreach($currencies as $currency)
                        <tr>
                            <td class="fw-bold text-dark">{{ $currency->name }}</td>
                            <td><span class="badge bg-light text-primary font-monospace px-2 py-1">{{ strtoupper($currency->code) }}</span></td>
                            <td class="fs-5">{{ $currency->symbol }}</td>
                            <td><span class="text-muted">{{ number_format($currency->exchange_rate, 4) }}</span></td>
                            <td>
                                @if($currency->is_default)
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill">Default</span>
                                @elseif($currency->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">Disabled</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm me-1" title="Edit" data-bs-toggle="modal" data-bs-target="#editCurrencyModal{{ $currency->id }}"><i class="bi bi-pencil text-secondary"></i></button>
                                <form action="{{ route('admin.localization.currencies.destroy', $currency->id) }}" method="POST" onsubmit="return confirm('Delete this currency?');" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Delete" {{ $currency->is_default ? 'disabled' : '' }}><i class="bi bi-trash text-danger"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-cash-coin text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No currencies available. Add USD or EUR.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $currencies->links() }}
        </div>
    </div>
</div>

<!-- Edit Currency Modals -->
@if(isset($currencies) && count($currencies) > 0)
@foreach($currencies as $currency)
<div class="modal fade" id="editCurrencyModal{{ $currency->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold">Edit Currency</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.localization.currencies.update', $currency->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Currency Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $currency->name }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">ISO Code</label>
                            <input type="text" class="form-control" name="code" value="{{ $currency->code }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Symbol</label>
                            <input type="text" class="form-control" name="symbol" value="{{ $currency->symbol }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Exchange Rate / Base</label>
                        <input type="number" step="0.0001" class="form-control" name="exchange_rate" value="{{ $currency->exchange_rate }}" required>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $currency->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold">Active for riders</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

<!-- Create Currency Modal -->
<div class="modal fade" id="createCurrencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-brand me-2"></i> Add Currency</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.localization.currencies.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Currency Name</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. US Dollar" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">ISO Code</label>
                            <input type="text" class="form-control" name="code" placeholder="e.g. USD" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Symbol</label>
                            <input type="text" class="form-control" name="symbol" placeholder="e.g. $" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Exchange Rate / Base</label>
                        <input type="number" step="0.0001" class="form-control" name="exchange_rate" value="1.0000" required>
                        <div class="form-text small">Value relative to your default system currency.</div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label fw-bold">Active for riders</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand rounded-pill px-4">Save Currency</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
