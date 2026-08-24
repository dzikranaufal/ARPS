@extends('layouts.admin')
@section('title', 'Publications')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Publications Antrian</h2>
</div>

<form method="GET" action="{{ route('admin.publications.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="pending" @selected($status==='pending')>Pending</option>
            <option value="approved" @selected($status==='approved')>Approved</option>
            <option value="rejected" @selected($status==='rejected')>Rejected</option>
            <option value="" @selected($status==='')>Semua</option>
        </select>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari judul">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Judul</th><th>Kategori</th><th>Member</th><th>Tanggal</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse ($publications as $i => $pub)
                        <tr>
                            <td>{{ $publications->firstItem() + $i }}</td>
                            <td>
                                <div class="fw-semibold">{{ $pub->judul }}</div>
                                @if($pub->deskripsi)<div class="small text-body-secondary">{{ \Illuminate\Support\Str::limit($pub->deskripsi, 60) }}</div>@endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $pub->kategori->value }}</span></td>
                            <td class="small">{{ $pub->member->nama ?? '—' }}</td>
                            <td class="small">{{ $pub->created_at->format('d M Y') }}</td>
                            <td>
                                @if($pub->status->value==='pending')<span class="badge bg-warning text-dark">Pending</span>
                                @elseif($pub->status->value==='approved')<span class="badge bg-success">Approved</span>
                                @else<span class="badge bg-danger">Rejected</span>@endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.publications.show', $pub) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-magnifying-glass"></i></a>
                                @if($pub->status->value==='pending')
                                    <button type="button" class="btn btn-sm btn-success me-1" data-coreui-toggle="modal" data-coreui-target="#approveModal{{ $pub->id }}">Approve</button>
                                    <button type="button" class="btn btn-sm btn-danger me-1" data-coreui-toggle="modal" data-coreui-target="#rejectModal{{ $pub->id }}">Reject</button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal{{ $pub->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Tidak ada karya.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $publications->links() }}</div>
    </div>
</div>

@foreach ($publications as $pub)
    @if($pub->status->value==='pending')
        <div class="modal fade" id="approveModal{{ $pub->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Approve Karya</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
                <div class="modal-body">Setujui karya <strong>{{ $pub->judul }}</strong>?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.publications.approve', $pub) }}" method="POST" class="d-inline">@csrf @method('PUT')<button type="submit" class="btn btn-success">Approve</button></form>
                </div>
            </div></div>
        </div>
        <div class="modal fade" id="rejectModal{{ $pub->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Reject Karya</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
                <div class="modal-body">Tolak karya <strong>{{ $pub->judul }}</strong>?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.publications.reject', $pub) }}" method="POST" class="d-inline">@csrf @method('PUT')<button type="submit" class="btn btn-danger">Reject</button></form>
                </div>
            </div></div>
        </div>
    @endif
    <div class="modal fade" id="deleteModal{{ $pub->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Hapus Karya</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
            <div class="modal-body">Hapus karya <strong>{{ $pub->judul }}</strong> beserta file?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.publications.destroy', $pub) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
            </div>
        </div></div>
    </div>
@endforeach
@endsection
