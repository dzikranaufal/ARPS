@extends('layouts.admin')
@section('title', 'Programs')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Programs</h2>
    <a href="{{ route('admin.programs.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Add Program</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>No</th><th>Gambar</th><th>Judul</th><th>Kategori</th><th class="text-end">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($programs as $i => $program)
                        <tr>
                            <td>{{ $programs->firstItem() + $i }}</td>
                            <td>
                                @if($program->gambar)
                                    <img src="{{ asset('storage/'.$program->gambar) }}" alt="gambar" width="48" height="48" class="rounded object-fit-cover">
                                @else
                                    <span class="text-body-secondary small">—</span>
                                @endif
                            </td>
                            <td>{{ $program->judul }}</td>
                            <td>
                                @if($program->kategori)
                                    <span class="badge bg-primary">{{ $program->kategori->nama }}</span>
                                @else
                                    <span class="badge bg-secondary">Tanpa kategori</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal{{ $program->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No programs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $programs->links() }}</div>
    </div>
</div>
@foreach ($programs as $program)
<div class="modal fade" id="deleteModal{{ $program->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Delete Program</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
        <div class="modal-body">Hapus program <strong>{{ $program->judul }}</strong>? Tindakan tidak dapat dibatalkan.</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
            <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
        </div>
    </div></div>
</div>
@endforeach
@endsection
