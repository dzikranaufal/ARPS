@extends('layouts.admin')
@section('title', 'Organization Structure')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Organization Structure</h2>
    <a href="{{ route('admin.structure.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Add Member</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Foto</th><th>Nama</th><th>Jabatan</th><th>Afiliasi</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse ($structures as $i => $s)
                        <tr>
                            <td>{{ $structures->firstItem() + $i }}</td>
                            <td>
                                @if($s->foto)
                                    <img src="{{ asset('storage/'.$s->foto) }}" width="48" height="48" class="rounded-circle object-fit-cover">
                                @else
                                    <span class="small text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>{{ $s->nama_pengurus }}</td>
                            <td>{{ $s->jabatan }}</td>
                            <td>{{ $s->afiliasi ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.structure.edit', $s) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal{{ $s->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No members found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $structures->links() }}</div>
    </div>
</div>
@foreach ($structures as $s)
<div class="modal fade" id="deleteModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Delete Member</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
        <div class="modal-body">Hapus pengurus <strong>{{ $s->nama_pengurus }}</strong>?</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
            <form action="{{ route('admin.structure.destroy', $s) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
        </div>
    </div></div>
</div>
@endforeach
@endsection
