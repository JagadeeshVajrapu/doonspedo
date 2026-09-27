@extends('layouts.admin')

@section('title', 'Customer KYC')
@section('page_title', 'Customer KYC')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Document</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $submission->user->name ?? $submission->full_name }}</div>
                            <div class="small text-muted">#{{ $submission->user_id }} · {{ $submission->user->mobile ?? $submission->user->email ?? '' }}</div>
                        </td>
                        <td>{{ str_replace('_', ' ', $submission->document_type) }}<br><span class="small text-muted">{{ $submission->document_number }}</span></td>
                        <td>{{ ucfirst($submission->status) }}</td>
                        <td>{{ $submission->created_at->format('d M Y H:i') }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.customers.kyc.download', $submission) }}">Document</a>
                            @if($submission->status === 'pending')
                                <form class="d-inline" method="POST" action="{{ route('admin.customers.kyc.approve', $submission) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit">Approve</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('admin.customers.kyc.reject', $submission) }}">
                                    @csrf
                                    <input name="rejection_reason" class="form-control form-control-sm d-inline-block" style="width: 180px;" placeholder="Rejection reason" required>
                                    <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No customer KYC submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $submissions->links() }}</div>
</div>
@endsection
