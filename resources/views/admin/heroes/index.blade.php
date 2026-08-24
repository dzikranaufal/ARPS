@extends('layouts.admin')
@section('title', 'Heroes')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Hero Carousel</h2>
    <a href="{{ route('admin.heroes.create') }}" class="btn btn-primary">Add Hero</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Gambar</th><th>Judul</th><th>Urutan</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse($heroes as $i=>$h)
                        <tr>
                            <td>{{ $heroes->firstItem()+$i }}</td>
                            <td>
                                @if($h->gambar)<img src="{{ asset('storage/'.$h->gambar) }}" width="80" height="45" class="rounded object-fit-cover">@else<span class="small text-body-secondary">—</span>@endif
                            </td>
                            <td><div class="fw-semibold">{{ $h->judul }}</div><div class="small text-body-secondary">{{ \Illuminate\Support\Str::limit(strip_tags($h->deskripsi),50) }}</div></td>
                            <td>{{ $h->urutan }}</td>
                            <td><span class="badge {{ $h->status->value==='aktif'?'bg-success':'bg-secondary' }}">{{ $h->status->value }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.heroes.edit',$h) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#del{{ $h->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty<tr><td colspan="6" class="text-center">Belum ada hero.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $heroes->links() }}</div>
    </div>
</div>
@foreach($heroes as $h)
<div class="modal fade" id="del{{ $h->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Hapus Hero</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
        <div class="modal-body">Hapus <strong>{{ $h->judul }}</strong>?</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
            <form action="{{ route('admin.heroes.destroy',$h) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
        </div>
    </div></div>
</div>
@endforeach
@endsection
