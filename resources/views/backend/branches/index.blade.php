@extends('layouts.admin')

@section('title', 'Manage Branches')
@section('page_title', 'Branches Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt text-brand me-2"></i> Our Branches</h5>
    <a href="{{ route('admin.branches.create') }}" class="btn btn-brand rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add New Branch
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4 px-4 py-3">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
@endif

<div class="admin-card">
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-muted small fw-bold border-0">Branch Name</th>
                        <th class="py-3 text-muted small fw-bold border-0">Contact Info</th>
                        <th class="py-3 text-muted small fw-bold border-0">Address</th>
                        <th class="py-3 text-muted small fw-bold border-0">Status</th>
                        <th class="py-3 text-muted small fw-bold border-0 text-end rounded-end-4 px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                    <tr>
                        <td class="px-4">
                            <div class="fw-bold text-dark">{{ $branch->name }}</div>
                        </td>
                        <td>
                            <div class="small text-dark"><i class="bi bi-phone me-1"></i> {{ $branch->phone ?? 'N/A' }}</div>
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $branch->email ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="small text-muted text-truncate" style="max-width: 200px;">
                                {{ $branch->address ?? 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.branches.toggle_status', $branch->id) }}" class="text-decoration-none">
                                @if($branch->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">Inactive</span>
                                @endif
                            </a>
                        </td>
                        <td class="text-end px-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit">
                                    <i class="bi bi-pencil text-secondary"></i>
                                </a>
                                <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Delete this branch?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" title="Delete">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt display-1 opacity-25"></i>
                            <p class="mt-3">No branches found. Add your first branch!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($branches->hasPages())
    <div class="card-footer bg-white border-0 p-4">
        {{ $branches->links() }}
    </div>
    @endif
</div>
@endsection
