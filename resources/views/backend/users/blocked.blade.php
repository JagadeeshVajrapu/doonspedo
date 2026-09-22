@extends('layouts.admin')

@section('title', 'Blocked Users')

@section('page_title', 'Blocked Customers')

@section('content')
<div class="card shadow-sm border-0 border-top border-warning border-3">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="mb-0 fw-bold"><i class="bi bi-slash-circle text-warning me-2"></i> Restricted Accounts</h5>
    </div>
    <div class="card-body p-0">
        <div class="admin-table-wrap">
            <table class="table table-hover align-middle mb-0 admin-responsive-table">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Blocked Date</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($blockedUsers) && count($blockedUsers) > 0)
                        @foreach($blockedUsers as $user)
                    <tr>
                        <td class="px-4 fw-bold text-muted">#{{ 1000 + $user->id }}</td>
                        <td>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>
                        <td>{{ $user->mobile }}</td>
                        <td>{{ $user->updated_at ? $user->updated_at->format('M d, Y') : 'Unknown' }}</td>
                        <td class="text-end px-4">
                            <form action="{{ route('admin.users.block', $user->id) }}" method="POST" class="d-inline border p-1 rounded bg-success bg-opacity-10">
                                @csrf
                                <button type="submit" class="btn btn-sm text-success border-0" title="Unblock User">
                                    <i class="bi bi-unlock-fill me-1"></i> Unblock
                                </button>
                            </form>
                        </td>
                    </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-3"></i>
                            <h5 class="fw-bold">All Good!</h5>
                            <p>There are no blocked users at the moment.</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
