@extends('layouts.driver')

@section('title', 'Manage Vehicles - Doonspedo')

@section('content')
<div class="drv-page-header">
    <div>
        <h1>Vehicles</h1>
        <p class="text-muted mb-0 small">Manage your fleet and select your active vehicle.</p>
    </div>
    <a href="{{ route('driver.vehicles.create') }}" class="btn btn-brand rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add vehicle
    </a>
</div>

@if(session('success'))
    @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success')])
@endif

@if(session('error'))
    @include('partials.ui.alert', ['variant' => 'danger', 'message' => session('error')])
@endif

<div class="row g-4">
    @if(isset($vehicles) && count($vehicles) > 0)
        @foreach($vehicles as $vehicle)
        @php
            $vehiclePhoto = $vehicle->documents->where('document_name', 'Vehicle Photo')->first();
        @endphp
    <div class="col-md-6 col-lg-4">
        <div class="drv-card h-100 border-top border-4 {{ $vehicle->status == 'active' ? 'border-brand' : 'border-light' }}">
            @if($vehiclePhoto)
                <img src="{{ asset('storage/' . $vehiclePhoto->document_path) }}" class="w-100" style="height: 160px; object-fit: cover;" alt="Vehicle photo">
            @else
                <div class="bg-light w-100 d-flex align-items-center justify-content-center border-bottom" style="height: 160px;">
                    <i class="bi bi-car-front text-muted display-4"></i>
                </div>
            @endif
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-light text-dark rounded-pill mb-2 px-3 fw-normal small">
                            {{ $vehicle->category->name }}
                        </span>
                        <h2 class="h5 fw-bold mb-0">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
                        <div class="text-muted small mt-1 fw-bold">{{ $vehicle->number_plate }}</div>
                    </div>
                    <div class="text-end">
                        @include('partials.ui.status-badge', [
                            'label' => ucfirst($vehicle->verification_status),
                            'variant' => $vehicle->verification_status == 'approved' ? 'success' : ($vehicle->verification_status == 'rejected' ? 'danger' : 'warning'),
                        ])
                    </div>
                </div>

                <div class="d-flex gap-4 mb-4 border-top border-bottom border-light py-2">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold opacity-50" style="font-size: 0.6rem;">Color</div>
                        <div class="small fw-bold">{{ $vehicle->color }}</div>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-bold opacity-50" style="font-size: 0.6rem;">Year</div>
                        <div class="small fw-bold">{{ $vehicle->year }}</div>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-bold opacity-50" style="font-size: 0.6rem;">Seats</div>
                        <div class="small fw-bold">{{ $vehicle->category->capacity_seats }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="small text-muted fw-bold"><i class="bi bi-file-earmark-check me-1"></i> Documents: {{ $vehicle->documents->count() }}</div>
                    @if($vehicle->status == 'active')
                        <div class="badge bg-brand text-dark rounded-pill px-3 py-1 fw-bold border-0 shadow-sm">
                            <i class="bi bi-record-circle-fill me-1"></i> ACTIVE
                        </div>
                    @endif
                </div>

                <div class="row g-2">
                    <div class="col-8">
                        @if($vehicle->status != 'active')
                            <form action="{{ route('driver.vehicles.setActive', $vehicle->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark w-100 rounded-pill btn-sm fw-bold py-2 {{ $vehicle->verification_status != 'approved' ? 'disabled' : '' }}" {{ $vehicle->verification_status != 'approved' ? 'disabled' : '' }}>
                                    SET ACTIVE
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="col-4">
                        <form action="{{ route('driver.vehicles.destroy', $vehicle->id) }}" method="POST" onsubmit="return confirm('Delete this vehicle?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm fw-bold py-2 {{ $vehicle->status == 'active' ? 'disabled' : '' }}" {{ $vehicle->status == 'active' ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($vehicle->verification_status == 'rejected' && $vehicle->admin_message)
                    <div class="mt-3 p-2 bg-danger bg-opacity-10 rounded-3">
                        <small class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i> Reason: {{ $vehicle->admin_message }}</small>
                    </div>
                @endif
                
                @if($vehicle->verification_status == 'pending')
                    <div class="mt-3 p-2 bg-light rounded-3 text-center">
                        <small class="text-muted fw-bold">Verification In Progress...</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="col-12">
        <div class="drv-card p-4">
            @include('partials.ui.empty-state', [
                'title' => 'No vehicles found',
                'message' => 'Add your vehicle details and documents to get started. Each vehicle must be approved before use.',
                'icon' => 'bi-car-front',
                'actionLabel' => 'Add your first vehicle',
                'actionUrl' => route('driver.vehicles.create'),
            ])
        </div>
    </div>
    @endif
</div>

<style>
.bg-brand {
    background-color: #cddc29 !important;
}
.border-brand {
    border-color: #cddc29 !important;
}
.text-brand {
    color: #cddc29 !important;
}
.btn-brand {
    background-color: #cddc29;
    color: #000;
}
.btn-brand:hover {
    background-color: #b9c825;
}
</style>
@endsection
