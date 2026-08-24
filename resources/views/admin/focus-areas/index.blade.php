@extends('layouts.admin')
@section('title', 'Focus Areas')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Focus Areas (About)</h2>
    <a href="{{ route('admin.focus-areas.create') }}" class="btn btn-primary">Add Focus</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Judul</th><th>Deskripsi</th><th>Urutan</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse($areas as $i=>$a)
                        <tr><td>{{ $areas->firstItem()+$i }}</td><td>{{ $a->judul }}</td><td class="small">{{ \Illuminate\Support\Str::limit(strip_tags($a->deskripsi),60) }}</td><td>{{ $a->urutan }}</td><td class="text-end"><a href="{{ route('admin.focus-areas.edit',$a) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a><button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#del{{ $a->id }}"><i class="cil-trash"></i></button></td></tr>
                    @empty<tr><td colspan="5" class="text-center">Belum ada.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $areas->links() }}</div>
    </div>
</div>
@foreach($areas as $a)
<div class="modal fade" id="del{{ $a->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Hapus</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
        <div class="modal-body">Hapus <strong>{{ $a->judul }}</strong>?</div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button><form action="{{ route('admin.focus-areas.destroy',$a) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form></div>
    </div></div>
</div>
@endforeach
@endsection
