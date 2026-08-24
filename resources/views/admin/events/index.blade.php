@extends('layouts.admin')
@section('title', 'Events')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Events</h2>
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Add Event</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Poster</th><th>Judul</th><th>Tanggal (WIB)</th><th>Lokasi</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse ($events as $i => $event)
                        <tr>
                            <td>{{ $events->firstItem() + $i }}</td>
                            <td>
                                @if($event->poster)
                                    <img src="{{ asset('storage/'.$event->poster) }}" width="48" height="48" class="rounded object-fit-cover">
                                @else
                                    <span class="small text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $event->judul }}</div>
                                @if($event->info_kontak_pendaftaran)
                                    <div class="small text-body-secondary">{{ $event->info_kontak_pendaftaran }}</div>
                                @endif
                            </td>
                            <td>{{ $event->tanggal_waktu->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</td>
                            <td>{{ $event->lokasi ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal{{ $event->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No events found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $events->links() }}</div>
    </div>
</div>
@foreach ($events as $event)
<div class="modal fade" id="deleteModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Delete Event</h5><button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>
        <div class="modal-body">Hapus event <strong>{{ $event->judul }}</strong>?</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
        </div>
    </div></div>
</div>
@endforeach
@endsection
