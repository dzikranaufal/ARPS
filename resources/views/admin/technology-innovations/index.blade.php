@extends('layouts.admin')
@section('title', 'Technology Innovations')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Technology Innovations</h2>
    <a href="{{ route('admin.technology-innovations.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Add Innovation</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Gambar</th><th>Judul</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse ($innovations as $i => $item)
                        <tr>
                            <td>{{ $innovations->firstItem() + $i }}</td>
                            <td>
                                @if($item->gambar)
                                    <img src="{{ asset('storage/'.$item->gambar) }}" width="48" height="48" class="rounded object-fit-cover">
                                @else
                                    <span class="small text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->judul }}</div>
                                <div class="small text-body-secondary">{{ \Illuminate\Support\Str::limit($item->deskripsi, 60) }}</div>
                            </td>
                            <td><span class="badge {{ $item->status->value === 'aktif' ? 'bg-success' : 'bg-secondary' }}">{{ $item->status->value }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.technology-innovations.edit', $item) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal{{ $item->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No innovations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $innovations->links() }}</div>
    </div>
</div>
@foreach ($innovations as $item)
<div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Delete Innovation</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
        <div class="modal-body">Hapus inovasi <strong>{{ $item->judul }}</strong>?</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
            <form action="{{ route('admin.technology-innovations.destroy', $item) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
        </div>
    </div></div>
</div>
@endforeach
@endsection
