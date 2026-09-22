@extends('layouts.admin')

@section('title', 'Manage Customers')

@section('page_title', 'All Customers')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Customer directory</h2>
    </div>
    <div class="card-body p-0">
        @if(session('success'))
            @include('partials.ui.alert', ['variant' => 'success', 'message' => session('success'), 'classExtra' => 'm-3'])
        @endif
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">ID</th>
                        <th>Name</th>
                        <th>Email / Mobile</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($users) && count($users) > 0)
                        @foreach($users as $user)
                        <tr>
                            <td class="px-4 fw-bold">#{{ 1000 + $user->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $user->name }}</div>
                            </td>
                            <td>
                                <div class="small text-muted">{{ $user->email ?? 'N/A' }}</div>
                                <div class="small fw-bold">{{ $user->mobile ?? 'N/A' }}</div>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-success rounded-pill">Active</span>
                            </td>
                            <td class="text-end px-4">
                                <form action="{{ route('admin.users.block', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Block User">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.users.view', $user->id) }}" class="btn btn-sm btn-outline-info" title="View Details" aria-label="View customer">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit" aria-label="Edit customer">
                                    <i class="bi bi-pencil" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" aria-label="Delete customer">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-3"></i>
                                No customers found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
