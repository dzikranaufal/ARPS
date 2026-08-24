@extends('layouts.admin')
@section('title', 'Admin Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Admin Users</h2>
    <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary">Add Admin</a>
</div>

<form method="GET" action="{{ route('admin.admin-users.index') }}" class="mb-3">
    <div class="input-group" style="max-width:320px;">
        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Cari nama/email">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
        @if($search)<a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary">Reset</a>@endif
    </div>
</form>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse ($users as $i => $u)
                        <tr>
                            <td>{{ $users->firstItem() + $i }}</td>
                            <td>{{ $u->nama }}</td>
                            <td class="small">{{ $u->email }}</td>
                            <td>
                                @if($u->role->value==='superadmin')<span class="badge bg-danger">Super Admin</span>
                                @else<span class="badge bg-primary">Admin Manager</span>@endif
                            </td>
                            <td>
                                @if($u->status && $u->status->value==='aktif')<span class="badge bg-success">Aktif</span>
                                @else<span class="badge bg-danger">Nonaktif</span>@endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.admin-users.edit', $u) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a>
                                <button type="button" class="btn btn-sm {{ $u->status && $u->status->value==='aktif' ? 'btn-outline-warning' : 'btn-outline-success' }} me-1" data-coreui-toggle="modal" data-coreui-target="#statusModal{{ $u->id }}">
                                    {{ $u->status && $u->status->value==='aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal{{ $u->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No admin users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $users->links() }}</div>
    </div>
</div>

@foreach ($users as $u)
    <div class="modal fade" id="statusModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Ubah Status</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
            <div class="modal-body">
                @if($u->status && $u->status->value==='aktif')
                    Nonaktifkan <strong>{{ $u->nama }}</strong>?
                @else
                    Aktifkan <strong>{{ $u->nama }}</strong>?
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.admin-users.update-status', $u) }}" method="POST" class="d-inline">@csrf @method('PUT')<button type="submit" class="btn {{ $u->status && $u->status->value==='aktif' ? 'btn-warning' : 'btn-success' }}">{{ $u->status && $u->status->value==='aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
            </div>
        </div></div>
    </div>
    <div class="modal fade" id="deleteModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Hapus Admin</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
            <div class="modal-body">Hapus admin <strong>{{ $u->nama }}</strong>?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.admin-users.destroy', $u) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
            </div>
        </div></div>
    </div>
@endforeach
@endsection
