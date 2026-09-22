@extends('layouts.admin')

@section('title', 'View Customer Details')

@section('page_title', 'Customer Profile')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4 border-top border-4 border-info">
            <div class="card-body text-center p-4">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-block d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                    <i class="bi bi-person text-primary display-4"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted small">Customer ID: #{{ 1000 + $user->id }}</p>
                <div class="mt-3">
                    <span class="badge bg-success rounded-pill px-3 py-2 fs-6 shadow-sm"><i class="bi bi-check-circle"></i> Active Account</span>
                </div>
                
                <hr class="my-4">
                
                <ul class="list-unstyled text-start mb-0">
                    <li class="mb-3 d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-center" style="width: 40px"><i class="bi bi-telephone text-secondary"></i></div>
                        <div>
                            <small class="text-muted d-block fw-bold">Mobile</small>
                            <span class="fw-bold">{{ $user->mobile ?? 'N/A' }}</span>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-center" style="width: 40px"><i class="bi bi-envelope text-secondary"></i></div>
                        <div>
                            <small class="text-muted d-block fw-bold">Email</small>
                            <span class="fw-bold">{{ $user->email ?? 'N/A' }}</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded me-3 text-center" style="width: 40px"><i class="bi bi-calendar3 text-secondary"></i></div>
                        <div>
                            <small class="text-muted d-block fw-bold">Joined On</small>
                            <span class="fw-bold">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 border-top border-4 border-success">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-wallet2 text-success me-2"></i> Wallet Summary</h5>
                <h2 class="fw-bold text-success mb-1">{{ $default_currency->symbol ?? '₹' }}0.00</h2>
                <p class="text-muted small mb-0">Current Available Balance</p>
                <div class="mt-3">
                    <a href="{{ route('admin.users.wallet') }}" class="btn btn-sm btn-outline-success w-100 fw-bold">Manage Wallet Settings</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="admin-card h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history text-secondary me-2"></i> Ride History</h5>
            </div>
            <div class="card-body p-0">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle mb-0 admin-responsive-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Ride ID</th>
                                <th>Driver Info</th>
                                <th>Fare</th>
                                <th>Date</th>
                                <th class="text-end px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Placeholder Data -->
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-car-front fs-1 d-block mb-3"></i>
                                    <h6 class="fw-bold mb-1">No Rides Yet!</h6>
                                    <p class="small">The customer hasn't requested any rides.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
