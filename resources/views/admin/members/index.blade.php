@extends('layouts.admin')
@section('title', 'Members')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Members</h2>
</div>

<form method="GET" action="{{ route('admin.members.index') }}" class="mb-3">
    <div class="input-group" style="max-width:320px;">
        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama/email">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
        @if($search)<a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">Reset</a>@endif
    </div>
</form>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>No</th><th>Foto</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Organisasi</th><th>Status</th><th>Karya</th><th class="text-end">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($members as $i => $m)
                        <tr>
                            <td>{{ $members->firstItem() + $i }}</td>
                            <td>
                                @if($m->foto)
                                    <img src="{{ asset('storage/'.$m->foto) }}" width="40" height="40" class="rounded-circle object-fit-cover">
                                @else
                                    <span class="small text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>{{ $m->nama }}</td>
                            <td class="small">{{ $m->email }}</td>
                            <td class="small">{{ $m->telepon ?? '—' }}</td>
                            <td class="small">{{ $m->organisasi ?? '—' }}</td>
                            <td>
                                @if($m->status && $m->status->value === 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $m->publications_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.members.show', $m) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-magnifying-glass"></i></a>
                                <button type="button" class="btn btn-sm {{ $m->status && $m->status->value==='aktif' ? 'btn-outline-warning' : 'btn-outline-success' }}" data-coreui-toggle="modal" data-coreui-target="#statusModal{{ $m->id }}">
                                    @if($m->status && $m->status->value==='aktif') Nonaktifkan @else Aktifkan @endif
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No members found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $members->links() }}</div>
    </div>
</div>

@foreach ($members as $m)
<div class="modal fade" id="statusModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Ubah Status</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
        <div class="modal-body">
            @if($m->status && $m->status->value==='aktif')
                Nonaktifkan akun <strong>{{ $m->nama }}</strong>? Member tidak akan bisa login.
            @else
                Aktifkan kembali akun <strong>{{ $m->nama }}</strong>?
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
            <form action="{{ route('admin.members.update-status', $m) }}" method="POST" class="d-inline">@csrf @method('PUT')<button type="submit" class="btn {{ $m->status && $m->status->value==='aktif' ? 'btn-warning' : 'btn-success' }}">{{ $m->status && $m->status->value==='aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
        </div>
    </div></div>
</div>
@endforeach
@endsection
