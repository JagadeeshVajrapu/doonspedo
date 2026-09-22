@extends('layouts.admin')

@section('title', 'Manage Coupons')
@section('page_title', 'Discount Coupons & Promos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-tag text-brand me-2"></i> Promotional Coupons</h5>
    <button class="btn btn-brand rounded-pill px-4 shadow-sm" onclick="alert('Create modal coming soon')">
        <i class="bi bi-plus-lg me-1"></i> Add Coupon
    </button>
</div>

<div class="admin-card mb-4">
    <div class="card-body p-4">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-muted small fw-bold border-0 rounded-start-4">Code</th>
                        <th class="py-3 text-muted small fw-bold border-0">Type</th>
                        <th class="py-3 text-muted small fw-bold border-0">Discount</th>
                        <th class="py-3 text-muted small fw-bold border-0">Usage</th>
                        <th class="py-3 text-muted small fw-bold border-0">Validity</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($coupons) && count($coupons) > 0)
                        @foreach($coupons as $coupon)
                        <tr>
                            <td class="fw-bold text-primary"><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 border border-primary border-dashed rounded-3 shadow-none text-uppercase">{{ $coupon->code }}</span></td>
                            <td>{{ ucfirst($coupon->discount_type) }}</td>
                            <td class="fw-bold text-success">{{ $coupon->discount_type == 'percent' ? intval($coupon->discount_value) . '%' : '$' . number_format($coupon->discount_value, 2) }}</td>
                            <td>
                                <div class="small fw-bold">{{ $coupon->used_count }} <span class="text-muted fw-normal">/ {{ $coupon->usage_limit ?? '∞' }}</span></div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $coupon->usage_limit ? ($coupon->used_count / $coupon->usage_limit) * 100 : 0 }}%"></div>
                                </div>
                            </td>
                            <td class="small">
                                <span class="text-muted">From:</span> {{ $coupon->valid_from ? \Carbon\Carbon::parse($coupon->valid_from)->format('M d, Y') : 'Always' }}<br>
                                <span class="text-muted">To:</span> {{ $coupon->valid_until ? \Carbon\Carbon::parse($coupon->valid_until)->format('M d, Y') : 'Forever' }}
                            </td>
                            <td>
                                @if($coupon->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm" aria-label="Edit coupon" title="Edit"><i class="bi bi-pencil text-secondary" aria-hidden="true"></i></button>
                                <button type="button" class="btn btn-sm btn-light rounded-circle shadow-sm ms-1" aria-label="Delete coupon" title="Delete"><i class="bi bi-trash text-danger" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-tags text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No active coupons found. Create a promotion to boost rides!</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection
