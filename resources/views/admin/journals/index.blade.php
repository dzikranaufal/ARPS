@extends('layouts.admin')

@section('title', 'Journals')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Journals</h2>
    <a href="{{ route('admin.journals.create') }}" class="btn btn-primary">
        <i class="cil-plus me-1"></i> Add Journal
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Cover</th>
                        <th>Nama</th>
                        <th>E-ISSN</th>
                        <th>Link Eksternal</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($journals as $i => $journal)
                        <tr>
                            <td>{{ $journals->firstItem() + $i }}</td>
                            <td>
                                @if($journal->cover)
                                    <img src="{{ asset('storage/'.$journal->cover) }}" alt="Cover" width="48" height="64" class="rounded object-fit-cover">
                                @else
                                    <span class="small text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $journal->nama }}</div>
                                <div class="small text-body-secondary">{{ \Illuminate\Support\Str::limit($journal->slug, 40) }}</div>
                                @if($journal->deskripsi)
                                    <div class="small text-body-secondary">{{ \Illuminate\Support\Str::limit(strip_tags($journal->deskripsi), 60) }}</div>
                                @endif
                            </td>
                            <td>{{ $journal->e_issn ?? '—' }}</td>
                            <td><a href="{{ $journal->link_eksternal }}" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width:160px;">{{ $journal->link_eksternal }}</a></td>
                            <td><span class="badge {{ $journal->status->value === 'aktif' ? 'bg-success' : 'bg-secondary' }}">{{ $journal->status->value }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.journals.edit', $journal) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="cil-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal"
                                    data-coreui-target="#deleteModal{{ $journal->id }}">
                                    <i class="cil-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No journals found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $journals->links() }}</div>
    </div>
</div>

@foreach ($journals as $journal)
<div class="modal fade" id="deleteModal{{ $journal->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Journal</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Hapus jurnal <strong>{{ $journal->nama }}</strong>? Tindakan tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.journals.destroy', $journal) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
